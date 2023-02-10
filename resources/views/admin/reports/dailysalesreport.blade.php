@extends('layouts.master')

@section('head_extra')
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
            Daily Sales Report
            <small>Product Wise Sales Report</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
    </section>
    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['route' => 'admin.reports.dailysales_report', 'id' => 'dailysales_report' , 'method'=>'GET']) !!}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <div class="input-group">
                                        <input id="ReportStartdate" type="text" name="startdate"
                                               class="form-control datepicker date-toggle-nep-eng1"
                                               value="{{ request()->get('startdate') ?? old('startdate')}}">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Project</label>
                                        <div class="input-group">
                                            <select name="project_id" id="project-id" class="form-control searchable"
                                                    required>
                                                <option value="">Select Project</option>
                                                <option value="over-all" selected>All Project</option>
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
                                <div class="col-md-1">
                                    @php
                                        $outlet_user= \App\Models\OutletUser::where('user_id', \Auth::user()->id)->first();
                                        $outletid= \App\Models\PosOutlets::where('id',$outlet_user->outlet_id)->first();
                                    @endphp
                                    <input type="hidden" name="outletid" value="{{ $outletid->id}}"
                                           class="form-control">
                                </div>
                            @endif
                            <div class="col-md-2">
                                <div class="form-group">
                                    <br/>
                                    {!! Form::submit('Filter Report', ['class' => 'btn btn-primary btn-sm', 'id' => 'btn-submit-edit']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(isset($products))
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

            @if(isset($products))
                <p><b>{{ $organization->organization_name }}</b></p>
                <p><b>{{$outletname->name}}<b></p>
                <h3>Sales Report From {{ $nepalistartdate }} to {{ $nepalienddate }}</h3>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped center">
                            <thead class="bg-info">
                            <tr>
                                <th rowspan=2>S.N</th>
                                <th rowspan=2>Particular</th>
                                <th rowspan=2>Opening Stock</th>
                                <th rowspan=2>Cold Store</th>
                                <th rowspan=2>Total</th>
                                <th colspan="3">Distributor</th>
                                <th colspan="3">Retailer</th>
                                <th colspan="3">Boothman</th>
                                <th colspan="3">Direct Customer</th>
                                <th colspan="3">Staff</th>
                                <th rowspan="2">Coldstore Return</th>
                                <th rowspan="2">Closing Stock</th>
                                <th colspan="3">Total Sales</th>
                            </tr>
                            <tr>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>VAT</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $dist_col_amount=0;
                                $dist_col_vat=0;
                                $retail_col_amount=0;
                                $retail_col_vat=0;
                                $boothman_col_amount=0;
                                $boothman_col_vat=0;
                                $dc_col_amount=0;
                                $dc_col_vat=0;
                                $staff_col_amount=0;
                                $staff_col_vat=0;
                            @endphp
                            @foreach ($products as  $productid=>$productname)
                                <tr>
                                    @php
                                        $alya = \App\Helpers\TaskHelper::getOpeningStock($productid, $startdate, request()->project_id??'over-all');
                                        // $alya=0;
                                        $cold_store=$stock[$productid][0]->quantity??0;
                                        $total=$cold_store+$alya;
                                    @endphp
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $productname }}</td>
                                    <td>{{ $alya }}</td>
                                    <td>{{$cold_store  }}</td>
                                    <td>{{ $total }}</td>
                                    {{-- //distributor --}}
                                    @php
                                        $distributordata=$data[$productid]['distributor'];
                                        $dist_qty=0;
                                        $dist_amount=0;
                                        $dist_vat=0;
                                        foreach ($distributordata as $value) {
                                            $dist_qty+=$value->quantity;
                                            // $dist_amount+=$value->total_amount;
                                            $dist_amount+=$value->total;
                                            $dist_vat+=$value->tax_amount;
                                            $dist_col_amount+=$value->total_amount;
                                            $dist_col_vat+=$value->tax_amount;
                                        }
                                    @endphp
                                    <td>{{ $dist_qty }}</td>
                                    <td>{{ $dist_amount }}</td>
                                    <td>{{ $dist_vat }}</td>
                                    {{-- //retailer --}}
                                    @php
                                        $retailerdata=$data[$productid]['retailer'];
                                        $retailer_qty=0;
                                        $retailer_amount=0;
                                        $retailer_vat=0;
                                        foreach ($retailerdata as $value) {
                                          $retailer_qty+=$value->quantity;
                                          $retailer_amount+=$value->total_amount;
                                          $retailer_vat+=$value->tax_amount;

                                          $retail_col_amount+=$value->total_amount;
                                          $retail_col_vat+=$value->tax_amount;
                                        }
                                    @endphp
                                    <td>{{ $retailer_qty }}</td>
                                    <td>{{ $retailer_amount }}</td>
                                    <td>{{ $retailer_vat }}</td>
                                    {{-- boothman --}}
                                    @php
                                        $boothmandata=$data[$productid]['boothman'];
                                        $boothman_qty=0;
                                        $boothman_amount=0;
                                        $boothman_vat=0;
                                        foreach ($boothmandata as $value) {
                                          $boothman_qty+=$value->quantity;
                                          $boothman_amount+=$value->total_amount;
                                          $boothman_vat+=$value->tax_amount;

                                          $boothman_col_amount+=$value->total_amount;
                                          $boothman_col_vat+=$value->tax_amount;
                                        }
                                    @endphp
                                    <td>{{ $boothman_qty }}</td>
                                    <td>{{ $boothman_amount }}</td>
                                    <td>{{ $boothman_vat }}</td>
                                    {{-- direct_customer --}}
                                    @php
                                        $direct_customerdata=$data[$productid]['direct_customer'];
                                        $direct_customer_qty=0;
                                        $direct_customer_amount=0;
                                        $direct_customer_vat=0;
                                        foreach ($direct_customerdata as $value) {
                                          $direct_customer_qty+=$value->quantity;
                                          $direct_customer_amount+=$value->total_amount;
                                          $direct_customer_vat+=$value->tax_amount;

                                          $dc_col_amount+=$value->total_amount;
                                          $dc_col_vat+=$value->tax_amount;
                                        }
                                    @endphp
                                    <td>{{ $direct_customer_qty }}</td>
                                    <td>{{ $direct_customer_amount }}</td>
                                    <td>{{ $direct_customer_vat }}</td>
                                    {{-- staff --}}
                                    @php
                                        $staffdata=$data[$productid]['staff'];
                                        $staff_qty=0;
                                        $staff_amount=0;
                                        $staff_vat=0;
                                        foreach ($staffdata as $value) {
                                          $staff_qty+=$value->quantity;
                                          $staff_amount+=$value->total_amount;
                                          $staff_vat+=$value->tax_amount;

                                          $staff_col_amount+=$value->total_amount;
                                          $staff_col_vat+=$value->tax_amount;
                                        }
                                    @endphp
                                    <td>{{ $staff_qty }}</td>
                                    <td>{{ $staff_amount }}</td>
                                    <td>{{ $staff_vat }}</td>
                                    <td></td>
                                    <td>{{ $total -($dist_qty + $retailer_qty + $boothman_qty + $direct_customer_qty + $staff_qty)}}</td>
                                    <td>{{ $dist_qty + $retailer_qty + $boothman_qty + $direct_customer_qty + $staff_qty}}</td>
                                    <td>{{ $dist_amount + $retailer_amount + $boothman_amount + $direct_customer_amount + $staff_amount}}</td>
                                    <td>{{ $dist_vat + $retailer_vat + $boothman_vat + $direct_customer_vat + $staff_vat}}</td>

                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="6">Total</th>
                                <th>{{ $dist_col_amount }}</th>
                                <th>{{ $dist_col_vat }}</th>
                                <th></th>
                                <th>{{ $retail_col_amount }}</th>
                                <th>{{ $retail_col_vat }}</th>
                                <th></th>
                                <th>{{ $boothman_col_amount }}</th>
                                <th>{{ $boothman_col_vat }}</th>
                                <th></th>
                                <th>{{ $dc_col_amount }}</th>
                                <th>{{ $dc_col_vat }}</th>
                                <th></th>
                                <th>{{ $staff_col_amount }}</th>
                                <th>{{ $staff_col_vat }}</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>{{ $dist_col_amount + $retail_col_amount +  $boothman_col_amount + $dc_col_amount + $staff_col_amount }}</th>
                                <th>{{ $dist_col_vat + $retail_col_vat + $boothman_col_vat + $dc_col_vat +$staff_col_vat }}</th>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-xs-3" style="padding-top:20px">Submitted By</div>
                    <div class="col col-xs-3" style="padding-top:20px">Marketing Officer</div>
                    <div class="col col-xs-3" style="padding-top:20px">Project Manager</div>
                </div>

                <div class="row">
                    <div class="col-xs-3" style="padding-top:40px">_______________</div>
                    <div class="col-xs-3" style="padding-top:40px">_______________</div>
                    <div class="col-xs-3" style="padding-top:40px">_______________</div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('body_bottom')
    @include('partials._date-toggle')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        $('.searchable').select2();
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

