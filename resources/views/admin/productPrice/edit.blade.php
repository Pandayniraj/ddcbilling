@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            {!! $page_title !!}
            <small>{!! $page_description ?? "Page description" !!}</small>
        </h1>
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-primary">
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::model( $productPricing, ['route' => ['admin.product-pricing.update', $productPricing->id], 'method' => 'PATCH', 'id' => 'form_edit_course','enctype'=>'multipart/form-data'] ) !!}
                        <div class="content">
                            <div class="form-group col-md-6">
                                <label>Projects</label>
                                {!! Form::select('project_id', $projects, null, ['class' => 'form-control searchable', 'id' =>'project-id', 'placeholder' => 'Select Projects', 'required']) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('price', 'Distributor Sales Pricing') !!}
                                {!! Form::number('distributor_price', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('price', 'Retailer Sales Pricing') !!}
                                {!! Form::number('retailer_price', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('price', 'Customer Sales Pricing') !!}
                                {!! Form::number('customer_price', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            {!! Form::button( trans('general.button.update'), ['class' => 'btn btn-primary btn-sm', 'id' => 'btn-submit-edit','type'=>'Submit'] ) !!}
                            <a href="{!! route('admin.product-pricing.index', $product->id) !!}" title="{{ trans('general.button.cancel') }}"
                               class='btn btn-default btn-sm'>{{ trans('general.button.cancel') }}</a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="product-id" value="{{$product->id}}">
@endsection

@section('body_bottom')
    <script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.searchable').select2();

            checkExists();
            $(document).on("change", "#project-id", function (e) {
                checkExists();
            });

            function checkExists() {
                $.ajax({
                    url: '{{route("admin.product-pricing.validate-request")}}',
                    type: 'POST',
                    data: {project_id: $("#project-id").val(), product: {{$product->id}}, product_price_id: {{$productPricing->id}}, _token: '{{ csrf_token() }}'},
                    success: function(result) {
                        if(result) {
                            $("#btn-submit-edit").attr('disabled',true);
                            alert('Product price for following product already exists.')
                        } else {
                            $("#btn-submit-edit").attr('disabled',false);
                        }
                    }
                });
            }
        })
    </script>
@endsection
