<?php

namespace App\Http\Controllers;

use App\Helpers\TaskHelper;
use App\Models\Announcement;
use App\Models\AttendanceChangeRequestProxy;
use App\Models\Audit as Audit;
use App\Models\BirthdayMessage;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Projects as ProjectsModel;
use App\Models\ProjectTask;
use App\Models\ProjectUser;
use App\Models\TravelRequest;
use App\Models\UserDetail;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Flash;
use App\Models\AttendanceChangeRequest;


class HomeController extends Controller
{
    /**
     * @param Application $app
     * @param Audit $audit
     */
    public function __construct(Application $app, Audit $audit)
    {
        parent::__construct($app, $audit);
        // Set default crumbtrail for controller.
        session(['crumbtrail.leaf' => 'home']);
    }


    public  function  attendenceMissing($uid){
        $isShiftStart = null;
        $missing_details = null;;


        $checkAttendance = \App\Models\ShiftAttendance::where('user_id',$uid)->where('date',date('Y-m-d'))->count();
        $allAtt=\AttendanceHelper::getUserAttendanceUncomplete(\Auth::user()->id, date('Y-m-d',strtotime('-1 year')), date('Y-m-d'));
        $leftCheckin=[];
        for ( $i = strtotime("+1 days", strtotime($allAtt->first()->date)); $i <= strtotime(date('Y-m-d',strtotime("-1 days",strtotime(date('Y-m-d',strtotime('today')))))); $i = $i + 86400 ) {
            $leftCheckin[] = date( 'Y-m-d', $i );
        }

        $userAtt =[];
        $checkout = \App\Models\AttendanceChangeRequest::where('user_id',\Auth::user()->id)->where('attendance_status',2)->get();
        $dates=[];
        foreach($checkout as  $shiftDate){
            array_push($dates,date('Y-m-d',strtotime($shiftDate['date'])) );
        }
        foreach ($allAtt as $att){
            if ($att->date!=date('Y-m-d') && !in_array($att->date,$dates)){
                $userAtt[]=$att;

            }
        }
        //chek in
        $missedAtt =[];
        $checkinRq = \App\Models\AttendanceChangeRequest::where('user_id',\Auth::user()->id)->where('attendance_status',1)->get();
        $checkinDate=[];
        foreach($checkinRq as  $shiftDate){
            array_push($checkinDate,date('Y-m-d',strtotime($shiftDate['date'])) );
        }
        foreach ($leftCheckin as $att){
            if ($att!=date('Y-m-d') && !in_array($att,$checkinDate)){
                $missedAtt[]=$att;

            }
        }



        if($checkAttendance == 0){
            $isShiftStart = \AttendanceHelper::getUserShift($uid);
            $missing_details = 'clock_in';
            if (AttendanceChangeRequest::where([['user_id',$uid],['date',date('Y-m-d')]])->count() > 1){
                $isShiftStart = null;
            }elseif (AttendanceChangeRequest::where([['user_id',$uid],['date',date('Y-m-d')]])->count() > 0){
                if (date('H:i:s') > date('H:i:s',strtotime($isShiftStart->end_time)+900)){
                    $missing_details = 'clock_out';
                }else{
                    $isShiftStart = null;
                }
            }
        }elseif ($checkAttendance == 1 || $userAtt){
            $isShiftStart = \AttendanceHelper::getUserShift($uid);
            if (date('H:i:s') > date('H:i:s',strtotime($isShiftStart->end_time)+900)){
                $missing_details = 'clock_out';
            }else{
                $isShiftStart = null;
            }
            $missing_details = 'yesterday_out';
        }
        if(\AttendanceHelper::yestardayClockOut($uid)){
            $weekDay = date('w', strtotime('yesterday'));
            if ($weekDay > 0 && $weekDay < 6){
                $isShiftStart = \AttendanceHelper::getUserShift($uid);
                $missing_details = 'yesterday_out';
            }
        }
        if($checkAttendance == 2){
            $isShiftStart = null;
        }
        return [$isShiftStart,$missing_details,$userAtt,$missedAtt];
    }

