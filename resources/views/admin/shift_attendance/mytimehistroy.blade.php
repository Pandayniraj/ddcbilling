@extends('layouts.master')
@section('content')

<style>
    .panel-custom .panel-heading {
        margin-bottom: 10px;
    }
</style>
<link href="{{ asset("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.css") }}" rel="stylesheet" type="text/css" />
<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
               Attendance Adjustment
                <small>{!! $page_description ?? "Page description" !!}</small>
            </h1>
            <p> Fetch the time history. Select the month and send the adjust request from action column</p>


            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-custom" data-collapsed="0">
            
            <div class="panel-body">
                <form id="attendance-form" role="form" enctype="multipart/form-data" action="{{route('admin.mytime.history.post')}}" method="Get" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label for="user_id" class="col-sm-3 control-label">Employee</label>
                        <div class="col-sm-5">
                        @if(auth::user()->hasRole('admins','hr-manager'))
                        <select id="filter-user" class="form-control select2"
                            style="width:150px; display:inline-block;" name="user_id">
                            @foreach($users as $usr)
                             <option value="{{$usr->id}}" @if(\Request::get('user_id') == $usr->id) selected="" @elseif(auth()->user()->id == $usr->id) selected = "" @endif >{{$usr->username}}</option>
                            @endforeach
                        </select>
                        @else    
                         <strong>{{ucfirst(\Auth::user()->username)}}</strong>
                        @endif 
                       </div>


                    </div>

                    <div class="form-group">
                        <label for="date_in" class="col-sm-3 control-label">Month<span class="required"> *</span></label>
                        <div class="col-sm-2" style="margin-right;">
                            <div class="input-group">
                                <input required="" type="text" class="form-control date_in" value="{{ isset($date_in) ? $date_in : '' }}" name="date_in" id="date_in">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                         <div class="col-sm-2 " style="float:left;">
                              <button type="submit" id="sbtn" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($allReport)



