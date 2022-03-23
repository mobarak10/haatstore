<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Model;

class SaleDetails extends Model
{
    protected $appends = [
        'total_quantity',
        'total_quantity_in_format',
        'total_product_rest_quantities',
        'total_product_rest_quantities_in_unit',
        'sale_product_purchase_price_total',
    ];

    protected $fillable = [
        'sale_id',
        'product_id',
        'purchase_price',
        'sale_price',
        'sale_type',
        'discount',
        'discount_type',
        'line_total'
        ];

    /*---Accessor Start---*/

    /**
     * Total sale quantities
     * @return int
     */
    public function getTotalQuantityAttribute()
    {
        $total_quantity = 0;
        foreach ($this->quantities as $quantity) {
            $total_quantity += $quantity->quantity->quantity;
        }
        return $total_quantity;
    }

    /**
     * Total sale quantities in units
     * @return array
     */
    public function getTotalQuantityInFormatAttribute()
    {
        return Converter::convert($this->getTotalQuantityAttribute(), $this->product->unit_code, 'd');
    }

    /**
     * Total product rest quantities
     * @return mixed
     */
    public function getTotalProductRestQuantitiesAttribute()
    {
        return $this->quantities->pluck('sale_product_rest_quantity')->sum();
    }

    /**
     * Total product rest quantities in units
     * @return array
     */
    public function getTotalProductRestQuantitiesInUnitAttribute()
    {
        return Converter::convert($this->total_product_rest_quantities, $this->product->unit_code, 'd');
    }

    /**
     * get sale product purchase price total
     * @return float|int
     */
    public function getSaleProductPurchasePriceTotalAttribute()
    {
        return $this->purchase_price * $this->total_quantity;
    }

    /*----Accessor End---*/

    /**
     * Quantities of sale (warehouse wise)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function quantities()
    {
        return $this->belongsToMany('\App\Models\Warehouse', 'sale_details_warehouses')
            ->withPivot('product_id', 'sale_id', 'quantity')
            ->withTimestamps()
            ->as('quantity');
    }

    /**
     * Sale table relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('\App\Models\Sale');
    }

    /**
     * Product
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('\App\Models\Product');
    }
}
