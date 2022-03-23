<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;

class LedgerReportController extends Controller
{
    private $meta = [
        'title'   => 'Ledger Report',
        'menu'    => 'ledger-report',
        'submenu' => 'ledger-report'
    ];

    public $data = [];

    public function __construct() {
        $this->middleware('auth');
    }

    public function supplierLedger()
    {
        $this->data['parties'] = Party::suppliers()->get();
        $party = '';

        if (request()->search){
            $party = Party::where('id', request()->party_id)->first();

            $this->data['party_ledgers'] = $party->ledgers()->whereBetween('date', [\request()->from_date, \request()->to_date])->get();
            $this->data['last_balance'] = $party->ledgers()->whereBetween('date', [\request()->from_date, \request()->to_date])->get()->last();
            $this->data['first_balance'] = $party->ledgers()->whereBetween('date', [\request()->from_date, \request()->to_date])->get()->first();

        }

        return view('user.reports.ledger.supplierLedger', compact('party'))->with($this->meta)->with($this->data);
    }

    public function customerLedger()
    {
        $this->data['parties'] = Party::customers()->get();
        $party = '';
        if (request()->search){
            $party = Party::where('id', request()->party_id)->first();
            $this->data['party_ledgers'] = $party->ledgers()->whereBetween('date', [\request()->from_date, \request()->to_date])->get();
            $this->data['last_balance'] = $party->ledgers()->whereBetween('date', [\request()->from_date, \request()->to_date])->get()->last();
            $this->data['first_balance'] = $party->ledgers()->whereBetween('date', [\request()->from_date, \request()->to_date])->get()->first();
        }
        return view('user.reports.ledger.customerLedger', compact('party'))->with($this->meta)->with($this->data);
    }
}
