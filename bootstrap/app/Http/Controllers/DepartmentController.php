<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Audit as Audit;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\Role as Permission;
use App\User;
use Auth;
use DB;
use Flash;
use Illuminate\Http\Request;

/**
 * THIS CONTROLLER IS USED AS PRODUCT CONTROLLER.
 */
class DepartmentController extends Controller
{
    /**
     * @var Permission
     */
    private $permission;

    /**
     * @param Permission $permission
     */
    public function __construct(Permission $permission)
    {
        parent::__construct();
        $this->permission = $permission;
    }

    // Stock Category
    public function index()
    {
        $page_title = 'Departments';
        $page_description = 'All Departments';

        $departments = Department::where('org_id', \Auth::user()->org_id)->orderBy('deptname', 'asc')->get();
        $division = Division::pluck('name','id');
        $departmentsByDivision = Department::orderBy('deptname', 'asc')->get()->groupBy('division_id');

        return view('admin.departments.index', compact('page_title', 'page_description', 'departments','division','departmentsByDivision'));
    }

    public function store(Request $request)
    {
        if ($request->departments_id == '') {
            $this->validate($request, ['deptname' => 'required']);
            $department = Department::create(['deptname' => $request->deptname, 'org_id'=>\Auth::user()->org_id,'division_id'=>$request->division_id]);
            if ($request->designations != '') {
                Designation::create(['departments_id' => $department->id, 'designations' => $request->designations,
                    'org_id'=>\Auth::user()->org_id, ]);
            }

            Flash::success('Department created Successfully');
        } else {
            if ($request->designations_id) {
                Designation::where('designations_id', $request->designations_id)->update(['departments_id'=>$request->departments_id, 'designations' => $request->designations]);
                Flash::success('Designation updated Successfully');
            } else {
                Designation::create(['departments_id'=>$request->departments_id, 'designations' => $request->designations, 'org_id'=>\Auth::user()->org_id]);
                Flash::success('Designation created Successfully');
            }
        }
        //return \Redirect::back();
        return redirect('/admin/departments');
    }

    public function edit($departments_id)
    {
        $department = Department::where('departments_id', $departments_id)->first();
        
        $division = Division::pluck('name','id');

        $data = view('admin.departments.edit',compact('department','division'))->render();

        return $data;
    }

    public function updateDepartment($departments_id, Request $request)
    {
        $department = Department::where('departments_id', $departments_id)->first();

        if (! $department) {
            abort(404);
        }

        Department::where('departments_id', $departments_id)->update(['deptname'=>$request->deptname,'division_id'=>$request->division_id]);

        Flash::success('Department updated Successfully');

        return redirect('/admin/departments');
    }

    public function editDesignation($departments_id, $designations_id)
    {
        $page_title = 'Departments';
        $page_description = 'All Departments';

        $designation = null;

        $departments = Department::where('org_id', \Auth::user()->org_id)->orderBy('deptname', 'asc')->get();
        $designation = Designation::where('designations_id', $designations_id)->first();
        $departmentsByDivision = Department::orderBy('deptname', 'asc')->get()->groupBy('division_id');
        $division = Division::pluck('name','id');



        if (! $designation) {
            abort(404);
        }

        return view('admin.departments.index', compact('page_title', 'page_description', 'departments', 'designation','division','departmentsByDivision'));
    }

    public function deleteDepartment($departments_id)
    {
        $department = Department::where('departments_id', $departments_id)->first();
        if (! $department->isEditable() && ! $department->canChangePermissions()) {
            abort(403);
        }

        Designation::where('departments_id', $departments_id)->delete();
        Department::where('departments_id', $departments_id)->delete();

        Flash::success('Department deleted Successfully');

        return redirect('/admin/departments');
    }

    public function deleteDesignation($designations_id)
    {
        $designation = Designation::where('designations_id', $designations_id)->first();
        if (! $designation->isEditable() && ! $designation->canChangePermissions()) {
            abort(403);
        }

        Designation::where('designations_id', $designations_id)->delete();
        Flash::success('Designation deleted Successfully');

        return redirect('/admin/departments');
    }
}
