@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
    <style>
    </style>
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            Stock List
            <small>{!! $page_description ?? "Page description" !!}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}

    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-header">
                    <a class="btn btn-primary btn-xs pull-left" title="Create new invoice"
                       href="{{ route('admin.stock.addstock') }}" style="margin-top: 15px;">
                        <i class="fa fa-plus"></i>&nbsp;<strong> Add Stock</strong>
                    </a>
                </div>

                <div class="wrap hide_on_tablet" style="margin-top:15px;margin-left:11px;">
                    <form method="get" action="{{route('admin.stock.stocklists')}}">
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
                                @if(\Auth::user()->hasRole('admins'))
                                    <div class="col-md-3">
                                        <label for="project-id">Project</label>
                                        <select name="project_id" id="project-id" class="form-control searchable" required>
                                            <option value="">Select Project</option>
                                            <option value="over-all" selected>All Project</option>
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}"
                                                        @if(request()->project_id==$project->id) selected @endif>{{$project->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="outlet-id">Outlet</label>
                                        <select name="outletid" class="form-control searchable" id="outlet-id">
                                            <option value="" disableSelected> Select Outlets</option>
                                            @foreach($outlets as $key=> $value)
                                                <option value="{{ $value->id}}"
                                                        @if((request()->outletid??'') == $value->id) selected @endif>{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-3" style="margin-top: 5px;">
                                    <button class="btn btn-primary btn-sm" id="btn-submit-filter" type="submit">
                                        <i class="fa fa-list"></i> Filter
                                    </button>
                                    <a href="{{route('admin.stock.stocklists')}}" class="btn btn-default btn-sm" id="btn-filter-clear">
                                        <i class="fa fa-close"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S.N</th>
                                <th>Date</th>
                                <th>Organization</th>
                                <th>Outlet</th>
                                <th>User</th>
                                <th>Action</th>
                            </tr>
                            <tbody>
                            @foreach ($stock_lists as $key=>$stock)
                                <tr>
                                    <td>{{$loop->index+1}}</td>
                                    <td>{{ TaskHelper::getNepaliDate($stock->date) }}</td>
                                    <td>{{$stock->org->organization_name}}</td>
                                    <td>{{$stock->location->name}}</td>
                                    <td>{{$stock->users->first_name}} {{$stock->users->last_name}}</td>
                                    <td>
                                        <a href="/admin/stock/details?stock_id={{$stock->id}}"
                                           class="btn btn-default"><i
                                                class="fa fa-eye"></i></a>
                                        @if (\Auth::user()->hasRole('admins'))
                                            <a href="/admin/stock/edit?stock_id={{$stock->id}}" class="btn btn-primary"
                                               title="Edit Stock"><i
                                                    class="fa fa-edit"></i></a>
                                            <a href="/admin/stock/destory?stock_id={{$stock->id}}"
                                               class="btn btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                    class="fa fa-trash"></i></a>
                                        @endif
                                        <a href="{{route('admin.stock.return', $stock->id)}}" class="btn btn-success"
                                           title="Return Stock"><i
                                                class="fa fa-repeat"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body_bottom')
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

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
        $('.searchable').select2();
        $(function () {
            $('#start_date').datepicker({
                dateFormat: 'yy-m-d',
                sideBySide: true,

            });
            $('#end_date').datepicker({
                dateFormat: 'yy-m-d',
                sideBySide: true,
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

