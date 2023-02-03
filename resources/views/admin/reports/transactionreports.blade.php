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
            Transaction Report
            <small>Customer Wise Sales Report</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
    </section>
    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-header">
                    {!! Form::open(['route' => 'admin.reports.transactionreports', 'id' => 'transaction_report' , 'method'=>'GET','target'=>'_blank']) !!}
                    <div class="content col-md-9" style="min-height: auto;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>
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
                            <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Outlet</label>
                                        <div class="input-group">
                                            <select name="outletid" class="form-control searchable" required>
                                                <option value="" disableSelected> Select Outlets</option>
                                                @foreach($outlets as $key=> $value)
                                                    <option value="{{ $value->id}}" @if((request()->outletid??'') == $value->id) selected @endif>{{$value->name}}</option>
                                                @endforeach
                                            </select>
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
                            @else
                                @php
                                    $outlet_user= \App\Models\OutletUser::where('user_id', \Auth::user()->id)->first();
                                    $outletid= \App\Models\PosOutlets::where('id',$outlet_user->outlet_id)->first();
                                @endphp
                                <input type="hidden" name="outletid" value="{{ $outletid->id}}" class="form-control">
                            @endif

                            <div class="col-md-3">
                                <div class="form-group">
                                    <br/>
                                    {!! Form::submit('Filter', ['class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($detail_transaction))
                        <div class="download-print float-right">
                            <button class="btn btn-sm btn-primary" type="submit" value="export" name="type"><i class="fa fa-file"></i> Export Excel</button>
                            <button class="btn btn-sm btn-success" type="submit" value="print" name="type"><i class="fa fa-print"></i> Pdf Download</button>
                        </div>
                    @endif
                    {!! Form::close() !!}
                </div>
            </div>
            @if(isset($detail_transaction))
            <div class="box">
                <div class="box-body">
                    <p><b>Company </b> {{ $organization->organization_name }}</p>
                    <p><b>Transaction Report of </b>{{ $outletname->name}} test</p>
                    <h3>Transaction Report From {{ $nepalistartdate }} to {{ $nepalienddate }}</h3>
                    <div class="row">
                        <table>
                            <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Customer</th>
                                <th>DR Amount</th>
                                <th>DR Vat</th>
                                <th>DR Total</th>
                                <th>CR Amount</th>
                                <th>Period Balance</th>
                                <th>Overall Balance</th>
                            </tr>

                            </thead>
                            <tbody>

                            @foreach ($clients as $client)
                                <tr>
                                    <td>{{$loop->index+1}}</td>
                                    <td>{{$client->name}}</td>
                                    @php
                                        $client_id=$client->id;
                                        $client_invoice=\App\Models\Invoice::where('client_id',$client_id)
                                        ->where('bill_date','>=',$startdate)
                                        ->where('bill_date','<=',$enddate)
                                        ->pluck('id');
                                        $temp=\App\Models\InvoicePayment::whereIn('invoice_id',$client_invoice)->select(DB::raw('SUM(amount) as cr_amount'))->first();
                                        $period_cr_amount=$temp->cr_amount??0;

                                        $overall_dr=\App\Models\Invoice::
                                        select('client_id',DB::raw('SUM(total_amount) as dr_total'))
                                        ->where('client_id',$client_id)
                                        ->first();
                                        $overall_paid=\App\Models\Invoicepayment::whereIn('invoice_id',\App\Models\Invoice::where('client_id',$client_id)->pluck('id'))->select(DB::raw('SUM(amount) as cr_amount'))->first();
                                        // dd($overall_detail,$overall_paid);
                                        $dr_amount=(double)$detail_transaction[$client_id][0]->dr_total - $detail_transaction[$client_id][0]->dr_vat;

                                    @endphp
                                    <td class="align-center">{{$dr_amount}}</td>
                                    <td class="align-center">{{(double)$detail_transaction[$client_id][0]->dr_vat??0}}</td>
                                    <td class="align-center">{{(double)$detail_transaction[$client_id][0]->dr_total??0}}</td>


                                    <td class="align-center">{{$period_cr_amount}}</td>
                                    <td class="align-center">{{(double)$detail_transaction[$client_id][0]->dr_total??0 - (double)$period_cr_amount}}</td>
                                    <td class="align-center">{{(double)$overall_dr->dr_total - (double)$overall_paid->cr_amount}}</td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-xs-3" style="padding-top:20px">Submitted By</div>
                        <div class="col-xs-3" style="padding-top:20px">Marketing Officer</div>
                        <div class="col-xs-3" style="padding-top:20px">Project Manager</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3" style="padding-top:40px">_______________</div>
                        <div class="col-xs-3" style="padding-top:40px">_______________</div>
                        <div class="col-xs-3" style="padding-top:40px">_______________</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
@section('body_bottom')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script src="{{ asset('/bower_components/admin-lte/plugins/daterangepicker/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js') }}"
            type="text/javascript"></script>

    @include('partials._date-toggle')
    <script type="text/javascript">
        $('.date-toggle-nep-eng1').nepalidatetoggle();
        $(function () {

            $('.datepicker').datetimepicker({
                //inline: true,
                format: 'YYYY-MM-DD',
                sideBySide: true,
                allowInputToggle: true
            });
        });

        $(".searchable").select2();

    </script>
@endsection

