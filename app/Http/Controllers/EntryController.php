<?php

namespace App\Http\Controllers;

use App\Models\COALedgers;
use App\Models\Entry;
use App\Models\Entryitem;
use App\Models\Entrytype;
use App\Models\Role as Permission;
use App\Models\Tag;
use App\User;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use DB;

/**
 * THIS CONTROLLER IS USED AS PRODUCT CONTROLLER.
 */
class EntryController extends Controller
{
    /**
     * @var Permission
     */
    private $permission;

    /**
     * @param Permission $permission
     */
    public function __construct(Permission $permission)
    {
        parent::__construct();
        $this->permission = $permission;
    }

    // index
    public function index()
    {
        $page_title = 'Entries';
        $page_description = 'List of all Entries';
        $entriestype = Entrytype::orderBy('id', 'asc')->where('org_id', \Auth::user()->org_id)->get();
        $entries = Entry::select('entries.*')
      ->leftjoin('entryitems', 'entryitems.entry_id', '=', 'entries.id')
      ->where('entries.org_id', \Auth::user()->org_id)
      ->where(function ($query) {
          if (\Request::get('start_date') != '' && \Request::get('end_date') != '') {
              return $query->where('date', '>=', \Request::get('start_date'))->where('date', '<=', \Request::get('end_date'));
          }
      })
      ->where(function ($query) {
          if (\Request::get('tag_id') && \Request::get('tag_id') != '') {
              return $query->where('tag_id', \Request::get('tag_id'));
          }
      })
      ->where(function ($query) {
          if (\Request::get('entries_type_id') && \Request::get('entries_type_id') != '') {
              return $query->where('entrytype_id', \Request::get('entries_type_id'));
          }
      })
      ->where(function ($query) {
          if (\Request::get('legder_id') && \Request::get('legder_id') != '') {
              return $query->where('entryitems.ledger_id', \Request::get('legder_id'));
          }
      })
      ->where(function ($query) {
          if (\Request::get('user_id') && \Request::get('user_id') != '') {
              return $query->where('entries.user_id', \Request::get('user_id'));
          }
      })
      ->orderBy('entries.id', 'desc')
      ->groupBy('entries.id')
      ->paginate(30);
        $tags = Tag::pluck('title as name', 'id')->all();
        $entries_type = Entrytype::where('org_id', \Auth::user()->org_id)->pluck('name', 'id')->all();
        $ledgers = COALedgers::where('org_id', \Auth::user()->org_id)->pluck('name', 'id')->all();
        $users = User::where('org_id', \Auth::user()->org_id)->pluck('username as name', 'id')->all();

        return view('admin.entries.index', compact('page_title', 'page_description', 'entriestype', 'entries', 'tags', 'entries_type', 'ledgers', 'users'));
    }

    public function Create($label)
    {
        $page_title = 'Entry Add';
        $page_description = 'create entry';

        $tags = Tag::orderBy('id', 'desc')->get();
        $currency = \App\Models\Country::select('*')->whereEnabled('1')->orderByDesc('id')->get();
        $selected_currency = 'NPR';
        return view('admin.entries.create', compact('page_title', 'page_description', 'tags','currency','selected_currency'));
    }

    public function show($label, $id)
    {
        $page_title = 'Entry Show';
        $page_description = 'show entries';

        $entries = Entry::where('id', $id)->first();
        $entriesitem = Entryitem::orderBy('id', 'asc')->where('entry_id', $entries->id)->get();

        return view('admin.entries.show', compact('page_title', 'page_description', 'entries', 'entriesitem'));
    }

    public function DownloadPdf($label, $id)
    {
        $entries = Entry::where('id', $id)->first();
        $entriesitem = Entryitem::orderBy('id', 'asc')->where('entry_id', $entries->id)->get();

        $imagepath = \Auth::user()->organization->logo;

        $pdf = \PDF::loadView('admin.entries.entryPDF', compact('entries', 'entriesitem', 'imagepath'));
        $file = $id.'_'.$entries->number.'.pdf';

        if (\File::exists('reports/'.$file)) {
            \File::Delete('reports/'.$file);
        }

        return $pdf->download($file);
    }

