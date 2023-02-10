@extends('layouts.master')

@section('head_extra')
    <link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet"/>
    <script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

    @include('partials._head_extra_select2_css')
@endsection

@section('content')
    @php $readonly = isset($readonly) ? $readonly : false;  @endphp

    @include('admin.products.modals.unit-modal')
    @include('admin.products.modals.category-modal')
    <div class="nav-tabs-custom" id="tabs">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">{{ $page_title??'General Product Item Settings'}}</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::model( $course, ['route' => ['admin.products.update', $course->id], 'method' => 'PATCH', 'id' => 'form_edit_course','enctype'=>'multipart/form-data'] ) !!}
                        @include('admin.products.form')
                        <br>
                        <div class="form-group">
                            {!! Form::button( trans('general.button.update'), ['class' => 'btn btn-primary btn-sm', 'id' => 'btn-submit-edit','type'=>'Submit'] ) !!}
                            <a href="{!! route('admin.products.index') !!}" title="{{ trans('general.button.cancel') }}"
                               class='btn btn-default btn-sm'>{{ trans('general.button.cancel') }}</a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('body_bottom')
    <script>
        $(document).ready(function () {
            $('.select2').select2()
        })
        $(function () {
            $('#outlet_id').on('change', function () {
                if ($(this).val() != '') {
                    $.ajax({
                        url: "/admin/outlet/ajax/getMenu"
                        , data: {
                            outlet_id: $(this).val()
                        }
                        , dataType: "json"
                        , success: function (data) {
                            var result = data.data;
                            $('#menu_id').html(result);
                        }
                    });
                }
            });
        });

    </script>
@endsection
