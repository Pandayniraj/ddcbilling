@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
    <style>
        .font-bold td {
            font-weight: bold;
        }
        table{
        font-size: 12px;
    }
    table td{
        padding: 0px important;
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
                <a class="btn btn-default btn-xs float-right" href="/admin/sales/receipts">
                    <i class="fa fa-times"></i> Close
                </a>
            </div>
        </div>
    </section>

    <div class='card'>
        <div class="card-body pt-6">
            <div class='row'>
                <div class='col-md-12'>
                    <form method="get" action="{{ route('admin.payment.multiple-invoice.create') }}">
                        {{-- {!! Form::open(['route' => 'admin.payment.multiple-invoice.create', 'id' => 'frmClientList']) !!} --}}
                        <div class="box box-default">
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="orders-table">
                                        <thead>
                                            <tr style="font-size: 11px; font-weight:normal">
                                                <th width="5%"></th>
                                                <th>Client</th>
                                                <th>Invoice #</th>
                                                <th>Outlet</th>
                                                <th>Salesman</th>
                                                <th>Date</th>
                                                <th>Invoice Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php $total = 0; @endphp
                                            @foreach ($invoices as $key => $invoice)
                                                <tr class="font-bold">
                                                    <td><input type="checkbox" class="check_debtor"
                                                            data-id="{{ $key }}"></td>
                                                    <td>
                                                        @php
                                                            $client = App\Models\Client::find($key);
                                                            
                                                        @endphp
                                                        {{ $client->name }}
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        {{ $invoice->sum('total_amount') - $invoice->sum('invoice_payment_sum_amount') }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $total += $invoice->sum('total_amount') - $invoice->sum('invoice_payment_sum_amount');
                                                @endphp
                                                @foreach ($invoice as $inv)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="orders[]"
                                                                value="{{ $inv->id }}"
                                                                class="check_order check_order_{{ $key }}"></td>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;#{{ $inv->id }}</td>
                                                        <td>{{$inv->outlet->short_name}}/{{$inv->fiscal_year}}/00{{$inv->bill_no }}</td>
                                                        <td>{{$inv->outlet->name}}</td>
                                                        <td>{{ $inv->user->username }}</td>
                                                        <td>{{ $inv->bill_date }}</td>
                                                        <td>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $inv->total_amount - $inv->invoice_payment_sum_amount }}
                                                        </td>
                                                        <td>
                                                            <a href="/admin/payment/invoice/{{ $inv->id }}/create"
                                                                class="btn btn-xs btn-primary">Pay</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            <tr class="font-bold">
                                                <td class="ps-2">Total</td>
                                                <td>{{ number_format($total, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="panel-footer footer">
                                        <button type="submit" class="btn btn-social btn-foursquare">
                                            <i class="fa fa-money"></i>Pay Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    {{-- {!! Form::close() !!} --}}
                </div>
            </div>
            <div id="makePaymentModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Pending & Partial Receipts</h4>
                        </div>
                        <div class="modal-body" style="z-index: 90000">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <select class="form-control searchable" id='purchaseId'>
                                            @foreach ($orderTopay ?? [] as $k => $value)
                                                <option value="{{ $value->id }}">
                                                    Bill#{{ $value->bill_no }} [ {{ $value->client->name }} ]
                                                    Total:
                                                    {{ $value->total_amount - \TaskHelper::getSalesPaymentAmount($value->id) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id='payNow'>Pay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body_bottom')
    <script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>
    <script type="text/javascript">
        $('.searchable').select2({
            width: '100%',
        });

        $('#payNow').click(function() {
            let pid = $('#purchaseId').val();
            location.href = `/admin/payment/invoice/${pid}/create`;
        });
    </script>
    <script>
        const checkDebtorElements = document.querySelectorAll('.check_debtor');
        const checkOrderElements = document.querySelectorAll('.check_order');
        const payMultipleBtn = document.getElementById('pay_multiple');
        checkDebtorElements.forEach(element => {
            element.addEventListener('click', function(e) {
                checkOrdersByDebtor(element);
            })
        })

        function checkOrdersByDebtor(element) {
            const checkOrderElements = document.querySelectorAll(`.check_order_${element.dataset.id}`);
            checkOrderElements.forEach(elem => {
                elem.checked = false;
                if (element.checked) {
                    elem.checked = true;
                }
            })
        }
    </script>
@endsection