    public function PrintEntry($label, $id)
    {
        $page_title = 'Entry Show';
        $page_description = 'show entries';
        $imagepath = \Auth::user()->organization->logo;

        $entries = Entry::where('id', $id)->first();
        $entriesitem = Entryitem::orderBy('id', 'asc')->where('entry_id', $entries->id)->get();

        return view('admin.entries.print', compact('entries', 'entriesitem', 'imagepath'));
    }

    public function store(Request $request)
    {

        DB::beginTransaction();

        $this->validate($request, [
        'date' => 'required',
        ]);

        if ($request->dr_total != $request->cr_total) {
            Flash::error('Credit Debit Not Matches ');

            return redirect()->back();
        }
        $image = '';
        if ($request->has('photo')) {
            $image = $this->uplaodImage($request->photo);
        }

        $attributes = $request->all();

        $attributes['org_id'] = \Auth::user()->org_id;
        $attributes['user_id'] = \Auth::user()->id;
        $attributes['dr_total'] = $request->dr_total;
        $attributes['source'] = 'Manual Entry';
        $attributes['cr_total'] = $request->cr_total;
        $attributes['image'] = $image;
        $attributes['fiscal_year_id'] = \FinanceHelper::cur_fisc_yr()->id;
        $attributes['currency'] = $request->currency;

        $entry = Entry::create($attributes);

        $dc = $request->dc;
        $ledger_id = $request->ledger_id;
        $dr_amount = $request->dr_amount;
        $cr_amount = $request->cr_amount;
        $narration = $request->narration;

        foreach ($ledger_id as $key => $value) {
            if ($value != '') {
                $detail = new Entryitem();
                $detail->entry_id = $entry->id;
                $detail->org_id = \Auth::user()->org_id;
                $detail->user_id = \Auth::user()->id;
                $detail->dc = $request->dc[$key];
                $detail->ledger_id = $request->ledger_id[$key];
                $detail->amount = $request->dr_amount[$key];
                $detail->narration = $request->narration[$key];
                $detail->save();
            }
        }

        Flash::success('Entry Created Successfully');

        DB::commit();

        return redirect('/admin/entries');
    }

    public function edit($label, $id)
    {


        $page_title = 'Entry Edit';
        $page_description = 'edit entry';

        $tags = Tag::orderBy('id', 'desc')->get();
        $types = Entrytype::orderBy('id', 'desc')->get();

        $entries = Entry::orderBy('id', 'desc')->where('id', $id)->first();

        $entriesitem = Entryitem::orderBy('id', 'asc')->where('entry_id', $id)->get();

        $currency = \App\Models\Country::select('*')->whereEnabled('1')->orderByDesc('id')->get();
        $selected_currency = $entries->currency;

        return view('admin.entries.edit', compact('page_title', 'page_description', 'entries', 'entriesitem', 'tags','currency','selected_currency','types'));
    }

    public function Update(Request $request, $id)
    {
        DB::beginTransaction();
        $this->validate($request, [

            'date' => 'required',

        ]);

        if ($request->dr_total != $request->cr_total) {
            Flash::error('Credit Debit Not Matches ');

            return redirect()->back();
        }
        $entry = Entry::find($id);
        $image = $entry->image;
        if ($request->has('photo')) {
            $image = $this->uplaodImage($request->photo);
        }
        $attributes = $request->all();
        $attributes['dr_total'] = $request->dr_total;
        $attributes['cr_total'] = $request->cr_total;
        $attributes['image'] = $image;
        $attributes['currency'] = $request->currency;
        $attributes['tag_id'] = $request->tag_id;
        $attributes['entrytype_id'] = $request->entrytype_id;

        $entry = Entry::find($id)->update($attributes);

        Entryitem::where('entry_id', $id)->delete();

        $dc = $request->dc;
        $ledger_id = $request->ledger_id;
        $dr_amount = $request->dr_amount;
        $cr_amount = $request->cr_amount;
        $narration = $request->narration;

        foreach ($ledger_id as $key => $value) {
            if ($value != '') {
                $detail = new Entryitem();
                $detail->entry_id = $id;
                $detail->dc = $request->dc[$key];
                $detail->ledger_id = $request->ledger_id[$key];
                $detail->amount = $request->dr_amount[$key];
                $detail->narration = $request->narration[$key];
                $detail->save();
            }
        }

        Flash::success('Entry Created Successfully');

        DB::commit();

        return redirect('/admin/entries');
    }

