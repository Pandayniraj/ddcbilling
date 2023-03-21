<?php

namespace App\Http\Controllers;

use App\Helpers\FinanceHelper;
use App\Models\Client;
use App\Models\CustomerDeposit;
use App\Models\DepositDeduct;
use App\Models\Entry;
use App\Models\Entryitem;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\DB;

class CustomerDepositController extends Controller
{
    public function index()
    {
        $page_title = "Deposit Amount";
        $page_description = "Deposit Amount";
        $data['id'] = \Request::get('id');
        $data['startdate'] = \Request::get('startdate');
        $data['enddate'] = \Request::get('enddate');
        $data['type'] = \Request::get('type');
        $customer = Client::find(request()->id);

        $customerdeposits = \App\Models\CustomerDeposit::where('client_id', \Request::get('id'))
            ->when(\Request::get('startdate') && \Request::get('startdate') != "", function ($q) {
                return $q->where('created_at', '<=', \Request::get('startdate'));
            })
            ->when(\Request::get('enddate') && \Request::get('enddate') != "", function ($q) {
                return $q->where('created_at', '>=', \Request::get('enddate'));
            })
            ->when(\Request::get('type') && \Request::get('type') != "", function ($q) {
                return $q->where('type', \Request::get('type'));
            })->paginate(30);

        $groups = \App\Models\COALedgers::orderBy('code', 'asc')->where('group_id', '13')->get();
        return view('admin.customerdeposits.index', compact('customerdeposits', 'page_title', 'page_description', 'data', 'groups', 'customer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customer = Client::find(request()->id);
        $page_title = "Create Deposit Amount";
        $page_description = $customer->name ?? '';
        $id = \Request::get('id');
        return view('admin.customerdeposits.create', compact('id', 'page_title', 'page_description'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $data=$request->all();
        DB::beginTransaction();
        $this->validate($request, ['amount'  => 'required']);
        $attributes['user_id'] = auth()->user()->id;
        $attributes['date'] = $request->date;
        $attributes['remarks'] = $request->remarks;
        $attributes['type'] = "Deposit";
        $attributes['client_id'] = $request->id;
        $attributes['amount'] = $request->amount;
        $attributes['balance'] = $request->amount;
        $attributes['closing'] = (float)(\App\Models\CustomerDeposit::where('client_id', $request->id)->latest()->first()->closing ?? 0) + (float)$request->amount;

        $deposit = CustomerDeposit::create($attributes);
        $attributes['client_id'] = $request->id;
        $attributes['ref_id'] = $deposit->id;
        $entry_id = FinanceHelper::depositEntry($attributes);
        $deposit->update(['entry_id' => $entry_id]);
        DB::commit();
        Flash::success('Customer Deposit Added Successfully');
        return redirect(url('/admin/customerdeposits/index?id=' . $request->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $page_title = "Create Deposit Amount";
        $page_description = "Create Deposit Amount";
        $products = \App\Models\Product::pluck('name', 'id');
        $brands = \App\Models\Brand::pluck('name', 'id');
        $customerdeposit = \App\Models\customerdeposits::find($id);

        return view('admin.customerdeposits.edit', compact('customerdeposit', 'products', 'brands', 'page_title', 'page_description'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, ['name'  => 'required|unique:products',]);
        $attributes['org_id'] = \Auth::user()->org_id;
        $attributes['created_by'] = \Auth::user()->id;
        if ($request->file('product_image')) {
            $stamp = time();
            $file = $request->file('product_image');
            $destinationPath = public_path() . '/products/';
            if (!\File::isDirectory($destinationPath)) {
                \File::makeDirectory($destinationPath, 0777, true, true);
            }
            $filename = $file->getClientOriginalName();
            $request->file('product_image')->move($destinationPath, $stamp . '_' . $filename);
            $attributes['product_image'] = $stamp . '_' . $filename;
        }
        $attributes['name'] = $request->name;
        $attributes['item_type'] = "customerdeposit";
        $attributes['brand_id'] = $request->brand_id;
        $attributes['parent_product_id'] = $request->product_id;
        $attributes['price'] = $request->price;
        $attributes['alert_qty'] = $request->qty;
        $attributes['description'] = $request->description;

        \App\Models\Product::where('id', $id)->update($attributes);

        Flash::success('Customer Deposit Update Successfully');
        return redirect(route('admin.customerdeposits.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Models\Product::where('id', $id)->delete();
        Flash::success('Customer Deposit Delete Successfully');
        return redirect(route('admin.customerdeposits.index'));
    }

    function addSettlement($request)
    {
        $deposits = CustomerDeposit::whereIn('id', $request->ids)->where('type', 'Deposit')->get();
        $deducts = CustomerDeposit::whereIn('id', $request->ids)->where('type', 'Deduct')->get();
        $batch = (DepositDeduct::latest()->first()->batch ?? 0) + 1;
        //Update deposit
        foreach ($deposits as $i => $deposit) {
            foreach ($deducts as $j => $deduct) {
                if ($deposit->balance == 0) {
                    break;
                }
                if ($deduct->balance == 0) {
                    continue;
                }
                $prevBalance = $deduct->balance;
                if ($deposit->balance >= abs($deduct->balance)) {
                    $deposit->balance -= abs($deduct->balance);
                    $deduct->balance = 0;
                } else {
                    $deduct->balance = $deposit->balance - abs($deduct->balance);
                    $deposit->balance = 0;
                }
                $deduct->save();
                $attributes = $request->only('date', 'paid_by', 'note');
                $this->processPayment($deduct, $prevBalance, $attributes, $i . $j, $deposit->id, $batch);
            }
            $deposit->save();
        }
    }
    public function settlement(Request $request)
    {
        DB::beginTransaction();
        $this->addSettlement($request);

        DB::commit();
        Flash::success("Settlement made successfully");
        return back();
    }
    function processPayment($deduct, $prevBalance, $attributes, $key, $deposit_id, $batch)
    {
        $invoice = Invoice::where('bill_no', $deduct->reference_no)->first();
        $attributes['invoice_id'] = $invoice->id;
        $attributes['amount'] = abs($prevBalance) - abs($deduct->balance);
        $attributes['reference_no'] = date('Ymds') + $key . 'd';
        $attributes['created_by'] = auth()->id();
        $attributes['type'] = "Cash";
        $attributes['payment_status'] = $deduct->balance > 0 ? 'Partial' : 'Paid';
        $attributes['client_id'] = $invoice->client_id;
        $ip = InvoicePayment::create($attributes);
        //ENTRY FOR Total AMOUNT
        $attributes['entrytype_id'] = FinanceHelper::get_entry_type_id('receipt'); //receipt
        $attributes['tag_id'] = '19'; //Invoice Payment
        $attributes['user_id'] = auth()->id();
        $attributes['org_id'] = auth()->user()->org_id;
        $attributes['number'] = FinanceHelper::get_last_entry_number($attributes['entrytype_id']);
        $attributes['date'] = \Carbon\Carbon::today();
        $attributes['dr_total'] = $attributes['amount'];
        $attributes['cr_total'] = $attributes['amount'];
        $attributes['source'] = "Invoice Deposit Settlement";
        $attributes['fiscal_year_id'] = FinanceHelper::cur_fisc_yr()->id;
        $attributes['ref_id'] = $invoice->id;
        $entry = \App\Models\Entry::create($attributes);
        \App\Models\Entryitem::where('entry_id', $entry->id)->delete();

        \App\Models\Entryitem::create([
            'entry_id' => $entry->id,
            'dc' => 'D',
            'ledger_id' => $attributes['paid_by'],
            'amount' => $attributes['amount'],
            'narration' => 'Receipt being made',
        ]);
        \App\Models\Entryitem::create([
            'entry_id' => $entry->id,
            'dc' => 'C',
            'ledger_id' => FinanceHelper::get_ledger_id('DEPOSIT_LEDGER'),
            'amount' => $attributes['amount'],
            'narration' => 'Deposit Amount',
        ]);

        $customerdeposit['user_id'] = auth()->id();
        $customerdeposit['date'] = \Carbon\Carbon::today();
        $customerdeposit['remarks'] = "Payment Made from Customer Deposit";
        $customerdeposit['type'] = "Deposit";
        $customerdeposit['client_id'] = $invoice->client->id;
        $customerdeposit['amount'] = $attributes['amount'];
        $customerdeposit['balance'] = 0;
        $customerdeposit['closing'] = (float)(\App\Models\CustomerDeposit::where('client_id', $customerdeposit['client_id'])->latest()->first()->closing ?? 0) + (float)$attributes['amount'];
        $customerdeposit['reference_no'] = $ip->reference_no;
        $cd = CustomerDeposit::create($customerdeposit);
        DepositDeduct::create(['invoice_deposit' => $deduct->id, 'amount' => $attributes['amount'], 'deduct_from' => $deposit_id, 'deposit_id' => $cd->id, 'batch' => $batch]);
        $ip->update(['entry_id' => $entry->id]);
    }

    function deleteSettlement($paymentDeposit)
    {
        $payment = InvoicePayment::where('reference_no', $paymentDeposit->reference_no)->first();
        $invoiceDeposit = CustomerDeposit::where('reference_no', $payment->invoice->bill_no)->first();
        //sub from invoice deposit
        $invoiceDeposit->balance = $invoiceDeposit->balance - $paymentDeposit->amount;
        $invoiceDeposit->save();
        //delete entries and entries items
        $entry = Entry::find($payment->entry_id);
        $entry->entry_items()->delete();
        $entry->delete();
        //delete payment
        $payment->delete();
        //delete this deposit
        $paymentDeposit->delete();

        $lastDeduct = $paymentDeposit->deducts()->latest()->first();
        if ($lastDeduct) {
            //add to deposit deposit
            $deposit = CustomerDeposit::find($lastDeduct->deduct_from);
            $deposit->balance = $deposit->balance + $paymentDeposit->amount;
            $deposit->save();
            //delete deposit deduct
            $lastDeduct->delete();
        }
    }
    public function unsettle($id)
    {
        DB::beginTransaction();
        $paymentDeposit = CustomerDeposit::find($id);
        $this->deleteSettlement($paymentDeposit);
        $this->recalculateClosing($paymentDeposit->client_id);
        DB::commit();
        Flash::success("Payment successfully unsettled");
        return back();
    }
    function recalculateClosing($client_id)
    {
        $closing = 0;
        $deposits = CustomerDeposit::where('client_id', $client_id)->get();
        foreach ($deposits as $deposit) {
            $closing = $deposit->type == "Deduct" ? $closing - $deposit->amount : $closing + $deposit->amount;
            $deposit->closing = $closing;
            $deposit->save();
        }
    }
    public function resettle($id)
    {
        $pdeposit = CustomerDeposit::find($id);
        $pmt = InvoicePayment::where('reference_no', $pdeposit->reference_no)->first();
        $batch = $pdeposit->deducts()->latest()->first()->batch;
        $batchDeducts = DepositDeduct::where('batch', $batch)->get();
        $deductIds = $batchDeducts->pluck('invoice_deposit')->toArray();
        $depositIds = $batchDeducts->pluck('deduct_from')->toArray();
        $fromDepo = true;
        if (count($batchDeducts)) {
            $deposits = CustomerDeposit::whereIn('id', $depositIds)->orWhereIn('id', $deductIds)->orderBy('type')->get();
            foreach ($deposits as $deposit) {
                if ($deposit->type == 'Deposit') {
                    $deposit->prev_balance = $deposit->balance + $batchDeducts->where('deduct_from', $deposit->id)->sum('amount');
                } else {
                    $deposit->prev_balance = $deposit->balance - $batchDeducts->where('invoice_deposit', $deposit->id)->sum('amount');
                }
            }
        } else {
            $deposits = CustomerDeposit::where('reference_no', $pmt->invoice->bill_no)->get();
            foreach ($deposits as $deposit) {
                $deposit->prev_balance = $deposit->balance - $deposit->amount;
            }
            $fromDepo = false;
        }

        $modal_title = 'Resettle Payment';
        $groups = \App\Models\COALedgers::orderBy('code', 'asc')->where('group_id', '13')->get();
        return view('admin.customerdeposits.resettle', compact('modal_title', 'groups', 'deposits', 'pmt', 'pdeposit', 'fromDepo'));
    }
    public function resettlestore(Request $request, $id)
    {
        $newRequest = new Request();
        $newRequest->query->add([
            'ids' => $request->idsu,
            'amount' => $request->amountu,
            'date' => $request->dateu,
            'paid_by' => $request->paid_byu,
            'note' => $request->noteu,
        ]);
        $pdeposit = CustomerDeposit::find($id);
        $batch = $pdeposit->deducts()->latest()->first()->batch;
        $depositIds = DepositDeduct::where('batch', $batch)->pluck('deposit_id')->toArray();
        DB::beginTransaction();
        if (count($depositIds)) {
            $deposits = CustomerDeposit::whereIn('id', $depositIds)->get();
            foreach ($deposits as $deposit) {
                $this->deleteSettlement($deposit);
            }
            $this->addSettlement($newRequest);
        } else {
            $payment = InvoicePayment::where('reference_no', $pdeposit->reference_no)->first();
            $payment->update($newRequest->only(['date', 'paid_by', 'note']));
        }

        DB::commit();
        Flash::success("Payment has been resettled successfully.");
        return back();
    }
}
