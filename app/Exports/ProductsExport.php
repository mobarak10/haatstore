<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsExport implements FromView
{
    /**
     * product export view
     *
     * @return View
     */
    public function view(): View
    {
        return view('user.exports.product', [
            'products' => Product::all(),
        ]);
    }
}
