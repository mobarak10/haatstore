<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $fillable = ['sale_id', 'user_id', 'adjustment', 'charge', 'charge_type', 'paid_from', 'cash_id', 'bank_account_id', 'note', 'business_id', 'adjust_to_customer_balance', 'customer_balance'];

    protected $appends = ['return_product_price_subtotal', 'return_product_price_total'];

    protected $touches = ['sale'];

    /*---Accessor Start---*/

    /**
     * Return Product return subtotal
     * @return int
     */
    public function getReturnProductPriceSubtotalAttribute()
    {
        $subtotal = 0;
        if ($this->returnProducts) $subtotal = $this->returnProducts->pluck('return_product_price_total')->sum();

        return $subtotal;
    }

    /**
     * Return product return price total
     * @return mixed
     */
    public function getReturnProductPriceTotalAttribute()
    {
        return $this->return_product_price_subtotal + $this->adjustment;
    }

    /*----Accessor End---*/

    /**
     * Sale
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('\App\Models\Sale');
    }

    /**
     * Sale Return products
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnProducts()
    {
        return $this->hasMany('\App\Models\SaleReturnProduct');
    }

    /**
     * Cash
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cash()
    {
        return $this->belongsTo('\App\Models\Cash');
    }

    /**
     * Operator
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('\App\Models\User\User');
    }
}
