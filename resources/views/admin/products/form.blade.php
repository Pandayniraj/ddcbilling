<div class="content">
    <div class="form-group">
        {!! Form::label('name', trans('admin/courses/general.columns.name')) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required', $readonly]) !!}
    </div>
    {{-- <div class="form-group col-md-6">--}}
    {{--     {!! Form::label('price', 'Distributor Sales Pricing') !!}--}}
    {{--     {!! Form::text('distributor_price', null, ['class' => 'form-control', $readonly]) !!}--}}
    {{-- </div>--}}
    {{-- <div class="form-group col-md-6">--}}
    {{--     {!! Form::label('price', 'Retailer Sales Pricing') !!}--}}
    {{--     {!! Form::text('retailer_price', null, ['class' => 'form-control', $readonly]) !!}--}}
    {{-- </div>--}}
    {{-- <div class="form-group col-md-6">--}}
    {{--     {!! Form::label('price', 'Direct Customer Sales Pricing') !!}--}}
    {{--     {!! Form::text('direct_customer_price', null, ['class' => 'form-control', $readonly]) !!}--}}
    {{-- </div>--}}

    <div class="form-group col-md-6">
        <label>Select Product Unit <a href="#" data-target="#modal_dialog_unit"
                                      data-toggle="modal">[+]</a></label>
        {!! Form::select('product_unit', $product_unit, null, ['class' => 'form-control label-primary','id'=>'product-unit', 'required']) !!}
    </div>
    <div class="form-group col-md-6">
        <label>Select Category <a href="#" data-target="#modal_dialog_category"
                                  data-toggle="modal">[+]</a></label>
        {!! Form::select('category_id', $categories, null, ['class' => 'form-control label-primary','id'=>'product-category']) !!}
    </div>
    <div class="form-group col-md-6">
        <label>Staff Quota Frequent</label>
        {!! Form::select('staff_quota_frequent', ['monthly' => 'Monthly', 'daily' => 'Daily'], null, ['class' => 'form-control', 'placeholder' => 'Select Staff Quota Frequent']) !!}
    </div>
    <div class="form-group col-md-6">
        <div class="checkbox">
            <label>
                {!! '<input type="hidden" name="enabled" value="0">' !!}
                {!! Form::checkbox('enabled', '1', null) !!} {{ trans('general.status.enabled') }}
            </label> <br>

            <label>
                {!! '<input type="hidden" name="is_vat" value="0">' !!}
                {!! Form::checkbox('is_vat', '1', null) !!} Is Vat
            </label>
        </div>
    </div>
    {{-- <div class="form-group col-md-6">--}}
    {{--     <label>Projects</label>--}}
    {{--     {!! Form::select('store_id', $stores, null, ['class' => 'form-control', 'required']) !!}--}}
    {{-- </div>--}}

    <div class="form-group col-md-6">
        <label>Staff Quota Limit</label>
        {!! Form::number('staff_quota', null, ['class' => 'form-control', 'placeholder' => 'Staff Quota Limit']) !!}
    </div>
    <div class="form-group col-sm-6">
        {!! Form::label('product_image', 'Product Image') !!}
        <div class="">
            <input type="file" name="product_image" class="form-control" accept="image/*">
            @if($course->product_image)
                <img src="{{ asset('products/'.$course->product_image) }}" alt="Product Img" style="width: 120px; height: auto;">
            @endif
        </div>
    </div>
</div>
<hr>
