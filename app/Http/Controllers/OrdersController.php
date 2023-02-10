<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Models\MasterComments;
use App\Models\Order as Order;
use App\Models\OrderDetail;
use App\Models\Orders;
use App\Models\PosOutlets;
use App\Models\Product;
use App\Models\Role as Permission;
use App\Models\StockMove;
use App\User;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Models\Invoice;
use DB;
/**
 * THIS CONTROLLER IS USED AS PRODUCT CONTROLLER.
 */
class OrdersController extends Controller
{
    /**
     * @var Client
     */
    private $orders;
    private $invoice;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @param Client $bug
     * @param Permission $permission
     * @param User $user
     */
    public function __construct(Permission $permission, Orders $order, Invoice $invoice)
    {
        parent::__construct();
        $this->permission = $permission;
        $this->orders = $order;
        $this->invoice = $invoice;

    }



    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = \App\Models\Orders::orderBy('id', 'desc')
            ->where('org_id', \Auth::user()->org_id)
            ->where(function ($query) {
                if (\Request::get('type') && \Request::get('type') == 'quotation') {
                    return $query->where('order_type', 'quotation');
                }
            })
            ->where(function ($query) {
                if (\Request::get('type') && \Request::get('type') == 'invoice') {
                    return $query->where('order_type', 'proforma_invoice');
                }
            })
            ->where(function ($query) {
                if (\Request::get('type') && \Request::get('type') == 'order') {
                    return $query->where('order_type', 'order');
                }
            })
            ->where(function ($query) {
                if (\Request::get('fiscal_id') && \Request::get('fiscal_id') != '') {

                    return $query->where('fiscal_year_id',\Request::get('fiscal_id'));
                }
            })
            ->where(function ($query) {
                if (\Request::get('status') && \Request::get('status') != '') {
                    return $query->where('status', \Request::get('status'));
                }
            })
            ->where(function ($query) {
                if (\Request::get('customer_id') && \Request::get('customer_id') != '') {
                    return $query->where('source','lead')->where('client_id', \Request::get('customer_id'));
                }
            })
            ->where(function ($query) {
                if (\Request::get('client_id') && \Request::get('client_id') != '') {
                    return $query->where('source','client')->where('client_id', \Request::get('client_id'));
                }
            })
            ->where(function ($query) {
                if (\Request::get('location_id') && \Request::get('location_id') != '') {
                    return $query->where('from_stock_location', \Request::get('location_id'));
                }
            })
            ->where(function($query){
                if(!Auth::user()->hasRole('admins')){
                    return $query->where('user_id',Auth::user()->id);
                }
            })->where(function($query){

                $pay_status = \Request::get('pay_status');
                if($pay_status == 'Pending'){

                    return $query->where('payment_status','Pending')
                            ->orWhereNull('payment_status')
                            ->orWhere('payment_status','');

                }elseif($pay_status == 'Partial'){

                   return $query->where('payment_status','Partial');

                }elseif($pay_status == 'Paid'){

                  return  $query->where('payment_status','Paid');
                }

            })
            ->where(function($query){
                $payments = \Request::get('payment');
                if($payments == 'pending_partial'){
                    return $query->whereIn('payment_status',['Pending','Partial'])
                            ->orWhereNull('payment_status')
                            ->orWhere('payment_status','');
                }
            })
            ->paginate(25);


        $locations = \App\Models\ProductLocation::where('enabled', '1')
            ->where('org_id', \Auth::user()->org_id)
            ->pluck('location_name', 'id')->all();

        $leads = Lead::pluck('name', 'id')->all();
        $clients = Client::pluck('name', 'id')->all();

        $fiscalyears = \App\Models\Fiscalyear::orderBy('fiscal_year', 'desc')->pluck('fiscal_year', 'id')->all();


        $page_title = 'Orders';
        $page_description = 'Manage Orders';

