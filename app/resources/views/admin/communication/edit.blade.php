@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')
@endsection

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">

                {!! Form::model( $communication, ['route' => ['admin.communication.update', $communication->id], 'method' => 'PATCH', 'id' => 'form_edit_communication'] ) !!}

                @include('partials._communication_form')

                <div class="form-group">
                    {!! Form::submit( trans('general.button.update'), ['class' => 'btn btn-primary', 'id' => 'btn-submit-edit'] ) !!}
                    <a href="{!! route('admin.communication.index') !!}" title="{{ trans('general.button.cancel') }}" class='btn btn-default'>{{ trans('general.button.cancel') }}</a>
                </div>

                {!! Form::close() !!}
                
            

            
            </div><!-- /.box-body -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection
