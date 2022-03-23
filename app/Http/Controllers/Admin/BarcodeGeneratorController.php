<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class BarcodeGeneratorController extends Controller {
    
    private $meta = [
        'title' => 'Barcode',
        'menu' => 'barcode',
        'submenu' => ''
    ];

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index() {
        $this->meta['barcode'] = 'generator';

        $product = null;

        if(request()->barcode != null) {
            $product = Product::where('barcode', request()->barcode)->first();
            
            if($product) {
                $product->quantity = request()->quantity;
            }
        }
        
        return view('admin.barcode.index', compact('product'))->with($this->meta);
    }

}
