<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnQuantity extends Model {
    protected $appends = ['return_product_purchase_price_total'];
    protected $fillable = [
        'sale_id',
        'sale_return_id',
        'warehouse_id',
        'product_id',
        'quantity',
    ];

    public function sale(){
        return $this->belongsTo('App\Models\Sale', 'sale_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function getReturnProductPurchasePriceTotalAttribute()
    {
        return $this->quantity * $this->product->purchase_price;
    }
}