<div id="EmpprintReport">
    <div class="row">
        <div class="col-sm-12 std_print">
            <div class="panel panel-custom">
                
                <div class="table-responsive">
                @foreach($allReport as $key => $shifts )

                <?php

                        $thisshiftDates = $shifts['data_by_date'];

                        $thisshiftbreak = $shifts['breakduration']['breakInfo'];

                    ?>
                    <table class="table">
                        <thead>
                             <tr class="bg-default" >
                               <th >Shift Name: {{$shifts['shift_name']}} </th>
                               <th style="text-align: right;">Start:
                                {{ date('h:i A',strtotime($shifts['shift']->shift_time)) }}</th>
                               <th style="text-align: right;">End:
                                {{ date('h:i A',strtotime($shifts['shift']->end_time)) }}</th>
                           </tr>
                        </thead>
                    </table>


                <table class="table table-bordered std_table">
                   <thead>

                       <tr class="bg-info">

                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Late By </th>
                            <th>Early By </th>
                           @foreach($thisshiftbreak as $tb=>$brks)

                               @for($i= 0; $i<2;$i++)
                                    <th class="bg-default">
                                    {{ $brks['name'] }} {{ $i ==0 ? 'In':'Out' }} <i class="{{ $brks['icon'] }}"></i><br>
                                    {{ date('h:i A',strtotime($brks[ $i ==0 ? 'start':'end'])) }}
                                    </th>
                                @endfor
                            @endforeach

                            <th>Clock Out </th>
                            <th>Break Taken </th>
                            <th>Working Hours </th>
                            <th title="Overtime Hours">OT Hours</th>
                            <th>Action</th>

                       </tr>
                   </thead>
                    <tbody>


                    @foreach($days as $dk=>$day)
                     <?php
                         foreach ($thisshiftDates as $ts => $records ){
                             $value = $records['data'];
                             $thisdate = $records['date'];
                         }
                     $attendanceid1 = \App\Models\ShiftAttendance::where('user_id', auth()->user()->id)
                         ->where('shift_id',$value['shift']->id)
                         ->where('date', $day)->where('attendance_status',2)->first();

                     $attendanceid = \App\Models\ShiftAttendance::where('user_id', auth()->user()->id)
                         ->where('shift_id',$value['shift']->id)
                         ->where('date', $day)->where('attendance_status',1)->first()->id;
                     // $shift_clock_in = \App\Models\Shift::where('id',$value['shift']->id)->first();
                    ?>
                       <tr  @if($value['earlyby'])class="bg-danger"
                            @elseif($value['lateby']) class="bg-danger" @endif>
                           <td>{{$day}}</td>


                           <td style="white-space: nowrap;">  @foreach($thisshiftDates as $ts => $records) @if ($day==$records['date'])
                                     @if($records['data']['clockin'])
                                   <a href="javascript::void()" onclick="openattendanceChange(this)" data-type="edit" data-id="{{$attendanceid}}" data-value="{{date('Y-m-d H:i A',strtotime($records['data']['clockin']))}}">
                                  {{date('H:i A',strtotime($records['data']['clockin']))}}
                                   </a>
                                    @else
                                    <?php 
                                    $shift_clock_in  = date('h:i A',strtotime($shifts['shift']->shift_time));

                                    $time = $records['date'].$shift_clock_in;
                                     ?>
                                     <a href="javascript::void()" onclick="openattendanceChange(this)" data-type="edit" data-id="{{$attendanceid1}}" data-value="{{date('Y-m-d H:i A',strtotime($time))}}">Adjust Deleted</a> 
                                   @endif
                               @endif @endforeach</td>
                            <td style="white-space: nowrap;">

                                @foreach($thisshiftDates as $ts => $records) @if($day==$records['date']) {{ $records['data']['lateby'] ?  TaskHelper::minutesToHours($records['data']['lateby']).' Hrs':'-'  }} @endif @endforeach

                            </td>
                            <td style="white-space: nowrap;">
                                @foreach($thisshiftDates as $ts => $records) @if($day==$records['date']) {{ $records['data']['earlyby'] ?  TaskHelper::minutesToHours($records['data']['earlyby']).' Hrs':'-'  }} @endif @endforeach
                            </td>



                           <?php
                             $timeDifference = $value['summary']['timeDifference'];
                             $breakTaken = [];
                             foreach ($timeDifference as $_td => $_timediff) {
                                    if($_td % 2 != 0){
                                        $breakTaken [] = $_timediff;
                                    }
                                }
                            ?>

                        @if(count($thisshiftbreak) > 0 )
                            @foreach($thisshiftbreak as $tb=>$brks)


                                <td>
                                    @foreach($thisshiftDates as $ts => $records)
                                    @if($day==$records['date'])
                        {{ $breakTaken[$tb]['start'] ? date('h:i A',strtotime($breakTaken[$tb]['start'])):'-' }}
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($thisshiftDates as $ts => $records)
                                    @if($day==$records['date'])
                        {{ $breakTaken[$tb]['end'] ?  date('h:i A',strtotime($breakTaken[$tb]['end'])): '-' }}
                                        @endif
                                    @endforeach
                                </td>

                            @endforeach
                        @else

