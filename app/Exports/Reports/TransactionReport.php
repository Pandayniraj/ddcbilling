<?php

namespace App\Exports\Reports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class TransactionReport implements FromView, ShouldAutoSize
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

    public function __construct($detail_transaction, $clients, $outletname, $startdate, $enddate, $nepalistartdate, $nepalienddate, $organization)
    {
        $this->outletname = $outletname;
        $this->nepalistartdate = $nepalistartdate;
        $this->nepalienddate = $nepalienddate;
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->organization = $organization;
        $this->clients = $clients;
        $this->detail_transaction = $detail_transaction;
    }

    public function view(): View
    {
        return view('admin.reports.excel.customerwisereportExcel',['detail_transaction'=>$this->detail_transaction, 'clients'=>$this->clients,
            'outletname'=>$this->outletname, 'startdate'=>$this->startdate, 'enddate'=>$this->enddate, 'nepalistartdate'=>$this->nepalistartdate,
            'nepalienddate'=>$this->nepalienddate, 'organization'=>$this->organization]);
    }
}
