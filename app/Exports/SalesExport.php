<?php
namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Entry;
use App\Models\Entryitem;

class SalesExport implements FromView ,ShouldAutoSize
{

	 use Exportable;

	protected $data,$excel_name;

	public function __construct($data,$excel_name){

		$this->data = $data;
		$this->excel_name = $excel_name;
	}

    public function view(): View
    {
		// dd($this->data);
    	return view('admin.sales-book.salesexport',['data'=>$this->data,'excel_name'=>$this->excel_name]);

        // return $this->viewFile ;
    }
}
