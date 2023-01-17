@extends('layouts.master')

@section('head_extra')
    <!-- jVectorMap 1.2.2 -->
    <link href="{{ asset("/bower_components/admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.css") }}" rel="stylesheet" type="text/css" />

       <style type="text/css">
                      [data-letters]:before {
                            content:attr(data-letters);
                            display:inline-block;
                            font-size:1em;
                            width:3.5em;
                            height:3.5em;
                            line-height:2.5em;
                            text-align:center;
                            background:#f47c11;
                            vertical-align:middle;
                            margin-right:0.1em;
                            color:white;
                            }
            .leave_tr:hover{
                cursor: pointer;
                background-color: #ECF0F5;
            }
.popover-content{
  padding: 0;}


      </style>
@endsection

@section('content')

<section class="content-header back-image" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                Human Resource Development Board
                <small>{!! $page_description ?? "Page description" !!}</small>
            </h1>
             <p> A Customized HRDM Board</p>

           <!-- {{ TaskHelper::topSubMenu('topsubmenu.hr')}} -->

            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>
<!--contents here -->

<div class="row">

<div class="col-lg-3 col-xs-4">
          <!-- small box -->
          <div class="small-box bg-primary">
            <div class="inner">
              <h4>Events</h4>

              <p>New Events</p>
            </div>
            <div class="icon" style="margin-top: 12px;">
              <i class="fa fa-calendar"></i>
            </div>
            <a href="/admin/events" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-4">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h4>Learn</h4>

              <p>Knowledge Base</p>
            </div>
            <div class="icon" style="margin-top: 12px;">
              <i class="fa fa-book"></i>
            </div>
            <a href="/admin/knowledge" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>


        <div class="col-lg-3 col-xs-4">
          <!-- small box -->
          <div class="small-box bg-maroon">
            <div class="inner">
              <h4>Recruit</h4>

              <p>New Vacancy</p>
            </div>
            <div class="icon" style="margin-top: 12px;">
              <i class="fa fa-users"></i>
            </div>
            <a href="/admin/job_posted" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-4">
          <!-- small box -->
          <div class="small-box bg-olive">
            <div class="inner">
              <h4>Start</h4>

              <p>On-boarding</p>
            </div>
            <div class="icon" style="margin-top: 12px;">
              <i class="fa fa-plane"></i>
            </div>
            <a href="/admin/onboard/task" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-4">
          <!-- small box -->
          <div class="small-box bg-teal">
            <div class="inner">
              <h4>Project</h4>

              <p>New Tasks</p>
            </div>
            <div class="icon">
              <i class="fa fa-bars"></i>
            </div>
            <a href="/admin/projects" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>


        <div class="col-lg-3 col-xs-4">
          <!-- small box -->
          <div class="small-box bg-orange">
            <div class="inner">
              <h4 class="box-title">Payroll</h4>

              <p>Manage</p>
            </div>
            <div class="icon">
              <i class="fa fa-money"></i>
            </div>
            <a href="/admin/payroll/run/step1" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-4" style="padding-bottom: 5px">
          <!-- small box -->
          <div class="small-box bg-orange">
            <div class="inner">
              <h4 class="box-title">Attendance</h4>

              <p>Check</p>
            </div>
            <div class="icon" style="margin-top: 12px;">
              <i class="fa fa-clock-o"></i>
            </div>
            <a href="/admin/attendanceReports" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-4">
          <!-- small box -->
          <div class="small-box bg-maroon">
            <div class="inner">
              <h4 class="box-title">Roaming</h4>

              <p>Track visit history</p>
            </div>
            <div class="icon" style="margin-top: 12px;">
              <i class="fa fa-map-marker"></i>
            </div>
            <a href="/admin/geolocations" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>









</div>

 <div class='row'>

