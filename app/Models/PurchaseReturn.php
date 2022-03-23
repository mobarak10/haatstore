<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable = ['purchase_id', 'user_id', 'adjustment', 'charge', 'charge_type', 'note', 'business_id'];

    protected $with = ['purchaseReturnProducts'];

    protected $appends = ['purchase_return_subtotal', 'purchase_return_total'];

    public function purchaseReturnProducts()
    {
        return $this->hasMany('\App\Models\PurchaseReturnProduct', 'purchase_return_id');
    }


    /*---Accessor Start----*/


    public function getPurchaseReturnSubtotalAttribute()
    {
        $subtotal = 0;

        foreach ($this->purchaseReturnProducts as $return_product) {
            $subtotal += $return_product->purchase_return_product_line_total;
        }

        return $subtotal;
    }


    public function getPurchaseReturnTotalAttribute()
    {
        return $this->purchase_return_subtotal + $this->adjustment;
    }

    public function user(){
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    public function purchase(){
        return $this->belongsTo('App\Models\Purchase', 'purchase_id');
    }
    /*---Accessor End----*/
}
