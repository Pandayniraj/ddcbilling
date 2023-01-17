@extends('layouts.master')
@section('content')

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                {{ $page_title ?? "Page Title"}}
                <small> {{ $page_description ?? "Page Description" }}</small>
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
</section>


<div class="box box-primary">
    <div class='row'>
        <div class='col-md-12'>
           <div class="box">
              <div class="box-body ">
                  <form method="get" action="/admin/spareparts" enctype="multipart/form-data">
                      <div class="row">
                            <div class="col-md-4">
                                <label class="control-label">Brand</label>
                            <select name="brand_id" id="brand_id" class="form-control">
                                 <option value="">--SELECT BRAND--</option>
                                 @foreach ($brands as $key=>$brand)
                                     <option value="{{$key}}" @if(\Request::get('brand_id')==$key) selected @endif>{{$brand}}</option>
                                 @endforeach
                            </select>

                            </div>
                             <div class="col-md-4">
                             <label class="control-label">Product</label>
                             <select name="product_id" id="product_id" class="form-control">
                                 <option value="">--SELECT PRODUCT--</option>
                                 @foreach ($products as $id=>$name)
                                     <option value="{{$id}}" @if(\Request::get('product_id')==$id) selected @endif >{{$name}}</option>
                                 @endforeach
                             </select>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::submit( "Filter", ['class' => 'btn btn-primary'] ) !!}
                            <a href="{!! route('admin.spareparts.index') !!}" class='btn btn-default'>Reset</a>
                        </div>
                </div>
             </div>
        </form>
     </div>
 </div>
       <table class="table table-hover table-no-border" id="spareparts-table">
        <div class="pull-right">
            <a href="/admin/spareparts/create" class="btn btn-success">Create</a>

        </div>
		<thead>
		    <tr>
		        <th style="text-align:center;width:20px !important">
		            <a class="btn" href="#" onclick="toggleCheckbox(); return false;" title="{{ trans('general.button.toggle-select') }}">
		                <i class="fa fa-check-square-o"></i>
		            </a>
		        </th>
		        <th>ID</th>
		        <th>Name</th>
		        <th>Brand</th>
		        <th>Product</th>
		        <th>Qty</th>
		        <th>Price</th>
		        <th>Description</th>
		        <th>Action</th>
		    </tr>
		</thead>
		<tbody>
		    @foreach($spareparts as $key=>$value)
{{dd($spareparts)}}
		    <tr>
		        <td>
		            <input type="checkbox" name="event_id" value="{{$value->id}}">
		        </td>
		        <td>{{ $value->id }}</td>
		        <td>{{ $value->name }}</td>
		        <td>{{$value->brands->name}}</td>
		        <td>{{$value->products->name}}</td>
		        <td>{{$value->alert_qty}}</td>
		        <td>{{$value->price}}</td>
		        <td>{{$value->description}}</td>
		        <td>
		            <a href="{{route('admin.spareparts.edit', $value->id)}}"><i class="fa fa-edit"></i></a>
		            <a href="{!! route('admin.spareparts.delete', $value->id) !!}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash-o deletable"></i></a>

		        </td>
		    </tr>

		    @endforeach

      </tbody>
   </table>
   {{$spareparts->appends($_GET)->links()}}
 </div>
</div>


@endsection
