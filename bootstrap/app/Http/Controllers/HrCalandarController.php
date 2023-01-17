<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class HrCalandarController extends Controller
{



	public function holidays($start_date,$end_date){


		$holidayData = \App\Models\Holiday::where("start_date",'>=',$start_date)
				->where('end_date','<=',$end_date)->orderBy('start_date','asc')->get();

		$data = [];
		foreach ($holidayData as $key => $value) {

		$data [] =[
			'title'=>'<i class="fa  fa-calendar-o"></i> '.$value->event_name,
			'start'=> $value->start_date,
			'end'=> $value->end_date,
			'url' => '/admin/hrcalendar/'.$value->holiday_id.'/view/holiday',
			'backgroundColor'=>'#2196F3',
			'id'=>$value->id,
			'description'=>$value->description
		];
		}

		return $data;
	}


	public function birthdays($start_date){

		$month = date('m',strtotime($start_date));
		$year = date('Y',strtotime($start_date));
		$birthdayData = \App\User::orderBy('id', 'desc')->whereMonth("dob",$month)
					->get();

		$data = [];
		foreach ($birthdayData as $key => $value) {

			$data [] =[
				'title'=>'<i class="fa  fa-birthday-cake"></i> '.$value->first_name.' '.$value->last_name,
				'start'=>  $year.'-'.date('m-d',strtotime($value->dob)),
				'end'=>  $year.'-'.date('m-d',strtotime($value->dob)),
				'url' => '/admin/hrcalendar/'.$value->id.'/view/birthday',
				'backgroundColor'=>'#F44336',
				'id'=>$value->id,
				'description'=>'Happy Birthday'
			];
		}

		return $data;
	}

	public function work_aniversary($start_date){
		$month = date('m',strtotime($start_date));
		$year = date('Y',strtotime($start_date));

		$aniversaryData = \App\Models\UserDetail::whereMonth("join_date",$month)
					->orderBy('join_date', 'desc')
					->get();

		$data = [];
		foreach ($aniversaryData as $key => $value) {
			$user = $value->user;
			$data [] =[
				'title'=>'<i class="fa  fa-calendar"></i> '.$user->first_name.' '.$user->last_name,
				'start'=>  $year.'-'.date('m-d',strtotime($value->join_date)),
				'end'=>  $year.'-'.date('m-d',strtotime($value->join_date)),
				'url' => '/admin/hrcalendar/'.$value->id.'/view/anniversary',
				'backgroundColor'=>'#4CAF50',
				'id'=>$value->id,
				'description'=>'Happy Aniversary'
			];
		}

		return $data;
	}


	public function leave($start_date,$end_date){


		$leavedata = \App\Models\LeaveApplication::where('application_status', '2')->where('leave_start_date', '>=', $start_date)->where('leave_end_date', '<=', $end_date)
			->orderBy('leave_start_date','asc')
			->get();
		$data  = [];
		foreach ($leavedata as $key => $value) {
			$user = $value->user;



			$data [] = [

				'title'=>'<i class="fa fa-hourglass"></i> '.$user->first_name.' '.$value->partOfDay(),
				'start'=> $value->leave_start_date,
				'end'=> $value->leave_end_date,
				'url' => '/admin/hrcalendar/'.$value->leave_application_id.'/view/leave',
				'backgroundColor'=>'#3F51B5',
				'id'=>$value->id,
				'description'=>'On Leave',
			];

		}

		return $data;

	}




	public function leaveByUser($start_date,$end_date,$user_id){


		$leavedata = \App\Models\LeaveApplication::where('leave_start_date', '>=', $start_date)->where('leave_end_date', '<=', $end_date)
			->where('user_id',$user_id)
			->orderBy('leave_start_date','asc')
			->get();

		$data  = [];

		$user = $leavedata->first()->user;


		foreach ($leavedata as $key => $value) {

			if($value->application_status == 1){
				$color = 'orange';
				$leave_status = '(Pending)';
			}elseif ($value->application_status == 2) {
				$color = 'green';
				$leave_status = '(Accepted)';
			}else{
				$color = 'red';
				$leave_status = '(Rejected)';
			}

			$data [] = [

				'title'=>'<i class="fa fa-hourglass"></i> '.$value->partOfDay().' '.$leave_status,
				'start'=> $value->leave_start_date,
				'end'=> $value->leave_end_date,
				'url' => '/admin/hrcalendar/'.$value->id.'/view/leaveUser',
				'backgroundColor'=>$color,
				'id'=>$value->id,
				'description'=>'On Leave',
			];

		}
		//dd($data);

		return $data;

	}




    public function index(){

           if( \Request::get('pre')){
               $time = date("Y-m-d",strtotime(\Request::get('pre')));;
               $preMonth = date('Y-m-d',strtotime("-1 month", strtotime($time)));
               $start_date = date('Y-m-01',strtotime($preMonth));
               $end_date = date('Y-m-t',strtotime($preMonth));
               return '/admin/hrcalandar/'.'?date_range='.$start_date;
           }elseif( \Request::get('next')){
                $time = date("Y-m-d",strtotime(\Request::get('next')));;
                $nextMonth = date('Y-m-d',strtotime("+1 month", strtotime($time)));
                $start_date = date('Y-m-01',strtotime($nextMonth));
                $end_date = date('Y-m-t',strtotime($nextMonth));
               return '/admin/hrcalandar/'.'?date_range='.$start_date;
           }

    	if(\Request::get('date_range')){

    		$start_date = date('Y-m-01',strtotime(\Request::get('date_range')));

    		$end_date = date('Y-m-t',strtotime(\Request::get('date_range')));

    	}else{
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
    	}
    	$page_title = 'Hr Board';

		$hrData =[];
		$leaveData = $this->leave( $start_date,$end_date );
		$holidayData =  $this->holidays( $start_date,$end_date );
		$birthdayData =  $this->birthdays( $start_date );
		$work_aniversaryData = $this->work_aniversary($start_date);
		$hrData = array_merge($leaveData,$hrData);
		$hrData = array_merge($holidayData,$hrData);
		$hrData = array_merge($birthdayData,$hrData);
		$hrData = array_merge($work_aniversaryData,$hrData);

    	return view('hrcalandar',compact('hrData','leaveData','holidayData','birthdayData','work_aniversaryData','start_date','page_title'));

    }
    public function viewDetail(Request $request,$id,$type){


		if($type == 'holiday'){
            $page_title = 'Holiday';
            $data = \App\Models\Holiday::where('holiday_id',$id)->first();
            $year = date('Y');
            $hrData =[
                'Name'=>$data->event_name,
                'Start Date'=> date('dS M, Y',strtotime($data->start_date)) ,
                'End Date'=> date('dS M, Y',strtotime($data->end_date)) ,
                'Description'=> $data->description,
            ];
        }elseif($type == 'birthday'){
            $page_title = 'Happy Birthday ';
            $data = \App\User::find($id);
            $hrData =[
                'image'=>$data->image?'/images/profiles/'.$data->image:$data->avatar,
                'Name'=>$data->first_name.' '.$data->last_name,
                'Date of Birth '=> date('dS M, Y',strtotime($data->dob)) ,
                'Description'=>'Happy Birthday '.$data->full_name.'<i class="fa  fa-birthday-cake"></i> ',
            ];
        }elseif($type == 'anniversary'){
            $page_title = 'Anniversary';
            $data = \App\Models\UserDetail::find($id);
            $hrData =[
                'image'=>$data->user->image?'/images/profiles/'.$data->user->image:$data->user->avatar,
                'Name'=>$data->user->full_name,
                'Date of Join'=> date('dS M, Y',strtotime($data->join_date)) ,
                'Description'=>'Happy Anniversary '.$data->user->full_name,
            ];

        }elseif($type == 'leave'){
            $page_title = 'Leave Application';
            $data = \App\Models\LeaveApplication::where('leave_application_id',$id)->first();
            $time = '';
            if($data->part_of_day == 1){
                $time = 'Full Day';
            }elseif($data->part_of_day == 2){
                $time = 'First Half of Day';
            }elseif($data->part_of_day == 3){
                $time = 'Second Half of day';
            }
            $application_status = '';
            if($data->application_status == 1){
                $application_status = 'pending';
            }elseif($data->application_status == 2){
                $application_status = 'accepted';
            }elseif($data->application_status == 3){
                $application_status = 'Rejected';
            }

            $hrData =[
                'image'=>$data->user->image?'/images/profiles/'.$data->user->image:$data->user->avatar,
                'Name'=>$data->user->full_name,
                'Leave Start Date'=> date('dS M, Y',strtotime($data->leave_start_date)) ,
                'Leave End Date'=> date('dS M, Y',strtotime($data->leave_end_date)) ,
                'Part of day'=> $time ,
                'Application Status '=> $application_status ,
                'Description'=>$data->comments,
            ];
        }elseif($type == 'leaveUser'){
            $page_title = 'User leave';
            $data = \App\Models\LeaveApplication::where('leave_application_id',$id)->first();
            $time = '';
            if($data->part_of_day == 1){
                $time = 'Full Day';
            }elseif($data->part_of_day == 2){
                $time = 'First Half of Day';
            }elseif($data->part_of_day == 3){
                $time = 'Second Half of day';
            }
            $application_status = '';
            if($data->application_status == 1){
                $application_status = 'pending';
            }elseif($data->application_status == 2){
                $application_status = 'accepted';
            }elseif($data->application_status == 3){
                $application_status = 'Rejected';
            }

            $hrData =[
                'image'=>$data->user->image?'/images/profiles/'.$data->user->image:$data->user->avatar,
                'Name'=>$data->user->full_name,
                'Leave Start Date'=> date('dS M, Y',strtotime($data->leave_start_date)) ,
                'Leave End Date'=> date('dS M, Y',strtotime($data->leave_end_date)) ,
                'Part of day'=> $time ,
                'Application Status '=> $application_status ,
                'description'=>$data->comments,
            ];
        }
        return view('admin.tasks.modals.viewfromcalander', compact('page_title', 'hrData'));
    }

    public  function  getUserAttendence($uid,$start_date,$end_date){



    	$begin = new \DateTime($start_date);
		$end = new \DateTime($end_date);
		$end = $end->modify( '+1 day' );
		$interval =  new \DateInterval('P1D');
		$period = new \DatePeriod($begin, $interval, $end);


    	$userAttendace = \App\Models\ShiftAttendance::where('date','>=',$start_date)
    					->where('date','<=',$end_date)->where('user_id',$uid)->orderBy('date','asc')->get();

    	$hrData = [];

    	$attendaceData = [];
	    foreach ($period as $dt) {

	    	$date = $dt->format('Y-m-d');

	    	$current_attendance = $userAttendace->where('date',$date)->sortBy('time');

	    	foreach ($current_attendance as $key => $value) {

	    		if($value->is_adjusted){
	    			$color= '#4caf50';
	    			$icon='<i class="fa fa-fw fa-cog"></i>&nbsp;';
	    		}elseif( $value->attendance_status % 2 == 0){ //out
	    			$color='#009688';
                    $icon='<i class="fa fa-fw fa-globe"></i>&nbsp;';
	    		}else{
	    			$color = '#00bcd4';
                    $icon='<i class="fa fa-fw fa-globe"></i>&nbsp;';
	    		}

		    	$attendaceData [] = [

					'title'=>$icon.date('H:i:s',strtotime($value->time)),
					'start'=> $value->date,
					'end'=> $value->date,
					'url' => '#',
					'backgroundColor'=>$color,
					'id'=>$value->id,
					'description'=>'',
				];


	    	}


		}

		$hrData = array_merge($attendaceData,$hrData);


		$leavedata = $this->leaveByUser($start_date,$end_date,$uid);

		$hrData = array_merge($leavedata,$hrData);

		$holidayData =  $this->holidays( $start_date,$end_date );

		$hrData = array_merge($holidayData,$hrData);


		return $hrData;


    }



    public function attendaceCalendar($uid){


    	if(\Request::get('date_range')){

    		$start_date = date('Y-m-01',strtotime(\Request::get('date_range')));

    		$end_date = date('Y-m-t',strtotime(\Request::get('date_range')));

    	}else{
	    	$start_date = date('Y-m-01');
			$end_date = date('Y-m-t');

    	}

    	$hrData = $this->getUserAttendence($uid,$start_date,$end_date);

		$taskData = $this->getTask($uid,$start_date,$end_date);
		$hrData = array_merge($hrData,$taskData);

		$page_title = 'User Attedance';
		$user = \App\User::find($uid);
		$form_action = route('admin.attendace.calandar',$user->id);

		return view('admin.users.attedance_calendar',compact('hrData','start_date','user','page_title','form_action'));
	}

	public function getTask($uid,$start_date,$end_date){

		$tasks = \App\Models\Task::leftjoin('task_users', 'task_users.task_id', '=', 'tasks.id')
                        ->select('tasks.id', 'tasks.task_subject as title', 'tasks.task_start_date as start', 'tasks.task_due_date as end',
                        'tasks.task_detail', 'tasks.color', 'tasks.task_start_date', 'tasks.lead_id','tasks.task_status')
						->where('tasks.task_start_date','>=',$start_date)
						->where('tasks.task_start_date','<=',$end_date)
                        ->Where('tasks.task_owner',$uid)

                        ->orderBy('tasks.task_start_date', 'desc')
						->get();
		$allTasks = [];
		foreach ($tasks as $key => $value) {
			$new = [
				'title'=>$value->title,
				'iscompleted'=>$value->task_status == 'Completed'?true:false,
				'start'=> $value->start,
				'end'=> $value->end,
				'url' => '/admin/tasks/'.$value->id.'/edit',
				'backgroundColor'=>$value->color ?? '#f56954',
				'borderColor'=>$value->color ?? '#f56954',
				'group_name'=>$value->lead->name,
				'id'=>$value->id,
				'type'=>'task',
				'end_date'=> date('dS M Y', strtotime($value->task_end_date)),
				'description'=>strlen($value->task_detail) > 20 ? substr($value->task_detail, 0, 20).'..' : $value->task_detail,
			];
			array_push($allTasks, $new);
		}
		return $allTasks;




	}


    public function showMyAttendace(){
        if( \Request::get('pre')){
            $time = date("Y-m-d",strtotime(\Request::get('pre')));;
            $preMonth = date('Y-m-d',strtotime("-1 month", strtotime($time)));
            $start_date = date('Y-m-01',strtotime($preMonth));
            $end_date = date('Y-m-t',strtotime($preMonth));
            return '/admin/my_attendance/calandar/'.'?date_range='.$start_date;
        }elseif( \Request::get('next')){
            $time = date("Y-m-d",strtotime(\Request::get('next')));;
            $nextMonth = date('Y-m-d',strtotime("+1 month", strtotime($time)));
            $start_date = date('Y-m-01',strtotime($nextMonth));
            $end_date = date('Y-m-t',strtotime($nextMonth));
            return '/admin/my_attendance/calandar/'.'?date_range='.$start_date;
        }

        if(\Request::get('date_range')){

    		$start_date = date('Y-m-01',strtotime(\Request::get('date_range')));

    		$end_date = date('Y-m-t',strtotime(\Request::get('date_range')));

    	}else{
	    	$start_date = date('Y-m-01');
			$end_date = date('Y-m-t');

    	}

    	$uid = \Auth::user()->id;

    	$user = \App\User::find($uid);

    	$hrData = $this->getUserAttendence($uid,$start_date,$end_date);
    	$taskData = $this->getTask($uid,$start_date,$end_date);

		$hrData = array_merge($hrData,$taskData);

    	$page_title = 'My Attedance';

    	$form_action = route('admin.my_attendance');

    	return view('admin.users.attedance_calendar',compact('hrData','start_date','user','page_title','form_action'));

    }







}
