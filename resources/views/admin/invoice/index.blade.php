@extends('layouts.master')
@section('head_extra')
    <link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet"/>

    <style type="text/css">
        @media only screen and (max-width: 770px) {

            .hide_on_tablet {
                display: none;
            }
        }

        .nep-date-toggle {
            width: 120px !important;
        }

        img.p-image {
            width: 27px;
            height: 27px;
            object-fit: cover;
            object-position: bottom;
            border-radius: 50%;
        }

        .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
            border: none !important;
        }

        .form-control {
            border-radius: 6px !important;
            box-shadow: none;
            border-color: #aaa;
            height: 28px;
        }

        .box {
            box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px !important;
            border-top: none;
            border-radius: 12px;
        }
        table#orders-table {
            font-size: 12px;
        }
        input.nep-date-toggle.form-control.andp-date-picker {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            {{ $page_title }} Manager
            <small>{{ $page_description }}</small>
        </h1>
        Current Fiscal Year: <strong>{{ FinanceHelper::cur_fisc_yr()->fiscal_year}}</strong>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}

    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-header">
                    <a class="btn btn-primary btn-xs pull-left" title="Create new invoice"
                       href="{{ route('admin.invoice.create') }}" style="margin-top: 15px;">
                        <i class="fa fa-plus"></i>&nbsp;<strong> Create new tax invoice</strong>
                    </a>
                </div>

                <div class="wrap hide_on_tablet" style="margin-top:15px;margin-left:11px;">
                    <form method="get" action="/admin/invoice1">
                        <div class="filter form-inline" style="margin:0 30px 0 0;">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="start_date">Start Date</label>
                                    {!! Form::text('start_date', \Request::get('start_date'), ['style' => 'width:100% !important;', 'class' => 'form-control input-sm input-sm date-toggle-nep-eng1', 'id'=>'start_date', 'placeholder'=>'Bill start date...','autocomplete' =>'off']) !!}
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date">End Date</label>
                                    {!! Form::text('end_date', \Request::get('end_date'), ['style' => 'width:100% !important; display:inline-block;', 'class' => 'form-control input-sm input-sm date-toggle-nep-eng1', 'id'=>'end_date', 'placeholder'=>'Bill end date..','autocomplete' =>'off']) !!}
                                </div>
                                <div class="col-md-3" style="margin-top: 5px;">
                                    <label for="filter-customer">Customer</label>
                                    {!! Form::select('client_id', ['' => 'Select Customer'] + ($clients ?? []), \Request::get('client_id'), ['id'=>'filter-customer', 'class'=>'form-control input-sm searchable', 'style'=>'width:100%; display:inline-block;']) !!}
                                </div>
                                <div class="col-md-3" style="margin-top: 5px;">
                                    <label for="fiscal_year">Year</label>
                                    {!! Form::select('fiscal_year', ['' => 'Fiscal Year'] + ($fiscal_years ?? []), \Request::get('fiscal_year'), ['id'=>'fiscal_year', 'class'=>'form-control input-sm searchable', 'style'=>'width:100%; display:inline-block;']) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::text('bill_no', \Request::get('bill_no'), ['style' => 'width:100%; display:inline-block;', 'class' => 'form-control input-sm input-sm', 'id'=>'bill_no', 'placeholder'=>'Enter bill number...','autocomplete' =>'off']) !!}
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control input-sm searchable" style="width: 100%;" name="outlet_id">
                                        <option value="">Select Outlet</option>
                                        @if(isset($outlets))
                                            @if(\Auth::user()->hasRole('admins'))
                                                @foreach($outlets as $key=>$out)
                                                    <option value="{{ $key }}"
                                                            @if(Request::get('outlet_id') == $key) selected="" @endif>
                                                        {{$out}}
                                                    </option>
                                                @endforeach
                                            @else
                                                {
                                                @foreach($outlets as $key=>$out)
                                                    <option value="{{ $out->id }}"
                                                            @if(Request::get('outlet_id') == $out->id) selected="" @endif>
                                                        {{$out->name}}
                                                    </option>
                                                @endforeach
                                                }
                                            @endif
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3" style="margin-top: 5px;">
                                    {!! Form::select('pay_status',[''=>'All Payments','Pending'=>'Pending',
                                   'Partial'=>'Partial','Paid'=>'Paid'] , Request::get('pay_status') ,
                                   ['class'=>'form-control input-sm','id'=>'pay_status', 'style' => 'width: 100%;'])  !!}
                                    <input type="hidden" name="search" value="true">
                                    <input type="hidden" name="type" value={{ Request::get('type') }}>
                                </div>
                                <div class="col-md-3" style="margin-top: 5px;">
                                    <button class="btn btn-primary btn-sm" id="btn-submit-filter" type="submit">
                                        <i class="fa fa-list"></i> Filter
                                    </button>
                                    <a href="/admin/invoice1" class="btn btn-default btn-sm" id="btn-filter-clear">
                                        <i class="fa fa-close"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                {!! Form::open( array('route' => 'admin.orders.enable-selected', 'id' => 'frmClientList') ) !!}
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="orders-table">
                            <thead>
                            <tr class="bg-info">
                                <th> Num</th>
                                {{-- <th>Bill date AD</th>--}}
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
                            @if(isset($orders) && !empty($orders))
                                @foreach($orders as $o)
                                    @php
                                        $paidAmount = TaskHelper::getTaxInvoicePaidAmount($o->id);
                                        if( $paidAmount >= $o->total_amount ){
                                            $paystatus = 'Paid';
                                            $paystatusClass = 'label-success';
                                        }elseif($paidAmount > 0){
                                            $paystatus = 'Partial';
                                            $paystatusClass = 'label-info';
                                        }else{
                                            $paystatus = 'Pending';
                                            $paystatusClass = 'label-warning';
                                        }
                                    @endphp

                                    @if(!Request::get('pay_status') || Request::get('pay_status') == $paystatus )
                                        <tr>
                                            <td>
                                                <a target="_blank"
                                                   href="/admin/entries/show/{{\FinanceHelper::get_entry_type_label($o->entry->entrytype_id)}}/{{$o->entry->id}}">{{$o->entry->number}}</a>
                                            </td>
{{--                                            <td>{{ date('dS M y',strtotime($o->bill_date))}}</td>--}}
                                            <td>{{ TaskHelper::getNepaliDate($o->bill_date) }}</td>
                                            <td style="padding-left: 8px;"><span class=""> <img
                                                        src="/images/profiles/{{$o->user->image ? $o->user->image : 'default.png'}}"
                                                        class="img-circle img-fluid" style="width: 27px;height: 27px;"
                                                        alt="User Image">  {{ucfirst($o->user->username)}} </span></td>
                                            {{-- <td>{{env('SALES_BILL_PREFIX')}}{{ $o->bill_no }}</td> --}}
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
                                            {{-- <td>{{ date('dS M y',strtotime($o->due_date))}}</td>--}}
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
                                                    <a href="{{route('admin.invoice.preview-thermalprint', $o->id)}}" target="_blank"
                                                       title="print"><i class="fa fa-print"
                                                                        style="font-size: 25px;"></i></a>
                                                @else
                                                    <a href="{{route('admin.invoice.preview-print', $o->id)}}" target="_blank"
                                                       title="print"><i class="fa fa-print"
                                                                        style="font-size: 25px;"></i></a>
                                                @endif
                                                <a href="/admin/invoice/payment/{{$o->id}}" title="Receive Payments"
                                                   style="margin-left: 5px;"><i class="fa fa-credit-card"
                                                                                style="font-size: 22px;"></i></a>
                                                {{-- @if( $o->isEditable())
                                                <a href="{{ route('admin.invoice.edit',$o->id) }}" title="edit"><i class="fa fa-edit"></i></a>
                                                @endif --}}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        {!! $orders->render() !!}
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection


<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
    <script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
    <script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}"
            type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}"
            type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.js") }}"></script>
    <script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

    @include('partials._date-toggle')
    <script language="JavaScript">
        function toggleCheckbox() {
            checkboxes = document.getElementsByName('chkClient[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = !checkboxes[i].checked;
            }
        }

        $('.date-toggle-nep-eng1').nepalidatetoggle();
    </script>

    <script>
        $(function () {
            $('#start_date').datepicker({
                //format: 'YYYY-MM-DD',
                dateFormat: 'yy-m-d',
                sideBySide: true,

            });
            $('#end_date').datepicker({
                //format: 'YYYY-MM-DD',
                dateFormat: 'yy-m-d',
                sideBySide: true,
            });
        });
    </script>

    <script type="text/javascript">
        $('.searchable').select2();
    </script>

@endsection
