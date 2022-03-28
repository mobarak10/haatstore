<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\RetailSaleRequest;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Party;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RetailSaleController extends Controller
{
    private $sale;
    private $errors;
    public function sale(RetailSaleRequest $request)
    {
    //    return $request->all();
        if ($request->customer_type === 'new') {
            // create customer
            $exits = Party::where('phone', $request->customer['phone'])->first();
            if ($exits) {
                $errors = 'Customer exist for this number';

                return response($errors, 422);
            }
        }
        DB::transaction(function () use ($request) {
            $party_id = null;
            $customer = null;
            $customer_debit_balance = 0;
            if ($request->customer_type === 'new') {
                // create customer
                $customer = Party::create(
                    [
                        'name' => $request->customer['name'] ?? 'Unknown',
                        'address' => $request->customer['address'] ?? '',
                        'phone' => $request->customer['phone'] ?? '',
                        'business_id' => auth()->user()->business_id,
                        'genus' => 'customer',
                        'type' => 'walking_customer',
                        'code' => 'PRT' . str_pad(Party::withTrashed()->max('id') + 1, 8, '0', STR_PAD_LEFT),
                    ]
                );

                $customer_debit_balance = $customer->balance;

                $party_id = $customer->id;
            }else{
                $party_id = $request->party_id;
                $customer = Party::findOrFail($request->party_id);
                $customer_debit_balance = $customer->balance;
            }

//            return $customer;

            $total_purchase_price = 0;
            foreach ($request->products as $product){
                $total_purchase_price += ($product['purchase_price'] * $product['total_quantity']);
            }

            $sale_data = [
                'date' => $request->date,
                'invoice_no' => 'INV' . str_pad(Sale::max('id') + 1, 8, '0', STR_PAD_LEFT),
                'party_id' => $party_id,
                'user_id' => Auth::user()->id,
                'payment_type' => $request->payment['method'],
                'total_purchase_price' => $total_purchase_price,
                'subtotal' => $request->payment['subtotal'],
                'discount' => $request->payment['discount'],
                'discount_type' => 'flat',
                'tendered' => $request->payment['paid'],
                'due' => $request->payment['due'] ?? 0,
                'change' => $request->payment['change'] ?? 0,
                'delivered' => $request->delivered,
                'comment' => $request->comment,
                'salesman_id' => auth()->id(),
                'business_id' => auth()->user()->business_id
            ];

            $grand_total = ($request->payment['subtotal'] - $request->payment['discount']);

            // insert sale
            $this->sale = Sale::create($sale_data);

            $sale = $this->sale;

            foreach ($request->products as $product) {

                $_product = Product::find($product['id']);

                $sale_details_data = [
                    'product_id' => $product['id'],
                    'purchase_price' => $product['purchase_price'],
                    'sale_price' => $product['price'],
                    'sale_type' => $request->sale_type,
                    'discount' => $product['discount'],
                    'discount_type' => 'flat',
                    'line_total' => $product['line_total'],
                ];

                // create sale details
                $sale_details = $sale->saleDetails()
                    ->create($sale_details_data);

                $sale_details_warehouse['product_id'] = $product['id'];
                $sale_details_warehouse['sale_id'] = $sale->id; //sell id
                $sale_details_warehouse['quantity'] = $product['total_quantity'];
                $sale_details_warehouse['created_at'] = now();
                $sale_details_warehouse['updated_at'] = now();
                //save quantity in sale_details_warehouses table
                $sale_details->quantities()->attach($product['warehouse_id'], $sale_details_warehouse);

                // decrement product quantity
                $warehouse = $_product->warehouses()
                    ->find($product['warehouse_id']);

                $quantity = $warehouse->stock->quantity;

                // if quantity is same then detach product from warehouse
                if ($quantity == $product['total_quantity']) {
                    $warehouse->products()->detach($_product->id);
                }
                else {
                    $warehouse->stock->decrement('quantity', $product['total_quantity']);
                }
            }

            // sale payment
            $sale->salePayment()
                ->create($request->payment['sale_payments']);

            // calculate paid amount
            $paid_amount = $request->payment['paid'];
            // if has change
            if (isset($request->payment['change']) && $request->payment['change'] >= 0) {
                $paid_amount = $request->payment['paid'] - $request->payment['change'];
            }

            // update customer balance
            // if customer has due then add the due in customer balance
            if (isset($request->payment['due']) && $request->payment['due'] >= 0) {
                $customer->balance = -1 * $request->payment['due'];
                $customer->save();
            }

            // set customer current balance
            if ($request->customer_type == 'new'){
                $sale->update([
                    'customer_balance' => $customer->fresh()->balance
                ]);
            }else{
                $sale->update([
                    'customer_balance' => $customer->balance
                ]);
            }

            $total_balance = $customer_debit_balance - $grand_total;

            if ($total_balance <= 0){
                $total_debit_balance = abs($total_balance);
            }else{
                $total_debit_balance = -1 * $total_balance;
            }

            // TODO Create Debit Customer Ledger
            $customer_ledger_debit = [
                'date' => now()->format('Y-m-d'),
                'description' => 'Product Sell Invoice Number ' . $sale_data['invoice_no'],
                'debit' => $grand_total,
                'balance' => $total_debit_balance,
                'note' => $request->comment,
            ];
            $customer->ledgers()->create($customer_ledger_debit);

            if ($paid_amount > 0) {
                // TODO Create Credit Customer Ledger
                $customer_ledger_credit = [
                    'date' => now()->format('Y-m-d'),
                    'description' => 'Receive Cash Invoice Number ' . $sale['invoice_no'],
                    'credit' => $paid_amount,
                    'balance' => $customer_ledger_debit['balance'] - $paid_amount,
                    'note' => $request->comment,
                ];
                $customer->ledgers()->create($customer_ledger_credit);
            }


            // increment cash or bank account balance
            switch ($request->payment['method']) {
                case 'cash':
                    $cash_id = $request->payment['sale_payments']['cash_id'];
                    Cash::find($cash_id)->increment('amount', $paid_amount);
                    // TODO Create Ledger
                    break;
                case 'bank':
                    $bank_account_id = $request->payment['sale_payments']['bank_account_id'];
                    BankAccount::find($bank_account_id)->increment('balance', $paid_amount);
                    // TODO Create Ledger
                    break;
            }
        });
        return response()->json($this->sale);

    }
}
