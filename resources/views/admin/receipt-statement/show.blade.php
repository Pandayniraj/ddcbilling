@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
    <style>
        .font-bold td {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            {!! $page_title ?? 'Page Title' !!}
            <small>{!! $page_description ?? 'Page Description' !!}</small>
        </h1>
        <div class="d-flex align-items-center py-1" style="margin-bottom: 5px;">
            <div data-bs-toggle="tooltip" data-bs-placement="left" data-bs-trigger="hover" title="">
                <a class="btn btn-default btn-xs float-right" href="/admin/sales/receipt-statement">
                    <i class="fa fa-times"></i> Close
                </a>
            </div>
        </div>
    </section>

    <div class='card'>
        <div class="card-body pt-6">
            <div class='row'>
                <div class='col-md-12'>
                    <div class="box box-primary">
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="orders-table">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th width="5%">SN</th>
                                            <th>Invoice#</th>
                                            <th>Invoice Date</th>
                                            <th>Invoice Amount</th>
                                            <th>Receipt Date</th>
                                            <th>Receipt Amount</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $paid = 0;
                                        @endphp
                                        @foreach ($invoices as $key => $invoice)
                                            @php
                                                $count = $invoice->invoicePayments()->count();
                                                $count = $count > 0 ? $count : 1;
                                            @endphp
                                            <tr>
                                                <td rowspan="{{ $count }}">{{ ++$key }}</td>
                                                <td rowspan="{{ $count }}">
                                                    {{$invoice->outlet->short_name}}/{{$invoice->fiscal_year}}/00{{$invoice->bill_no }}</td>
                                                <td rowspan="{{ $count }}">{{ $invoice->bill_date }}</td>
                                                <td rowspan="{{ $count }}">{{ number_format($invoice->total_amount, 2) }}</td>
                                                @forelse($invoice->invoicePayments as $payment)
                                                    <td>{{ date('Y-m-d', strtotime($payment->date)) }}</td>
                                                    <td>{{ $payment->amount }}</td>
                                                    @php
                                                        $paid += $payment->amount;
                                                    @endphp
                                                @empty
                                                    <td>-</td>
                                                    <td>0</td>
                                                @endforelse
                                                <td rowspan="{{ $count }}">
                                                    {{ number_format(($invoice->total_amount - $invoice->invoicePayments()->sum('amount')), 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="font-bold">
                                            <td class="ps-2" colspan="3" align="right">Total</td>
                                            <td colspan="2">{{ number_format($invoices->sum('total_amount'), 2) }}</td>
                                            <td>{{ number_format($paid, 2) }}</td>
                                            <td>{{ number_format(($invoices->sum('total_amount') - $paid), 2) }}</td>
                                        </tr>
                                        <tr class="font-bold">
                                            <td class="ps-2" colspan="3" align="right">Grand Total</td>
                                            <td colspan="2">{{ number_format($totalInvoice, 2) }}</td>
                                            <td>{{ number_format($totalPayment, 2) }}</td>
                                            <td>{{ number_format(($totalInvoice - $totalPayment), 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                {{ $invoices->render() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
