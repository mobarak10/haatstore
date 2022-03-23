<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;
    public static $snakeAttributes = false; //camelcase relation true

    protected $appends = ['grand_total', 'total_discount', 'previous_balance', 'sale_product_purchase_price', 'return_product_price_total', 'return_product_purchase_price_total'];

    protected $fillable = ['invoice_no', 'party_id', 'payment_type', 'user_id', 'subtotal', 'total_purchase_price', 'vat', 'discount', 'discount_type', 'tendered', 'due', 'change', 'business_id', 'customer_balance', 'adjust_to_customer_balance', 'salesman_id', 'delivered'];

    /*======================Accessor Start======================*/

    /**
     * get total discount
     * @return float|int|mixed
     */
    public function getTotalDiscountAttribute()
    {
        $total_discount = 0;

        $discount = $this->discount;
        if ($this->discount_type === 'flat') {
            $total_discount += $discount;
        } else {
            $total_discount += (($this->subtotal * $discount) / 100);
        }

        return $total_discount;
    }

    public function getPreviousBalanceAttribute()
    {
        if($this->grand_total > $this->tendered && $this->change > 0) {
            return $this->grand_total + $this->change;
        }
        else if ($this->grand_total > $this->tendered) {
            return $this->customer_balance - ($this->tendered - $this->grand_total);
        }
        else if ($this->adjust_to_customer_balance) {
            return $this->customer_balance - ($this->tendered - $this->grand_total);
        }
        else if($this->grand_total < $this->tendered){
            return ($this->tendered - $this->change) - $this->grand_total;
        }
        else {
            return $this->customer_balance;
        }
    }

    /**
     * Get Grand Total of a sale
     *
     * @return number
     */
    public function getGrandTotalAttribute()
    {
        $vat = $this->vat;
        $subtotal = $this->subtotal;
        $total = $subtotal + (($subtotal * $vat) / 100);

        $discount = $this->discount;
        if ($this->discount_type === 'flat') {
            $total -= $discount;
        } else {
            $total -= (($total * $discount) / 100);
        }

        return $total;
    }

    /**
     * get sale product purchase price
     * @return int|mixed
     */
    public function getSaleProductPurchasePriceAttribute()
    {
        return $this->saleDetails->sum('sale_product_purchase_price_total');
    }

    /**
     * Return product return price total
     * @return mixed
     */
    public function getReturnProductPriceTotalAttribute()
    {
        return $this->saleReturns->sum('return_product_price_total');
    }

    /**
     * Return product return purchase price total
     * @return mixed
     */
//    public function getReturnProductPurchasePriceTotalAttribute()
//    {
//        return $this->saleReturns->sum('return_product_price_total');
//    }

    public function getReturnProductPurchasePriceTotalAttribute()
    {
        return $this->saleReturnQuantity->sum('return_product_purchase_price_total');
    }

    /*======================Accessor End======================*/


    /*======================Relationship Start======================*/

    /**
     * Sale Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function salePayment(){
        return $this->hasOne('\App\Models\SalePayment');
    }

    /**
     * Sale details
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleDetails()
    {
        return $this->hasMany('\App\Models\SaleDetails');
    }
    /**
     * Sale returns
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleReturns()
    {
        return $this->hasMany('\App\Models\SaleReturn');
    }

    /**
     * Sale returns
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleReturnQuantity()
    {
        return $this->hasMany('\App\Models\SaleReturnQuantity');
    }

    /**
     * Customer
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo('\App\Models\Party', 'party_id');
    }

    /**
     * Operator
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function operator()
    {
        return $this->belongsTo('\App\Models\User\User', 'user_id');
    }

    public function business()
    {
        return $this->belongsTo('\App\Models\Business', 'business_id');
    }

    /**
     * Salesman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesman()
    {
        return $this->belongsTo('\App\Models\User\User', 'salesman_id');
    }

    /*======================Relationship End======================*/
}
