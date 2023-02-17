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
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body>
<p><b>{{ $organization->organization_name }}</b></p>
<p><b>{{$outletname->name}}<b></p>
<h3>Sales Report From {{ $nepalistartdate }} to {{ $nepalienddate }}</h3>
<div class="row">
    <table>
        <thead>
        <tr>
            <th rowspan=2>S.N</th>
            <th rowspan=2>Particular</th>
            <th rowspan=2>Opening Stock</th>
            <th rowspan=2>Cold Store</th>
            <th rowspan=2>Total Stock</th>
            <th rowspan="2">Cold-store Return</th>
            <th rowspan="2">Closing Stock</th>
            <th colspan="3">Total Sales</th>
        </tr>
        <tr>
            <th>Qty</th>
            <th>Amount</th>
            <th>VAT</th>
        </tr>
        </thead>
        <tbody>
        @php
            $dist_col_amount=0;
            $dist_col_vat=0;
            $retail_col_amount=0;
            $retail_col_vat=0;
            $boothman_col_amount=0;
            $boothman_col_vat=0;
            $dc_col_amount=0;
            $dc_col_vat=0;
            $staff_col_amount=0;
            $staff_col_vat=0;
        @endphp
        @foreach ($products as  $productid=>$productname)

            <tr>
                @php
                    $alya=0;
                    $cold_store=$stock[$productid][0]->quantity;
                    $total=$cold_store+$alya;
                @endphp
                <td>{{ $loop->index+1 }}</td>
                <td>{{ $productname }}</td>
                <td>{{ $alya }}</td>
                <td>{{$cold_store  }}</td>
                <td>{{ $total }}</td>

                @php
                      $distributordata=$data[$productid]['distributor'];
                      $dist_qty=0;
                      $dist_amount=0;
                      $dist_vat=0;
                      foreach ($distributordata as $value) {

                        $dist_qty+=$value->quantity;
                        $dist_amount+=$value->total_amount;
                        $dist_vat+=$value->tax_amount;

                        $dist_col_amount+=$value->total_amount;
                        $dist_col_vat+=$value->tax_amount;
                      }
                @endphp

                @php
                    $retailerdata=$data[$productid]['retailer'];
                    $retailer_qty=0;
                    $retailer_amount=0;
                    $retailer_vat=0;
                    foreach ($retailerdata as $value) {
                      $retailer_qty+=$value->quantity;
                      $retailer_amount+=$value->total_amount;
                      $retailer_vat+=$value->tax_amount;

                      $retail_col_amount+=$value->total_amount;
                      $retail_col_vat+=$value->tax_amount;
                    }
                @endphp

                @php
                    $boothmandata=$data[$productid]['boothman'];
                    $boothman_qty=0;
                    $boothman_amount=0;
                    $boothman_vat=0;
                    foreach ($boothmandata as $value) {
                      $boothman_qty+=$value->quantity;
                      $boothman_amount+=$value->total_amount;
                      $boothman_vat+=$value->tax_amount;

                      $boothman_col_amount+=$value->total_amount;
                      $boothman_col_vat+=$value->tax_amount;
                    }
                @endphp

                @php
                    $direct_customerdata=$data[$productid]['direct_customer'];
                    $direct_customer_qty=0;
                    $direct_customer_amount=0;
                    $direct_customer_vat=0;
                    foreach ($direct_customerdata as $value) {
                      $direct_customer_qty+=$value->quantity;
                      $direct_customer_amount+=$value->total_amount;
                      $direct_customer_vat+=$value->tax_amount;

                      $dc_col_amount+=$value->total_amount;
                      $dc_col_vat+=$value->tax_amount;
                    }
                @endphp

                @php
                    $staffdata=$data[$productid]['staff'];
                    $staff_qty=0;
                    $staff_amount=0;
                    $staff_vat=0;
                    foreach ($staffdata as $value) {
                      $staff_qty+=$value->quantity;
                      $staff_amount+=$value->total_amount;
                      $staff_vat+=$value->tax_amount;

                      $staff_col_amount+=$value->total_amount;
                      $staff_col_vat+=$value->tax_amount;
                    }
                @endphp

                <td></td>
                <td>{{ $total -($dist_qty + $retailer_qty + $boothman_qty + $direct_customer_qty + $staff_qty)}}</td>
                <td>{{ $dist_qty + $retailer_qty + $boothman_qty + $direct_customer_qty + $staff_qty}}</td>
                <td>{{ $dist_amount + $retailer_amount + $boothman_amount + $direct_customer_amount + $staff_amount}}</td>
                <td>{{ $dist_vat + $retailer_vat + $boothman_vat + $direct_customer_vat + $staff_vat}}</td>

            </tr>
        @endforeach
        <tr>
            <th colspan="8">Total</th>
            <th>{{ $dist_col_amount + $retail_col_amount +  $boothman_col_amount + $dc_col_amount + $staff_col_amount }}</th>
            <th>{{ $dist_col_vat + $retail_col_vat + $boothman_col_vat + $dc_col_vat +$staff_col_vat }}</th>
        </tr>
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-xs-2" style="padding-top:40px">_______________ <br>Submitted By</div>
    <div class="col-xs-2" style="padding-top:40px">_______________ <br>Marketing Officer</div>
    <div class="col-xs-2" style="padding-top:40px">_______________ <br>Project Manager</div>
</div>
</body>
</html>