        return view('admin.orders.index', compact('orders', 'page_title', 'page_description', 'locations', 'leads', 'fiscalyears','clients'));
    }

    //renewals

    public function renewals()
    {
        $orders = \App\Models\Orders::orderBy('id', 'desc')
            ->where('org_id', \Auth::user()->org_id)
            ->where('is_renewal', '1')
            ->where(function ($query) {
                if (\Request::get('type') && \Request::get('type') == 'quotation') {
                    return $query->where('order_type', 'quotation');
                }
            })
            ->where(function ($query) {
                if (\Request::get('type') && \Request::get('type') == 'invoice') {
                    return $query->where('order_type', 'proforma_invoice');
                }
            })
            ->where(function ($query) {
                if (\Request::get('type') && \Request::get('type') == 'order') {
                    return $query->where('order_type', 'order');
                }
            })
            ->where(function ($query) {
                if (\Request::get('fiscal_id') && \Request::get('fiscal_id') != '') {
                    $start_date = \App\Models\Fiscalyear::where('id', \Request::get('fiscal_id'))->first()->start_date;
                    $end_date = \App\Models\Fiscalyear::where('id', \Request::get('fiscal_id'))->first()->end_date;

                    return $query->whereBetween('bill_date', [$start_date, $end_date]);
                }
            })
            ->where(function ($query) {
                if (\Request::get('status') && \Request::get('status') != '') {
                    return $query->where('status', \Request::get('status'));
                }
            })
            ->where(function ($query) {
                if (\Request::get('customer_id') && \Request::get('customer_id') != '') {
                    return $query->where('client_id', \Request::get('customer_id'));
                }
            })
            ->where(function ($query) {
                if (\Request::get('location_id') && \Request::get('location_id') != '') {
                    return $query->where('from_stock_location', \Request::get('location_id'));
                }
            })
            ->paginate(25);

        $locations = \App\Models\ProductLocation::where('enabled', '1')
            ->where('org_id', \Auth::user()->org_id)
            ->pluck('location_name', 'id')->all();

        $leads = Lead::pluck('name', 'id')->all();

        $fiscalyears = \App\Models\Fiscalyear::orderBy('fiscal_year', 'desc')->pluck('fiscal_year', 'id')->all();


        $page_title = 'Renewals';
        $page_description = 'Renewal Orders';

        return view('admin.orders.renewals', compact('orders', 'page_title', 'page_description', 'locations', 'leads', 'fiscalyears'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $ord = $this->orders->find($id);
        \TaskHelper::authorizeOrg($ord);
        $page_title = 'Orders';
        $page_description = 'View Order';
        $orderDetails = OrderDetail::where('order_id', $ord->id)->get();
        $imagepath = \Auth::user()->organization->logo;

        return view('admin.orders.show', compact('ord', 'imagepath', 'page_title', 'page_description', 'orderDetails'));
    }

    public function printInvoice($id)
    {
        $ord = $this->orders->find($id);
        $orderDetails = OrderDetail::where('order_id', $ord->id)->get();
        $imagepath = \Auth::user()->organization->logo;

        return view('admin.orders.print', compact('ord', 'imagepath', 'orderDetails'));
    }

    public function copyDoc($id)
    {
        $order = Orders::where('id', $id)->first();
        $orderdetail = OrderDetail::where('order_id', $id)->get();

        $new_item = $order->replicate(); //copy attributes
        $new_item->push();
        foreach ($orderdetail as $ord) {
            $neworderdetails = $ord->toArray();
            unset($neworderdetails['id']);
            $neworderdetails['order_id'] = $new_item->id;
            OrderDetail::create($neworderdetails);
        }

        Flash::success('Quotation successfully duplicated.');

        return redirect('/admin/orders?type=quotation');
    }

    public function generatePDF($id)
    {
        $ord = $this->orders->find($id);
        $orderDetails = OrderDetail::where('order_id', $ord->id)->get();
        $imagepath = \Auth::user()->organization->logo;

        $pdf = \PDF::loadView('admin.orders.generateInvoicePDF', compact('ord', 'imagepath', 'orderDetails'));
        $file = $id . '_' . $ord->name . '_' . str_replace(' ', '_', $ord->client->name) . '.pdf';

        if (\File::exists('reports/' . $file)) {
            \File::Delete('reports/' . $file);
        }

        return $pdf->download($file);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $page_title = 'Orders';
        $page_description = 'Add Orders';
        $order = null;
        $orderDetail = null;
        $products = Product::select('id', 'name')->get();
        $users = \App\User::where('enabled', '1')->where('org_id', \Auth::user()->org_id)->pluck('first_name', 'id');

        $last_order = Orders::orderBy('id', 'Desc')->first();

        $productlocation = \App\Models\ProductLocation::pluck('location_name', 'id')->all();

        $prod_unit = \App\Models\ProductsUnit::orderBy('id', 'desc')->get();


        $lead_clients = \App\Models\Lead::select('id', 'name')->where('org_id', \Auth::user()->org_id)->orderBy('id', 'DESC')->get();
        $customer_clients = \App\Models\Client::where('enabled', '1')->where('org_id', \Auth::user()->org_id)->orderBy('id', 'desc')->get();

        return view('admin.orders.create', compact('page_title', 'users', 'productlocation', 'page_description', 'order', 'orderDetail', 'products', 'lead_clients', 'prod_unit', 'customer_clients', 'last_order'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $this->validate($request, [
            'customer_id' => 'required',
            'order_type' => 'required',
        ]);

        $org_id = \Auth::user()->org_id;

        $ckfiscalyear = \App\Models\Fiscalyear::where('current_year', '1')
                        ->where('org_id', $org_id)
                        ->first();
        if(!$ckfiscalyear){
            Flash::error("Please Set Fiscal Year First");
            return redirect()->back();
        }
        $fiscal_year = \App\Models\Fiscalyear::where('current_year', '1')->where('org_id', $org_id)->first();

        $bill_no = \DB::select("SELECT MAX(Convert(`bill_no`,SIGNED)) as last_bill from fin_orders WHERE fiscal_year_id = '$ckfiscalyear->id' AND  org_id = '$org_id' limit 1");

        $bill_no = $bill_no[0]->last_bill + 1;
        $order_attributes = $request->all();

        $order_attributes['user_id'] = \Auth::user()->id;
        $order_attributes['org_id'] = \Auth::user()->org_id;
        $order_attributes['client_id'] = $request->customer_id;
        $order_attributes['tax_amount'] = $request->taxable_tax;
        $order_attributes['total_amount'] = $request->final_total;
        $order_attributes['bill_no'] = $bill_no;
        $order_attributes['fiscal_year_id'] = $fiscal_year->id;
        $order = $this->orders->create($order_attributes);
        $isInvoice = false;
        if ($order->order_type == 'proforma_invoice') {
            $isInvoice = true;
            $this->postLedger($order->id); //post invoice to ledegr
        }

        $product_id = $request->product_id;
        $price = $request->price;
        $unit = $request->unit;
        $quantity = $request->quantity;
        $tax = $request->tax;
        $tax_amount = $request->tax_amount;
        $total = $request->total;

        foreach ($product_id ?? [] as $key => $value) {
            if ($value != '') {
                $detail = new OrderDetail();
                $detail->order_id = $order->id;
                $detail->product_id = $product_id[$key];
                $detail->price = $price[$key];
                $detail->quantity = $quantity[$key];
                $detail->unit = $unit[$key];
                $detail->tax = $tax[$key] ?? null;
                $detail->tax = $tax[$key] ?? null;
                $detail->tax_amount = $tax_amount[$key] ?? null;
                $detail->total = $total[$key];
                $detail->date = date('Y-m-d H:i:s');
                $detail->is_inventory = 1;
                $detail->save();
                if($isInvoice){
                    // create stockMove
                    $stockMove = new StockMove();
                    $stockMove->stock_id = $product_id[$key];
                    $stockMove->tran_date = \Carbon\Carbon::now();
                    $stockMove->user_id = \Auth::user()->id;
                    $stockMove->reference = 'store_out_' . $order->id;
                    $stockMove->transaction_reference_id = $order->id;
                    $stockMove->qty = '-' . $quantity[$key];
                    $stockMove->trans_type = SALESINVOICE;
                    $stockMove->order_no = $bill_no;
                    $stockMove->location = $request->from_stock_location;
                    $stockMove->order_reference = $bill_no;
                    $stockMove->save();
                }

            }
        }

        // Custom items
        $tax_id_custom = $request->custom_tax_amount;
        $custom_items_name = $request->custom_items_name;
        $custom_items_rate = $request->custom_items_rate;
        $custom_items_qty = $request->custom_items_qty;
        $custom_items_price = $request->custom_items_price;
        $custom_unit = $request->custom_unit;
        $custom_tax_amount = $request->custom_tax_amount;
        $custom_total = $request->custom_total;

        foreach ($custom_items_name ?? [] as $key => $value) {
            if ($value != '') {
                $detail = new OrderDetail();
                $detail->order_id = $order->id;
                $detail->description = $custom_items_name[$key];
                $detail->price = $custom_items_price[$key];
                $detail->quantity = $custom_items_qty[$key];
                $detail->unit = $custom_unit[$key];
                $detail->tax = $tax_id_custom[$key];
                $detail->tax_amount = $custom_tax_amount[$key];
                $detail->total = $custom_total[$key];
                $detail->date = date('Y-m-d H:i:s');
                $detail->is_inventory = 0;
                $detail->save();
            }
        }

        Flash::success('Order created Successfully.');

        if (\Request::get('order_type') && \Request::get('order_type') == 'quotation') {
            $type = 'quotation';
        } elseif (\Request::get('order_type') && \Request::get('order_type') == 'proforma_invoice') {
            $type = 'invoice';
        } elseif (\Request::get('order_type') && \Request::get('order_type') == 'order') {
            $type = 'order';
        } else {
            $type = 'quotation';
        }
        DB::commit();

        return redirect('/admin/orders?type=' . $type);
    }

    /**
     * @param $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $page_title = 'Orders';
        $page_description = 'Edit Orders';
        $order = Orders::where('id', $id)->first();
         \TaskHelper::authorizeOrg($order);
        $orderDetails = OrderDetail::where('order_id', $order->id)->get();
        $fiscal_year = \FinanceHelper::cur_fisc_yr();
        $products = Product::select('id', 'name')->get();
        // $clients = Client::select('id', 'name', 'location')->get();
        if ($order->source == 'lead') {
            $clients = \App\Models\Lead::select('id', 'name')->where('org_id', \Auth::user()->org_id)->orderBy('id', 'DESC')->get();
        } else {
            $clients = \App\Models\Client::where('enabled', '1')->where('org_id', \Auth::user()->org_id)->orderBy('id', 'desc')->get();
        }
        $users = \App\User::where('enabled', '1')->where('org_id', \Auth::user()->org_id)->pluck('first_name', 'id');
        $bill_no = \App\Models\Invoice::select('bill_no')
            ->where('fiscal_year', $fiscal_year->fiscal_year ?? null)
            ->orderBy('bill_no', 'desc')
            ->first();
        $bill_no = $bill_no->bill_no + 1;
        $productlocation = \App\Models\ProductLocation::pluck('location_name', 'id')->all();

        $units = \App\Models\ProductsUnit::orderBy('id', 'desc')->get();

        return view('admin.orders.edit', compact('page_title', 'users', 'page_description', 'units', 'productlocation', 'order', 'orderDetails', 'products', 'clients', 'bill_no'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {

      //  DB::beginTransaction();

        $this->validate($request, [

            'customer_id' => 'required',
            'order_type' => 'required',

        ]);


        $order = $this->orders->find($id);
         \TaskHelper::authorizeOrg($order);

        if ($order->isEditable()) {
            $order_attributes = $request->all();

            $order_attributes['org_id'] = \Auth::user()->org_id;
            $order_attributes['client_id'] = $request->customer_id;

            $order_attributes['tax_amount'] = $request->taxable_tax;
            $order_attributes['total_amount'] = $request->final_total;

            $order->update($order_attributes);
            $isInvoice = false;
            if ($order->order_type == 'proforma_invoice') {
                $isInvoice = true;
                $this->postLedger($order->id); //post invoice to ledegr
            }

            $orderdetail = OrderDetail::where('order_id', $order->id)->get();

            foreach ($orderdetail as $od) {
                $stockmove = StockMove::where('trans_type', SALESINVOICE)->where('reference', 'store_out_' . $order->id)->where('stock_id', $od->product_id)->delete();
            }

            OrderDetail::where('order_id', $order->id)->delete();

            $product_id = $request->product_id;
            $price = $request->price;
            $quantity = $request->quantity;
            $tax = $request->tax;
            $unit = $request->unit;
            $tax_amount = $request->tax_amount;
            $total = $request->total;

            foreach ($product_id ?? [] as $key => $value) {
                if ($value != '') {
                    $detail = new OrderDetail();
                    $detail->order_id = $order->id;
                    $detail->product_id = $product_id[$key];
                    $detail->price = $price[$key];
                    $detail->quantity = $quantity[$key];
                    $detail->tax = $tax[$key] ?? null;
                    $detail->unit = $unit[$key] ?? null;
                    $detail->tax_amount = $tax_amount[$key] ?? null;
                    $detail->total = $total[$key];
                    $detail->is_inventory = 1;
                    $detail->date = date('Y-m-d H:i:s');
                    $detail->save();
                    if($isInvoice){
                        // create stockMove
                        $stockMove = new StockMove();
                        $stockMove->stock_id = $product_id[$key];
                        $stockMove->tran_date = \Carbon\Carbon::now();
                        $stockMove->user_id = \Auth::user()->id;
                        $stockMove->reference = 'store_out_' . $order->id;
                        $stockMove->transaction_reference_id = $order->id;
                        $stockMove->qty = '-'.$quantity[$key];
                        $stockMove->trans_type = SALESINVOICE;
                        $stockMove->order_no = $order->bill_no;
                        $stockMove->location = $request->from_stock_location;
                        $stockMove->order_reference = $order->bill_no;
                        $stockMove->save();
                    }

                }
            }

            $description_custom = $request->description_custom;
            $price_custom = $request->price_custom;
            $quantity_custom = $request->quantity_custom;
            $tax_custom = $request->tax_custom;
            $unit_custom = $request->unit_custom;
            $tax_amount_custom = $request->tax_amount_custom;
            $total_custom = $request->total_custom;

            foreach ($description_custom ?? [] as $key => $value) {
                if ($value != '') {
                    $detail = new OrderDetail();
                    $detail->order_id = $order->id;
                    $detail->description = $description_custom[$key];
                    $detail->price = $price_custom[$key];
                    $detail->quantity = $quantity_custom[$key];
                    $detail->tax = $tax_custom[$key];
                    $detail->unit = $unit_custom[$key];
                    $detail->tax_amount = $tax_amount_custom[$key];
                    $detail->total = $total_custom[$key];
                    $detail->is_inventory = 0;
                    $detail->date = date('Y-m-d H:i:s');
                    $detail->save();
                }
            }

            if ($request->product_id_new != null) {
                $product_id_new = $request->product_id_new;
                $ticket_new = $request->ticket_new;
                $price_new = $request->price_new;
                $quantity_new = $request->quantity_new;
                $unit_new = $request->unit_new;
                $flight_date_new = $request->flight_date_new;
                $tax_new = $request->tax_new;
                $tax_amount_new = $request->tax_amount_new;
                $total_new = $request->total_new;

                foreach ($product_id_new as $key => $value) {
                    $detail = new OrderDetail();
                    $detail->order_id = $order->id;
                    $detail->product_id = $product_id_new[$key];
                    $detail->price = $price_new[$key];
                    $detail->quantity = $quantity_new[$key];
                    $detail->unit = $unit_new;
                    $detail->tax = $tax_new[$key];
                    $detail->tax_amount = $tax_amount_new[$key];
                    $detail->total = $total_new[$key];
                    $detail->is_inventory = 1;
                    $detail->date = date('Y-m-d H:i:s');
                    $detail->save();

                    $stockMove = new StockMove();
                    $stockMove->stock_id = $product_id_new[$key];
                    $stockMove->tran_date = \Carbon\Carbon::now();
                    $stockMove->user_id = \Auth::user()->id;
                    $stockMove->reference = 'store_out_' . $order->id;
                    $stockMove->transaction_reference_id = $order->id;
                    $stockMove->qty = '-' . $quantity_new[$key];
                    $stockMove->trans_type = SALESINVOICE;
                    $stockMove->order_no = $order->bill_no;
                    $stockMove->location = $request->from_stock_location;
                    $stockMove->order_reference = $order->bill_no;
                    $stockMove->save();
                }
            }

            // Custom items Start
            $custom_items_name_new = $request->custom_items_name_new;
            $custom_ticket_new = $request->custom_ticket_new;
            $custom_items_price_new = $request->custom_items_price_new;
            $custom_items_qty_new = $request->custom_items_qty_new;
            $custom_unit_new = $request->custom_unit_new;
            $custom_flight_date_new = $request->custom_flight_date_new;
            $tax_id_custom_new = $request->tax_id_custom_new;
            $custom_tax_amount_new = $request->custom_tax_amount_new;
            $custom_total_new = $request->custom_total_new;

            if (!empty($custom_items_name_new)) {
                foreach ($custom_items_name_new as $key => $value) {
                    $detail = new OrderDetail();
                    $detail->order_id = $order->id;
                    $detail->description = $custom_items_name_new[$key];
                    $detail->price = $custom_items_price_new[$key];
                    $detail->quantity = $custom_items_qty_new[$key];
                    $detail->unit = $custom_unit_new[$key];
                    $detail->tax = $tax_id_custom_new[$key];
                    $detail->tax_amount = $custom_tax_amount_new[$key];
                    $detail->total = $custom_total_new[$key];
                    $detail->is_inventory = 0;
                    $detail->date = date('Y-m-d H:i:s');
                    $detail->save();
                }
            }

            Flash::success('Order updated Successfully.');
            if (\Request::get('order_type') && \Request::get('order_type') == 'quotation') {
                $type = 'quotation';
            } elseif (\Request::get('order_type') && \Request::get('order_type') == 'proforma_invoice') {
                $type = 'invoice';
            } elseif (\Request::get('order_type') && \Request::get('order_type') == 'order') {
                $type = 'order';
            } else {
                $type = 'quotation';
            }

            return redirect()->back();
        } else {
            Flash::success('Error in updating Order.');
        }
       // DB::commit();
        return \Redirect::back()->withInput(\Request::all());
    }


    private function postLedger($ord_id)
    {
        $order = \App\Models\Orders::find($ord_id);

        if ($order->entry_id && $order->entry_id != '0') {
            //upadte entries
            $entry = \App\Models\Entry::find($order->entry_id);
            $entry_attr = [
                'tag_id' => '6',
                'entrytype_id' => \FinanceHelper::get_entry_type_id('sales'),
                'number' => $order->id,
                'org_id' => \Auth::user()->org_id,
                'user_id' => \Auth::user()->id,
                'date' => date('Y-m-d'),
                'dr_total' => $order->total_amount,
                'cr_total' => $order->total_amount,
                'source' => 'AUTO_ORD_INV',
            ];

            $entry->update($entry_attr);
            $clients = \App\Models\Client::find($order->client_id);

            $entry_item_c = \App\Models\Entryitem::where('entry_id', $order->entry_id)->where('dc', 'C')->first();
            $item_c = [
                'entry_id' => $entry->id,
                'org_id' => \Auth::user()->org_id,
                'dc' => 'C',
                'ledger_id' => \FinanceHelper::get_ledger_id('SALES_LEDGER_ID'), //Sales Ledger
                'amount' => $order->taxable_amount,
                'narration' => 'Sales being made',
            ];

            $entry_item_c->update($item_c);

            $entry_item_d_tax = \App\Models\Entryitem::where('entry_id', $order->entry_id)
                    ->where('dc', 'C')->where('ledger_id',
                        \FinanceHelper::get_ledger_id('SALES_TAX_LEDGER'))->first();
            $item_d_tax = [
                'entry_id' => $entry->id,
                'org_id' => \Auth::user()->org_id,
                'dc' => 'C',
                'ledger_id' => \FinanceHelper::get_ledger_id('SALES_TAX_LEDGER'),
                'amount' => $order->tax_amount,
                'narration' => 'Sales being made',
            ];
            $entry_item_d_tax->updateOrCreate($item_d_tax);

            $entry_item_d = \App\Models\Entryitem::where('entry_id', $order->entry_id)->where('dc', 'D')->where('ledger_id', $clients->ledger_id)->first();
            $item_d = [
                'entry_id' => $entry->id,
                'org_id' => \Auth::user()->org_id,
                'dc' => 'D',
                'ledger_id' => $clients->ledger_id,
                'amount' => $order->total_amount,
                'narration' => 'Sales being made',
            ];
            $entry_item_d->update($item_d);

            return 0;
        } else {
            $entrytype_id = \FinanceHelper::get_entry_type_id('sales');
            $entry = \App\Models\Entry::create([
                'tag_id' => '6',
                'entrytype_id' =>$entrytype_id,
                'number' => \FinanceHelper::get_last_entry_number($entrytype_id),
                'org_id' => \Auth::user()->org_id,
                'user_id' => \Auth::user()->id,
                'date' => date('Y-m-d'),
                'dr_total' => $order->total_amount,
                'cr_total' => $order->total_amount,
                'fiscal_year_id' => \FinanceHelper::cur_fisc_yr()->id,
                'source' => 'AUTO_ORD_INV',
            ]);

            $clients = \App\Models\Client::find($order->client_id);
            //send total to sales ledger
            $entry_item = \App\Models\Entryitem::create([
                'entry_id' => $entry->id,
                'org_id' => \Auth::user()->org_id,
                'dc' => 'C',
                'ledger_id' => \FinanceHelper::get_ledger_id('SALES_LEDGER_ID'), //Sales Ledger
                'amount' => $order->taxable_amount,
                'narration' => 'Sales being made',
            ]);

            //send the taxable amount to SALES TAX LEDGER
            $entry_item = \App\Models\Entryitem::create([
                'entry_id' => $entry->id,
                'org_id' => \Auth::user()->org_id,
                'dc' => 'C',
                'ledger_id' =>  \FinanceHelper::get_ledger_id('SALES_TAX_LEDGER'), //Sales Tax Ledger
                'amount' => $order->tax_amount,
                'narration' => 'Tax to pay',
            ]);
            //send amount before tax to customer ledger
            $entry_item = \App\Models\Entryitem::create([
                'entry_id' => $entry->id,
                'org_id' => \Auth::user()->org_id,
                'dc' => 'D',
                'ledger_id' => $clients->ledger_id,
                'amount' => $order->total_amount,
                'narration' => 'Sales being made',
            ]);

            $order->update(['entry_id' => $entry->id]);

            return 0;
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $order = $this->orders->find($id);
         \TaskHelper::authorizeOrg($order);
        $orderDetail = OrderDetail::where('order_id', $order->id)->get();

        if (!$order->isdeletable()) {
            abort(403);
        }

        $order->delete($id);

        OrderDetail::where('order_id', $id)->delete($id);

        foreach ($orderDetail as $od) {
            $stockmove = StockMove::where('trans_type', SALESINVOICE)->where('stock_id', $od->product_id)->where('reference', 'store_out_' . $order->id)->delete();
        }

        if ($order->entry_id != '') {
            $entries = \App\Models\Entry::find($order->entry_id);
            \App\Models\Entryitem::where('entry_id', $entries->id)->delete();

            \App\Models\Entry::find($order->entry_id)->delete();
        }

        MasterComments::where('type', 'orders')->where('master_id', $id)->delete();

        Flash::success('Order successfully deleted.');

        if (\Request::get('type')) {
            return redirect('/admin/orders?type=' . \Request::get('type'));
        }

        return redirect('/admin/orders?type=quotation');
    }

    public function getProductDetailAjax($productId)
    {
        if (\Request::get('term')) {
            $product = Product::select('id', 'name', 'price','distributor_price','retailer_price','boothman_price',
                'direct_customer_price','is_vat', 'cost')
                ->where('name', \Request::get('term'))->first();
        } else {
            if (request()->has('outlet_id') && (request()->outlet_id != '')) {
                $outlet = PosOutlets::findOrFail(request()->outlet_id);
                $product = Product::select('products.id', 'products.name', 'product_prices.distributor_price', 'product_prices.retailer_price',
                    'product_prices.customer_price', 'products.is_vat', 'products.product_unit')
                    ->with('unit:id,symbol')
                    ->leftJoin('product_prices', 'product_prices.product_id', 'products.id')
                    ->where('product_prices.product_id', $productId)
                    ->where('product_prices.project_id', $outlet->project_id)
                    ->first();
            } else {
                $product = Product::select('id', 'name', 'price', 'distributor_price', 'retailer_price',
                    'direct_customer_price', 'cost', 'is_vat', 'product_unit')->with('unit:id,symbol')->where('id', $productId)->first();
            }
        }
        if ($outlet->project_id) $stock=\TaskHelper::returnAvailableStock($productId, $outlet->project_id);
        else $stock=\TaskHelper::getTranslations($productId);



        $stock_moves=StockMove::selectRaw('sum(qty) as total_qty,sum(price*qty) as total_price')->where('trans_type',103)->where('stock_id',$product->id)->first();

        $avg_price=$stock_moves->total_qty!=0?(($stock_moves->total_price)/$stock_moves->total_qty):0;
        $client_type=\App\Models\Client::find(\Request::get('client'))->relation_type;
        return ['data' => json_encode($product),'stock'=>$stock,'avg_price'=>round($avg_price,2),'client_type'=>$client_type]; }

    /**
     * Delete Confirm.
     *
     * @param   int   $id
     * @return  View
     */
    public function getModalDelete($id)
    {
        $error = null;

        $orders = $this->orders->find($id);


        if (!$orders->isdeletable()) {
            abort(403);
        }

        $modal_title = 'Delete Order';


        $orders = $this->orders->find($id);
        if (\Request::get('type')) {
            $modal_route = route('admin.orders.delete', ['orderId' => $orders->id]) . '?type=' . \Request::get('type');
        } else {
            $modal_route = route('admin.orders.delete', ['orderId' => $orders->id]);
        }

        $modal_body = 'Are you sure you want to delete this order?';

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    /**
     * @param Request $request
     * @return array|static[]
     */
    public function searchByName(Request $request)
    {
        $return_arr = null;

        $query = $request->input('query');

        $orders = $this->orders->pushCriteria(new ordersWhereDisplayNameLike($query))->all();

        foreach ($orders as $orders) {
            $id = $orders->id;
            $name = $orders->name;
            $email = $orders->email;

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
        $orders = $this->orders->find($id);

        return $orders;
    }

    public function get_client()
    {
        $term = strtolower(\Request::get('term'));
        $contacts = ClientModel::select('id', 'name')->where('name', 'LIKE', '%' . $term . '%')->groupBy('name')->take(5)->get();
        $return_array = [];

        foreach ($contacts as $v) {
            if (strpos(strtolower($v->name), $term) !== false) {
                $return_array[] = ['value' => $v->name, 'id' => $v->id];
            }
        }

        return \Response::json($return_array);
    }


    public function SalesPayment()
    {
        $page_title = 'Tax Invoice Payment List';
        $page_description = 'Listing of all tax sales Payments';

        $payment_list = \App\Models\InvoicePayment::orderBy('id', 'desc')->whereNotNull('invoice_id')->get();
        $orderTopay = \App\Models\Invoice::orderBy('id')->whereIn('payment_status', ['Partial', 'Pending'])->orWhere('payment_status', null)->get();

        return view('admin.orders.salespaymentlist', compact('page_title', 'page_description', 'payment_list', 'orderTopay'));
    }

    public function paymentlist(){

        $page_title = 'Sales Payment List';
        $page_description = 'Listing of all Sales Payments';

        $payment_list = \App\Models\Payment::orderBy('id', 'desc')
                        ->whereNotNull('sale_id')->get()->groupBy('sale_id');

        $purchaseToPay = $this->orders->whereIn('payment_status',['Pending','Partial'])
                ->orWhereNull('payment_status')
                ->orWhere('payment_status','')
                ->where('order_type','proforma_invoice')
                ->get();

        return view('admin.orders.allsalespaymentlist', compact('page_title', 'page_description', 'payment_list','purchaseToPay'));



    }

    public function SalesPaymentPdf()
    {
        $payment_list = \App\Models\Payment::orderBy('id', 'desc')->whereNotNull('sale_id')->get();
        $pdf = \PDF::loadView('admin.orders.salespaymentpdf', compact('payment_list'));
        $file = 'Sales_payment' . date('_Y_m_d') . '.pdf';

        if (\File::exists('reports/' . $file)) {
            \File::Delete('reports/' . $file);
        }

        return $pdf->download($file);
    }


    public function addtoStockMoves($order_id){
        $order = $this->orders->find($order_id);
        $ordersDetails = \App\Models\OrderDetail::where('order_id',$order_id)->get();

        foreach ($ordersDetails as $key => $value) {

            $stockMove = new StockMove();
            $stockMove->order_no = $order_id;
            $stockMove->stock_id = $value->product_id;
            $stockMove->tran_date = \Carbon\Carbon::now();
            $stockMove->user_id = \Auth::user()->id;
            $stockMove->reference = 'store_out_' . $value->order_id;
            $stockMove->transaction_reference_id = $value->order_id;
            $stockMove->qty = '-' . $value->quantity;
            $stockMove->trans_type = SALESINVOICE;
            $stockMove->order_no = $order->bill_no;
            $stockMove->location = null;
            $stockMove->order_reference = $order->bill_no;
            $stockMove->save();
        }
        return false;




    }



    public function convertToPI($id)
    {

        $orders = \App\Models\Orders::find($id);

        if (!$orders) {
            Flash::error('Order Not Found #' . $id . '');

            return Redirect::back();
        }

        $leads = \App\Models\Lead::find($orders->client_id);

        if ( ($leads->company->name ?? '') ) {
            $clients_name = $leads->name;
        } else {
            $clients_name = $leads->name ?? '';
        }

        if (($leads->mob_phone ?? '')) {
            $mobile = $leads->mob_phone;
        } else {
            $mobile = '';
        }

        $clients = [
            'org_id' => \Auth::user()->org_id,
            'name' => $clients_name,
            'phone' => $mobile,
            'email' => $leads->email ?? '',
            'type' => $leads->leadType->name ??'',
            'enabled' => '1',
        ];

        $client = \App\Models\Client::create($clients);
        $full_name = $client->name;
        $_ledgers = \TaskHelper::PostLedgers($full_name, \FinanceHelper::get_ledger_id('CUSTOMER_LEDGER_GROUP'));
        $attributes['ledger_id'] = $_ledgers;
        $client->update($attributes);

        $orders->update(['client_id' => $client->id, 'order_type' => 'proforma_invoice', 'source' => 'client']);
        $leads->update(['moved_to_client' => '1']);

        $this->postLedger($id); //post invoice to ledegr
        $this->addtoStockMoves($id);
        //Audit::log(Auth::user()->id, trans('admin/clients/general.audit-log.category'), trans('admin/clients/general.audit-log.msg-store', ['name' => $client->name]));
        Flash::success('Quotation  #' . $id . ' Successfully moved to Porforma Invoice');

        return Redirect::back();
    }

    public function ConfirmconvertToPI($id)
    {
        $error = null;

        $orders = \App\Models\Orders::find($id);

        $modal_title = 'Convert to Quotation to Porforma Invoice';

        $type = \Request::get('type');
        $modal_route = route('admin.orders.convert_to_pi', ['id' => $orders->id]);

        $modal_body = 'Are you sure you want to convert To Performa Invoice ';

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

     public function showcreditnote($id)
    {
        $ord = $this->invoice->find($id);
        $page_title = 'Credit Note';
        $page_description = 'View Order';
        $orderDetails = OrderDetail::where('order_id', $ord->id)->get();
        $imagepath = \Auth::user()->organization->logo;
        return view('admin.orders.credit_note_show', compact('ord', 'imagepath', 'page_title', 'page_description', 'orderDetails'));
    }

    public function printcreditnote($id, $type)
    {
        $ord = $this->invoice->find($id);

        $orderDetails = OrderDetail::where('order_id', $ord->id)->get();

        $imagepath = \Auth::user()->organization->logo;

        if($type == 'print'){


            return view('admin.orders.credit_note_print', compact('ord', 'imagepath', 'page_title', 'page_description', 'orderDetails'));


        }


        return view('admin.orders.generatecreditnotePDF', compact('ord', 'imagepath', 'orderDetails'));
        $file = $id . '_' . $ord->name . '_' . str_replace(' ', '_', $ord->client->name) . '.pdf';
        if (\File::exists('reports/' . $file)) {
            \File::Delete('reports/' . $file);
        }
        return $pdf->download($file);
    }

}
