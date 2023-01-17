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

    .select2-container--bootstrap .select2-results__group { font-size: 15px !important; padding: 6px 3px !important; }
    .select2-container--bootstrap .select2-results__option .select2-results__option { color: #777 !important; }

    .fileinput {
        margin-bottom: 9px;
        display: inline-block;
    }

    .fileinput-exists .fileinput-new, .fileinput-new .fileinput-exists {
        display: none;
    }
    .fileinput-filename { padding-left:  5px; }

    .fileinput .btn {
        vertical-align: middle;
    }

    .btn.btn-default {
        border-color: #ddd;
        background: #f4f4f4;
    }
    .btn-file {
        overflow: hidden;
        position: relative;
        vertical-align: middle;
    }

    .btn-file > input {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        opacity: 0;
        filter: alpha(opacity=0);
        transform: translate(-300px, 0) scale(4);
        font-size: 23px;
        direction: ltr;
        cursor: pointer;
    }
    input[type="file"] {
        display: block;
    }

    .close {
        float: right;
        font-size: 21px;
        font-weight: bold;
        line-height: 1;
        color: #000000;
        text-shadow: 0 1px 0 #ffffff;
        opacity: 0.2;
        filter: alpha(opacity=20);
    }

    .fileinput.fileinput-exists .close {
        opacity: 1;
        color: #dee0e4;
        position: relative;
        top: 3px;
        margin-left: 5px;
    }
</style>
<link href="{{ asset("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.css") }}" rel="stylesheet" type="text/css" />

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                {{$page_title ?? "Page Title"}}
                <small>{!! $page_description ?? "Page description" !!}</small>
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>

<div class="row">
    <div class="col-sm-12" data-offset="0">
        <div class="panel panel-custom">
          {!! Form::open( ['route' => 'jobapplication.store', 'id' => 'form_edit_lead', 'enctype' => 'multipart/form-data'] ) !!} 
            <div class="content">
                            

                            <div class="form-group">
                                {!! Form::label('full_name', 'Full name') !!}
                                {!! Form::text('full_name', null, ['class' => 'form-control', 'required'=>'required']) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('email', 'Email') !!}
                                {!! Form::text('email', null, ['class' => 'form-control', 'required'=>'required']) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('mobile', 'Mobile') !!}
                                {!! Form::text('mobile', null, ['class' => 'form-control','required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('application_for', 'Application For') !!}
                                {!! Form::select('application_for',$jobtitle, null, ['class' => 'form-control','required'=>'required']) !!}
                            </div>
                            <div>
                                <label>CV / Resume</label>
                                <input type="file" name="cv_file" class="form-control">
                            </div>
                            

                         <div class="form-group">
                           {!! Form::button( trans('general.button.create'), ['class' => 'btn btn-primary', 'id' => 'btn-submit','type'=>'Submit'] ) !!}
                           <a href="/admin/job_applied" title="{{ trans('general.button.cancel') }}" class='btn btn-default'>{{ trans('general.button.cancel') }}</a>
                          </div>
            </form>
        </div>
    </div>

</div>



@endsection

