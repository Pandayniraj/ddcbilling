@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')

@endsection

{{-- <link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" /> --}}

@section('content')

<link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" />


<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
    <h1>
      {{ $page_title ?? 'Page Title' }}
        <small>{{$page_description ?? 'Page Description'}}</small>
    </h1>
   
    {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
</section>

 <div class='row'>
   <div class='col-md-12'>
      <div class="box">
         <div class="box-body">
        <form method="post" action="{{route('admin.deliveryroute.create')}}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">

                <div class="col-md-4 col-sm-12 form-group">
                    <label class="control-label">Distributor</label>
                    <div class="input-group">
                        <Select name="distributor_id" class="form-control searchable input-sm">
                            <option value="" disable selected>Select Any One</option>
                            @foreach($distributor as $key=>$distributor)
                            <option value="{{$key}}">{{ $distributor}}</option>   
                            @endforeach
                            </Select>
                            
                      <div class="input-group-addon">
                            <a href="#"><i class="fa  fa-sort-numeric-asc"></i></a>
                        </div>
                    </div>
                </div>

                 <div class="col-md-4 col-sm-12 form-group">
                    <label class="control-label">Route Name</label>
                    <div class="input-group">
                      <input type="text" name="route_name" class="form-control  input-sm" placeholder="Route Name">
                      <div class="input-group-addon">
                            <a href="#"><i class="fa  fa-sort-amount-desc"></i></a>
                        </div>
                    </div>
                </div>

                 <div class="col-md-4 col-sm-12 form-group">
                     <label class="control-label">Route Code
                     </label>
                    <div class="input-group">
                      <input type="text" name="route_code" class="form-control  input-sm" placeholder="Route Code">
                      <div class="input-group-addon">
                            <a href="#"><i class="fa  fa-calendar"></i></a>
                        </div>
                    </div>
                </div>

            </div>

         <div class="row">
                    <div class="col-md-12 ">

                        <div class="form-group pull-right">
                            {!! Form::submit( trans('admin/ticket/general.button.create'), ['class' => 'btn btn-primary', 'id' => 'btn-submit-edit'] ) !!}
                            <a href="/admin/deliveryroute/" title="{{ trans('general.button.cancel') }}" class='btn btn-default'>{{ trans('general.button.cancel') }}</a>
                        </div>
                        </div>
                 </div>

    </form>


         </div>
        </div>
    </div>
</div>


<div id="morefiles" style="display: none">
   <table class="table">
      <tbody id="more-custom-tr">
         <tr>
            <td class="moreattachment" style=""> 
               <input type="file" name="attachment[]" class="attachment" >
            </td>
            <td class="w-25" >
               <img src=""  style="max-height: 100px;float: right;margin-left: 30px" class='uploads'>
            </td>
            <td >
               <a href="javascript:void()" style="font-size: 20px; float: right" class="remove-this-attachment"> <i class="fa fa-close deletable"></i></a>
            </td>
         </tr>
      </tbody>
   </table>
</div>

<script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>
<script type="text/javascript">

  
$('#more-button').click(function(){
       $(".multipleDiv-attachment").after($('#morefiles #more-custom-tr').html());
});
$('.datepicker').datetimepicker({
                    //inline: true,
                    //format: 'YYYY-MM-DD',
                    format: 'YYYY-MM-DD', 
                    sideBySide: true,
                    allowInputToggle: true
                });
    $(document).on('click','.remove-this-attachment',function(){
      $(this).parent().parent().remove();
    });

    const validImageTypes = ['image/gif', 'image/jpeg', 'image/png'];
$(document).on('change','.attachment',function(){
  var input = this;
  // console.log('done');
   var parent = $(this).parent().parent();
      if (input.files && input.files[0]) {
        var fileType = input.files[0]['type'];
        var reader = new FileReader();
        reader.onload = function (e) {
          if (validImageTypes.includes(fileType)) {
            parent.find('.uploads')
                .attr('src', e.target.result)
                .width(150)
                .height(200);
            }
          else{
             parent.find('.uploads')
                .attr('src','')
                .width(0)
                .height(0);
          }
       
        };

        reader.readAsDataURL(input.files[0]);
    }
});

$('.searchable').select2()
</script>
@endsection

