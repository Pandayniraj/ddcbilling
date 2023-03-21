<!DOCTYPE html>
<html lang="en">
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400&display=swap" rel="stylesheet">
    <title>{{ \Config::get('restro.APP_COMPANY', env('APP_COMPANY'))  }} | INVOICE</title>
    <style type="text/css">
        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 0.2cm;
            }
        }
        body {
            font-size: 10px !important;
            font-weight: 400 !important;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
        }
        hr.dotted {
            border-top: 1px dashed black;
        }
        .dotted-thead td {
            border-top: 1px dashed black;
            border-bottom: 1px dashed black;
        }
        #bg-text {
            color: lightgrey;
            position: absolute;
            left: 0;
            right: 0;
            top: 40%;
            text-align: center;
            margin: auto;
            opacity: 0.5;
            z-index: 2;
            font-size: 80px;
            transform: rotate(330deg);
            -webkit-transform: rotate(330deg);
        }
        @media all {
            .page-break {
                display: none;
            }
        }
        @media print {
            .page-break {
                display: block;
                page-break-after: always;
            }
        }
        .amountSummary td {
            white-space: nowrap;
        }
        .top-heading h5, .top-heading h6 {
            line-height: 0.1;
            text-transform: uppercase;
        }
        .bill-details p {
            line-height: 0.3;
        }
        .bill-details span {
            margin-left: 8px;
        }
        .top-heading span {
            font-size: 11px;
            font-weight: 400 !important;
        }
        .amountSummary tr td {
            text-align: left;
        }
        tr.tr-gap {
            line-height: 1;
        }
    </style>
</head>

