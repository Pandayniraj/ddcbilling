<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\PurchaseOrder;
class TradingBoardController extends Controller
{



    public function __construct(){


        if(\Request::get('start_date') && \Request::get('end_date') ){



            $this->start_date = \Request::get('start_date');
            $this->end_date = \Request::get('end_date');

        }else{


            $this->start_date = date('Y-m') .'-01';
            $this->end_date = date('Y-m-t');
        }


    


    }


    public function index(){


        $salesData = $this->SalesData();

        $purhaseData = $this->PurchaseData();

        $expenseData = $this->ExpenseData();

        $stockInventoryData = $this->StockInventoryData();

        $dates = [
            'start'=>$this->start_date,
            'end'=>$this->end_date,

        ];

        $page_title = 'Admin | Stock Boards';
        //dd($salesData);
        // return ['salesData'=>$salesData,'purhaseData'=>$purhaseData,'expenseData'=>$expenseData,'stockInventoryData'=>$stockInventoryData];
        return view('stocksboards',compact('salesData','purhaseData','expenseData','stockInventoryData','dates','page_title'));




        // $salesBoard = \App\Models\Order



    }




    public function SalesData(){
        $orders = Orders::where('bill_date','>=',$this->start_date)
                    ->where('bill_date','<=',$this->end_date)
                    ->where('order_type','proforma_invoice')
                    ->get()
                    ->groupBy('client_id');

        $salesData = [
            'totalSales'=>0,
            'receiveAmount'=>0,
            'graphData'=>[],
            'dataWithCustomer'=>[],

        ];

        foreach ($orders as $key => $value) {
            
            $totalAmount = $value->sum('total_amount');

            $salesData['totalSales'] += $totalAmount;
            
            $salesData['graphData'][] = (float) $totalAmount;
        
            $pendingPartial = $value->whereIn('payment_status',['Pending','Partial',""]);


            $pendingTotal = $value->sum('total_amount');
            
            $pendingAmount = $pendingTotal ;
            $paid = 0;
            foreach ($pendingPartial as $key => $p) {
               $paid += \TaskHelper::getSalesPaymentAmount($p->id);
            }
            $pendingAmount -= $paid;

            $salesData['receiveAmount'] += $pendingAmount;
            $salesData['dataWithCustomer'][] = [
                'customer'=>$value->first()->client,
                'amount'=>$pendingAmount
            ];
            

        
        }


        $dataWithCustomer = $salesData['dataWithCustomer'];

        array_multisort( array_column($dataWithCustomer, "amount"), SORT_ASC, $dataWithCustomer );

        $salesData['dataWithCustomer'] = $dataWithCustomer;
            



        return $salesData;
    }

    public function PurchaseData(){

        $orders  = PurchaseOrder::where('bill_date','>=',$this->start_date)
                        ->where('bill_date','<=',$this->end_date)
                        ->where('purchase_type','bills')
                        
                        ->get()
                        ->groupBy('supplier_id');
        $purchaseData = [
            'totalSales'=>0,
            'payAmount'=>0,
            'graphData'=>[],
            'dataWithCustomer'=>[],

        ];

        foreach ($orders as $key => $value) {
            $totalAmount = $value->sum('total');
            $purchaseData['totalSales'] += $totalAmount;
            
            $purchaseData['graphData'][] = (float) $totalAmount;
   
            $pendingPartial = $value->whereIn('payment_status',['Pending','Partial',""]);
            
            $paid = 0;

            $pendingTotal = $value->sum('total');

            foreach ($pendingPartial as $key => $p) {
                
                $paid += \TaskHelper::getPurchasePaymentAmountWithoutTds($p->id);
            }
            $pendingAmount = $pendingTotal ;
            $pendingAmount -= $paid;

            $purchaseData['payAmount'] += $pendingAmount;
            $purchaseData['dataWithCustomer'][] = [
                    'customer'=>$value->first()->client,
                    'amount'=>$pendingAmount
                ];

            $purchaseData['allDataWithCustomer'][] = [
                    'customer'=>$value->first()->client,
                    'amount'=>$totalAmount
                ];




        }

        
        $dataWithCustomer = $purchaseData['dataWithCustomer'];
        
        array_multisort( array_column($dataWithCustomer, "amount"), SORT_ASC, $dataWithCustomer );

        $purchaseData['dataWithCustomer'] = $dataWithCustomer;



        return $purchaseData;
    }


    public function ExpenseData(){

        $expenseData = [
            'totalSales'=>0,
            'graphData'=>[],

        ];

        $orders = \App\Models\Expense::where('date','>=',$this->start_date)
                        ->where('date','<=',$this->end_date)
                        ->get();

        foreach ($orders as $key => $value) {
            
            $expenseData['totalSales'] += $value->amount;
            $expenseData['graphData'][] = (float) $value->amount;
        }


        return $expenseData;

    }



    public function StockInventoryData(){



        $transaltions =  \App\Models\Product::select('products.name','products.price',\DB::raw('SUM(product_stock_moves.qty) as qty'))
                        ->leftjoin('product_stock_moves', 'products.id', '=', 'product_stock_moves.stock_id')
                        ->where('products.type','trading')
                        ->groupBy('products.id')
                        ->get();
       
        $stocksValue = $transaltions->where('qty','>','0');
        $totalStockValue = 0;
        foreach ($stocksValue as $key => $value) {
           
            $totalStockValue += $value->qty * $value->price;

        }


        $lowStocks = $transaltions->where('qty','<=','0');



        return ['stocksValue'=>$totalStockValue , 'lowStocks'=>$lowStocks];
    }
}
