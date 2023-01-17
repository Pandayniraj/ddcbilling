@extends('layouts.master')
@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            Time Change Request <small>TCR


            </small>
        </h1>


        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
    </section>
    <style>
        .required {
            color: red;
        }
        .rounded{
            border-radius: 50%
        }

        .panel-custom .panel-heading {
            border-bottom: 2px solid #1797be;
        }

        .panel-custom .panel-heading {
            margin-bottom: 10px;
        }
    </style>
    <link href="{{ asset("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.css") }}"
          rel="stylesheet" type="text/css"/>

    <div class="row">

        <div class="col-sm-12">
              
            <div class="" data-collapsed="0">

                    <div class="pull-left">
                        
                    </div>

        <div class="pull-right">
        <form style="margin: 16px 37px 0 0;" class="" method="GET" action="{{route('admin.timechange_request')}}">

                <div class="filter form-inline" style="margin:0 30px 0 0;">
                      <strong>Total Count :{{$timechangeCount}}</strong>&nbsp;  
                    @if(\Auth::user()->hasRole(['admins','hr-staff']) || \Auth::user()->isAuthsupervisor())

                    <select id="filter-user" class="form-control select2"
                            style="width:150px; display:inline-block;" name="user">

                        <option value="" selected="selected">Select User</option>
                        @foreach($sub_ordinates as $user)

                        <option  {{request('user')==$user->id ? 'selected' : ''}}  value="{{$user->id}}">{{$user->fullname}}</option>
                        @endforeach


                    </select>&nbsp;&nbsp;
                    @endif
                    <select id="filter-user" class="form-control select2"
                            style="width:150px; display:inline-block;" name="status">
                        <option value="" selected="selected">Select Status</option>

                        <option  {{request('status')==1 ? 'selected' : ''}}  value="1">Pending</option>
                        <option {{request('status')==2 ? 'selected' : ''}}  value="2">Accepted</option>
                        <option  {{request('status')==3 ? 'selected' : ''}} value="3">Rejected</option>

                    </select>&nbsp;&nbsp;


                    <button class="btn btn-primary btn-sm" type="submit" id="btn-submit-filter">
                                    <i class="fa fa-list"></i> Filter
                                </button>
                    <button class="btn btn-danger btn-sm btn-filter-clear" type="reset" value="Reset" >
                                    <i class="fa fa-close"></i> Clear
                                </button>
                </div>
                {{--                    <button class="btn btn-primary btn-sm" id="search" type="submit"><i class="fa fa-search"></i>&nbsp;Search</button>--}}
            </form>
            <br/>
