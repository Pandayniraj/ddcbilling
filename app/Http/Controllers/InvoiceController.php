<?php

namespace App\Http\Controllers;

use App\Helpers\FinanceHelper;
use App\Models\Audit;
use App\Models\Client;
use App\Models\COAgroups;
use App\Models\COALedgers;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoicePayment;
use App\Models\MasterComments;
use App\Models\OrderDetail;
use App\Models\PosOutlets;
use App\Models\Product;
use App\Models\Role as Permission;
use App\Models\Store;
use App\User;
use App\Models\IrdDetail;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * THIS CONTROLLER IS USED AS PRODUCT CONTROLLER.
 */
class InvoiceController extends Controller
{
    /**
     * @var Client
     */
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
    public function __construct(Permission $permission, Invoice $invoice)
    {
        parent::__construct();
        $this->permission = $permission;
        $this->invoice = $invoice;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $outletuser = \App\Models\OutletUser::where('user_id', \auth()->id())->select('outlet_id')->get()->toArray();
        $orders = Invoice::where(function ($query) {
            $start_date = \Request::get('start_date');
            $end_date = \Request::get('end_date');
            if ($start_date && $end_date) {
                return $query->where('bill_date', '>=', $start_date)
                    ->where('bill_date', '<=', $end_date);
            }

        })
            ->where(function ($q) use ($outletuser) {
                if (!\Auth::user()->hasRole('admins')) {
                    $q->whereIn('outlet_id', $outletuser);
                }
            })
            ->where(function ($query) {
                $bill_no = \Request::get('bill_no');
                if ($bill_no) {
                    return $query->where('bill_no', $bill_no);
                }
            })
            ->where(function ($query) {
                $client_id = \Request::get('client_id');
                if ($client_id) {
                    return $query->where('client_id', $client_id);
                }
            })
            ->where(function ($query) {
                $fiscal_year = \Request::get('fiscal_year');
                if ($fiscal_year) {
                    return $query->where('fiscal_year', $fiscal_year);
                }

            })->where(function ($query) {

                $outlet_id = \Request::get('outlet_id');

                if ($outlet_id) {

                    return $query->where('outlet_id', $outlet_id);
                }

            })
            ->where('org_id', \Auth::user()->org_id)
            ->orderBy('id', 'desc')
            ->paginate(30);
        $page_title = 'Invoice';
        $page_description = 'Manage Invoice';
        $clients = \App\Models\Client::select('id', 'name')->where('org_id', \Auth::user()->org_id)->orderBy('id', 'DESC')->pluck('name', 'id')->all();
        $fiscal_years = \App\Models\Fiscalyear::pluck('fiscal_year as name', 'fiscal_year as id')->all();

        $outlets = \TaskHelper::getUserOutlets();
        return view('admin.invoice.index', compact('orders', 'page_title', 'page_description', 'clients', 'fiscal_years', 'outlets'));
    }

