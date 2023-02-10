@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
@endsection

@section('content')

    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            Edit Stock
            <small>{!! $page_description ?? "edit stock details" !!}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-header">
                <div class="content">

                    {!! Form::open( ['route'=>'admin.stock.save_return', 'class' => 'form-horizontal', 'id' => 'form_edit_client','files'=>'true'] ) !!}
                    <div class="col-sm-6">
                        <label class="control-label">Outlets</label>
                        {!! Form::select('store_id',[''=>'Select Outlets']+$stores, $newstock->store_id ?? null, ['class'=>'form-control']) !!}
                    </div>
                    <div class="col-sm-6 ">
                        <label class="control-label"> Recevice From Store Date</label>
                        <input type="date" name="date" class="form-control datepicker date-toggle-nep-eng"
                               value="{{$newstock->date}}">
                    </div>

                    {{-- <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4 control-label" style="text-transform: capitalize;">
                            Recevice From Store Date
                        </label>
                        <div>
                            {!! Form::date('date',$newstock->date, null, ['class' => 'form-control', 'readonly'=>'readonly']) !!}
                        </div>
                    </div> --}}
                    <input type="hidden" name="stock_id" value="{{$newstock->id}}">
                    <table class="table table-bordered">
                        <tr>
                            <th>S.N</th>
                            <th>Product Name</th>
                            <th>Buy Quantity</th>
                            <th>Return Quantity</th>
                            <th>Opening Stock</th>
                            <th>Remarks</th>
                        </tr>
                        <tbody>
                        @foreach ($stock_entries as $id=>$stock)
                            @php
                                $key=$stock->stock_id;
                            @endphp
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{$stock->product_details->name}}</td>
                                <td><input type="number" id="quantity" name="quantity[{{$key}}]" value="{{$stock->buy_qty}}" readonly></td>
                                <td><input type="number" id="return_quantity" name="return_quantity[{{$key}}]" max="{{$stock->return_qty}}" placeholder="Previuos return {{$stock->return_qty}}"></td>
                                <td><input type="number" id="opening-stock" name="opening_stock[{{$key}}]" readonly value="{{$stock->opening_stock}}"></td>
                                <td><input type="text" id="remarks" name="remarks[{{$key}}]" value="{{$stock->remarks}}"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="form-group">
                        {!! Form::button( trans('general.button.update'), ['class' => 'btn btn-primary', 'id' => 'btn-submit-edit'] ) !!}
                        <a href="/admin/addstock/list" class="btn btn-default"> Cancel</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body_bottom')
    <!-- form submit -->
    @include('partials._date-toggle')
    @include('partials._body_bottom_submit_client_edit_form_js')
    <script>
        $(function () {
            $('.date-toggle-nep-eng').nepalidatetoggle();
            $('.datepicker').datetimepicker({
                //inline: true,
                format: 'YYYY-MM-DD',
                sideBySide: true,
                allowInputToggle: true
            });
        });

        $(document).ready(function () {
            $("#nep-eng-date-toogle").val('nep');
            $("#nep-eng-date-toogle").trigger('change');
        });
    </script>
@endsection