<div class="col-md-3">
         <div class="box-widget">
             <div class="box-footer">
     <div style="font-weight:bold; font-size: 16px; padding-bottom: 15px; border-bottom: 1px lightgray solid">
                     <span>Attendance</span>
                 </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="chart-responsive">
                    <canvas id="pieChart" height="90" width="164" style="width: 190px; height: 90px;"></canvas>
                  </div>
                  <!-- ./chart-responsive -->
                </div></div>

              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer no-padding">
              <ul class="nav nav-pills nav-stacked">
                <li><a href="javascript::void()" data-toggle="popover" title="Present Employee" data-type='presentUser' data-html="true" >Present
                  <span class="pull-right ">{{ count($present_user) }}</span></a></li>
                <li><a href="javascript::void()"  data-toggle="popover" title="Absent Employee"  data-type='absentUser' data-html="true" >Absent <span class="pull-right">{{ count($absent_user) }}</span></a>
                </li>
                <li><a href="javascript::void()" data-type='leaveUser'  data-toggle="popover" title="Leave Employee"   data-html="true" >Leave
                  <span class="pull-right "> {{ count($on_leave) }}</span></a></li>
              </ul>
            </div>
            <!-- /.footer -->
          </div>
    @if($attendance_request)
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-flash"></i> Time Change Request</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <ul class="products-list product-list-in-box">

                    @foreach($attendance_request as $act)
                        <?php $user = TaskHelper::getUser($act->user_id); ?>
                        <li class="item">
                            <div class="product-img">
                                <img class="direct-chat-img" src="{{ \TaskHelper::getProfileImage($act->user_id) }}" alt="Product Image">
                            </div>
                            <div class="product-info">
                                <a href="/admin/timechange_request_modal/{{ $act->id }}" class="product-title" title="Approve" data-toggle="modal" data-target="#modal_dialog">
                                    {{ $user->first_name." ".$user->last_name }}
                                </a>
                                </span>
                                <span class="product-description">
                     {{ \Carbon\Carbon::createFromTimeStamp(strtotime($act->created_at))->diffForHumans() }}...
                    </span>
                            </div>
                        </li>
                    @endforeach

                </ul>
            </div>
            <!-- /.box-body -->
            <!-- /.box-footer -->
        </div>
    @endif
</div>


<div class="col-md-6">

      <div class="tab-pane @if(isset($page_status) && $page_status == 'add') active @endif" id="my_leave" style="position: relative;">
                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->

                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#recent_leave" data-toggle="tab" aria-expanded="true">Recent Leave</a>
                        </li>
                        <li class="">
                            <a href="#pending_leave" data-toggle="tab" aria-expanded="false">Pending Leave</a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">

                        <div class="tab-pane active" id="recent_leave">
                          <div class="table-responsive">
                              <table class="table table-condensed">
                               <tbody>
                                <tr class="">
                                  <th>Employee</th>
                                  <th>Supervisor</th>
                                  <th>Status</th>

                                </tr>
                                @foreach($allleaves as $al)
                                  <tr  style="font-size: 14px !important;" onclick="window.open('/admin/leave_management/detail/{{ $al->leave_application_id }}')" class="leave_tr">
                                    <td>
                                      <img src="{{ $al->user->image?'/images/profiles/'.$al->user->image:$al->user->avatar }}" alt="User Image" width="25px" height="25px" style="border-radius: 50%;max-width: 100%;height: auto;">
                                      {{$al->user->first_name}} {{$al->user->last_name}}</td>
                                    <td>
                                    @if($al->approve)
                                    <img src="{{ $al->approve->image?'/images/profiles/'.$al->approve->image:$al->approve->avatar }}" alt="User Image" width="25px" height="25px" style="border-radius: 50%;max-width: 100%;height: auto;"> {{ $al->approve->first_name }} {{ $al->approve->last_name }}
                                    @else
                                     --
                                    @endif
                                    </td>
                                    <td data-toggle="tooltip"  data-original-title="{{ \Carbon\Carbon::createFromTimeStamp(strtotime($al->created_at))->diffForHumans() }}" title="{{ \Carbon\Carbon::createFromTimeStamp(strtotime($al->created_at))->diffForHumans() }}">
                                      <span class="label @if($al->application_status == 1) label-warning @elseif($al->application_status == 2) label-success @else label-danger @endif">@if($al->application_status == 1) Pending @elseif($al->application_status == 2) Accepted @else Rejected @endif</span>
                                    </td>

                                  </tr>
                                @endforeach

                              </tbody>
                            </table>
                            <div class="box-footer clearfix">
                           <a href="/admin/allpendingleaves" class="btn btn-sm btn-default btn-flat pull-right">View All </a>
                         </div>
                          </div>
                        </div>

                        <div class="tab-pane" id="pending_leave">
                            <div class="table-responsive">
                              <table class="table table-condensed">
                                  <tbody>
                                    <tr class="bg-info">
                                    <th>Employee</th>
                                    <th>Supervisor</th>
                                    <th style="width: 40px">Date</th>
                                    </tr>
                                  @foreach($pen_leave as $pl)
                                    <tr style="font-size: 14px !important;" onclick="window.open('/admin/leave_management/detail/{{ $pl->leave_application_id }}')" class="leave_tr">
                                    <td><img src="{{ $pl->user->image?\TaskHelper::getProfileImage($pl->user->id):$pl->user->avatar }}" alt="User Image" width="25px" height="25px" style="border-radius: 50%;max-width: 100%;height: auto;">
                                      {{$pl->user->first_name}} {{$pl->user->last_name}}</td>
                                    
                                    <td>
                                    <?php $line_manager = \TaskHelper::getUserData($pl->user->first_line_manager); ?>
                                    
                                    <img src="{{ isset($line_manager->image) &&  $line_manager->image ? \TaskHelper::getProfileImage($line_manager->id) : $line_manager->avatar   ?? ''}}" alt="User Image" width="25px" height="25px" style="border-radius: 50%;max-width: 100%;height: auto;"> {{ $line_manager->first_name ?? '' }} {{ $line_manager->last_name ?? '' }}</td>

                                    <td><small class="" >  {{ \Carbon\Carbon::createFromTimeStamp(strtotime($pl->created_at))->diffForHumans() }}</small></td>
                                  </tr>
                              @endforeach

                              </tbody></table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
  </div>