    //renewals
    public function renewals()
    {
        $orders = Invoice::orderBy('id', 'desc')->where('is_renewal', '1')->where('org_id', \Auth::user()->org_id)->paginate(30);
        $page_title = 'Invoice Renewals';
        $page_description = 'Manage Invoice renewals';
        $fiscal_years = \App\Models\Fiscalyear::pluck('fiscal_year as name', 'fiscal_year as id')->all();

        $outlets = \TaskHelper::getUserOutlets();
        return view('admin.invoice.index', compact('orders', 'page_title', 'page_description', 'outlets', 'fiscal_years'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $ord = Invoice::find($id);
        \TaskHelper::authorizeOrg($ord);
        $page_title = 'Invoice';
        $page_description = 'View Invoice';
        $orderDetails = InvoiceDetail::where('invoice_id', $id)->get();
        $imagepath = \Auth::user()->organization->logo;

        return view('admin.invoice.show', compact('ord', 'imagepath', 'page_title', 'page_description', 'orderDetails'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // $ledgers = COALedgers::where('code', 'like', '01-01-01-01'.'%')->get();
        // foreach ($ledgers as $key=>$ledger) {
        //     $code = explode('01-01-01-01', $ledger->code);
        //     $newCode = '01-01-07'.$code[1];
        //     $ledger->code = $newCode;
        //     $ledger->save();
        // }
        // $group = COAgroups::where('name', 'Milk Supply Scheme')->first();
        // $group->code = '01-01-07';
        // $group->save();

        $page_title = 'Invoice';
        $page_description = 'Add invoice';
        $order = null;
        $orderDetail = null;
        $products = Product::select('id', 'name')->where('org_id', \Auth::user()->org_id)->get();
        $users = \App\User::where('enabled', '1')->where('org_id', \Auth::user()->org_id)->pluck('first_name', 'id');

        $productlocation = \App\Models\PosOutlets::pluck('name', 'id')->all();

        $prod_unit = \App\Models\ProductsUnit::orderBy('id', 'desc')->get();
        //$clients = Client::select('id', 'name', 'location')->orderBy('id', DESC)->get();
        $clients = \App\Models\Client::select('id', 'name', 'location')->where('org_id', \Auth::user()->org_id)->where('enabled', 1)->orderBy('id', 'DESC')->get();

        $outlets = \App\Helpers\TaskHelper::getUserOutlets();

        return view('admin.invoice.create', compact('page_title', 'users', 'page_description', 'order', 'prod_unit', 'orderDetail', 'products', 'clients', 'productlocation', 'outlets'));
    }

    public function forrandomcustomer_outlet(Request $request)
    {
        $randomcustomer_outlet = \App\Models\PosOutlets::where('id', $request->id)->first();
        return ['data' => $randomcustomer_outlet];
    }

    public function store(Request $request)
    {
        $this->validate($request, ['client_type' => 'required', 'outlet_id' => 'required']);
        if ($request->client_type == 'random_customer') $this->validate($request, ['customer_name' => 'required']);
        else $this->validate($request, ['customer_id' => 'required']);

        \DB::beginTransaction();
        $org_id = \Auth::user()->org_id;
        $ckfiscalyear = \App\Models\Fiscalyear::where('current_year', '1')
            ->where('org_id', $org_id)
            ->first();
        if (!$ckfiscalyear) {
            Flash::error("Please Set Fiscal Year First");
            return redirect()->back();
        }
        $bill_no = \DB::select("SELECT MAX(Convert(`bill_no`,SIGNED)) as last_bill from invoice WHERE fiscal_year_id = '$ckfiscalyear->id' AND  org_id = '$org_id' AND outlet_id = '$request->outlet_id' limit 1");

        $bill_no = $bill_no[0]->last_bill + 1;

        $order_attributes = $request->all();
        //  $order_attributes['user_id'] = \auth()->id();
        $order_attributes['org_id'] = \Auth::user()->org_id;
        if ($request->bill_type && $request->bill_type != '') {
            $order_attributes['bill_type'] = $request->bill_type;
        } elseif ($request->bill_type_one && $request->bill_type_one != '') {
            $order_attributes['bill_type'] = $request->bill_type_one;
        } else {
            $order_attributes['bill_type'] = $request->bill_type_two;
        }
        $order_attributes['bank_deposit'] = $request->bank_deposit;
        $order_attributes['credit_limit'] = $request->credit_limit;
        $order_attributes['deposit_amount'] = $request->deposit_amount;
        $order_attributes['customer_name'] = $request->customer_name;
        $order_attributes['remaining_amount'] = $request->remaining_amount;

        if ($request->client_type && $request->client_type != '') $order_attributes['client_type'] = $request->client_type;
        else $order_attributes['client_type'] = $request->client_type_one;

        $order_attributes['tax_amount'] = $request->taxable_tax;
        $order_attributes['total_amount'] = $request->final_total;
        $order_attributes['roundoff_amount'] = $request->roundoff_amount;
        $order_attributes['bill_no'] = $bill_no;
        $order_attributes['fiscal_year'] = $ckfiscalyear->fiscal_year;
        $order_attributes['is_bill_active'] = 1;
        $order_attributes['fiscal_year_id'] = $ckfiscalyear->id;
        $order_attributes['outlet_id'] = $request->outlet_id;
        if($request->client_type=="random_customer") {
            $client = Client::where('name', 'Random Customers')->first();

            $order_attributes['ledger_id'] = \App\Helpers\FinanceHelper::get_ledger_id("RANDOM_CUSTOMER");
            $order_attributes['client_id'] = @$client->id??0;
        } else {
            $order_attributes['client_id'] = $request->customer_id;
            $order_attributes['ledger_id'] = \App\Models\Client::find($request->customer_id)->ledger_id??0;
        }

        $productIds = $request->product_id;
        if ($order_attributes['client_type'] == 'staff') {
            foreach ($productIds as $key=>$value) {
                $product = Product::findOrFail($productIds[$key]);
                if ($product->staff_quota_frequent == 'monthly') {
                    $monthStartDate = date('Y-m-d', strtotime(date('Y-m')));
                    $monthEndDate = date('Y-m-d', strtotime('last day of this month', strtotime($monthStartDate)));
                } elseif ($product->staff_quota_frequent == 'weekly') {
                    $monthStartDate = date('Y-m-d', strtotime(date('Y-m')));
                    $monthEndDate = date('Y-m-d', strtotime('last day of this month', strtotime($monthStartDate)));
                } elseif ($product->staff_quota_frequent == 'daily') {
                    $monthStartDate = date('Y-m-d', strtotime(date('Y-m')));
                    $monthEndDate = date('Y-m-d', strtotime('last day of this month', strtotime($monthStartDate)));
                } else {
                    $monthStartDate = date('Y-m-d', strtotime(date('Y-m')));
                    $monthEndDate = date('Y-m-d', strtotime('last day of this month', strtotime($monthStartDate)));
                }

                $invoice = Invoice::where('client_id', $order_attributes['client_id'])->where('bill_date', '>=', $monthStartDate)
                    ->where('bill_date', '<=', $monthEndDate)->where('client_type', 'staff')->orderBy('created_at', 'desc')->first();

                if (isset($invoice) || ($request->quantity[$key] > $product->staff_quota)) {
                    DB::rollBack();
                    Flash::error('Please select only valid product with quantity or something else.');
                    return redirect()->back();
                }
            }
        }

        $invoice = $this->invoice->create($order_attributes);

        $deposit_deduct['user_id'] = \auth()->id();
        $deposit_deduct['date'] = $request->bill_date;
        $deposit_deduct['reference_no'] = $bill_no;
        $deposit_deduct['remarks'] = "Dedeucted From Invoice";
        $deposit_deduct['type'] = "Deduct";
        $deposit_deduct['client_id'] = $request->customer_id;
        $deposit_deduct['amount'] = $request->final_total;
        $deposit_deduct['closing'] = (float)(\App\Models\CustomerDeposit::where('client_id', $request->customer_id)->latest()->first()->closing ?? 0) - (float)$request->final_total;

        \App\Models\CustomerDeposit::create($deposit_deduct);

        $product_id = $request->product_id;

        $price = $request->price;
        $quantity = $request->quantity;
        $tax = $request->tax;
        $tax_type = $request->tax_type;

        $unit = $request->units;
        $tax_amount = $request->tax_amount;
        $total = $request->total;
        foreach ($product_id as $key => $value) {
            if (($value != '') && ($quantity[$key] != '') && ($quantity[$key]>0)) {
                $detail = new InvoiceDetail();
                $detail->client_id = $request->customer_id;
                $detail->invoice_id = $invoice->id;
                $detail->product_id = $product_id[$key];
                $detail->price = $price[$key];
                $detail->quantity = $quantity[$key];
                $detail->tax = $tax[$key] ?? null;
                $detail->unit = $unit[$key];
                $detail->tax_amount = $tax_amount[$key] ?? null;
                $detail->tax_type_id = $tax_type[$key] ?? 0;

                $detail->total = $total[$key];
                $detail->date = date('Y-m-d H:i:s');
                $detail->is_inventory = 1;
                $detail->save();

                // create stockMove
                $stockMove = new \App\Models\StockMove();
                $stockMove->stock_id = $product_id[$key];
                $stockMove->tran_date = \Carbon\Carbon::now();
                $stockMove->user_id = \auth()->id();
                $stockMove->reference = 'store_out_' . $bill_no;
                $stockMove->transaction_reference_id = $bill_no;
                $stockMove->qty = '-' . $quantity[$key];
                $stockMove->trans_type = OTHERSALESINVOICE;
                $stockMove->order_no = $bill_no;
                $stockMove->store_id = $request->outlet_id;
                $stockMove->order_reference = $bill_no;
                $stockMove->save();
            }
        }

        // Custom items
        $tax_id_custom = $request->custom_tax_amount;
        $custom_items_name = $request->custom_items_name;
        $custom_items_rate = $request->custom_items_rate;
        $custom_items_qty = $request->custom_items_qty;
        $custom_unit = $request->custom_unit;
        $custom_items_price = $request->custom_items_price;

        $custom_tax_amount = $request->custom_tax_amount;
        $custom_total = $request->custom_total;

        foreach ($custom_items_name ?? [] as $key => $value) {
            if ($value != '') {
                $detail = new InvoiceDetail();
                $detail->client_id = $request->customer_id;
                $detail->invoice_id = $invoice->id;
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
        //ENTRY FOR Total AMOUNT

        $invoicemeta = new \App\Models\InvoiceMeta();
        $invoicemeta->invoice_id = $invoice->id;
        $invoicemeta->sync_with_ird = 0;
        $invoicemeta->is_bill_active = 1;
        $invoicemeta->save();

        $this->updateentries($invoice->id, $request);
        Flash::success('Invoices created Successfully.');
        $isird = IrdDetail::select('is_ird')->first();
        if ($isird->is_ird == 1) {
            $this->postInvoicetoIRD($invoice);
        } else {
            Flash::warning('Bill not synced with ird');
        }
        \DB::commit();
        return \redirect()->route('admin.payment.invoice.create', $invoice->id);
        // php artisan return redirect('/admin/invoice1');
    }

    /**
     * @param $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $page_title = 'Invoice';
        $page_description = 'Edit invoice';
        $order = null;
        $orderDetail = null;
        $products = Product::select('id', 'name')->where('org_id', \Auth::user()->org_id)->get();
        $users = \App\User::where('enabled', '1')->where('org_id', \Auth::user()->org_id)->pluck('first_name', 'id');

        $productlocation = \App\Models\ProductLocation::pluck('location_name', 'id')->all();

        //$clients = Client::select('id', 'name', 'location')->orderBy('id', DESC)->get();
        $clients = \App\Models\Client::select('id', 'name')->where('org_id', \Auth::user()->org_id)->orderBy('id', 'DESC')->get();
        $invoice = $this->invoice->find($id);
        \App\Helpers\TaskHelper::authorizeOrg($invoice);
        $invoice_details = \App\Models\InvoiceDetail::where('invoice_id', $id)->get();

        $prod_unit = \App\Models\ProductsUnit::orderBy('id', 'desc')->get();
        //return $invoice_details;
        $outlets = \App\Helpers\TaskHelper::getUserOutlets();
        return view('admin.invoice.edit', compact('page_title', 'users', 'prod_unit', 'page_description', 'order', 'orderDetail', 'products', 'clients', 'productlocation', 'invoice', 'invoice_details', 'outlets'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        \DB::beginTransaction();
        $this->validate($request, [
            'customer_id' => 'required',
        ]);
        $invoice = $this->invoice->find($id);
        \TaskHelper::authorizeOrg($invoice);
        $product_id = $request->product_id;

        $price = $request->price;

        $quantity = $request->quantity;
        $tax = $request->tax;
        $tax_type = $request->tax_type;
        $unit = $request->units;
        $tax_amount = $request->tax_amount;
        $total = $request->total;

        InvoiceDetail::where('invoice_id', $id)->delete();
        foreach ($product_id as $key => $value) {
            if ($value != '') {
                $detail = new InvoiceDetail();
                $detail->client_id = $request->customer_id;
                $detail->invoice_id = $invoice->id;
                $detail->product_id = $product_id[$key];
                $detail->price = $price[$key];
                $detail->quantity = $quantity[$key];
                $detail->tax = $tax[$key] ?? null;
                $detail->unit = $unit[$key];
                $detail->tax_amount = $tax_amount[$key] ?? null;
                $detail->tax_type_id = $tax_type[$key] ?? 0;
                $detail->total = $total[$key];
                $detail->date = date('Y-m-d H:i:s');
                $detail->is_inventory = 1;
                $detail->save();

                // create stockMove

                $stockMove = new \App\Models\StockMove();

                $stockMove->stock_id = $product_id[$key];
                $stockMove->tran_date = \Carbon\Carbon::now();
                $stockMove->user_id = \auth()->id();
                $stockMove->reference = 'store_out_' . $invoice->bill_no;
                $stockMove->transaction_reference_id = $invoice->bill_no;
                $stockMove->qty = '-' . $quantity[$key];
                $stockMove->trans_type = OTHERSALESINVOICE;
                $stockMove->order_no = $invoice->bill_no;
                $stockMove->location = $request->outlet_id;
                $stockMove->order_reference = $invoice->bill_no;
                $stockMove->save();
            }
        }

        // Custom items
        $tax_id_custom = $request->custom_tax_amount;
        $custom_items_name = $request->custom_items_name;
        $custom_items_rate = $request->custom_items_rate;
        $custom_items_qty = $request->custom_items_qty;
        $custom_items_price = $request->custom_items_price;
        $custom_unit = $request->custom_unit;
        $custom_tax_type = $request->custom_tax_type;

        $custom_tax_amount = $request->custom_tax_amount;
        $custom_total = $request->custom_total;

        foreach ($custom_items_name ?? [] as $key => $value) {
            if ($value != '') {
                $detail = new InvoiceDetail();
                $detail->client_id = $request->customer_id;
                $detail->invoice_id = $invoice->id;
                $detail->description = $custom_items_name[$key];
                $detail->price = $custom_items_price[$key];
                $detail->quantity = $custom_items_qty[$key];
                $detail->tax = $tax_id_custom[$key];
                $detail->unit = $custom_unit[$key];
                $detail->tax_type_id = $custom_tax_type[$key] ?? 0;
                $detail->tax_amount = $custom_tax_amount[$key];
                $detail->total = $custom_total[$key];
                $detail->date = date('Y-m-d H:i:s');
                $detail->is_inventory = 0;

                $detail->save();
            }
        }

        $order_attributes = $request->all();
        $order_attributes['customer_name'] = $request->customer_name;
        $order_attributes['org_id'] = \Auth::user()->org_id;
        $order_attributes['client_id'] = $request->customer_id;
        $order_attributes['tax_amount'] = $request->taxable_tax;
        $order_attributes['total_amount'] = $request->final_total;
        $order_attributes['roundoff_amount'] = $request->roundoff_amount;
        $order_attributes['is_bill_active'] = 1;
        $invoice->update($order_attributes);
        $this->updateentries($id, $request);
        Flash::success('Invoices created Successfully.');
        \DB::commit();
        return redirect()->back();
    }

    /**
     * @param $id
     * @return
     */
    public function destroy($id)
    {
        $orders = $this->orders->find($id);
        \TaskHelper::authorizeOrg($orders);
        if (!$orders->isdeletable()) {
            abort(403);
        }

        $this->orders->delete($id);
        OrderDetail::where('order_id', $id)->delete($id);

        MasterComments::where('type', 'orders')->where('master_id', $id)->delete();

        Flash::success('Order successfully deleted.');

        if (\Request::get('type')) {
            return redirect('/admin/orders?type=' . \Request::get('type'));
        }

        return redirect('/admin/orders?type=quotation');
    }

    public function getProductDetailAjax($productId)
    {
        $product = Course::select('id', 'name', 'price', 'cost')->where('id', $productId)->first();

        return ['data' => json_encode($product)];
    }

    /**
     * Delete Confirm.
     *
     * @param int $id
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
            $modal_route = route('admin.orders.delete', ['id' => $orders->id]) . '?type=' . \Request::get('type');
        } else {
            $modal_route = route('admin.orders.delete', ['id' => $orders->id]);
        }

        $modal_body = 'Are you sure you want to delete this order?';

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    public function previewPrintInvoice($id)
    {
        $ord = $this->invoice->find($id);
        \TaskHelper::authorizeOrg($ord);
        $orderDetails = InvoiceDetail::where('invoice_id', $id)->get();

        $imagepath = \Auth::user()->organization->logo;

        return view('admin.invoice.previewPrint', compact('ord', 'imagepath', 'orderDetails'));
    }

    public function downloadPrintInvoice($id)
    {
        $ord = $this->invoice->find($id);
        // \TaskHelper::authorizeOrg($ord);
        $orderDetails = InvoiceDetail::where('invoice_id', $id)->get();

        $imagepath = \Auth::user()->organization->logo;

        // return view('admin.invoice.download-print', compact('ord', 'imagepath', 'orderDetails'));
        $pdf = \PDF::loadView('admin.invoice.download-print', compact('ord', 'imagepath', 'orderDetails'))->setPaper('a3', 'landscape');
        $file = 'invoice_'.date('Y_m_d'). '.pdf';

        return $pdf->download($file);
    }

    public function printInvoice($id)
    {
        $ord = $this->invoice->find($id);
        \TaskHelper::authorizeOrg($ord);
        $orderDetails = InvoiceDetail::where('invoice_id', $id)->get();

        $imagepath = \Auth::user()->organization->logo;
        $print_no = \App\Models\Invoiceprint::where('invoice_id', $id)->count();
        $attributes = new \App\Models\Invoiceprint();
        $attributes->invoice_id = $id;
        $attributes->printed_date = \Carbon\Carbon::now();
        $attributes->printed_by = \auth()->id();
        $attributes->save();
        $ord->update(['is_bill_printed' => 1]);

        return view('admin.invoice.print', compact('ord', 'imagepath', 'orderDetails', 'print_no'));
    }

    public function previewThermalprintInvoice($id)
    {
        $ord = $this->invoice->find($id);
        \TaskHelper::authorizeOrg($ord);
        $orderDetails = InvoiceDetail::where('invoice_id', $id)->get();

        $imagepath = \Auth::user()->organization->logo;
        return view('admin.invoice.previewThermalprint', compact('ord', 'imagepath', 'orderDetails'));
    }

    public function thermalprintInvoice($id)
    {
        $ord = $this->invoice->find($id);
        \TaskHelper::authorizeOrg($ord);
        $orderDetails = InvoiceDetail::where('invoice_id', $id)->get();

        $imagepath = \Auth::user()->organization->logo;
        $print_no = \App\Models\Invoiceprint::where('invoice_id', $id)->count();
        $attributes = new \App\Models\Invoiceprint();
        $attributes->invoice_id = $id;
        $attributes->printed_date = \Carbon\Carbon::now();
        $attributes->printed_by = \auth()->id();
        $attributes->save();
        $ord->update(['is_bill_printed' => 1]);
        return view('admin.invoice.thermalprint', compact('ord', 'imagepath', 'orderDetails', 'print_no'));
    }

    public function generatePDF($id)
    {
        $ord = $this->invoice->find($id);
        \TaskHelper::authorizeOrg($ord);
        $orderDetails = InvoiceDetail::where('invoice_id', $id)->get();
        $imagepath = \Auth::user()->organization->logo;

        $pdf = \PDF::loadView('admin.invoice.generateInvoicePDF', compact('ord', 'imagepath', 'orderDetails'));
        $file = $id . '_' . $ord->name . '_' . str_replace(' ', '_', $ord->client->name) . '.pdf';

        if (\File::exists('reports/' . $file)) {
            \File::Delete('reports/' . $file);
        }

        return $pdf->download($file);
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
        \TaskHelper::authorizeOrg($orders);
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

    public function postOrdertoInvoice(Request $request, $id)
    {


        $order = \App\Models\Orders::find($id);
        \TaskHelper::authorizeOrg($order);

        $orderdetails = OrderDetail::where('order_id', $order->order_id)->get();
        $ckfiscalyear = \App\Models\Fiscalyear::where('current_year', '1')
            ->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        if (!$ckfiscalyear) {
            return \Redirect::back()->withErrors(['Please update fiscal year <a href="/admin/fiscalyear/create">Click Here</a>!']);
        }
        $bill_no = \App\Models\Invoice::select('bill_no')
            ->where('fiscal_year', $ckfiscalyear->fiscal_year)
            ->orderBy('bill_no', 'desc')
            ->first();
        $bill_no = $bill_no->bill_no + 1;

        $invoice = new Invoice();

        $invoice->bill_no = $bill_no;
        $invoice->user_id = \auth()->id();
        $invoice->client_id = $order->client_id;
        $invoice->org_id = $order->org_id;
        $invoice->name = $order->name;
        $invoice->position = $order->position;
        $invoice->address = $order->address;
        $invoice->comment = $order->comment;
        $invoice->ship_date = $order->ship_date;
        $invoice->require_date = $order->require_date;
        $invoice->sales_tax = $order->sales_tax;
        $invoice->status = $order->status;
        $invoice->bill_date = $order->bill_date;
        $invoice->due_date = $order->due_date;
        $invoice->amount = $order->amount;
        $invoice->total_amount = $order->total_amount;
        $invoice->subtotal = $order->subtotal;
        $invoice->discount_amount = $order->discount_amount;
        $invoice->discount_note = $order->discount_note;
        $invoice->trans_type = $order->trans_type;
        $invoice->fiscal_year = $order->fiscal_year;
        $invoice->customer_pan = $order->customer_pan;
        $invoice->discount_percent = $order->discount_percent;


        $invoice->save();

        foreach ($orderdetails as $orderdetail) {
            $invoicedetail = new InvoiceDetail();
            $invoicedetail->client_id = $orderdetail->client_id;
            $invoicedetail->invoice_id = $orderdetail->order_id;
            $invoicedetail->product_id = $orderdetail->product_id;
            $invoicedetail->description = $orderdetail->description;
            $invoicedetail->price = $orderdetail->price;
            $invoicedetail->quantity = $orderdetail->quantity;
            $invoicedetail->total = $orderdetail->total;
            $invoicedetail->bill_date = $orderdetail->bill_date;
            $invoicedetail->date = $orderdetail->date;
            $invoicedetail->tax = $orderdetail->tax;
            $invoicedetail->tax_amount = $orderdetail->tax_amount;
            $invoicedetail->is_inventory = $orderdetail->is_inventory;
            $invoicedetail->save();
        }
        $order->update([
            'status' => 'Invoiced',
        ]);

        // $entry = \App\Models\Entry::create([
        //     'tag_id' => env('SALES_TAG_ID'),
        //     'entrytype_id' => \FinanceHelper::get_entry_type_id('journal'),
        //     'number' => $invoice->id,
        //     'org_id' => \Auth::user()->org_id,
        //     'user_id' => \auth()->id(),
        //     'date' => date('Y-m-d'),
        //     'fiscal_year_id' => \FinanceHelper::cur_fisc_yr()->id,
        //     'dr_total' => $invoice->total_amount,
        //     'cr_total' => $invoice->total_amount,
        // ]);

        // $clients = \App\Models\Client::find($invoice->client_id);
        // $entry_item = \App\Models\Entryitem::create([
        //     'entry_id' => $entry->id,
        //     'dc' => 'C',
        //     'ledger_id' => $clients->ledger_id,
        //     'amount' => $invoice->total_amount,
        //     'narration' => 'Purchase being made',
        // ]);

        // $entry_item = \App\Models\Entryitem::create([
        //     'entry_id' => $entry->id,
        //     'dc' => 'D',
        //     'ledger_id' => \FinanceHelper::get_ledger_id('SALES_LEDGER_ID'),
        //     'amount' => $invoice->total_amount,
        //     'narration' => 'Purchase being made',
        // ]);

        return redirect('/admin/invoice');
    }

    /**
     * Delete Confirm.
     *
     * @param int $id
     * @return  View
     */
    public function getModalConverttoInvoice($id)
    {
        $error = null;

        $orders = \App\Models\Orders::find($id);
        \TaskHelper::authorizeOrg($orders);
        $modal_title = 'Convert This to Invoice';

        $modal_route = route('admin.invoice.change', ['id' => $orders->id]);

        $modal_body = 'Are you Sure you convert This Invoice?';

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    public function invoiceVoid($id)
    {
        $error = null;

        $invoice = $this->invoice->find($id);
        \TaskHelper::authorizeOrg($invoice);
        $modal_title = 'Void invoice';

        $modal_route = route('admin.salesaccount.void', ['id' => $invoice->id]);

        $modal_body = 'Are you you want to mark invoice with ID: ' . $id . 'as void';

        return view('modal_void_reason', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    public function MakeVoid(Request $request, $id)
    {
        $invoice = $this->invoice->find($id);
        \TaskHelper::authorizeOrg($invoice);
        $invoice->update(['is_bill_active' => '0', 'void_reason' => $request->reason]);

        return redirect()->back();
    }

    public function makepayment($id)
    {
        $invoice_id = $id;
        $payment_list = \App\Models\InvoicePayment::where('invoice_id', $id)->where('type', 'Cash')->orderby('id', 'desc')->get();
        $order_detail = \App\Models\Invoice::find($id);
        \TaskHelper::authorizeOrg($order_detail);
        $lead_name = $order_detail->lead->name;
        $page_title = 'Invoice Payment List';
        $page_description = 'Receipt List Of ' . $lead_name . ' Invoice # ' . $order_detail->outlet->short_name.'/'.$order_detail->fiscal_year.'/00'.$order_detail->bill_no;

        $purchase_total = $order_detail->total_amount;
        $paid_amount = \App\Models\InvoicePayment::where('invoice_id', $id)->where('type', '!=', 'credit')->sum('amount');
        $payment_remain = $purchase_total - $paid_amount;

        return view('admin.invoice.invoicepayment', compact('page_title', 'page_description', 'invoice_id', 'payment_list', 'payment_remain'));
    }

    public function invoicePaymentcreate($id)
    {
        $page_title = 'Receive Payment ';
        $page_description = 'create payments of purchase';
        $invoice_id = $id;

        $payment_method = \App\Models\Paymentmethod::orderby('id')->pluck('name', 'id');

        $purchase_order = \App\Models\Invoice::where('id', $id)->first();
        $purchase_total = $purchase_order->total_amount;
        $paid_amount = \App\Models\InvoicePayment::where('invoice_id', $id)->where('type', '!=', 'credit')->sum('amount');
        $payment_remain = $purchase_total - $paid_amount;

        return view('admin.invoice.paymentcreate', compact('page_title', 'page_description', 'invoice_id',
            'payment_method', 'payment_remain', 'purchase_order'));
    }

    public function InvoicePaymentPost(Request $request, $id)
    {
        DB::beginTransaction();
        $attributes = $request->all();
        $attributes['created_by'] = \auth()->id();
        $invoice = \App\Models\Invoice::find($id);
        if ($request->payment_type != 'Credit') {
            $attributes['type'] = "Cash";

            if ($request->file('attachment')) {
                $stamp = time();
                $file = $request->file('attachment');

                $destinationPath = public_path() . '/attachment/';
                $filename = $file->getClientOriginalName();
                $request->file('attachment')->move($destinationPath, $stamp . '_' . $filename);

                $attributes['attachment'] = $stamp . '_' . $filename;
            }
            $invoicePayment = InvoicePayment::create($attributes);

            $paid_amount = InvoicePayment::where('invoice_id', $id)->where('type', 'Cash')->sum('amount');

            if ($invoice->client_type == 'random_customer') $customer_ledger = \App\Helpers\FinanceHelper::get_ledger_id("RANDOM_CUSTOMER");
            else {
                $invoicePayment->paid_by = $invoice->client->id??null;
                $invoicePayment->save();
                $customer_ledger = $invoice->client->ledger_id ?? 465;
            }

            if ($request->payment_type == 'Cash') {
                $attributes_purchase['type'] = 'Cash';
                if ($paid_amount >= $invoice->total_amount) {
                    $attributes_purchase['payment_status'] = 'Paid';
                    $invoice->update($attributes_purchase);
                } elseif ($paid_amount <= $invoice->total_amount && $paid_amount > 0) {
                    $attributes_purchase['payment_status'] = 'Partial';
                    $invoice->update($attributes_purchase);
                } else {
                    $attributes_purchase['payment_status'] = 'Pending';
                    $invoice->update($attributes_purchase);
                }
            }
            if (!$customer_ledger) {
                Flash::error("Create custome Ledger first !!");
                return redirect()->back();
            }

            //ENTRY FOR Total AMOUNT
            $attributes['entrytype_id'] = \FinanceHelper::get_entry_type_id('receipt'); //receipt
            $attributes['tag_id'] = '19'; //Invoice Payment
            $attributes['user_id'] = \auth()->id();
            $attributes['org_id'] = \Auth::user()->org_id;
            $attributes['number'] = \FinanceHelper::get_last_entry_number($attributes['entrytype_id']);
            $attributes['date'] = \Carbon\Carbon::today();
            $attributes['dr_total'] = $request->amount;
            $attributes['cr_total'] = $request->amount;
            if ($request->payment_type == 'Credit') $attributes['source'] = "Credit Invoice";
            else $attributes['source'] = "Invoice Cash Payment";
            $attributes['fiscal_year_id'] = \FinanceHelper::cur_fisc_yr()->id;
            $attributes['ref_id'] = $invoice->id;
            $entry = \App\Models\Entry::create($attributes);

            if ($invoice->client_type == 'random_customer') $ledgerId = \App\Helpers\FinanceHelper::get_ledger_id("RANDOM_CUSTOMER");
            $ledgerId = \App\Models\Client::find($invoice->client_id)->ledger_id ?? 465;

            \App\Models\Entryitem::where('entry_id', $entry->id)->delete();
            if ($request->payment_type == 'Cash') {
                $entry_item = \App\Models\Entryitem::create([
                    'entry_id' => $entry->id,
                    'dc' => 'D',
                    'ledger_id' => $customer_ledger??465, // cash_sales ledger id
                    'amount' => $invoice->total_amount,
                    'narration' => 'Cash payment on Sales being made',
                ]);

                $entry_item = \App\Models\Entryitem::create([
                    'entry_id' => $entry->id,
                    'dc' => 'C',
                    'ledger_id' => $ledgerId, //Sales Ledger 39
                    'amount' => $invoice->total_amount,
                    'narration' => 'Sales Amount',
                ]);
            }

            $customerdeposit['user_id'] = \auth()->id();
            $customerdeposit['date'] = \Carbon\Carbon::today();
            $customerdeposit['remarks'] = "Payment Made from Invoice payement";
            $customerdeposit['type'] = "Deposit";
            if ($invoice->client_type == 'random_customer') $customerdeposit['client_id'] = 38;
            else $customerdeposit['client_id'] = $invoice->client->id;
            $customerdeposit['amount'] = $request->amount;
            $customerdeposit['closing'] = (float)(\App\Models\CustomerDeposit::where('client_id', $customerdeposit['client_id'])->latest()->first()->closing ?? 0) + (float)$request->amount;

            \App\Models\CustomerDeposit::create($customerdeposit);

            DB::commit();
            Flash::success('Receipt Created');

            return redirect('/admin/invoice/payment/' . $id . '');
        } else {
            Flash::success('Fail to create Receipt');
            return redirect('/admin/invoice/payment/' . $id . '');
        }
    }

    public function invoicePaymentshow($id)
    {
        $page_title = 'Invoice Receipt #' . $id;
        $page_description = 'showing receipt of receipt #' . $id;
        $invoice_id = $id;

        $payment_method = \App\Models\Paymentmethod::orderby('id')->pluck('name', 'id');

        $edit = \App\Models\InvoicePayment::find($id);

        return view('admin.invoice.showpayment', compact('page_title', 'page_description', 'edit'));
    }

    private function updateentries($invoice_id, $request)
    {
        $invoice = $this->invoice->find($invoice_id);
        if ($invoice->entry_id) {
            $entry = \App\Models\Entry::find($invoice->entry_id);
            $attributes = [
                'tag_id' => '6',
                'entrytype_id' => \FinanceHelper::get_entry_type_id('sales'),
                'number' => \FinanceHelper::get_last_entry_number(11),
                'ref_id' => $invoice->id,
                'org_id' => \Auth::user()->org_id,
                'user_id' => \auth()->id(),
                'date' => date('Y-m-d'),
                'dr_total' => $request->final_total,
                'cr_total' => $request->final_total,
                'source' => 'Tax Invoice',
            ];
            $entry->update($attributes);
            if ($invoice->client_type == 'random_customer') $ledgerId = \App\Helpers\FinanceHelper::get_ledger_id("RANDOM_CUSTOMER");
            $ledgerId = \App\Models\Client::find($invoice->client_id)->ledger_id??465;

            \App\Models\Entryitem::where('entry_id', $entry->id)->delete();

            //send amount before tax to customer ledger
            $entry_item = \App\Models\Entryitem::create([
                'entry_id' => $entry->id,
                'dc' => 'C',
                'ledger_id' => 465, // cash_sales ledger id
                'amount' => $request->subtotal,
                'narration' => 'Cash payment on Sales being made',
            ]);

            $entry_item = \App\Models\Entryitem::create([
                'entry_id' => $entry->id,
                'dc' => 'D',
                'ledger_id' => $ledgerId, //Customer Ledger Id
                'amount' => $request->final_total,
                'narration' => 'Sales Amount',
            ]);

            if ($request->total_tax_amount > 0) {
                $entry_item = \App\Models\Entryitem::create([
                    'entry_id' => $entry->id,
                    'dc' => 'C',
                    'ledger_id' => \FinanceHelper::get_ledger_id('SALES_TAX_LEDGER'), //Sales Tax Ledger
                    'amount' => $request->total_tax_amount,
                    'narration' => 'Tax to pay',
                ]);
            }
            return 0;
        } else {
            $entrytype_id = \FinanceHelper::get_entry_type_id('sales');
            $entry = \App\Models\Entry::create([
                'tag_id' => '6',
                'entrytype_id' => \FinanceHelper::get_entry_type_id('sales'),
                'number' => \FinanceHelper::get_last_entry_number($entrytype_id),
                'ref_id' => $invoice->id,
                'org_id' => \Auth::user()->org_id,
                'user_id' => \auth()->id(),
                'date' => date('Y-m-d'),
                'dr_total' => $request->final_total,
                'cr_total' => $request->final_total,
                'fiscal_year_id' => \FinanceHelper::cur_fisc_yr()->id,
                'source' => 'Tax Invoice',
            ]);
            if ($invoice->client_type == 'random_customer') $ledgerId = \App\Helpers\FinanceHelper::get_ledger_id("RANDOM_CUSTOMER");
            else $ledgerId = \App\Models\Client::find($invoice->client_id)->ledger_id??465;

            //send amount before tax to customer ledger
            $entry_item = \App\Models\Entryitem::create([
                'entry_id' => $entry->id,
                'dc' => 'C',
                'ledger_id' => 465, // cash_sales ledger id
                'amount' => $request->subtotal,
                'narration' => 'Cash payment on Sales being made',
            ]);

            $entry_item = \App\Models\Entryitem::create([
                'entry_id' => $entry->id,
                'dc' => 'D',
                'ledger_id' => $ledgerId, //Customer Ledger Id
                'amount' => $request->final_total,
                'narration' => 'Sales Amount',
            ]);

            if ($request->total_tax_amount > 0) {
                $entry_item = \App\Models\Entryitem::create([
                    'entry_id' => $entry->id,
                    'dc' => 'C',
                    'ledger_id' => \FinanceHelper::get_ledger_id('SALES_TAX_LEDGER'), //Sales Tax Ledger
                    'amount' => $request->total_tax_amount,
                    'narration' => 'Tax to pay',
                ]);
            }

            $invoice->update(['entry_id' => $entry->id]);
        }
    }

    private function convertdate($date)
    {
        $date = explode('-', $date);
        $cal = new \App\Helpers\NepaliCalendar();
        $converted = $cal->eng_to_nep($date[0], $date[1], $date[2]);
        $nepdate = $converted['year'] . '.' . $converted['nmonth'] . '.' . $converted['date'];

        return $nepdate;
    }

    private function convertdateTime($date)
    {
        $date = explode('-', $date);
        $cal = new \App\Helpers\NepaliCalendar();
        $converted = $cal->eng_to_nep($date[0], $date[1], $date[2]);
        $nepdate = $converted['year'] . '/' . $converted['nmonth'] . '/' . $converted['date'];

        return $nepdate;
    }

    public function postInvoicetoIRD($invoice)
    {
        Audit::log(\auth()->id(), ' Invoice', 'Final Bill Is Created: ID-' . $invoice->id);

        if ($invoice) {
            if ($invoice->client) {
                $guest_name = @$invoice->client->name??'';
                $buyer_pan = @$invoice->client->vat??'';
            } else {
                $guest_name = $invoice->name;
                $buyer_pan = $invoice->customer_pan;
            }

            $bill_date_nepali = $this->convertdate($invoice->bill_date);
            // $bill_date_nepali = $invoice->bill_date;
            // $bill_date_nepali = new DateTime($bill_date_nepal.' '.date('H:i:s'));

            $bill_today_date_nep = date('Y-m-d H:i:s');
            // $bill_today_date_nep = new DateTime($bill_today_date_n.' '.date('H:i:s A'));
            $irddetail = IrdDetail::first();

            $data = json_encode(['username' => @$irddetail->username, 'password' => @$irddetail->username, 'seller_pan' => @$irddetail->seller_pan??'',
                'buyer_pan' => $buyer_pan, 'fiscal_year' => $invoice->fiscal_year, 'buyer_name' => $guest_name,
                'invoice_number' => $invoice->outlet->short_name . '/' . $invoice->fiscal_year . '/' . '00' . $invoice->bill_no,
                'invoice_date' => $bill_date_nepali, 'total_sales' => $invoice->total_amount, 'taxable_sales_vat' => $invoice->taxable_amount,
                'vat' => $invoice->tax_amount, 'excisable_amount' => 0, 'excise' => 0, 'taxable_sales_hst' => 0, 'hst' => 0,
                'amount_for_esf' => 0, 'esf' => 0, 'export_sales' => 0, 'tax_exempted_sales' => 0, 'isrealtime' => true,
                'datetimeClien' => $bill_today_date_nep]);

            // $data = json_encode(['username' => env('IRD_USERNAME'), 'password' => env('IRD_PASSWORD'), 'seller_pan' => env('SELLER_PAN'), 'buyer_pan' => $buyer_pan, 'fiscal_year' => $invoice->fiscal_year, 'buyer_name' => $guest_name, 'invoice_number' => env('SALES_BILL_PREFIX').$invoice->bill_no, 'invoice_date' => $bill_date_nepali, 'total_sales' => $invoice->total_amount, 'taxable_sales_vat' => $invoice->taxable_amount, 'vat' => $invoice->tax_amount, 'excisable_amount' => 0, 'excise' => 0, 'taxable_sales_hst' => 0, 'hst' => 0, 'amount_for_esf' => 0, 'esf' => 0, 'export_sales' => 0, 'tax_exempted_sales' => 0, 'isrealtime' => true, 'datetimeClient' => $bill_today_date_nep]);

            $irdsync = new \App\Models\NepalIRDSync();
            $response = $irdsync->postbill($data, @$irddetail->api_link??'');

            if ($response == 200) {
                \App\Models\InvoiceMeta::where('invoice_id', $invoice->id)->first()->update(['sync_with_ird' => 1, 'is_realtime' => 1]);

                Audit::log(Auth::user()->id, 'Hotel Invoice', 'Successfully Posted to IRD, ID-' . env('HOTEL_BILL_PREFIX') . $invoice->bill_no . ' Response:' . $response . '');

                Flash::success(' Successfully Posted to IRD. Code: ' . $response . '');
                return redirect()->back();
            } else {
                if ($response == 101) {
                    \App\Models\InvoiceMeta::where('invoice_id', $invoice->id)->first()->update(['sync_with_ird' => 1, 'is_realtime' => 1]);
                } else {
                    \App\Models\InvoiceMeta::where('invoice_id', $invoice->id)->first()->update(['is_realtime' => 1]);
                }
                Audit::log(Auth::user()->id, 'Invoice', 'Failed To post in IRD, ID-' . env('HOTEL_BILL_PREFIX') . $invoice->bill_no . ', Response:' . $response . '');
                Flash::error(' Post Cannot Due to Response Code: ' . $response . '');

                return redirect()->back();
            }
        }

        Flash::error('Bill No Not Found');

        return \Redirect::back();
    }

    public function returnfromird()
    {
        $page_title = 'Admin | Invoice | Sales | Return';
        $fiscalyear = \App\Models\Fiscalyear::orderBy('id', 'desc')->where('org_id', \Auth::user()->org_id)->get();
        $description = 'Sales Return Book';
        if (\Auth::user()->hasRole('admins')) {
            $outlets = \App\Models\PosOutlets::orderBy('id', 'DESC')
                ->where('enabled', 1)
                ->get();
        } else {
            $outletusers = \App\Models\OutletUser::where('user_id', \auth()->id())->get()->pluck('outlet_id');
            $outlets = \App\Models\PosOutlets::whereIn('id', $outletusers)
                ->orderBy('id', 'DESC')
                ->where('enabled', 1)
                ->get();
        }
        $creditnum = \App\Models\InvoiceMeta::orderBy('credit_note_no', 'desc')->where('credit_note_no', '!=', 'null')->first()->credit_note_no;
        $credit_note_no = isset($creditnum) ? (int)$creditnum + 1 : 0 + 1;
        // $credit_note_no = \App\Models\InvoiceMeta::orderBy('id', 'desc')->where('credit_note_no', '!=', 'null')->first()->credit_note_no ?? 0 + 1;

        return view('admin.invoice.invoicereturn', compact('credit_note_no', 'page_title', 'fiscalyear', 'description', 'outlets'));
    }

    public function getbillinfo()
    {
        $order = \App\Models\Invoice::where('bill_no', \Request::get('bill_no'))->where('outlet_id', \Request::get('outlet_id'))->where('fiscal_year_id', \Request::get('fiscal_year'))->first();
        return $order;
    }

    public function returnfromirdpost(Request $request)
    {
        $invoice = \App\Models\Invoice::where('org_id', \Auth::user()->org_id)->where('fiscal_year_id', $request->fiscal_year)->where('bill_no', $request->bill_no)->first();
        $invoicemeta = \App\Models\InvoiceMeta::orderBy('id', 'desc')->where('invoice_id', $invoice->id)->first();

        if ($invoicemeta === null && $invoice) {
            $invoicemeta = new \App\Models\InvoiceMeta();
            $invoicemeta->invoice_id = $invoice->id;
            $invoicemeta->sync_with_ird = 0;
            $invoicemeta->is_bill_active = 1;
            $invoicemeta->save();
        }

        if (count($invoice) == 1) {
            if ($invoice->client) {
                $guest_name = $invoice->client->name;
                $guest_pan = $invoice->client->vat??'0';
            } else {
                $guest_name = $invoice->name;
                $guest_pan = $invoice->customer_pan??'0';
            }

            $bill_date_nepali = $this->convertdateTime($invoice->bill_date);
            $cancel_date = $this->convertdate($request->cancel_date);

            $bill_today_date_nep = $this->convertdateTime(date('Y-m-d')).' '.date('H:i:s');
            $bill_today_date_nep = date('Y-m-d H:i:s');
            $irddetail = IrdDetail::first();

            //POSTING DATA TO IRD
            if($guest_pan == '') $guest_pan = '0';

            $data = json_encode(['username' => $irddetail->ird_username, 'password' => $irddetail->ird_password, 'seller_pan' => $irddetail->seller_pan, 'buyer_pan' => $guest_pan??"0", 'fiscal_year' => $invoice->fiscal_year, 'buyer_name' => $guest_name, 'ref_invoice_number' => $invoice->outlet->short_name . '/' . $invoice->fiscal_year . '/' . '00' . $invoice->bill_no, 'credit_note_date' => $cancel_date, 'credit_note_number' => $request->credit_note_no, 'reason_for_return' => $request->void_reason, 'total_sales' => $invoice->total_amount, 'taxable_sales_vat' => $invoice->taxable_amount, 'vat' => $invoice->tax_amount, 'excisable_amount' => 0, 'excise' => 0, 'taxable_sales_hst' => 0, 'hst' => 0, 'amount_for_esf' => 0, 'esf' => 0, 'export_sales' => 0, 'tax_exempted_sales' => 0, 'isrealtime' => true, 'datetimeClien' => $bill_today_date_nep]);
            $irdsync = new \App\Models\NepalIRDSync();
            $response = $irdsync->returnbill($data, $irddetail->api_return_link);
            if ($response == 200) {
                $clients = \App\Models\Client::find($invoice->client_id);

                if ($clients->ledger_id) {
                    $attributes_order['entrytype_id'] = 7; //crdeitnotes
                    $attributes_order['tag_id'] = 3; //crdeitnotes
                    $attributes_order['user_id'] = \auth()->id();
                    $attributes_order['org_id'] = \Auth::user()->org_id;
                    $attributes_order['number'] = \FinanceHelper::get_last_entry_number(7);
                    // $attributes_order['resv_id'] = $invoice->reservation_id;
                    $attributes_order['source'] = 'Sales_Return';
                    $attributes_order['date'] = \Carbon\Carbon::today();
                    $attributes_order['notes'] = 'Credit Return: ' . $invoice->id . '';

                    $attributes_order['dr_total'] = $invoice->total_amount;
                    $attributes_order['cr_total'] = $invoice->total_amount;
                    $attributes['fiscal_year_id'] = \FinanceHelper::cur_fisc_yr()->id;
                    $entry = \App\Models\Entry::create($attributes_order);

                    $cash_amount = new \App\Models\Entryitem();
                    $cash_amount->entry_id = $entry->id;
                    $cash_amount->dc = 'C';
                    $cash_amount->ledger_id = $clients->ledger_id;
                    $cash_amount->amount = $invoice->total_amount;
                    $cash_amount->narration = 'being sales Return ';
                    $cash_amount->save();

                    $cash_amount = new \App\Models\Entryitem();
                    $cash_amount->entry_id = $entry->id;
                    $cash_amount->dc = 'D';
                    $cash_amount->ledger_id = \FinanceHelper::get_ledger_id('SALES_RETURN_LEDGER');
                    $cash_amount->amount = $invoice->total_amount;
                    $cash_amount->narration = 'being Sales Return';
                    $cash_amount->save();
                }

                //UPDATING THE ORDERS TABLE
                $invoicemeta->update(['is_bill_active' => 0, 'void_reason' => $request->void_reason, 'cancel_date' => $request->cancel_date, 'credit_note_no' => $request->credit_note_no, 'is_realtime' => 1, 'credit_user_id' => \auth()->id()]);

                //UPDATE AUDIT LOG
                Audit::log(Auth::user()->id, 'Invoice', 'Bill Is Returned To IRD: ID-' . $invoice->id . ' Response :' . $response . '');
                Flash::success('Successfully Returned from IRD. Code: ' . $response . '');

                return redirect()->back();
            } else {
                if ($response == 101) {
                    $invoicemeta->update(['is_bill_active' => 0, 'is_realtime' => 1]);
                } else {
                    $invoicemeta->update(['is_realtime' => 1]);
                }

                Audit::log(Auth::user()->id, 'Invoice', 'Bill Is Returned To IRD: ID-' . $invoice->bill_no . ' Response :' . $response . '');
                Flash::error('Return Cannot Due to Response Code: ' . $response . '');
                return redirect()->back();
            }
        } else {
            Audit::log(Auth::user()->id, 'Invoice', 'Bill Is Not Found: ID-' . $invoice->id . '');
            \Flash::warning('No Bill Found Of this Number');

            return redirect()->back();
        }

        return redirect()->back();
    }

    public function returnsales()
    {
        $page_title = 'Return Sales Book';
        $page_description = 'Return Sales Book';
        $users = \App\User::where('enabled', '1')->pluck('username', 'id')->all();
        $posoutlet = PosOutlets::select('id', 'name')->get();
        $description = 'Return Sales List';
        return view('admin.invoice.returnsaleslist', compact('page_description', 'page_title', 'users', 'posoutlet', 'description'));
    }

    public function returnsaleslist(Request $request)
    {
        $page_title = 'Admin | POS | Sales Return Book';
        $users = \App\User::where('enabled', '1')->pluck('username', 'id')->all();
        $posoutlet = PosOutlets::select('id', 'name')->get();

        $op = \Request::get('op');

        $startdate = $request->start_date;
        $enddate = $request->end_date;
        $invoice = \App\Models\Invoice::select('invoice_meta.*', 'invoice.*')
            ->leftjoin('invoice_meta', 'invoice.id', '=', 'invoice_meta.invoice_id')
            ->where('invoice.bill_date', '>=', $request->start_date)
            ->where('invoice.bill_date', '<=', $request->end_date)
            ->where('invoice.org_id', \Auth::user()->org_id)
            ->where('invoice_meta.is_bill_active', 0)
            ->where(function ($query) use ($request) {
                if ($request->user_id) {
                    return $query->where('invoice.user_id', $request->user_id);
                }
            })
            ->where(function ($query) use ($request) {
                if ((int)$request->outlet) {
                    return $query->where('invoice.outlet_id', (int)$request->outlet);
                }
            })
            ->paginate(50);
        if ($op == 'print') {
            $invoice_print = \App\Models\Invoice::select('invoice_meta.*', 'invoice.*')
                ->leftjoin('invoice_meta', 'invoice.id', '=', 'invoice_meta.invoice_id')
                ->where('invoice.bill_date', '>=', $request->start_date)
                ->where('invoice.bill_date', '<=', $request->end_date)
                ->where('invoice.org_id', \Auth::user()->org_id)
                ->where('invoice_meta.is_bill_active', 0)
                ->where(function ($query) use ($request) {
                    if ($request->user_id) {
                        return $query->where('invoice.user_id', $request->user_id);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->outlet) {
                        return $query->where('invoice.outlet_id', $request->outlet);
                    }
                })
                ->get();

            return view('print.returnbook', compact('invoice_print', 'startdate', 'enddate'));
        } elseif ($op == 'pdf') {
            $invoice_pdf = \App\Models\Invoice::select('invoice_meta.*', 'invoice.*')
                ->leftjoin('invoice_meta', 'invoice.id', '=', 'invoice_meta.invoice_id')
                ->where('invoice.bill_date', '>=', $request->start_date)
                ->where('invoice.bill_date', '<=', $request->end_date)
                ->where('invoice.org_id', \Auth::user()->org_id)
                ->where('invoice_meta.is_bill_active', 0)
                ->where(function ($query) use ($request) {
                    if ($request->user_id) {
                        return $query->where('invoice.user_id', $request->user_id);
                    }
                })->where(function ($query) use ($request) {
                    if ($request->outlet) {
                        return $query->where('invoice.outlet_id', $request->outlet);
                    }
                })
                ->get();


            $pdf = \PDF::loadView('pdf.returnbook', compact('invoice_pdf', 'fiscal_year', 'startdate', 'enddate'))->setPaper('a4', 'landscape');
            $file = 'Report_returnbook_filtered' . date('_Y_m_d') . '.pdf';
            if (File::exists('reports/' . $file)) {
                File::Delete('reports/' . $file);
            }

            return $pdf->download($file);
        } elseif ($op == 'excel') {
            $invoice = \App\Models\Invoice::select('invoice_meta.*', 'invoice.*')
                ->leftjoin('invoice_meta', 'invoice.id', '=', 'invoice_meta.invoice_id')
                ->where('invoice.bill_date', '>=', $request->start_date)
                ->where('invoice.bill_date', '<=', $request->end_date)
                ->where('invoice.org_id', \Auth::user()->org_id)
                ->where('invoice_meta.is_bill_active', 0)
                ->where(function ($query) use ($request) {
                    if ($request->user_id) {
                        return $query->where('invoice.user_id', $request->user_id);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->outlet) {
                        return $query->where('invoice.outlet_id', $request->outlet);
                    }
                })
                ->get();

            return \Excel::download(new \App\Exports\CreditNoteList($invoice, 'credit Note'), 'CreditNote.xls');
        }
        $request = $request->all();
        $page_description = 'Sales Return List';
        $description = $page_description;

        return view('admin.invoice.returnsaleslist', compact('page_description', 'page_title', 'users', 'request', 'invoice', 'description', 'posoutlet'));
    }

    public function materializeview()
    {
        $page_title = 'Admin | Invoice | Sales | Materialize | Search';

        $users = \App\User::where('enabled', '1')->pluck('username', 'username as id')->all();
        $description = 'Invoice Materialize View';
        $posoutlet = \App\Models\PosOutlets::where('enabled', '1')->orderBy('id', 'desc')->get();

        return view('admin.invoice.sales_materalize', compact('page_title', 'users', 'description', 'posoutlet'));
    }

    public function materializeviewresult(Request $request)
    {
        $page_title = 'Admin | Invoice | Sales | Materialize | Results';

        $users = \App\User::where('enabled', '1')->pluck('username', 'username as id')->all();
        $posoutlet = PosOutlets::select('id', 'name')->get();
        $op = \Request::get('op');

        $startdate = $request->start_date;
        $enddate = $request->end_date;

        $sales = DB::table('invoice_materialize_view')->where('bill_date', '>=', $request->start_date)
            ->where('bill_date', '<=', $request->end_date)
            ->where(function ($query) use ($request) {
                if ($request->user_id) return $query->where('entered_by', $request->user_id);
            })
            ->where(function ($query) use ($request) {
                if ($request->outlet) return $query->where('outlet_id', $request->outlet);
            })->paginate(50);
        if ($op == 'print') {
            $sales_print = DB::table('invoice_materialize_view')->where('bill_date', '>=', $request->start_date)
                ->where('bill_date', '<=', $request->end_date)
                ->where(function ($query) use ($request) {
                    if ($request->user_id) {
                        return $query->where('entered_by', $request->user_id);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->outlet) {
                        return $query->where('outlet_id', $request->outlet);
                    }
                })
                ->get();

            return view('print.materializebook', compact('sales_print', 'startdate', 'enddate'));
        } elseif ($op == 'pdf') {
            $sales_pdf = DB::table('invoice_materialize_view')->where('bill_date', '>=', $request->start_date)
                ->where('bill_date', '<=', $request->end_date)
                ->where(function ($query) use ($request) {
                    if ($request->user_id) {
                        return $query->where('entered_by', $request->user_id);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->outlet) {
                        return $query->where('outlet_id', $request->outlet);
                    }
                })->get();


            $pdf = \PDF::loadView('pdf.materializebook', compact('sales_pdf',  'startdate', 'enddate'))->setPaper('a4', 'landscape');
            $file = 'Report_materializebook_filtered' . date('_Y_m_d') . '.pdf';
            if (File::exists('reports/' . $file)) {
                File::Delete('reports/' . $file);
            }

            return $pdf->download($file);
        } elseif ($op == 'excel') {
            $invoice = DB::table('invoice_materialize_view')->where('bill_date', '>=', $request->start_date)
                ->where('bill_date', '<=', $request->end_date)
                ->where(function ($query) use ($request) {
                    if ($request->user_id) {
                        return $query->where('entered_by', $request->user_id);
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->outlet) {
                        return $query->where('outlet_id', $request->outlet);
                    }
                })->get();
            return \Excel::download(new \App\Exports\MaterializeviewList($invoice, 'MaterializeList'), 'MaterializeList.xls');
        }

        $request = $request->all();
        $description = 'Invoice Materialize View';
        return view('admin.invoice.sales_materalize', compact('page_title', 'posoutlet', 'users', 'sales', 'request', 'description'));
    }

    public function showcreditnote($id)
    {
        $ord = $this->invoice->find($id);
        $creditnote_no = \App\Models\InvoiceMeta::where('invoice_id', $id)->first()->credit_note_no;
        $page_title = 'Credit Note';
        $page_description = 'View Order';
        $orderDetails = InvoiceDetail::where('invoice_id', $ord->id)->get();

        $imagepath = \Auth::user()->organization->logo;

        return view('admin.orders.credit_note_show', compact('ord', 'creditnote_no', 'imagepath', 'page_title', 'page_description', 'orderDetails'));
    }

    public function printcreditnote($id, $type)
    {
        $ord = $this->invoice->find($id);
        $creditnote_no = \App\Models\InvoiceMeta::where('invoice_id', $id)->first()->credit_note_no;
        $orderDetails = InvoiceDetail::where('invoice_id', $ord->id)->get();
        $imagepath = \Auth::user()->organization->logo;
        if ($type == 'print') {
            // $pdf =  \PDF::loadView('admin.orders.credit_note_print', compact('ord', 'imagepath', 'orderDetails'));
            // return  $pdf->download($file);
            return view('admin.orders.credit_note_print', compact('ord', 'imagepath', 'orderDetails', 'creditnote_no'));
        };
        return view('admin.orders.generatecreditnotePDF', compact('ord', 'imagepath', 'orderDetails', 'creditnote_no'));
        $file = $id . '_' . $ord->name . '_' . str_replace(' ', '_', $ord->client->name) . '.pdf';
        if (\File::exists('reports/' . $file)) {
            \File::Delete('reports/' . $file);
        }
        return $pdf->download($file);
    }

    public function productwisereport(Request $request)
    {
        $projects = Store::orderBy('id')->get();
        if ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all')) {
            $outlets = \App\Models\PosOutlets::where('project_id', $request->project_id)->select('name', 'id')->get();
        }
        else $outlets = \App\Models\PosOutlets::select('name', 'id')->get();

        if (!\Auth::user()->hasRole('admins'))
            $outlets = \App\Models\PosOutlets::where('project_id', \auth()->user()->project_id)->select('name', 'id')->get();

        $page_title = "Product-wise Report";
        if ($request->has('type')) {
            if ($request->type == 'export') $op = "excel";
            elseif ($request->type == 'print') $op = "pdf";
        }

        $data = [];
        if ($request->startdate && $request->startdate != "") {
            $startdate = $request->startdate;

            if ($request->enddate && $request->enddate != "") $enddate = $request->enddate;

            $nepalistartdate = \App\Helpers\TaskHelper::getNepaliDate($startdate);
            $nepalienddate = \App\Helpers\TaskHelper::getNepaliDate($enddate);
            $data = \App\Models\Invoice::where('org_id', \Auth::user()->org_id)
                ->leftJoin('invoice_detail', 'invoice.id', '=', 'invoice_detail.invoice_id')
                ->where(function ($query) use ($request) {
                    if ($request->has('outletid') && ($request->outletid != '')) {
                        $query->where('outlet_id', $request->outletid);
                    } else {
                        if (\Auth::user()->hasRole('admins')) {
                            if ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all')) {
                                $outletId = PosOutlets::where('project_id', $request->project_id)->pluck('id')->toArray();
                                $query->whereIn('outlet_id', $outletId);
                            } else {
                                $outletId = PosOutlets::where('project_id', \auth()->user()->project_id)->pluck('id')->toArray();
                                $query->whereIn('outlet_id', $outletId);
                            }
                        }
                    }
                })->where('org_id', \Auth::user()->org_id)
                ->when($startdate, function ($q) use($startdate) {
                    $q->where('invoice.bill_date', '>=', $startdate);
                })->when($enddate, function ($q) use($enddate) {
                    $q->where('invoice.bill_date', '<=', $enddate);
                })->get();
            $data = $data->groupby(['product_id', 'client_type']);
            $products = \App\Models\Product::where('org_id', \Auth::user()->org_id)->pluck('name', 'id');
            $organization = \Auth::user()->organization;
            $stock_entries = \App\Models\StockMove::select('stock_id', DB::raw('SUM(qty) as quantity'))
                ->where('org_id', $organization->id)
                ->where('tran_date', '>=', $startdate)
                ->where('tran_date', '<=', $enddate)
                ->where('order_reference', null)
                ->where(function ($query) use ($request) {
                    if ($request->has('outletid') && ($request->outletid != '')) {
                        $query->where('store_id', $request->outletid);
                    }
                    elseif ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all')) {
                        $outletId = PosOutlets::where('project_id', $request->project_id)->pluck('id')->toArray();
                        $query->whereIn('store_id', $outletId);
                    }
                })->groupby('stock_id')->get();
            $stock = $stock_entries->groupby('stock_id');
            $outletname = \App\Models\PosOutlets::where('id', $request->outletid??'')->select('name')->first();
            $file = $startdate . '_' . $enddate . '_' . str_replace(' ', '_', $organization->organization_name);

            if ($op == "excel") {
                return \Excel::download(new \App\Exports\Reports\ProductWiseSalesReport($stock, $data, $products,
                    $nepalistartdate, $outletname, $nepalienddate, $organization), $file.'.xls');
            } elseif ($op == "pdf") {
                $pdf = \PDF::loadView('admin.reports.pdf.productwisereportPDF', compact('stock', 'data', 'products',
                    'nepalistartdate', 'outletname', 'nepalienddate', 'organization'))->setPaper('a3', 'landscape');
                $f = $file . '.pdf';
                if (\File::exists('reports/' . $f)) {
                    \File::Delete('reports/' . $f);
                }
                return $pdf->download($f);
            }
            return view('admin.reports.dailysalesreport', compact('page_title', 'outlets', 'projects', 'stock',
                'data', 'products', 'nepalistartdate', 'outletname', 'nepalienddate', 'organization', 'startdate', 'enddate'));
        }
        return view('admin.reports.dailysalesreport', compact('page_title', 'outlets', 'projects'));
    }

    public function stockwiseReport(Request $request)
    {
        $projects = Store::orderBy('id')->get();
        if ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all')) {
            $outlets = \App\Models\PosOutlets::where('project_id', $request->project_id)->select('name', 'id')->get();
        }
        else $outlets = \App\Models\PosOutlets::select('name', 'id')->get();

        if (!\Auth::user()->hasRole('admins'))
            $outlets = \App\Models\PosOutlets::where('project_id', \auth()->user()->project_id)->select('name', 'id')->get();

        $page_title = "Stock Report";
        $page_description = "Stock Wise Sales Report";
        if ($request->has('type')) {
            if ($request->type == 'export') $op = "excel";
            elseif ($request->type == 'print') $op = "pdf";
        }

        $data = [];
        if ($request->startdate && $request->startdate != "") {
            $startdate = $request->startdate;
            if ($request->enddate && $request->enddate != "") $enddate = $request->enddate;

            $nepalistartdate = \App\Helpers\TaskHelper::getNepaliDate($startdate);
            $nepalienddate = \App\Helpers\TaskHelper::getNepaliDate($enddate);
            $data = \App\Models\Invoice::where('org_id', \Auth::user()->org_id)
                ->leftJoin('invoice_detail', 'invoice.id', '=', 'invoice_detail.invoice_id')
                ->where(function ($query) use ($request) {
                    if ($request->has('outletid') && ($request->outletid != '')) {
                        $query->where('outlet_id', $request->outletid);
                    } else {
                        if (\Auth::user()->hasRole('admins')) {
                            if ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all')) {
                                $outletId = PosOutlets::where('project_id', $request->project_id)->pluck('id')->toArray();
                                $query->whereIn('outlet_id', $outletId);
                            } else {
                                $outletId = PosOutlets::where('project_id', \auth()->user()->project_id)->pluck('id')->toArray();
                                $query->whereIn('outlet_id', $outletId);
                            }
                        }
                    }
                })->where('org_id', \Auth::user()->org_id)
                ->when($startdate, function ($q) use($startdate) {
                    $q->where('invoice.bill_date', '>=', $startdate);
                })->when($enddate, function ($q) use($enddate) {
                    $q->where('invoice.bill_date', '<=', $enddate);
                })->get();
            $data = $data->groupby(['product_id', 'client_type']);
            $products = \App\Models\Product::where('org_id', \Auth::user()->org_id)->pluck('name', 'id');
            $organization = \Auth::user()->organization;
            $stock_entries = \App\Models\StockMove::select('stock_id', DB::raw('SUM(qty) as quantity'))
                ->where('org_id', $organization->id)
                ->where('tran_date', '>=', $startdate)
                ->where('tran_date', '<=', $enddate)
                ->where('order_reference', null)
                ->where(function ($query) use ($request) {
                    if ($request->has('outletid') && ($request->outletid != '')) {
                        $query->where('store_id', $request->outletid);
                    }
                    elseif ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all')) {
                        $outletId = PosOutlets::where('project_id', $request->project_id)->pluck('id')->toArray();
                        $query->whereIn('store_id', $outletId);
                    }
                })->groupby('stock_id')->get();
            $stock = $stock_entries->groupby('stock_id');
            $outletname = \App\Models\PosOutlets::where('id', $request->outletid??'')->select('name')->first();
            $file = $startdate . '_' . $enddate . '_' . str_replace(' ', '_', $organization->organization_name);

            if ($op == "excel") {
                return \Excel::download(new \App\Exports\Reports\StockWiseSalesReport($stock, $data, $products,
                    $nepalistartdate, $outletname, $nepalienddate, $organization), $file.'.xls');
            } elseif ($op == "pdf") {
                $pdf = \PDF::loadView('admin.reports.pdf.stockwiseSalesReportPDF', compact('stock', 'data', 'products',
                    'nepalistartdate', 'outletname', 'nepalienddate', 'organization'))->setPaper('a3', 'landscape');
                $f = $file . '.pdf';
                if (\File::exists('reports/' . $f)) {
                    \File::Delete('reports/' . $f);
                }
                return $pdf->download($f);
            }
            return view('admin.reports.stockreport', compact('page_title', 'page_description', 'outlets', 'projects', 'stock',
                'data', 'products', 'nepalistartdate', 'outletname', 'nepalienddate', 'organization', 'startdate', 'enddate'));
        }
        return view('admin.reports.stockreport', compact('page_title', 'page_description', 'outlets', 'projects'));
    }

    public function transactionreports(Request $request)
    {
        $projects = Store::orderBy('id')->get();
        if ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all'))
            $outlets = \App\Models\PosOutlets::where('project_id', $request->project_id)->select('name', 'id')->get();
        else $outlets = \App\Models\PosOutlets::select('name', 'id')->get();

        if (!\Auth::user()->hasRole('admins'))
            $outlets = \App\Models\PosOutlets::where('project_id', \auth()->user()->project_id)->select('name', 'id')->get();
        $op = "not assign";
        if ($request->has('type')) {
            if ($request->type == 'export') $op = "excel";
            elseif ($request->type == 'print') $op = "pdf";
        }
        $data = [];

        if ($request->startdate && ($request->startdate != "")) {
            $startdate = $request->startdate;
            if ($request->enddate && $request->enddate != "") $enddate = $request->enddate;

            if ($request->outletid && $request->outletid != '') $outlet = $request->outletid;

            $nepalistartdate = \App\Helpers\TaskHelper::getNepaliDate($startdate);
            $nepalienddate = \App\Helpers\TaskHelper::getNepaliDate($enddate);
            $groupId =FinanceHelper::get_group_id('Customer Group');
            $clients = COALedgers::where('group_id', $groupId)->where('org_id', \Auth::user()->org_id)->get();
            $clients = \App\Models\Client::where('org_id', \Auth::user()->org_id)->select('name', 'id', 'relation_type')->get();

            $detail_transaction = \App\Models\Invoice::select('client_id', DB::raw('SUM(subtotal) as dr_total'), DB::raw('SUM(tax_amount) as dr_vat'))
                ->where(function ($query) use ($request) {
                    if ($request->has('outletid') && ($request->outletid != '')) $query->where('outlet_id', $request->outletid);
                    else {
                        if (\Auth::user()->hasRole('admins')) {
                            if ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all')) {
                                $outletId = PosOutlets::where('project_id', $request->project_id)->pluck('id')->toArray();
                                $query->whereIn('outlet_id', $outletId);
                            }
                        } else {
                            $outletId = PosOutlets::where('project_id', \auth()->user()->project_id)->pluck('id')->toArray();
                            $query->whereIn('outlet_id', $outletId);
                        }
                    }
                })->where('org_id', \Auth::user()->org_id)
                ->when($startdate, function ($q) use($startdate) {
                    $q->where('bill_date', '>=', $startdate);
                })->when($enddate, function ($q) use($enddate) {
                    $q->where('bill_date', '<=', $enddate);
                })
                ->groupby('client_id')->get();

            $detail_transaction = $detail_transaction->groupby('client_id');

            $organization = \Auth::user()->organization;
            $outletname = \App\Models\PosOutlets::where('id', $outlet)->select('name')->first();
            $file = $startdate . '_' . $enddate . '_' . str_replace(' ', '_', $organization->organization_name);

            if ($op == "excel") {
                return \Excel::download(new \App\Exports\Reports\TransactionReport($detail_transaction, $clients, $outletname,
                    $startdate, $enddate, $nepalistartdate, $nepalienddate, $organization), $file.'.xls');
            } elseif ($op == "pdf") {
                $pdf = \PDF::loadView('admin.reports.pdf.customerwisereportPDF', compact('detail_transaction', 'clients', 'outletname',
                    'startdate', 'enddate', 'nepalistartdate', 'nepalienddate', 'organization'))->setPaper('a4', 'landscape');
                $full_name = $file . '.pdf';
                if (\File::exists('reports/' . $full_name)) {
                    \File::Delete('reports/' . $full_name);
                }
                return $pdf->download($full_name);
            }
            return view('admin.reports.transactionreports', compact('outlets', 'detail_transaction', 'clients',
                'outletname', 'startdate', 'enddate', 'nepalistartdate', 'nepalienddate', 'organization', 'projects'));
        }
        return view('admin.reports.transactionreports', compact('outlets', 'projects'));
    }

    public function customerwisedetailreports(Request $request)
    {
        $projects = Store::orderBy('id')->get();
        if ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all'))
            $outlets = \App\Models\PosOutlets::where('project_id', $request->project_id)->select('name', 'id')->get();
        else $outlets = \App\Models\PosOutlets::select('name', 'id')->get();

        if (!\Auth::user()->hasRole('admins'))
            $outlets = \App\Models\PosOutlets::where('project_id', \auth()->user()->project_id)->select('name', 'id')->get();

        $page_title = "Customer-Wise-Detail Report";
        $page_description = "Detail Report";
        $data = [];
        if ($request->has('type')) {
            if ($request->type == 'export') $op = "excel";
            elseif ($request->type == 'print') $op = "pdf";
        }

        if ($request->startdate && $request->startdate != "") {
            $startdate = $request->startdate;
            $endddate = $request->startdate;
            if ($request->enddate && $request->enddate != "") {
                $enddate = $request->enddate;
            }
            if ($request->outletid && $request->outletid != '') {
                $outlet = $request->outletid;
            }
            $nepalistartdate = \App\Helpers\TaskHelper::getNepaliDate($startdate);
            $nepalienddate = \App\Helpers\TaskHelper::getNepaliDate($enddate);
            $clients = \App\Models\Client::where('org_id', \Auth::user()->org_id)->select('name', 'id', 'relation_type')->get();
            $detail_transaction = \App\Models\Invoice::where(function ($query) use ($request) {
                    if ($request->has('outletid') && ($request->outletid != '')) $query->where('outlet_id', $request->outletid);
                    else {
                        if (\Auth::user()->hasRole('admins')) {
                            if ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all')) {
                                $outletId = PosOutlets::where('project_id', $request->project_id)->pluck('id')->toArray();
                                $query->whereIn('outlet_id', $outletId);
                            }
                        } else {
                            $outletId = PosOutlets::where('project_id', \auth()->user()->project_id)->pluck('id')->toArray();
                            $query->whereIn('outlet_id', $outletId);
                        }
                    }
                })->where('org_id', \Auth::user()->org_id)
                ->when($startdate, function ($q) use($startdate) {
                    $q->where('bill_date', '>=', $startdate);
                })->when($enddate, function ($q) use($enddate) {
                    $q->where('bill_date', '<=', $enddate);
                })->get();
            $detail_transaction = $detail_transaction->groupby(['bill_type', 'client_id']);
            $created_by = \Auth::user()->first_name . ' ' . \Auth::user()->last_name;
            $organization = \Auth::user()->organization;
            $outletname = \App\Models\PosOutlets::where('id', $outlet)->select('name')->first();
            $file = $startdate . '_' . $enddate . '_' . str_replace(' ', '_', $organization->organization_name);
            if ($op == "excel") {
                // return view('admin.reports.excel.customerwisedetailreportExcel', compact('detail_transaction', 'clients', 'outletname', 'startdate', 'enddate', 'nepalistartdate',
                //     'nepalienddate', 'organization', 'created_by'));

                return \Excel::download(new \App\Exports\Reports\CustomerWiseDetailReport($detail_transaction, $clients, $outletname, $startdate, $enddate, $nepalistartdate,
                    $nepalienddate, $organization, $created_by), $file.'.xls');
            } elseif ($op == "pdf") {
                // return view('admin.reports.pdf.customerwisedetailreportPDF', compact('detail_transaction', 'clients', 'outletname', 'startdate', 'enddate', 'nepalistartdate',
                //     'nepalienddate', 'organization', 'created_by'));
                $pdf = \PDF::loadView('admin.reports.pdf.customerwisedetailreportPDF', compact('detail_transaction', 'clients', 'outletname', 'startdate', 'enddate', 'nepalistartdate',
                    'nepalienddate', 'organization', 'created_by'))->setPaper('a4', 'portrait');
                $f = $file . '.pdf';
                if (\File::exists('reports/' . $f)) {
                    \File::Delete('reports/' . $f);
                }
                return $pdf->download($f);
            }
            return view('admin.reports.customerwisedetailreport', compact('page_title', 'page_description', 'data', 'created_by',
                'detail_transaction', 'clients', 'outletname', 'startdate', 'enddate', 'nepalistartdate', 'nepalienddate', 'organization', 'outlets', 'projects'));
        }
        return view('admin.reports.customerwisedetailreport', compact('page_title', 'page_description', 'outlets', 'projects'));
    }

    public function salesInvoiceDetail(Request $request)
    {
        $projects = Store::orderBy('id')->get();
        if ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all'))
            $outlets = \App\Models\PosOutlets::where('project_id', $request->project_id)->select('name', 'id')->get();
        else $outlets = \App\Models\PosOutlets::select('name', 'id')->get();
        $page_title = "Sales Invoice";
        $page_description = "Detail Report";
        $data = [];
        if ($request->has('type')) {
            if ($request->type == 'export') $op = "excel";
            elseif ($request->type == 'print') $op = "pdf";
        }

        if ($request->startdate && $request->startdate != "") {
            $startdate = $request->startdate;
            $endddate = $request->startdate;
            if ($request->enddate && $request->enddate != "") {
                $enddate = $request->enddate;
            }
            if ($request->outletid && $request->outletid != '') {
                $outlet = $request->outletid;
            }
            $nepalistartdate = \App\Helpers\TaskHelper::getNepaliDate($startdate);
            $nepalienddate = \App\Helpers\TaskHelper::getNepaliDate($enddate);
            $clients = \App\Models\Client::where('org_id', \Auth::user()->org_id)->select('name', 'id', 'relation_type')->get();
            $detail_transactions = \App\Models\Invoice::where(function ($query) use ($request) {
                    if ($request->has('outletid') && ($request->outletid != '')) $query->where('outlet_id', $request->outletid);
                    elseif ($request->has('project_id') && ($request->project_id != '') && ($request->project_id != 'over-all')) {
                        $outletId = PosOutlets::where('project_id', $request->project_id)->pluck('id')->toArray();
                        $query->whereIn('outlet_id', $outletId);
                    }
                })->where('org_id', \Auth::user()->org_id)
                ->when($startdate, function ($q) use($startdate) {
                    $q->where('bill_date', '>=', $startdate);
                })->when($enddate, function ($q) use($enddate) {
                    $q->where('bill_date', '<=', $enddate);
                })->groupby(['bill_no'])->get();
            $created_by = \Auth::user()->first_name . ' ' . \Auth::user()->last_name;
            $organization = \Auth::user()->organization;
            $outletname = \App\Models\PosOutlets::where('id', $outlet)->select('name')->first();
            $file = $startdate . '_' . $enddate . '_' . str_replace(' ', '_', $organization->organization_name);
            if ($op == "excel") {
                // return view('admin.reports.excel.sales_invoice_detail_Excel', compact('detail_transactions', 'clients', 'outletname', 'startdate', 'enddate', 'nepalistartdate',
                //     'nepalienddate', 'organization', 'created_by'));

                return \Excel::download(new \App\Exports\Reports\SalesInvoiceDetailReport($detail_transaction, $clients, $outletname, $startdate, $enddate, $nepalistartdate,
                    $nepalienddate, $organization, $created_by), $file.'.xls');
            } elseif ($op == "pdf") {
                // return view('admin.reports.pdf.sales_invoice_detailPDF', compact('detail_transaction', 'clients', 'outletname', 'startdate', 'enddate', 'nepalistartdate',
                //     'nepalienddate', 'organization', 'created_by'));
                $pdf = \PDF::loadView('admin.reports.pdf.sales_invoice_detail_PDF', compact('detail_transaction', 'clients', 'outletname', 'startdate', 'enddate', 'nepalistartdate',
                    'nepalienddate', 'organization', 'created_by'))->setPaper('a4', 'portrait');
                $f = $file . '.pdf';
                if (\File::exists('reports/' . $f)) {
                    \File::Delete('reports/' . $f);
                }
                return $pdf->download($f);
            }
            return view('admin.reports.sales_invoice_detail', compact('page_title', 'page_description', 'data', 'created_by',
                'detail_transaction', 'clients', 'outletname', 'startdate', 'enddate', 'nepalistartdate', 'nepalienddate', 'organization', 'projects'));
        }
        return view('admin.reports.sales_invoice_detail', compact('page_title', 'page_description', 'outlets', 'projects'));
    }

    public function productledgerreport(Request $request)
    {
        $page_title = "Product Ledger Report";
        $page_title = "Each product Report";
        $outlets = \App\Models\PosOutlets::select('name', 'id')->get();
        $products = \App\Models\Product::where('org_id', \Auth::user()->org_id)->pluck('name', 'id');
        $op = "pdf";
        $data = [];
        if ($request->startdate && $request->startdate != "") {
            $startdate = $request->startdate;
            $endddate = $request->startdate;

            if ($request->enddate && $request->enddate != "") {
                $enddate = $request->enddate;
            }
            if ($request->outlet && $request->outlet != '') {
                $outlet = $request->outlet;
            }
            $opening_stock = \App\Models\StockMove::where('tran_date', '<', $request->startdate)->where('stock_id', $request->product)->where('store_id', $request->outlet)->select('stock_id', DB::raw('SUM(qty) as openingstock'))->first();
            $productid = $request->product;
            $productname = \App\Models\Product::where('id', $request->product)->select('name')->first();
            $nepalistartdate = \App\Helpers\TaskHelper::getNepaliDate($startdate);
            $nepalienddate = \App\Helpers\TaskHelper::getNepaliDate($enddate);
            $daterange = $period = \carbon\CarbonPeriod::create($request->startdate, $request->enddate);
            $data = \App\Models\Invoice::
            where('org_id', \Auth::user()->org_id)
                ->leftJoin('invoice_detail', 'invoice.id', '=', 'invoice_detail.invoice_id')
                ->where('invoice.bill_date', '>=', $startdate)
                ->where('invoice.bill_date', '<=', $enddate)
                ->where('invoice_detail.product_id', $request->product)
                ->select('invoice.*', 'invoice_detail.product_id', 'invoice_detail.quantity', 'invoice_detail.price', 'invoice_detail.total')
                ->get();
            $data = $data->groupby(['bill_date', 'client_type']);
            $products = \App\Models\Product::where('org_id', \Auth::user()->org_id)->pluck('name', 'id');
            $organization = \Auth::user()->organization;
            if ($op == "excel") {

            } elseif ($op == "pdf") {
                // ->where('order_reference',null)
                $stock_entries = \App\Models\StockMove::select('stock_id', 'qty', 'tran_date')
                    ->where('org_id', $organization->id)
                    ->where('tran_date', '>=', $startdate)
                    ->where('tran_date', '<=', $enddate)
                    ->where('stock_id', $request->product)
                    ->where('store_id', $outlet)
                    ->groupby('tran_Date')
                    ->get();
                $stock = $stock_entries->groupby('tran_date');
                $outletname = \App\Models\PosOutlets::where('id', $outlet)->select('name')->first();

                $pdf = \PDF::loadView('admin.reports.productledgerPDF', compact('stock', 'data', 'products', 'productid', 'productname', 'nepalistartdate', 'outletname', 'nepalienddate', 'organization', 'opening_stock', 'daterange'))->setPaper('a3', 'landscape');
                $file = $startdate . '_' . $enddate . '_' . str_replace(' ', '_', $organization->organization_name) . '.pdf';
                if (\File::exists('reports/' . $file)) {
                    \File::Delete('reports/' . $file);
                }
                return $pdf->download($file);
            }
            return view('admin.reports.productledger_report', compact('page_title', 'page_description', 'data'));
        }
        return view('admin.reports.productledger_report', compact('page_title', 'page_description', 'outlets', 'products'));
    }

    public function checkUnpaid($clientID)
    {
        $start_date = date('Y-m-d', strtotime('-20 days'));
        $end_date = date('Y-m-d', strtotime('-40 days'));
        $invoices = Invoice::with('invoicePayments')
            ->where('client_id', $clientID)
            ->where('bill_date', '<=', $start_date)
            ->where('bill_date', '>=', $end_date)
            ->orderBy('created_at')
            ->get();
        foreach ($invoices as $key=>$invoice) {
            if (count($invoice->invoicePayments) == 0) return response()->json(['status' => 'unpaid']);

            $amount = 0;
            foreach ($invoice->invoicePayments as $invoicePayment) {
                $amount += (int)$invoicePayment->amount;
            }
            if ($amount<$invoice->total_amount) return response()->json(['status' => 'partial']);
            elseif ($amount==0) return response()->json(['status' => 'unpaid']);
        }
        return response()->json(['status' => 'paid']);
    }

    public function billwisedebtorlist()
    {
        $page_title = 'Bill wise debtor list';
        $page_description = 'Listing of all the debtors by bill';
        // $invoices = Invoice::withSum('invoicePayments','amount')
        //     ->whereHas("invoicePayments",function ($query){
        //         $query->havingRaw('invoice.total_amount>sum(amount)');
        //         $query->groupBy('invoice_id');
        //     })->orDoesntHave("invoicePayments")
        //     ->get()->groupBy('client_id');

        $tempamt = \App\Models\Invoice::leftjoin('invoice_payment', 'invoice.id', '=', 'invoice_payment.invoice_id')
            ->where('invoice_payment.type', 'Credit')
            ->select('invoice.total_amount', DB::raw('SUM(invoice_payment.amount) as paid_amount'))
            ->groupby('invoice.id')
            ->get();

        $invoices = Invoice::leftjoin('invoice_payment_status', 'invoice_payment_status.invoice_id', 'invoice.id')
            ->leftjoin('invoice_payment', 'invoice.id', '=', 'invoice_payment.invoice_id')
            ->select('invoice.*', 'invoice_payment_status.payment_status')
            ->whereIn('invoice_payment_status.payment_status', ['Pending', 'Partial'])
            ->orWhereNull('invoice_payment_status.payment_status')
            ->get();

        return view('admin.orders.billwisedebtorlist', compact('page_title', 'page_description', 'invoices'));
    }

    public function followuplist()
    {
        $orders = Invoice::where(function ($query) {
            $start_date = \Request::get('start_date');
            $end_date = \Request::get('end_date');
            if ($start_date && $end_date) {
                return $query->where('bill_date', '>=', $start_date)
                    ->where('bill_date', '<=', $end_date);
            }
        })->where(function ($query) {
                $bill_no = \Request::get('bill_no');
                if ($bill_no) return $query->where('bill_no', $bill_no);
            })->where(function ($query) {
                $client_id = \Request::get('client_id');
                if ($client_id) {
                    return $query->where('client_id', $client_id);
                }
            })->where(function ($query) {
                $fiscal_year = \Request::get('fiscal_year');
                if ($fiscal_year) {
                    return $query->where('fiscal_year', $fiscal_year);
                }

            })->where(function ($query) {
                $outlet_id = \Request::get('outlet_id');
                if ($outlet_id) {
                    return $query->where('outlet_id', $outlet_id);
                }
            })->orderBy('id', 'desc')->paginate(30);
        $page_title = 'Followup Invoice Payments';
        $page_description = 'Manage Followup invoices';
        $clients = \App\Models\Client::select('id', 'name')->where('org_id', \Auth::user()->org_id)->orderBy('id', 'DESC')->pluck('name', 'id')->all();
        $outlets = $this->getUserOutlets();

        return view('admin.invoice.followup', compact('orders', 'page_title', 'page_description', 'clients', 'outlets'));
    }

    public function getUserOutlets()
    {
        if (\Auth::user()->hasRole('admins')) $outlets = \App\Models\PosOutlets::get();
        else {
            $outletusers = \App\Models\OutletUser::where('user_id', \auth()->id())->get()->pluck('outlet_id');
            $outlets = \App\Models\PosOutlets::whereIn('id', $outletusers)
                ->orderBy('id', 'DESC')
                ->where('enabled', 1)
                ->get();
        }
        return $outlets;
    }
}
