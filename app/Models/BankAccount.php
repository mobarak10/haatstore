<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BankAccount extends Model {
    use SoftDeletes;

	protected $fillable = [
		'bank_id',
		'account_name',
		'account_number',
		'branch',
		'type',
		'balance',
		'note'
	];
    public function bank() {
    	return $this->belongsTo('App\Models\Bank');
    }

    /**
     * Get all of the ledgers for the bank-account.
     */
    public function ledgers() {
        return $this->morphToMany('App\Models\Ledger', 'ledgerable');
    }

}
