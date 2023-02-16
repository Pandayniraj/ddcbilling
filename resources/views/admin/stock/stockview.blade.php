@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
@endsection

@section('content')

    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            View Stock Details
            <small>{!! $page_description ?? "Page description" !!}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-header">
                <div class="content">


                    <div class="form-group">
                        <label for="inputEmail3" class="control-label" style="text-transform: capitalize;">
                            Recevice From Store Date :{{$newstock->date}}
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="control-label" style="text-transform: capitalize;">
                            Outlets :{{$newstock->location->name}}
                        </label>
                    </div>
                    <input type="hidden" name="stock_id" value="{{$newstock->location->id}}" id="outlet-id">
                    <input type="hidden" name="stock_id" value="{{$newstock->id}}">
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
                        @php
                            foreach($stock_entries as $stock) {
                                $product[] = $stock->stock_id;
                            }
                            $products=\App\Models\Product::where('org_id',\Auth::user()->org_id)->whereIn('id', $product)->pluck('name','id');
                        @endphp
                        <input type="hidden" id="profuct-id" value="{{$products}}">
                        @foreach ($stock_entries as $id=>$stock)
                            @php $key=$stock->stock_id; @endphp
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{$stock->product_details->name}}</td>
                                <td>{{$stock->qty}}</td>
                                <td>{{$stock->return_quantity}}</td>
                                <td id="opening-stock-{{$stock->stock_id}}">0</td>
                                <td>{{$stock->remarks}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="form-group">
                        <a href="/admin/addstock/list" class="btn btn-default"> Back</a>
                        <button type="button" id="check-click" class="btn btn-success">Check</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body_bottom')
    @include('partials._body_bottom_submit_client_edit_form_js')

    <script>
        $(document).ready(function () {
            getOpeningStockValue({_token:'{{csrf_token()}}', product_id:$('#profuct-id').val(), outlet_id:$("#outlet-id").val()});

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
        });

        function getOpeningStockValue(val) {
            $.ajax({
                type:'POST',
                url:"{{route('admin.stock.get-opening-stock-price')}}",
                data:val,
                success:function(response) {
                    console.log(response);
                    $.each(response, function(k, v) {
                        $(`#opening-stock-${k}`).html(v);
                    });
                }
            });
        }

        $(document).on('click', '#check-click', function () {
            getOpeningStockValue({_token:'{{csrf_token()}}', product_id:$('#profuct-id').val(), outlet_id:$("#outlet-id").val()});
        })
    </script>
@endsection
