<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CashInOutController extends Controller
{


	public function cash(){

		if(\Request::get('start_date') && \Request::get('end_date')){

			$start_date = \Request::get('start_date');

			$end_date = \Request::get('end_date');

		}else{

			$start_date = date('Y-m-d');

			$end_date = date('Y-m-d');

		}
		$types = ['customer_payment'=>'Customer Payment', 'customer_advance'=>'Customer Advance', 'sales_without_invoice'=>'Sales Without Invoice', 'other_income'=>'Other Income', 'interest_income'=>'Interest Income'];



		$payment = \App\Models\Payment::where('date','>=',$start_date)
					->where('date','<=',$end_date)
					->get();


		$bankingIncome = \App\Models\BankIncome::where('date_received','>=',$start_date)
					->where('date_received','<=',$end_date)
					->get();

		$expenses = \App\Models\Expense::where('date','>=',$start_date)
					->where('date','<=',$end_date)
					->get();

	//					dd($bankingIncome);

		$page_title = 'Day Book & Cash Flow';

		return view('admin.cashinout.list',compact('payment','bankingIncome','expenses','types','page_title','start_date','end_date'));


	}
    public function daybook(Request $request){

        $page_title = 'Daybook';
		if(\Request::get('start_date') && \Request::get('end_date')){

			$start_date = \Request::get('start_date');

			$end_date = \Request::get('end_date');

		}else{

			$start_date = date('Y-m-d');

			$end_date = date('Y-m-d');

		}
        $op = \Request::get('op');
        $prefix = '';
        $entries_table = new \App\Models\Entry();

        if ($selected_fiscal_year != $current_fiscal_year->numeric_fiscal_year) {
            $prefix = $selected_fiscal_year . '_';
            $new_entries_table = $prefix . $entries_table->getTable();
            $entries_table->setTable($new_entries_table);
        }

        $entries = $entries_table->select($prefix.'entries.*')
        ->leftjoin($prefix.'entryitems', $prefix.'entryitems.entry_id', '=', $prefix.'entries.id')
        ->where($prefix.'entries.org_id', \Auth::user()->org_id)
        ->where(function ($query) use ($prefix,$start_date,$end_date) {
            if ($start_date && $end_date) {
                return $query->whereDate($prefix.'entries.date', '>=', $start_date)->whereDate($prefix.'entries.date', '<=',$end_date);
            }
        })
        ->orderBy($prefix.'entries.id', 'desc')
        ->groupBy($prefix.'entries.id')
        ->paginate(30);
        if ($op == 'excel') {

            $date = date('Y-m-d');
            $title = 'Day Book';
            $entries = $entries_table->select($prefix.'entries.*')
                ->leftjoin($prefix.'entryitems', $prefix.'entryitems.entry_id', '=', $prefix.'entries.id')
                ->where($prefix.'entries.org_id', \Auth::user()->org_id)
                ->where(function ($query) use ($prefix,$start_date,$end_date) {
                    if ($start_date && $end_date) {
                        return $query->whereDate($prefix.'entries.date', '>=', $start_date)->whereDate($prefix.'entries.date', '<=',$end_date);
                    }
                })
                ->orderBy($prefix.'entries.id', 'desc')
                ->groupBy($prefix.'entries.id')
                ->get();
            return \Excel::download(new \App\Exports\DayBookExcelExport($title,$entries,$start_date,$end_date), "Daybook_{$date}.xls");

        }
        return view('admin.cashinout.daybook',compact('start_date','end_date','entries','page_title'));
    }

    public function gl(Request $request){

        $page_title = 'General Ledger';
        $ledgers = \App\Models\COALedgers::paginate(50);

        return view('admin.cashinout.gl',compact('page_title', 'ledgers'));

    }



}
