<?php

namespace App\Exports\Reports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class StockWiseSalesReport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $outletname;
    private $nepalienddate;
    private $organization;
    private $stock;
    private $data;
    private $nepalistartdate;
    private $products;

    public function __construct($stock, $data, $products, $nepalistartdate, $outletname, $nepalienddate, $organization)
    {
        $this->stock = $stock;
        $this->data = $data;
        $this->products = $products;
        $this->nepalistartdate = $nepalistartdate;
        $this->outletname = $outletname;
        $this->organization = $organization;
        $this->nepalienddate = $nepalienddate;
    }

    public function view(): View
    {
        return view('admin.reports.excel.stockwiseSalesReportExcel',['stock'=>$this->stock, 'data'=>$this->data,
            'products'=>$this->products, 'nepalistartdate'=>$this->nepalistartdate, 'outletname'=>$this->outletname,
            'nepalienddate'=>$this->nepalienddate, 'organization'=>$this->organization]);
    }
}
