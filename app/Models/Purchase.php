<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['party_id', 'cash_id', 'bank_account_id', 'voucher_no', 'date', 'subtotal', 'adjustment', 'discount', 'discount_type', 'paid', 'note', 'business_id'];
    protected $dates = ['date'];
    protected $appends = ['grand_total'];

    /**
     * Purchase details
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany('\App\Models\PurchaseDetails');
    }

    public function getGrandTotalAttribute(){
        return $this->subtotal + $this->adjustment - $this->discount;
    }


    /**
     * Purchase returns
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchaseReturns()
    {
        return $this->hasMany('\App\Models\PurchaseReturn');
    }

    /**
     * Supplier
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function party()
    {
        return $this->belongsTo('\App\Models\Party');
    }
}
