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
                <input type="hidden" name="stock_id" value="{{$newstock->id}}">
                <table class="table table-bordered">
                    <tr>
                        <th>S.N</th>
                        <th>Product Name</th>
                        <th >Quantity</th>
                        <th >Store Return</th>
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
                            <td>{{$stock->qty}}</td>
                            <td>{{$stock->return_quantity}}</td>
                            <td>{{$stock->remarks}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>


                <div class="form-group">
                    <a href="/admin/addstock/list" class="btn btn-default"> Back</a>
                </div>
            </div>

            </div><!-- /.box-body -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection

@section('body_bottom')
    <!-- form submit -->
    @include('partials._body_bottom_submit_client_edit_form_js')
@endsection
