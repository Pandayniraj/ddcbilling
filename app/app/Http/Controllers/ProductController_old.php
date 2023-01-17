<?php

namespace App\Http\Controllers;

use App\Models\Audit as Audit;
use App\Models\Product as Course;
use App\Models\Role as Permission;
use App\Models\StockAdjustmentDetail;
use App\Models\StockMove;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * THIS CONTROLLER IS USED AS PRODUCT CONTROLLER.
 */
class ProductController extends Controller
{
    /**
     * @var Course
     */
    private $course;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @param Course $course
     * @param Permission $permission
     * @param User $user
     */
    public function __construct(Course $course, Permission $permission)
    {
        parent::__construct();
        $this->course = $course;
        $this->permission = $permission;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-index'));

        $courses = $this->course->select('products.*')->where(function ($query) {
            $terms = \Request::get('term');
            if ($terms)
                return $query->where('products.name', 'LIKE', '%' . $terms . '%');
        })->where(function($query){

            $product_cat = \Request::get('product_cat');

            if($product_cat){

                return $query->where('product_categories.id',$product_cat);
            }


        })->where(function($query){

            if(\Request::get('alert_qty') == 'neg'){
                $posCountStock = \App\Models\StockMove::select('stock_id',\DB::raw('SUM(product_stock_moves.qty) as qty'))
                                ->groupBy('stock_id')
                                ->get();
                $posCountStock = $posCountStock->where('qty','>','0')->pluck('stock_id')->toArray();


                return $query->whereNotIn('products.id',$posCountStock)
                        ->where('type','trading');
            }

        })
        ->leftjoin('product_categories','products.category_id','=','product_categories.id')
        ->orderBy('id', 'desc')
            ->where('products.org_id',auth()->user()->org_id)
        ->groupBy('id')
        ->paginate(30);

        $page_title = 'Products & Inventory';

        $page_description = trans('admin/courses/general.page.index.description');


        $productCategory = \App\Models\ProductCategory::pluck('name','id');
        //dd($transations);

        return view('admin.products.index', compact('courses', 'page_title', 'page_description','productCategory'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $course = $this->course->find($id);

        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-show', ['name' => $course->name]));

        $page_title = trans('admin/courses/general.page.show.title'); // "Admin | Course | Show";
        $page_description = trans('admin/courses/general.page.show.description'); // "Displaying course: :name";

        return view('admin.products.show', compact('course', 'page_title', 'page_description'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $page_title = trans('admin/courses/general.page.create.title'); // "Admin | Course | Create";
        $page_description = trans('admin/courses/general.page.create.description'); // "Creating a new course";

        $course = new \App\Models\Product();
        $perms = $this->permission->all();
        $categories = \App\Models\ProductCategory::orderBy('name', 'ASC')->where('org_id', Auth::user()->org_id)->pluck('name', 'id');

        $product_unit = \App\Models\ProductsUnit::pluck('name', 'id');
        $supplier = \App\Models\Client::where('relation_type', 'supplier')->pluck('name', 'id');

        //dd($product_unit);
        if (\Request::ajax()) {
            return view('admin.products.modals.create', compact('course', 'perms', 'categories', 'product_unit', 'supplier'));
        }

        return view('admin.products.create', compact('course', 'perms', 'page_title', 'page_description', 'categories', 'product_unit', 'supplier'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if (\Request::ajax()) {
            $validator = \Validator::make($request->all(), [
                'name'  => 'required|unique:products',
            ]);
            if ($validator->fails()) {
                return ['error' => $validator->errors()];
            }
        }
        $this->validate($request, ['name'  => 'required|unique:products',]);

        $attributes = $request->all();
        $attributes['org_id'] = Auth::user()->org_id;


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

        // dd($attributes);

        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-store', ['name' => $attributes['name']]));

        $course = $this->course->create($attributes);

        if ($request->is_fixed_assets) {
            $this->postledgers($course);
        }
        if (\Request::ajax()) {
            return ['status' => 'success', 'lastcreated' => $course];
        }
        Flash::success(trans('admin/courses/general.status.created')); // 'Course successfully created');

        return redirect('/admin/products');
    }

    /**
     * @param $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $course = $this->course->find($id);
        $categories = \App\Models\ProductCategory::orderBy('name', 'ASC')->where('org_id', Auth::user()->org_id)->pluck('name', 'id');

        //dd($categories);

        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-edit', ['name' => $course->name]));

        $page_title = trans('admin/courses/general.page.edit.title'); // "Admin | Course | Edit";
        $page_description = trans('admin/courses/general.page.edit.description', ['name' => $course->name]); // "Editing course";

        $transations = \App\Models\StockMove::where('product_stock_moves.stock_id', $id)
            ->leftjoin('products', 'products.id', '=', 'product_stock_moves.stock_id')
            ->leftjoin('product_location', 'product_location.id', '=', 'product_stock_moves.location')
            ->select('product_stock_moves.*', 'products.name', 'product_location.location_name')
            ->orderBy('product_stock_moves.tran_date', 'DESC')
            ->get()
            ->groupBy('trans_type');





        $locData = DB::table('product_location')->get();

        $loc = DB::table('product_location')->get();

        $loc_name = [];
        foreach ($loc as $value) {
            $loc_name[$value->id] = $value->location_name;
        }

        $loc_name = $loc_name;

        if (!$course->isEditable() && !$course->canChangePermissions()) {
            abort(403);
        }

        $product_unit = \App\Models\ProductsUnit::pluck('name', 'id');
        $product_model = \App\Models\ProductModel::where('product_id', $id)->get();
        $product_serial_num = [];
        $supplier = \App\Models\Client::where('relation_type', 'supplier')->pluck('name', 'id');

        return view('admin.products.edit', compact('course', 'product_unit', 'page_title', 'locData', 'loc_name', 'page_description', 'transations', 'categories', 'product_serial_num', 'product_model', 'supplier'));
    }

    private function postledgers($products)
    {
        if (!$products->ledger_id) {
            $ledger_id = \TaskHelper::PostLedgers(
                $products->name,
                \FinanceHelper::get_ledger_id('FIXED_ASSETS_LEDGER')
            );
            $products->update(['ledger_id' => $ledger_id]);
        }
    }

    public function stocks_by_location()
    {
        $page_title = 'Stock Feeds';
        $page_description = 'counts';

        $location = \App\Models\ProductLocation::pluck('location_name', 'id')->all();

        //dd($location);

        return view('admin.products.stocksbylocation', compact('page_title', 'page_description', 'location'));
    }

    public function stocks_by_location_post(Request $request)
    {
        $page_title = 'Stock By Location';
        $page_description = 'counts';

        $transations = DB::table('product_stock_moves')
            // ->where('product_stock_moves.stock_id',$id)
            ->leftjoin('products', 'products.id', '=', 'product_stock_moves.stock_id')
            ->leftjoin('product_location', 'product_location.id', '=', 'product_stock_moves.location')
            ->select('product_stock_moves.*', 'products.name', 'product_location.location_name')
            ->orderBy('product_stock_moves.tran_date', 'DESC')
            ->where('location', $request->location_id)
            ->get();

        $current_location = $request->location_id;

        $location = \App\Models\ProductLocation::pluck('location_name', 'id')->all();

        return view('admin.products.stocksbylocation', compact('page_title', 'page_description', 'location', 'transations', 'location', 'current_location'));
    }

    public function stocks_count()
    {
        $page_title = 'Stock Feeds';
        $page_description = 'counts';

        $transations = DB::table('product_stock_moves')
            // ->where('product_stock_moves.stock_id',$id)
            ->leftjoin('products', 'products.id', '=', 'product_stock_moves.stock_id')
            ->leftjoin('product_location', 'product_location.id', '=', 'product_stock_moves.location')
            ->select('product_stock_moves.*', 'products.name', 'product_location.location_name', 'products.id as pid')
            ->orderBy('product_stock_moves.tran_date', 'DESC')
            ->paginate(20);

        return view('admin.products.stockscount', compact('page_title', 'page_description', 'transations'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, ['name'          => 'required|unique:products,name,' . $id,]);
        $course = $this->course->find($id);

        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-update', ['name' => $course->name]));

        $attributes = $request->all();
        $attributes['org_id'] = Auth::user()->org_id;




        if ($request->file('product_image')) {
            $stamp = time();
            $destinationPath = public_path() . '/products/';
            //file_upload
            $file = \Request::file('product_image');
            if (!\File::isDirectory($destinationPath)) {
                \File::makeDirectory($destinationPath, 0777, true, true);
            }
            //base_path() is proj root in laravel
            $filename = $file->getClientOriginalName();
            \Request::file('product_image')->move($destinationPath, $stamp . $filename);

            //create second image as big image and delete original
            $image = \Image::make($destinationPath . $stamp . $filename)
                ->save($destinationPath . $stamp . $filename);

            $attributes['product_image'] = $stamp . $filename;
        }

        if ($course->isEditable()) {
            $course->update($attributes);
            if ($request->is_fixed_assets) {
                $this->postledgers($course);
            }
        }

        Flash::success(trans('admin/courses/general.status.updated')); // 'Course successfully updated');

        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $course = $this->course->find($id);

        if (!$course->isdeletable()) {
            abort(403);
        }

        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-destroy', ['name' => $course->name]));

        $course->delete($id);
        \App\Models\ProductModel::where('product_id', $id)->delete();
        \App\Models\ProductSerialNumber::where('product_id', $id)->delete();
        Flash::success(trans('admin/courses/general.status.deleted')); // 'Course successfully deleted');

        return redirect('/admin/products');
    }

    /**
     * Delete Confirm.
     *
     * @param   int   $id
     * @return  View
     */
    public function getModalDelete($id)
    {
        $error = null;

        $course = $this->course->find($id);

        if (!$course->isdeletable()) {
            abort(403);
        }

        $modal_title = trans('admin/courses/dialog.delete-confirm.title');

        $course = $this->course->find($id);
        $modal_route = route('admin.products.delete', ['courseId' => $course->id]);

        $modal_body = trans('admin/courses/dialog.delete-confirm.body', ['id' => $course->id, 'name' => $course->name]);

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function enable($id)
    {
        $course = $this->course->find($id);

        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-enable', ['name' => $course->name]));

        $course->enabled = true;
        $course->save();

        Flash::success(trans('admin/courses/general.status.enabled'));

        return redirect('/admin/products');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function disable($id)
    {
        //TODO: Should we protect 'admins', 'users'??

        $course = $this->course->find($id);

        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-disabled', ['name' => $course->name]));

        $course->enabled = false;
        $course->save();

        Flash::success(trans('admin/courses/general.status.disabled'));

        return redirect('/admin/products');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function enableSelected(Request $request)
    {
        $chkCourses = $request->input('chkCourse');

        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-enabled-selected'), $chkCourses);

        if (isset($chkCourses)) {
            foreach ($chkCourses as $course_id) {
                $course = $this->course->find($course_id);
                $course->enabled = true;
                $course->save();
            }
            Flash::success(trans('admin/courses/general.status.global-enabled'));
        } else {
            Flash::warning(trans('admin/courses/general.status.no-course-selected'));
        }

        return redirect('/admin/products');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function disableSelected(Request $request)
    {
        //TODO: Should we protect 'admins', 'users'??

        $chkCourses = $request->input('chkCourse');

        Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-disabled-selected'), $chkCourses);

        if (isset($chkCourses)) {
            foreach ($chkCourses as $course_id) {
                $course = $this->course->find($course_id);
                $course->enabled = false;
                $course->save();
            }
            Flash::success(trans('admin/courses/general.status.global-disabled'));
        } else {
            Flash::warning(trans('admin/courses/general.status.no-course-selected'));
        }

        return redirect('/admin/products');
    }

    /**
     * @param Request $request
     * @return array|static[]
     */
    public function searchByName(Request $request)
    {
        $return_arr = null;

        $query = $request->input('query');

        $courses = $this->course->where('name', 'LIKE', '%' . $query . '%')->get();
        foreach ($courses as $course) {
            $id = $course->id;
            $name = $course->name;
            $email = $course->email;

            $entry_arr = ['id' => $id, 'text' => "$name ($email)"];
            $return_arr[] = $entry_arr;
        }

        return $return_arr;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getInfo(Request $request)
    {
        $id = $request->input('id');
        $course = $this->course->find($id);

        return $course;
    }

    public function stock_adjustment(Request $request)
    {

        $page_title = 'Stock Adjustment';
        $page_description = 'Add stock or remove stocks fr example damaged or others';

        $reason=$request->reason?$request->reason:'';
        $startdate=$request->startdate?$request->startdate:'';
        $enddate=$request->enddate?$request->enddate:'';
        $store=$request->store?$request->store:'';

        $stockadjustment = \App\Models\StockAdjustment::orderBy('id', 'desc')
                            ->when($reason, function ($q) use ($reason) {
                                $q->where('reason', $reason);
                            })
                            ->when($startdate && $enddate, function ($q) use ($startdate, $enddate) {
                                $q->where('transaction_date', '>=', $startdate);
                                $q->where('transaction_date', '<=', $enddate);
                            })
                            ->when($store, function ($q) use ($store) {
                                $q->where('store_id', $store);
                            })
                            ->with('detail')
                            ->get();
                            
        $stores = \App\Models\PosOutlets::select('name', 'id')->get();
        $reasons  = \App\Models\AdjustmentReason::select('name', 'id')->get();

        return view('admin.products.adjust.adjust', compact('page_title', 'page_description', 'stockadjustment', 'stores', 'reasons'));
    }

     public function stock_adjustment_create()
    {


        $page_title = 'Stock Adjustment Create';
        $page_description = 'Add stock or remove stocks fr example damaged or others';

        $stores = \App\Models\Store::pluck('name', 'id')->all();

        $account_ledgers = \App\Models\COALedgers::where('group_id', env('COST_OF_GOODS_SOLD'))->pluck('name', 'id')->all();

        $units = \App\Models\ProductsUnit::select('id', 'name', 'symbol')->get();
        $products = \App\Models\Product::where('enabled', '1')
        ->where(function ($q){
            $q->where('parent_product_id',0);
            $q->orWhereNull('parent_product_id');
        })
        ->get();
        $reasons  = \App\Models\AdjustmentReason::pluck('name', 'id')->all();
        $costcenter = \App\Models\PosOutlets::pluck('name', 'id')->all();
        $users = \App\User::pluck('username', 'id')->all();
        $departments = \App\Models\Department::pluck('deptname', 'departments_id')->all();


        return view('admin.products.adjust.create', compact('page_title', 'page_description', 'stores', 'account_ledgers', 'units', 'products', 'reasons','costcenter','users','departments'));
    }

public function stock_adjustment_store(Request $request)
    {
        DB::beginTransaction();
        $attributes = $request->all();

        $attributes['tax_amount'] = $request->taxable_tax;
        $attributes['total_amount'] = $request->final_total;
        $attributes['transaction_date'] = $request->transaction_date;

        $stock_adjustment = \App\Models\StockAdjustment::create($attributes);

        $product_id = $request->product_id;
        $price = $request->price;
        $quantity = $request->quantity;
        $tax_amount = $request->tax_amount;
        $tax_rate = $request->tax_rate;
        $total = $request->total;
        $units = $request->units;


        $total_qty=0;
        foreach ($quantity as $qty){
            $total_qty+=$qty;
        }
        $stockmaster = new \App\Models\StockMaster();
        $stockmaster->stock_entry_id = 1;
        $stockmaster->tran_date = $request->transaction_date;
        $stockmaster->modules = "Stock Adjustments";
        $stockmaster->comment =  " From Stock Adjustment";
        $stockmaster->reason_id = $stock_adjustment->comments;
        $stockmaster->total_value = $stock_adjustment->total_amount;
        $stockmaster->total_qty = $total_qty;
        $stockmaster->store_id = $request->store_id;
        $stockmaster->reason_id = $request->reason;
        $stockmaster->module_id = $stock_adjustment->id;
        $stockmaster->active = 1;
        $stockmaster->save();


//        $stockmaster = new \App\Models\StockMaster();
//        $stockmaster->stock_entry_id = 1;
//        $stockmaster->tran_date = $request->transaction_date;
//        $stockmaster->modules = "Stock Adjustments";
//        $stockmaster->comment =  " From Stock Adjustment";
//        $stockmaster->reason_id = $request->reason;
//        $stockmaster->total_value = $request->final_total;
//        $stockmaster->save();
        $request_reason = \App\Models\AdjustmentReason::find($request->reason);

        foreach ($product_id as $key => $value) {
            if ($value != '') {

                $detail = new \App\Models\StockAdjustmentDetail();
                $detail->adjustment_id = $stock_adjustment->id;
                $detail->product_id = $product_id[$key];
                $detail->price = $price[$key];
                $detail->qty = $quantity[$key];
                $detail->total = $total[$key];
                $detail->tax_amount = $tax_amount[$key];
                $detail->tax_rate = $tax_rate[$key];
                $detail->unit = $units[$key];
                $detail->save();



                if ($request_reason) {

                    $stockMove = new StockMove();
                    $stockMove->stock_id = $product_id[$key];
                    $stockMove->master_id = $stockmaster->id;
                    $stockMove->order_no = $stock_adjustment->id;
                    $stockMove->tran_date = $request->transaction_date;
                    $stockMove->user_id = \Auth::user()->id;


                    $stockMove->trans_type = $request_reason->trans_type;
                    $stockMove->reference = $request_reason->name . '_' . $stock_adjustment->id;
                    $stockMove->order_reference =  $stock_adjustment->id;

                    if ($request_reason->reason_type == 'positive') {

                        $stockMove->qty =  $quantity[$key]  * \StockHelper::getUnitPrice($detail->unit);
                    } else {

                        $stockMove->qty = '-' . $quantity[$key]  * \StockHelper::getUnitPrice($detail->unit);
                    }


                    $stockMove->transaction_reference_id = $stock_adjustment->id;
                    $stockMove->store_id = $request->store_id;

                    $stockMove->price = $price[$key];
                    $stockMove->save();
                }
            }
        }
        if ($request_reason->reason_type == 'negative')
            $this->updateEntries($stock_adjustment->id);

        DB::commit();

        Flash::success('Stock Adjustment Done Successfully');

        return redirect('/admin/product/stock_adjustment');
    }

    public function stock_adjustment_edit($id)
    {
        $page_title = 'Stock Adjustment Edit';
        $page_description = 'Add stock or remove stocks fr example damaged or others';

        $stock_adjustment = \App\Models\StockAdjustment::find($id);

        $stock_adjustment_details = \App\Models\StockAdjustmentDetail::where('adjustment_id', $stock_adjustment->id)->get();
        //dd($stock_adjustment_details);

        $stores = \App\Models\Store::pluck('name', 'id')->all();

        $account_ledgers = \App\Models\COALedgers::where('group_id', env('COST_OF_GOODS_SOLD'))->pluck('name', 'id')->all();

        $units = \App\Models\ProductsUnit::select('id', 'name', 'symbol')->get();
        $products = \App\Models\Product::where('enabled', '1')
        ->where(function ($q){
            $q->where('parent_product_id',0);
            $q->orWhereNull('parent_product_id');
        })->get();

        $reasons  = \App\Models\AdjustmentReason::pluck('name', 'id')->all();
        $costcenter = \App\Models\PosOutlets::pluck('name', 'id')->all();
        $users = \App\User::pluck('username', 'id')->all();
        $departments = \App\Models\Department::pluck('deptname', 'departments_id')->all();
        return view('admin.products.adjust.edit', compact('departments','users','costcenter','page_title', 'page_description', 'stores', 'account_ledgers', 'units', 'products', 'stock_adjustment', 'stock_adjustment_details', 'reasons'));
    }

    public function stock_adjustment_update(Request $request, $id)
    {
         DB::beginTransaction();
        $old_reason_name = \App\Models\StockAdjustment::find($id)->adjustmentreason->name;

        $old_trans_type = \App\Models\StockAdjustment::find($id)->adjustmentreason->trans_type;

        $attributes = $request->all();
        $attributes['tax_amount'] = $request->taxable_tax;
        $attributes['total_amount'] = $request->final_total;

        $stock_adjustment = \App\Models\StockAdjustment::find($id)->update($attributes);

        $purchasedetails = StockAdjustmentDetail::where('adjustment_id', $id)->get();

        foreach ($purchasedetails as $pd) {
            $stockmove = \App\Models\StockMove::where('stock_id', $pd->product_id)->where('order_no', $id)->where('trans_type', $old_trans_type)->where('reference', $old_reason_name . '_' . $id)->delete();
        }

        \App\Models\StockAdjustmentDetail::where('adjustment_id', $id)->delete();

        $product_id = $request->product_id;
        $price = $request->price;
        $quantity = $request->quantity;
        $tax_amount = $request->tax_amount;
        $total = $request->total;
        $units = $request->units;

        $total_qty=0;
        foreach ($quantity as $qty){
            $total_qty+=$qty;
        }

        $stockmaster_attr['stock_entry_id'] = 1;
        $stockmaster_attr['modules'] = "Stock Adjustments";
        $stockmaster_attr['comment'] =  " From Stock Adjustment";
        $stockmaster_attr['total_value'] = $stock_adjustment->total_amount;
        $stockmaster_attr['total_qty'] = $total_qty;
        $stockmaster_attr['store_id'] = $stock_adjustment->location_id;
        $stockmaster_attr['reason_id'] = $request->reason;
        $stockmaster_attr['module_id'] = $stock_adjustment->id;
        $stockmaster_attr['active'] = 1;

        $stockmaster=\App\Models\StockMaster::where('modules','Stock Adjustments')
            ->where('module_id',$id)->first();
//        dd($stock_adjustment);
        $stockmaster->update($stockmaster_attr);
        StockMove::where('master_id',$stockmaster->id)->delete();

        foreach ($product_id as $key => $value) {
            if ($value != '') {
                $detail = new \App\Models\StockAdjustmentDetail();
                $detail->adjustment_id = $id;
                $detail->product_id = $product_id[$key];
                $detail->price = $price[$key];
                $detail->qty = $quantity[$key];
                $detail->total = $total[$key];
                $detail->unit = $units[$key];
                $detail->save();

                $request_reason = \App\Models\AdjustmentReason::find($request->reason);

                if ($request_reason) {
                    $stockMove = new StockMove();
                    $stockMove->stock_id = $product_id[$key];
                    $stockMove->order_no = $id;
                    $stockMove->tran_date = \Carbon\Carbon::now();
                    $stockMove->user_id = Auth::user()->id;

                    $stockMove->trans_type = $request_reason->trans_type;
                    $stockMove->reference = $request_reason->name . '_' . $id;
                    if ($request_reason->reason_type == 'positive') {
                        $stockMove->qty = $quantity[$key];
                    } else {
                        $stockMove->qty = '-' . $quantity[$key];
                    }

                    $stockMove->transaction_reference_id = $id;
                    $stockMove->location = $request->location_id;
                    $stockMove->org_id = auth()->user()->org_id;

                    $stockMove->price = $price[$key];
                    $stockMove->save();
                }
            }
        }

        $product_id_new = $request->product_id_new;
        $price_new = $request->price_new;
        $quantity_new = $request->quantity_new;
        $tax_amount_new = $request->tax_amount_new;
        $total_new = $request->total_new;
        $units_new = $request->units_new;

        foreach ($product_id_new ?? [] as $key => $value) {
            if ($value != '') {
                $detail = new \App\Models\StockAdjustmentDetail();
                $detail->adjustment_id = $id;
                $detail->product_id = $product_id_new[$key];
                $detail->price = $price_new[$key];
                $detail->qty = $quantity_new[$key];
                $detail->total = $total_new[$key];
                $detail->unit = $units_new[$key];
                $detail->save();

                $request_reason = \App\Models\AdjustmentReason::find($request->reason);

                if ($request_reason) {
                    $stockMove = new StockMove();
                    $stockMove->stock_id = $product_id_new[$key];
                    $stockMove->order_no = $id;
                    $stockMove->tran_date = \Carbon\Carbon::now();
                    $stockMove->user_id = Auth::user()->id;

                    $stockMove->trans_type = $request_reason->trans_type;
                    $stockMove->reference = $request_reason->name . '_' . $id;

                    if ($request_reason->reason_type == 'positive') {
                        $stockMove->qty = $quantity_new[$key];
                    } else {
                        $stockMove->qty = '-' . $quantity_new[$key];
                    }

                    $stockMove->transaction_reference_id = $id;
                    $stockMove->location = $request->location_id;
                    $stockMove->org_id = auth()->user()->org_id;

                    $stockMove->price = $price[$key];
                    $stockMove->save();
                }
            }
        }

        $this->updateEntries($id);
         DB::commit();
        Flash::success('Stock Adjustment Updated Successfully');

        return redirect('/admin/product/stock_adjustment');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function stock_adjustment_destroy($id)
    {

        //dd($id);

        $old_reason_name = \App\Models\StockAdjustment::find($id)->adjustmentreason->name;

        $old_trans_type = \App\Models\StockAdjustment::find($id)->adjustmentreason->trans_type;

        $stock_adjustment = \App\Models\StockAdjustment::find($id)->delete();

        $purchasedetails = StockAdjustmentDetail::where('adjustment_id', $id)->get();

        foreach ($purchasedetails as $pd) {
            $stockmove = \App\Models\StockMove::where('stock_id', $pd->product_id)->where('order_no', $id)->where('trans_type', $old_trans_type)->where('reference', $old_reason_name . '_' . $id)->delete();
        }

        \App\Models\StockAdjustmentDetail::where('adjustment_id', $id)->delete();

        Flash::success('Stock Adjustment Destroyed');

        return redirect('/admin/product/stock_adjustment');
    }

    /**
     * Delete Confirm.
     *
     * @param   int   $id
     * @return  View
     */
    public function stock_adjustment_getModalDelete($id)
    {
        $error = null;

        $modal_title = 'Want to delete Stock Adjustment';

        $stock_adjustment = \App\Models\StockAdjustment::find($id);

        $stock_adjustment_details = \App\Models\StockAdjustmentDetail::where('adjustment_id', $stock_adjustment->id)->get();

        $modal_route = route('admin.products.stock_adjustment.delete', ['id' => $stock_adjustment->id]);

        $modal_body = 'Are you Sure to Delete This?';

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

 private function updateEntries($adj_id)
    {
        $stock_adjustment =  \App\Models\StockAdjustment::find($adj_id);

        $totalAmountBeforeTax = $stock_adjustment->total_amount;
        if ($stock_adjustment->entry_id && $stock_adjustment->entry_id != '0') {
            //update the ledgers
            $attributes['entrytype_id'] = '16'; //Journal
            $attributes['tag_id'] = '2'; //Material cost
            $attributes['user_id'] = \Auth::user()->id;
            $attributes['org_id'] = \Auth::user()->org_id;
//            $attributes['number'] = \FinanceHelper::get_last_entry_number($attributes['entrytype_id']);
            $attributes['bill_no'] = '';
            $attributes['date'] = $stock_adjustment->transaction_date;
            $attributes['dr_total'] = $totalAmountBeforeTax;
            $attributes['cr_total'] = $totalAmountBeforeTax;
            $attributes['source'] = 'AUTO_ADJUSTMENT';
            $attributes['currency'] = 'NPR';
            $attributes['notes'] = "ADJUSTMENT ID No # ". $adj_id;
            $entry = \App\Models\Entry::find($stock_adjustment->entry_id);
            $entry->update($attributes);

            Entryitem::where('entry_id',$entry->id)->delete();
        } else {
            // dd( \App\Models\Client::find($purchaseorder->supplier_id));
            //create the new entry items
            $attributes['entrytype_id'] = '16'; //Journal
            $attributes['tag_id'] = '2'; //Adjustment
            $attributes['user_id'] = \Auth::user()->id;
            $attributes['org_id'] = \Auth::user()->org_id;
            $attributes['bill_no'] = '';
            $attributes['date'] = $stock_adjustment->transaction_date;
            $attributes['dr_total'] = $totalAmountBeforeTax;
            $attributes['cr_total'] = $totalAmountBeforeTax;
            $attributes['source'] = 'AUTO_ADJUSTMENT';
            $attributes['fiscal_year_id'] = \FinanceHelper::cur_fisc_yr()->id;
            $attributes['currency'] = 'NPR';
            $attributes['notes'] = "Adjustment ID No # ". $stock_adjustment->id;
            $type = \App\Models\Entrytype::find(5);
            $attributes['number'] = \TaskHelper::generateId($type);

            $entry = \App\Models\Entry::create($attributes);
        }

        // Debitte to Bank or cash account that we are already in
        $order_product_type  = \App\Models\StockAdjustmentDetail::where('adjustment_id', $stock_adjustment->id)
        ->select('products.product_type_id')
        ->leftJoin('products','products.id','=','stock_adjustment_details.product_id')
        ->distinct('product_type_id')->get();
        foreach ($order_product_type as $opt) {
            if ($opt->product_type_id) {

                $purchase_ledger_id = \App\Models\ProductTypeMaster::find($opt->product_type_id)->purchase_ledger_id;
                $cogs_ledger_id = \App\Models\ProductTypeMaster::find($opt->product_type_id)->cogs_ledger_id;
                $product_type_total_amount = \App\Models\StockAdjustmentDetail::where('adjustment_id', $stock_adjustment->id)
                ->whereHas('product',function ($q) use ($opt) {
                    $q->where('product_type_id', $opt->product_type_id);
                })
                ->sum(\DB::raw('total'));
//                    dd($product_type_total_amount);

                $sub_amount = new \App\Models\Entryitem();
                $sub_amount->entry_id = $entry->id;
                $sub_amount->dc = 'D';
                $sub_amount->user_id = \Auth::user()->id;
                $sub_amount->org_id = \Auth::user()->org_id;
                $sub_amount->ledger_id = $cogs_ledger_id;
                $sub_amount->amount = $product_type_total_amount;
                $sub_amount->narration = 'Being purchase issued';
                $sub_amount->save();

                $cogs = new \App\Models\Entryitem();
                $cogs->entry_id = $entry->id;
                $cogs->dc = 'C';
                $cogs->user_id = \Auth::user()->id;
                $cogs->org_id = \Auth::user()->org_id;
                $cogs->ledger_id = $purchase_ledger_id;
                $cogs->amount = $product_type_total_amount;
                $cogs->narration = 'Being purchase issued';
                $cogs->save();
            }
        }

        //now update entry_id in income row
        $stock_adjustment->update(['entry_id' => $entry->id]);

        return 0;
    }

    public function stocks_entries()
    {
        $page_title = 'Stock Entries Main';
        $page_description = 'feed';

        $transations = \App\Models\StockMaster::orderBy('tran_date', 'DESC')
        ->paginate(40);

        return view('admin.products.stockentries', compact('page_title', 'page_description', 'transations'));
    }


    //Barcode features

    public function promotionCreate($id)
    {

        return view('admin.products.promotionmodal', compact('id'));
    }


    public function barcodeCreate($id)
    {
        $products = \App\Models\Product::find($id);
        $page_title = "Admin | Products | Barcode | Print";
        $page_description = "Lits of products barcode";

        return view('admin.products.barcodecreate', compact('products', 'page_title', 'id'));
    }

    public function barcodePost(Request $request, $id)
    {
        //$products = \App\Models\Product::find($id);
        $page_title = "Admin | Products | Barcode | Print";
        $page_description = "Lits of products barcode";

        $requests = $request->all();

        $products_all = $request->product;
        $quantity = $request->quantity;

        ///dd($products);


        return view('admin.products.barcodecreate', compact( 'page_title',  'products_all', 'quantity', 'requests', 'id'));
    }

    public function getPrintProduct(Request $request)
    {

        $attributes = $request->all();

        $products = \App\Models\Product::orderBy('id', 'desc')->Where('name', $request->product_name)->first();

        if (count($products) > 0) {

            $data = '<tr>
                    <td><input name="product[]" type="hidden" value="' . $products->id . '">' . $products->name . ' (' . $products->product_code . ')</td>
                    <td><input class="form-control quantity " name="quantity[]" type="number" value="100"  onclick="this.select();"></td>
                    <td></td>
                    <td class="text-center"> <a href="javascript::void(1);" style="width: 10%;" readonly>
                                <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable" style="float: right; color: #fff;"></i>
                    </a>
                    </td>
                </tr>';
        } else {

            $data = 0;
        }

        return ['purchasedetailinfo' => $data];
    }


    public function product_statement(Request $request){

        $page_title = 'Product Statement';
        $page_description = 'Search product to find stock ledger statement';

        $products = \App\Models\Product::orderBy('ordernum')->where('enabled', '1')
            ->where('org_id', Auth::user()->org_id)->orderBy('name', 'ASC')
            ->pluck('name', 'id');

        $current_product = $request->product_id;



        $transations = \App\Models\StockMove::where(function($query) use ($current_product){

            return $query->where('stock_id',$current_product);
        })->where(function($query){

            $start_date = \Request::get('start_date');
            $end_date = \Request::get('end_date');

            if($start_date && $end_date){

                return $query->whereBetween('tran_date',[$start_date,$end_date]);


            }



        })

        ->orderBy('id');

        $isExcel = false;
        if($request->submit && $request->submit == 'excel' ){
            $transations = $transations->get();
            $view = view('admin.products.product-statement',compact('transations','isExcel'));
            return \Excel::download(new \App\Exports\ExcelExportFromView($view), 'product_statement.xlsx');

        }
        $transations = $transations->paginate(50);

        // ->paginate(50);

         $isExcel = false;


        return view('admin.products.statement', compact('transations','page_description','page_title','products','current_product','transations','isExcel'));

    }


    public function multipledelete(Request $request){

       $ids = $request->chkCourse;
       Audit::log(Auth::user()->id, trans('admin/courses/general.audit-log.category'), trans('admin/courses/general.audit-log.msg-destroy', ['name' => 'Deleted multiple products' ]));
        try{

            $this->course->whereIn('id',$ids)->delete();
            \App\Models\ProductModel::whereIn('product_id', $ids)->delete();
            \App\Models\ProductSerialNumber::whereIn('product_id', $ids)->delete();
            Flash::success(trans('admin/courses/general.status.deleted'));


        }catch(\Exception $e){

            Flash::error("The selected Products Are Related With Invoice");
        }



        return redirect('/admin/products');
    }
    public function inportExportView(Request $request)
    {
        $page_title = 'Import Export Budget';
        $page_description = 'Import Export the Budget Excel File';

        return view('admin.products.importExport', compact('page_title', 'page_description'));
    }

    public function int_purch($id)
    {
       $page_description = "Purchase Description";
       $int_purch = \App\Models\ProductInternationPurchase::where('product_id',$id)->first();
       $product_name = \App\Models\Product::where('id',$id)->first()->name;
       return view('admin.products.internation_purchase_create',compact('page_description','int_purch','id','product_name'));
    }


    public function int_purch_update(Request $request,$id)
    {
       $attributes = $request->all();
       $int_purch = \App\Models\ProductInternationPurchase::firstOrCreate(['product_id' =>  $id],);
       $int_purch->update($attributes);
       Flash::success('Product International Purchase Updated Successfully');
        return redirect()->route('admin.products.index');
    }

    public function stocksOverview()
    {
        $page_title = 'Store Overview';
        $page_description = 'Export Store Overview Report';

        $current_fiscal = \App\Models\Fiscalyear::where('current_year', 1)->first();


        $fiscal_year = request()->fiscal_year ? request()->fiscal_year : $current_fiscal->fiscal_year;

        $op = \Request::get('op');
        $outlets = \App\Models\PosOutlets::where('enabled', '1')->pluck('name', 'id')->all();
        $allFiscalYear = \App\Models\Fiscalyear::pluck('fiscal_year', 'fiscal_year')->all();

        if (\Request::get('start_date_nep') != '' && \Request::get('end_date_nep') != '') {
            $start_date = \Request::get('start_date_nep');
            $end_date = \Request::get('end_date_nep');
            $cal = new \App\Helpers\NepaliCalendar();
            $startdate = explode('-', $start_date);
            $date = $cal->nep_to_eng($startdate[0], $startdate[1], $startdate[2]);
            $startdate = $date['year'] . '-' . $date['month'] . '-' . $date['date'];
            $enddate = explode('-', $end_date);
            $date = $cal->nep_to_eng($enddate[0], $enddate[1], $enddate[2]);
            $enddate = $date['year'] . '-' . $date['month'] . '-' . $date['date'];

        } elseif(\Request::get('start_date') != '' && \Request::get('end_date') != '') {
            $startdate = \Request::get('start_date');
            $enddate = \Request::get('end_date');
        }else{
            $fyc=\App\Models\Fiscalyear::where('fiscal_year',$fiscal_year)->first();
            $startdate = $fyc->start_date;
            $enddate =$fyc->end_date;
        }

        $prefix = '';
        if ($fiscal_year != $current_fiscal->fiscal_year) {
            $prefix = Fiscalyear::where('fiscal_year', $fiscal_year)->first()->numeric_fiscal_year . '_';
        }
        $categories=\App\Models\ProductCategory::where('org_id',1)->pluck('name','id')->all();
        // dd($categories);

        $productCategorylists= request()->category_id ? [request()->category_id] :\App\Models\ProductCategory::where('org_id',1)->pluck('id');
        $filter_category_name= request()->category_id ?\App\Models\ProductCategory::find( request()->category_id)->name : null;
        $productlistwithgrn= \App\Models\Product::select('products.id','products.name','products.category_id')
        ->whereIn('category_id',$productCategorylists)
        // ->rightJoin('grn_details','grn_details.product_id','=','products.id')
        //
        ->get();
        $current_store=\Request::get('outlet_id')?\Request::get('outlet_id'):3;
        $outlet_name=\App\Models\PosOutlets::find($current_store)->name;
//anamol
        $dataarray=$productlistwithgrn->groupby('category_id');
        foreach($dataarray as $category_id=>$products){
            $category_name=\App\Models\ProductCategory::find($category_id)->name;
            foreach($products as $product){
                $opening_detail=\App\Models\StockAdjustmentDetail::select(DB::raw("SUM(qty) as quantity,SUM(total) as total, AVG(price) as rate"))
                ->where('product_id',$product->id)
                ->whereIn('adjustment_id',\App\Models\StockAdjustment::where('reason',5)->where('store_id',$current_store)
                    ->when($startdate && $enddate, function ($q) use($startdate,$enddate) {
                        return $q->wherebetween('transaction_date',[$startdate,$enddate]);
                    })->pluck('id'))//opening stock only
               ->first();
                $records[$category_name][$product->name]['opening']=$opening_detail;

                $purchase_receipt=\App\Models\PurchaseOrderDetail::select(DB::raw("SUM(quantity_recieved) as quantity, SUM(total) as total,AVG(unit_price) as rate"))
                ->where('product_id',$product->id)
                ->whereIn('order_no',\App\Models\PurchaseOrder::where('into_stock_location',$current_store)
                    ->when($startdate && $enddate, function ($q) use($startdate,$enddate) {
                        return $q->wherebetween('bill_date',[$startdate,$enddate]);
                    })->pluck('id'))//opening stock only
               ->first();
                $records[$category_name][$product->name]['receipt']=$purchase_receipt;

                $receipt_return=\App\Models\SupplierReturnDetail::select(DB::raw("SUM(return_quantity) as quantity, SUM(return_total) as total,AVG(return_price) as rate"))
                ->where('product_id',$product->id)
                ->whereIn('supplier_return_id',\App\Models\SupplierReturn::where('into_stock_location',$current_store)
                ->when($startdate && $enddate, function ($q) use($startdate,$enddate) {
                    return $q->wherebetween('return_date',[$startdate,$enddate]);
                })->pluck('id'))//opening stock only
           ->first();
                $records[$category_name][$product->name]['receipt_return']=$receipt_return;

                $issue=\App\Models\InvoiceDetail::select(DB::raw("SUM(quantity) as quantity,SUM(total) as total, AVG(price) as rate"))
                ->where('product_id',$product->id)
                ->whereIn('invoice_id',\App\Models\Invoice::where('outlet_id',$current_store)
                    ->when($startdate && $enddate, function ($q) use($startdate,$enddate) {
                        return $q->wherebetween('bill_date',[$startdate,$enddate]);
                    })->pluck('id'))
               ->first();
                $records[$category_name][$product->name]['issue']=$issue;

                $invoice_ids = \App\Models\Invoice::select( 'invoice.*')
                ->leftjoin('invoice_meta', 'invoice.id', '=', 'invoice_meta.invoice_id')
                ->when($startdate && $enddate, function ($q) use($startdate,$enddate) {
                    return $q->wherebetween('invoice.bill_date',[$startdate,$enddate]);
                })
                ->where('outlet_id',$current_store)
                ->where('invoice.org_id', \Auth::user()->org_id)
                ->where('invoice_meta.is_bill_active', 0)
                ->pluck('invoice.id');

                $issue_return=\App\Models\InvoiceDetail::select(DB::raw("SUM(quantity) as quantity,SUM(total) as total, AVG(price) as rate"))
                ->where('product_id',$product->id)
                ->whereIn('invoice_id',$invoice_ids)
               ->first();
                $records[$category_name][$product->name]['issue_return']=$issue_return;

                $adjustment=\App\Models\StockAdjustmentDetail::select(DB::raw("SUM(qty) as quantity,SUM(total) as total, AVG(price) as rate"))
                ->where('product_id',$product->id)
                ->whereIn('adjustment_id',\App\Models\StockAdjustment::where('reason','!=',5)->where('store_id',$current_store)
                    ->when($startdate && $enddate, function ($q) use($startdate,$enddate) {
                        return $q->wherebetween('transaction_date',[$startdate,$enddate]);
                    })->pluck('id'))//opening stock only
               ->first();
                $records[$category_name][$product->name]['adjustment']=$adjustment;
            }
        }
        // dd($records);
        if ($op == 'excel') {

            $date = date('Y-m-d');
            $title = 'Store Overview Report';
            return \Excel::download(new \App\Exports\StoreOverviewExcelExport($title,$records,$fiscal_year,$startdate,$enddate,$outlet_name,$filter_category_name), "store_overview_{$date}.xls");

        }
        $title = 'Store Overview Report';
        return view('admin.products.storeoverview', compact('outlet_name','page_description', 'page_title', 'outlets', 'allFiscalYear', 'fiscal_year','records','title','startdate','enddate','categories','filter_category_name'));
    }

}
