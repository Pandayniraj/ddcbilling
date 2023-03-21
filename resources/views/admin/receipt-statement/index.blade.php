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
                                                <th>Client</th>
                                                <th>Closing Balance</th>
                                                <th style="width: 10%">Rcv Statments</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php $total = 0; @endphp
                                            @foreach ($clients as $key => $client)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $client->name }}</td>
                                                    <td>{{ number_format(($client->invoices_sum_total_amount - $client->payments_sum_amount), 2) }}</td>
                                                    <td>
                                                        <a href="/admin/sales/receipt-statement/{{ $client->id }}/show"
                                                            class="btn btn-xs btn-primary">show</a>
                                                    </td>
                                                </tr>
                                                @php
                                                    $total += ($client->invoices_sum_total_amount - $client->payments_sum_amount);
                                                @endphp
                                            @endforeach
                                            <tr class="font-bold">
                                                <td class="ps-2" colspan="3" align="right">Total</td>
                                                <td>{{ number_format($total, 2) }}</td>
                                            </tr>
                                            <tr class="font-bold">
                                                <td class="ps-2" colspan="3" align="right">Grand Total</td>
                                                <td>{{ number_format($totalBalance, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    {{ $clients->render() }}
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection

