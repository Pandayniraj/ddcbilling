<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReceiptStatementController extends Controller
{
    public function index()
    {
        $page_title = "Receipt Statement";
        $page_description = "";
        $clients = Client::whereHas('invoices')->withSum('invoices', 'total_amount')->withSum('payments', 'amount')->paginate(20);
        $totalPayment = InvoicePayment::whereNotNull('client_id')->whereNotNull('invoice_id')->sum('amount');
        $invoiceTotal = Invoice::whereNotNull('client_id')->sum('total_amount');
        $totalBalance = $invoiceTotal - $totalPayment;
        return view('admin.receipt-statement.index', compact('clients', 'page_title', 'page_description', 'totalBalance'));
    }
    public function show($id)
    {
        $client = Client::find($id);
        $page_title = "Receipt Statement";
        $page_description = "Receipt statement for \"{$client->name}\"";
        $invoices = Invoice::where('client_id', $id)->with('invoicePayments')->paginate(10);
        $totalInvoice = Invoice::where('client_id', $id)->sum('total_amount');
        $totalPayment = InvoicePayment::where('client_id', $id)->whereNotNull('invoice_id')->sum('amount');
        return view('admin.receipt-statement.show', compact('invoices', 'page_title', 'page_description', 'totalInvoice', 'totalPayment'));
    }
}
