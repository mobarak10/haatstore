<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Converter;
use App\Helpers\QuantityHelper;
use App\Models\Cash;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Party;
use App\Models\SaleReturn;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Auth;

class POSController extends Controller
{
    use QuantityHelper;

    private $meta = [
        'title'   => 'POS',
        'menu'    => 'pos',
        'submenu' => '',
        'header'  => false
    ];

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $this->meta['submenu'] = 'list';

        $sales = Sale::latest()->paginate(15);
        $date = null;

        if (request()->search) {
            // if search by date
            $date = date(request()->date);
            $sale = Sale::whereDate('created_at', $date); // whereDate remove time from created_at;

            if (request()->invoice) {
                // if search by invoice number
                $sale = Sale::where('invoice_no', request()->invoice);
            }

            if (request()->number) {
                // if search by phone number
                // first get party id then sell table party_id for party id
                $party = Party::where('phone', request()->number)->get()->pluck('id');
                $sale = Sale::where('party_id', $party);
            }

            $sales = $sale->paginate(100);
        }

        return view('admin.pos.index', compact('sales'))
            ->with($this->meta);
    }

    public function create()
    {
        $this->meta['submenu'] = 'add';
        $this->meta['aside'] = false; //hide aside

        return view('admin.pos.create')
            ->with($this->meta);
    }

    public function show($id)
    {
        $sale = Sale::with('saleDetails.quantities')->where('id', $id)->first();

        $calculated_amount = [];
        $calculated_amount['vat'] = ($sale->subtotal * $sale->vat) / 100; //vat
        $calculated_amount['total'] = $sale->subtotal + $calculated_amount['vat']; // total with vat
        $calculated_amount['discount'] = ($sale->discount_type === 'percentage') ? ($calculated_amount['total'] * $sale->discount) / 100 : $sale->discount; // calculate discount
        $calculated_amount['grand_total'] = ($sale->subtotal + $calculated_amount['vat']) - $calculated_amount['discount']; //calculate grand total
        $calculated_amount['paid'] = ($sale->change > 0) ? abs($sale->tendered - $sale->change) : abs($calculated_amount['grand_total'] - $sale->due); // calculate paid amount

        return view('admin.pos.show', compact('sale', 'calculated_amount'))
            ->with($this->meta);
    }


    /**
     * Checkout view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkout()
    {
        return view('admin.pos.checkout')
            ->with($this->meta);
    }

    /**
     * Return view
     * @param $invoice_no
     * @return mixed
     */
    public function return($invoice_no)
    {
        $this->meta['aside'] = false;

        $sale = Sale::with('saleDetails.quantities')->where('invoice_no', $invoice_no)->first();

        $calculated_amount = [];
        $calculated_amount['vat'] = ($sale->subtotal * $sale->vat) / 100; //vat
        $calculated_amount['total'] = $sale->subtotal + $calculated_amount['vat']; // total with vat
        $calculated_amount['discount'] = ($sale->discount_type === 'percentage') ? ($calculated_amount['total'] * $sale->discount) / 100 : $sale->discount; // calculate discount
        $calculated_amount['grand_total'] = ($sale->subtotal + $calculated_amount['vat']) - $calculated_amount['discount']; //calculate grand total
        $calculated_amount['paid'] = ($sale->change > 0) ? abs($sale->tendered - $sale->change) : abs($calculated_amount['grand_total'] - $sale->due); // calculate paid amount

        //warehouses
        $warehouses = Warehouse::active()->get();
        //cashes
        $cashes = Cash::all();

        $extras = collect([]);
        $extras['calculated_amount'] = $calculated_amount;
        $extras['warehouses'] = $warehouses;
        $extras['cashes'] = $cashes;

        return view('admin.pos.return', compact('sale', 'calculated_amount', 'extras'))
            ->with($this->meta);
    }


    /**
     * Return Process
     * @param $invoice_no
     * @param Request $request
     * @return Request
     */
    public function returnProceed(Request $request)
    {
        /********************
         * Database Action
        -------------------
         * Insert into sale_returns
         * Insert into sale_returns_products
         * Insert into sale_returns_products_quantities
         * Increment Stock quantity
         * Decrement Sale quantity
         **************************/

        $request->validate([
            'sale_id' => 'required|integer',
            'adjustment' => 'nullable|string',
            'paid_from' => 'required|string',
            'cash_id' => 'required|integer',
            'note' => 'nullable|string'
        ]);

        //get current sale
        $sale = Sale::find($request->sale_id);

        $errors = $this->validateQuantity($sale, $request->quantities);

        if(count($errors)){
            return response()->json($errors, 422);
        }

        $_sale_return = [
            'sale_id'     => $sale->id,
            'user_id'     => Auth::id(), // todo auth()->id
            'adjustment'  => $request->adjustment ?? 0,
            'paid_from'   => $request->paid_from,
            'cash_id'     => $request->cash_id,
            'note'        => $request->note,
            'business_id' => Auth::user()->business_id,
        ];

        //save into sale return table
        $sale_return = SaleReturn::create($_sale_return);

        $return_subtotal = 0;

        foreach ($request->quantities as $product_id => $quantities){
            $_return_product = [
                'product_id' => $product_id,
                'return_price' => $sale->saleDetails->where('product_id', $product_id)->first()->sale_price
            ];
            //insert into return product table
            $return_product = $sale_return->returnProducts()->create($_return_product);

            //current sale details
            $current_sale_details = $sale->saleDetails->where('product_id', $product_id)->first();

            //init product wise total quantity
            $product_total_quantity = 0;

            foreach ($quantities as $warehouse_id => $quantity){
                $formatted_quantity = $this->formattedSingleQuantity($quantity, count($current_sale_details->product->product_unit_labels));
                $warehouse_wise_quantity_units = Converter::convert($formatted_quantity, $current_sale_details->product->unit_code, 'u');
                $warehouse_wise_quantity = $warehouse_wise_quantity_units['result'];

                //add price to subtotal
                $product_total_quantity += $warehouse_wise_quantity;

                // insert into sale_return_quantities table
                $return_product->quantities()->attach($warehouse_id, [
                    'sale_id' => $sale->id,
                    'product_id' => $current_sale_details->product_id,
                    'quantity' => $warehouse_wise_quantity,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // update stock
                $_product = Product::find($product_id);

                $_has_previous_quantity = $_product->warehouses->where('id', $warehouse_id)->first();

                // product already exists in stock
                if($_has_previous_quantity){
                    $_has_previous_quantity->stock->increment('quantity', $warehouse_wise_quantity);
                }else{ // product not exists in stock
                    $_product->warehouses()->attach($warehouse_id, [
                        'quantity'   => $warehouse_wise_quantity,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            //product wise price
            $product_wise_line_total = $product_total_quantity * $current_sale_details->sale_price;

            //add to return subtotal
            $return_subtotal += $product_wise_line_total;
        }

        //return total
        $return_total = $return_subtotal + $request->adjustment;

        //update cash
        Cash::find($request->cash_id)->decrement('amount', $return_total);

        return response()->json($sale_return, 200);
    }

    /*--------Helper Method Start-------*/

    /**
     * Return Product total quantity
     * @param $quantities
     * @param $unit_length
     * @param $unit_code
     * @return int
     */
    private function totalProductQuantity($quantities, $unit_length, $unit_code)
    {
        $total_return_quantity = 0;
        foreach ($quantities as $warehouse_id => $quantity) {
            //get return unit
            $return_units = Converter::convert($this->formattedSingleQuantity($quantity, $unit_length), $unit_code);
            //sum total return quantity
            $total_return_quantity += $return_units['result'];
        }
        return $total_return_quantity;
    }

    /**
     * Get Original Sale Details Quantity
     * @param $details
     * @return array
     */
    private function originalSaleDetailsQuantity($details)
    {
        $original_sale = [];

        foreach($details as $detail){
            //total sale quantity
            $total_sale_quantity = 0;
            //for every warehouse's quantity
            foreach ($detail->quantities as $quantities){
                $total_sale_quantity += $quantities->quantity->quantity;
            }

            //add product sale quantity with product id
            $original_sale[$detail->product_id] = $total_sale_quantity;
        }

        return $original_sale;
    }
    /*--------Helper Method End-------*/

    /*--------AJAX Request Start--------*/

    /**
     * All active products
     * @return \Illuminate\Http\JsonResponse
     */
    public function allActiveProducts()
    {
        $products = Product::has('warehouses')->with(['warehouses', 'unit'])->active()->get();
        return response()->json($products, 200);
    }


    /**
     * All products
     * @return \Illuminate\Http\JsonResponse
     */
    public function allProducts()
    {
        $products = Product::with(['warehouses', 'unit'])->orderBy('name', 'ASC')->active()->get();

        return response()->json($products, 200);
    }


    /**
     * Get Product details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productDetails(Request $request)
    {
        $product = Product::with(['warehouses', 'unit'])->where('code', $request->code)->first();
        return response()->json($product, 200);
    }


    /**
     * All active categories
     * @return \Illuminate\Http\JsonResponse
     */
    public function allActiveCategories()
    {
        return response()->json(Category::active()->get(), 200);
    }


    /**
     * Calculate line total
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateLineTotal(Request $request)
    {
        //todo validation
        $response = [];

        $sale = Sale::find($request->saleId);

        $errors = $this->validateQuantity($sale, $request->quantities);

        if(count($errors)){
            return response()->json($errors, 422);
        }

        foreach ($request->quantities as $product_id => $quantities) {
            $current_sale_details = $sale->saleDetails->where('product_id', $product_id)->first();

            $current_product_sale_price = $current_sale_details->sale_price;

            //init total quantity
            $total_quantity = 0;

            foreach ($quantities as $warehouse_id => $quantity){
                $formatted_quantity = $this->formattedSingleQuantity($quantity, count($current_sale_details->product->product_unit_labels));
                $warehouse_wise_quantity_units = Converter::convert($formatted_quantity, $current_sale_details->product->unit_code, 'u');
                $warehouse_wise_quantity = $warehouse_wise_quantity_units['result'];
                //add warehouse wise quantity into total quantity
                $total_quantity += $warehouse_wise_quantity;
            }

            // init line total
            $response[$product_id] = $total_quantity * $current_product_sale_price;

        }

        return response()->json($response, 200);
    }
    /*---------AJAX Request End--------*/

    /**
     * Validate quantities
     * @param $sale
     * @param $_quantities
     * @return array
     */
    private function validateQuantity($sale, $_quantities)
    {
        $errors = [];

        foreach($_quantities as $product_id => $quantities){
            $current_sale_product = $sale->saleDetails->where('product_id', $product_id)->first();

            $warehouses = $current_sale_product->quantities;

            foreach ($quantities as $warehouse_id => $quantity){
                $formatted_quantity = $this->formattedSingleQuantity($quantity, count($current_sale_product->product->product_unit_labels));
                $warehouse_wise_quantity_units = Converter::convert($formatted_quantity, $current_sale_product->product->unit_code, 'u');
                $available_quantity = $warehouses->where('id', $warehouse_id)->first()->sale_product_rest_quantity;

                if ($warehouse_wise_quantity_units['result'] > $available_quantity){
                    $errors[$sale->id][$product_id][$warehouse_id] = 'Insufficient quantity';
                }
            }

        }
        return $errors;
    }
}