    public function index()
    {

        if (! \Auth::check()) {
            return \Redirect::to('login')->with('message', 'Login to Start');
        }



        $missing_attendance = $this->attendenceMissing(\Auth::user()->id);

        $isShiftStart = $missing_attendance[0];
        $missing_details = $missing_attendance[1];
        $userAtt = $missing_attendance[2];
        $leftCheckin = $missing_attendance[3];


        $create_newsFeed = TaskHelper::watchNewEvents();
        $page_title = 'Home';
        $page_description = 'Welcome to <strong>'.TaskHelper::GetOrgName(\Auth::user()->org_id)->organization_name.'</strong>';

        $newsfeeds = \App\Models\NewsFeed::whereNull('schedule')->orWhere('schedule','<=',date('Y-m-d H:i'))->orderBy('id','desc')->paginate(5);
        $leads = \App\Models\Lead::where('org_id', \Auth::user()->org_id)->get();
        if (Auth::user()->hasRole('admins')) {
            $projects = ProjectsModel::where('org_id',\Auth::user()->org_id)
                ->orderBy('id', 'DESC')
                ->get();

        } else {
            $projectsUsers = ProjectUser::where('user_id', Auth::user()->id)->get()->pluck('project_id');

            $projects = ProjectsModel::whereIn('id', $projectsUsers)
                ->orderBy('id', 'DESC')
                ->where('enabled', 1)
                ->where('org_id',\Auth::user()->org_id)
                ->get();
        }
        $users = \App\User::select('first_name','last_name','id')->get();


        $today =  date('Y-m-d');

        $thisYear = date('Y');
        $birthdays = \App\User::select('*',\DB::raw('DATE_FORMAT(dob,"'.$thisYear.'-%m-%d") as birthdayDate '))
            ->whereMonth("dob",date('m',strtotime($today)))
            ->orderByRaw('DATE_FORMAT(dob, "%m-%d")')
            ->get();
        $birthdays = $birthdays->where('birthdayDate','>=',$today)->sortBy('birthdayDate')->first();
        $birthdayMessage = BirthdayMessage::where('message_type','birthday')->where('active',1)->first();
        $anniversaryMessage = BirthdayMessage::where('message_type','anniversary')->where('active',1)->first();

        if(\Request::ajax() && \Request::get('page')){

            $feeds = view('admin.newsfeeds.feeds_partials',compact('newsfeeds'))->render();

            return ['html'=>$feeds,'newsfeeds'=>$newsfeeds];
        };


        $annoucements = Announcement::where('org_id', \Auth::user()->org_id)->orderBy('created_at', 'desc')->take(7)->get();

        $attendance_log = \App\Models\ShiftAttendance::where('user_id', \Auth::user()->id)->where('date', date('Y-m-d'))->orderBy('attendance_status', 'desc')->first();



        if (! $attendance_log) {
            //check for next day night shift
            $check_night_shift = \App\Models\ShiftAttendance::where('user_id', \Auth::user()->id)
                            ->orderBy('date', 'desc')
                            ->first();

            if ($check_night_shift->shift->is_night) {
                $previous_day = date('Y-m-d', strtotime(date('Y-m-d').' -1 day'));

                $attendance_log = \App\Models\ShiftAttendance::where('user_id', \Auth::user()->id)->where('date', $previous_day)->orderBy('attendance_status', 'desc')->first();
            }
        }

        $a_p_l = \AttendanceHelper::getAbsentPesentHolidayUser(); //absent present leave

        $present_user = $a_p_l['p'];

        $on_leave = $a_p_l['l'];

        $absent_user = $a_p_l['a'];

        $today =  date('Y-m-d');

        $thisYear = date('Y');
        $birthdays = \App\User::select('*',\DB::raw('DATE_FORMAT(dob,"'.$thisYear.'-%m-%d") as birthdayDate '))
                    ->whereMonth("dob",date('m',strtotime($today)))
                    ->where('enabled', '1')
                    ->orderByRaw('DATE_FORMAT(dob, "%m-%d")')
                    ->get();
        $birthdays = $birthdays->where('birthdayDate','>=',$today)->sortBy('birthdayDate');

        $aniversaryData = \App\Models\UserDetail::select('*',\DB::raw('DATE_FORMAT(join_date,"'.$thisYear.'-%m-%d") as aniversaryDate '))
                    ->whereMonth("join_date",date('m',strtotime($today)))
                    ->orderByRaw('DATE_FORMAT(join_date, "%m-%d")')
                    ->get();
        $aniversary = $aniversaryData->where('aniversaryDate','>=',$today)->sortBy('aniversaryDate');


        $holidays = \App\Models\Holiday::where('start_date', '>=', $today)->limit(3)->get();
        $greetings = $this->greetings();
        $user = new User();

        if(\Auth::user()->hasRole(['admins','hr-staff'])) {
            $attendance = \App\Models\AttendanceChangeRequest::where('status',1)->orderBy('created_at', 'desc')->limit(8)->get();
            $add_attendance = AttendanceChangeRequestProxy::where('status',1)->groupBy('groups')->orderBy('created_at', 'desc')->limit(8)->get();
            $travel_request = TravelRequest::where('status',1)->orderBy('created_at', 'desc')->limit(10)->get();
         }
        elseif(\Auth::user()->isAuthsupervisor()){
             $users = User::where(function($query){
                if(!\Auth::user()->hasRole('hr-manager')){
                    return $query->where('first_line_manager',\Auth::user()->id);
                }
            })->get();
            $userViewAble = $users->pluck('id')->toArray();
            $userViewAble[] = \Auth::user()->id;
            $attendance = \App\Models\AttendanceChangeRequest::where(function ($q) use($userViewAble){
                                return $q->whereIn('user_id',$userViewAble);
                           })->orderBy('id', 'desc')->take(8)->get();

            $add_attendance = AttendanceChangeRequestProxy::where('status',1)->groupBy('groups')->orderBy('created_at', 'desc')->limit(8)->get();
            $travel_request = TravelRequest::where('status',1)->orderBy('created_at', 'desc')->limit(10)->get();

        }else{
            $firstline_subordinates = \App\User::firstLineSubOrdinates();
            $secondline_subordinates = \App\User::secondLineSubOrdinates();

            $attendance1 = \App\Models\AttendanceChangeRequest::whereIn('user_id',$firstline_subordinates)->where('status',1)->where('is_forwarded',0)->orderBy('created_at', 'desc')->limit(8)->get();
             $attendance2 = \App\Models\AttendanceChangeRequest::whereIn('user_id',$secondline_subordinates)->where('status',1)->where('is_forwarded',1)->orderBy('created_at', 'desc')->limit(8)->get();
            $attendance = $attendance1->merge($attendance2);

            $add_attendance1 = AttendanceChangeRequestProxy::whereIn('user_id',$firstline_subordinates)->where('status',1)->where('is_forwarded',0)->groupBy('groups')->orderBy('created_at', 'desc')->limit(8)->get();
            $add_attendance2 = AttendanceChangeRequestProxy::whereIn('user_id',$secondline_subordinates)->where('status',1)->where('is_forwarded',1)->groupBy('groups')->orderBy('created_at', 'desc')->limit(8)->get();
            $add_attendance = $add_attendance1->merge($add_attendance2);




            $travel_request1 = TravelRequest::whereIn('user_id',$firstline_subordinates)->where('status',1)->where('is_forwarded',0)->orderBy('created_at', 'desc')->limit(10)->get();
            $travel_request2 = TravelRequest::whereIn('user_id',$secondline_subordinates)->where('status',1)->where('is_forwarded',1)->orderBy('created_at', 'desc')->limit(10)->get();
            $travel_request = $travel_request1->merge($travel_request2);
            // dd($travel_request,$attendance,$add_attendance);
        }
        

        $online_leads = \App\Models\Lead::where('lead_type_id','1')->orderBy('id', 'DESC')->limit(5)->get();



        return view('home', compact('page_title', 'leftCheckin','page_description','annoucements', 'attendance_log','projects','newsfeeds','present_user','leads','on_leave','absent_user','users','birthdays','holidays','greetings','birthdayMessage','anniversaryMessage','aniversary','attendance','isShiftStart','add_attendance','missing_details','userAtt','travel_request','online_leads'));
    }

