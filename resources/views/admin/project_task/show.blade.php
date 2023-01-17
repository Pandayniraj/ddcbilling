@extends('layouts.dialog')

@section('head_extra')
<!-- Select2 css -->
@include('partials._head_extra_select2_css')

@endsection
<script src="{{ asset ("/bower_components/admin-lte/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<link
rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"> <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>
<style type="text/css">
    body{
        margin-top:20px;
        background: #90d2ff;
    }
    .card-box {
        padding: 10px;
        border-radius: 3px;
        margin-bottom: 30px;


    }
    .card-box.card2 {
        height: auto;
        background-color: #fff;
    }
    .thumb-sm {
        height: 36px;
        width: 36px;
    }
    .img-thumbnail {
        width: 50%;
    }
    .task-detail .task-dates li {
        width: 50%;
        float: left
    }

    .task-detail .task-tags .bootstrap-tagsinput {
        padding: 0;
        border: none
    }

    .task-detail .assign-team a {
        display: inline-block;
        margin: 5px 5px 5px 0
    }

    .task-detail .files-list .file-box {
        display: inline-block;
        vertical-align: middle;
        width: 80px;
        padding: 2px;
        border-radius: 3px;
        -moz-border-radius: 3px;
        background-clip: padding-box
    }

    .task-detail .files-list .file-box img {
        line-height: 70px
    }

    .task-detail .files-list .file-box p {
        width: 100%;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap
    }

    .add-new-plus {
        height: 32px;
        text-align: center;
        width: 32px;
        display: block;
        line-height: 32px;
        color: #98a6ad;
        font-weight: 700;
        background-color: #e3eaef;
        border-radius: 50%
    }

    .project-sort-item .form-group {
        margin-right: 30px
    }

    .project-sort-item .form-group:last-of-type {
        margin-right: 0
    }

    .project-box {
        position: relative
    }

    .project-box .badge {
        position: absolute;
        right: 20px
    }

    .project-box h4 {
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
        width: 100%;
        overflow: hidden
    }

    .project-box ul li {
        padding-right: 30px
    }

    .project-box .project-members a {
        margin: 0 0 10px -12px;
        display: inline-block;
        border: 3px solid #fff;
        border-radius: 50%;
        -webkit-box-shadow: 0 0 24px 0 rgba(0, 0, 0, .06), 0 1px 0 0 rgba(0, 0, 0, .02);
        box-shadow: 0 0 24px 0 rgba(0, 0, 0, .06), 0 1px 0 0 rgba(0, 0, 0, .02)
    }

    .project-box .project-members a:first-of-type {
        margin-left: 0
    }

    .company-card .company-logo {
        float: left;
        height: 60px;
        width: 60px;
        border-radius: 3px
    }

    .company-card .company-detail {
        margin: 0 0 50px 75px
    }
    
    .text-muted {
        color: #000 !important;
    }

    p {
        line-height: 1.6;
        font-size: 14px;
    }
    .mt-5, .my-5 {
        margin-top: 1px !important;
    }
    .bootstrap-tagsinput .label-info {
        background-color: #02c0ce;
        display: inline-block;
        padding: 4px 8px;
        font-size: 13px;
        margin: 3px 1px;
        border-radius: 3px;
    }
    .img-thumbnail {
        width: 80%;
        border: 1px solid #fff !important;
    }
    .thumbnail {
    max-width: 150px !important;
    height: auto;
    float: left;
    padding: 0px !important;
    margin-bottom: 6px !important;
    width: 130px;
}
    img.img-thum {
        height: 90px !important;
        max-width: 100px;
        max-width: 100% !important;
        width: auto;
    }
    .thumbnail .caption {
        padding: 3px !important;
        color: #333 !important;
        font-size: 14px;
        text-align: center;
    }
    input.btn.btn-light.waves-effect {
        background-color: #3c8dbc;
        color: #fff;
        width: 81px;
        font-weight: bold;
    }
    .align-items-center{
        margin-top: -11px;
    }
    i.fa.fa-paperclip {
        color: #00a65a;
        font-size: 21px;
        margin-left: 15px;
    }
    /*.task-detail {
        height: 900px !important;
    }*/
    h5.m-b-5 {
        font-weight: bold;
    }
    h5.attac {
        font-weight: bold;
    }
    h4.m-b-20 {
        text-transform: uppercase;
    }
    .files-list {
        position: relative;
        top: 4px;
    }
    .attached-files.mt-4 {
        border-bottom: 1px solid #ddd;
    }
    button.btn.btn-primary {
        height: 27px;
        padding: 3px;
    }
    .label.label-primary {
        width: 108px !important;
    }

    .hett {
        height: auto !important;
        background-color: #fff;

    }
    .editable-click, a.editable-click, a.editable-click:hover {
        text-decoration: none;
        border-bottom: none !important;
    }
    h4.header-title.m-b-30 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 9px;
}
.headN {
    font-size: 15px;
    font-weight: 600;
}
</style>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 hett">
                <div class="card-box task-detail">
                    <div class="media mt-0 m-b-30">
                        <img src="{{ TaskHelper::getProfileImage($task->user_id) }}" class="d-flex mr-3 rounded-circle" width="48px" height="48px">
                        <div class="media-body">
                            <h5 class="media-heading mb-0 mt-0 ">{{ $task->user->first_name}} {{ $task->user->last_name}}</h5>
                            <span class="badge badge-danger">{{$task->status}}</span>
                            @if ( $task->isDeletable() )
                            <a class="btn btn-default  btn-xs" onclick="deletetask({{$task->id}},event)" href="#" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}">{{ trans('admin/projects/general.button.delete') }}</a>
                            @endif

                        </div>
                    </div>
                    <h4 class="m-b-20">
                        <span>#{!! $task->id !!}. 
                                        <span id="edit_task_subject">{!! $task->subject !!}</span> </span>

                    </h4>


                    <p class="text-muted task_description">{!! $task->description !!}</p>

                    <ul class="list-inline task-dates m-b-0 mt-5">
                        <li>
                            <h5 class="m-b-5">Start Date</h5>
                            <p>{{date('d M y',strtotime($task->start_date))}} </p>
                        </li>
                        <li>
                            <h5 class="m-b-5">Due Date</h5>
                            <p>{{date('d M y',strtotime($task->end_date))}}</p>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                    <div class="task-tags mt-4">
                       <!--  <h5 class="">Tags</h5> -->
                       <div class="bootstrap-tagsinput">
                            <!-- <span class="tag label label-info">Amsterdam<span data-role="remove"></span>
                        </span> 
                        <span class="tag label label-info">Washington<span data-role="remove"></span>
                    </span> 
                    <span class="tag label label-info">Sydney<span data-role="remove"></span>
                </span> -->

                <div class="col-md-9 label label-primary" style="width: 90px;font-weight: bold; font-size: 15px;padding: 8px; margin-top: -15px;">
                    <i class="fa fa-spinner" aria-hidden="true"></i>
                    <span id='task_status_update' data-type="select" data-pk="1" data-title="Change Status"></span>
                </div>

            </div>

        </div>
        <div class="assign-team mt-4">
            <br/>
            <h5 class="m-b-5">Assign to</h5>

            <div>

                @foreach($task->taskuser as $k => $v)
                <a target="_blank" href="/admin/profile/show/{{ $v->user_id }}"><img class="rounded-circle thumb-sm" alt="64x64" src="{{ TaskHelper::getProfileImage($v->user_id) }}" title="{{ $v->user_id->username }}"> </a>
                @endforeach

            </div>

        </div>
        <div class="attached-files mt-4">
            <h5 class="attac">Attached Files</h5>

            <div class="row">
                <h5 class="d-flex align-items-center mb-3 col-md-4 "><i class="fa fa-paperclip" aria-hidden="true"></i>
                &nbsp;{{ trans('admin/projects/general.columns.attachment') }}</h5>
                <form class="col-md-8 pull-right" action="{{route('admin.project_task.attachment.upload')}}" method="post" enctype="multipart/form-data" style="display: flex">
                    @csrf
                    <input name="attachment" type="file">
                    <input name="task_id" value="{{$task->id}}" type="hidden">
                    <!-- <input class="btn btn-light waves-effect" type="submit" value="upload" > -->
                    <button type="submit" class="btn btn-primary">Upload <i class="fa fa-cloud-upload" aria-hidden="true"></i></button>
                    

                </form>
            </div>

            <div class="files-list">

                @if($task->attachment != '' && $task->attachment != 'Array')
                <div class="col-md-3 col-sm-3">
                    <div class="">
                        <div class="thumbnail img-responsive img-enlargable">
                            <a href="/task_attachments/{{$task->attachment}}" download="{{$ta->attachment}}">
                                @if(is_array(getimagesize(public_path().'/task_attachments/'.$task->attachment)))
                                <img src="/task_attachments/{{$task->attachment}}" alt="task_img"  class="img-responsive img-thumbnail">
                                @else
                                <span class="mailbox-attachment-icon" style="height: 120px;"><i class="fa fa-file-pdf-o"></i></span>
                                @endif
                                <div class="caption" style="padding: 0 !important;">
                                   <p class="font-13 mb-1 text-muted"><small>{{ substr($ta->attachment,-10) }}..</small></p>
                               </div>
                           </a>
                       </div>
                   </div>
               </div>
               @endif

               @foreach($task_attachments as $key => $ta)

               @if(is_array(getimagesize(public_path().'/task_attachments/'.$ta->attachment)))
               <div class="col-md-3 col-sm-3">
                <div class="">
                    <div class="thumbnail img-responsive img-enlargable">
                        <a href="/task_attachments/{{$ta->attachment}}" download="{{$ta->attachment}}">

                            <img src="/task_attachments/{{$ta->attachment}}" alt="task_img" class="img-responsive img-thumbnail img-thum">
                            <div class="caption" style=" border-top: 1px solid black;">

                                <p class="font-13 mb-1 text-muted"><small>{{ substr($ta->attachment,-10) }}..</small></p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="col-md-3 col-sm-3">
                <div class="">
                    <div class="thumbnail img-responsive img-enlargable">
                        <a href="/task_attachments/{{$ta->attachment}}" download="{{$ta->attachment}}">

                            <span class="mailbox-attachment-icon" style="height: 120px;"><i class="fa fa-file-pdf-o"></i></span>

                            <div class="caption" style="padding: 0 !important;margin-top: -15px; border-top: 1px solid black;">
                                <p class="font-13 mb-1 text-muted"><small>{{ substr($ta->attachment,-10) }}..</small></p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-md-1 col-sm-1">
                <a href="" data-toggle="modal" class="delete-comment" onclick="deleteAttachment({{$ta->id}},event)" data-target="#modal_dialog"  title="Delete"><i style="color: red" class="fa fa-trash deletable"></i></a>    
            </div >


            @endforeach






        </div>

    </div>
