@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')
@endsection

@section('content')
    <link href="{{ asset('/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css') }}" rel="stylesheet"
        type="text/css" />
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
                <div class="box-body">
                    {!! Form::open(['route' => 'admin.reports.transactionreports', 'id' => 'transaction_report' , 'method'=>'GET','target'=>'_blank']) !!}
                    <div class="content col-md-9">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <div class="input-group">
                                        <input id="ReportStartdate" type="text" name="startdate" class="form-control datepicker date-toggle-nep-eng1" value="{{ request()->get('startdate') ?? old('startdate')}}">
                                        <div class="input-group-addon">
                                            <i>
                                                <div class="fa fa-info-circle" data-toggle="tooltip" title="Note : Leave start date as empty if you want statement from the start of the financial year.">
                                                </div>
                                            </i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <!-- /.form group -->
                            </div>
                             <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>

                                    <div class="input-group">
                                        <input id="ReportEnddate" type="text" name="enddate" class="form-control datepicker date-toggle-nep-eng1" value="{{request()->get('enddate') ?? old('enddate')}}">
                                        <div class="input-group-addon">
                                            <i>
                                                <div class="fa fa-info-circle" data-toggle="tooltip" title="Note : Leave end date as empty if you want statement till the end of the financial year.">
                                                </div>
                                            </i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <!-- /.form group -->
                            </div>
                            @if (\Auth::user()->username == 'root')
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Outlet</label>

                                    <div class="input-group">
                                        <select name="outletid" class="form-control" required>
                                            <option value="" disableSelected> Select Outlets</option>
                                            @foreach($outlets as $key=> $value)
                                            <option value="{{ $value->id}}">{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-addon">
                                            <i>
                                                <div class="fa fa-info-circle" data-toggle="tooltip" title="Note : Leave end date as empty if you want statement till the end of the financial year.">
                                                </div>
                                            </i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <!-- /.form group -->
                            </div>
                            @else
                            @php
                            $outlet_user= \App\Models\OutletUser::where('user_id', \Auth::user()->id)->first();
                            $outletid= \App\Models\PosOutlets::where('id',$outlet_user->outlet_id)->first();
                            @endphp
                          <input type="hidden" name="outletid" value="{{ $outletid->id}}" class="form-control">
                         @endif

                            <div class="col-md-12">
                                <div class="form-group">
                                    <br />
                                    {!! Form::submit('Download Report', ['class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection
@section('body_bottom')
    <script src="{{ asset('/bower_components/admin-lte/plugins/daterangepicker/moment.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js') }}"
        type="text/javascript"></script>

    @include('partials._date-toggle')
    <script type="text/javascript">
         $('.date-toggle-nep-eng1').nepalidatetoggle();
        $(function() {

            $('.datepicker').datetimepicker({
              //inline: true,
              format: 'YYYY-MM-DD',
              sideBySide: true,
              allowInputToggle: true
            });

          });

    </script>
@endsection

