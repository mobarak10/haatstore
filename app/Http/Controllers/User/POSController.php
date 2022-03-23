<?php

namespace App\Http\Controllers\User;

use App\Helpers\Converter;
use App\Helpers\QuantityHelper;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\User\User;
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
use Illuminate\Validation\Rule;

class POSController extends Controller
{
    use QuantityHelper;

    private $meta = [
        'title'   => 'POS',
        'menu'    => 'pos',
        'submenu' => '',
        'header'  => false
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->meta['submenu'] = 'list';

        $business_id = Auth::user()->business_id;
        $sales = Sale::where('business_id', $business_id)->with('saleReturns')->latest()->paginate(75);
        $employees = User::all();
        $date = null;

        if (request()->search) {
            $sales = Sale::query(); // create sale instance
            $where = [['business_id', '=', $business_id]]; // set business-id conditions
            $date = []; // set date

            // pluck all the employees using phone number
            $party = Party::where('phone', request()->condition['phone'])->get()->pluck('id');

            foreach (request()->condition as $input => $value) {
                if ($value != null) {
                    if ($input == 'phone') {
                        $where[] = ['party_id', '=', $party];
                    } else {
                        $where[] = [$input, '=', $value];
                    }
                }
            }

            // set query
            $sales = $sales->where($where);

            // set date
            if (request()->date['from'] != null) {
                $date[] = date(request()->date['from'] . ' 00:00:00');
            }

            if (request()->date['to'] != null) {
                $date[] = date(request()->date['to'] . ' 23:59:00');
            } else {
                if (request()->date['from'] != null) {
                    $date[] = date('Y-m-d') . ' 23:59:00';
                }
            }

            if (count($date) > 0) {
                $sales = $sales->whereBetween('created_at', $date);
            }

            // get data
            $sales = $sales->paginate(75);
        }

        return view('user.pos.index', compact('sales', 'employees'))->with($this->meta);
    }

    public function create()
    {
        $this->meta['submenu'] = 'add';
        $this->meta['aside'] = false; //hide aside

        $cashes = Cash::all();

        $warehouses = Warehouse::with('products.unit')->get();

        $customers = Party::customers()->select('id', 'name', 'phone', 'address', 'balance')->get();

        $bank_accounts = BankAccount::with('bank')
            ->get();


        return view('user.pos.create', compact('warehouses', 'cashes', 'bank_accounts', 'customers'))
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

        return view('user.pos.show', compact('sale', 'calculated_amount'))
            ->with($this->meta);
    }


    /**
     * Checkout view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkout()
    {
        return view('user.pos.checkout')
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

        return view('user.pos.return', compact('sale', 'calculated_amount', 'extras'))
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
            'paid_from' => [Rule::requiredIf($request->adjust_to_customer_balance == false)],
            'cash_id' => [Rule::requiredIf($request->adjust_to_customer_balance == false)],
            'note' => 'nullable|string',
            'adjust_to_customer_balance' => 'required|boolean'
        ]);

        //get current sale
        $sale = Sale::find($request->sale_id);

        $errors = $this->validateQuantity($sale, $request->quantities);

        if (count($errors)) {
            return response()->json($errors, 422);
        }

        $_sale_return = [
            'sale_id'    => $sale->id,
            'user_id'     => Auth::id(), // todo auth()->id
            'adjustment'  => $request->adjustment ?? 0,
            'paid_from'   => $request->adjust_to_customer_balance ? null : $request->paid_from, // if adjust to customer balance then store null in paid from column
            'cash_id'     => $request->adjust_to_customer_balance ? null : $request->cash_id, // if adjust to customer balance then store null in cash id column
            'note'        => $request->note,
            'adjust_to_customer_balance' => $request->adjust_to_customer_balance,
            'business_id' => Auth::user()->business_id,
        ];

        //save into sale return table
        $sale_return = SaleReturn::create($_sale_return);

        $return_subtotal = 0;

        foreach ($request->quantities as $product_id => $quantities) {
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

            foreach ($quantities as $warehouse_id => $quantity) {
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
                if ($_has_previous_quantity) {
                    $_has_previous_quantity->stock->increment('quantity', $warehouse_wise_quantity);
                } else { // product not exists in stock
                    $_product->warehouses()->attach($warehouse_id, [
                        'quantity'   => $warehouse_wise_quantity,
                        'avarage_purchase_price' => $_product->purchase_price,
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

        // if adjust to customer balance
        if ($request->adjust_to_customer_balance) {
            // get the customer
            $customer = Party::find($sale->party_id);
            // update sale return with customer current balance
            $sale_return->update([
                'customer_balance' => $customer->balance
            ]);

            if ($customer->balance <= 0){
                $total_balance = abs($customer->balance);
            }else{
                $total_balance = -1 * $customer->balance;
            }

            $customer_debit_ledger = [
                'date' => now()->format('Y-m-d'),
                'description' => 'Product Return Invoice Number '.$sale->invoice_no.' on increment customer balance',
                'voucher' => $sale->invoice_no,
                'debit' => $return_total,
                'balance' => $total_balance + $return_total,
            ];
            $customer_credit_ledger = [
                'date' => now()->format('Y-m-d'),
                'description' => 'Product Return Invoice Number '.$sale->invoice_no.' on increment customer balance',
                'voucher' => $sale->invoice_no,
                'credit' => $return_total,
                'balance' => $customer_debit_ledger['balance'] - $return_total,
            ];
            $customer->ledgers()->create($customer_debit_ledger);
            $customer->ledgers()->create($customer_credit_ledger);
            // increment customer balance
            $customer->increment('balance', $return_total);
        } else {
            //update cash
            Cash::find($request->cash_id)->decrement('amount', $return_total);
            $customer = Party::find($sale->party_id);
            $cash = Cash::find($request->cash_id);

            if ($customer->balance <= 0){
                $total_balance = abs($customer->balance);
            }else{
                $total_balance = -1 * $customer->balance;
            }

            $customer_debit_ledger = [
                'date' => now()->format('Y-m-d'),
                'description' => 'Product Return Invoice Number '.$sale->invoice_no.' on cash '. $cash->title,
                'debit' => $return_total,
                'balance' => $total_balance + $return_total,
            ];
            $customer->ledgers()->create($customer_debit_ledger);

            $customer_credit_ledger = [
                'date' => now()->format('Y-m-d'),
                'description' => 'Product Return Invoice Number '.$sale->invoice_no. ' receive product',
                'credit' => $return_total,
                'balance' => $customer_debit_ledger['balance'] - $return_total,
            ];
            $customer->ledgers()->create($customer_credit_ledger);

            $cash_ledger = [
                'date' => now()->format('Y-m-d'),
                'description' => 'Product Return',
                'credit' => $return_total,
                'debit' => $return_total,
                'balance' => $cash->amount,
            ];
            $cash->ledgers()->create($cash_ledger);
        }


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

        foreach ($details as $detail) {
            //total sale quantity
            $total_sale_quantity = 0;
            //for every warehouse's quantity
            foreach ($detail->quantities as $quantities) {
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
        $user = Auth::user()->business_id;
        $products = Product::where('business_id', $user)->has('warehouses')->with(['warehouses', 'unit'])->active()->get();
        return response()->json($products, 200);
    }

    public function filterWiseProducts(Request $request)
    {

        $business_id = auth()->user()->business_id;

        $products_query = Product::where('business_id', $business_id)
            ->has('warehouses')->with(['warehouses', 'unit'])
            ->active();

        $products_query = $this->productFilters($products_query, $request);

        $products = $products_query->get();

        //return response($request->all());
        return response($products);
    }

    /**
     * Product Filters
     */
    private function productFilters($products, $request)
    {
        foreach ($request->filters as $key => $value) {
            if ($key == 'categoryId' and $value) {
                $products = $products->where('category_id', $value);
                continue;
            }

            if ($key === 'productName' and $value) {
                $products = $products->where('name', 'LIKE', '%' . $value . '%');
                continue;
            }
        }

        return $products;
    }


