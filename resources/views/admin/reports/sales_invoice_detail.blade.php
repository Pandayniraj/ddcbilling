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
            {{$page_title}}
            <small>{{$page_description}}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
    </section>
    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['route' => 'admin.reports.sales-invoice-detail', 'id' => 'dailysales_report' , 'method'=>'GET']) !!}
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
                                <table class="table table-hover table-bordered table-striped" id="orders-table">
                                    <thead>
                                    <tr class="bg-info">
                                        <th>Invoice</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th colspan="2">Taxable</th>
                                        <th colspan="4">Export</th>
                                    </tr>
                                    <tr class="bg-info">
                                        <th> Num</th>
                                        <th>Bill date BS</th>
                                        <th>Officer</th>
                                        <th>Bill No.</th>
                                        <th>Customer name</th>
                                        <th>Due date</th>
                                        <th>Total</th>
                                        <th>Tax Amount</th>
                                        <th>Outlet</th>
                                        <th>Pay Status</th>
                                        <th>Tools</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($detail_transactions) && !empty($detail_transactions))
                                        @foreach($detail_transactions as $o)
                                                <tr>
                                                    <td>
                                                        <a target="_blank"
                                                           href="/admin/entries/show/{{\FinanceHelper::get_entry_type_label($o->entry->entrytype_id)}}/{{$o->entry->id}}">{{$o->entry->number}}</a>
                                                    </td>
                                                    <td>{{ TaskHelper::getNepaliDate($o->bill_date) }}</td>
                                                    <td style="padding-left: 8px;"><span class=""> <img
                                                                src="/images/profiles/{{$o->user->image ? $o->user->image : 'default.png'}}"
                                                                class="img-circle img-fluid" style="width: 27px;height: 27px;"
                                                                alt="User Image">  {{ucfirst($o->user->username)}} </span></td>
                                                    <td>{{$o->outlet->short_name}}/{{$o->fiscal_year}}/00{{$o->bill_no }}</td>
                                                    <td>

                                                        @if($o->client->image)
                                                            <img src="{{asset($o->client->image)}}" class="p-image" id="blah"
                                                                 src="#" alt="your image"  onerror="this.onerror=null;this.src='/images/profiles/default.png';"/>
                                                        @else
                                                            <img src="/images/profiles/default.png" class="p-image" id="blah"
                                                                 src="#" alt="your image"/>
                                                        @endif

                                                        <span> <a href="/admin/invoice1/{{$o->id}}"
                                                                  style="color: #1a2226c9;"> {{ $o->client->name??$o->customer_name }}</a> <small>{{ $o->name }}</small> </span>
                                                    </td>
                                                    <td>{{ TaskHelper::getNepaliDate($o->due_date) }}</td>
                                                    <td>{!! number_format($o->total_amount,2) !!}</td>
                                                    <td>{{ number_format($o->tax_amount, 2) }}</td>
                                                    <td> {{ $o->outlet->outlet_code  }} </td>
                                                    <td><span class="label {{$paystatusClass}}">{{$paystatus}}</span></td>
                                                        <?php
                                                        $checkprintformat = \App\Models\Posoutlets::where('id', $o->outlet_id)->first()->printformat;
                                                        ?>
                                                    <td style="display: flex;">
                                                        @if($checkprintformat=="thermal")
                                                            <a href="/admin/invoice/thermalprint/{{$o->id}}" target="_blank"
                                                               title="print"><i class="fa fa-print"
                                                                                style="font-size: 25px;"></i></a>
                                                        @else
                                                            <a href="/admin/invoice/print/{{$o->id}}" target="_blank"
                                                               title="print"><i class="fa fa-print"
                                                                                style="font-size: 25px;"></i></a>
                                                        @endif
                                                        <a href="/admin/invoice/payment/{{$o->id}}" title="Receive Payments"
                                                           style="margin-left: 5px;"><i class="fa fa-credit-card"
                                                                                        style="font-size: 22px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    </tbody>
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