</div>
</div>
<!-- end col -->
<div class="col-lg-4">
    <div class="card-box card2">
        <h4 class="header-title m-b-30">Comments</h4>
        <div>


            @foreach($comments as $ck => $cv)
            <div class="media m-b-20">
                <div class="d-flex mr-3">
                    <a href="#">
                        <img src="{{ TaskHelper::getProfileImage($cv->user->id) }}" class="media-object rounded-circle thumb-sm"  style="margin-left: 20px;">
                    </a>
                </div>
                <div class="media-body">
                    <h5 class="mt-0 headN">{{ $cv->user->first_name }} {{ $cv->user->last_name }}</h5>
                    <p class="font-13 text-muted mb-0"> {!! nl2br($cv->comment_text) !!}</p>
                    <span class="direct-chat-timestamp pull-right"> {{ date('dS M y', strtotime($cv->created_at)) }} <a href="" data-toggle="modal" class="delete-comment" onclick="deleteComment({{$cv->id}},event)" data-target="#modal_dialog"  title="Delete"><i style="color: red" class="fa fa-trash deletable"></i></a></span>
                    <br>
                    @if($cv->file)
                    <br /><i class="fa fa-paperclip"></i> File: <a href="/files/{{ $cv->file }}" target="_blank">{{ $cv->file }}</a>
                    @endif
                </div>
            </div>
            @endforeach


            <div class="media m-b-20">
                <div class="d-flex mr-3">
                    <a href="#">
                        <img src="{{ TaskHelper::getProfileImage($task->user_id) }}" class="media-object rounded-circle thumb-sm"></a>
                    </div>
                    <div class="media-body">
                     <form action="/admin/post_comment" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <!-- .img-push is used to add margin to elements next to floating images -->
                        <div class="img-push">
                            <div class="">
                                <textarea type="text" style="width: 60%" class="form-control input-sm" name="comment_text" placeholder="{{ trans('admin/projects/general.placeholder.comment_post') }}"></textarea>
                                <input type="hidden" name="type" value="project_task">
                                <input type="hidden" name="master_id" id="master_id" value="{{$task->id}}">
                                <br/>

                                <input style="display: inline-block;" type="file" name="file_name" id="file_name">
                            </div>
                            <div class="mt-2 text-right">
                                <button type="submit" style="float: left;" name="submit_comment" class="btn btn-success waves-effect waves-light">Save Comment</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- end col -->
