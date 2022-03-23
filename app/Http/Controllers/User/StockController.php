<?php

namespace App\Http\Controllers\User;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exports\ProductsExport;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\DamageStock;
use App\Models\Stock;
use App\Helpers\Converter;
use App\Models\Category;
use Auth;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{

    private $meta = [
        'title'   => 'Current Stock',
        'menu'    => 'stock',
        'submenu' => ''
    ];

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $business_id = Auth::user()->business_id;
        $products = Product::with('stock')->where('business_id', $business_id)->paginate(27);

        $print_products = Product::with('brand', 'stock', 'unit', 'category')->where('business_id', $business_id)->get();
        $categories = Category::active()->where('business_id', $business_id)->get();

        if(request()->search) {
            // set contition
            $where = [
                ['business_id', '=', $business_id],
            ];

            foreach(request()->filter_by as $key => $value) {
                if($value != null) {
                    if($key == 'name') {
                        $where[] = [$key, 'like', '%' . $value . '%'];
                    } else {
                        $where[] = [$key, '=', $value];
                    }
                }
            }

            // get data
            $products = Product::where($where)->paginate(50);
        }

        return view('user.stock.index', compact('products', 'print_products', 'categories'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        //
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
    public function edit($id) {
        $business_id = Auth::user()->business_id;
        $product = Product::where('business_id', $business_id)->find($id);
        return view('user.stock.edit', compact('product'))->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // get product details
        $product = Product::find($id);

        foreach($request->quantities as $stockID => $quantity) {
            // set quantity
            foreach($quantity as $i => $q) {
                if(empty($q)) {
                    $quantity[$i] = 0;
                }
            }

            // convert stock quantity into unit
            $quantity = Converter::convert(join('/', $quantity), $product->unit_code)['result'];

            // stock update
            $stock = Stock::findOrFail($stockID);
            $stock->quantity = $quantity;

            if ($stock->save()) {
                // if product quantity is zero(0) remove that product
                if ($quantity <= 0) {
                    $product = Product::find($id);
                    $product->warehouses()->detach($stock->warehouse_id);
                }
            }
        }

        // set flase message
        session()->flash('success', 'Quantity updated successfully.');

        // return to view
        return redirect(route('stock.index'));
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
    }

    public function getProductsFromWarehouse(Request $request){
        $products = Warehouse::with('products.warehouses')->where('id', $request->id)->first()->products;
        return response()->json($products);
    }

    public function productExport()
    {
        // return Excel::download(new CustomersExport, 'customer.xlsx');
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
}
