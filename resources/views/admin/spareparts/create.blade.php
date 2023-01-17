@extends('layouts.master')
@section('content')

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                {{ $page_title ?? "Page Title"}}
                <small> {{ $page_description ?? "Page Description" }}</small>
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
</section>

 <div class='row'>
       <div class='col-md-12'>
          <div class="box">
		     <div class="box-body ">
		     	<form method="post" action="/admin/spareparts/store" enctype="multipart/form-data">
		     	{{ csrf_field() }}

		     	    <div class="row">

	                   	<div class="col-md-4">
	                   	    <label class="control-label">Name</label>
                            <input type="text" name="name" placeholder="Name" id="name" value="" class="form-control " >
	                   	</div>
	                   	<div class="col-md-4">
	                   	    <label class="control-label">Brand</label>
                            <select name="brand_id" id="brand_id" class="form-control">
                                <option value="">--SELECT BRAND--</option>
                                @foreach ($brands as $key=>$brand)
                                    <option value="{{$key}}">{{$brand}}</option>
                                @endforeach
                            </select>

	                   	</div>
	                   	 <div class="col-md-4">
                            <label class="control-label">Product</label>
                            <select name="product_id" id="product_id" class="form-control">
                                <option value="">--SELECT PRODUCT--</option>
                                @foreach ($products as $id=>$name)
                                    <option value="{{$id}}">{{$name}}</option>
                                @endforeach
                            </select>
	                   	</div>
                           <div class="col-md-4">
                            <label class="control-label">Qty</label>
                         <input type="number" name="qty" placeholder="qty" id="qty" value="" class="form-control " >
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">Price</label>
                         <input type="text" name="price" placeholder="price" id="price" value="" class="form-control " >
                        </div>
                           <div class="col-md-4">
                            <label class="control-label">Description</label>
                         <input type="text" name="description" placeholder="Description" id="description" value="" class="form-control " >
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">Remarks</label>
                         <input type="text" name="remarks" placeholder="remarks" id="remarks" value="" class="form-control " >
                        </div>
                    </div>

               </div>
		    </div>

                <div class="row">
	                <div class="col-md-12">
	                    <div class="form-group">
	                        {!! Form::submit( trans('general.button.create'), ['class' => 'btn btn-primary', 'id' => 'btn-submit-edit'] ) !!}
	                        <a href="{!! route('admin.spareparts.index') !!}" class='btn btn-default'>{{ trans('general.button.cancel') }}</a>
	                    </div>
	                 </div>
	            </div>


		     </form>

	</div>
</div>

    <link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
    <script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}" type="text/javascript"></script>
	<script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}" type="text/javascript"></script>


 <script type="text/javascript">
    $(function(){
        $('.datepicker').datetimepicker({
          //inline: true,
          format: 'HH:mm',
          sideBySide: true,
          allowInputToggle: true
        });

      });
</script>



@endsection
