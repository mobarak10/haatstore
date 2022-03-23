<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stockPDF()
    {
        $business_id = Auth::user()->business_id;

        $data = [
            'print_products' => Product::with('brand', 'stock', 'unit')->where('business_id', $business_id)->paginate(30)
        ];

        $pdf = PDF::loadView('user.PDF.stockPDF', $data);
        return $pdf->download('stock.pdf');
    }
}