{{--                            <td>-</td>--}}

                        @endif

                          <td style="white-space: nowrap;">

                              @foreach($thisshiftDates as $ts => $records)
                                  @if($day==$records['date']) <a href="/admin/shiftAttendance-clockout/{{$value['user']->id}}/{{$value['shift']->id}}/{{$day}}?user=self" data-toggle="modal" data-target="#modal_dialog">@if($records['data']['clockout']) {{date('H:i A',strtotime( $records['data']['clockout']))}} @else<i class="fa fa-plus" style="color:blue"></i>@endif</a> @endif
                              @endforeach

                          </td>

                          <td style="white-space: nowrap;">

                              @foreach($thisshiftDates as $ts => $records)
                              @if($day==$records['date']) {{$records['data']['summary'] ? TaskHelper::minutesToHours($records['data']['summary']['breakTime']).' Hrs' : '-' }} @endif
                              @endforeach
                          </td>

                             <td
                                 @foreach($thisshiftDates as $ts => $records)
                                 @if($day==$records['date']) title="{{$records['data']['summary']['message']}}" style="white-space: nowrap;"> {{$records['data']['summary'] ? TaskHelper::minutesToHours($records['data']['summary']['workTime']) .' Hrs': '-' }} @endif
                                 @endforeach
                             </td>

                                <td style="white-space: nowrap;">

                                    @foreach($thisshiftDates as $ts => $records)
                                    @if($day==$records['date'])
                                {{ $records['data']['overTime'] ?  TaskHelper::minutesToHours($records['data']['overTime']).' Hrs':'-'  }}
                                        @endif
                                    @endforeach
                            </td>
                           <td>
                               <?php $dates=[];
                               foreach($thisshiftDates as  $shiftDate){
                                   array_push($dates, $shiftDate['date']);
                               }
                               ?>
                                                                         @if(in_array($day,$dates))
                                                                      <a href="/admin/shiftAttendance/{{$value['user']->id}}/{{$value['shift']->id}}/{{$day}}?user=self" data-toggle="modal" data-target="#modal_dialog" >	<i class="fa fa-edit" style="color:blue"></i></a>
                                                                          @elseif(strtotime(date('Y-m-d')) >= strtotime($day) )
                                                                      <a href="/admin/shiftAttendance/{{$value['user']->id}}/{{$value['shift']->id}}/{{$day}}?user=self" data-toggle="modal" data-target="#modal_dialog" ><i class="fa fa-plus" style="color:blue"></i></a>
                                                                          @endif

                            </td>
                       </tr>

{{--                    @endforeach--}}
                    @endforeach
                 </table>

                 @endforeach

            </div>
        </div>
    </div>
</div>
</div>




<div id="attendaceChange" class="modal" role="dialog fade" style="z-index: 10000000;">
  <div class="modal-dialog">

    <div class="modal-content">

        <div class="modal-body wrap-modal wrap">

        <form class="form-horizontal" action="/admin/update_my_attendance" method="post" >
            {{ csrf_field() }}
            <input type="hidden" name="attendance_id" >
            <input type="hidden" name="user_id" value="{{\Request::get('user_id') ?? auth()->user()->id}}"> 
            <div class="form-group">
            <label class="control-label col-sm-2" for="email">Time:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control fixdatetimepicker"
              placeholder="Enter the time to fix" name="time">
            </div>

          </div>

           <div class="form-group">
            <label class="control-label col-sm-2" for="email">Reason:</label>
            <div class="col-sm-10">
              <textarea type="text" class="form-control"
              placeholder="Enter the reason" name="reason" required=""></textarea>
            </div>

          </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-primary submit-button" >Apply Change</button>
               <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
          </div>
        </form>

        </div>

    </div>

  </div>
</div>






@endif

@endsection
@section('body_bottom')
<link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2.css") }}">
<link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2-bootstrap.css") }}">

  <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2.css") }}">
    <link rel="stylesheet" href="{{ asset("/bower_components/admin-lte/select2/css/select2-bootstrap.css") }}">
    <script src="{{ asset("/bower_components/admin-lte/select2/js/select2.js") }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
@include('partials._date-toggle')
<script type="text/javascript">

       $(function() {

        $('.date_in').datetimepicker({
             format: 'YYYY-MM',
            sideBySide: true
        });

        // $('.date-toggle').nepalidatetoggle()
        $('.select_box').select2({
            theme: 'bootstrap',
        });

        $('.fixdatetimepicker').datetimepicker({
           format: 'YYYY-MM-DD H:m:s',
            sideBySide: true
        });

        $('[data-toggle="tooltip"]').tooltip();
    });


function openattendanceChange(ev){
	 let el = $(ev);
	let _id = el.attr('data-id');
    let _value = el.attr('data-value');

    $("#attendaceChange form input[name='attendance_id']").val(_id);
    $("#attendaceChange form input[name='time']").val(_value);
	$('#attendaceChange').modal('show');
    return 0;

}




</script>


@endsection
