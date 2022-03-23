<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DueManage extends Model {
    protected $fillable = [
        'party_id',
        'date',
        'amount',
        'payment_type',
        'current_balance',
        'cash_id',
        'bank_id',
        'bank_account_id',
        'check_issue_date',
        'check_number',
        'user_id',
        'description',
        'business_id',
    ];
    protected $dates = ['date'];

    public function party() {
    	return $this->belongsTo('App\Models\Party');
    }
}
