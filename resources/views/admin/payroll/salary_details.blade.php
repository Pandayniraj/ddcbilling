@extends('layouts.master')
@section('content')

<style>
    .required { color: red; }
    .panel-custom .panel-heading {
        border-bottom: 2px solid #1797be;
    }
    .panel-custom .panel-heading {
        margin-bottom: 10px;
    }
</style>
<link href="{{ asset("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.css") }}" rel="stylesheet" type="text/css" />
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-custom" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong>Manage Salary</strong>
                </div>
            </div>
            <div class="panel-body">
                <form id="attendance-form" role="form" enctype="multipart/form-data" action="/admin/payroll/manage_salary_details" method="post" class="form-horizontal form-groups-bordered">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label for="department" class=" control-label">Select Division<span
                                    class="required">*</span></label>
                            <div>
                                <select class="form-control select_box" name="division_id" required="" id="division_id">
                                    @foreach($divisions as $division)
                                        <option value="{{$division->id}}" @if(\Request::get('division_id') == $division->id) selected="selected" @endif>{{$division->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="department" class="control-label">Select Department<span
                                    class="required">*</span></label>
                            <div >
                                <select name="departments_id" id="department" class="form-control department select_box">
                                    <option value="">Select Department</option>
{{--                                    @foreach($departments as $dk => $dv)--}}
{{--                                        <option value="{{ $dv->departments_id }}" @if($departments_id == $dv->departments_id) selected="selected" @endif>--}}
{{--                                            {{$dv->division->name}} > {{ $dv->deptname }}</option>--}}
{{--                                    @endforeach--}}
                                </select>

                            </div>
                        </div>


                        <div class="col-sm-5">

</div>

<div class="col-sm-4">
    <label>
        <label for=""></label>
    </label>
    <div>
        <button type="submit" id="sbtn" class="btn btn-primary">Search</button>
    </div>
</div>

</div>
</form>
</div>
</div>
</div>
</div>

@if($users)
<div id="EmpprintReport">
<div class="row">
<div class="col-sm-12 std_print">
<div class="panel panel-custom">
<form id="form" role="form" enctype="multipart/form-data" action="/admin/payroll/store_salary_details" method="post" class="form-horizontal form-groups-bordered">
{{ csrf_field() }}
<table id="" class="table table-bordered std_table">
<thead>
<tr>
<th class="col-sm-3">Name</th>
<th class="col-sm-2">Designation</th>
<th class="col-sm-3">Salary Template</th>
{{--<th class="col-sm-2">Add 2 hours</th>--}}
<th class="col-sm-2">Marital Status</th>
</tr>
</thead>
<tbody>
<?php $flag = 0; ?>
@foreach($users as $uv)
<tr>
<td class="col-sm-3">{{ $uv->first_name.' '.$uv->last_name }}</td>
<td class="col-sm-2">{{ $uv->designation->designations }}</td>
<td class="col-sm-3">
    <?php $payroll = \PayrollHelper::getEmployeePayroll($uv->id); ?>
    <input type="hidden" name="user_id[]" value="{{ $uv->id }}">
    <select name="salary_template_id[]" class="form-control select_box">
        <option value="">Select Salary Template</option>
        @foreach($salaryTemplates as $stv)
        <option value="{{ $stv->salary_template_id }}" @if($payroll->salary_template_id == $stv->salary_template_id) selected @endif>{{ $stv->salary_grade }}</option>
        @endforeach
    </select>
</td>
{{--<td class="col-sm-2">--}}
{{--    <select class="form-control select_box two_hours_additional" name="additional_2_hours[]">--}}
{{--        <option value="0" @if($payroll->additional_2_hours == 0) selected @endif >NO</option>--}}
{{--        <option value="1" @if($payroll->additional_2_hours == 1) selected @endif >YES</option>--}}
{{--    </select>--}}
{{--</td>--}}
<td class="col-sm-2">
    <select class="form-control select_box marrital_option" name="marrital_option[]">
        <option value="0" @if($payroll->marrital_option == 0) selected @endif >Unmarried</option>
        <option value="1" @if($payroll->marrital_option == 1) selected @endif >Married</option>
    </select>
</td>
</tr>
@endforeach
</tbody>
</table>
<div class="col-sm-8"></div>
<div class="col-sm-4 row mt-lg pull-right" style="margin-top: 10px;">
<input type="hidden" name="departments_id" value="{{ $departments_id }}">
<button id="salery_btn" type="submit" class="btn btn-primary btn-block">Update</button>
</div>
</form>
</div>
</div>
</div>
</div>
@endif

@endsection


<!-- Optional bottom section for modals etc... -->
@section('body_bottom')

<!-- SELECT2-->
<link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2.css") }}">
<link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2-bootstrap.css") }}">
<script src="{{ asset("/bower_components/admin-lte/select2/js/select2.js") }}"></script>

<script type="text/javascript">
$(function() {

// $('.select_box').select2({
// theme: 'bootstrap',
// });

$('[data-toggle="tooltip"]').tooltip();
});


$(document).ready(function (){
    var division_id = $('#division_id').val();
    setDepartments(division_id)
})
$('#division_id').change(function(){
    var division_id = $(this).val();
    getDepartments(division_id)

});
function getDepartments(division_id){
    $.get(`/admin/get-departments/${division_id}`,function(res){
        let dept = res.data;
        var options = '<option value="">Select Department</option>';
        for(let u of dept){
            options = options + `<option value='${u.departments_id}'>${u.deptname}</option>`
        }
        $('#department').html(options);
    }).fail(function(){
        alert("Failed To Load");
    });
}
function setDepartments(division_id){
    var dept_id={!! json_encode(Request::get('departments_id')) !!}

    $.get(`/admin/get-departments/${division_id}`,function(res){
        let dept = res.data;
        var options = '<option value="">Select Department</option>';
        for(let u of dept){
            options = options + `<option value='${u.departments_id}'>${u.deptname}</option>`
        }
        $('#department').html(options).val(dept_id).change();
    }).fail(function(){
        alert("Failed To Load");
    });
}
</script>
@endsection
