@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')
@endsection

@section('content')
<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                Profile Centre
                <small> Welcome to profile centre {{ $user->username }}, to change password goto edit profile</small>   
            
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>

    <div class='row'>


        <div class='col-md-12 box'>

            <div class="box-body">
                <a href="/admin/profile/show/{{ auth()->user()->id }}" class="btn btn-info btn-sm">Public Profile </a>
                <a href="/admin/edit-my-profile" class="btn btn-default btn-sm">Edit PIS Profile </a>
                <a href="/user/profile" class="btn btn-default btn-sm">Edit Profile </a>
                
            </div>

            <div class="box-body">

                <div class="tab-pane active col-md-6" id="tab_details">
                    <div class="form-group">
                        {!! Form::label('first_name', trans('admin/users/general.columns.first_name')) !!}
                        {!! Form::text('first_name', $user->first_name, ['class' => 'form-control', 'readonly']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('last_name', trans('admin/users/general.columns.last_name')) !!}
                        {!! Form::text('last_name', $user->last_name, ['class' => 'form-control', 'readonly']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('username', trans('admin/users/general.columns.username')) !!}
                        {!! Form::text('username', $user->username, ['class' => 'form-control', 'readonly']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('email', trans('admin/users/general.columns.email')) !!}
                        {!! Form::text('email', $user->email, ['class' => 'form-control', 'readonly']) !!}
                    </div>

                
                </div>
                <div class="col-md-6" >
                	@if(\Auth::user()->image)
                    <label>Profile Photo:</label><br/>
                    <img style="max-width: 200px" src="/images/profiles/{!! \Auth::user()->image !!}" alt="{!! \Auth::user()->first_name !!}" />
                    @endif
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <a href="/admin/dashboard" class='btn btn-primary'>Close</a>
                        @if ($user->isEditable())
                            <a href="{!! route('admin.myprofile.edit') !!}" title="{{ trans('general.button.edit') }}" class='btn btn-default'>{{ trans('general.button.edit') }}</a>
                        @endif
                    </div>
                </div>

            </div><!-- /.box-body -->
        </div><!-- /.col -->

    </div><!-- /.row -->

@endsection

@section('body_bottom')
    <!-- Select2 js -->
    @include('partials._body_bottom_select2_js_role_search')
@endsection