   public function greetings(){

   if(date("H") < 12){

     return "<span class='material-icons' style='font-size:10px;padding-top: 5px'>wb_sunny</span> Good Morning";

   }elseif(date("H") > 11 && date("H") < 18){

     return "<span class='material-icons' style='font-size:10px;padding-top: 5px'>filter_drama</span>Good Afternoon";

   }elseif(date("H") > 17){

     return "<span class='material-icons' style='font-size:10px;padding-top: 5px'>mode_night</span>Good Evening";

   }

}

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function welcome(Request $request)
    {
        $page_title = trans('general.text.welcome');
        $page_description = 'This is the welcome page';

        $request->session()->reflash();

        return view('welcome', compact('page_title', 'page_description'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function notViewedCases(Request $request)
    {
        $page_title = 'Not Viewed Cases';
        $page_description = 'This is the Lists of Not Viewed Cases';

        $cases = \App\Models\Cases::where('status', '!=', 'closed')
                                    ->where('org_id', Auth::user()->org_id)
                                    ->where('enabled', '1')
                                    ->orderBy('id', 'desc')->paginate(20);

        // dd($cases);

        return view('notviewedcases', compact('page_title', 'page_description', 'cases'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function NotViewedLeads(Request $request)
    {
        $page_title = 'Not Viewed Cases';
        $page_description = 'This is the Lists of Not Viewed Cases';

        $leads = \App\Models\Lead::where('viewed', '0')
                                        ->where('rating', 'active')
                                        ->where('enabled', '1')
                                        ->where('org_id', Auth::user()->org_id)
                                        ->orderBy('id', 'desc')->paginate(20);

        $stages = \App\Models\Stage::where('enabled', '1')->orderby('ordernum')->pluck('name', 'id');
        $lead_status = \App\Models\Leadstatus::where('enabled', '1')->pluck('name', 'id')->all();

        return view('notviewedleads', compact('page_title', 'page_description', 'leads', 'stages', 'lead_status'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function NotViewedMarketingTasks(Request $request)
    {
        $page_title = 'Not Viewed Marketing Tasks';
        $page_description = 'This is the Lists of Not Marketing Tasks';

        $tasks = \App\Models\Task
                            ::whereIn('task_assign_to', [\Auth::user()->id])
                            ->where('enabled', '1')
                            ->where('task_status', '!=', 'Completed')
                            ->whereBetween('task_due_date', [\Carbon\Carbon::yesterday(), \Carbon\Carbon::now()->addDays(30)])
                            ->orderBy('id', 'DESC')
                            ->paginate(20);

        return view('notviewedmarketingtasks', compact('page_title', 'page_description', 'tasks'));
    }




    public function postFeeds(Request $request){



        $attributes = $request->all();

        $attributes['user_id'] = \Auth::user()->id;



        $news = \App\Models\NewsFeed::create($attributes);


        $files = $request->file('attachment');

        $destinationPath = public_path('/news_feeds/');

        if (! \File::isDirectory($destinationPath)) {
            \File::makeDirectory($destinationPath, 0777, true, true);
        }

        foreach ($files as $key=>$doc_) {
            if ($doc_) {
                $doc_name = time().''.$doc_->getClientOriginalName();
                $doc_->move($destinationPath, $doc_name);
                $attachment = ['images'=>$doc_name,'news_feeds_id'=>$news->id];
                \App\Models\NewsFeedFiles::create($attachment);
            }
        }
        Flash::success("NewsFeed Successfully added");
       return redirect()->back();

    }


    public function postdislikelikes($id,$type){

        $news = \App\Models\NewsFeed::find($id);
        $checkLikes = $news->checkLikes();
        if($checkLikes){

            $checkLikes->delete();

        }else{

            \App\Models\NewsFeedLikes::create([

                'news_feeds_id'=>$news->id,
                'user_id'=>\Auth::user()->id,

            ]);

        }

        return ['success'=>true];


    }


    public function post_comments(Request $request,$pid){

        $attributes = $request->all();
        $attributes['news_feeds_id'] = $pid;
        $attributes['user_id'] = \Auth::user()->id;
        $comment = \App\Models\NewsFeedComments::create($attributes);
        $commenthtml = view('admin.newsfeeds.feed_comments_partials',compact('comment'))->render();

        return ['comment'=>$commenthtml];



    }


    public function viewallcomment($pid){

        $comments = \App\Models\NewsFeedComments::where('news_feeds_id',$pid)->get();

        $allcomments = '';

        foreach ($comments as $key => $comment) {
            $allcomments .= view('admin.newsfeeds.feed_comments_partials',compact('comment'))->render();
        }

        return ['html'=>$allcomments];



    }


    public function removeComment($cid){

        $comment = \App\Models\NewsFeedComments::find($cid);

        if(!$comment->isDeletable()){

            abort(403);

        }
        $comment->delete();

        return ['success'=>true];


    }

    public function removenews($id){



        $newsfeeds = \App\Models\NewsFeed::find($id);

        if(!$newsfeeds->isEditable()){

            abort(403);
        }

        $newsfeeds->delete();

        \App\Models\NewsFeedComments::where('news_feeds_id',$id)->delete();

        \App\Models\NewsFeedLikes::where('news_feeds_id',$id)->delete();

        \App\Models\NewsFeedFiles::where('news_feeds_id',$id)->delete();


        return ['success'=>true];

    }
    public function viewLiker(Request $request,$id)
    {
        $page_title = 'People who have liked this post';
        $page_description = 'List of User who like the post';
        $newsfeeds = \App\Models\NewsFeed::find($id);
        $postLikes = $newsfeeds->getTotalLikes;
        $user = [];
        foreach ($postLikes as $key=>$value){
            $user[]=[
                'image' => $value->user->image?'/images/profiles/'.$value->user->image:$value->user->avatar,
                'name' => $value->user->first_name.' '.$value->user->last_name,
                'created_at' => $value->created_at,
            ];
        }
        return view('admin.newsfeeds.like_user_modal', compact('page_title', 'page_description', 'user'));

    }

}
