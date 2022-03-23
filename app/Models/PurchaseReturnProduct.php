<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnProduct extends Model
{
    protected $fillable = ['purchase_return_id', 'product_id', 'return_price', 'charge', 'charge_type'];

    protected $appends = ['total_return_product_quantity', 'total_return_product_quantity_in_unit', 'purchase_return_product_line_total'];

    protected $with = ['quantities'];

    /**
     * Product
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('\App\Models\Product');
    }
    /**
     * Purchase return
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function quantities()
    {
        return $this->belongsToMany('\App\Models\Warehouse', 'purchase_return_quantities')
            ->withPivot('purchase_id', 'product_id', 'quantity')
            ->withTimestamps()
            ->as('purchase_return_quantity');
    }


    /*----Accessor Start-----*/

    public function getTotalReturnProductQuantityAttribute()
    {
        $total_quantity = 0;

        foreach ($this->quantities as $warehouse){
            $total_quantity += $warehouse->purchase_return_quantity->quantity;
        }

        return $total_quantity;
    }


    public function getTotalReturnProductQuantityInUnitAttribute()
    {
        $unit_code = Product::withTrashed()->find($this->product_id)->unit_code;
        return Converter::convert($this->total_return_product_quantity, $unit_code, 'd');
    }

    public function getPurchaseReturnProductLineTotalAttribute()
    {
        return $this->return_price * $this->total_return_product_quantity ;
    }

    public function purchaseReturn(){
        return $this->belongsTo('App\Models\PurchaseReturn', 'purchase_return_id');
    }

    /*----Accessor End-----*/
}
