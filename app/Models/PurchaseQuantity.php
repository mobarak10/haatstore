<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseQuantity extends Model {

    public function purchase(){
        return $this->belongsTo('App\Models\Purchase', 'purchase_id');
    }

    public function purchaseDetails(){
        return $this->belongsTo('App\Models\PurchaseDetails', 'purchase_details_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}