    public function ajaxAddEntry(Request $request)
    {
        $i = time() + rand(0, time()) + rand(0, time()) + rand(0, time());

        $dc_option_val = $request->dc_option_val;

        $cur_ledger_id = $request->cur_ledger_id;
        $amount = $request->amount;
        $narration = $request->narration;
        $ledger_option = $request->ledger_option;
        $dc_option = $request->dc_option;
        // $ledger_balance = $request->ledger_balance;

        // if ($cur_ledger_id == '') {
        //     $data = '<tr class="danger"><td colspan="7" style="text-align:center">Please Select a Ledger.</td></tr>';

        //     return $data;
        // }
        // if ($amount == '') {
        //     $data = '<tr class="danger"><td colspan="7" style="text-align:center">Amount is required.</td></tr>';

        //     return $data;
        // }
        // if (! is_numeric($amount)) {
        //     $data = '<tr class="danger"><td colspan="7" style="text-align:center">Invalid Amount.</td></tr>';

        //     return $data;
        // }
        // if ($narration == '') {
        //     $data = '<tr class="danger"><td colspan="7" style="text-align:center">Narration is required.</td></tr>';

        //     return $data;
        // }
        $ledger_balance = 'Dr --';
        $data = '<tr class="'.$i.'" id='.$i.'>
               <td class="'.$dc_option_val.'"><select name="dc[]" class="form-control dr_cr_toggle"><option value="D">Dr</option><option value="C" '.(  $dc_option_val == 'C' ? "selected":'' ).' >Cr</option></select>'
               .'</td><td class="'.$cur_ledger_id.'" id="cur_ledger">'.$ledger_option.'<input type="hidden" name="ledger_id[]" value="'.$cur_ledger_id.'"></td>';

        if ($dc_option_val == 'D') {
            $data .= '<td class="dr_row">'.'<input type="number" name="dr_amount[]"  step="any" value="'.$amount.'" class="form-control dr-item line-amounts input-sm"></td>
               <td class="cr_row"><strong>-</strong></td>';
        } else {
            $data .= '<td class="dr_row"><strong>-</strong></td><td class="cr_row">'.'<input type="text" name="dr_amount[]" value="'.$amount.'" class="form-control cr-item line-amounts input-sm"></td>
               ';
        }

        $data .= '<td>'.'<input type="text" name="narration[]" value="'.$narration.'" class="form-control input-sm"></td>
               <td class="ledger-balance">
               <div>'.$ledger_balance.'</div><input type="hidden" name="ledger_balance[]" value="'.$ledger_balance.'">
               </td>
               <td><span class="deleterow " escape="false"><i class="fa fa-trash deletable"></i></span></td>
              </tr>';

        return $data;
    }

