@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')

    <link href="{{ asset('/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css') }}" rel="stylesheet"
          type="text/css"/>

    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .row .col {
            margin: 0 !important;
            padding: 0 !important;
        }
        .row {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        td.align-center {
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            Daily Sales Detail Report
            <small>Sales Detail Report</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
    </section>
    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['route' => 'admin.reports.customerwisedetailreports', 'id' => 'dailysales_report' , 'method'=>'GET']) !!}
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Date</label>
                                <div class="input-group">
                                    <input id="ReportStartdate" type="text" name="startdate"
                                           class="form-control datepicker date-toggle-nep-eng1"
                                           value="{{ request()->get('startdate') ?? old('startdate')}}" required>
                                    <div class="input-group-addon">
                                        <i>
                                            <div class="fa fa-info-circle" data-toggle="tooltip"
                                                 title="Note : Leave start date as empty if you want statement from the start of the financial year.">
                                            </div>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>End Date</label>
                                <div class="input-group">
                                    <input id="ReportEnddate" type="text" name="enddate"
                                           class="form-control datepicker date-toggle-nep-eng1"
                                           value="{{request()->get('enddate') ?? old('enddate')}}">
                                    <div class="input-group-addon">
                                        <i>
                                            <div class="fa fa-info-circle" data-toggle="tooltip"
                                                 title="Note : Leave end date as empty if you want statement till the end of the financial year.">
                                            </div>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (\Auth::user()->hasRole('admins'))
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Project</label>
                                    <div class="input-group">
                                        <select name="project_id" id="project-id" class="form-control searchable"
                                                required>
                                            <option value="">Select Project</option>
                                            <option value="over-all" selected>All Project
                                            </option>
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}"
                                                        @if(request()->project_id==$project->id) selected @endif>{{$project->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Outlet</label>
                                    <div class="input-group">
                                        <select name="outletid" class="form-control searchable" id="outlet-id">
                                            <option value="" disableSelected> Select Outlets</option>
                                            @foreach($outlets as $key=> $value)
                                                <option value="{{ $value->id}}"
                                                        @if((request()->outletid??'') == $value->id) selected @endif>{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @else
                            @php
                                $outlet_user= \App\Models\OutletUser::where('user_id', \Auth::user()->id)->first();
                                $outletid= \App\Models\PosOutlets::where('id',$outlet_user->outlet_id)->first();
                            @endphp
                            <input type="hidden" name="outletid" value="{{ $outletid->id}}" class="form-control">
                        @endif

                        <div class="col-md-2">
                            <div class="form-group">
                                <br/>
                                {!! Form::submit('Filter', ['class' => 'btn btn-primary']) !!}
                            </div>
                        </div>
                    </div>
                    @if(isset($detail_transaction))
                        <div class="download-print float-right">
                            <button class="btn btn-sm btn-primary" type="submit" value="export" name="type"><i
                                    class="fa fa-file"></i> Excel
                            </button>
                            <button class="btn btn-sm btn-success" type="submit" value="print" name="type"><i
                                    class="fa fa-print"></i> Pdf
                            </button>
                        </div>
                    @endif
                    {!! Form::close() !!}
                </div>
            </div>

            @if(isset($detail_transaction))
                <div class="box">
                    <div class="box-body">
                        <p style="text-align:center"><Strong>{{ $organization->organization_name }}</strong></p>
                        <p Style="text-align:center"><strong>{{ $outletname->name}}</strong></p>
                        <p style="text-align:center">Daily Sales Detail Report</p>
                        <p>
                            <span
                                style="float:left">Detail Report From {{ $nepalistartdate }} to {{ $nepalienddate }}</span>
                            <span
                                style="float:right">Print Date:{{(\App\Helpers\TaskHelper::getNepaliDate(\Carbon\Carbon::today()->toDateString()))}}</span>
                            <br>
                        @php
                            $grand_amount=0;
                            $grand_vat_amount=0;
                            $grand_total_amount=0;
                        @endphp
                        <div class="row">
                            <div class="table-responsive">
                            <table class="table table-striped center" style="width:100%;">
                                <thead class="bg-info">
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
                                            <th style="text-align:center;" colspan="3">Customer
                                                name: {{\App\Models\Client::find($client_id)->name??""}}</th>
                                            <th style="text-align:center;" colspan="3">Prepared
                                                By: {{$created_by??""}}</th>
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
                                                    <td style="text-align:center;">{{$details->order->outlet->short_name}}
                                                        /{{$details->order->fiscal_year}}
                                                        /00{{$details->order->bill_no }}</td>
                                                    <td style="text-align:center;">{{$details->product->name}}</td>
                                                    <td style="text-align:right;">{{$details->quantity}}</td>
                                                    <td style="text-align:right;">{{$details->total-$details->tax_amount}}</td>
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
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('body_bottom')
    @include('partials._date-toggle')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".btn-primary, .btn-success").attr('disabled', false);
        });

        $(".searchable").select2();
        $('.date-toggle-nep-eng1').nepalidatetoggle();
        $(function () {
            $('.datepicker').datetimepicker({
                //inline: true,
                format: 'YYYY-MM-DD',
                sideBySide: true,
                allowInputToggle: true
            });
        });
    </script>

    <script>
        $(document).on('change', '#project-id', function () {
            if ($(this).val() != 'over-all') {
                $.ajax({
                    method: "POST",
                    url: "{{route('admin.project.get-outlet')}}",
                    data: {
                        "project_id": $(this).val(),
                        "_token": '{{csrf_token()}}'
                    }
                }).done(function (response) {
                    $('#outlet-id').html('');
                    $('#outlet-id').append(`<option value="">Select Outlets</option>`);
                    $.each(response, function (k, v) {
                        $('#outlet-id').append(`<option value="${v.id}">${v.name}</option>`);
                    });
                });
            }
        });
    </script>
@endsection

