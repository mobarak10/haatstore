<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{
    protected $fillable = [
        'product_id',
        'purchase_price',
        'line_total',
        'purchase_price_with_cost'
    ];

    protected $appends = ['purchase_total_quantities', 'purchase_total_quantities_in_unit'];

    /*---Accessor Start---*/

    /**
     * Purchase total quantity
     * @return int
     */
    public function getPurchaseTotalQuantitiesAttribute()
    {
        $total_quantities = 0;

        foreach ($this->quantities as $quantities) {
            $total_quantities += $quantities->quantity->quantity;
        }

        return $total_quantities;
    }


    /**
     * Purchase total quantity in unit
     * @return array
     */
    public function getPurchaseTotalQuantitiesInUnitAttribute()
    {
        return Converter::convert($this->purchase_total_quantities, $this->product()->withTrashed()->first()->unit_code, 'd');
    }



    /*----Accessor End---*/

    /**
     * Purchase quantities
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function quantities()
    {
        return $this->belongsToMany('\App\Models\Warehouse', 'purchase_quantities')
            ->withPivot('product_id', 'quantity', 'free_quantity')
            ->withTimestamps()
            ->as('quantity');
    }

    public function purchase()
    {
        return $this->belongsTo('\App\Models\Purchase');
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