</div>

            </div>

        </div>
    </div>

    <div id="EmpprintReport">
        <div class="row">
            <div class="col-sm-12 std_print">
                <div class="panel panel-custom">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr class="bg-info">
                            <th>Req #</th>
                            
                            <th>Name</th>
                            
                            <th>Applied Time</th>
                            <th>Actual Time</th>
                            <th>Requested Time</th>
                            <th>Diff</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="hidden-print">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($timechange as $hk => $hv)
                            <?php $user = TaskHelper::getUserData($hv->user_id); ?>
                      
                            @if((\Auth::user()->hasRole(['admins','hr-staff'])) || ( \Auth::user()->id ==  \TaskHelper::getUserData($user->second_line_manager)->id) || (\Auth::user()->id ==  \TaskHelper::getUserData($user->first_line_manager)->id) || (\Auth::user()->id == $hv->user_id) )
                      
                            @if($hv->isDeletable())
                            <tr>
                                <td style="width: 70px">TCR{{ $hv->id }}</td>
                                

                                <td style="white-space: normal;line-height: 1.1" title=" 1st Line Manager: {{ \TaskHelper::getUserData($user->first_line_manager)->username}} 2nd Line Manager: {{ \TaskHelper::getUserData($user->second_line_manager)->username}}
                                Work Station: {{$user->work_station}} &#13;&#10;
                                Division: {{$user->division}}
                                    ">
                                   <a href="/admin/profile/show/{{$hv->user_id}}"> 
                                        <img src="/images/profiles/{{$user->image ? $user->image : 'attReq.jpg'}}" class="rounded" style="width: 30px;height: 30px;" alt="User Image">
                                    <span style="margin-left: ;font-size: ;margin-bottom: -20px">{{ $user->first_name." ".$user->last_name }}</span>
                                   </a>
                                <small style="margin-left:">{{ $user->designation->designations }}</small>

                            </td>
                                
                                <td>{{ date('d M y H:i', strtotime($hv->created_at)) }}</td>
                                <td>{{ date('d M y H:i', strtotime($hv->actual_time)) }}</td>
                                <td>{{ date('d M y H:i', strtotime($hv->requested_time)) }}</td>
                                <td><mark>
                                    <?php
                            $datetime1 = new DateTime($hv->actual_time);
                            $datetime2 = new DateTime($hv->requested_time);
                            $interval = $datetime1->diff($datetime2);
                            echo $interval->format('%h')." Hr ".$interval->format('%i')." m";
                                ?></mark>
                                </td>
                                <td>
                                    @if($hv->attendance_status == 1)
                                        <span class="label label-success">Clock In</span>
                                    @elseif($hv->attendance_status == 2)
                                        <span class="label label-danger">Clock Out</span>
                                    @endif
                                </td>
                                <td>
                                    @if($hv->status == 1)
                                        <span class="label label-warning">Pending</span>
                                    @elseif($hv->status == 2)
                                        <span class="label label-success">Accepted</span>
                                    @else
                                        <span class="label label-danger">Rejected</span>
                                    @endif
                                </td>
                                <td class="hidden-print">
                                 <!--    {{$hv->id}} -->
                                   @if((\Auth::user()->id != $hv->user_id && $user->approve_level_1 == 1 && $hv->status == 1  && $hv->is_forwarded !=2 && \Auth::user()->id == \TaskHelper::getUserData($user->first_line_manager)->id) || (\Auth::user()->id == \TaskHelper::getUserData($user->second_line_manager)->id && $hv->status == 1 && $user->approve_level_2 == 1)||(\Auth::user()->hasRole(['admins','hr-staff']) && $hv->status == 1  && $hv->user_id != \Auth::user()->id))
                                    <a href="/admin/timechange_request_modal/{{ $hv->id }}"
                                                            class="btn btn-primary btn-xs" title="Edit"
                                                            data-toggle="modal" data-target="#modal_dialog"><span
                                            class="fa fa-edit"></span></a>
                                    @endif   

                                     <a href="/admin/timechange_view_modal/{{ $hv->id }}"
                                                            class="btn btn-primary btn-xs" title="View"
                                                            data-toggle="modal" data-target="#modal_dialog"><span
                                      class="fa fa-list-alt"></span></a>   
                                   
                                    
                                      
                                 @if((\Auth::user()->id != \TaskHelper::getUserData($user->second_line_manager)->id && !empty(\TaskHelper::getUserData($user->second_line_manager)->id) && \Auth::user()->id == \TaskHelper::getUserData($user->first_line_manager)->id && $hv->is_forwarded != 2 && $user->forward_level_1 == 1 && $hv->status == 1) || ((\Auth::user()->hasRole(['admins','hr-staff'])) && $hv->status == 1 && $hv->is_forwarded != 2  && $hv->user_id != \Auth::user()->id && (!empty(\TaskHelper::getUserData($user->second_line_manager)->id))))
                                      <a href="/admin/timechange_forward_modal/{{ $hv->id }}"
                                                        class="btn btn-primary btn-xs" title="Forward"
                                                        data-toggle="modal" data-target="#modal_dialog"><span
                                        class="fa fa-paper-plane"></span></a> 
                                    @endif



                                  

                                   
                                {{--    @if($hv->status == 2 && !\Auth::user()->hasRole(['admins','hr-staff']))
                                        <i class="fa fa-trash text-muted" title="Delete"></i>
                                    @elseif(\Auth::user()->hasRole(['admins','hr-staff'])) 

                                    <a href="{!! route('admin.timechange_request_modal.delete', $hv->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash-o deletable"></i></a>   
                                        @else
                                    @if( $hv->isDeletable() )
                                        <a href="{!! route('admin.timechange_request_modal.delete', $hv->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash-o deletable"></i></a>
                                    @endif
                                        @endif  --}}



                                    @if((\Auth::user()->hasRole(['admins','hr-staff'])) )
                                         <a href="{!! route('admin.timechange_request_modal.delete', $hv->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash-o deletable"></i></a> 
                                     @elseif($hv->status != 2 && $hv->status != 3 && $hv->user_id == \Auth::user()->id) 
                                        <a href="{!! route('admin.timechange_request_modal.delete', $hv->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash-o deletable"></i></a>
                                     @elseif(($user->delete_level_1 == 1 && $hv->user_id != \Auth::user()->id && $hv->status != 2 && $hv->status != 3 && \TaskHelper::getUserData($user->first_line_manager)->id == \Auth::user()->id)||($user->delete_level_2 == 1 && $hv->user_id != \Auth::user()->id && $hv->status != 2 && \TaskHelper::getUserData($user->second_line_manager)->id == \Auth::user()->id))
                                      <a href="{!! route('admin.timechange_request_modal.delete', $hv->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash-o deletable"></i></a>
                                      @else   
                                        <i class="fa fa-trash text-muted" title="Delete"></i>
                                     @endif   
                                </td>
                            </tr>
                            @endif
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="text-align: center;">{!! $timechange->appends(request()->input())->links() !!} </div>
        </div>
    </div>

    <style type="text/css">
        .datepicker {
            z-index: 1050 !important;
        }

        .bootstrap-timepicker-widget.dropdown-menu.open {
            display: inline-block;
            z-index: 99999 !important;
        }
    </style>
    <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

            </div>
        </div>
    </div>

