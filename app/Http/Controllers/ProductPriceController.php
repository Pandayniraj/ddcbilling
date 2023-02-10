<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Store;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class ProductPriceController extends Controller
{
    /**
     * @var ProductPrice
     */
    private $productPrice;
    /**
     * @var Product
     */
    private $product;

    public function __construct(ProductPrice $productPrice, Product $product)
    {
        $this->product=$product;
        $this->productPrice=$productPrice;
    }

    public function index($id)
    {
        $product = $this->product->find($id);
        $page_title = 'Product Prices of '.$product->name??'';
        $page_description = '';
        $productPrices= $this->productPrice->where('product_id', $product->id)->orderBy('id', 'desc')->paginate(25);

        return view('admin.productPrice.index', compact('page_title', 'page_description', 'product', 'productPrices'));
    }
    /**
     * @return View
     */
    public function create($id)
    {
        $product = $this->product->find($id);
        $page_title = 'Add Product Price of '.$product->name??'';
        $page_description = 'Create Product Price';
        $projects = Store::orderBy('name')->pluck('name', 'id')->toArray();
        return view('admin.productPrice.create', compact('page_title', 'page_description', 'product', 'projects'));
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request, $id)
    {
        $attributes = $request->validate([
            "project_id" => "required|exists:store,id",
            "distributor_price" => "required|gt:0",
            "retailer_price" => "required|gt:0",
            "customer_price" => "required|gt:0",
        ]);
        $attributes['product_id'] = $id;

        $checkExists = $this->productPrice->where([
            ['project_id', $request->project_id??''], ['product_id', $id??'']
        ])->first();
        if (isset($checkExists)) {
            Flash::error('Product Price for following project already exists.');
            return redirect()->route('admin.product-pricing.index', $id);
        } else {
            $this->productPrice->create($attributes);
            Flash::success('Product Price Successfully Created');
            return redirect()->route('admin.product-pricing.index', $id);
        }
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $productPricing = $this->productPrice->find($id);
        $product = $this->product->find($productPricing->product_id);
        $page_title = 'Add Product Price of '.$product->name??'';
        $page_description = 'Edit Product Price';
        $projects = Store::orderBy('name')->pluck('name', 'id')->toArray();

        return view('admin.productPrice.edit', compact('page_title', 'page_description', 'productPricing',
            'product', 'projects'));
    }

    /**
     * @param  Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $attributes = $request->validate([
            "project_id" => "required|exists:store,id",
            "distributor_price" => "required|gt:0",
            "retailer_price" => "required|gt:0",
            "customer_price" => "required|gt:0",
        ]);
        $productPrice = $this->productPrice->find($id);
        $checkExists = $this->productPrice->where([
            ['project_id', $request->project_id??''], ['product_id', $productPrice->product_id??''], ['id', '!=', $id]
        ])->first();
        if (isset($checkExists)) {
            Flash::error('Product Price for following project already exists.');
            return redirect()->route('admin.product-pricing.index', $productPrice->product_id);
        } else {
            $productPrice = $this->productPrice->find($id);
            $productPrice->update($attributes);

            Flash::success('Product Price Update Successfully');
            return redirect()->route('admin.product-pricing.index', $productPrice->product_id);
        }
    }


    /**
     * @param  int  $id
     * @return View
     */
    public function getModalDelete($id)
    {
        $error = null;
        $modal_title = "Delete Product Price ?";
        // $brand = $this->productPrice->find($id);
        $modal_route = route('admin.product-pricing.destroy', array('id' => $id));
        $modal_body = "Are You Sure You Want To Delete This? This Is Irreversible";

        return view('modal_confirmation', compact('error', 'modal_route', 'modal_title', 'modal_body'));
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $productPrice = $this->productPrice->find($id);
        $productId = $productPrice->product_id;
        if (!$productPrice->isDeletable()) abort(403);
        $productPrice->delete($id);
        Flash::success('Product Price successfully deleted');
        return redirect()->route('admin.product-pricing.index', $productId);
    }

    public function validateRequest(Request $request)
    {
        $checkExists = $this->productPrice->where([
            ['project_id', $request->project_id??''], ['product_id', $request->product??'']
        ])->where(function ($q) use($request) {
            if ($request->has('product_price_id') && $request->product_price_id != 0) $q->where('id', '!=', $request->product_price_id);
        })->first();
        return response()->json(isset($checkExists));
    }
}
