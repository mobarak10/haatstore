<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Converter;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Party;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use Darryldecode\Cart\Cart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private $meta = [
        'title'   => 'Product',
        'menu'    => 'product',
        'submenu' => ''
    ];

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->meta['submenu'] = 'product-list';

        $products = Product::paginate(15);

        $suppliers = Party::suppliers()->active()->get();
        $warehouses = Warehouse::active()->get();
        $categories = Category::active()->get();
        // dd($categories);

        return view('admin.product.index', compact('products', 'suppliers', 'warehouses', 'categories'))
            ->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->meta['submenu'] = 'product-list';

        $suppliers  = Party::suppliers()->get();
        $units      = Unit::all();
        $warehouses = Warehouse::active()->get();

        return view('admin.product.create', compact('suppliers', 'units', 'warehouses'))
            ->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function store(Request $request)
    {
        $request['code'] = 'PRD' . str_pad(Product::withTrashed()->max('id') + 1, 3, '0', STR_PAD_LEFT);

        if (!$request->has('barcode') || empty($request['barcode'])){
            $request['barcode'] = $request['code'];
        }

        $product_basic_info = $request->validate([
            'party_id'        => 'required|integer',
            'brand_id'        => 'required|integer',
            'category_id'     => 'required|integer',
            'unit_id'         => 'required|integer',
            'model'           => 'nullable|string',
            'name'            => 'required|string',
            'code'            => 'required|string|unique:products',
            'barcode'         => 'required|string|unique:products',
            'purchase_price'  => 'required|numeric',
            'retail_price'    => 'required|numeric',
            'wholesale_price' => 'required|numeric',
            'stock_alert'     => 'nullable|numeric',
            'description'     => 'nullable|string',
        ], [], [
            'party_id' => 'supplier'
        ]);

        $product_basic_info['slug'] = Str::slug($request->name);

        $product = Product::create($product_basic_info);

        $unit_length = count(explode('/', Unit::find($request->unit_id)->relation));

        $product->warehouses()->sync($this->productQuantities($request->quantity, $unit_length, $product->unit_code));

        session()->flash('success', 'Product created successfully');

        return response()->json('Product created successfully', 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('admin.product.show', compact('product'))
            ->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->meta['submenu'] = 'product-list';

        $product = Product::with('warehouses')->where('id', $id)->first();


        $suppliers  = Party::suppliers()->get();
        $units      = Unit::all();
        $warehouses = Warehouse::active()->get();

        $extras               = collect([]);
        $extras['brands']     = Party::find($product->party_id)->brands;
        $extras['categories'] = Brand::find($product->brand_id)->categories;
        $extras['unit']       = Unit::find($product->unit_id);

        $stock = [];

        foreach($product->warehouses as $warehouse){
            $stock[$warehouse->id] = Converter::convert($warehouse->stock->quantity, $extras['unit']->code, 'd')['result'];
        }

        $extras['quantity'] = $stock;

        return view('admin.product.edit', compact('product', 'suppliers', 'units', 'warehouses', 'extras'))
            ->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product_basic_info = $request->validate([
            'party_id'        => 'required|integer',
            'brand_id'        => 'required|integer',
            'category_id'     => 'required|integer',
            'unit_id'         => 'required|integer',
            'model'           => 'nullable|string',
            'name'            => 'required|string',
            'barcode'         => 'required|string',
            'purchase_price'  => 'required|numeric',
            'retail_price'    => 'required|numeric',
            'wholesale_price' => 'required|numeric',
            'stock_alert'     => 'nullable|numeric',
            'description'     => 'nullable|string',
        ]);

        $product_basic_info['slug'] = Str::slug($request->name);

        $unit_length = count(explode('/', Unit::find($request->unit_id)->relation));

        $product->update($product_basic_info);

        $product->warehouses()->sync($this->productQuantities($request->quantity, $unit_length, $product->unit_code));

        session()->flash('success', 'Product updated successfully');

        return response()->json(route('admin.product.index'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->back()->withSuccess('Product deleted successfully');
    }

    public function search(Request $request)
    {
        $this->meta['submenu'] = 'product-list';

        $products = Product::query();

        $products = $this->searchQuery($products, $request)->paginate(15);

        $suppliers = Party::suppliers()->active()->get();
        $warehouses = Warehouse::active()->get();
        $categories = Category::active()->get();

        if($request->has('party_id') AND !empty($request->party_id)){
            $request['brands']     = Party::find($request->party_id)->brands;
        }

        if ($request->has('brand_id') AND !empty($request->brand_id)){
            $request['categories'] = Brand::find($request->brand_id)->categories;
        }

        return view('admin.product.index', compact('products', 'suppliers', 'warehouses', 'categories'))
            ->with($this->meta)
            ->with('searched_query', collect($request->all()));
    }


    /**
     * Perform Search
     * @param $products
     * @param $request
     * @return mixed
     */
    private function searchQuery($products, $request)
    {
        //suppler/company wise search
        if ($request->has('party_id') AND !empty($request->party_id)){
            $products->where('party_id', $request->party_id);
        }

        // brand wise search
        if ($request->has('brand_id') AND !empty($request->brand_id)) {
            $products->where('brand_id', $request->brand_id);
        }

        // category wise search
        if ($request->has('category_id') AND !empty($request->category_id)) {
            $products->where('category_id', $request->category_id);
        }
        // category wise search
        if ($request->has('barcode') AND !empty($request->barcode)) {
            $products->where('barcode', $request->barcode);
        }

        // warehouse wise search
        if ($request->has('warehouse_id') AND !empty($request->warehouse_id)) {
            $warehouse_id = $request->warehouse_id;
            $products->whereHas('warehouses', function (Builder $query) use ($warehouse_id) {
                $query->where('warehouse_id', $warehouse_id);
            });
        }

        return $products;
    }


    /**
     * Calculate product quantities
     * @param $quantities
     * @param $unit_length
     * @param $unit_code
     * @return array
     */
    private function productQuantities($quantities, $unit_length, $unit_code, $call_from = 'store/update')
    {
        $product_quantities = [];

        foreach ($quantities as $warehouse_id => $quantity){
            /*$input = '';
            for ($i = 0; $i < $unit_length; $i++){
                if (!array_key_exists($i, $quantity) || $quantity[$i] == null){
                    $quantity[$i] = 0;
                }
                $input .= $quantity[$i];

                if ($unit_length - $i > 1){
                    $input .= '/';
                }
            }*/

            //$quantity = Converter::convert($input, $unit_code)['result'];
            $quantity = $this->getQuantityToUnit($quantity, $unit_length, $unit_code)['result'];

            if (!$quantity) continue;

            //for store and update method
            if($call_from === 'store/update'){
                $product_quantities[$warehouse_id] = [
                    'quantity'   => $quantity,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }elseif ($call_from === 'addToCart'){
                $product_quantities[$warehouse_id] = $quantity;
            }

        }

        return $product_quantities;
    }

    /**
     * Get Quantity to unit
     * @param $quantity
     * @param $unit_length
     * @param $unit_code
     * @return mixed
     */
    public function getQuantityToUnit($quantity, $unit_length, $unit_code)
    {
        $input = '';
        for ($i = 0; $i < $unit_length; $i++){
            if (!array_key_exists($i, $quantity) || $quantity[$i] == null){
                $quantity[$i] = 0;
            }
            $input .= $quantity[$i];

            if ($unit_length - $i > 1){
                $input .= '/';
            }
        }

        return Converter::convert($input, $unit_code);
    }

    /**
     * Get unit Length
     * @param $unit_code
     * @return int
     */
    public function getUnitLength($unit_code)
    {
        return count(explode('/', Unit::where('code', $unit_code)->first()->relation));
    }

    /*--------AJAX Request Start--------*/

    /**
     * All active products
     * @return \Illuminate\Http\JsonResponse
     */
    /*public function allActiveProducts()
    {
        $products = Product::has('warehouses')->active()->get();
        return response()->json($products, 200);
    }*/


    /**
     * Get Product details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /*public function productDetails(Request $request)
    {
        $product = Product::with('warehouses')->where('code', $request->code)->first();
        return response()->json($product, 200);
    }*/
    /*---------AJAX Request End--------*/
}