<div class="col-md-3">
    @if( isset($travel_request) &&  count($travel_request) > 0)
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-plane"></i> Outstation Request</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <ul class="products-list product-list-in-box">

                    @foreach($travel_request as $act)
                        <?php $user = TaskHelper::getUser($act->staff_id); ?>
                        <li class="item">
                            <div class="product-img">
                                <img class="direct-chat-img" src="{{ \TaskHelper::getProfileImage($act->staff_id) }}" alt="Product Image">
                            </div>
                            <div class="product-info">
                                <a href="/admin/travel_request_modal/{{ $act->id }}" class="product-title" title="Outstation Request" data-toggle="modal" data-target="#modal_dialog">
                                    {{ $user->first_name." ".$user->last_name }}
                                </a>
                                </span>
                                <span class="product-description">
                                    {{ \Carbon\Carbon::createFromTimeStamp(strtotime($act->created_at))->diffForHumans() }}.
                                </span>
                            </div>
                        </li>
                    @endforeach

                </ul>
            </div>
            <!-- /.box-body -->
            <!-- /.box-footer -->
        </div>
    @endif

@if(isset($add_attendance) && count($add_attendance) > 0)
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-flash"></i> Attendance Add Request</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <ul class="products-list product-list-in-box">

                    @foreach($add_attendance as $act)
                        <?php $user = TaskHelper::getUser($act->user_id); ?>
                        <li class="item">
                            <div class="product-img">
                                <img src="{{ \TaskHelper::getProfileImage($act->user_id) }}" alt="Product Image">
                            </div>
                            <div class="product-info">
                                <a href="/admin/attendance_request_proxy_modal/{{ $act->id }}" class="product-title" title="{{$act->attendance_status==2?'Clock out': 'Clock in'}}" data-toggle="modal" data-target="#modal_dialog">
                                    {{ $user->first_name." ".$user->last_name }}
                                </a>
                                </span>
                                <span class="product-description">
                     {{ \Carbon\Carbon::createFromTimeStamp(strtotime($act->created_at))->diffForHumans() }}...
                    </span>
                            </div>
                        </li>
                    @endforeach

                </ul>
            </div>
            <!-- /.box-body -->
            <!-- /.box-footer -->
        </div>
    @endif
