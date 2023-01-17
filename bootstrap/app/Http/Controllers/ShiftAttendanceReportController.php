<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ShiftAttendance;
use App\User;
use AttendanceHelper;
use DB;
use Flash;
use Illuminate\Http\Request;

class ShiftAttendanceReportController extends Controller
{


   

   
    public function timeHistory()
    {
        $page_title = 'Attendance | history';
        $user_id = null;
        $users = User::select('id', 'first_name','last_name')->where('enabled', '1')->get();
        $history = null;

        $shifts = \App\Models\Shift::get();
        //dd($shifts);
        $page_description = 'Filter User By attendance';
        return view('admin.shift_attendance.timehistory', compact('users', 'page_title','shifts','page_description'));
    }

    public function timeHistoryShow(Request $request)
    {
        $page_title = 'Attendance | history';
        $user_id = $request->user_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $shift_id = $request->shift_id;
        $users = User::select('id', 'first_name','last_name')->where('enabled', '1')->get();


        $attendance = ShiftAttendance::where(function ($q) use ($user_id){
            if($user_id){
                return $q->where('user_id', $user_id);
            }
        })->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date)
                        ->where(function($query) use ($shift_id){

                            if($shift_id){

                                return $query->where('shift_id',$shift_id);
                            }
                        })
                        ->get()
                        ->groupBy('user_id');
                        // dd($attendance);
        $shifts = \App\Models\Shift::get();
        $allReport = AttendanceHelper::singleuserAttendanceReportWithShift($attendance);



        // dd($allReport);
        // dd($allReport);
        $thisUser = \App\User::find($user_id);
        $page_description = 'Attendance Report of '.$thisUser->first_name .' '.$thisUser->last_name;
        if (count($allReport) == 0) {
            Flash::warning('This user Does Not have any record on attendance');
        }

