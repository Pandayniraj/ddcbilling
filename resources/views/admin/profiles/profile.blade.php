@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')
@endsection

@section('content')
<style type="text/css">
 .list-group-item {
    font-size: 13px !important;
  }
</style>

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                {{$user->first_name}} {{$user->last_name}}
                <small>            {{ PayrollHelper::getDepartment($user->departments_id) }},
            {{ PayrollHelper::getDesignation($user->designations_id) }}</small>
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>

<section class="content">

      <div class="row">
        <div class="col-md-4">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">


              @if($user->image)
              <img class="profile-user-img img-responsive img-circle" src="/images/profiles/{{$user->image}}" alt="User profile picture" style="margin: 0 auto;width: 100px; border: 3px solid #d2d6de;">
              @else
              <img class="profile-user-img img-responsive img-circle" src="{{ Gravatar::get(Auth::user()->email , 'tiny') }}}" alt="User profile picture" style="margin: 0 auto;width: 100px;">
              @endif


              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Name:</b> <a class="">{{$user->first_name}} {{$user->last_name}}</a>
                </li>

                <li class="list-group-item">
                  <b>Gender:</b> <a class="">{{$user_detail->gender}}</a>
                </li>
                <li class="list-group-item">
                  <b>Birthday:</b> <a class=""> {{ date('F d',strtotime($user->dob)) }}   </a>
                </li>
                <li class="list-group-item">
                  <b>Joined Since:</b> <a class="">

                      @if(strtotime($user_detail->join_date))
                      @php
                      $today = new DateTime( date('Y-m-d') );

                      $join_date = new DateTime($user_detail->join_date);

                      $interval = $join_date->diff($today);

                      echo $interval->format('%y years %m months');

                      @endphp
                      @endif



                  </a>
                </li>
                 <li class="list-group-item">
                  <b>Organization:</b> <a class="">{{ $user->organization->organization_name  }}</a>
                </li>
                <li class="list-group-item">
                  <b>Division:</b> <a class="">
                      {{ $user->department->division->name }}</a>
                </li>
                 <li class="list-group-item">
                  <b>Department:</b> <a class="">{{$user->department->deptname}}</a>
                </li>
                <li class="list-group-item">
                  <b>Designation:</b> <a class="">{{$user->designation->designations}}</a>
                </li>
                 <li class="list-group-item">
                  <b>Level:</b> <a class="">{{$user->designation->designations}}</a>
                </li>
                <li class="list-group-item">
                  <b>Branch:</b> <a class="">{{$user->work_station}}</a>
                </li>
                @php $supervisor = $user->firstLineManger;
                    $secondLine = $user->secondLineManger;


                @endphp
                <li class="list-group-item">
                  <b>First Line Manager:</b> <a class="">{{$supervisor->first_name}} {{$supervisor->last_name}}</a>
                </li>
                <li class="list-group-item">
                  <b>Second Line Manger:</b> <a class="">{{$secondLine->first_name}} {{$secondLine->last_name}}</a>
                </li>

                 <li class="list-group-item">
                  <b>Email:</b> <a href="mailto:{{$user->email}}"class="" title="{{$user->email}}">{{ $user->email }}
                    
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border bg-info">
              <h3 class="box-title">About Me</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <strong><i class="fa fa-book margin-r-5"></i> Education</strong>

              <p class="text-muted">
                {{$user_detail->education}}
              </p>
              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>
              <p class="text-muted">{{$user_detail->present_address}}</p>
              <hr>

              <strong><i class="fa fa-pencil margin-r-5"></i> Skills</strong>

               <?php
                   $skills = explode(',', $user_detail->skills);
                ?>
              <p>
               @foreach($skills as $key => $value)
                <span  @if($key % 2 == 0) class="label label-info" @elseif($key % 3 == 0) class="label label-success" @else class="label label-danger" @endif>{{$value}} </span>&nbsp;
                @endforeach
              </p>

              <hr>

              <strong><i class="fa fa-file-text-o margin-r-5"></i> Bank Info</strong>

              <h6><strong>Name:</strong> {{$user_detail->bank_name}}</h6>
              <h6> <strong>Account Name:</strong>  {{$user_detail->bank_account_name}}</h6>
              <h6><strong>Account No:</strong>  {{$user_detail->bank_account_no}}</h6>
              <h6><strong>Account Branch:</strong>  {{$user_detail->bank_account_branch}}</h6>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-8">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs bg-danger">
             {{--  <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="false">Leads</a></li>
              <li class=""><a href="#timeline" data-toggle="tab" aria-expanded="true">Lead Tasks</a></li> --}}
              <li class="active"><a href="#subordinates" data-toggle="tab">Subordinates</a></li>
              <li ><a href="#settings" data-toggle="tab">Project Tasks</a></li>
              @if(Auth::user()->hasRole(['admins','hr-staff']) || $user->isAuthsupervisor()['supervisor']  )
              <li ><a href="#calandar" data-toggle="tab">Calendar</a></li>
              <li><a href="#leaveDetails" data-toggle='tab'>Leave Application</a></li>
              @endif
            <li ><a href="#asset_info" data-toggle="tab">Assets Information</a></li>

            </ul>
            <div class="tab-content">
            {{--   <div class="tab-pane active" id="activity">
                <!-- Post -->
                <table class="table table-hover table-no-border table-striped" id="leads-table">
                    <thead>
                        <tr>
                            <th>{{ trans('admin/leads/general.columns.id') }}</th>
                            <th>{{ trans('admin/leads/general.columns.name') }}</th>
                            <th>{{ trans('admin/leads/general.columns.course_name') }}</th>
                            <th>{{ trans('admin/leads/general.columns.mob_phone') }}</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>

                            @foreach($leads as $lead)
                            <tr>
                               <td>{{ env('APP_CODE')}}{{ $lead->id }}</td>
                                <td style="float:lef; font-size: 16px">
                                       {{$lead->name}} {{$lead->company->name}}
                                </td>
                                    <td><span class="label label-default">{{ mb_substr($lead->course->name,0,13) }}..</span></td>
                                    <td>{{ $lead->mob_phone }}</td>
                                    <td><span class="label label-success">{{ $lead->communication->name }}
                                        </span>
                                    </td>
                                    <td>{{$lead->status->name}}</td>

                                    @if( $lead->rating == 'active')
                                    <td class="" style="background-color: #4B77BE">{{ $lead->rating }}</td>
                                    @endif
                                    @if( $lead->rating == 'failed')
                                    <td class="" style="background-color: #8F1D21">{{ $lead->rating }}</td>
                                    @endif
                                    @if( $lead->rating == 'acquired')
                                    <td class="" style="background-color: #26A65B">{{ $lead->rating }}</td>
                                    @endif
                                    @if( $lead->rating == 'blacklist')
                                    <td class="" style="background-color: #000000">{{ $lead->rating }}</td>
                                    @endif
                            </tr>
                              @endforeach

                    </tbody>
                </table>



                <!-- /.post -->
              </div> --}}
              <!-- /.tab-pane -->
            {{--   <div class="tab-pane " id="timeline">
                <!-- The timeline -->
                <table class="table table-hover table-no-border" id="leads-table">
                   <thead>
                     <tr><th>Id</th>
                        <th>{{ trans('admin/tasks/general.columns.lead') }}</th>
                        <th>{{ trans('admin/tasks/general.columns.task_subject') }}</th>
                        <th>{{ trans('admin/tasks/general.columns.task_status') }}</th>
                        <th>{{ trans('admin/tasks/general.columns.task_owner') }}</th>
                        <th>{{ trans('admin/tasks/general.columns.assigned_to') }}</th>
                        <th>{{ trans('admin/tasks/general.columns.task_priority') }}</th>
                        <th>{{ trans('admin/tasks/general.columns.task_due_date') }}</th>
                     </tr>
                 </thead>
                 <tbody>
                  @foreach($tasks as $task)
                  <tr>
                    <td>{{env('APP_CODE')}}{{$task->id}}</td>
                    <td class="bg-success">{{ $task->lead->name }}</td>
                    <td ><a href="/admin/tasks/{{$task->id}}">{{ mb_substr($task->task_subject,0,25) }}..</a></td>

                    @if( $task->task_status == 'Started')
                    <td class="" style="background-color: #4B77BE">{{ $task->task_status }}</td>
                    @endif
                    @if( $task->task_status == 'Completed')
                    <td class="" style="background-color: #26A65B">{{ $task->task_status }}</td>
                    @endif
                    @if( $task->task_status == 'Open')
                    <td class="" style="background-color: #8F1D21">{{ $task->task_status }}</td>
                    @endif
                    @if( $task->task_status == 'Processing')
                    <td class="" style="background-color: pink">{{ $task->task_status }}</td>
                    @endif
                    <td class="">
                       {{$task->owner->full_name}}
                    </td>
                    <td><span class="label label-default">{{$task->assigned_to->full_name}}</span></td>
                    <td><span class="label label-success">{{ $task->task_priority }}
                        </span>
                    </td>
                    <td>{!! date('dS M y', strtotime($task->task_due_date)) !!}
                    </td>

                  </tr>
                  @endforeach
                 </tbody>

                </table>

              </div> --}}
              <!-- /.tab-pane -->
              <div class="tab-pane" id="calandar" style="padding: 0px;">



                  <iframe src="/admin/attendace/{{ $user->id }}/calandar?isframe=true" height="960"></iframe>





              </div>


              <div class="tab-pane" id="leaveDetails">
                  <div class="row">
                    <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped DataTables " id="">
                                    <thead>
                                        <tr class="bg-purple">
                                            <th>Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Part</th>
                                            <th>Leave Category</th>
                                            <th>Status</th>
                                            <th class="col-sm-2">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach(\AttendanceHelper::getLeaves($user->id) as $lv)
                                        <tr>
                                            <td>{{ $lv->user->first_name.' '.$lv->user->last_name }}</td>
                                            <td>{{ $lv->leave_start_date }}</td>
                                            <td>{{ $lv->leave_end_date }}</td>
                                            <td>{{ $lv->partOfDay() }}  </td>
                                            <td>{{ $lv->category->leave_category }}</td>
                                            <td><span class="label @if($lv->application_status == 1) label-warning @elseif($lv->application_status == 2) label-success @else label-danger @endif">@if($lv->application_status == 1) Pending @elseif($lv->application_status == 2) Accepted @else Rejected @endif</span></td>
                                            @if($lv->application_status == 1)
                                            <td>
                                                <a href="/admin/leave_management/detail/{{ $lv->leave_application_id }}" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><span class="fa fa-list-alt"></span></a>
                                                <a href="/admin/leave_management_delete/{{ $lv->leave_application_id }}" class="btn btn-danger btn-xs" title="" data-toggle="tooltip" data-placement="top" onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" data-original-title="Delete"><i class="fa fa-trash"></i></a>
                                            </td>
                                            @else
                                            <td>
                                                <a href="javascript:undefined" class="btn btn-info btn-xs disabled" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><span class="fa fa-list-alt"></span></a>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            </div>
                              <div class="col-md-5"> {{--  client wants everything thing in same page no mess :D  --}}
                                 @php
                                 $userIdStatus = $user;
                                 $categories = \App\Models\LeaveCategory::get();
                                 @endphp

                                 @include('admin.leave_mgmt.leave_status')
                              </div>


                            </div>



                        </div>



              <div class="tab-pane " id="settings">

                <table class="table table-hover table-no-border" >
                   <thead>
                     <tr>
                        <th>ID</th>
                        <th>Project</th>
                        <th>Project Task</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                     </tr>
                   </thead>
                   <tbody>
                    @foreach($projects_tasks  as $lead)
                     <tr>
                         <td>{{env('APP_CODE')}}{{ $lead->id }}</td>
                         <td>
                              {{ $lead->project->name }}
                         </td>
                         <td><a href="/admin/project_task/{{$lead->id}}" target="_blank">{{ $lead->subject }}</a></td>

                         @if($lead->status =='new')
                        <td><span class="label label-primary">{{ $lead->status }}</span></td>
                        @elseif($lead->status =='ongoing')
                        <td><span class="label label-info">{{ $lead->status }}</span></td>
                        @else($lead->status =='completed')
                        <td><span class="label label-success">{{ $lead->status }}</span></td>

                        @endif

                        <td>{{ date('dS M y', strtotime($lead->start_date))}}</td>
                        <td>{{ date('dS M y', strtotime($lead->end_date))}}</td>


                     </tr>
                     @endforeach
                   </tbody>
                 </table>





              </div>
                <div class="tab-pane " id="asset_info">

                    <table class="table table-hover table-no-border" >
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Project</th>
                            <th>Stock Category</th>
                            <th>Assigned Quantity</th>
                            <th>Assigned Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($stock_assigns as $ak => $av)
                            <tr>
                                <td>{{ $ak+1 }}</td>
                                <?php $assign_stock = StockHelper::getStock($av->stock_id); ?>
                                <td>{{ $assign_stock->item_name }}</td>
                                <td>{{ $av->project->name }}</td>
                                <td>{{ StockHelper::getCatSubCat($assign_stock->stock_sub_category_id) }}</td>
                                <td>{{ $av->assign_inventory }}</td>
                                <td>{{ date('dS M, Y',strtotime($av->assign_date)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>





                </div>



              <div class="tab-pane active" id="subordinates">
                <table class="table table-hover table-no-border">
                    <thead>
                      <tr>
                      <th>Name</th>
                      <th>Job Title</th>
                      <th>Level</th>
                      <th>Status</th>
                      <th>Join Date</th>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach($sub_ordinates as $key=>$sub_user)
                        <tr>
                          <td class="col-md-3">
                        <div class="media ">

                          <div class="media-left">
                            <a href="/admin/profile/show/{{ $sub_user->id }}">
                              <img class="img-circle img-bordered-sm"
                              src="{{ $sub_user->image?'/images/profiles/'.$sub_user->image:$sub_user->avatar }}" style="height: 28px; width: 28px;margin-top: 5px;" alt="{{ $sub_user->username }}">
                            </a>
                          </div>


                          <div class="media-body media-body-profile-name">
                                <a href="/admin/profile/show/{{ $sub_user->id }}">
                                    <span class="media-heading link-profile-name" title="Sushma  Joshi">
                                     {{$sub_user->first_name}} {{$sub_user->last_name}}
                                    </span>

                                      <span class="department-profile-name text-muted">
                                        <span title="HR"><br>

                                          {{$sub_user->designation->designations}}

                                        </span>


                                      </span>

                                </a>
                          </div>

                        </div>
                          </td>@php $userDetails = $sub_user->userDetail;  @endphp
                          <td>{{$sub_user->designation->designations}}</td>

                          <td>{{$sub_user->designation->designations}}</td>
                          <td> {{ ucfirst($userDetails->employemnt_type) }} </td>
                          <td style="white-space: nowrap;"> {{ $userDetails->join_date }} </td>
                        </tr>


                      @endforeach

                    </tbody>

                </table>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>




@endsection

@section('body_bottom')
    <!-- Select2 js -->
    @include('partials._body_bottom_select2_js_role_search')

    <script type="text/javascript">

      $('#calandar iframe').width($('#subordinates').width() );


    </script>
@endsection
