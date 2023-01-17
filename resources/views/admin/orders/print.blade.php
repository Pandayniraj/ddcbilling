<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ env('APP_COMPANY')}} | INVOICE</title>

    <!-- block from searh engines -->
    <meta name="robots" content="noindex">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Set a meta reference to the CSRF token for use in AJAX request -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Bootstrap 3.3.4 -->
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons 4.7.0 -->
    <link href="{{ asset("/bower_components/admin-lte/font-awesome/css/all.css") }}" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.1 -->
    <link href="{{ asset("/bower_components/admin-lte/ionicons/css/ionicons.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/admin-lte/dist/css/AdminLTE.min.css") }}" rel="stylesheet" type="text/css" />


<style type="text/css">
    @media print {
   body {
      -webkit-print-color-adjust: exact;
   }
}

.vendorListHeading th {
   background-color: #1a4567 !important;
   color: white !important;
}

table{
    border: 1px solid dotted !important;
    font-size: 14px !important;
    padding-top: 2px !important; /*cancels out browser's default cell padding*/
    padding-bottom: 2px !important; /*cancels out browser's default cell padding*/
}

td{
  border: 1px dotted #999 !important;
  padding-top: 2px !important; /*cancels out browser's default cell padding*/
  padding-bottom: 2px !important; /*cancels out browser's default cell padding*/
}

th{
  border: 1px dotted #999 !important;
    padding-top: 2px !important; /*cancels out browser's default cell padding*/
  padding-bottom: 2px !important; /*cancels out browser's default cell padding*/
}

.invoice-col{
      /*border: 1px dotted #1a4567 !important;*/
      font-size: 13px !important;
      margin-bottom: -20px !important;
}

 @page {
    size: auto;
    margin: 0;
  }

  body{
    padding-left: 1.3cm;
    padding-right: 1.3cm;
    padding-top: 1.3cm;
  }

  @media print {
    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
}
</style>


</head>

<body onload="window.print();" cz-shortcut-listen="true" class="skin-blue sidebar-mini">

    <div class='wrapper'>

        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <div class="col-xs-3">
                            <img src="{{env('APP_URL')}}{{ '/org/'.$organization->logo }}" style="max-width: 200px;">
                        </div>
                        <div class="col-xs-9">
                            <span class="pull-right">
                                <span>{{ ucwords($ord->source) }} {{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}}</span>
                            </span>
                        </div>
                        <hr>
                    </h2>

                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">

                    <address>
                        <strong>{{ \Auth::user()->organization->organization_name }} </strong><br>
                        {{ \Auth::user()->organization->address }}<br>
                        Phone: {{ \Auth::user()->organization->phone }}<br>
                        Email: {{ \Auth::user()->organization->email }}<br>
                        Seller's PAN: {{ \Auth::user()->organization->vat_id }}
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    To
                    <address>
                        <strong>{{ $ord->name }}
                        @if($ord->source=='client') {{ $ord->client->name }} @else {{ $ord->lead->name }} @endif </strong><br />
                        @if($ord->source == 'lead'){{ $ord->lead->email }}@else {{ $ord->client->email }} @endif <br>
                        Address: {!! nl2br($ord->address ) !!}<br>
                        @if($ord->source == 'client') Customer's PAN: {!! $ord->client->vat !!} @endif
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    <b>{{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}}@if($ord->order_type == 'quotation'){{\FinanceHelper::getAccountingPrefix('QUOTATION_PRE')}}@else{{\FinanceHelper::getAccountingPrefix('SALES_PRE')}}@endif{{ $ord->id }}</b><br>


                    <b>Bill Date:</b> {{ date("d/M/Y", strtotime($ord->created_at )) }}<br>
                    <?php $timestamp = strtotime($ord->created_at) ?>
                    <b>Valid Till:</b> {{ date("d/M/Y", strtotime("+30 days", $timestamp )) }}<br>
                    <b>Terms :</b> {{ $ord->terms }}<br>
                    <b>{{ ucwords($ord->source) }} Account:</b> #@if($ord->source == 'lead') {{ $ord->lead->id }} @else {{ $ord->client->id }} @endif<br>
                    <b>Source: </b> {{ $ord->source }}
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            Thank you for choosing {{ env('APP_COMPANY') }}. Your {{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}} is detailed below. If you find errors or desire certain changes, please contact us.
            <hr />
            <!-- Table row -->
            <div class="row col-xs-12 table-responsive">
                <div class="col-xs-12 table-responsive">
                    <table id="t01" class="table table-striped">
                        <thead class="vendorListHeading">
                            <tr>
                                <th>SN</th>
                                <th>Particulars</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                  $n= 0;
                  ?>
                            @foreach($orderDetails as $odk => $odv)
                            <tr>
                                <td class="no">{{\FinanceHelper::getAccountingPrefix('PRODUCT_PRE')}}{{++$n}}</td>
                                @if($odv->is_inventory == 1)
                                <td>{{ $odv->product->name }}</td>
                                @elseif($odv->is_inventory == 0)
                                <td>{{ $odv->description }}</td>
                                @endif
                                <td>{{$odv->price}}</td>
                                <td>{{ $odv->quantity }}</td>
                                <td>{{ $odv->units->name }}</td>
                                <td>{{ env('APP_CURRENCY').' '.$odv->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
             <?php




 ?>


            <div class="row">
                <!-- accepted payments column -->
                <div class="col-xs-6">
                    <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;text-transform: capitalize;">
                        In Words: {{\FinanceHelper::numberRupeeFomatter($ord->total_amount)}}
                    </p>
                    <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                        {{ nl2br($ord->comment) }}
                    </p>

                </div>
                <!-- /.col -->
                <div class="col-xs-6">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width:50%">Subtotal:</th>
                                    <td>{{ env('APP_CURRENCY').' '.number_format($ord->subtotal ,2)}}</td>
                                </tr>
                                <tr>
                                    <th>Discount Percent(%):</th>
                                    <td>{{ ($ord->discount_percent ? $ord->discount_percent  : '0') }}%</td>
                                </tr>
                                <tr>
                                    <th>Taxable Amount:</th>
                                    <td>{{ env('APP_CURRENCY').' '. number_format($ord->taxable_amount,2) }}</td>
                                </tr>
                                <tr>
                                    <th>Tax Amount(13%)</th>
                                    <td>{{ env('APP_CURRENCY').' '. number_format($ord->tax_amount,2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td>{{ env('APP_CURRENCY').' '. number_format($ord->total_amount,2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

        </section>

    </div><!-- /.col -->

</body>
