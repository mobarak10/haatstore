<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cash extends Model {
    use SoftDeletes;

    /**
     * Get all of the ledgers for the cash.
     */
    public function ledgers() {
        return $this->morphToMany('App\Models\Ledger', 'ledgerable');
    }

    //  public function business(){
    //     return $this->belongsTo('App\Models\Buseness', 'business_id');
    // }

    /**
     * Get the capitals for the Cash.
     */
    public function capitals()
    {
        return $this->hasMany(Capital::class);
    }

}
