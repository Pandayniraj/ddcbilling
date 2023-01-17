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
		     	<form method="post" action="/admin/customerdeposits/store?id={{ $id }}" enctype="multipart/form-data">
		     	{{ csrf_field() }}

		     	    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label">Date</label>
                         <input type="date"  name="date" placeholder="Date" id="date" value="" class="form-control " required>
                        </div>
	                   	<div class="col-md-4">
	                   	    <label class="control-label">Deposit amount</label>
                            <input type="number" step="0.1" name="amount" placeholder="Deposit Amount" id="name" value="" class="form-control " required>
	                   	</div>
                      
                        <div class="col-md-4">
                            <label class="control-label">Remarks</label>
                         <input type="text"  name="remarks" placeholder="Remarks" id="remarks" value="" class="form-control ">
                        </div>
                    </div>
		        </div>

                <div class="row">
	                <div class="col-md-12">
	                    <div class="form-group">
	                        {!! Form::submit( trans('general.button.create'), ['class' => 'btn btn-primary', 'id' => 'btn-submit-edit'] ) !!}
	                        <a href="/admin/customerdeposits/index?id={{ $id }}" class='btn btn-default'>{{ trans('general.button.cancel') }}</a>
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