<div class="box-widget" style="padding-bottom: 10px">
             <div class="box-footer">
     <div style="font-weight:bold; font-size: 16px; padding-bottom: 15px; border-bottom: 1px lightgray solid">
                     <span>Contract Expiring</span>
                 </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                  <ul class="users-list clearfix">
                    @foreach($leaving_employee as $le)
                    <li>
                      <img src="{{ $le->user->image?\TaskHelper::getProfileImage($le->user->id):$le->user->avatar }}" alt="User Image">
                      <a class="users-list-name" href="#"  data-toggle="tooltip" data-html="true"
                      title="{{ $le->user->username }} <br> <em>{{ $le->user->first_name }} {{ $le->user->last_name }}</em>" data-placement="bottom">
                      {{ $le->user->first_name }} {{ $le->user->last_name }}
                      </a>
                      <span class="users-list-date">
                        {{ date('dS M',strtotime($le->contract_end_date)) }}</span>
                    </li>
                    @endforeach
                  </ul>
                  <!-- /.users-list -->
                </div></div>
                <!-- /.box-body -->
                <!-- /.box-footer -->
              </div>

                  <!-- /.box-header -->

              <div class="box-widget">
             <div class="box-footer">
     <div style="font-weight:bold; font-size: 16px; padding-bottom: 15px; border-bottom: 1px lightgray solid">
                     <span>Birthdays</span>
                 </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                  <ul class="users-list clearfix">

                   @foreach($birthdays as $bd)
                    <li title="{{ $bd->first_name }}  {{ $bd->last_name }} {{ $bd->birthdayDate }} ">
                      <a class="users-list-name" href="/admin/users/{{$bd->id}}">
                          <img src="{{ \TaskHelper::getProfileImage($bd->id) }}" alt="User Image" width="50px" height="50px" style="border-radius: 50%;max-width: 100%;height: auto;">

{{--                          {{ $bd->first_name }}--}}
                      </a>
                    </li>
                    @endforeach


                  </ul>
                  <!-- /.users-list -->
                </div></div>

              </div>
</div>

</div>

<div class="row">
    <div class="col-md-3">
             <div class="box-widget" style="padding-bottom: 10px">
             <div class="box-footer">
     <div style="font-weight:bold; font-size: 16px; padding-bottom: 15px; border-bottom: 1px lightgray solid">
                     <span>On Leave</span>
                 </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                  <ul class="users-list clearfix">
                    @foreach($on_leave as $ol)
                    <li>
                      <img src="userlogo.jpg" alt="User Image" width="50px" height="50px" style="border-radius: 50%;max-width: 100%;height: auto;">
                       <a class="users-list-name" href="/admin/users/{{$ol->user->id}}">{{ $ol->user->first_name }}</a>
                    </li>
                    @endforeach
                  </ul>
                  <!-- /.users-list -->
                </div></div>
                <!-- /.box-body -->
                <!-- /.box-footer -->
              </div>
            <!-- Absent Employees -->


        <div class="box-widget">
             <div class="box-footer">
     <div style="font-weight:bold; font-size: 16px; padding-bottom: 15px; border-bottom: 1px lightgray solid">
                     <span>Holidays</span>
                 </div>


            <!-- /.box-header -->
            <div class="box-body">
              <ul class="products-list product-list-in-box">
                @foreach($holidays as $hd)
                  <li class="item">
                    <div class="product-img">
                       <a href="#" data-toggle="modal" data-target="#saveLogo">
                          <small data-letters="{!! date('d M', strtotime($hd->start_date)) !!}"></small>
                       </a>
                    </div>
                    <div class="product-info">
                      <a href="javascript:void(0)" class="">{{ $hd->event_name }}</a>
                      <span class="product-description">
                          {{ \Carbon\Carbon::createFromTimeStamp(strtotime($hd->start_date))->diffForHumans() }}..
                      </span>
                    </div>
                  </li>
                @endforeach

              </ul>
            </div>
            <!-- /.box-body -->
            <!-- /.box-footer -->
          </div></div>

    </div>

    <div class="col-md-6">

    <div class="">

      <div class="box-widget">

                 <div class="box-body">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="chart-responsive">

                          <div id="pieChart4" style="height: 250px"></div>
                        </div>
                        <!-- ./chart-responsive -->
                      </div>

                    </div>
                    <!-- /.row -->
                  </div>


                <!-- /.box-footer -->
            <div class="box-footer no-padding">

            </div>

          </div>

    </div>

    </div>
    <div class="col-md-3">

        <div class="box-widget">
             <div class="box-footer">
     <div style="font-weight:bold; font-size: 16px; padding-bottom: 15px; border-bottom: 1px lightgray solid">
                     <span>Activity Stream</span>
                 </div>
            <!-- /.box-header -->
            <div class="box-body">
              <ul class="products-list product-list-in-box">

                @foreach($activity as $act)
                <li class="item">
                  <img class="direct-chat-img" src="{{ \TaskHelper::getProfileImage($act->user->id) }}" alt="Product Image">
                  <div class="product-info">

                    <a href="javascript:void(0)" class="">{{$act->user->first_name}} {{$act->user->last_name}}
                    </a>
                    <span class="product-description">
                     Requested <span style="text-transform: capitalize;">{{ $act->category->leave_category}}</span>
                    </span>
                    <span class="product-description">
                     {{ \Carbon\Carbon::createFromTimeStamp(strtotime($act->created_at))->diffForHumans() }}..
                    </span>
                  </div>
                </li>
                @endforeach

              </ul>
            </div></div>
            <!-- /.box-body -->
            <!-- /.box-footer -->
          </div>

    </div>