    public function ajaxcl(Request $request)
    {
        $id = $request->cur_ledger_id;

        if ($id == '') {
            return 0;
        }
        $ledgers = COALedgers::where('id', $id)->where('org_id', \Auth::user()->org_id)->get();
        if (count($ledgers) == 0) {
            $cl = ['cl' => ['dc' => '', 'amount' => '']];
        } else {
            $op = COALedgers::find($id);

            $op_total = 0;
            $op_total_dc = $op->op_balance_dc;

            if (empty($op->op_balance)) {
                $op_total = 0;
            } else {
                $op_total = $op['op_balance'];
            }

            $dr_total = 0;
            $cr_total = 0;
            $dr_total_dc = 0;
            $cr_total_dc = 0;

            //Debit Amount
            $total = Entryitem::select('amount')->where('ledger_id', $request->cur_ledger_id)->where('dc', 'D')
        ->sum('amount');

            if (empty($total)) {
                $dr_total = 0;
            } else {
                $dr_total = $total;
            }

            //Credit Amount
            $total = Entryitem::select('amount)')->where('ledger_id', $request->cur_ledger_id)->where('dc', 'C')
        ->sum('amount');

            if (empty($total)) {
                $cr_total = 0;
            } else {
                $cr_total = $total;
            }

            /* Add opening balance */
            if ($op_total_dc == 'D') {
                $dr_total_dc = $op_total + $dr_total;
                $cr_total_dc = $cr_total;
            } else {
                $dr_total_dc = $dr_total;
                $cr_total_dc = $op_total + $cr_total;
            }

            /* $this->calculate and update closing balance */
            $cl = 0;
            $cl_dc = '';
            if ($dr_total_dc > $cr_total_dc) {
                $cl = $dr_total_dc - $cr_total_dc;
                $cl_dc = 'D';
            } elseif ($cr_total_dc == $dr_total_dc) {
                $cl = 0;
                $cl_dc = $op_total_dc;
            } else {
                $cl = $cr_total_dc - $dr_total_dc;
                $cl_dc = 'C';
            }

            $cl = ['dc' => $cl_dc, 'amount' => $cl, 'dr_total' => $dr_total, 'cr_total' => $cr_total];

            $status = 'ok';
            if ($op->type == 1) {
                if ($cl->dc == 'C') {
                    $status = 'neg';
                }
            }

            /* Return closing balance */
            $cl = [
                'cl' => [
                'dc' => $cl['dc'],
                'amount' => $cl['amount'],
                'status' => $status,
            ]];
        }

        return json_encode($cl);
    }

    public function getModalEntry($id)
    {
        $groups = Entry::find($id);

        $modal_title = 'Delete Entity';

        $groups = Entry::find($id);

        $modal_route = route('admin.entries.delete', ['id' => $groups->id]);

        $modal_body = 'Are you sure you want to delete this Entry?';

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    public function destroyEntry($id)
    {
        $groups = Entry::find($id);

        if ($id == '') {
            Flash::error('Entry Not Specified.');

            return redirect('/admin/entries');
        }

        Entry::find($id)->delete();
        Entryitem::where('entry_id', $id)->delete();

        Flash::success('Entry successfully deleted.');

        return redirect('/admin/entries');
    }

    public function uplaodImage($photo){
        $extension = $photo->getClientOriginalExtension();
        $filename = date('ymdhis').''.rand(888, 8888) . '.' . $extension;
        $photo->move('images/voucher/', $filename);
        return 'images/voucher/'. $filename;
    }

    public function DownloadExcel($label, $id)
    {
        $entries = Entry::where('id', $id)->first();
        // $data = Entryitem::orderBy('id', 'asc')->where('entry_id', $entries->id)->get()->toArray();
        return \Excel::download(new \App\Exports\ExcelExportFromView($id), 'entry_'.$label.'_'.$entries->number.'.xlsx');
    }


    public function importentries(Request $request)
    {
        try {
            if ($request->hasFile('import_file')) {
                $file = $request->file('import_file');
                $fiscal_year_id = \FinanceHelper::cur_fisc_yr()->id;
                \Excel::import(new \App\Imports\EntryExcelImport($fiscal_year_id), $file);
                Flash::success('Entry imported successfully');
                return redirect()->back()->with('response','Data was imported successfully!');
            }else{
             Flash::error('Select File To Import');
            return redirect()->back()->with('response','Select File Import!');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