</div>
<!-- end row -->
</div>
<!-- container -->
</div>




<link href="/x-editable/bootstrap-editable.css" rel="stylesheet" />
<script src="/x-editable/bootstrap-editable.min.js"></script>

<script>
    const task_id = '{{$task->id}}';
    var isChanged = false;
    const currentPeople = $('#peoplesField').val();
    const task_status = [
    @foreach($project_status as $key => $stat) {
        value: '{{$key}}'
        , text: '{{$stat}}'
    }
    , @endforeach
    ];

    function deleteComment(id,event) {
        let c = confirm('Are you sure you want to delete the comment');
        if (c) {
            $.get('/admin/post_comment/delete/'+id, function(response) {
                console.log("helo");
                if (response.status == 1) {
                    console.log('check');
                    location.reload()
                }
            });
        }
        return false;

    }
    function deleteAttachment(id,event) {
        let c = confirm('Are you sure you want to delete the attachment?');
        if (c) {
            $.get('/admin/task/attachment/delete/'+id, function(response) {
                if (response.status == 1) {
                    location.reload()
                }
            });
        }
        return false;

    }
    $('#task_status_update').editable({
        value: '{{ $task->status }}'
        , source: task_status
        , success: function(response, newValue) {
            var taskId = $('#master_id').val();
            $.post("/admin/ajax_proj_task_status", {
                id: taskId
                , status: newValue
                , _token: $('meta[name="csrf-token"]').attr('content')
            }
            , function(data, status) {
                if (data.status == '1')
                    $("#task_status").after("<span style='color:green; margin-left:15px;' id='status_update'>Status is successfully updated.</span>");
                else
                    $("#task_status").after("<span style='color:red; margin-left:15px;' id='status_update'>Problem in updating status; Please try again.</span>");

                $('#status_update').delay(3000).fadeOut('slow');
                isChanged = true;
                    //alert("Data: " + data + "\nStatus: " + status);
                });
            handleChange(newValue, 'priority');
        }
        , });

    function handleChange(value, type) {
        $.post("/admin/ajaxTaskUpdate", {
            id: task_id
            , update_value: value
            , type: type
            , _token: $('meta[name="csrf-token"]').attr('content')
        }
        , function(data) {
            if (data.status == '1')
                $("#task_status").after("<span style='color:green; margin-left:15px;' id='status_update'>" + type + " is successfully updated.</span>");
            else
                $("#task_status").after("<span style='color:red; margin-left:15px;' id='status_update'>Problem in updating status; Please try again.</span>");

            $('#status_update').delay(3000).fadeOut('slow');
            isChanged = true;
                //alert("Data: " + data + "\nStatus: " + status);
            });
    }

    $('.datepicker_end_date').datepicker({
        dateFormat: 'd M y'
        , sideBySide: true
        , onSelect: function(dateText) {
            handleChange(dateText, 'end_date');
        }
    });

    $('.datepicker_start_date').datepicker({
        dateFormat: 'd M y'
        , sideBySide: true
        , onSelect: function(dateText) {
            handleChange(dateText, 'start_date');
        }
    });
    $('.task_description').editable({
        type: 'textarea'
        , pk: 1
        , url: null
        , placement: 'bottom'
        , title: `Task description`
        , success: function(response, newValue) {
            handleChange(newValue, 'description');
        }
        , })
    $('#edit_task_subject').editable({
        type: 'textarea'
        , placement: 'bottom'
        , success: function(response, newValue) {
            handleChange(newValue, 'subject');
        }
    })
    $('.progress').editable({
        success: function(response, newValue) {
            handleChange(newValue, 'percent_complete');
        }
        , validate: function(value) {
            if ($.isNumeric(value) == '') {
                return 'Only Numberical value is allowed';
            } else if (Number(value) > 100 || Number(value) < 0) {
                return 'Task Progress Can Be Between 0 to 100';
            }
        }
    });
    $('.taskdays').editable({
        success: function(response, newValue) {
            handleChange(newValue, 'duration');
        }
    });
    // $('#taskpriority').change(function(){
    //   handleChange($(this).val(),'priority');
    // });

    $('#taskpriority').editable({
        value: '{{$task->priority}}'
        , source: [{
            value: 'Low'
            , text: 'Low'
        }
        , {
            value: 'Medium'
            , text: 'Medium'
        }
        , {
            value: 'High'
            , text: 'High'
        }
        , {
            value: 'Urgent'
            , text: 'Urgent'
        }
        , {
            value: 'None'
            , text: 'None'
        }
        ]
        , success: function(response, newValue) {
            handleChange(newValue, 'priority');
        }
        , });



    $('#category_id').editable({
        value: '{{$task->category_id}}'
        , source: <?php echo json_encode($cat) ?>,
        success: function(response, newValue) {
          location.reload();
          handleChange(newValue,'category');
      },
  });

    $('#sub_cat').editable({
        value: '{{$task->sub_cat_id}}',
        source: <?php echo json_encode($sub_cat) ?>,
        success: function(response, newValue) {
            handleChange(newValue,'sub_cat_id');
        },
    });
    $('#project_id').editable({
        value: '{{$task->project_id}}',
        source: <?php echo json_encode($projects) ?>,
        success: function(response, newValue) {
            let c = confirm("Are sure you want to change project");
            if(c)
              handleChange(newValue,'projects');
          else
              return false;
      }
  })

