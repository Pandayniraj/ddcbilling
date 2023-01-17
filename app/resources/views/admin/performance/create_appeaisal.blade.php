@extends('layouts.master')
@section('content')
<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
    <h1>
        {{$page_title ?? "Page Title"}}
        <small>{{$page_description ?? "Page Description"}}</small>
    </h1>
    {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
</section>
<form method="post" action="{{route('admin.performance.create-appeaisal')}}">
    <div class="panel panel-custom">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-horizontal form-groups-bordered">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="user_info" class="col-sm-3 control-label">Employee<span class="required">*</span></label>

                            <div class="col-sm-5">
                                <select required name="user_info" id="user_info" class="form-control select_box">

                                    <option value="">Select Employee</option>
                                    @foreach($department as $dep)
                                    <optgroup label="{{$dep->deptname}}">
                                        <?php $emp = \App\User::select('users.id', 'users.username', 'tbl_designations.designations', 'users.designations_id')->leftjoin('tbl_designations', 'tbl_designations.designations_id', '=', 'users.designations_id')->where('users.departments_id', $dep->departments_id)->get();
                                        ?>
                                        @foreach($emp as $e)
                                        <option value="<?php echo serialize([$e->id, $e->designations_id]) ?>" @if($selecteduser==$e->id) selected @endif>
                                            {{ucfirst(trans($e->username))}}({{$e->designations}})
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                    <!--    @foreach($designations as $d)
                                 <option value="{{ $d->designations_id }}" >{{$d->designations}}</option>
                                @endforeach -->
                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_info" class="col-sm-3 control-label">Evaluator<span class="required">*</span></label>

                            <div class="col-sm-5">
                                <select required name="evaluator_id" id="evaluator_id" class="form-control select_box">

                                    <option value="">Select Evaluator</option>
                                    @foreach($department as $dep)
                                    <optgroup label="{{$dep->deptname}}">
                                        <?php $emp = \App\User::select('users.id', 'users.username', 'tbl_designations.designations', 'users.designations_id')->leftjoin('tbl_designations', 'tbl_designations.designations_id', '=', 'users.designations_id')->where('users.departments_id', $dep->departments_id)->get();
                                        ?>
                                        @foreach($emp as $e)
                                        <option value="<?php echo serialize([$e->id, $e->designations_id]) ?>" @if($selecteduser==$e->id) selected @endif>
                                            {{ucfirst(trans($e->username))}}({{$e->designations}})
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach

                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="date_in" class="col-sm-3 control-label">Month<span class="required"> *</span></label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input required="" type="text" class="form-control date_in" value="{{ isset($selecteddate) ? $selecteddate : '' }}" name="appraisal_month" id="appraisal_month">
                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>


                            <button class="btn btn-primary" id="btn-submit-edit" type="submit" name="showappeaisal" value="show">Go</button>

                        </div>
                    </div>
                </div>
            </div>
            @if($showappeaisal)
                @if($userappeaisal)
                <div align="center" style="margin-left: -150px !important"><label style="color: red"> Appraisal Information Already provided to this user once for:</label> <label>{{date('F Y', strtotime($userappeaisal->appraisal_month))}}</label></div>
                @endif
                <div class="row">
                    @foreach($apprisalObjTypes as $aotk => $aotv)
                        @if($aotv->objectives->count())
                        <div class="col-sm-6">
                            <div class="panel panel-custom">
                                <div class="bg-info box-header">
                                    <span class="panel-title pull-left">{{ $aotv->name }}</span>
                                    <span class="panel-title pull-right">
                                        <span class="badge bg-yellow">{{ $aotv->points }}</span>
                                        Marks
                                    </span>
                                </div>
                                <div class="box-body">
                                    @foreach($aotv->objectives as $obk => $obv)
                                    <br />
                                    <div class="row">
                                        <div class="form-group" id="border-none">
                                            <span class="col-sm-8">{{ $obv->objective }}</span>
                                            <div class="col-sm-4">
                                                <select name="appraisal_marks[$aotv->id][$obv->id]" class="form-control">
                                                    @foreach($qw as $key=>$tm)
                                                        <option value="{{round(($key*$obv->marks*0.2), 2)}}">{{$tm}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="panel panel-custom">
                            <div class="col-md-6">
                                <label for="inputEmail3" class="control-label">Final Comments</label>
                                <textarea class="form-control" name="comment" id="comment" placeholder="Write Description">{!! $userappeaisal->general_remarks !!}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="inputEmail3" class="control-label">Recommendations</label>
                                <textarea class="form-control" name="recommendation" id="recommendation" placeholder="Write Recommendation">{!! $userappeaisal->recommendation !!}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <br>
                        @if($userappeaisal)
                        <div class="form-group">
                            <button class="btn btn-primary" id="btn-submit-edit" type="submit" name="updateappeasial" value="update">Update</button>
                        </div>
                        <input type="hidden" name="aid" value="{{$userappeaisal->performance_appraisal_id}}">
                        @else
                        <div class="form-group">
                            <button class="btn btn-primary" id="btn-submit-edit" type="submit" name="createappeasial" value="create">Create</button>
                            <a class="btn btn-default" href="/admin/performance/appraisal"> Cancel </a>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</form>
@endsection
@section('body_bottom')
<link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet" type="text/css" />
<script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
<script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}" type="text/javascript"></script>

<!-- Timepicker -->
<link href="{{ asset("/bower_components/admin-lte/bootstrap/css/timepicker.css") }}" rel="stylesheet" type="text/css" />
<script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/timepicker.js") }}" type="text/javascript"></script>

<!-- SELECT2-->
<link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2.css") }}">
<link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2-bootstrap.css") }}">
<script src="{{ asset("/bower_components/admin-lte/select2/js/select2.js") }}"></script>

<script type="text/javascript">
    $(function() {
        $('#appraisal_month').datetimepicker({
            format: 'YYYY-MM',
            sideBySide: true
        });


    });
</script>
@endsection