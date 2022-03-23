<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capital extends Model
{
     protected $guarded = [];


     /**
     * Get the cash for the capital.
     */
    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }
}
