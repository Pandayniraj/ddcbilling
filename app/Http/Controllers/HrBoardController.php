<?php

namespace App\Http\Controllers;

use App\Models\AttendanceChangeRequestProxy;
use App\Models\TravelRequest;
use Illuminate\Support\Facades\DB;

class HrBoardController extends Controller
{
    /**
     * Create a new dashboard controller instance.
     *
     * @return void
     */
    private $org_id;

    public function __construct()
    {
        parent::__construct();
        // Protect all dashboard routes. Users must be authenticated.
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->org_id = \Auth::user()->org_id;

            return $next($request);
        });
    }

    public function hrBoard()
    {
        $page_title = 'HR DashBoard';

        $page_description = 'Details of Employes Activities';
        $today =  date('Y-m-d');

        $thisYear = date('Y');
        $birthdays = \App\User::select('*',\DB::raw('DATE_FORMAT(dob,"'.$thisYear.'-%m-%d") as birthdayDate '))
                    ->whereMonth("dob",date('m',strtotime($today)))
                    ->orderByRaw('DATE_FORMAT(dob, "%m-%d")')
                    ->get();
        $birthdays = $birthdays->where('birthdayDate','>=',$today)->sortBy('birthdayDate');

        $today = \Carbon\Carbon::today();

        $duration = \Carbon\Carbon::now()->addDays(20);

        $holidays = \App\Models\Holiday::where('start_date', '>=', $today)->limit(3)->get();

        $onedayago = \Carbon\Carbon::now()->subDays(2);
        $activity = \App\Models\LeaveApplication::select('tbl_leave_application.*')
                    ->leftjoin('users', 'tbl_leave_application.user_id', '=', 'users.id')
                    ->where('users.org_id', $this->org_id)
                    ->where('application_date', '<=', $today)
                    ->where('application_date', '>=', $onedayago)
                    ->get();

        $on_leave = \App\Models\LeaveApplication::select('tbl_leave_application.*')
                    ->leftjoin('users', 'tbl_leave_application.user_id', '=', 'users.id')
                    ->where('users.org_id', $this->org_id)
                    ->where('leave_start_date', '<=', $today)
                    ->where('leave_end_date', '>=', $today)
                    ->where('application_status', '2')->get();

        $present_user  = \App\Models\ShiftAttendance::select('users.*')->leftjoin('users', 'shift_attendance.user_id', '=', 'users.id')
                        ->where('users.enabled','1')
                        ->where('users.org_id', $this->org_id)
                        ->where('date',date('Y-m-d'))
                        ->groupBy('users.id')
                        ->get();
        $present_user_list = $present_user->pluck('id')->toArray();

        $absent_user = \App\User::where('enabled', '1')->whereNotIn('id',$present_user_list)->get();


        $pen_leave = \App\Models\LeaveApplication::select('tbl_leave_application.*')
                    ->leftjoin('users', 'tbl_leave_application.user_id', '=', 'users.id')
                    ->where('users.org_id', $this->org_id)
                    ->where('application_status', '1')->get();

        $allleaves = \App\Models\LeaveApplication::select('tbl_leave_application.*')
                    ->leftjoin('users', 'tbl_leave_application.user_id', '=', 'users.id')
                    ->where('users.org_id', $this->org_id)
                    ->orderBy('leave_start_date', 'desc')->get()->take(10);

        $dep_users = \DB::select("SELECT tbl_departments.departments_id, tbl_departments.deptname , COUNT(*) as total  FROM users LEFT JOIN tbl_departments ON users.departments_id = tbl_departments.departments_id WHERE users.departments_id != '0' AND users.enabled = '1' AND users.org_id = '$this->org_id'
            GROUP BY users.departments_id ");
        $user_by_dep_data = [];
        foreach ($dep_users as $key => $value) {
            $data = [
                'name'=>$value->deptname,
                'y'=>$value->total,
            ];
            array_push($user_by_dep_data, $data);
        }

        $today_date = date('Y-m-d');  //2020-03-15
        $date_after_x_days = date('Y-m-d', strtotime('+ 60 days'));

        $leaving_employee = \App\Models\UserDetail::select('user_details.*')
                    ->leftjoin('users', 'user_details.user_id', '=', 'users.id')
                    ->where('users.org_id', $this->org_id)
                    ->where('contract_end_date', '>=', $today_date)
                    ->where('contract_end_date', '<=', $date_after_x_days)
                    ->orderBy('contract_end_date', 'asc')->get();

        function x_week_range($date)
        {
            $ts = strtotime($date);
            $start = (date('w', $ts) == 0) ? $ts : strtotime('last monday', $ts);

            return [date('Y-m-d', $start), date('Y-m-d', strtotime('next sunday', $start))];
        }

        list($start_date, $end_date) = x_week_range($today_date);
        //dd($start_date, $end_date);
        $timesheet_info = [
            'total_project'=>\App\Models\Projects::count(),
            'total_user_involved'=>0,
            'total_active_project'=>0,
            'total_regular_cost' => 0,
            'total_overtime_cost' => 0,
            'total_cost' => 0,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
        ];

        $project_ids = [];
        $timesheet_project_chart = [];
        $timesheet_project_chart_by_cost = [];
        $working_employee = \App\Models\TimeSheet::where('date', '>=', $start_date)->where('date', '<=', $end_date)->groupBy('employee_id')->get();
        foreach ($working_employee as $key => $value) {
            if ($value->employee->project) {
                $project = $value->employee->project;

                $total_time = \TaskHelper::GetTimeDifference($value->time_from, $value->time_to);
                $template = \PayrollHelper::getTimeSheetSalaryDetails($value->employee_id)->template;
                $salary = \PayrollHelper::timeSheetSalaryPerDay($template, $total_time);
                $timesheet_info['total_regular_cost'] += $salary['regular_salary'];
                $total_regular_cost['total_overtime_cost'] += $salary['overtime_salary'];
                $timesheet_info['total_user_involved']++;
                $total = $salary['regular_salary'] + $salary['overtime_salary'];
                $timesheet_info['total_cost'] = $timesheet_info['total_cost'] + $total;

                if (! in_array($project->id, $project_ids)) {
                    $project_ids[] = $value->employee->project->id;
                    $timesheet_project_chart[$project->id] = ['name'=>$project->name, 'y'=>1]; //adds first one employee
                    $timesheet_project_chart_by_cost[$project->id] = ['name'=>$project->name, 'y'=>$total]; //adds first cost
                    $timesheet_info['total_active_project']++;
                } else {
                    $timesheet_project_chart[$project->id]['y']++; //adds another employee
                    $timesheet_project_chart_by_cost[$project->id]['y'] = $timesheet_project_chart_by_cost[$project->id]['y'] + $total; //adds another cost
                }
            }
        }

        $timesheet_project_chart = array_values($timesheet_project_chart);
        $timesheet_project_chart_by_cost = array_values($timesheet_project_chart_by_cost);

        $user_dep = \App\User::where('enabled', '1')->where('org_id', $this->org_id)->where('departments_id', '!=', '0')->get()->groupBy('departments_id');
        $dep_by_user_salary = [];
        foreach ($user_dep as $key => $dep) {
            $salary = 0;
            foreach ($dep as $key => $user) {
                $salary += floatval($this->getEmployeSalary($user->id));
            }
            $data = ['name'=>$dep[0]->department->deptname, 'y'=>$salary];
            array_push($dep_by_user_salary, $data);
        }
        $male_female_count = DB::select("
        SELECT SUM(CASE WHEN user_details.gender = 'Male' THEN 1 ELSE 0 END) as male,
               SUM(CASE WHEN user_details.gender = 'Female' THEN 1 ELSE 0 END) as female
        FROM user_details
        LEFT JOIN users ON users.id = user_details.user_id
        WHERE users.org_id = '$this->org_id'
        ORDER BY user_id
        ");
        $male_female_data = [
            ['name'=>'Male', 'y'=>(int) $male_female_count[0]->male],
            ['name'=>'Female', 'y'=>(int) $male_female_count[0]->female],
        ];

        $probation_count = DB::select("
        SELECT SUM(CASE WHEN user_details.employemnt_type = 'permanent' THEN 1 ELSE 0 END) as permanent,
               SUM(CASE WHEN user_details.employemnt_type = 'contract' THEN 1 ELSE 0 END) as contract,
               SUM(CASE WHEN user_details.employemnt_type = 'probation' THEN 1 ELSE 0 END) as probation,
               SUM(CASE WHEN user_details.employemnt_type = 'part-time' THEN 1 ELSE 0 END) as parttime,
               SUM(CASE WHEN user_details.employemnt_type = 'tempo' THEN 1 ELSE 0 END) as tempo,
               SUM(CASE WHEN user_details.employemnt_type = 'short' THEN 1 ELSE 0 END) as short,
               SUM(CASE WHEN user_details.employemnt_type = 'consult' THEN 1 ELSE 0 END) as consult,
               SUM(CASE WHEN user_details.employemnt_type = 'outsource' THEN 1 ELSE 0 END) as outsource
        FROM user_details
        LEFT JOIN users ON users.id = user_details.user_id
        WHERE users.org_id = '$this->org_id'
        ORDER BY user_id
        ");

        $probation = [
            ['name'=>'permanent', 'y'=>(int) $probation_count[0]->permanent],
            ['name'=>'contract', 'y'=>(int) $probation_count[0]->contract],
            ['name'=>'probation', 'y'=>(int) $probation_count[0]->probation],
            ['name'=>'part-time', 'y'=>(int) $probation_count[0]->parttime],
            ['name'=>'tempo', 'y'=>(int) $probation_count[0]->tempo],
            ['name'=>'short', 'y'=>(int) $probation_count[0]->short],
            ['name'=>'consult', 'y'=>(int) $probation_count[0]->consult],
            ['name'=>'outsource', 'y'=>(int) $probation_count[0]->outsource]
        ];

        $employeeAge = DB::select("
        SELECT SUM(CASE WHEN user_details.employemnt_type = 'permanent' THEN 1 ELSE 0 END) as permanent,
               SUM(CASE WHEN user_details.employemnt_type = 'contract' THEN 1 ELSE 0 END) as contract
        FROM user_details
        LEFT JOIN users ON users.id = user_details.user_id
        WHERE users.org_id = '$this->org_id'
        ORDER BY user_id
        ");;


        $employeeUserDetails = $this->getAllEmployeeUserDetails();


        $user_age = [];
        foreach ($employeeUserDetails['age'] as $key => $value) {

            $data = [
                'name'=>$value['name'],
                'y'=>count($value['y']),
            ];
            array_push($user_age, $data);
        };
            $blood_group_count = DB::select("
        SELECT SUM(CASE WHEN user_details.blood_group = 'A+' THEN 1 ELSE 0 END) as Aplus,
               SUM(CASE WHEN user_details.blood_group = 'A-' THEN 1 ELSE 0 END) as Aminus,
               SUM(CASE WHEN user_details.blood_group = 'B+' THEN 1 ELSE 0 END) as Bplus,
               SUM(CASE WHEN user_details.blood_group = 'B-' THEN 1 ELSE 0 END) as Bminus,
               SUM(CASE WHEN user_details.blood_group = 'O+' THEN 1 ELSE 0 END) as Oplus,
               SUM(CASE WHEN user_details.blood_group = 'O-' THEN 1 ELSE 0 END) as Ominus,
               SUM(CASE WHEN user_details.blood_group = 'AB+' THEN 1 ELSE 0 END) as ABplus,
               SUM(CASE WHEN user_details.blood_group = 'AB-' THEN 1 ELSE 0 END) as ABminus
        FROM user_details
        LEFT JOIN users ON users.id = user_details.user_id
        WHERE users.org_id = '$this->org_id'
        ORDER BY user_id
        ");
        $blood_group = [
            ['name'=>'A+', 'y'=>(int) $blood_group_count[0]->Aplus],
            ['name'=>'A-', 'y'=>(int) $blood_group_count[0]->Aminus],
            ['name'=>'B+', 'y'=>(int) $blood_group_count[0]->Bplus],
            ['name'=>'B-', 'y'=>(int) $blood_group_count[0]->Bminus],
            ['name'=>'O+', 'y'=>(int) $blood_group_count[0]->Oplus],
            ['name'=>'O-', 'y'=>(int) $blood_group_count[0]->Ominus],
            ['name'=>'AB+', 'y'=>(int) $blood_group_count[0]->ABplus],
            ['name'=>'AB-', 'y'=>(int) $blood_group_count[0]->ABminus],
        ];

        //dd($timesheet_project_chart,$user_by_dep_data);
        if(\Auth::user()->hasRole(['admins','hr-staff']) || $user->isLineManager()) {
            $attendance_request = \App\Models\AttendanceChangeRequest::where('status',1)->orderBy('created_at', 'desc')->limit(1)->get();
            $add_attendance = AttendanceChangeRequestProxy::where('status',1)->orderBy('created_at', 'desc')->limit(1)->get();
            $travel_request = TravelRequest::where('status',1)->orderBy('created_at', 'desc')->limit(10)->get();
        }else{
            $attendance_request=null;
            $add_attendance=null;
            $travel_request=null;
        }

        return view('hrboard', compact('page_title', 'page_description', 'birthdays', 'holidays','blood_group', 'activity', 'on_leave','probation','employeeAge','blood_group', 'user_age','pen_leave', 'allleaves', 'user_by_dep_data', 'timesheet_project_chart', 'timesheet_info', 'timesheet_project_chart_by_cost', 'leaving_employee', 'absent_user', 'present_user', 'male_female_data', 'dep_by_user_salary','employeeUserDetails','attendance_request','add_attendance','travel_request'));
    }

    private function getEmployeSalary($user_id)
    {
        $template = \PayrollHelper::getEmployeePayroll($user_id)->template ?? '';
        $net_salary = $template->basic_salary ?? '';
        $allowances = isset($template->salary_template_id) ? \PayrollHelper::getSalaryAllowance($template->salary_template_id) : '' ;
        $deductions = isset($template->salary_template_id) ? \PayrollHelper::getSalaryDeduction($template->salary_template_id): '' ;
        if(is_array($allowances)){
            foreach ($allowances as $ak => $av) {
                $net_salary += $av->allowance_value;
            }
        }
        if(is_array($allowances)){
            foreach ($deductions as $dk => $dv) {
                $net_salary -= $dv->deduction_value;
            }
        }

        return $net_salary;
    }


    private function getAllEmployeeUserDetails(){

        $user_detail_info = \App\Models\UserDetail::select('user_details.*','users.dob')
                            ->leftjoin('users','users.id','=','user_details.user_id')
                            ->groupBy('user_id')
                            ->get();

        $userDetailsInfo = [

            'probation'=>[],
            'blood_group'=>[],

        ];

        foreach ($user_detail_info as $key => $value) {


            if($value->employemnt_type){
                $userDetailsInfo['probation'][$value->employemnt_type]['name'] = $value->employemnt_type;
                if(isset($userDetailsInfo['probation'][$value->employemnt_type]['y'])){

                 $userDetailsInfo['probation'][$value->employemnt_type]['y'] ++ ;
                }


            }
            if($value->blood_group){
                $userDetailsInfo['blood_group'][$value->blood_group]['name'] = $value->blood_group;
                if(isset($userDetailsInfo['probation'][$value->blood_group]['y'])){
                    $userDetailsInfo['blood_group'][$value->blood_group]['y'] ++;
                }
            }
            if(strtotime($value->dob)){
                $datetime1 = new \DateTime($value->dob);
                $datetime2 = new \DateTime(date('Y-m-d'));
                $interval = $datetime1->diff($datetime2);
                $age = $interval->format('%y');
                if($age >= 50){
                    $userDetailsInfo['age']['50_above']['name'] ='50 and above';
                    if (isset($userDetailsInfo['age']['50_above']['y'])) {
                        $userDetailsInfo['age']['50_above']['y'] ++;
                    }


                }elseif($age >= 40 && $age < 50 ){

                    $userDetailsInfo['age']['40_50']['name'] ='40-50';
                    if (isset($userDetailsInfo['age']['40_50']['y'])) {
                        $userDetailsInfo['age']['40_50']['y'] ++;
                    }



                }elseif($age >= 30 && $age < 40 ){

                    $userDetailsInfo['age']['30_40']['name'] ='30-40';
                    if (isset($userDetailsInfo['age']['30_40']['y'])) {
                        $userDetailsInfo['age']['30_40']['y'] ++;
                    }



                }else{

                    $userDetailsInfo['age']['20_30']['name'] ='20-30';
                    if(isset($userDetailsInfo['age']['20_30']['y'])){
                        $userDetailsInfo['age']['20_30']['y'] ++;
                    }


                     $userDetailsInfo['age']['20_30']['y'][] = $age;

                }
            }
        }


        $userDetailsInfo['probation'] = array_values($userDetailsInfo['probation']);

        $userDetailsInfo['blood_group'] = array_values($userDetailsInfo['blood_group']);

        $userDetailsInfo['age'] = array_values($userDetailsInfo['age']);

        return $userDetailsInfo;






    }
}
