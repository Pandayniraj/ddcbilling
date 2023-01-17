<div id="multi-delete-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
<div class="modal-header">
    <h4 class="modal-title" id='title-confirm-delete'></h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <p id='message-confirm-delete'></p>
</div>

<div class="modal-footer justify-content-between">
   <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general.button.cancel') }}</button>
    <button type="button" class="btn   btn-danger" id='confirm-delete-button'>Yes Delete All!</button>
   
   <input type="hidden" id="multi-delete-route">
   <input type="hidden" id="delete-form-id">

</div>
</div>
</div>
</div>


<script type="text/javascript">
    


$(document).on('click','#confirm-delete-button',function(){

    let formid = $('input#delete-form-id').val();
    let formroute =  $('input#multi-delete-route').val();
    $('#'+formid).attr('action',formroute);
    $('#'+formid).submit();

});
$.fn.multipleDeleteIndex = function({title,body,route,formid}){

    $(this).click(function(){

        $('#multi-delete-modal').modal('show');
        $('.modal #title-confirm-delete').text(title);
        $('.modal #message-confirm-delete').text(body);
        $('#multi-delete-route').val(route)
        $('#delete-form-id').val(formid);
    });


}


</script>