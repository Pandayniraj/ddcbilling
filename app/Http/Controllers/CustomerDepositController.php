<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flash;

class CustomerDepositController extends Controller
{
    public function index()
    {
        $page_title="Deposit Amount";
        $page_description="Deposit Amount";
       $data['id']=\Request::get('id');
       $data['startdate']=\Request::get('startdate');
       $data['enddate']=\Request::get('enddate');
       $data['type']=\Request::get('type');

        $customerdeposits=\App\Models\CustomerDeposit::
        where('client_id',\Request::get('id'))
        ->when(\Request::get('startdate') && \Request::get('startdate')!="", function ($q) {
            return $q->where('created_at','<=',\Request::get('startdate'));
        })
        -> when(\Request::get('enddate') && \Request::get('enddate')!="", function ($q) {
            return $q->where('created_at','>=',\Request::get('enddate'));
        })
        -> when(\Request::get('type') && \Request::get('type')!="", function ($q) {
            return $q->where('type',\Request::get('type'));
        })
        ->paginate(30);
        return view('admin.customerdeposits.index',compact('customerdeposits','page_title','page_description','data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title="Create Deposit Amount";
        $page_description="Create Deposit Amount";
        $id=\Request::get('id');
        return view('admin.customerdeposits.create',compact('id','page_title','page_description'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $data=$request->all();
        $this->validate($request, ['amount'  => 'required']);
        $attributes['user_id'] = \Auth::user()->id;
        $attributes['date'] = $request->date;
        $attributes['remarks'] =$request->remarks;
        $attributes['type'] = "Deposit";
        $attributes['client_id'] = $request->id;
        $attributes['amount'] =$request->amount;
        $attributes['closing'] =(float)(\App\Models\CustomerDeposit::where('client_id',$request->id)->latest()->first()->closing??0) + (float)$request->amount;

        \App\Models\CustomerDeposit::create($attributes);

        Flash::success('Customer Deposit Added Successfully');
        return redirect(url('/admin/customerdeposits/index?id='.$request->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $page_title="Create Deposit Amount";
        $page_description="Create Deposit Amount";
        $products=\App\Models\Product::pluck('name','id');
        $brands=\App\Models\Brand::pluck('name','id');
        $customerdeposit=\App\Models\customerdeposits::find($id);

        return view('admin.customerdeposits.edit',compact('customerdeposit','products','brands','page_title','page_description'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, ['name'  => 'required|unique:products',]);
        $attributes['org_id'] = \Auth::user()->org_id;
        $attributes['created_by'] = \Auth::user()->id;
        if ($request->file('product_image')) {
            $stamp = time();
            $file = $request->file('product_image');
            $destinationPath = public_path() . '/products/';
            if (!\File::isDirectory($destinationPath)) {
                \File::makeDirectory($destinationPath, 0777, true, true);
            }
            $filename = $file->getClientOriginalName();
            $request->file('product_image')->move($destinationPath, $stamp . '_' . $filename);
            $attributes['product_image'] = $stamp . '_' . $filename;
        }
        $attributes['name']=$request->name;
        $attributes['item_type']="customerdeposit";
        $attributes['brand_id']=$request->brand_id;
        $attributes['parent_product_id']=$request->product_id;
        $attributes['price']=$request->price;
        $attributes['alert_qty']=$request->qty;
        $attributes['description']=$request->description;

        \App\Models\Product::where('id',$id)->update($attributes);

        Flash::success('Customer Deposit Update Successfully');
        return redirect(route('admin.customerdeposits.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        \App\Models\Product::where('id',$id)->delete();
        Flash::success('Customer Deposit Delete Successfully');
        return redirect(route('admin.customerdeposits.index'));
    }
}
