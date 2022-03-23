<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Model;

class SaleDetailsWarehouse extends Model {

    public function sale(){
        return $this->belongsTo('App\Models\Sale', 'sale_id');
    }

    public function saleDetails(){
        return $this->belongsTo('App\Models\SaleDetails', 'sale_details_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id');
    }


}
