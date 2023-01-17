<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\PerformanceApprisal;
use App\Models\AppraisalObjectiveType;
use App\Models\AppraisalObjective;
use App\Models\AppraisalTemplate;
use App\Models\AppraisalTemplateType;
use Flash;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Admin | Performance | Indicator';
        $page_description = 'List of  Performance Indicator';
        $performance = PerformanceIndicator::select('tbl_designations.designations', 'performance_indicator.performance_indicator_id', 'tbl_departments.deptname')->leftjoin('tbl_designations', 'tbl_designations.designations_id', '=', 'performance_indicator.designations_id')->leftjoin('tbl_departments', 'tbl_departments.departments_id', '=', 'tbl_designations.departments_id')->orderBy('performance_indicator_id', 'desc')->get();

        return view('admin.performance.indicator', compact('performance', 'page_description', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createIndicator()
    {
        $page_title = 'Admin | create';
        $competency = ['Unsatisfactory', 'Need Improvement', 'Meets Requirement', 'Exceeds Requirement', 'Outstanding'];

        $department = Department::all();

        return view('admin.performance.create_indicator', compact('competency', 'department', 'page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $indicator = $request->all();
        PerformanceIndicator::create($indicator);
        Flash::success('Indicator Created');

        return redirect('/admin/performance/indicator/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showIndicator($id)
    {
        $page_title = 'Admin | showindicator';
        $performance = PerformanceIndicator::leftjoin('tbl_designations', 'tbl_designations.designations_id', '=', 'performance_indicator.designations_id')->leftjoin('tbl_departments', 'tbl_departments.departments_id', '=', 'tbl_designations.departments_id')->where('performance_indicator_id', $id)->first();

        return view('admin.performance.show-indicator', compact('performance', 'page_title'));
    }

    public function editIndicator($id)
    {
        $page_title = 'Admin | editIndicator';
        $edit = PerformanceIndicator::where('performance_indicator_id', $id)->first();
        $competency = ['Unsatisfactory', 'Need Improvement', 'Meets Requirement', 'Exceeds Requirement', 'Outstanding'];
        $department = Department::all();

        return view('admin.performance.edit-indicator', compact('edit', 'department', 'competency', 'page_title'));
    }

    public function updateIndicator($id, Request $request)
    {
        $indicator = $request->all();
        unset($indicator['_token']);
        PerformanceIndicator::where('performance_indicator_id', $id)->update($indicator);
        Flash::success('Indicator Updated');

        return redirect('/admin/performance/indicator/');
    }

    public function getIndicatorDelete($id)
    {
        $error = null;
        $indicator = PerformanceIndicator::where('performance_indicator_id', $id)->first();
        $modal_title = 'Delete Indicator';
        $modal_body = 'Are you sure that you want to delete indicator '.$indicator->performance_indicator_id.'? This operation is irreversible';
        $modal_route = route('admin.performance.delete-indicator', $indicator->performance_indicator_id);

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    public function deleteIndicator($id)
    {
        $d = PerformanceIndicator::where('performance_indicator_id', $id)->delete();
        Flash::success('Indicator SucessFully Deleted !! ');

        return redirect('/admin/performance/indicator/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function appraisalIndex()
    {
        $page_title = 'Admin | Appraisal';
        $page_description = 'Lists Of Appraisals';
        $appraisal = PerformanceApprisal::leftjoin('users', 'performance_appraisal.user_id', '=', 'users.id')
                    ->orderBy('performance_appraisal_id', 'desc')->get();
      
        return view('admin.performance.appraisal', compact('page_title', 'appraisal', 'page_description'));
    }

    public function userAppraisal()
    {
        $page_title = 'Admin | User | Appraisal';
        $page_description = 'User Appraisal';
        $department = Department::all();

        return view('admin.performance.create_appeaisal', compact('department', 'page_title', 'page_description'));
    }

    public function appraisalCreate()
    {
        $page_title = 'Admin | Appraisal | Create';
        $qw = ['1' => 'Unsatisfactory', '2'=>'Need Improvement', '3'=>'Meets Requirement', '4'=>'Exceeds Requirement', '5'=>'Outstanding'];
        $department = Department::all();
        $apprisalObjTypes = AppraisalObjectiveType::select('id','name','points')->with('objectives:id,obj_type_id,objective,marks')->where([['is_master', '=', 'master'],['status' ,'=', '1']])->get();
        
        return view('admin.appraisal.create', compact('department', 'qw', 'page_title', 'apprisalObjTypes'));
    }
    public function userAppraisalCreate(Request $request)
    {
        if (isset($request->showappeaisal)) {
            $userinfo = unserialize($request->user_info);
           // $check = PerformanceIndicator::where('designations_id', $userinfo[1])->exists();
           // if ($check) {
            $page_title = 'Admin | Appraisal';
            $selecteduser = $userinfo[0];
            $selecteddate = $request->appraisal_month;
            $department = Department::all();
            $showappeaisal = true;
            $qw = ['1' => 'Unsatisfactory', '2'=>'Need Improvement', '3'=>'Meets Requirement', '4'=>'Exceeds Requirement', '5'=>'Outstanding'];
            
            $userappeaisal = PerformanceApprisal::where('user_id', $userinfo[0])->where('appraisal_month', $request->appraisal_month)->first();
            
            $apprisalObjTypes = AppraisalObjectiveType::select('id','name','points')->with('objectives:id,obj_type_id,objective,marks')->where([['is_master', '=', 'master'],['status' ,'=', '1']])->get();
            return view('admin.performance.create_appeaisal', compact('showappeaisal', 'department', 'qw', 'selecteduser', 'userappeaisal', 'page_title', 'selecteddate', 'apprisalObjTypes'));
        }
        if (isset($request->createappeasial)) {
            $userinfo = unserialize($request->user_info);
            $appraisal = $request->all();
            $appraisal['user_id'] = $userinfo[0];
            $appraisal['evaluator_id'] = $request->evaluator_id;
            PerformanceApprisal::create($appraisal);
            Flash::success('Appraisal SucessFully Created !! ');

            return redirect('/admin/performance/appraisal/');
        }
        if (isset($request->updateappeasial)) {
            $aid = $request->aid;
            $newappeasial = $request->all();
            unset($newappeasial['_token']);
            unset($newappeasial['user_info']);
            unset($newappeasial['updateappeasial']);
            unset($newappeasial['aid']);
            PerformanceApprisal::where('performance_appraisal_id', $aid)->update($newappeasial);
            Flash::success('Appraisal SucessFully Updated !! ');

            return redirect('/admin/performance/appraisal/');
        }
    }

    public function showAppraisal($id)
    {
        $page_title = 'Admin | show';
        $appraisal = PerformanceApprisal::leftjoin('users', 'performance_appraisal.user_id', '=', 'users.id')->where('performance_appraisal.performance_appraisal_id', $id)->first();

        return view('admin.performance.show-appeaisal', compact('appraisal', 'page_title'));
    }

    public function editAppraisal($id)
    {
        $page_title = 'Admin | User | Appraisal | Edit';
        $page_description = 'User Appraisal';
        $qw = ['1' => 'Unsatisfactory', '2'=>'Need Improvement', '3'=>'Meets Requirement', '4'=>'Exceeds Requirement', '5'=>'Outstanding'];
        $wh = ['1' => 'Unsatisfactory', '2'=>'Need Improvement', '3'=>'Meets Requirement', '4'=>'Exceeds Requirement', '5'=>'Outstanding'];
        $jk = ['1' => 'Unsatisfactory', '2'=>'Need Improvement', '3'=>'Meets Requirement', '4'=>'Exceeds Requirement', '5'=>'Outstanding'];
        $ir = ['1' => 'Unsatisfactory', '2'=>'Need Improvement', '3'=>'Meets Requirement', '4'=>'Exceeds Requirement', '5'=>'Outstanding'];
        $ls = ['1' => 'Unsatisfactory', '2.33'=>'Need Improvement', '4.33'=>'Meets Requirement', '6.33'=>'Exceeds Requirement', '8.33'=>'Outstanding'];


        $userappeaisal = PerformanceApprisal::leftjoin('users', 'performance_appraisal.user_id', '=', 'users.id')->where('performance_appraisal_id', $id)->first();

        return view('admin.performance.edit-appeaisal', compact('userappeaisal', 'qw','wh','jk','ir','ls','page_title', 'page_description'));
    }

    public function updateAppeasial($id, Request $request)
    {
        $newappeasial = $request->all();
        unset($newappeasial['_token']);
        PerformanceApprisal::where('performance_appraisal_id', $id)->update($newappeasial);
        Flash::success('Appraisal SucessFully Updated !! ');

        return redirect()->back();
    }

    public function getappeaisalDelete($id)
    {
        $error = null;
        $appeaisal = PerformanceApprisal::where('performance_appraisal_id', $id)->first();
        $modal_title = 'Delete appeaisal';
        $modal_body = 'Are you sure that you want to delete indicator '.$appeaisal->performance_appraisal_id.'? This operation is irreversible';
        $modal_route = route('admin.performance.delete-appeaisal', $appeaisal->performance_appraisal_id);

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    public function deleteAppeaisal($id)
    {
        $d = PerformanceApprisal::where('performance_appraisal_id', $id)->delete();
        Flash::success('Appeaisal SucessFully Deleted !! ');

        return redirect('/admin/performance/appraisal/');
    }

    public function ReportIndex()
    {
        $page_title = 'Admin | Report';

        return view('admin.performance.report', compact('page_title'));
    }

    public function Report($id)
    {
        $competency = ['Unsatisfactory', 'Need Improvement', 'Meets Requirement', 'Exceeds Requirement', 'Outstanding'];
        $userappeaisal = PerformanceApprisal::leftjoin('users', 'performance_appraisal.user_id', '=', 'users.id')->where('performance_appraisal_id', $id)->first();
        $html = view('admin.performance.appeaisalmodel', compact('userappeaisal', 'competency'));

        return $html;
    }
}
