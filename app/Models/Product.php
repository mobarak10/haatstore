<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brand_id',
        'category_id',
        'code',
        'barcode',
        'description',
        'model',
        'name',
        'party_id',
        'purchase_price',
        'purchase_price_with_cost',
        'retail_price',
        'slug',
        'stock_alert',
        'unit_id',
        'wholesale_price',
        'active',
        'business_id'
    ];

    protected $casts = [
        'barcode' => 'string',
     ];

    //to add code into json
    protected $appends = ['unit_code', 'product_unit_labels'];


    /*-----Scope Start-----*/

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', 0);
    }

    /*------Scope End-----*/

    /**
     * Get Warehouse with quantity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function warehouses()
    {
        return $this->belongsToMany('\App\Models\Warehouse', 'stocks')
            ->withPivot('quantity','id', 'avarage_purchase_price')
            ->withTimestamps()
            ->as('stock');
    }

    // public function warehouse(){
    //     return $this->belongsTo('App\Models\Warehouse', 'warehouse_id');
    // }

    public function stock() {
        return $this->hasMany('\App\Models\Stock');
    }

    public function damageStock() {
        return $this->hasMany('\App\Models\DamageStock');
    }

    /**
     * Get Warehouse with quantity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function damageProductsInWarehouse()
    {
        return $this->belongsToMany('\App\Models\Warehouse', 'damage_stocks')
            ->withPivot('quantity','id', 'operator_id')
            ->withTimestamps()
            ->as('damage');
    }

    /**
     * Get purchases products
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function purchaseProductsInWarehouse()
    {
        return $this->belongsToMany('\App\Models\Warehouse', 'purchase_quantities')
            ->withPivot('quantity', 'purchase_details_id', 'product_id', 'free_quantity')
            ->withTimestamps()
            ->as('quantity');
    }

    /**
     * Supplier
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo('\App\Models\Party', 'party_id');
    }

    /**
     * Brand
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo('\App\Models\Brand', 'brand_id');
    }

    /**
     * Category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('\App\Models\Category', 'category_id');
    }

    /**
     * Unit
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo('\App\Models\Unit', 'unit_id');
    }

    /**
     * All sell details of product
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleDetails()
    {
        return $this->hasMany('\App\Models\SaleDetails');
    }

    /**
     * All sell details of product
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleDetailsWarehouse()
    {
        return $this->hasMany('\App\Models\SaleDetailsWarehouse');
    }

    /**
     * All return quantity of product
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleReturnQuantity()
    {
        return $this->hasMany('\App\Models\SaleReturnQuantity');
    }

    /**
     * All return details of product
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleReturnProduct()
    {
        return $this->hasMany('\App\Models\SaleReturnProduct');
    }

    /**
     * return purchase quantity
     * @return HasMany
     */
    public function purchaseQuantity()
    {
        return $this->hasMany('App\Models\PurchaseQuantity');
    }

    /**
     * return purchase return quantity
     * @return HasMany
     */
    public function purchaseReturnQuantity()
    {
        return $this->hasMany('App\Models\PurchaseReturnQuantity');
    }

    /**
     * Get unit code
     * @return mixed
     */
    public function getUnitCodeAttribute()
    {
        return $this->unit->code;
    }

    /**
     * Get Product unit labels
     * @return array
     */
    public function getProductUnitLabelsAttribute()
    {
        return explode('/', $this->unit->labels);
    }
}
