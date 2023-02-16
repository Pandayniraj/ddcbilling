@extends('layouts.master')
@section('head_extra')
    <link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet"/>

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
        table {
            border: 1px solid dotted !important;
            font-size: 12px !important;
        }
        td {
            border: 1px dotted #999 !important;

        }
        th {
            border: 1px dotted #999 !important;
        }
        .invoice-col {
        / border: 1 px dotted #1a4567 !important;
        / font-size: 13 px !important;
            margin-bottom: -20px !important;
        }
        @page {
            size: auto;
            margin: 0;
        }
        .white-unit{
            border-right: 1px solid #d2d6de !important;
        }
        @media print {
            .pagebreak {
                page-break-before: always;
            }
            .footer {
                position: fixed;
                bottom: 15px;
                /*margin-bottom: 10px;*/
            }
            .white-unit{
                border-right: 1px solid #fff !important;
            }
            /* page-break-after works, as well */
        }

        .page-header {
            border-bottom: none;
        }
        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
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
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px;">
        <a class="btn btn-primary btn-xs pull-left" title="Print Invoice"
           href="{{ route('admin.invoice.print', $ord->id) }}" target="_blank">
            <i class="fa fa-print"></i>&nbsp;<strong> Print</strong>
        </a>
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <?php
                $loop = 1;

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
                        '19' => 'nineteen',
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
                    return $result . ' Rupees' . $points;
                }
                ?>

                <div class='wrapper'>
                    @foreach(range(1,$loop) as $key)
                        @if($key >= 2) <div class="pagebreak"></div> @endif
                        <section class="invoice">
                            <div class="row">
                                <div class="col-xs-12">
                                    <h2 class="page-header">
                                        <div class="col-xs-4">
                                            <img src="{{ '/org/'.auth()->user()->organization->logo }}">
                                        </div>
                                        <address>
                                            @php $outlet_code=\App\Models\PosOutlets::where('id',$ord->outlet_id)->first(); @endphp
                                            <div class="col-xs-7" style="text-align: center;">
                                                <h4 style="margin-bottom: -7px;">Dairy Development Corporation</h4>
                                                <h5 style="margin-bottom: -7px;">{{ $outlet_code->name??'' }}
                                                    Pan:{{ \Auth::user()->organization->vat_id }}</h5>
                                                <h5 style="margin-bottom: -7px;">{{ \Auth::user()->organization->email }}
                                                    / {{ $outlet_code->email??'' }}</h5>
                                                <h5 style="margin-bottom: -7px;">{{ $outlet_code->phone }}</h5>
                                                <h5 style="margin-bottom: -8px;">
                                                    <b>TAX Invoice</b>
                                                </h5>
                                            </div>
                                            <div class="col-xs-1"></div>
                                        </address>
                                        <hr/>
                                    </h2>
                                </div>
                            </div>
                            <div class="row invoice-info">
                                <div class="col-xs-4 invoice-col">
                                    {{-- <span style="font-size: 15px; font-weight: bold" > {{$outlet_code->short_name}} </span><br> --}}
                                    <strong> Customer:</strong><span style="white-space: nowrap;">{{ $ord->client->name }}</span>
                                    <strong style="white-space: nowrap;"> Customer Address:</strong><span
                                        style="white-space: nowrap;">{{ $ord->client->physical_address??'' }}</span><br>
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
                                        <Strong>Invoice No:</Strong>{{$outlet_code->short_name}}/{{$ord->fiscal_year}}
                                        /00{{ $ord->bill_no }}<br>
                                        <strong>Printed Date:</strong><span>{{ date('Y-m-d') }}<br><strong>Invoice Date:</strong><span>{{ $ord->bill_date??'' }}<br>
                        <strong>Due Date:</strong><span>{{ $ord->due_date??'' }}<br>
                        <strong> Mode of Payment:</strong>{{ $ord->bill_type }}<br></span></p>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead class="bg-gray">
                                        <tr class="">
                                            <th style="width: 5%;">S.No</th>
                                            <th style="width: 30%;">Particulars</th>
                                            <th style="width: 6%; text-align: center;">Vat</th>
                                            <th style="width: 8%; text-align: center;" class="white-unit">Unit</th>
                                            <th style="width: 1%;"></th>
                                            <th style="width: 15%; text-align: center">Quantity</th>
                                            <th style="width: 15%; text-align: center">Rate</th>
                                            <th style="width: 20%; text-align: center">Amount</th>
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
                                                <td style="text-align: center;">{{ $odv->product->is_vat?'13%':0 }}</td>
                                                <td style="text-align: center; border-right: 1px solid #fff !important;">{{ $odv->units->symbol }}</td>
                                                <td></td>
                                                <td style="text-align: center">{{ number_format($odv->quantity,2) }}</td>
                                                <td style="text-align: right">{{ number_format($odv->price,2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($odv->total-$odv->tax_amount,2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="8" style="border-left: 1px solid #fff !important; border-right: 1px solid #fff !important;padding: 5px !important;"></td>
                                        </tr>
                                        <tr>
                                            <th colspan="4" rowspan="4">Remarks</th>
                                            <td style="border-top: 1px solid #fff !important; border-bottom: 1px solid #fff !important;"></td>
                                            <th colspan="2">Subtotal: </th>
                                            <th style="text-align: right;">{{ number_format($ord->total_amount-$ord->tax_amount,2) }}</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <th colspan="2">Discount: </th>
                                            <th style="text-align: right;">{{ number_format($ord->discount_amount,2) }}</th>
                                        </tr>
                                        <tr>
                                            <td style="border-top: 1px solid #fff !important; border-bottom: 1px solid #fff !important;"></td>
                                            <th colspan="2">Non Taxable Amount: </th>
                                            @if($ord->total_amount-$ord->taxable_amount-$ord->tax_amount >= 1)
                                                <td style="text-align: right;">{{ number_format($ord->total_amount-$ord->taxable_amount-$ord->tax_amount,2) }}</td>
                                            @else
                                                <td style="text-align: right;"> 0.00</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <th colspan="2">Taxable Amount: </th>
                                            <th style="text-align: right;">{{ number_format($ord->taxable_amount,2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" rowspan="4">
                                                In Words: <?php echo numberFomatter($ord->total_amount); ?>
                                            </th>
                                            <td style="border-top: 1px solid #fff !important; border-bottom: 1px solid #fff !important;"></td>
                                            <th colspan="2">Vat Amount: </th>
                                            <th style="text-align: right;">{{ number_format($ord->tax_amount,2) }}</th>
                                        </tr>
                                        <tr>
                                            <td style="border-bottom: 1px solid #fff !important;"></td>
                                            <th colspan="2">Total Amount: </th>
                                            <th style="text-align: right;">{{ number_format($ord->total_amount,2) }}</th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <p style="margin-bottom:-2px;"><b>Bank Account Detail For Electronic Payment</b></p>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead class="bg-gray">
                                            <tr>
                                                <th style="width: 30%">Particulars</th>
                                                <th style="width: 30%">Bank One</th>
                                                <th style="width: 30%">Bank Two</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>Bank A/C Name</td>
                                                <td>@if(isset($ord->outlet->bank_ac_name_one)) {{ $ord->outlet->bank_ac_name_one??'' }} @endif</td>
                                                <td>@if(isset($ord->outlet->bank_ac_name_two)) {{ $ord->outlet->bank_ac_name_two??'' }} @endif</td>
                                            </tr>
                                            <tr>
                                                <td>Bank A/C No.</td>
                                                <td>@if(isset($ord->outlet->bank_account_one)) {{ $ord->outlet->bank_account_one??'' }} @endif</td>
                                                <td>@if(isset($ord->outlet->bank_account_two)) {{ $ord->outlet->bank_account_two??'' }} @endif</td>
                                            </tr>
                                            <tr>
                                                <td>Bank Name</td>
                                                <td>@if(isset($ord->outlet->bank_name_one)) {{ $ord->outlet->bank_name_one??'' }} @endif</td>
                                                <td>@if(isset($ord->outlet->bank_name_two)) {{ $ord->outlet->bank_name_two??'' }} @endif</td>
                                            </tr>
                                            <tr>
                                                <td>Branch</td>
                                                <td>@if(isset($ord->outlet->bank_address_one)) {{ $ord->outlet->bank_address_one??'' }} @endif</td>
                                                <td>@if(isset($ord->outlet->bank_address_two)) {{ $ord->outlet->bank_address_two??'' }} @endif</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row footer footer-new" style="margin-top: 150px;">
                                <div class="col-xs-4">
                                    _______________________
                                    <br><span style="text-indent: 10px;">
                        &nbsp;&nbsp;Customer Seal & Signature</span>
                                </div>
                                <div class="col-xs-4">
                                    _______________________
                                    <br><span style="text-indent: 10px;">
                        &nbsp;&nbsp;Prepared By</span>
                                </div>
                                <div class="col-xs-4">
                                    _______________________<br><span style="text-indent: 10px; white-space: nowrap;"><b>For, Dairy Development Corp.</b><br>
                        &nbsp;&nbsp;Authorised Signatory</span>
                                </div>
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
