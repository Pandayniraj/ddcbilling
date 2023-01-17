<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
  <head>
    <meta charset="UTF-8">
    <title>{{ env('APP_COMPANY')}} | Attendance Report</title>

    <!-- block from searh engines -->
    <meta name="robots" content="noindex">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Set a meta reference to the CSRF token for use in AJAX request -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Bootstrap 3.3.4 -->
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons 4.7.0 -->
    <link href="{{ asset("/bower_components/admin-lte/font-awesome/css/all.css") }}" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/admin-lte/dist/css/AdminLTE.min.css") }}" rel="stylesheet" type="text/css" />

    <!-- Application CSS-->
    <link href="{{ asset(elixir('css/all.css')) }}" rel="stylesheet" type="text/css" />


  </head>

<body onload="window.print();" cz-shortcut-listen="true" class="skin-blue sidebar-mini">

  <div class='wrapper'>

    @if($attendance)

    <div id="EmpprintReport">
         <?php
            $datetype = \Request::get('type');
            $begin = new DateTime($start_date);
            $end = new DateTime($end_date);
            $end->add(new \DateInterval('P1D'));
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);
            $cal = new \App\Helpers\NepaliCalendar();
            $date_in = implode('.', $date_in);
        ?>
        <div class="row">
            <div class="col-sm-12 std_print">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <address>
                        Company Name: {{ \Auth::user()->organization->organization_name }} <br>
                        Pan Number:   {{ \Auth::user()->organization->tpid }}<br>
                        Address: {{ \Auth::user()->organization->address }} <br>
                        </address>
                        <h3 class="panel-title">
                            <strong>Attendance List of 
                        @if($datetype == 'nepali')
                            {{ ucfirst($department->deptname).':-'.$cal->full_nepali_from_eng_date($start_date).' to '.$cal->full_nepali_from_eng_date($start_date)  }}
                            &nbsp;in B.S 
                        @else
                             {{ ucfirst($department->deptname).':-'.($start_date).' to '.($start_date)  }}
                            &nbsp;in A.D 
                        @endif
                            </strong>
                        </h3>

                    </div>
                   
                    <table id="" class="table table-bordered std_table">
                        <thead>
                        <tr>
                            <th >Name</th>
                            @foreach ($period as $dt) 
                                <?php
                                    $engdate = $dt->format("Y-m-d");
                                    if($datetype == 'nepali'){
                                        $nepdate = $cal->formated_nepali_from_eng_date($engdate);
                                        $d = explode('-', $nepdate);
                                        echo "<th class='std_p'>{$d[0]}</th>";
                                    }else{
                                        echo "<th class='std_p'>{$dt->format('d')}</th>";
                                    }
                                    
                                ?>
                                
                            @endforeach
                        </tr>
                        </thead>
                            <tbody>
                                <?php $flag = 0; ?>
                                @foreach($attendance as $ak => $av)
                                
                                <tr>
                                    <?php $userAtt = \TaskHelper::getUserAttendanceNew($av->user_id, $date_in); ?>
                                
                                    <td >{{ $av->user_name }}</td>
                                @foreach ($period as $dt) 
                                    <?php $data = '<td></td>'; ?>
                                    @if(date('l', strtotime($dt->format("Y-m-d"))) == 'Saturday')
                                        <?php $data = '<th data-toggle="tooltip" data-placement="top" title="Saturday"><span style="padding:2px; 4px"class="col-sm-1">H</span></th>'; ?>
                                    @else
                                        <?php
                                            $holidayFlag = 0;
                                        ?>
                                        @foreach($holidays as $hk => $hv)
                                            @if(strtotime($date_in.'-'.$i) >= strtotime($hv->start_date) && strtotime($dt->format("Y-m-d")) <= strtotime($hv->end_date))
                                                <?php
                                                    $data = '<th data-toggle="tooltip" data-placement="top" title="'.$hv->event_name.'"><span style="padding:2px; 4px" class="label label-info std_p">H</span></th>';
                                                    $holidayFlag++;
                                                    break;
                                                ?>
                                            @endif
                                        @endforeach
                                        @if(!$holidayFlag)
                                     
                                                    <?php
                                                    if($userAtt[$dt->format("Y-m-d")]==null || !$userAtt[$dt->format("Y-m-d")])
                                                        $data='<td><span style="padding:2px; 4px" class="label label-danger std_p">A</span></td>';
                                                    elseif($userAtt[$dt->format("Y-m-d")]->count() > 1)
                                                    $data = '<td><span style="padding:2px; 4px" class="label label-success std_p">P</span></td>';
                                                    elseif($userAtt[$dt->format("Y-m-d")]->count() == 1)
                                                        $data = '<td><span style="padding:2px; 4px" class="label label-warning std_p">P</span></td>';
                                                    else
                                                         $data = '<td><span style="padding:2px; 4px" class="label label-info std_p">null</span></td>';

                                                    ?>
                                        @endif
                                    @endif
                                    <?php echo $data; ?>
                                @endforeach
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

  </div><!-- /.col -->

</body>
