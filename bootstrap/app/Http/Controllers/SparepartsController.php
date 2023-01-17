<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flash;

class SparepartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title="Spare Parts";
        $page_description="Spare Parts";
        $brands=\App\Models\Brand::pluck('name','id');
        $products=\App\Models\Product::where('item_type','product')->pluck('name','id');
        $spareparts=\App\Models\Product::
        when(\Request::get('brand_id') && \Request::get('brand_id')!="", function ($q) {
            return $q->where('brand_id', \Request::get('brand_id'));
        })
        -> when(\Request::get('product_id') && \Request::get('product_id')!="", function ($q) {
            return $q->where('product_id', \Request::get('product_id'));
        })
        -> when(\Request::get('name') && \Request::get('name')!="", function ($q) {
            return $q->where('name',LIKE,'%'.name.'%');
        })
        ->where('item_type','sparepart')
        ->orderby('id','desc')
        ->paginate(30);
        return view('admin.spareparts.index',compact('brands','products','spareparts','page_title','page_description'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title="Create Spare Parts";
        $page_description="Create Spare Parts";
        $products=\App\Models\Product::where('item_type','product')->pluck('name','id');
        $brands=\App\Models\Brand::pluck('name','id');


        return view('admin.spareparts.create',compact('products','brands','page_title','page_description'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // $data=$request->all();
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
        $attributes['item_type']="sparepart";
        $attributes['brand_id']=$request->brand_id;
        $attributes['parent_product_id']=$request->product_id;
        $attributes['price']=$request->price;
        $attributes['alert_qty']=$request->qty;
        $attributes['description']=$request->description;

        \App\Models\Product::create($attributes);

        Flash::success('Spare Part Added Successfully');
        return redirect(route('admin.spareparts.index'));
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
        $page_title="Create Spare Parts";
        $page_description="Create Spare Parts";
        $products=\App\Models\Product::pluck('name','id');
        $brands=\App\Models\Brand::pluck('name','id');
        $sparepart=\App\Models\Spareparts::find($id);

        return view('admin.spareparts.edit',compact('sparepart','products','brands','page_title','page_description'));
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
        $attributes['item_type']="sparepart";
        $attributes['brand_id']=$request->brand_id;
        $attributes['parent_product_id']=$request->product_id;
        $attributes['price']=$request->price;
        $attributes['alert_qty']=$request->qty;
        $attributes['description']=$request->description;

        \App\Models\Product::where('id',$id)->update($attributes);

        Flash::success('Spare Part Update Successfully');
        return redirect(route('admin.spareparts.index'));
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
        Flash::success('Spare Part Delete Successfully');
        return redirect(route('admin.spareparts.index'));
    }
}
