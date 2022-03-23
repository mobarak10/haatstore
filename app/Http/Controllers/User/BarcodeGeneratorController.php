<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

use Auth;

class BarcodeGeneratorController extends Controller {

    private $meta = [
        'title' => 'Barcode',
        'menu' => 'barcode',
        'submenu' => ''
    ];

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $this->meta['barcode'] = 'generator';

        $product = null;
        $business_id = Auth::user()->business_id;

        if(request()->barcode != null) {
            $product = Product::where('business_id', $business_id)->where('barcode', request()->barcode)->first();

            if($product) {
                $product->quantity = request()->quantity;
            }
        }

        return view('user.barcode.index', compact('product'))->with($this->meta);
    }

}