</div>
<div class="row">
      <div class="col-md-6">

      <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa  fa-user"></i> Employee By Gender</h3>

                </div>

                 <div class="box-body">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="chart-responsive">
                         {{--  <canvas id="Z" height="195" width="303" style="width: 243px; height: 156px;"></canvas> --}}
                          <div id="pieChartgender" style="height: 250px"></div>
                        </div>
                        <!-- ./chart-responsive -->
                      </div>

                    </div>
                    <!-- /.row -->
                  </div>
                <!-- /.box-footer -->
          </div>

    </div>
    <div class="col-md-6">

      <div class="box box-warning box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa  fa-money"></i> Salary Invested By department</h3>

                </div>

                 <div class="box-body">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="chart-responsive">
                         {{--  <canvas id="Z" height="195" width="303" style="width: 243px; height: 156px;"></canvas> --}}
                          <div id="pieChartSalary" style="height: 250px"></div>
                        </div>
                        <!-- ./chart-responsive -->
                      </div>

                    </div>
                    <!-- /.row -->
                  </div>
                <!-- /.box-footer -->
          </div>

    </div>
  </div>
<div class="row">




    <div class="col-md-6">
           <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa  fa-random"></i>Employee By Blood Group</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>

         <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                <div class="chart-responsive">

                  <div id="pieChartbloodGroup" style="height: 250px"></div>
                </div>

              </div>

            </div>
            <!-- /.row -->
          </div>


                <!-- /.box-footer -->
            <div class="box-footer no-padding">

            </div>

          </div>

    </div>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa  fa-users"></i> Employee Probation</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-responsive">

                            <div id="pieChartprobation" style="height: 250px"></div>
                        </div>

                    </div>

                </div>
                <!-- /.row -->
            </div>


            <!-- /.box-footer -->
            <div class="box-footer no-padding">

            </div>

        </div>

    </div>

</div>


  <div class="row">

        <div class="col-md-6">
           <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa  fa-line-chart"></i>Employee By age</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>

         <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                <div class="chart-responsive">

                  <div id="pieChartage" style="height: 250px"></div>
                </div>

              </div>

            </div>
            <!-- /.row -->
          </div>


                <!-- /.box-footer -->
            <div class="box-footer no-padding">

            </div>

          </div>

    </div>





  </div>


