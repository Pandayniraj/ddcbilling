<?php
namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Entry;
use App\Models\Entryitem;

class LeadgerExcelExport implements FromView ,ShouldAutoSize
{

	 use Exportable;

	protected $entry_items ,$company_name ,$ledgers_data,$start_date,$end_date;

	public function __construct($entry_items ,$company_name ,$ledgers_data,$start_date,$end_date){

		$this->entry_items = $entry_items;
        $this->company_name = $company_name;
        $this->ledgers_data = $ledgers_data;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
	}


    

    public function view(): View
    {   
         $entry_items = $this->entry_items;
         $company_name = $this->company_name;
         $ledgers_data = $this->ledgers_data;
         $start_date = $this->start_date;
         $end_date = $this->end_date;

    	 return view('admin.coa.excelledgers',[
                        'entry_items'=>$entry_items,
                         'pan' =>  \Auth::user()->organization->vat_id,
                         'address' => \Auth::user()->organization->address,
                         'name' => $company_name,
                         'ledgers_data' => $ledgers_data,
                         'start_date' => $start_date,
                         'end_date' => $end_date,
                     ]);

        // return $this->viewFile ;
    }
}