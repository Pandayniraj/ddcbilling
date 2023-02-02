<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IrdDetail;
use Flash;
use DB;

class IrdDetailController extends Controller
{
    public function index()
    {
        $pagetitle = "Ird";
        $pagedescription = "Information";
        $irddetail = IrdDetail::first();
        return view('irddetail.index', compact('pagetitle', 'pagedescription', 'irddetail'));
    }

    public function edit(Request $request, $id)
    {
        $pagetitle = "Ird";
        $pagedescription = "Information edit";
        $editirddetail = IrdDetail::where('id', $id)->first();
        return view('irddetail.create', compact('pagetitle', 'pagedescription', 'editirddetail'));
    }

    public function update(Request $request, $id)
    {
        $updateirddetail = IrdDetail::where('id', $id)->update(['api_link' => $request->api_link, 'ird_username' => $request->ird_username, 'ird_password' => $request->ird_password, 'seller_pan' => $request->seller_pan]);
        Flash::success('Ird-Detail updated Successfully.');
        return redirect(route('admin.irddetail'));
    }

    public function generatematerializeview()
    {
        DB::statement('TRUNCATE TABLE invoice_materialize_view');
        DB::statement('INSERT INTO invoice_materialize_view (`order_id`,`fiscal_year`,`bill_no`,`customer_name`,`customer_pan`,`bill_date`,`amount`,`discount`,
                                             `taxable_amount`,`total_amount`,`sync_with_ird`,`is_bill_printed`,`is_bill_active`,`printed_time`
                                              ,`entered_by`,`printed_by`,`is_realtime`)
        select invoice.id as order_id, invoice.fiscal_year, invoice.bill_no, clients.name as customer_name,clients.vat as customer_pan,
        invoice.bill_date, invoice.amount as amount , invoice.discount_amount as discount, invoice.taxable_amount as taxable_amount, invoice.total_amount as total_amount, invoice_meta.sync_with_ird, case when COUNT(bill_print_invoice.invoice_id) <> 0 then 1 else 0 end as `is_bill_printed`, invoice_meta.is_bill_active, bill_print_invoice.printed_date as printed_time, users.username as entered_by,
         p_user.username as printed_by,invoice_meta.is_realtime
         from invoice
         LEFT JOIN clients ON clients.id = invoice.client_id
         LEFT JOIN bill_print_invoice ON bill_print_invoice.invoice_id = invoice.id
         LEFT JOIN invoice_meta ON invoice_meta.invoice_id = invoice.id
         LEFT JOIN users ON users.id = invoice.user_id
         LEFT JOIN users p_user ON p_user.id = bill_print_invoice.printed_by
         WHERE 1 GROUP  BY invoice.id');
        Flash::success('Generated Successfully.');
        return back();
    }
}
