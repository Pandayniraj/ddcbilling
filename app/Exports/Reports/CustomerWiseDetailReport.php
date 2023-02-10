<?php

namespace App\Exports\Reports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomerWiseDetailReport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $exceldata = [];
    protected $excelcolumns = [];
    protected $companyheading;
    private $nepalistartdate;
    private $outletname;
    private $nepalienddate;
    private $clients;
    private $detail_transaction;
    private $startdate;
    private $enddate;
    private $organization;

    public function __construct($detail_transaction, $clients, $outletname, $startdate, $enddate, $nepalistartdate, $nepalienddate, $organization, $created_by)
    {
        $this->detail_transaction = $detail_transaction;
        $this->clients = $clients;
        $this->outletname = $outletname;
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->nepalistartdate = $nepalistartdate;
        $this->nepalienddate = $nepalienddate;
        $this->organization = $organization;
        $this->created_by = $created_by;
    }

    public function view(): View
    {
        return view('admin.reports.excel.customerwisedetailreportExcel',['detail_transaction'=>$this->detail_transaction, 'clients'=>$this->clients,
            'outletname'=>$this->outletname, 'startdate'=>$this->startdate, 'enddate'=>$this->enddate, 'nepalistartdate'=>$this->nepalistartdate,
            'nepalienddate'=>$this->nepalienddate, 'organization'=>$this->organization, 'created_by'=>$this->created_by]);
    }
}