<body onload="window.print();" onafterprint="myFunction()">
<?php
$loop = $print_no > 0 ? 1 : 3;
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
    $paise = ($decimal) ? ' and ' . ($words[$decimal - $decimal % 10]) . " " . ($words[$decimal % 10]) . ' Paisa' : '';
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
    $words = array('0' => '', '1' => 'one', '2' => 'two', '3' => 'three',
        '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen', '15' => 'fifteen',
        '16' => 'sixteen', '17' => 'seventeen', '18' => 'eighteen',
        '19' => 'nineteen', '20' => 'twenty', '30' => 'thirty',
        '40' => 'forty', '50' => 'fifty', '60' => 'sixty',
        '70' => 'seventy', '80' => 'eighty', '90' => 'ninety');

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

    return $result . ' Rupees' . $points;
}
?>
@php $outlet_code=\App\Models\PosOutlets::where('id',$ord->outlet_id)->first(); @endphp
@foreach(range(1,$loop) as $key)
    @if($key >= 2)
        <div class="page-break"></div>
    @endif
    <div class="bill-print">
        <div class="top-heading" style="text-align: center;">
            <span>Dairy Development Corporation</span><br>
            <span>{{$outlet_code->short_name??'' }} / Pan:{{ \Auth::user()->organization->vat_id??'' }}</span> <br>
            <span style="margin-bottom: -7px;">{{ $outlet_code->address??'' }}</span><br>
            <span style="margin-bottom: -7px;">{{ $outlet_code->email??'' }}/{{$outlet_code->phone}}</span><br>
            <span>
                @if($print_no == 0 && $key <= 1)
                    <b> TAX Invoice </b>
                @else
                    <b> Invoice </b>
                @endif
                @if($print_no > 0)
                    <b> (Copy of original {{ $print_no }})</b>
               @endif
            </span>
        </div>

        <div class="bill-details">
            <p>Invoice No :<span>{{$outlet_code->short_name}}/{{$ord->fiscal_year}}/00{{ $ord->bill_no }}</span></p>
            <p>Invoice Date:<span>{{ $ord->bill_date??'' }}</span></p>
            <p>Printed Date:<span>{{ date('Y-m-d') }}</span></p>
            <p>Customer's Name:<span>{{ $ord->client?$ord->client->name:$ord->customer_name }}</span></p>
            <p>Customer's TPIN:<span>{{ $ord->customer_pan??'' }}</span></p>
            <p>Customer's Address:<span>{{ $ord->client?$ord->client->physical_address:'' }}</span></p>
            <p>Customer phone:<span>{{ $ord->client->phone??'' }}</span></p>
        </div>
        <table>
            <tr class="dotted-thead">
                <td>SN</td>
                <td colspan="6">Particulars</td>
                <td style="text-align:right;">Unit</td>
                <td style="text-align:center;">QTY</td>
                <td style="text-align:left;">Rate</td>
                <td style="text-align: right;">Amount</td>
            </tr>
            <tbody>
            @foreach($orderDetails as $odk => $odv)
                <tr>
                    <td>{{ $odk+1 }}</td>
                    @if($odv->is_inventory == 1)
                        <td colspan="6" style="white-space: nowrap;">{{ $odv->product->name }}</td>
                    @elseif($odv->is_inventory == 0)
                        <td colspan="6" style="white-space: nowrap;">{{ $odv->description }}</td>
                    @endif
                    <td style="width:50%;text-align:right ;">{{ $odv->units->symbol }}</td>
                    <td style="text-align:center ;">{{ number_format($odv->quantity,2) }}</td>
                    <td style="text-align: left;">{{ number_format($odv->price,2) }}</td>
                    <td style="text-align: right;">
                        {{number_format($odv->total,2) }}
                    </td>
                </tr>
            @endforeach
            <tr class="tr-gap">
                <td colspan="8"></td>
                <td colspan="1" style="text-align: left; border-top: 1px  dashed black; white-space:nowrap;">
                    Sub Total:
                </td>
                <td colspan="4" style="text-align: right; border-top: 1px  dashed black;">
                    {{ number_format($ord->total_amount-$ord->tax_amount,2) }}
                </td>
            </tr>
            <tr class="tr-gap">
                <td colspan="8"></td>
                <td colspan="2" style="text-align: left;white-space:nowrap;">
                    Non Taxable Amt:
                </td>
                <td colspan="4" style="text-align: right;">
                    {{ number_format($ord->total_amount-$ord->taxable_amount-$ord->tax_amount>=1?$ord->total_amount-$ord->taxable_amount-$ord->tax_amount:0,2) }}
                </td>
            </tr>

            <tr class="tr-gap">
                <td colspan="8"></td>
                <td colspan="2" style="text-align: left; white-space:nowrap;">
                    Taxable Amt:
                </td>
                <td colspan="3" style="text-align: right;">
                    {{number_format($ord->taxable_amount,2) }}
                </td>
            </tr>

            <tr class="tr-gap">
                <td colspan="8"></td>
                <td colspan="1" style="text-align: left; white-space:nowrap;">
                    VAT Amt:
                </td>
                <td colspan="4" style="text-align: right;">
                    {{number_format($ord->tax_amount,2) }}
                </td>
            </tr>

            <tr class="tr-gap">
                <td colspan="8"></td>
                <td colspan="1" style="text-align: left; border-bottom: 1px  dashed black; white-space:nowrap;">
                    Total:
                </td>
                <td colspan="4" style="text-align: right; border-bottom: 1px  dashed black;">
                    {{ number_format($ord->total_amount,2) }}
                </td>
            </tr>

            {{-- <tr>
                <td colspan="4"></td>
                <td colspan="3" style="text-align: left;">
                 Paid Amt:
             </td>
             <td style="text-align: right;">
              22,526.93
            </td>
            </tr> --}}
            {{-- <tr> --}}
            {{-- <td colspan="4"></td>
            <td colspan="3" style="text-align: left; border-bottom: 1px  dashed black;">
             Due Amt:
         </td>
         <td style="text-align: right; border-bottom: 1px  dashed black;">
            0.00
        </td>
        </tr> --}}


            </tbody>
        </table>

        <p style="border-bottom: 1px  dashed black;margin-top:1px;">
            In Words: <?php echo numberFomatter($ord->total_amount); ?>
        </p>
        <br>
        <span style="float: right;">
            <span>-------------------------------</span><br>
            <span style="text-align: center !important;margin-left: 55px;">Signature</span>
        </span>
    </div>
@endforeach

<script type="text/javascript">
    window.print();
    window.onafterprint = () => window.close();
</script>
</body>
</html>
