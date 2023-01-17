<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ env('APP_COMPANY')}} | INVOICE</title>
    <style type="text/css">
        @font-face {
            font-family: Arial;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 18cm;
            height: 24.7cm;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 5px;

    font-size: 14px !important;
    padding-top: 2px !important; /*cancels out browser's default cell padding*/
    padding-bottom: 2px !important; /*cancels out browser's default cell padding*/
        }

        table th,
        table td {
            padding: 3px;
            background: ;
            text-align: left;
            border-bottom: 1px solid #FFFFFF;
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: left;
        }

        table td h3 {
            color: #349eeb;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0.2em 0;
        }

        table .no {
            color: #FFFFFF;
            font-size: 1em;
            background: #349eeb;
        }

        table .desc {
            text-align: left;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        table tbody tr:last-child td {
            border: none;
        }

        table tfoot td {
            padding: 5px 10px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr:last-child td {
            font-size: 1em;
            border-top: 1px solid #349eeb;
            font-weight: bold;

        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #349eeb;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

    </style>
</head>
<body>
    <header class="clearfix">
        <table>
            <tr>
                <td width="50%" style="float:left">
                    <div id="logo">
                        <img style="" src="{{ public_path()  }}{{ '/org/'.$organization->logo }}">
                    </div>
                </td>
                <td width="50%" style="text-align: right">
                    <div id="company">
                        <div style="font-size:16px;font-weight:bold">{{ \Auth::user()->organization->organization_name }} </div>
                        <div>{{ \Auth::user()->organization->address }}</div>
                        <div>Phone: {{ \Auth::user()->organization->phone }}</div>
                        <div>Seller's PAN:{{ \Auth::user()->organization->vat_id }}
                        
                        </div>
                        <div><a href="mailto:{{ \Auth::user()->organization->email }}">{{ \Auth::user()->organization->email }}</a></div>
                    </div>
                </td>
            </tr>

        </table>

        </div>
    </header>
    <main>
        <div id="details" class="clearfix">

            <table>
                <TR>
                    <TD width="50%" style="float:left">
                        <div id="client">
                            <div class="to">{{ ucwords($ord->source) }} {{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}} To:</div>
                            <div style="font-weight:bold">{{ $ord->name }}</div>
                            
                            <div class="name" style="font-weight: bold;"> @if($ord->source=='client') {{ $ord->client->name }} @else {{ $ord->lead->name }} @endif</div>

                            <div class='email'>Email: @if($ord->source == 'lead'){{ $ord->lead->email }}@else {{ $ord->client->email }} @endif </div>

                            <div class="address">Position: {!! $ord->position !!}<br /></div>
                            <div class="address">Address: {!! nl2br($ord->address ) !!}<br /></div>
                            <div class=""> @if($ord->source == 'client') Customer's PAN: {!! $ord->client->vat !!} @endif</div>
                        </div>
                    </TD>

                    <TD width="50%" style="text-align: right">
                        <div id="invoice">
                            <div style="font-weight:bold">{{ ucwords($ord->source) }} {{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}} @if($ord->order_type == 'quotation'){{\FinanceHelper::getAccountingPrefix('QUOTATION_PRE')}}@else{{\FinanceHelper::getAccountingPrefix('SALES_PRE')}}@endif{{ $ord->id }}</div>
                            <div class="date">Date of {{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}}: {{ date("d/M/Y", strtotime($ord->bill_date )) }}</div>
                            <div class="date">Due Date: {{ date("d/M/Y", strtotime($ord->due_date )) }}</div>
                           
                            <div>Terms: {{ $ord->terms }} </div>
                            <div>{{ ucwords($ord->source) }} Account:</b> #@if($ord->source == 'lead') {{ $ord->lead->id }} @else {{ $ord->client->id }}@endif</div>
                            <div>Source: {{ ucwords($ord->source) }}</div>
                        </div>
        </div>
        </TD>
        </TR>
        </table>
        <!-- /.row -->
        <p>Thank you for choosing {{ env('APP_COMPANY') }}. Your {{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}} is detailed below. If you find errors or desire certain changes, please contact us.</p>
        <table cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="no">SN</th>
                    <th class="no">PARTICULARS</th>
                    <th class="no">QUANTITY</th>
                    <th class="no">PRICE</th>

                    <th class="no">UNIT</th>
                    <th class="no">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php
          $n= 0;
          ?>
                @foreach($orderDetails as $odk => $odv)
                <tr>
                    <td>{{\FinanceHelper::getAccountingPrefix('PRODUCT_PRE')}}{{++$n}}</td>
                    @if($odv->is_inventory == 1)
                    <td>{{ $odv->product->name }}</td>
                    @elseif($odv->is_inventory == 0)
                    <td>{{ $odv->description }}</td>
                    @endif
                    <td>{{ $odv->quantity }}</td>
                    <td>{{ env('APP_CURRENCY').' '.number_format($odv->price,2) }}</td>
                    <td>{{ $odv->units->name }}</td>
                    <td>{{ env('APP_CURRENCY').' '.number_format($odv->total,2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <br/><br/>
            <tfoot style="border: 1px solid #eee; padding-top: 5px">
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">SubTotal</td>
                    <td>{{ env('APP_CURRENCY').' '. number_format($ord->subtotal,2) }}</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">Discount Percentage(%)</td>
                    <td>{{$ord->discount_percent }}%</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">Taxable Amount</td>
                    <td>{{env('APP_CURRENCY').' '. number_format($ord->taxable_amount,2) }}</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">Tax Amount(13%)</td>
                    <td>{{ env('APP_CURRENCY').' '. number_format($ord->tax_amount,2) }}</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">Total</td>
                    <td>{{ env('APP_CURRENCY').' '. number_format($ord->total_amount,2) }}</td>
                </tr>
            </tfoot>
        </table>
        <p id="" style="text-transform: capitalize;">
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

  $points = getPaisa($constnum);

  return $result . ' Rupees' .$points;
}


 ?>
            In Words: <?php echo numberFomatter($ord->total_amount); ?>
            <br/> Issued by: {{\Auth::user()->username}}<br/>
                    Issue Time: NPT {{ date("F j, Y, g:i a") }} 
        </p>
        <br>
        <div id="notices">
            <div>Note:</div>
            <div class="notice"> {!! nl2br($ord->comment) !!}</div>
        </div>
        <div id=""> ___________________________________</div>
        <div id="">Authorized Signature</div>
        <div>
        <img style="width:250px;" src="{{ public_path()  }}{{ '/org/'.$organization->stamp }}">
    </div>
    </main>
    <footer>
        {{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}} was created on MEROCRM.
    </footer>
</body>
</html>
