<?php

namespace App\Http\Controllers\User;

use App\Models\Sale;
use App\Models\Setting;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class InvoiceController extends Controller
{
    private $meta = [
        'title' => 'Invoice',
        'menu' => 'invoice',
        'submenu' => '',
        // 'aside' => false, // hide aside
        'header' => false
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($invoice_no)
    {
        $business_id = Auth::user()->business->id;
        $business = Business::where('id', $business_id)->first();

        $sale =  Sale::where('business_id', $business_id)
            ->with('saleDetails.quantities', 'saleDetails.product', 'customer', 'operator')
            ->where('invoice_no', $invoice_no)
            ->first();

        $calculated_amount = [];
        $calculated_amount['vat'] = ($sale->subtotal * $sale->vat) / 100; // vat
        $calculated_amount['total'] = $sale->subtotal + $calculated_amount['vat']; // total with vat
        $calculated_amount['discount'] = ($sale->discount_type === 'percentage') ? ($calculated_amount['total'] * $sale->discount) / 100 : $sale->discount; // calculate discount
        $calculated_amount['grand_total'] = ($sale->subtotal + $calculated_amount['vat']) - $calculated_amount['discount']; //calculate grand total
        $calculated_amount['paid'] = $sale->tendered;

        if ($sale->grand_total > $sale->tendered) {
            $calculated_amount['previous_balance'] = $sale->customer_balance - ($sale->tendered - $sale->grand_total);
        } else if ($sale->adjust_to_customer_balance) {
            $calculated_amount['previous_balance'] = $sale->customer_balance - ($sale->tendered - $sale->grand_total);
        }else if($sale->grand_total < $sale->tendered){
            $calculated_amount['previous_balance'] = ($sale->tendered - $sale->change) - $sale->grand_total;
        }
         else {
            $calculated_amount['previous_balance'] = $sale->customer_balance;
        }


        return view('user.invoice.index', compact('sale', 'calculated_amount', 'business'))
            ->with($this->meta);
    }
}