// $('#category_id').change(function(){
//   handleChange($(this).val(),'category');
// })
$('.searchable').select2({ dropdownAutoWidth: true });
jQuery("#peoples").tagit({
  singleField: true,
  singleFieldNode: $('#peoplesField'),
  allowSpaces: true,
  minLength: 2,
  tagLimit: 5,
  placeholderText: 'Enter User Name',
  allowNewTags: false,
  requireAutocomplete: true,

  removeConfirmation: true,
  tagSource: function( request, response ) {
          //console.log("1");
          $.ajax({
              url: "/admin/getUserTagsJson",
              data: { term:request.term },
              dataType: "json",
              success: function( data ) {
                  response( $.map( data, function( item ) {
                    console.log(item)
                    return {
                      label: item.username ,
                      value: item.value,
                      icon: item.icons
                  }
              }));
              }
          });
      },
      onTagAdded:function(){
        tagitcss();
    }
});
$( window ).unload(function() {
   var addedpeoples = $('#peoplesField').val();
   let a = addedpeoples.split(',');
   let p= currentPeople.split(',');
   var union = [...new Set([...a, ...p])];
   if((union.length > p.length) || (a.length < p.length )){
    window.opener.HandlePeopleChanges(addedpeoples,task_id,isChanged);
}else{
    window.opener.UpdateChanges(isChanged);
}
});
function tagitcss(){
  setTimeout(function(){
    $('.tagit .tagit-choice').attr('style','background-color:   #367FA9 !important')
    $('.tagit-label,.text-icon').attr('style','color: white !important');
},200)

}
monkeyPatchAutocomplete();