<div class="row">

    <div class="col-md-6">
            <div class="box box-info">
                  <div class="box-header with-border">
                    <h3 class="box-title">New Vacancy Announcement</h3>

                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table no-margin">
                        <thead>
                            <tr class="bg-info">
                              <th>SL</th>
                              <th>Job Title</th>
                              <th># </th>

                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($circulars))
                        @foreach($circulars as $av)
                          <tr style="font-size: 13px;">
                            <td>JP{{ $av->job_circular_id }}</</a></td>
                            <td>{{ $av->job_title }}</td>
                            <td>{{ $av->vacancy_no }}</td>

                          </tr>
                          @endforeach
                          @endif

                        </tbody>
                      </table>
                    </div>
                    <!-- /.table-responsive -->
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer clearfix">
                    <a href="/admin/job_posted" class="btn btn-sm btn-default btn-flat pull-right">View All </a>
                  </div>
                  <!-- /.box-footer -->
                </div>
    </div>

    <div class="col-md-6">

              <div class="box box-default box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Org. Announcements</h3>

            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                      <tr>
                        <th>SL</th>
                        <th>Title</th>

                      </tr>
                  </thead>
                  <tbody>
                  @if(isset($announcements))
                  @foreach($announcements as $av)
                    <tr style="font-size: 13px;">
                      <td>AN<a>{{ $av->announcements_id }}</</a></td>
                      <td>{{ $av->title }}</td>

                    </tr>
                  @endforeach
                  @endif

                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <a href="/admin/announcements" class="btn btn-sm btn-default btn-flat pull-right">View All </a>
            </div>
            <!-- /.box-footer -->
          </div>
    </div>


</div>

<div id='presentUser' style="display: none;">
  <div style="max-height: 500px; overflow-y: scroll;">
  <table class="table table-striped" >

    <tr>
      <td>Username</td>
      <td>Department</td>
    </tr>
     @foreach($present_user as $key=>$useratt)

     <tr style="font-size: smaller;">
       <td>{{ ++$key }}</td>
       <td>{{ $useratt->first_name }} {{ $useratt->last_name }}</td>
       <td>{{$useratt->department->deptname ?? '-'}}</td>
     </tr>
     @endforeach

  </table>
</div>
</div>

<div id='absentUser' style="display: none;">
  <div style="max-height: 500px; overflow-y: scroll;">
  <table class="table table-striped" >

    <tr>
      <td>S.N</td>
      <td>Username</td>
      <td>Department</td>
    </tr>
     @foreach($absent_user as $key=>$useratt)

     <tr style="font-size: smaller;">
      <td>{{ ++$key }}</td>
       <td>{{ $useratt->first_name }} {{ $useratt->last_name }}</td>
       <td>{{$useratt->department->deptname ?? '-'}}</td>
     </tr>
     @endforeach

  </table>

</div>
</div>


<div id='leaveUser' style="display: none;">
  <div style="max-height: 500px; overflow-y: scroll;">
  <table class="table table-striped" >

    <tr>
      <td>S.N</td>
      <td>Username</td>
      <td>Department</td>
    </tr>
     @foreach($on_leave as $key=>$useratt)
     <?php  $useratt = $useratt->user; ?>
     <tr style="font-size: smaller;">
      <td>{{ ++$key }}</td>
       <td>{{ $useratt->first_name }} {{ $useratt->last_name }}</td>
       <td>{{$useratt->department->deptname ?? '-'}}</td>
     </tr>
     @endforeach

  </table>


</div>
</div>



@endsection




</div>

