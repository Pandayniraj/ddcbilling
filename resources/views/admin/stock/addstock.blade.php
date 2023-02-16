@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
@endsection

@section('content')

    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            Add Stock
            <small>{!! $page_description ?? "Page description" !!}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-header">
                <div class="content">
                    {!! Form::open( ['route'=>'admin.stock.storestock', 'class' => 'form-horizontal', 'id' => 'form_edit_client','files'=>'true'] ) !!}
                    <div class="col-sm-6">
                        <label class="control-label">Outlets</label>
                        <select name="store_id" class="form-control searchable" required id="outlet-id">
                            <option value="">Select</option>
                            @foreach ($stores as $store )
                                <option value={{ $store->id }}>{{ $store->name }}</option>
                            @endforeach
                        </select>
                        {{-- {!! Form::select('store_id',[''=>'Select Outlets']+$stores, $current_store ?? null, ['class'=>'form-control']) !!} --}}
                    </div>
                    <div class="col-sm-6 ">
                        <label class="control-label"> Recevice From Store Date</label>
                        <input type="date" name="date" class="form-control datepicker date-toggle-nep-eng"
                               value="{{$date}}">
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th>S.N</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Store Return</th>
                            <th>Opening Stock</th>
                            <th>Remarks</th>
                        </tr>
                        <tbody>
                        <input type="hidden" id="profuct-id" value="{{$products}}">
                        @foreach ($products as $key=>$product)
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{$product}}</td>
                                <td><input type="number" class="quantity move-input" name="quantity[{{$key}}]" value="0"></td>
                                <td><input type="number" id="return_quantity" name="return_quantity[{{$key}}]" readonly></td>
                                <td><input type="number" id="opening-stock-{{$key}}" name="opening_stock[{{$key}}]" readonly></td>
                                <td><input type="text" id="remarks" name="remarks[{{$key}}]" class="move-input"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="form-group">
                        {!! Form::button( trans('general.button.create'), ['class' => 'btn btn-primary btn-sm', 'id' => 'btn-submit-edit'] ) !!}
                        <a href="/admin/addstock/list" class="btn btn-default btn-sm"> Cancel</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body_bottom')
<script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

<!-- form submit -->
@include('partials._date-toggle')
@include('partials._body_bottom_submit_client_edit_form_js')
<script>
    $(".searchable").select2();

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
        getOpeningStockValue({_token:'{{csrf_token()}}', product_id:$('#profuct-id').val()});

        $(document).on("change", "#outlet-id", function () {
            getOpeningStockValue({_token:'{{csrf_token()}}', product_id:$('#profuct-id').val(), outlet_id:$("#outlet-id").val()});
        });

        $("#form_edit_client").submit(function(e) {
            var customer_type = $("#customer_type").val();
            var outlet_id = $("#outlet-id").val();
            if(outlet_id == '') {
                e.preventDefault();
                alert('Please select Outlet');
            }
        });

        $(document).on('click', '#btn-submit-edit', function (e) {
            e.preventDefault();
            if (confirm("Are you Sure!") == true) {
                $("#form_edit_client").submit();
            }
        });
    });

    function getOpeningStockValue(val) {
        $.ajax({
            type:'POST',
            url:"{{route('admin.stock.get-opening-stock-price')}}",
            data:val,
            success:function(response) {
                console.log(response);
                $.each(response, function(k, v) {
                    $(`#opening-stock-${k}`).val(v);
                });
            }
        });
    }
</script>
@endsection
