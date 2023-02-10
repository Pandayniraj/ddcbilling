<?php

namespace App\Http\Controllers;

use App\Helpers\UserSystemInfoHelper;
use App\Models\Role as Permission;
use App\Models\UserDependant;
use App\Models\UserDocument;
use App\Models\UserEducation;
use App\Models\UserWorkExperience;
use App\Models\WorkingStatus;
use App\User;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use App\Models\UserDetail;
/**
 * THIS CONTROLLER IS USED AS PRODUCT CONTROLLER.
 */
class UserDetailController extends Controller
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var Permission
     */
    private $permission;

    /**
     * @param Client $client
     * @param Permission $permission
     * @param User $user
     */
    public function __construct(Permission $permission)
    {
        parent::__construct();
        $this->permission = $permission;
    }

    public function create(Request $request, $user_id = false)
    {

        if(!$user_id){

            $user_id = \Auth::user()->id;

        }

        $user_detail = \App\Models\UserDetail::where('user_id', $user_id)->first();

        if ($user_detail) {

            return redirect("/admin/users/{$user_id}/detail/{$user_detail->id}/edit");


        }

        $page_title = 'Create User Details PIS';

        $page_description = 'Personal Information System';

        $user = \App\User::find($user_id);
        $team = \App\Models\UserTeam::orderBy('id', 'desc')->where('user_id', $user_id)->first();
        $team_name = \App\Models\Team::find($team->team_id)->name;

        return view('admin.userdetails.create', compact('page_title', 'page_description', 'user_id', 'user', 'team_name'));
    }

    public function store(Request $request, $user_id)
    {
        $user_detail = \App\Models\UserDetail::where('user_id', $user_id)->first();
        if (count($user_detail) > 0) {

            Flash::warning('Ussh!! Not Allowed');

            return redirect('/admin/users');
        }

        $attributes = $request->all();

        $attributes['user_id'] = $user_id;

        if ($request->file('id_proof')) {
            $stamp = time();
            $file = $request->file('id_proof');
            $destinationPath = public_path().'/id_proof/';

            $filename = $file->getClientOriginalName();

            $request->file('id_proof')->move($destinationPath, $stamp.'_'.$filename);

            $attributes['id_proof'] = $stamp.'_'.$filename;
        }

        if ($request->file('resume')) {
            $stamp = time();
            $file = $request->file('resume');
            $destinationPath = public_path().'/resume/';

            $filename = $file->getClientOriginalName();
            $request->file('resume')->move($destinationPath, $stamp.'_'.$filename);

            $attributes['resume'] = $stamp.'_'.$filename;
        }





        $user_details = \App\Models\UserDetail::create($attributes);



        // user dependent table
        $dependents_name = $request->dependents_name;
        $dependents_relationship = $request->dependents_relationship;
        $dependents_dob = $request->dependents_dob;

        foreach ($dependents_name as $key => $value) {
            if ($value != '') {
                $detail = new UserDependant();
                $detail->user_detail_id = $user_details->id;
                $detail->name = $dependents_name[$key];
                $detail->relationship = $dependents_relationship[$key];
                $detail->dob = $dependents_dob[$key];
                $detail->save();
            }
        }

        //user education Table

        $education_level = $request->education_level;
        $education_institute = $request->education_institute;
        $education_major = $request->education_major;
        $education_year = $request->education_year;
        $education_score = $request->education_score;
        $education_start_date = $request->education_start_date;
        $education_end_date = $request->education_end_date;

        foreach ($education_level as $key => $value) {
            if ($value != '') {
                $detail = new UserEducation();
                $detail->user_detail_id = $user_details->id;
                $detail->level = $education_level[$key];
                $detail->institute = $education_institute[$key];
                $detail->major = $education_major[$key];
                $detail->year = $education_year[$key];
                $detail->score = $education_score[$key];
                $detail->start_date = $education_start_date[$key];
                $detail->end_date = $education_end_date[$key];
                $detail->save();
            }
        }

        //user  work experience

        $work_company = $request->work_company;
        $work_title = $request->work_title;
        $work_from = $request->work_from;
        $work_to = $request->work_to;
        $work_comment = $request->work_comment;

        foreach ($work_company as $key => $value) {
            if ($value != '') {
                $detail = new UserWorkExperience();
                $detail->user_detail_id = $user_details->id;
                $detail->company = $work_company[$key];
                $detail->job_title = $work_title[$key];
                $detail->date_from = $work_from[$key];
                $detail->date_to = $work_to[$key];
                $detail->comment = $work_comment[$key];
                $detail->save();
            }
        }

        // user document table

        $document_name = $request->document_name;
        $document_file = $request->document_file;

        foreach ($document_name as $key => $value) {
            if ($value != '') {
                $detail = new UserDocument();
                $detail->user_detail_id = $user_details->id;
                $detail->document_name = $document_name[$key];
                //$detail->file = $document_file[$key];

                if ($document_file[$key]) {
                    $stamp = time();
                    $file = $document_file[$key];
                    $destinationPath = public_path().'/userdocument/';

                    $filename = $file->getClientOriginalName();
                    $document_file[$key]->move($destinationPath, $stamp.'_'.$filename);

                    $detail->file = $stamp.'_'.$filename;
                }

                $detail->save();
            }
        }

        Flash::success('Users Detail Successfully Created');

        return redirect()->back();
    }

    public function countUserNewsFeed($user_id){


        $news_feed = \App\Models\NewsFeed::where('user_id',$user_id)->pluck('id');

        $newsfeddLikes = \App\Models\NewsFeedLikes::whereIn('news_feeds_id',$news_feed)->count();

        $newsfeedComments = \App\Models\NewsFeedComments::whereIn('news_feeds_id',$news_feed)
                                    ->count();

        $user_newsfeedLikes = \App\Models\NewsFeedLikes::where('user_id',$user_id)->count();

        $user_newsfeedComments = \App\Models\NewsFeedComments::where('user_id',$user_id)->count();

        $details = [
            'posted'=>count($news_feed),
            'posted_likes'=> $newsfeddLikes ,
            'posted_comment'=> $newsfeedComments ,
            'liked'=>count($user_newsfeedLikes),
            'comment'=>count($user_newsfeedComments),
        ];
        return $details;



    }

    public function getAttendaceSummary($user_id){

        $thisYear = date('Y');
        $start_date = $thisYear.'-01'.'-01';
        $end_date = date('Y-m-t',strtotime($thisYear.'-12-01'));

        $userAttendace = \App\Models\ShiftAttendance::where('user_id',$user_id)
                        ->where('date','>=',$start_date)
                        ->where('date','<=',$end_date)
                        ->get()
                        ->groupBy('date');


        $attendace_data = [
            'time_data'=>[],
            'in_time_data'=>[],
            'out_time_data'=>[],

        ];

        $getAvgStay = function($arr){

            if(count($arr) == 0){
                return null;
            }
            $average = array_sum($arr) / count($arr);

            $time = \AttendanceHelper::minutesToHours($average);


            return $time;
        };

        $getTotalStay = function($arr){

            $total = array_sum($arr);

            $time = \AttendanceHelper::minutesToHours($total);

            return $time;
        };

        $getAvgTime = function($arr){
            if(count($arr) == 0){return 0;}
          return   date('H:i', array_sum(array_map('strtotime', $arr)) / count($arr));


        };

        $getAvg = function($arr){
            if(count($arr) == 0){
                return null;
            }
            return  array_sum($arr) / count($arr);

        };

        $getTotal = function($arr){

            return  array_sum($arr);

        };


        $leaveData = \App\Models\LeaveApplication::where('application_status','2')
                    ->where("leave_start_date",'>=',$start_date)
                    ->where('leave_end_date','<=',$end_date)
                    ->where('user_id',$user_id)
                    ->get();


        foreach ($userAttendace as $date => $attendace) {


            $inTime = $attendace->first()->time;

            $outTime = $attendace->sortByDesc('attendance_status')->first()->time;


            $timeDiff = strtotime($outTime) - strtotime($inTime);

            $attendace_data['time_data'][] = $timeDiff / 60;

            $attendace_data['in_time_data'][] = date('H:i:s',strtotime($inTime));

            $attendace_data['out_time_data'][] = date('H:i:s',strtotime($outTime)) ;



        }
        $averageStay = $getAvgStay($attendace_data['time_data']);

        $totalStay = $getTotalStay($attendace_data['time_data']);

        $avgInTime = $getAvgTime($attendace_data['in_time_data']);

        $avgOutTime = $getAvgTime($attendace_data['out_time_data']);

        $leaveDays = $leaveData->pluck('leave_days')->toArray();

        $leaveAvg = $getAvg($leaveDays);

        $leaveTotal = $getTotal($leaveDays);




        return [

            'averageStay'=>$averageStay,
            'totalStay'=>$totalStay,
            'avgInTime'=>$avgInTime,
            'avgOutTime'=>$avgOutTime,
            'leaveAvg'=>$leaveAvg,
            'leaveTotal'=>$leaveTotal,


        ];


    }


    public function user_detail_info($user_id){


        $user_detail = \App\Models\UserDetail::where('user_id',$user_id)->first();
        $user_detail1 = \App\Models\UserDetail::where('user_id',$user_id)->first();
        $user_dependents = UserDependant::where('user_detail_id', $user_detail->id)->get();
        $user_education = UserEducation::where('user_detail_id', $user_detail->id)->get();
        $user_work_experience = UserWorkExperience::where('user_detail_id', $user_detail->id)->get();
        $user_documents = UserDocument::where('user_detail_id', $user_detail->id)->get();



        $team = \App\Models\UserTeam::orderBy('id', 'desc')->where('user_id', $user_id)->first();
        $team_name = \App\Models\Team::find($team->team_id)->name;


        $employement_details = \App\Models\EmployementDetails::where('user_id',$user_id)->orderBy('id','ASC')->get();


        $current_employement = $employement_details->where('is_current','1')->first();

        $news_feed_info = $this->countUserNewsFeed($user_id);

        $attendace_summary = $this->getAttendaceSummary($user_id);

        $fromProxy = false;

        if(\Auth::user()->hasRole(['admins','hr-staff'] || \Auth::user()->id == $user_id )){

            $user_detail_proxy = \App\Models\UserDetailsProxy::where('user_detail_id',$user_detail->id)->first();
            if($user_detail_proxy){

                $fromProxy = true;

                $user_detail = $user_detail_proxy;

                $changed_data = \App\Helpers\TaskHelper::changed_data($user_detail , $user_detail1);
            }

        }
        $working_status= WorkingStatus::pluck('name', 'id');




        return ['changed_data'=>$changed_data, 'user_detail'=>$user_detail,'user_dependents'=>$user_dependents,'user_education'=>$user_education,'user_work_experience'=>$user_work_experience,'user_documents'=>$user_documents,'team_name'=>$team_name,'employement_details'=>$employement_details,'news_feed_info'=>$news_feed_info,'attendace_summary'=>$attendace_summary,'current_employement'=>$current_employement ,'fromProxy'=>$fromProxy,'working_status'=>$working_status ];

    }


    public function edit(Request $request, $user_id, $detail_id)
    {
        $page_description = 'Personal Information System';
        $user = \App\User::find($user_id);

        $page_title =$user->first_name .' '.$user->last_name;



        $array = $this->user_detail_info($user_id);
        $banking_detail_changed = array();



        extract($array);
        $cnt_chg_bank_detl = (count($array['changed_data']['bank_deatil_changed']));
        $changed_data = $array['changed_data']['changed_data'];
        return view('admin.userdetails.edit', compact('changed_data','cnt_chg_bank_detl','user_detail', 'page_title', 'page_description', 'user_dependents', 'user_education', 'user_work_experience', 'user_documents', 'team_name', 'user','employement_details','news_feed_info','attendace_summary','current_employement','fromProxy','detail_id','working_status'));
    }

    public function update(Request $request, $user_id, $detail_id)
    {
        $changed_data = $request->changed_data;
        $user_detail = \App\Models\UserDetail::find($detail_id);

        $attributes = $request->all();

        \App\Models\UserDetailsProxy::where('user_detail_id',$detail_id)->delete();


        $attributes['user_id'] = $user_id;

        if ($request->file('id_proof')) {
            $stamp = time();
            $file = $request->file('id_proof');

            $destinationPath = public_path().'/id_proof/';
            $filename = $file->getClientOriginalName();
            $request->file('id_proof')->move($destinationPath, $stamp.'_'.$filename);
            $attributes['id_proof'] = $stamp.'_'.$filename;
        }

        if ($request->file('resume')) {
            $stamp = time();
            $file = $request->file('resume');
            $destinationPath = public_path().'/resume/';
            $filename = $file->getClientOriginalName();
            $request->file('resume')->move($destinationPath, $stamp.'_'.$filename);
            $attributes['resume'] = $stamp.'_'.$filename;
        }
        if( !\Auth::user()->hasRole(['hr-staff','admins']) || $user_detail->user->isLineManager()  ){
            $attributes['user_detail_id'] = $detail_id;

            \App\Models\UserDetailsProxy::create($attributes);
        }else{

            $user_detail->update($attributes);

        }
        $dependents_name = $request->dependents_name;
        $dependents_relationship = $request->dependents_relationship;
        $dependents_dob = $request->dependents_dob;



         $canContinueToUpdate = true;


        $user_dependents = UserDependant::where('user_detail_id', $detail_id)->delete();
        $user_education = UserEducation::where('user_detail_id', $detail_id)->delete();
        $user_work_experience = UserWorkExperience::where('user_detail_id', $detail_id)->delete();

        // user dependent table


        foreach ($dependents_name as $key => $value) {
            if ($value != '') {
                $detail = new UserDependant();
                $detail->user_detail_id = $detail_id;
                $detail->name = $dependents_name[$key];
                $detail->relationship = $dependents_relationship[$key];
                $detail->dob = $dependents_dob[$key];
                if($canContinueToUpdate){
                    $detail->save();
                }else{
                    $attributes['UserDependant'] = $detail;
                }

            }
        }

        //user education Table

        $education_level = $request->education_level;
        $education_institute = $request->education_institute;
        $education_major = $request->education_major;
        $education_year = $request->education_year;
        $education_score = $request->education_score;
        $education_start_date = $request->education_start_date;
        $education_end_date = $request->education_end_date;

        foreach ($education_level as $key => $value) {
            if ($value != '') {
                $detail = new UserEducation();
                $detail->user_detail_id = $detail_id;
                $detail->level = $education_level[$key];
                $detail->institute = $education_institute[$key];
                $detail->major = $education_major[$key];
                $detail->year = $education_year[$key];
                $detail->score = $education_score[$key];
                $detail->start_date = $education_start_date[$key];
                $detail->end_date = $education_end_date[$key];
                if($canContinueToUpdate){
                    $detail->save();
                }else{
                    $attributes['UserEducation'] = $detail;
                }
            }
        }


        $work_company = $request->work_company;
        $work_title = $request->work_title;
        $work_from = $request->work_from;
        $work_to = $request->work_to;
        $work_comment = $request->work_comment;

        foreach ($work_company as $key => $value) {
            if ($value != '') {
                $detail = new UserWorkExperience();
                $detail->user_detail_id = $detail_id;
                $detail->company = $work_company[$key];
                $detail->job_title = $work_title[$key];
                $detail->date_from = $work_from[$key];
                $detail->date_to = $work_to[$key];
                $detail->comment = $work_comment[$key];
                if($canContinueToUpdate){
                    $detail->save();
                }else{
                    $attributes['UserWorkExperience'] = $detail;
                }
            }
        }



        Flash::success('User Detail Updated Successfully');

        return redirect()->back();
    }

    public function userdocument(Request $request, $user_id, $detail_id)
    {
        $attributes = $request->all();

        $attributes['user_detail_id'] = $detail_id;

        if ($request->file('file')) {

            $stamp = time();
            $file = $request->file('file');
            $destinationPath = public_path().'/userdocument/';

            $filename = $file->getClientOriginalName();
            $request->file('file')->move($destinationPath, $stamp.'_'.$filename);

            $attributes['file'] = $stamp.'_'.$filename;
        }


        \App\Models\UserDocument::create($attributes);

        Flash::success('File Uploaded');

        return Redirect::back();
    }

    public function DeleteFile($id)
    {
        UserDocument::find($id)->delete();

        return Redirect::bacK();
    }

    public function confirmDeleteFile($id)
    {
        $error = null;
        $docs = UserDocument::find($id);
        // if (!$attraction->isdeletable())
        // {
        //     abort(403);
        // }
        $modal_title = 'Delete User Doc';
        $modal_body = 'Are you sure that you want to delete  User docs id '.$docs->id.'? This operation is irreversible';
        $modal_route = route('admin.userdocument.delete-file', $docs->id);

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    public function pdf($detail_id)
    {
        $user_detail = \App\Models\UserDetail::find($detail_id);

        $user_name = $user_detail->user->username;

        $user_dependents = UserDependant::where('user_detail_id', $detail_id)->get();
        $user_education = UserEducation::where('user_detail_id', $detail_id)->get();
        $user_work_experience = UserWorkExperience::where('user_detail_id', $detail_id)->get();
        // $user_documents = UserDocument::where('user_detail_id',$detail_id)->get();

        $user = \App\User::find($user_detail->user_id);
        $team = \App\Models\UserTeam::orderBy('id', 'desc')->where('user_id', $user_detail->user_id)->first();
        $team_name = \App\Models\Team::find($team->team_id)->name;

        $pdf = \PDF::loadView('admin.userdetails.userpdf', compact('user_detail', 'user_name', 'user_dependents', 'user_education', 'user_work_experience', 'user', 'team_name'));
        $file = $detail_id.'_'.$user_name.'.pdf';

        if (File::exists('reports/'.$file)) {
            File::Delete('reports/'.$file);
        }

        return $pdf->download($file);
    }



    public function edit_profile(){


        $page_description = 'Personal Information System';

        // $page_title =$user->first_name .' '.$user->last_name;

        $user_id = \Auth::user()->id;
        $array = $this->user_detail_info($user_id);
        $banking_detail_changed = array();


        $user = \App\User::find($user_id);

        extract($array);
        $cnt_chg_bank_detl = (count($array['changed_data']['bank_deatil_changed']));
        $changed_data = $array['changed_data']['changed_data'];
        $detail_id = $user_detail->id;
        return view('admin.userdetails.edit', compact('changed_data','cnt_chg_bank_detl','user_detail', 'page_title', 'page_description', 'user_dependents', 'user_education', 'user_work_experience', 'user_documents', 'team_name', 'user','employement_details','news_feed_info','attendace_summary','current_employement','fromProxy','detail_id','working_status'));


        // $user_id = \Auth::user()->id;

        // $user = \App\User::find($user_id);

        // $array = $this->user_detail_info($user_id);

        // extract($array);
        // if(!$user_detail){
        //     return redirect('/admin/create-my-profile');

        // }
        // $page_title =$user->first_name .' '.$user->last_name;
        // $page_description = 'Personal Information System';
        // $detail_id = $user_detail->id;
        // return view('admin.userdetails.edit', compact('user_detail', 'page_title', 'page_description', 'user_dependents', 'user_education', 'user_work_experience', 'user_documents', 'team_name', 'user','detail_id'));

    }



    public function addemployment($user_id){

        $user = \App\User::find($user_id);

        $allOrganization = \App\Models\Organization::pluck('organization_name as name','id');

        $departments = \App\Models\Department::all();

        $employment_type = ['permanent'=>'Permanent','probation'=>'Probation','contract'=>'Contractual','part-time'=>'Part Time','tempo'=>'Temporary','short'=>'Short Term','consult'=>'Consultant','outsource'=>'Outsourced'];

        $change_type = ['promotion'=>'Promotion','confirmation'=>'Confirmation','transfer'=>'Transfer','extension'=>'Extension','hired_as_employee'=>'Hired as Employee'];

        $supervisor = \App\User::all();

        $project = \App\Models\Projects::pluck('name','id');

        $desgination = \App\Models\Designation::pluck('designations as name','designations_id as id');

        return view('admin.userdetails.add_employment',compact('user','allOrganization','departments','employment_type','change_type','supervisor','project','desgination'));

    }


    public function storeemployment(Request $request,$user_id){
        $service_info = UserSystemInfoHelper::updateServiceInfo($request->all(),$user_id);
        $attributes = $request->all();

        $attributes['user_id'] = $user_id;

        $employement = \App\Models\EmployementDetails::create($attributes);

        if($employement->is_current){

            \App\Models\EmployementDetails::where('id','!=',$employement->id)->update(['is_current'=>0]); //remove all from current

            $user_info  = [

                'org_id'=>$employement->org_id,
                'departments_id'=>$employement->departments_id,
                'designations_id'=>$employement->designations_id,
                'first_line_manager'=>$employement->first_line_manager,
                'project_id'=>$employement->project_id,
                'work_station'=>$employement->work_station,
            ];


            \App\User::find($user_id)->safeUpdate($user_info);


            $user_details = [

                'employment_type' =>  $employement->employment_type,

            ];
            $user_detailObj = \App\Models\UserDetail::where('user_id',$user_id)->first();

            if($user_detailObj){

                $user_detailObj->update($user_details);
            }

            Flash::success("EmployementDetails added & user , UserDetail are updated");

        }

        Flash::success("EmployementDetails updated");


        return redirect()->back();

    }

    public function editemployment($id){

        $employement_details = \App\Models\EmployementDetails::find($id);

        $user_id = $employement_details->user_id;

        $user = \App\User::find($user_id);

        $allOrganization = \App\Models\Organization::pluck('organization_name as name','id');

        $departments = \App\Models\Department::all();

        $designations = \App\Models\Designation::where('departments_id',$employement_details->departments_id)->pluck('designations as name','designations_id as id');

        $employment_type = ['permanent'=>'Permanent','probation'=>'Probation','contract'=>'Contractual','part-time'=>'Part Time','tempo'=>'Temporary','short'=>'Short Term','consult'=>'Consultant','outsource'=>'Outsourced'];

        $change_type = ['promotion'=>'Promotion','confirmation'=>'Confirmation','transfer'=>'Transfer','extension'=>'Extension','hired_as_employee'=>'Hired as Employee'];

        $supervisor = \App\User::all();

        $project = \App\Models\Projects::pluck('name','id');

        $desgination = \App\Models\Designation::pluck('designations as name','designations_id as id');

        return view('admin.userdetails.edit_employment',compact('user','allOrganization','departments','employment_type','change_type','supervisor','project','desgination','employement_details','designations'));

    }

    public function updateemployment(Request $request,$id){

        $attributes = $request->all();

        $employement = \App\Models\EmployementDetails::find($id);
        $user_id = $employement->user_id;
        $attributes['user_id'] = $employement->user_id;
        $attributes['is_current'] = $request->is_current == 'on' ? 1 : 0;
        $service_info = UserSystemInfoHelper::updateServiceInfo($request->all(),$user_id);


        if($attributes['is_current']){
            \App\Models\EmployementDetails::where('id','!=',$employement->id)->update(['is_current'=>0]);

            $user_info  = [

                'org_id'=>$employement->org_id,
                'departments_id'=>$employement->departments_id,
                'designations_id'=>$employement->designations_id,
                'first_line_manager'=>$employement->first_line_manager,
                'project_id'=>$employement->project_id,
                'work_station'=>$employement->work_station,
            ];


            \App\User::find($user_id)->update($user_info);


            $user_details = [

                'employemnt_type' =>  $employement->employment_type,

            ];
            $user_detailObj = \App\Models\UserDetail::where('user_id',$user_id)->first();

            if($user_detailObj){

                $user_detailObj->update($user_details);
            }

            Flash::success("EmployementDetails added & user , UserDetail are updated");

        }
        $employement->update($attributes);

        Flash::success("EmployementDetails updated");


        return redirect()->back();


    }

    public function allpendingprofilerequest(){

         $page_title = 'Profile Change Requests';
         $page_description = 'All profile change requests';
         $allrequest = \App\Models\UserDetailsProxy::orderBy('id', 'desc')->paginate(15);

        return view('admin.userdetails.allpendingprofilerequest', compact('allrequest', 'page_title', 'page_description'));
    }


    public function approve($detail_id){

        $user_detail = UserDetail::find($detail_id);
        $proId = \App\Models\UserDetailsProxy::where('user_detail_id',$detail_id)->first();
        if($proId){
                $user_detail =UserDetail::find($detail_id);
                if(empty($user_detail)){
                $user_id = $proId->user_id;
                $user_detail = UserDetail::where('user_id',$user_id)->first();
                $proId->delete();
          }
        }



        if(\Auth::user()->hasRole(['admins','hr-staff']) || $user_detail->user->isLineManager() ){
            $user_detail_id = $user_detail->id;
            $proxyInfo = \App\Models\UserDetailsProxy::where('user_detail_id',$user_detail_id)->first();

            $proxyInfoArr = $proxyInfo->toArray();

            unset($proxyInfoArr['id']);

            $user_detail->update($proxyInfoArr);
            $proxyInfo->delete();
            Flash::success("User Details Approved");

            return redirect()->route('admin.allpendingprofilerequest');
        }else{

            abort(403);
        }
    }

     public function reject($detail_id){


        $user_detail = UserDetail::find($detail_id);

        if(\Auth::user()->hasRole(['admins','hr-staff']) || $user_detail->user->isLineManager() ){

            $proxyInfo = \App\Models\UserDetailsProxy::where('user_detail_id',$detail_id)->first();
            $proxyInfo->delete();
            Flash::success("User Details Rejected");
            return redirect()->back();
        }else{

            abort(403);
        }
    }

}
