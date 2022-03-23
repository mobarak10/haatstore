<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnQuantity extends Model {

    public function purchaseReturnProduct(){
        return $this->belongsTo('App\Models\PurchaseReturnProduct', 'purchase_return_product_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}