function monkeyPatchAutocomplete()
{
    $.ui.autocomplete.prototype._renderItem = function(ul, item) {
        var regexp = new RegExp(this.term);
        var highlightedVal = item.label.replace(regexp, "<span style='font-weight:bold;color:Blue;'>" + this.term + "</span>");
        return $("<li'></li>")
        .data("item.autocomplete", item)
        .append("<a><img class='autocompleteUserAvatar' src='" + item.icon + "' />" + highlightedVal + "</a>")
        .appendTo(ul);
    };
}


function deletetask(id,event) {
    let c = confirm('Are you sure you want to delete the attachment?');
    if (c) {
       $.get('/admin/project_task/{{$task->id}}/delete', function(response) {
        if (response.status == 1) {
            isChanged = true;
            window.opener.UpdateChanges(isChanged);
            window.close();
        }
    });
   }
   return false;

}

function addenlargeclass() {
    $('img[data-enlargable]').addClass('img-enlargable').click(function() {
        var src = $(this).attr('src');
        var modal;

        function removeModal() {
            modal.remove();
            $('body').off('keyup.modal-close');
        }
        modal = $('<div>').css({
            background: 'RGBA(0,0,0,.5) url(' + src + ') no-repeat center'
            , backgroundSize: 'contain'
            , width: '100%'
            , height: '100%'
            , position: 'fixed'
            , zIndex: '10000'
            , top: '0'
            , left: '0'
            , cursor: 'zoom-out'
        }).click(function() {
            removeModal();
        }).appendTo('body');
            //handling ESC
            $('body').on('keyup.modal-close', function(e) {
                if (e.key === 'Escape') {
                    removeModal();
                }
            });
        });
}

</script>