@endsection


<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet"
          type="text/css"/>
    <script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
    <script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}"
            type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}"
            type="text/javascript"></script>

    <!-- Timepicker -->
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/timepicker.css") }}" rel="stylesheet"
          type="text/css"/>
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/timepicker.js") }}"
            type="text/javascript"></script>

    <!-- SELECT2-->
    <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2.css") }}">
    <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2-bootstrap.css") }}">
    <script src="{{ asset("/bower_components/admin-lte/select2/js/select2.js") }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
        });
        $(function () {
            $('#date_in').datetimepicker({
                format: 'YYYY-MM',
                sideBySide: true
            });

            $('.select_box').select2({
                theme: 'bootstrap',
            });

            $('[data-toggle="tooltip"]').tooltip();
        });

        $(document).on('focusin', '#clockin_edit', function () {
            $(this).timepicker();
        });
        $(document).on('focusin', '#clockout_edit', function () {
            $(this).timepicker({
                // minuteStep: 1,
                // showSeconds: false,
                // showMeridian: false,
                // defaultTime: false
            });
        });


        $(document).on('click', '#request_update', function () {
            $('.reason_error').hide();
            if ($('#reason').val() == '') {
                $("#reason").attr('placeholder', 'Please mention your reason.');
                $("#reason").after("<span style='color:red;' class='reason_error'>Please mention your reason.</span>");
                return false;
            }

            var formData = new FormData();
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("clockin_edit", $('#clockin_edit').val());
            formData.append("clockout_edit", $('#clockout_edit').val());
            formData.append("reason", $('#reason').val());

            $.ajax({
                type: 'POST',
                url: $('#time_update_request').attr('action'),
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success == '1') {
                        $("#reason").after("<span style='color:green;' class='reason_error'>Time udpate request has been sent.</span>");
                        setTimeout(function () {
                            $('#myModal').modal('hide');
                        }, 2000);
                    } else
                        $("#reason").after("<span style='color:red;' class='reason_error'>Problem in time update. Please try again.</span>");
                },
                fail: function (xhr, textStatus, errorThrown) {
                    $("#reason").after("<span style='color:red;' class='reason_error'>Request failed.</span>");
                }
            })


            alert(JSON.stringify(data));
        });

      $(".btn-filter-clear").on("click", function () {
      window.location.href = "{!! url('/') !!}/admin/timechange_request";
    });

    </script>
@endsection
