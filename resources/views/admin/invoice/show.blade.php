@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')
    <style>
        .box-comment {
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        .box-comment img {
            float: left;
            margin-right: 10px;
        }

        .username {
            font-weight: bold;
        }

        .comment-text span {
            display: block;
        }

    </style>
@endsection

@section('content')
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

    // $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    ?>
    <div class='row'>
        <div class='col-md-12'>

            <section class="invoice">
                <!-- title row -->
                <div class="row">
                    <div class="col-xs-12">
                        <h2 class="page-header">
                            <img width="30%" height="" src="{{ '/org/'.auth()->user()->organization->logo }}">
                            <span class="pull-right">
                            <span>


                            Tax Invoice</span>
                            <a href="/admin/invoice/print/{{ $ord->id }}" target="_blank"
                               class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>

                            <a href="/admin/invoice1">
                                <button type="button" class="btn btn-success btn-sm pull-right">
                                    <i class="fa fa-times-circle"></i> Close
                                </button>
                            </a> &nbsp;




                        </span>
                            @if( $paidAmount >= $ord->total_amount )
                                <td><span class="label label-success">Paid</span></td>
                            @elseif($paidAmount > 0)
                                <td><span class="label label-info">Partial</span></td>
                            @else
                                <td><span class="label label-warning">Unpaid</span></td>
                            @endif
                        </h2>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- info row -->
                <div class="row invoice-info">
                    <div align="center" style="background-color: #CCCCCC">
                        @if(!$ord->is_bill_active)
                            {{$ord->void_reason}}
                        @endif
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <address>
                            @php
                                $outlet_code=\App\Models\PosOutlets::where('id',$ord->outlet_id)->first()->outlet_code??"KMSS";
                            @endphp
                            <span style="font-size: 15px; font-weight: bold"> {{$outlet_code}} </span><br>
                            Bill type:{{$ord->bill_type}}<br>
                            {{ env('APP_ADDRESS1') }}<br>
                            {{ env('APP_ADDRESS2') }}<br>
                            Phone: {{ env('APP_PHONE1') }}<br>
                            Email: {{ env('APP_EMAIL') }}<br>
                            Seller's PAN: {{ \Auth::user()->organization->vat_id }}
                        </address>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 invoice-col">
                        To: Customer: #0{{ $ord->client_id }}
                        <address>

                            <span style="font-size: 15px; font-weight: bold"> {{ $ord->client->name }}</span><br/>
                            Address: {!! nl2br($ord->address ) !!}<br>
                            Contact: {{ $ord->client->phone }}<br/>
                            Cust. PAN: {!! $ord->client->vat !!}<br/>
                            <strong>Contact Person: {{ $ord->name }}</strong><br>

                        </address>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 invoice-col">
                        <b>Bill No:</b> {{$outlet_code}}/{{$ord->fiscal_year}}/00{{ $ord->bill_no }}
                        <br>
                        <b>{{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}} {{\FinanceHelper::getAccountingPrefix('TAX_INVOICE_PRE')}}{{ $ord->id }}</b><br>
                        <b>Bill Date:</b> {{ date("d/M/Y", strtotime($ord->bill_date )) }}<br>
                        <?php $timestamp = strtotime($ord->created_at) ?>
                        <b>Due:</b> {{ date("d/M/Y", strtotime("+30 days", $timestamp )) }}<br>
                        <b>Payment Terms :</b> {{ $ord->terms }} Days<br>
                        Generated by: {{ $ord->user->first_name}} {{ $ord->user->last_name}}<br>
                        <b>Customer Account:</b> #{{ $ord->client_id }}
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                Thank you for choosing {{ env('APP_COMPANY') }}.
                Your {{ ucwords(str_replace("_", " ", ucfirst($ord->order_type)))}} is detailed below. If you find
                errors or desire certain changes, please contact us.
                <hr/>
                <!-- Table row -->
                <div class="row col-xs-12 table-responsive">
                    <div class="col-xs-12 table-responsive">
                        <table id="t01" class="table table-striped">
                            <thead class="bg-gray">
                            <tr>

                                <th>Particulars</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Vat(%)</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orderDetails as $odk => $odv)
                                <tr>
                                    @if($odv->is_inventory == 1)
                                        <td style="font-size: 16.5px">{{ $odv->product->name }}</td>
                                    @elseif($odv->is_inventory == 0)
                                        <td>{{ $odv->description }}</td>
                                    @endif
                                    <td>{{ $odv->price }}</td>
                                    <td>{{ $odv->quantity }}</td>
                                    <td>{{ $odv->units->name }}</td>
                                    <td>{{ $odv->tax_amount>0?'13':'0' }}</td>
                                    <td>{{ env('APP_CURRENCY').' '.$odv->total }}</td>
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
                    <div class="col-xs-6">
                        <p class="text-muted well well-sm no-shadow"
                           style="margin-top: 10px;text-transform: capitalize;font-size: 16,5px">
                            In Words: {{ numberFomatter($ord->total_amount) }}
{{--                            In Words: {{ $f->format($ord->total_amount)}}--}}
                        </p>


                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                            Above information is only an estimate of services/goods described above.
                        </p>

                        <h4> Special Notes and Instruction</h4>
                        <p class="text-muted well well-sm well-primary no-shadow" style="margin-top: 10px;">
                            {!! nl2br($ord->comment) !!}
                        </p>

                        <p class="text-muted well well-sm well-success no-shadow" style="margin-top: 10px;">
                            ___________________________________

                            <br>Authorized Signature
                        </p>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-6">


                        <div>
                            <table id="" class="table-responsive table table-striped">
                                <tbody>
                                <tr style="padding:0px; margin:0px;">
                                    <th style="width:50%">Sub Total:</th>
                                    <td>{{ env('APP_CURRENCY').' '. number_format($ord->subtotal,2) }}</td>
                                </tr>
                                <tr style="padding:0px; margin:0px;">
                                    <th style="width:50%">Discount Amount</th>
                                    <td>{{env('APP_CURRENCY')}} {{$ord->discount_amount }}</td>
                                </tr>
                                <tr>
                                    <th style="width:50%">Taxable Amount</th>
                                    <td>{{env('APP_CURRENCY').' '. number_format($ord->taxable_amount,2) }}</td>
                                </tr>
                                <tr style="padding:0px; margin:0px;">
                                    <th style="width:50%">Tax Amount(13%):</th>
                                    <td>{{ env('APP_CURRENCY').' '. number_format($ord->tax_amount,2) }}</td>
                                </tr>
                                <tr style="padding:0px; margin:0px;">
                                    <th style="width:50%">Total:</th>
                                    <td>{{ env('APP_CURRENCY').' '. number_format($ord->total_amount,2) }}</td>
                                </tr>
                                <!--   <tr>
                <th>Discount:</th>
                <td>{{ env('APP_CURRENCY').' '.($ord->discount_amount ? $ord->discount_amount : '0') }}</td>
              </tr>
              <tr>
                <th>Tax Amount</th>
                <td>{{ env('APP_CURRENCY').' '.$ord->total_tax_amount }}</td>
              </tr>

              <tr>
                <th>Total:</th>
                <td>{{ env('APP_CURRENCY').' '.$ord->total }}</td>
              </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <!-- this row will not appear when printing -->
                <div class="row no-print">
                    <div class="col-xs-12">


                    </div>
                </div>
            </section>


        </div><!-- /.col -->

    </div><!-- /.row -->

@endsection

@section('body_bottom')
    <!-- Select2 js -->
    @include('partials._body_bottom_select2_js_user_search')
@endsection
