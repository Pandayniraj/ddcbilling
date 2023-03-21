@extends('layouts.master')
@section('head_extra')
    <link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet"/>

    <style type="text/css">
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
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px;">
        <a class="btn btn-primary btn-xs pull-left" title="Print Invoice"
           href="{{ route('admin.invoice.thermalprint', $ord->id) }}" target="_blank">
            <i class="fa fa-print"></i>&nbsp;<strong> Print</strong>
        </a>
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <?php
                $loop = 1;
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
                            <span><b> TAX Invoice </b> </span>
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
                                        {{number_format($odv->total-$odv->tax_amount,2) }}
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
            </div>
        </div>
    </div>
@endsection