@section('body_bottom')
    <!-- ChartJS -->
      <link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset ("/bower_components/highcharts/highcharts.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/highcharts/funnel.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/highcharts/highcharts-3d.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/highcharts/exporting.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/highcharts/export-data.js") }}" type="text/javascript"></script>

    <script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}" type="text/javascript"></script>
  <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}" type="text/javascript"></script>

        <script src="{{ asset ("/bower_components/admin-lte/plugins/chartjs/Chart.min.js") }}" type="text/javascript"></script>

     <script type="text/javascript">
    $(document).ready(function(){



        $('[data-toggle="popover"]').each(function(){


        let id = `#${$(this).attr('data-type')}`;
        var popovercont = $(this);
        $(this).attr('data-content',$(id).html());
        // setTimeout(function(){
        //    popovercont.popover({ container: 'body'});

        // },500);

        });

        $('[data-toggle="popover"]').popover({
            container: 'body'
        });


    });
        //-------------
        //- PIE CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
        var pieChart = new Chart(pieChartCanvas);
        var PieData = [
            {
                value: {{ count($absent_user) }},
                color: "#f56954",
                highlight: "#f56954",
                label: "Absent"
            },
            {
                value:  {{ count($present_user) }},
                color: "#00a65a",
                highlight: "#00a65a",
                label: "Present"
            },
            {
                value: {{ count($on_leave) }},
                color: "#f39c12",
                highlight: "#f39c12",
                label: "On Leave"
            }
        ];
        var pieOptions = {
            //Boolean - Whether we should show a stroke on each segment
            segmentShowStroke: true,
            //String - The colour of each segment stroke
            segmentStrokeColor: "#fff",
            //Number - The width of each segment stroke
            segmentStrokeWidth: 1,
            //Number - The percentage of the chart that we cut out of the middle
            percentageInnerCutout: 50, // This is 0 for Pie charts
            //Number - Amount of animation steps
            animationSteps: 100,
            //String - Animation easing effect
            animationEasing: "easeOutBounce",
            //Boolean - Whether we animate the rotation of the Doughnut
            animateRotate: true,
            //Boolean - Whether we animate scaling the Doughnut from the centre
            animateScale: false,
            //Boolean - whether to make the chart responsive to window resizing
            responsive: true,
            // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio: false,
            //String - A legend template
            legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
            //String - A tooltip template
            tooltipTemplate: "<%=value %> <%=label%> users"
        };
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        pieChart.Doughnut(PieData, pieOptions);
        //-----------------
        //- END PIE CHART -
        //-----------------
    </script>

    <script type="text/javascript">
  //Piechart for Communication
    const departmentdata = <?php echo json_encode($user_by_dep_data) ?>;
    const  timesheet_project_chart = <?php echo json_encode($timesheet_project_chart); ?>;
    const  timesheet_project_chart_by_cost = <?php echo json_encode($timesheet_project_chart_by_cost); ?>;
    const male_female_data =<?php echo json_encode($male_female_data); ?>;
    const dep_by_user_salary =<?php echo json_encode($dep_by_user_salary); ?>;
    const probation =<?php echo json_encode($probation); ?>;
    $(function () {
      $('#pieChart4').highcharts({
      chart: {
        type: 'pie',
        options3d: {
          enabled: true,
          alpha: 45,
          beta: 0
        }
      },
      title: {
        text: 'Employee By Department'
      },
      tooltip: {
        pointFormat: '{point.name}: <b>{point.percentage:.1f}%</b> ({point.y})'
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          depth: 35,
          dataLabels: {
            enabled: true,
            format: '{point.name} <br/> <b>{point.percentage:.1f}%</b> ({point.y})'
          }
        }
      },
      series: [{
        type: 'pie',
        name: 'Department',
        data: departmentdata
      }]
    });




if(timesheet_project_chart && timesheet_project_chart.length > 0){

    Highcharts.chart('pieChart2', {
      chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
      },
      title: {
        text: 'TimeSheet Report Based on Working Employee and Project'
      },
      tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
      accessibility: {
        point: {
          valueSuffix: '%'
        }
      },
      tooltip: {
            pointFormat: '{point.name}: <b>{point.percentage:.1f}%</b> ({point.y})'
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: false
          },
          showInLegend: true
        }
      },
       legend: {
       layout: 'vertical',
       align: 'right',
       verticalAlign: 'middle',
       itemMarginTop: 10,
       itemMarginBottom: 10
     },
      series: [{
        name: 'Employee',
        colorByPoint: true,
        data: timesheet_project_chart,
      }]
    });

}


