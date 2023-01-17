<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ \Auth::user()->organization->organization_name }} | INVOICE</title>

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

    <!-- Application CSS-->


<style type="text/css">
body {
    padding-top: 0.3cm !important;
}
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
    font-size: 12px !important;
}

td{
  border: 1px dotted #999 !important;
 
}

th{
  border: 1px dotted #999 !important;
}

.invoice-col{
      /border: 1px dotted #1a4567 !important;/
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
.page-header {
    border-bottom: none;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 2px !important;
}
label {
    margin-bottom: 0px !important;
}
.page-header {
    margin: -25px 0 20px 0 !important;
}

.invoice {
    padding: 15px !important;
    margin: 0px !important;
}
</style>
</head>


<body onload="window.print();" cz-shortcut-listen="true" class="skin-blue sidebar-mini">

    <?php

        $loop = $print_no > 0 ? 1: 3;

     ?>
     <?php
     function getPaisa($number)
    {
        $no = round($number);
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $words = array(
            0 => '',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety');
        $paise = ($decimal) ?  ' and ' .($words[$decimal - $decimal%10]) ." " .($words[$decimal%10]) .' Paisa'  : '';
        return $paise;
    }




    function numberFomatter($number)

    {

        $constnum = $number;

       $no = floor($number);

       $point = round($number - $no, 2) * 100;

       $hundred = null;

       $digits_1 = strlen($no);

       $i = 0;

       $str = array();

       $words = array('0' => '', '1' => 'one',

        '2' => 'two',

        '3' => 'three',

        '4' => 'four', '5' => 'five', '6' => 'six',

        '7' => 'seven', '8' => 'eight', '9' => 'nine',

        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',

        '13' => 'thirteen', '14' => 'fourteen',

        '15' => 'fifteen', '16' => 'sixteen',

        '17' => 'seventeen',

        '18' => 'eighteen',

        '19' =>'nineteen',

        '20' => 'twenty',

        '30' => 'thirty',

        '40' => 'forty',

        '50' => 'fifty',

        '60' => 'sixty',

        '70' => 'seventy',

        '80' => 'eighty',

        '90' => 'ninety');

       $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');

       while ($i < $digits_1) {

         $divider = ($i == 2) ? 10 : 100;

         $number = floor($no % $divider);

         $no = floor($no / $divider);

         $i += ($divider == 10) ? 1 : 2;

         if ($number) {

            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;

            $hundred = ($counter == 1 && $str[0]) ? '' : null;

            $str [] = ($number < 21) ? $words[$number] .

                " " . $digits[$counter] . $plural . " " . $hundred

                :

                $words[floor($number / 10) * 10]

                . " " . $words[$number % 10] . " "

                . $digits[$counter] . $plural . " " . $hundred;

         } else $str[] = null;

      }

      $str = array_reverse($str);

      $result = implode('', $str);

      $points =getPaisa($constnum);

      return $result . ' Rupees' .$points;
    }


     ?>

    <div class='wrapper'>


        @foreach(range(1,$loop) as $key)
        @if($key >= 2) <div class="pagebreak"> </div>@endif
        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <div class="col-xs-4">
                            <img src="{{ '/org/'.auth()->user()->organization->logo }}">

                          </div>
                          <address>
                            @php
                                $outlet_code=\App\Models\PosOutlets::where('id',$ord->outlet_id)->first();
                            @endphp
                        <div class="col-xs-7"  style="text-align: center;">
                         <span> <h4 style="margin-bottom: -7px;">Dairy Development Corporation</h4>
                          <h5 style="margin-bottom: -7px;">{{ $outlet_code->name??'' }} Pan:{{ \Auth::user()->organization->vat_id }}</h5>
                          <h5 style="margin-bottom: -7px;">{{ \Auth::user()->organization->email }} / {{ $outlet_code->email??'' }}</h5>
                          <h5 style="margin-bottom: -7px;">{{ $outlet_code->phone }}</h5>
                          <h5 style="margin-bottom: -8px;">@if($print_no == 0 && $key <= 1) TAX Invoice
                            @else Invoice @endif</h5></span>
                            <h5>@if($print_no > 0)
                            <b> Copy of original {{ $print_no }}</b>
                            @endif</h5>
                        </div>
                        <div class="col-xs-1">
                           
                        </div>
                        <hr/>
                    </h2>

                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-xs-4 invoice-col">
                        {{-- <span style="font-size: 15px; font-weight: bold" > {{$outlet_code->short_name}} </span><br> --}}
                        <strong> Customer:</strong><span style="white-space: nowrap;">{{ $ord->client->name }}</span>
                        <strong style="white-space: nowrap;"> Customer Address:</strong><span style="white-space: nowrap;">{{ $ord->client->physical_address??'' }}</span><br>
                        <strong> Customer contact:</strong>{{ $ord->client->phone??'' }}<br>
                        <strong> Customer pan/vat:</strong>{{ $ord->client->vat??'' }}<br>
                        <strong style="white-space: nowrap;"> Customer email:</strong>{{ $ord->client->email??'' }}<br>
                        <strong style="white-space: nowrap;"> Customer PO no:</strong><br>
                        
                    </span></p>
                </div>
                <div class="col-xs-4"></div>
                <!-- /.col -->
                <!-- /.col -->
                <div class="col-xs-4 invoice-col">
                    <p>
                        <Strong>Invoice No:</Strong>{{$outlet_code->short_name}}/{{$ord->fiscal_year}}/00{{ $ord->bill_no }}<br>
                        <strong>Printed Date:</strong><span>{{ date('Y-m-d') }}<br><strong>Invoice Date:</strong><span>{{ $ord->bill_date??'' }}<br>
                        <strong>Due Date:</strong><span>{{ $ord->due_date??'' }}<br>    
                        <strong> Mode of Payment:</strong>{{ $ord->bill_type }}<br></span></p>

                </div>
                <!-- /.col -->
          
            </div>
            <!-- /.row -->
            <br/>
            <!-- Table row -->
            <div class="row">
                <div class="col-xs-12 table-responsive">
                    <table class="table table-striped">

                        <thead class="bg-gray">
                            <tr class="">
                                <th>S.No</th>
                                <th>Particulars</th>
                                <th style="text-align: center">Unit</th>
                                <th style="text-align: center">Quantity</th>
                                <th style="text-align: center">Rate</th>
                                <th style="text-align: center">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderDetails as $odk => $odv)
                            <tr>
                                <td>{{$odk+1}}</td>
                                @if($odv->is_inventory == 1)
                                <td>{{ $odv->product->name }}</td>
                                @elseif($odv->is_inventory == 0)
                                <td>{{ $odv->description }}</td>
                                @endif
                                <td style="text-align: center">{{ $odv->units->symbol }}</td>
                                <td style="text-align: center">{{ number_format($odv->quantity,2) }}</td>
                                <td style="text-align: right">{{ number_format($odv->price,2) }}</td>
                                <td style="text-align: right;">  
                                    {{ number_format($odv->total-$odv->tax_amount,2) }}
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <div class="row">
                <!-- accepted payments column -->
                <div class="col-xs-6" style="font-size: 11px !important; margin-top: -17px;">
                    {{-- <p class="text-muted well well-sm no-shadow" style="margin-top: 7px;text-transform: capitalize;">
                      Remarks
                      <br/>
                      <br/>
                      <br/>
                      <br/>
                    </p> --}}
                  <div class="tabl-border" style="border: 1px dotted #999 !important; margin-top: 6px;">
                    <table style="width:100%;margin-top: -4px;">
                        <tr style="height: 73px;" >
                          <fieldset><label style="margin-left: 6px; margin-top: 3px;">Remarks</label></fieldset>
                        </tr>
                        
                    </table>
                  </div>
                    <table style="margin-top: 2px; width:100%;">
                        <tr style="height: 42px">
                            <th style="padding-left: 6px;">In Words: <?php
                                echo numberFomatter($ord->total_amount);
                                ?></th>
                        </tr>
                    </table>
                    {{--  --}}
                    <br/><br/><br/><br/>
                    <br/> <br/><br/><br/><br/><br/>
                 
                </div>
                <!-- /.col -->
                <div class="col-xs-6" style="margin-top: -10px !important">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody class="ttl-body">
                                <tr style="padding:0px; margin:0px;">
                                    <td style="width:62%">SubTotal:</td>
                                    <td style="text-align: right;">{{ number_format($ord->total_amount-$ord->tax_amount,2) }}</td>
                                </tr>
                                <tr style="padding:0px; margin:0px;">
                                    <th style="width:30%">Discount:</th>
                                    <td style="text-align: right;">{{ number_format($ord->discount_amount,2) }}</td>
                                </tr>
                                <tr style="padding:0px; margin:0px;">
                                    <th style="width:30%">Non Taxable Amt:</th>
                                    @if($ord->total_amount-$ord->taxable_amount-$ord->tax_amount >= 1)
                                    <td style="text-align: right;">{{ number_format($ord->total_amount-$ord->taxable_amount-$ord->tax_amount,2) }}</td> 
                                    @else<td style="text-align: right;"> 0.00</td>
                                    @endif
                                </tr>

                                <tr style="padding:0px; margin:0px;">
                                    <th style="width:30%">Taxable Amt:</th>
                                    <td style="text-align: right;">{{ number_format($ord->taxable_amount,2) }}</td>
                                </tr>
                                <tr style="padding:0px; margin:0px;">
                                    <th style="width:30%">Vat Amount:</th>
                                <td style="text-align: right;">{{number_format($ord->tax_amount,2) }}</td>
                                </tr>
                                <tr style="padding:0px; margin:0px;">
                                    <th style="width:30%">NPR Total:</th>
                                    <td style="text-align: right;">{{ number_format($ord->total_amount,2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                     

                    </div>
                </div>
                <!-- /.col -->
            </div>
            <br>
            <div class="row">
            <div class="col-xs-12">
            <p><b>Bank Account Detail For Electronic Payment</b></p>    
            </div>    
            </div><br> <br><br> <br><br> <br><br>
            <div class="row" style="margin-top: 160px;">
                <div class="col-xs-4">
                    <br>_______________________<br><span style="text-indent: 10px;">
                        &nbsp;&nbsp;Customer Seal & Signature</span>
                </div>
                <div class="col-xs-4">
                    <br>_______________________<br><span style="text-indent: 10px;">
                        &nbsp;&nbsp;Prepared By</span>
                </div>
                <div class="col-xs-4">
                    <br>_______________________<br><span style="text-indent: 10px;"><b>For, Dairy Development Corp.</b><br>
                        &nbsp;&nbsp;Authorised Signatory</span>
                </div> 
            </div>
        </section>

        @endforeach

    </div><!-- /.col -->

</body>