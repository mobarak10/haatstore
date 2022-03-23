<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use Auth;

class InvoiceController extends Controller {
    private $meta = [
        'title' => 'Invoice',
        'menu' => 'invoice',
        'submenu' => '',
//        'aside' => false, // hide aside
        'header' => false
    ];

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index($invoice_no)
    {
//        $user = Auth::user()->business->id;
        $sale =  Sale::with('saleDetails.quantities', 'saleDetails.product', 'customer', 'operator')
            ->where('invoice_no', $invoice_no)
            ->first();

        $calculated_amount = [];
        $calculated_amount['vat'] = ($sale->subtotal * $sale->vat) / 100; //vat
        $calculated_amount['total'] = $sale->subtotal + $calculated_amount['vat']; // total with vat
        $calculated_amount['discount'] = ($sale->discount_type === 'percentage') ? ($calculated_amount['total'] * $sale->discount) / 100 : $sale->discount; // calculate discount
        $calculated_amount['grand_total'] = ($sale->subtotal + $calculated_amount['vat']) - $calculated_amount['discount']; //calculate grand total
        $calculated_amount['paid'] = ($sale->change > 0) ? abs($sale->tendered - $sale->change) : abs($calculated_amount['grand_total'] - $sale->due); // calculate paid amount

        return view('admin.invoice.index', compact('sale', 'calculated_amount'))
            ->with($this->meta);
    }
}
