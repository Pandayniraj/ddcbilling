<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Product Ledger Report</title>
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
    <!-- <p><b>{{ $organization->organization_name }}</b></p> -->
    <p><b>{{$outletname->name}}<b></p>

    <h3>Product Ledger of <span style="color:red">{{$productid}} )</span> <span style="color:red">{{$productname->name}} </span>  From {{ $nepalistartdate }} to {{ $nepalienddate }}</h3>
    <div class="row">
      <table>
        <thead>
            <tr>
                <th rowspan=2>S.N</th>
                <th rowspan=2>Date</th>
                <th rowspan=2>Opening Stock</th>
                <th rowspan=2>Cold Store</th>
                <th rowspan=2>Total</th>
                <th colspan="3">Distributor</th>
                <th colspan="3">Retailer</th>
                <th colspan="3">Boothman</th>
                <th colspan="3">Direct Customer</th>
                <th colspan="3">Staff</th>
                <th rowspan="2">Coldstore Return</th>
                <th rowspan="2">Closing Stock</th>
                <th colspan="3">Total Sales</th>
            </tr>
            <tr>
                <th>Qty</th>
                <th>Amount</th>
                <th>VAT</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>VAT</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>VAT</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>VAT</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>VAT</th>
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
            $count=0;
          @endphp
          @foreach ($daterange as $range)

          <tr>
            @php

            if( $count == 0){
              $alya=$opening_stock->openingstock;
            }
            else{
              $alya= $total -($dist_qty + $retailer_qty + $boothman_qty + $direct_customer_qty + $staff_qty);
            }
              $cold_store=$stock[$range->format('Y-m-d')][0]->qty;
              $total=$cold_store+$alya;
              $date=$range->format('Y-m-d');
            @endphp
            <td>{{ $loop->index+1 }}</td>
            <td>{{ \App\Helpers\TaskHelper::getNepaliDate($date) }}</td>
            <td>{{ $alya }}</td>
            <td>{{$cold_store  }}</td>
            <td>{{ $total }}</td>
            {{-- //distributor --}}
            @php
              $distributordata=$data[$date]['distributor'];
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
            <td>{{ $dist_qty }}</td>
            <td>{{ $dist_amount }}</td>
            <td>{{ $dist_vat }}</td>
            {{-- //retailer --}}
            @php
              $retailerdata=$data[$date]['retailer'];
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
            <td>{{ $retailer_qty }}</td>
            <td>{{ $retailer_amount }}</td>
            <td>{{ $retailer_vat }}</td>
            {{-- boothman --}}
            @php
              $boothmandata=$data[$date]['boothman'];
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
            <td>{{ $boothman_qty }}</td>
            <td>{{ $boothman_amount }}</td>
            <td>{{ $boothman_vat }}</td>
            {{-- direct_customer --}}
            @php
              $direct_customerdata=$data[$date]['direct_customer'];
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
            <td>{{ $direct_customer_qty }}</td>
            <td>{{ $direct_customer_amount }}</td>
            <td>{{ $direct_customer_vat }}</td>
    {{-- staff --}}
            @php
              $staffdata=$data[$date]['staff'];
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
            <td>{{ $staff_qty }}</td>
            <td>{{ $staff_amount }}</td>
            <td>{{ $staff_vat }}</td>
            <td></td>
            <td>{{ $total -($dist_qty + $retailer_qty + $boothman_qty + $direct_customer_qty + $staff_qty)}}</td>
            <td>{{ $dist_qty + $retailer_qty + $boothman_qty + $direct_customer_qty + $staff_qty}}</td>
            <td>{{ $dist_amount + $retailer_amount + $boothman_amount + $direct_customer_amount + $staff_amount}}</td>
            <td>{{ $dist_vat + $retailer_vat + $boothman_vat + $direct_customer_vat + $staff_vat}}</td>

        </tr>
        @php
              $count++;
        @endphp
          @endforeach
          <tr>
            <th colspan="6">Total</th>
            <th>{{ $dist_col_amount }}</th>
            <th>{{ $dist_col_vat }}</th>
            <th></th>
            <th>{{ $retail_col_amount }}</th>
            <th>{{ $retail_col_vat }}</th>
            <th></th>
            <th>{{ $boothman_col_amount }}</th>
            <th>{{ $boothman_col_vat }}</th>
            <th></th>
            <th>{{ $dc_col_amount }}</th>
            <th>{{ $dc_col_vat }}</th>
            <th></th>
            <th>{{ $staff_col_amount }}</th>
            <th>{{ $staff_col_vat }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th>{{ $dist_col_amount + $retail_col_amount +  $boothman_col_amount + $dc_col_amount + $staff_col_amount }}</th>
            <th>{{ $dist_col_vat + $retail_col_vat + $boothman_col_vat + $dc_col_vat +$staff_col_vat }}</th>
          </tr>

        </tbody>
    </table>
    </div>


      <div class="row">

          <div class="col col-xs-3" style="padding-top:20px">Submitted By</div>
          <div class="col col-xs-3" style="padding-top:20px">Marketing Officer</div>
          <div class="col col-xs-3" style="padding-top:20px">Project Manager</div>

      </div>
      <div class="row">

      <div class="col-xs-3" style="padding-top:40px">_______________</div>
      <div class="col-xs-3" style="padding-top:40px">_______________</div>
      <div class="col-xs-3" style="padding-top:40px">_______________</div>
  </div>
</body>
</html>
