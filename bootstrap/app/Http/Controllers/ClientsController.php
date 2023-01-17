<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Audit as Audit;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\MasterComments;
use App\Models\OrderDetail;
use App\Models\Orders;
use App\Models\Orders as Order;
use App\Models\Product;
use App\Models\Proposal;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Role as Permission;
use App\Models\StockMove;
use App\User;
use Auth;
use DB;
use Flash;
use Illuminate\Http\Request;

/**
 * THIS CONTROLLER IS USED AS PRODUCT CONTROLLER.
 */
class ClientsController extends Controller
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @param Client $client
     * @param Permission $permission
     * @param User $user
     */
    public function __construct(Client $client, Permission $permission)
    {
        parent::__construct();
        $this->client = $client;
        $this->permission = $permission;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-index'));

        $clients = Client::where('org_id', \Auth::user()->org_id)
            ->where(function ($query) {
                if (\Request::get('clients_types') && \Request::get('clients_types') != '') {
                    return  $query->where('type', \Request::get('clients_types'));
                }
            })->where(function ($query) {
                if (\Request::get('term') && \Request::get('term') != '') {
                    return $query->where('name', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('location', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('type', 'LIKE', '%' . \Request::get('term') . '%');
                }
            })->orderBy('id', 'DESC')->paginate(25);

        $page_title = trans('admin/clients/general.page.index.title');

        $page_description = trans('admin/clients/general.page.index.description');
        $groups = \App\Models\CustomerGroup::where('org_id', \Auth::user()->org_id)->get();

        return view('admin.clients.index', compact('clients', 'page_title', 'page_description', 'groups'));
    }

    public function customers()
    {
        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-index'));
        $relation_type=\Request::get('relation_type');

        if($relation_type=="distributor"){
            $page_title = "Distributor";
            $page_description = "List of distributors";
        }elseif($relation_type=="retailer"){
            $page_title = "Retailer";
            $page_description = "List of retailers";
        }elseif($relation_type=="boothman"){
            $page_title = "Booth Man";
            $page_description = "List of boothmans";
        }elseif($relation_type=="direct_customer"){
            $page_title = "Direct Customer";
            $page_description = "List of Direct customer";
        }elseif($relation_type == "staff"){
            $page_title = "Staff";
            $page_description = "List of Staff";
        }
        $clients = Client::where('org_id', \Auth::user()->org_id)
            ->where('relation_type', $relation_type)
            ->where(function ($query) {
                if (\Request::get('clients_types') && \Request::get('clients_types') != '') {
                    return  $query->where('customer_group', \Request::get('clients_types'));
                }
            })->where(function ($query) {
                if (\Request::get('term') && \Request::get('term') != '') {
                    return $query->where('name', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('location', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('customer_group', 'LIKE', '%' . \Request::get('term') . '%');
                }
            })->orderBy('id', 'DESC')->paginate(25);


        $groups = \App\Models\CustomerGroup::where('org_id', \Auth::user()->org_id)
                //->where('type','customer')
                ->get();
            // dd($clients);
        return view('admin.clients.customers', compact('clients', 'page_title', 'page_description', 'groups'));
    }

    public function suppliers()
    {
        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-index'));

        $clients = Client::where('org_id', \Auth::user()->org_id)
            ->where('relation_type', 'supplier')
            ->where(function ($query) {
                if (\Request::get('clients_types') && \Request::get('clients_types') != '') {
                    return  $query->where('customer_group', \Request::get('clients_types'));
                }
            })->where(function ($query) {
                if (\Request::get('term') && \Request::get('term') != '') {
                    return $query->where('name', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('location', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('customer_group', 'LIKE', '%' . \Request::get('term') . '%');
                }
            })->orderBy('id', 'DESC')->paginate(25);

        $page_title = trans('admin/clients/general.page.index.title');

        $page_description = trans('admin/clients/general.page.index.description');
        $groups = \App\Models\CustomerGroup::where('org_id', \Auth::user()->org_id)
                //->where('type','supplier')
                ->get();

        return view('admin.clients.suppliers', compact('clients', 'page_title', 'page_description', 'groups'));
    }

    public function dealer()
    {
        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-index'));

        $clients = Client::where('org_id', \Auth::user()->org_id)
            ->where('relation_type', 'dealer')
            ->where(function ($query) {
                if (\Request::get('clients_types') && \Request::get('clients_types') != '') {
                    return  $query->where('customer_group', \Request::get('clients_types'));
                }
            })->where(function ($query) {
                if (\Request::get('term') && \Request::get('term') != '') {
                    return $query->where('name', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('location', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('customer_group', 'LIKE', '%' . \Request::get('term') . '%');
                }
            })->orderBy('id', 'DESC')->paginate(25);

        $page_title = trans('admin/clients/general.page.index.title');

        $page_description = trans('admin/clients/general.page.index.description');
        $groups = \App\Models\CustomerGroup::where('org_id', \Auth::user()->org_id)->get();

        return view('admin.clients.dealers', compact('clients', 'page_title', 'page_description', 'groups'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $client = $this->client->find($id);

        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-show', ['name' => $client->name]));

        $page_title = trans('admin/clients/general.page.show.title'); // "Admin | Client | Show";
        $page_description = trans('admin/clients/general.page.show.description'); // "Displaying client: :name";

        $contacts = Contact::where('client_id', $id)->get();
        $proposal = Proposal::where('client_type', 'client')->where('client_lead_id', $id)->get();

        return view('admin.clients.show', compact('client', 'page_title', 'page_description', 'contacts', 'proposal'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $page_title = trans('admin/clients/general.page.create.title'); // "Admin | Client | Create";

        $relation_type=\Request::get('relation_type');
        if($relation_type=="distributor"){
            $page_description = "Creating a new distributor";
        }elseif($relation_type=="retailer"){
            $page_description = "Creating a new retailer";
        }elseif($relation_type=="boothman"){
            $page_description = "Creating a new boothman";
        }elseif($relation_type=="direct_customer"){
            $page_title = "Direct Customer";
            $page_description = "List of Direct customer";
        }
        $distributors=\App\Models\Client::select('name','id')->where('relation_type','distributor')->where('org_id',\Auth::user()->org_id)->where('enabled',1)->pluck('name','id');
        $deliveryroutes=\App\Models\DeliveryRoute::pluck('route_name','id');
        $client = new \App\Models\Client();
        $perms = $this->permission->all();
        $groups = \App\Models\CustomerGroup::select('name','id')->where('type',\Request::get('relation_type') )->pluck('name','id');
        return view('admin.clients.create', compact('deliveryroutes','client', 'perms', 'page_title', 'page_description','groups','distributors'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function getNextCodeLedgers($id)
    {
        $group_data = \App\Models\COAgroups::find($id);
        $group_code = $group_data->code;

        $q = \App\Models\COALedgers::where('group_id', $id)->get();

        if ($q) {
            $last = $q->last();
            $last = $last->code;
            $l_array = explode('-', $last);
            $new_index = end($l_array);
            $new_index += 1;
            $new_index = sprintf('%04d', $new_index);

            return $group_code . '-' . $new_index;
        } else {
            return $group_code . '-0001';
        }
    }

    private function PostLedgers($name, $id)
    {
        $detail = new \App\Models\COALedgers();
        $staff_or_company_id = \App\Models\COAgroups::find($id);
        $detail->group_id = $id;

        $detail->org_id = \Auth::user()->org_id;
        $detail->user_id = \Auth::user()->id;

        $detail->code = $this->getNextCodeLedgers($id);
        $detail->name = $name;
        $detail->op_balance_dc = 'D';
        $detail->op_balance = 0.00;
        $detail->notes = $name;
        $detail->ledger_type = $staff_or_company_id->name;
        $detail->staff_or_company_id = $staff_or_company_id->parent_id;
        if ($request->type == 1) {
            $detail->type = $request->type;
        } else {
            $detail->type = 0;
        }
        if ($request->reconciliation == 1) {
            $detail->type = $request->reconciliation;
        } else {
            $detail->reconciliation = 0;
        }
        $detail->save();

        return $detail->id;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'phone'     => 'min:4|max:255',
            'email'     => 'email',
        ]);

        $attributes = $request->all();

        if ($request->relation_type == 'distributor') {
            $accounting_type = $request->types ?? env('CLIENT_DISTRIBUTOR_SERVICES_LEDGER_GROUP');
        }
        elseif($request->relation_type == 'retailer') {
            $accounting_type = $request->types ?? env('CLIENT_RETAILER_SERVICES_LEDGER_GROUP');
        }
        elseif($request->relation_type == 'boothman') {
            $accounting_type = $request->types ?? env('CLIENT_BOOTHMAN_SERVICES_LEDGER_GROUP');
        }
        elseif($request->relation_type == 'direct_customer') {
            $accounting_type = $request->types ?? env('CLIENT_DIRECT_CUSTOMER_SERVICES_LEDGER_GROUP');
        }
        elseif($request->relation_type == 'staff') {
            $accounting_type = $request->types ?? env('CLIENT_STAFF_SERVICES_LEDGER_GROUP');
        }
        elseif ($request->relation_type == 'supplier') {
            $accounting_type = $request->types ??
                \FinanceHelper::get_ledger_id('CLIENT_SUPPLIER_LEDGER_GROUP');
        }else{
            $accounting_type = $request->types ?? env('CLIENT_SERVICES_LEDGER_GROUP');
        }
        // if ($request->relation_type == 'customer') {
        //     $accounting_type = $request->types ?? env('CLIENT_SERVICES_LEDGER_GROUP');
        // } else {
        //     $accounting_type = $request->types ??
        //         \FinanceHelper::get_ledger_id('CLIENT_SUPPLIER_LEDGER_GROUP');
        // }

        $attributes['type'] = (\App\Models\COAgroups::find($accounting_type))->name;
        $attributes['org_id'] = \Auth::user()->org_id;

        if (!isset($attributes['enabled'])) {
            $attributes['enabled'] = 0;
        }
         if($request->file('image')){
            $files=$request->file('image');
            $doc_name= time()."".$files->getClientOriginalName();
            $destinationPath=public_path('/clientsimage/');
            $files->move($destinationPath,$doc_name);
            $doc_name='/clientsimage/'.$doc_name;
            $attributes['image']=$doc_name;
        }
// dd($attributes);
        $client = $this->client->create($attributes);
        if ($accounting_type) { // dont`t create ledger if accounting type is null
            $full_name = $client->name;
            $_ledgers = \TaskHelper::PostLedgers($full_name, $accounting_type);
            $attributes['ledger_id'] = $_ledgers;
            $client->update($attributes);
        }
        if($request->deposit_amount!="" && $request->deposit_amount){
            $deposit['user_id'] = \Auth::user()->id;
            $deposit['date'] = \Carbon\Carbon::now();
            $deposit['remarks'] ="Added the deposit amount while creating";
            $deposit['type'] = "Deposit";
            $deposit['client_id'] = $client->id;
            $deposit['amount'] =$request->deposit_amount;
            $deposit['closing'] =(float)(\App\Models\CustomerDeposit::where('client_id',$client->id)->latest()->first()->closing??0) + (float)$request->deposit_amount;
    
            \App\Models\CustomerDeposit::create($deposit);
        }
      

        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-store', ['name' => $attributes['name']]));
        // if($request->ajax()){
        //  $clients = \App\Models\Client::select('id', 'name')->orderBy('id', DESC)->get();
        //  $lastcreated = $client->id;
        // }
        Flash::success(trans('admin/clients/general.status.created')); // 'Client successfully created');

        return redirect('/admin/customer/?relation_type=' . $request->relation_type);
    }

    public function postModal(Request $request)
    {
        $attributes = $request->all();
        $validator = \Validator::make($attributes, [
            'name'      => 'required',
            'phone'     => 'min:4|max:255',
            'email'     => 'email',
        ]);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }
        $attributes['type'] = (\App\Models\COAgroups::find($request->types))->name;

        $attributes['org_id'] = \Auth::user()->org_id;
        if (!isset($attributes['enabled'])) {
            $attributes['enabled'] = 0;
        }

        $client = $this->client->create($attributes);
        $full_name = $client->name;
        $_ledgers = \TaskHelper::PostLedgers($full_name, $attributes['types']);
        $attributes['ledger_id'] = $_ledgers;
        $client->update($attributes);
        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-store', ['name' => $attributes['name']]));
        $clients = \App\Models\Client::select('id', 'name','phone')->orderBy('id', DESC)->get();
        $lastcreated = $client->id;
        return ['clients' => $clients, 'lastcreated' => $lastcreated, 'relation_type' => $request->relation_type,'lastcreatedClient'=>$client];
    }

    /**
     * @param $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $client = $this->client->find($id);

        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-edit', ['name' => $client->name]));

        $page_title = trans('admin/clients/general.page.edit.title'); // "Admin | Client | Edit";
        $page_description = trans('admin/clients/general.page.edit.description', ['name' => $client->name]); // "Editing client";

        if (!$client->isEditable() && !$client->canChangePermissions()) {
            abort(403);
        }
        $distributors=\App\Models\Client::select('name','id')->where('relation_type','distributor')->where('org_id',\Auth::user()->org_id)->where('enabled',1)->pluck('name','id');
        $deliveryroutes=\App\Models\DeliveryRoute::pluck('route_name','id');

        $ledger_list = \App\Models\COALedgers::all()->pluck('name', 'id');
        $groups = \App\Models\CustomerGroup::where('org_id', \Auth::user()->org_id)->pluck('name', 'id');

        return view('admin.clients.edit', compact('deliveryroutes','distributors','client', 'page_title', 'page_description', 'ledger_list', 'groups'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'      => 'required',
            'phone'     => 'min:4|max:255',
        ]);

        $attributes = $request->all();
        $attributes['type'] = (\App\Models\COAgroups::find($request->types))->name;
        $attributes['org_id'] = \Auth::user()->org_id;
        if($request->file('image')){
            $files=$request->file('image');
            $doc_name= time()."".$files->getClientOriginalName();
            $destinationPath=public_path('/clientsimage/');
            $files->move($destinationPath,$doc_name);
            $doc_name='/clientsimage/'.$doc_name;
            $attributes['image']=$doc_name;
        }
        if (!isset($attributes['enabled'])) {
            $attributes['enabled'] = 0;
        }
        // dd($attributes);
        $clients = $this->client->find($id);
        if ($clients->isEditable()) {
            $clients->update($attributes);
        }
        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-update', ['name' => $clients->name]));

        Flash::success(trans('admin/clients/general.status.updated')); // 'Client successfully updated');

        return redirect()->back();
    }
    public function get_bank_deposit($id){
        $customer=\App\Models\CustomerDeposit::where('client_id',$id)->latest()->first();
        // dd($customer);
        $data['deposit_amount']=$customer->closing??"0";
        $data['credit_limit']=$customer->closing??"0";
        $data['remaining_amount']=$customer->closing??"0";
        return $data;
    }
    public function get_retailer(){
        $parent_distributer=\Request::get('client_id');
        $distributor=Client::find($parent_distributer);

        $page_title=$distributor->name."'s Retailer";
        $page_description="List of Retailer";

        $relation_type="retailer";

        $clients = Client::where('org_id', \Auth::user()->org_id)
            ->where('relation_type', $relation_type)
            ->where('parent_distributor',$parent_distributer)
            ->where(function ($query) {
                if (\Request::get('clients_types') && \Request::get('clients_types') != '') {
                    return  $query->where('customer_group', \Request::get('clients_types'));
                }
            })->where(function ($query) {
                if (\Request::get('term') && \Request::get('term') != '') {
                    return $query->where('name', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('location', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('customer_group', 'LIKE', '%' . \Request::get('term') . '%');
                }
            })->orderBy('id', 'DESC')->paginate(25);


        $groups = \App\Models\CustomerGroup::where('org_id', \Auth::user()->org_id)
                ->get();
        return view('admin.clients.distributor_retailers', compact('clients', 'page_title', 'page_description', 'groups'));
    }
    public function get_boothman(){
        $parent_distributer=\Request::get('client_id');
        $distributor=Client::find($parent_distributer);

        $page_title=$distributor->name."'s Boothman";
        $page_description="List of Boothman";

        $relation_type="boothman";

        $clients = Client::where('org_id', \Auth::user()->org_id)
            ->where('relation_type', $relation_type)
            ->where('parent_distributor',$parent_distributer)
            ->where(function ($query) {
                if (\Request::get('clients_types') && \Request::get('clients_types') != '') {
                    return  $query->where('customer_group', \Request::get('clients_types'));
                }
            })->where(function ($query) {
                if (\Request::get('term') && \Request::get('term') != '') {
                    return $query->where('name', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('location', 'LIKE', '%' . \Request::get('term') . '%')
                        ->orWhere('customer_group', 'LIKE', '%' . \Request::get('term') . '%');
                }
            })->orderBy('id', 'DESC')->paginate(25);


        $groups = \App\Models\CustomerGroup::where('org_id', \Auth::user()->org_id)
                ->get();
        return view('admin.clients.distributor_boothmans', compact('clients', 'page_title', 'page_description', 'groups'));
    }
    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $clients = $this->client->find($id);

        if (!$clients->isdeletable()) {
            abort(403);
        }
        if (\App\Models\Entryitem::where('ledger_id', $clients->ledger_id)->exists()) {
            Flash::error('Entry Item Exists So Cannot Delete');

            return redirect()->back();
        }

        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-destroy', ['name' => $clients->name]));

        \App\Models\Cases::where('client_id', $id)->delete();

        $clients->delete();

        Flash::success(trans('admin/clients/general.status.deleted')); // 'Client successfully deleted');

        return redirect()->back();
    }

    /**
     * Delete Confirm.
     *
     * @param   int   $id
     * @return  View
     */
    public function getModalDelete($id)
    {
        $error = null;

        $clients = $this->client->find($id);

        if (!$clients->isdeletable()) {
            abort(403);
        }

        $modal_title = trans('admin/clients/dialog.delete-confirm.title');

        $clients = $this->client->find($id);
        $modal_route = route('admin.clients.delete', ['leadId' => $clients->id]);

        $modal_body = trans('admin/clients/dialog.delete-confirm.body', ['id' => $clients->id, 'name' => $clients->name]);

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function enable($id)
    {
        $clients = $this->client->find($id);

        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-enable', ['name' => $clients->name]));

        $clients->enabled = true;
        $clients->save();

        Flash::success(trans('admin/clients/general.status.enabled'));

        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function disable($id)
    {
        //TODO: Should we protect 'admins', 'users'??

        $clients = $this->client->find($id);

        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-disabled', ['name' => $clients->name]));

        $clients->enabled = false;
        $clients->save();

        Flash::success(trans('admin/clients/general.status.disabled'));

        return redirect()->back();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function enableSelected(Request $request)
    {
        $chkclients = $request->input('chkClient');

        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-enabled-selected'), $chkclients);

        if (isset($chkclients)) {
            foreach ($chkclients as $clients_id) {
                $clients = $this->client->find($clients_id);
                $clients->enabled = true;
                $clients->save();
            }
            Flash::success(trans('admin/clients/general.status.global-enabled'));
        } else {
            Flash::warning(trans('admin/clients/general.status.no-client-selected'));
        }

        return redirect()->back();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function disableSelected(Request $request)
    {
        //TODO: Should we protect 'admins', 'users'??

        $chkclients = $request->input('chkClient');

        Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-disabled-selected'), $chkclients);

        if (isset($chkclients)) {
            foreach ($chkclients as $clients_id) {
                $clients = $this->client->find($clients_id);
                $clients->enabled = false;
                $clients->save();
            }
            Flash::success(trans('admin/clients/general.status.global-disabled'));
        } else {
            Flash::warning(trans('admin/clients/general.status.no-client-selected'));
        }

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return array|static[]
     */
    public function searchByName(Request $request)
    {
        $return_arr = null;

        $query = $request->input('query');

        $clients = $this->client->pushCriteria(new clientsWhereDisplayNameLike($query))->all();

        foreach ($clients as $clients) {
            $id = $clients->id;
            $name = $clients->name;
            $email = $clients->email;

            $entry_arr = ['id' => $id, 'text' => "$name ($email)"];
            $return_arr[] = $entry_arr;
        }

        return $return_arr;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getInfo(Request $request)
    {
        $id = $request->input('id');
        $clients = $this->client->find($id);

        return $clients;
    }

    public function get_client()
    {
        $relation_type=\Request::get('relation_type');
        if($relation_type!='' && $relation_type){
            $clients=Client::where('relation_type',$relation_type)->where('enabled', '1')->pluck('name','id');
            return $clients->toarray();
        }else{
            $term = strtolower(\Request::get('term'));
            $contacts = Client::select('id', 'name')->where('name', 'LIKE', '%' . $term . '%')->where('enabled', '1')->groupBy('name')->take(5)->get();
            $return_array = [];

            foreach ($contacts as $v) {
                if (strpos(strtolower($v->name), $term) !== false) {
                    $return_array[] = ['value' => $v->name, 'id' => $v->id];
                }
            }

            return \Response::json($return_array);
        }
    }

    public function get_client_info(Request $request)
    {
        $temp_contact = \App\Models\Client::find($request->client_id);

        //dd($temp_contact);

        if ($temp_contact) {
            return ['data' => $temp_contact];
        } else {
            $data = [
                'phone' => '',
                'email' => '',
            ];

            return ['data' => $data];
        }
    }

    public function showmodal()
    {
        $page_title = trans('admin/clients/general.page.create.title'); // "Admin | Client | Create";
        $page_description = trans('admin/clients/general.page.create.description'); // "Creating a new client";

        $client = new \App\Models\Client();
        $perms = $this->permission->all();
        $groups = \App\Models\CustomerGroup::where('org_id', \Auth::user()->org_id)
                ->where('type',\Request::get('relation_type'))
                ->pluck('name', 'id');

        return view('admin.clients.modals.create', compact('client', 'perms', 'page_title', 'page_description', 'groups'));
    }
}
