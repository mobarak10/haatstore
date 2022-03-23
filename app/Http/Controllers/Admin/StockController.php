<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Warehouse;
use App\Models\Product;
use App\Models\DamageStock;
use App\Models\Stock;
use App\Helpers\Converter;

class StockController extends Controller
{

    private $meta = [
        'title'   => 'Current Stock',
        'menu'    => 'stock',
        'submenu' => ''
    ];

    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $products = Product::paginate(15);

        if(request()->search) {
            $products = Product::where('code', '=', request()->productCode)->paginate(15);
        }
        
        return view('admin.stock.index', compact('products'))->with($this->meta);
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
        $product = Product::find($id);
        return view('admin.stock.edit', compact('product'))->with($this->meta);
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
        return redirect(route('admin.stock.index'));
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
}
