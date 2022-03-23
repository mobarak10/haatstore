<?php

namespace App\Http\Controllers\User\Report;

use App\Models\BalanceTransfer;
use App\Models\ClosingBalance;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Cash;
use App\Models\Capital;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Purchase;
use App\Models\DueManage;
use App\Models\Expenditure;
use App\Models\PurchaseReturn;
use Auth;

class CashBookController extends Controller {
    private $meta = [
        'title'   => 'Cash Book',
        'menu'    => 'reports',
        'submenu' => ''
    ];

    public $data = [];

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
    	$this->meta['submenu'] = 'daily-report';

        $date = Carbon::today();
        if (request()->search) {
            $date = request()->date;
        }
        $opening_date = '2021-08-01';
        $previous_day = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        $business_id = Auth::user()->business_id;

        // for income
        $this->data['sales'] = Sale::where('business_id', $business_id)->whereDate('created_at', $date)->select('tendered', 'discount', 'change', 'due')->get();
        $this->data['total_sale_return'] = SaleReturn::where('business_id', $business_id)->whereDate('created_at',$date)->get()->sum("return_product_price_total");
        $this->data['transaction_form_bank'] = BalanceTransfer::where('business_id', $business_id)->where('transfer_from', 'bank')->whereDate('created_at', $date)->get();
        $this->data['due_receive'] = DueManage::where('business_id', $business_id)->where('payment_type', 'received')->whereDate('created_at', $date)->get();

        // for expense
        $this->data['salaries'] = Salary::whereDate('created_at', $date)->get();
        // get closing balance
        $this->data['closing_balance'] = ClosingBalance::where('date', $previous_day)->first();

        if (!$this->data['closing_balance']){
            $this->data['closing_balance'] = ClosingBalance::whereBetween('date', [$opening_date, $previous_day])->get()->last();
        }

        $this->data['cashes'] = Cash::where('business_id', $business_id)->get();
        // return $party = Party::with('ledgers')->get();
        $this->data['expenses'] = Expenditure::where('business_id', $business_id)->whereDate('created_at', $date)->get();
        $this->data['due_paid'] = DueManage::where('business_id', $business_id)->where('payment_type', 'paid')->whereDate('created_at', $date)->get();
        $this->data['transaction_form_cash'] = BalanceTransfer::where('business_id', $business_id)->where('transfer_from', 'cash')->whereDate('created_at', $date)->get();
        $this->data['purchases'] = Purchase::where('business_id', $business_id)->whereDate('created_at', $date)->get();
        $this->data['total_purchase_return'] = PurchaseReturn::where('business_id', $business_id)->whereDate('created_at',$date)->get()->sum("purchase_return_total");
        $this->data['capital'] = Capital::whereDate('created_at', $date)->get();


        return view('user.reports.cash-book.index')->with($this->meta)->with($this->data);
    }

    public function storeBalance(Request $request)
    {
//        return $request->all();
        $data = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric'
        ]);

        $exist = ClosingBalance::where('date', $request->date)->first();
        if ($exist){
            return redirect()->back()->withError('Closing balance already exist for this date');
        }else{
            ClosingBalance::create($data);
        }
        return redirect()->back()->withSuccess('Balance close successfully');
    }
}