    /**
     * All products
     * @return \Illuminate\Http\JsonResponse
     */
    public function allProducts()
    {
        $user = Auth::user()->business_id;
        $products = Product::where('business_id', $user)->with(['warehouses', 'unit'])->orderBy('name', 'ASC')->active()->get();

        return response()->json($products, 200);
    }


    /**
     * Get Product details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productDetails(Request $request)
    {
        //return $request->all();

        $productQuery = Product::with(['warehouses', 'unit']);

        if ($request->has('code')) {
            $productQuery->where('code', $request->code);
        } else if ($request->has('barcode')) {
            $productQuery->where('barcode', $request->barcode);
        }

        $product = $productQuery->firstOrFail();

        return response()->json($product, 200);
    }


    /**
     * All active categories
     * @return \Illuminate\Http\JsonResponse
     */
    public function allActiveCategories()
    {
        $business_id = Auth::user()->business_id;
        return response()->json(Category::where('business_id', $business_id)->active()->get(), 200);
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

        if (count($errors)) {
            return response()->json($errors, 422);
        }

        foreach ($request->quantities as $product_id => $quantities) {
            $current_sale_details = $sale->saleDetails->where('product_id', $product_id)->first();

            $current_product_sale_price = $current_sale_details->sale_price;

            //init total quantity
            $total_quantity = 0;

            foreach ($quantities as $warehouse_id => $quantity) {
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

        foreach ($_quantities as $product_id => $quantities) {
            $current_sale_product = $sale->saleDetails->where('product_id', $product_id)->first();

            $warehouses = $current_sale_product->quantities;

            foreach ($quantities as $warehouse_id => $quantity) {
                $formatted_quantity = $this->formattedSingleQuantity($quantity, count($current_sale_product->product->product_unit_labels));
                $warehouse_wise_quantity_units = Converter::convert($formatted_quantity, $current_sale_product->product->unit_code, 'u');
                $available_quantity = $warehouses->where('id', $warehouse_id)->first()->sale_product_rest_quantity;

                if ($warehouse_wise_quantity_units['result'] > $available_quantity) {
                    $errors[$sale->id][$product_id][$warehouse_id] = 'Insufficient quantity';
                }
            }
        }
        return $errors;
    }

    public function deliver(Sale $sale)
    {

        $sale->update([
            'delivered' => !$sale->delivered
        ]);

        return redirect()->back()->withSuccess('Status changed successfully');
    }

    /**
     * Salesmen list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function salesmen()
    {
        $data['salesmen'] = User::with('media')->get();
        $data['login_user_id'] = Auth::user()->id;

        return response()->json($data);
    }

    public function addDiscount(Request $request, $id)
    {
        $request->validate([
            'discount' => 'required|string'
        ]);
        $sale = Sale::findOrFail($id);
        // find customer
        $customer = Party::where('id', $sale->party_id)->first();
        // increment customer balance
        $customer->increment('balance', $request->discount);

        if($sale->due > 0){
            $sale->decrement('due', $request->discount);
            if($sale->due > 0){
                $sale->due = 0;
                $sale->save();
            }
        }
        $sale->increment('customer_balance', $request->discount);
        $sale->increment('discount', $request->discount);

        session()->flash('success', 'discount added successfully');
        return redirect()->back();
    }
}