        return view('admin.shift_attendance.timehistory', compact('users', 'page_title', 'allReport', 'user_id', 'start_date', 'end_date','shifts','shift_id','page_description'));
    }

    public function attendanceReport()
    {
        $page_title = 'Attendance';
        $page_description = 'Attendance Report';

        $attendance = null;
      
        // $departments = Department::get();
        $shifts = \App\Models\Shift::get();

        $division = \App\Models\Division::get();

        return view('admin.shift_attendance.attendance_report', compact('page_title', 'page_description', 'attendance', 'shifts','division'));
    }

    public function attendanceReportShow(Request $request)
    {
        $requestData = $request->all();
        $page_title = 'Attendance';
        $page_description = 'Attendance Report';
        //dd($request->all());
        $department = $request->department_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $date_in = $start_date.'.'.$end_date;
        $division = $request->division;


        /* $attendance = DB::table('tbl_attendance')
                        ->select('users.first_name as user_name', 'users.id as user_id')
                        ->join('users', 'users.id', '=', 'tbl_attendance.user_id')
                        ->where('users.department', $request->department_id)
                        ->where('tbl_attendance.date_in', '>=', $date_in.'-01')
                        ->where('tbl_attendance.date_in', '<=', $date_in.'-32')
                        ->groupBy('user_id')
                        ->get(); */

        $filterShift = function ($query) use ($request) {
            if ($request->shift_id) {
                $shiftMap = AttendanceHelper::getShiftusers($request->shift_id);

                return $query->whereIn('id', $shiftMap);
            }
        };
        $attendance = DB::table('users')
                        ->select('first_name as user_name', 'id as user_id')
                        // ->where(function ($query) use ($request) {
                        //     if(!\Auth::user()->hasRole('admins')){

                        //     $depaments_list = Department::where('departments_id',\Auth::user()
                        //     ->department_head)->get()->pluck('departments_id')->toArray();

                        //     if(count($depaments_list) == 0 ){
                        //         return $query->where('id',\Auth::user()->id);
                        //     }

                        //     return $query->whereIn('departments_id', $depaments_list);


                        //     }
                        //     if ($request->department_id) {
                        //         return $query->where('departments_id', $request->department_id);
                        //     }
                        // })
                        ->when(!empty($department), function ($q) use ($department) {
                            $q->where('departments_id', $department);
                            })
                        ->when(!empty($division), function ($q) use ($division) {
                            $q->where('division', $division);
                            })

                        ->where('enabled', '1')
                        ->where(function ($query) use ($filterShift) {
                            $filterShift($query);
                        })
                        ->groupBy('user_id')
                        ->get();

        $holidays = \App\Models\Holiday::select('event_name', 'start_date', 'end_date')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->get();
        //dd($holidays);
        $reqdivision = \App\Models\Division::where('name',$request->division)->first();
        $departments = Department::where('division_id',$reqdivision->id)->get();
        $shifts = \App\Models\Shift::get();
        $shift = $request->shift_id;
        $division = \App\Models\Division::get();

        // dd($requestData['department_id']);

        return view('admin.shift_attendance.attendance_report', compact('page_title', 'page_description', 'departments', 'date_in', 'attendance', 'holidays', 'departments', 'start_date', 'end_date', 'shifts', 'shift','requestData','division'));
    }

    private function absPresentExcel($users, $holidays, $lang, $start_date, $end_date)
    {
        $excelReport = [];

        $begin = new \DateTime($start_date);
        $end = new \DateTime($end_date);
        $end->add(new \DateInterval('P1D'));
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        $cal = new \App\Helpers\NepaliCalendar();
        $columns = [];
        foreach ($period as $dt) {
            $date = $dt->format('Y-m-d');
            if ($lang == 'nepali') {
                $dateArr = [
                    'label'=>$cal->formated_nepali_from_eng_date($date),
                    'value'=>$date,
                ];
            } else {
                $dateArr = [
                    'label'=>$date,
                    'value'=>$date,
                ];
            }
            $columns[] = $dateArr;
        }

        $weekends = \Config::get('hrm.weekends');
        foreach ($users as $key=>$av) {
            $report = [];
            $report['emp_id'] = $av->user_id;
            $report['username'] = $av->user_name;
            $userAtt = \AttendanceHelper::getUserAttendanceHistroy($av->user_id, $start_date, $end_date);
            foreach ($columns as $k=>$c) {
                $currentDate = $c['value'];
                $currentDateLabel = $c['label'];

                $checkHoliday = $holidays->where('start_date', '<=', $currentDate)
                                        ->where('end_date', '>=', $currentDate)
                                        ->first();

                $checkLeave = AttendanceHelper::checkUserLeave($av->user_id, $currentDate);

                $checkPresent = count($userAtt->where('date', $currentDate));
                if (in_array(date('l', strtotime($currentDate)), $weekends)) {
                    $report[$currentDateLabel] = 'W';
                } elseif ($checkHoliday) {
                    $report[$currentDateLabel] = 'H';
                } elseif ($checkLeave) {
                    $report[$currentDateLabel] = 'L';
                } elseif ($checkPresent > 0) {
                    if ($checkPresent % 2 == 0) {
                        $report[$currentDateLabel] = 'P';
                    } else {
                        $report[$currentDateLabel] = 'PnoClockOut';
                    }
                } else {
                    $report[$currentDateLabel] = '-';
                }
            }

            $excelReport[] = $report;
        }

        return ['summary'=>$excelReport];
    }

    public function download_report(Request $request, $type)
    {
        $filterShift = function ($query) use ($request) {
            if ($request->shift_id) {
                $shiftMap = AttendanceHelper::getShiftusers($request->shift_id);

                return $query->whereIn('id', $shiftMap);
            }
        };
        $attendance = DB::table('users')
                        ->select('first_name as user_name', 'id as user_id')
                        ->where(function ($query) use ($request) {
                            if ($request->department_id) {
                                return $query->where('departments_id', $request->department_id);
                            }
                        })
                        ->where('enabled', '1')
                        ->where(function ($query) use ($filterShift) {
                            $filterShift($query);
                        })
                        ->where(function ($query) use ($request) {
                            if(!\Auth::user()->hasRole('admins')){

                            $depaments_list = Department::where('departments_id',\Auth::user()
                            ->department_head)->get()->pluck('departments_id')->toArray();

                            if(count($depaments_list) == 0 ){
                                return $query->where('id',\Auth::user()->id);
                            }

                            return $query->whereIn('departments_id', $depaments_list);


                            }

                        })
                        ->groupBy('user_id')
                        ->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $holidays = \App\Models\Holiday::select('event_name', 'start_date', 'end_date')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->get();
        $report = $this->absPresentExcel($attendance, $holidays, $request->lang, $start_date, $end_date);
        $summaryReport = $report['summary'];

        return \Excel::download(new \App\Exports\ExcelExport($summaryReport), "attendance_report_summary_{$start_date}_{$end_date}.{$type}");
    }



    private function createExcelArr($allReport){ // pass return value from singleuserAttendanceReportWithShift function

        $excelReport = [];
        foreach ($allReport as $key => $shiftWise) {
            $shift_name = $shiftWise['shift']->shift_name;
            $attendanceData = $shiftWise['data_by_date'];
           // dd($attendanceData);
            foreach ($attendanceData as $key => $value) {
                $date = $value['date'];
                $data = $value['data'];
                $clockin = strtotime($data['clockin']);
                $clockout = strtotime($data['clockout']);
                $clockInAdjusted = $data['clockinAdjusted'] ? '( Adjusted )': '';
                $clockOutAdjusted = $data['clockoutAdjusted'] ? '( Adjusted )': '';
                $dataArr = [

                    'shift_name'=>$shift_name,
                    'clock_in_date'=>$date,
                    'clock_in_time'=>($clockin ? date('H:i',$clockin) : '' ). $clockInAdjusted,
                    'in_location'=>$in_location,
                    'lateby'=>$data['lateby'] ? \TaskHelper::minutesToHours($data['lateby']) :'',
                    'earlyby'=>$data['earlyby'] ? \TaskHelper::minutesToHours($data['earlyby']) :'',
                    'break_taken'=>$data['breakduration']['formatted'],
                    'clock_out_date'=>$clockout ? date('Y-m-d',strtotime($data['clockout'])) : '' ,
                    'clock_out_time'=>($clockout ? date('H:i',$clockout) : '').$clockOutAdjusted,
                    'out_location'=>$data['out_location'],
                    'overTime'=>$data['overTime'] ? \TaskHelper::minutesToHours($data['overTime']) :'',
                    'remarks'=>$data['summary']['message']
                ];

                $excelReport [] = $dataArr;
            }
        }



        return $excelReport;





    }


    public function downloadAttendaceUserWise(Request $request,$type){
        $user_id = $request->user_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $shift_id = $request->shift_id;
        $attendance = ShiftAttendance::where('user_id', $user_id)
                        ->where('date', '>=', $start_date)
                        ->where('date', '<=', $end_date)
                        ->where(function($query) use ($shift_id){

                            if($shift_id){

                                return $query->where('shift_id',$shift_id);
                            }
                        })
                        ->get()
                        ->groupBy('shift_id');

        $allReport = AttendanceHelper::singleuserAttendanceReportWithShift($attendance);

        $excelReport = $this->createExcelArr($allReport);
        $user = \App\User::find($user_id);
        $user_name = $user->first_name.' '.$user->last_name;
         $excelData = [
            'data'=>$excelReport,
            'companyHeading'=>true,
            'extraHeader'=>[['Employee Name',$user_name]],
            'style'=> [
                5 => [ 'font' => [ 'bold' => true ] ],
            ],
        ];

        return \Excel::download(new \App\Exports\AdvancedExcelExport($excelData), "{$user_name}_attendacte_report.xlsx");
    }



    public function lateArrivalReport(){

        $shifts = \App\Models\Shift::get();

        return view('admin.shift_attendance.late_arrival',compact('shifts'));



    }

    public function getLateInfo($report){
        $allData = $report['result'];
        $reportData = [];
        foreach ($allData as $key => $shiftAttend) {


           $attendData = $shiftAttend['data_by_date'];


           foreach ($attendData as $key => $value) {
            $date = $value['date'];

            $data  = $value['data'];


            foreach ($data as $key => $value) {

                if($value['lateby']){//user id late

                    if($value['lateby'] > env("MAX_LATE",1)){
                        $reportData [] = [
                            'user'=>$value['user']->first_name .' '.$value['user']->last_name,
                            'shift'=>$value['shift']->shift_name,
                            'shift_start' =>  $value['shift']->shift_time,
                            'shift_end' =>  $value['shift']->end_time,

                            'lateby'=>$value['lateby'] ? \TaskHelper::minutesToHours($value['lateby']).'Hrs' :'',
                            'clockin'=>$value['clockin'],
                            'clockout'=>$value['clockout'],
                            'overTime'=>$value['overTime'] ? \TaskHelper::minutesToHours($value['overTime']).'Hrs' :'',
                            'remarks'=>$value['summary']['message'],
                            'isAdjusted'=>$value['clockoutAdjusted'] == 1 ? 'Adjusted': '',
                        ];

                    }
                }
            }


           }
        }
        return $reportData;




    }


    public function lateArrivalReportView(Request $request){

        $start_date = $request->start_date;

        $end_date = $request->end_date;

        $shifts = \App\Models\Shift::get();
        $shift_id = $request->shift_id;
        $isFiltered = true;
        $attendance = ShiftAttendance::where('date', '>=', $start_date)
                    ->where('date', '<=', $end_date)
                    ->where(function($query) use ($shift_id){

                        if($shift_id){

                            return $query->where('shift_id',$shift_id);
                        }
                    })
                    ->get()
                    ->groupBy('shift_id');

        $report = \AttendanceHelper::reportSummary($attendance);

        $lateReport = $this->getLateInfo($report);

        if($request->type == 'excel'){


            $excelData = [
            'data'=>$lateReport,
            'companyHeading'=>true,
            'style'=> [
                4 => [ 'font' => [ 'bold' => true ] ],
                ],
            ];

        return \Excel::download(new \App\Exports\AdvancedExcelExport($excelData), "late_report_{$start_date}_{$end_date}.xlsx");



        }


        //dd($request->all());
        $page_title = 'Admin | Late Arrival';

        return view('admin.shift_attendance.late_arrival',compact('lateReport','shifts','shift_id','start_date','end_date','isFiltered','page_title'));



    }


    public function get_departments_from_division(Request $request)
    {
        $division = \App\Models\Division::where('name',$request->Division)->first();
        $departments = \App\Models\Department::where('division_id',$division->id)->get();

       return response()->json([
            'departments' => $departments,
        ], 200); 
    }


}
