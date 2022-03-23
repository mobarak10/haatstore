<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Model;

class SaleReturnProduct extends Model
{
    protected $fillable = ['product_id', 'sale_return_id', 'return_price', 'charge', 'charge_type'];

    protected $appends = ['total_return_quantity', 'total_return_quantity_in_unit', 'return_product_price_total', 'return_product_purchase_price_total'];

    /*----Accessor Start----*/

    /**
     * Get Total return quantity
     * @return array
     */
    public function getTotalReturnQuantityAttribute()
    {
        $total_quantity = 0;
        foreach ($this->quantities as $warehouse){
            $total_quantity += $warehouse->sale_return_quantity->quantity;
        }

        return $total_quantity;
    }

    /**
     * Get Total return quantity in display format
     * @return array
     */
    public function getTotalReturnQuantityInUnitAttribute()
    {
        return Converter::convert($this->total_return_quantity, $this->product->unit_code,'d');
    }

    public function getReturnProductPriceTotalAttribute()
    {
        return $this->total_return_quantity * $this->return_price;
    }

    /**
     * @return float|int
     */
    public function getReturnProductPurchasePriceTotalAttribute()
    {
        return $this->total_return_quantity * $this->product->purchase_price;
    }

    /*-----Accessor End-----*/

    /**
     * Sale return
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function quantities()
    {
        return $this->belongsToMany('\App\Models\Warehouse', 'sale_return_quantities', 'sale_return_id')
            ->withPivot('sale_id', 'product_id', 'quantity')
            ->withTimestamps()
            ->as('sale_return_quantity');
    }

    /**
     * Sale return
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function saleReturn()
    {
        return $this->belongsTo('\App\Models\SaleReturn');
    }

    /**
     * Product details
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('\App\Models\Product');
    }
}
