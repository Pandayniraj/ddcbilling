<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeaveReportExcel implements FromView ,ShouldAutoSize
{

	 use Exportable;

	protected $budgets,$selected_start_date,$selected_end_date,$filter_type;

	public function __construct($categories,$nepstart_date,$nepend_date,$users,$startdate, $enddate,$filter_type){

		$this->categories = $categories;
		$this->nepstart_date = $nepstart_date;
		$this->nepend_date = $nepend_date;
		$this->users = $users;
		$this->startdate = $startdate;
		$this->enddate = $enddate;
		$this->filter_type = $filter_type;
	}




    public function view(): View
    {

    	 return view('admin.leave_mgmt.leave-report-excel',['users'=>$this->users,
             'categories'=>$this->categories, 'nepstart_date'=>$this->nepstart_date,
             'nepend_date'=>$this->nepend_date,'startdate'=>$this->startdate,
             'enddate'=>$this->enddate,'filter_type'=>$this->filter_type
             ]);

    }
}
