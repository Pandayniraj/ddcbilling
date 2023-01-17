<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryRoute;
use App\Models\Client;
use Auth;
use Flash;

class DeliveryRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd('hello');
        $term= \Request::get('term');
        $deliveryroutes= DeliveryRoute::where('org_id', Auth::user()->org_id)
        ->when($term && $term != '', function ($query) use($term){
            return $query->where('route_name', 'LIKE', '%'.$term.'%')
            ->orWhere('route_code', 'LIKE', '%'.$term.'%');
        })
        ->paginate(25);
        $page_title= 'Delivery Routes';
        $page_description= 'List of Delivery Routes';
        return view('admin.deliveryroutes.index', compact('deliveryroutes','page_title','page_description'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = "Delivery Routes";
        $page_description = "Create Delivery Route";
        $distributor= Client::where('org_id', Auth::user()->org_id)->where('relation_type', 'distributor')->pluck('name', 'id');
        return view('admin.deliveryroutes.create', compact('page_title','page_description', 'distributor'));
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->all();
        $data['org_id']= Auth::user()->org_id;
        $data['user_id']= Auth::user()->id;
       DeliveryRoute::create($data);
       Flash::success('Delivery Route Created Successfully');
       return redirect(route('admin.deliveryroute'));
        //'
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
        // dd('hello');
        $finddeliveryroute = DeliveryRoute::find($id);
        $page_title = "Delivery Routes";
        $page_description = "Update Delivery Route";
        $distributor= Client::where('org_id', Auth::user()->org_id)->where('relation_type', 'distributor')->pluck('name', 'id');
        return view('admin.deliveryroutes.edit', compact('page_title','finddeliveryroute','page_description', 'distributor'));
        //
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

        $data['route_name']= $request->route_name;
        $data['route_code']= $request->route_code;
        $data['distributor_id']= $request->distributor_id;
        $data['route_name']= $request->route_name;
        $data['org_id']= Auth::user()->org_id;
        $data['user_id']= Auth::user()->id;
       DeliveryRoute::where('id', $id)->update($data);
       Flash::success('Delivery Route Updated Successfully');
       return redirect(route('admin.deliveryroute'));
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DeliveryRoute::where('id', $id)->delete();
        Flash::success('Delivery Route deleted Successfully');
        return redirect(route('admin.deliveryroute'));
        //
    }
    public function getModalDelete($id)
    {
        $error = null;
        $modal_title = 'Delete Delivery Route';
        $modal_body = "Are you sure you want to detele this delivery route ?";
        $modal_route = route('admin.deliveryroute.delete', ['id' => $id]);

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }
}
