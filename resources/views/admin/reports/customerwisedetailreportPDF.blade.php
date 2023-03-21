<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .row .col {
            margin: 0px !important;
            padding: 0px !important;
        }
        .center {
        margin-left: auto;
        margin-right: auto;
        }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body>
    <p style="text-align:center"><Strong>{{ $organization->organization_name }}</strong></p>
    <p Style="text-align:center"><strong>{{ $outletname->name}}</strong></p>
    <p style="text-align:center">Daily Sales Detail Report</p>
    <p>
    <span style="float:left">Detail Report From {{ $nepalistartdate }} to {{ $nepalienddate }}</span>
    <span style="float:right">Print Date:{{(\App\Helpers\TaskHelper::getNepaliDate(\Carbon\Carbon::today()->toDateString()))}}</span>
    <br>
@php
    $grand_amount=0;
    $grand_vat_amount=0;
    $grand_total_amount=0;
@endphp
    <div class="row" >
      <table style="width:100%;" class="center">
            @foreach ($detail_transaction as $bill_type=>$values )
                <thead>
                    <tr>
                        <th colspan="6">Payment Method: {{$bill_type}}</th>
                    </tr>
                </thead>

                    @php
                        $billtype_amount=0;
                        $billtype_vat_amount=0;
                        $billtype_total_amount=0;
                    @endphp
                    @foreach ($values as $client_id=>$invoice )
                    <thead>
                        <tr>
                            <th style="text-align:center;" colspan="3">Customer name: {{\App\Models\Client::find($client_id)->name??""}}</th>
                            <th  style="text-align:center;" colspan="3">Prepared By: {{$created_by??""}}</th>
                        </tr>
                        <tr>
                            <th style="text-align:center;">Bill No</th>
                            <th style="text-align:center;">Item Name</th>
                            <th style="text-align:center;">Quantity</th>
                            <th style="text-align:center;">Amount</th>
                            <th style="text-align:center;">Vat Amt</th>
                            <th style="text-align:center;">Total Amount</th>
                        </tr>
                    </thead>
                        <tbody>
                            @php
                                $customer_amount=0;
                                $customer_vat_amount=0;
                                $customer_total_amount=0;
                            @endphp
                        @foreach($invoice as $temp_invoicedetail)
                        @foreach($temp_invoicedetail->invoicedetails as $details)
                                <tr>
                                    <td style="text-align:center;">{{$details->order->outlet->short_name}}/{{$details->order->fiscal_year}}/00{{$details->order->bill_no }}</td>
                                    <td style="text-align:center;">{{$details->product->name}}</td>
                                    <td style="text-align:right;">{{$details->quantity}}</td>
                                    <td style="text-align:right;" >{{$details->total-$details->tax_amount}}</td>
                                    <td style="text-align:right;">{{$details->tax_amount}}</td>
                                    <td style="text-align:right;">{{$details->total}}</td>
                                    @php
                                    $customer_amount+=($details->total-$details->tax_amount);
                                    $customer_vat_amount+=$details->tax_amount;
                                    $customer_total_amount+=$details->total;
                                @endphp
                                </tr>
                        @endforeach
                        @endforeach
                        <tr>
                            <td style="text-align:center;" colspan="3">Total</td>
                            <td style="text-align:right;">{{$customer_amount}}</td>
                            <td style="text-align:right;">{{$customer_vat_amount}}</td>
                            <td style="text-align:right;">{{$customer_total_amount}}</td>
                            @php
                            $billtype_amount+=$customer_amount;
                            $billtype_vat_amount+=$customer_vat_amount;
                            $billtype_total_amount+=$customer_total_amount;
                        @endphp
                        </tr>
                    @endforeach
                    <tr>
                        <td style="text-align:center;" colspan="3">Total of {{$bill_type}} only</td>
                        <td style="text-align:right;">{{$billtype_amount}}</td>
                        <td style="text-align:right;">{{$billtype_vat_amount}}</td>
                        <td style="text-align:right;">{{$billtype_total_amount}}</td>
                        @php
                            $grand_amount+=$billtype_amount;
                            $grand_vat_amount+=$billtype_vat_amount;
                            $grand_total_amount+=$billtype_total_amount;
                        @endphp
                    </tr>
            @endforeach
            <thead>
                <tr>
                <th style="text-align:left;" colspan="3">Grand Total</th>
                <th style="text-align:right;">{{$grand_amount}}</th>
                <th style="text-align:right;">{{$grand_vat_amount}}</th>
                <th style="text-align:right;">{{$grand_total_amount}}</th>
                </tr>
            </thead>
    </table>
    </div>

</body>

</html>