if(timesheet_project_chart_by_cost && timesheet_project_chart_by_cost.length > 0){
    Highcharts.chart('pieChart3', {
    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'pie'
    },
    title: {
      text: 'TimeSheet Report Based on Employee Cost and Project '
    },
    accessibility: {
      point: {
        valueSuffix: '%'
      }
    },
     tooltip: {
          pointFormat: '{point.name}: <b>{point.percentage:.1f}%</b> ,{{ env('APP_CURRENCY') }}. {point.y} '
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
          enabled: false
        },
        depth: 35,
        showInLegend: true
      }
    },
     legend: {
     layout: 'vertical',
     align: 'right',
     verticalAlign: 'middle',
     itemMarginTop: 10,
     itemMarginBottom: 10
   },
    series: [{
      name: 'Cost',
      colorByPoint: true,
      data: timesheet_project_chart_by_cost,
    }]
  });


}


 $('#pieChartgender').highcharts({
      chart: {
        type: 'pie',
        options3d: {
          enabled: true,
          alpha: 45,
          beta: 0
        }
      },
      title: {
        text: 'Employee By Gender'
      },
      tooltip: {
        pointFormat: '{point.name}: <b>{point.percentage:.1f}%</b> ({point.y})'
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          depth: 35,
          dataLabels: {
            enabled: true,
            format: '{point.name} <br/> <b>{point.percentage:.1f}%</b> ({point.y})'
          }
        }
      },
      series: [{
        type: 'pie',
        name: 'Gender',
        data: male_female_data
      }]
    });

  $('#pieChartSalary').highcharts({
      chart: {
        type: 'pie',
        options3d: {
          enabled: true,
          alpha: 45,
          beta: 0
        }
      },
      title: {
        text: 'Salary Invested By Department'
      },
      tooltip: {
           pointFormat: '{point.name}: <b>{point.percentage:.1f}%</b> ,{{ env('APP_CURRENCY') }}.{point.y} '
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          depth: 35,
          dataLabels: {
            enabled: true,
            format: '{point.name} <br/> <b>{point.percentage:.1f}%</b> ,{{ env('APP_CURRENCY') }}.{point.y}'
          }
        }
      },
      series: [{
        type: 'pie',
        name: 'Salary',
        data: dep_by_user_salary
      }]
    });

$('#timesheet_chart_option').change(function(){
    if($(this).val()=='emp')
    {
      $('#pieChart2').show();
      $('#pieChart3').hide();
    }else{
      $('#pieChart2').hide();
      $('#pieChart3').show();
    }
});



 $('#pieChartprobation').highcharts({
      chart: {
        type: 'pie',
        options3d: {
          enabled: true,
          alpha: 45,
          beta: 0
        }
      },
      title: {
        text: 'Employee By Probation'
      },
      tooltip: {
        pointFormat: '{point.name} <br/> <b>{point.percentage:.1f}%</b> ({point.y})'
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          depth: 35,
          dataLabels: {
            enabled: true,
            format: '{point.name} <br/> <b>{point.percentage:.1f}%</b> ({point.y})'
          }
        }
      },
      series: [{
        type: 'pie',
        name: 'probation',
        data:  probation
      }]
    });



 $('#pieChartage').highcharts({
      chart: {
        type: 'pie',
        options3d: {
          enabled: true,
          alpha: 45,
          beta: 0
        }
      },
      title: {
        text: 'Employee By Age'
      },
      tooltip: {
        pointFormat: '{point.name}: <b>{point.percentage:.1f}%</b> ({point.y})'
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          depth: 35,
          dataLabels: {
            enabled: true,
            format: '{point.name} <br/> <b>{point.percentage:.1f}%</b> ({point.y})'
          }
        }
      },
      series: [{
        type: 'pie',
        name: 'probation',
        data:  @php echo json_encode($user_age);  @endphp
      }]
    });


    $('#pieChartbloodGroup').highcharts({
      chart: {
        type: 'column',
        options3d: {
          enabled: true,
          alpha: 45,
          beta: 0
        }
      },
      title: {
        text: 'Employee By blood group'
      },
      tooltip: {
        pointFormat: '{point.name}: <b>{point.percentage:.1f}%</b> ({point.y})'
      },
      plotOptions: {
        column: {
          allowPointSelect: true,
          cursor: 'pointer',
          depth: 35,
          dataLabels: {
            enabled: true,
            format: '{point.name} <br/> <b>{point.percentage:.1f}%</b> ({point.y})'
          }
        }
      },
      series: [{
        type: 'column',
        stacking: 'normal',
        name: 'blood_group',
        data:  @php echo json_encode($blood_group);  @endphp
      }],
      legend: [{
        layout: 'vertical',
        align: 'right',
         verticalAlign: 'top',
         x: -40,
         y: 100,
         floating: true,
         borderWidth: 1,
      }]


    });



});


    </script>




@endsection
