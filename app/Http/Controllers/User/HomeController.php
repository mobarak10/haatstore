<?php
namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Cash;
use App\Models\BankAccount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Purchase;
use App\Models\DamageStock;
use App\Models\Stock;
use App\Models\DueManage;
use App\Models\Expenditure;
use App\Models\Party;
use DateTime;
use Auth;

class HomeController extends Controller
{
    private $meta = [
        'title' => 'Dashboard',
        'menu' => 'dashboard',
        'submenu' => ''
    ];

    public $data = [];

    /**
     *
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     *
     */
    public function index() {
        $business_id = Auth::user()->business_id;
        $this->meta['submenu'] = 'daily-report';

        $date = date("Y-m-d");

        $cash = Cash::where('business_id', $business_id)->get();
        $account = BankAccount::where('business_id', $business_id)->get();

        //$amount = Sale::whereDate('created_at', today())->count();

        $sales = Sale::where('business_id', $business_id)->selectRaw("*, (subtotal + ((subtotal * vat)/100)) - CASE WHEN discount_type='flat' THEN discount ELSE (subtotal * discount)/100 END as grand_total")->whereRaw('DATE(created_at) = ?',$date)->get();

        $total_sale_return = SaleReturn::whereRaw('DATE(created_at) = ?',$date)->get()->sum("return_product_price_total");
        $total_purchase = Purchase::where('business_id', $business_id)->whereRaw('DATE(created_at) = ?',$date)->get();
        $damage_stock = DamageStock::where('business_id', $business_id)->get();
        $total_stock = Product::where('business_id', $business_id)->get();
        $customer_balance = Party::where('business_id', $business_id)->customers()->sum('balance');
        $suppliers = Party::where('business_id', $business_id)->selectRaw('balance')->where('genus', 'supplier')->get();
        $due_paid = Duemanage::where('business_id', $business_id)->where("payment_type","paid")->where("date",$date)->get();
        $due_receive = Duemanage::where('business_id', $business_id)->where("payment_type","received")->where("date",$date)->get();
        $expense = Expenditure::where('business_id', $business_id)->where("date", $date)->get();

        $getWeeklyPurchase = $this->getWeeklyPurchase();
        $getWeeklyExpense  = $this->getWeeklyExpense();
        $getDailySale      = $this->getDailySale();
        $getDailyReturn    = $this->getDailyReturn();

        return view('user.home', compact('cash','account','sales','total_sale_return','total_purchase','damage_stock','due_paid','due_receive','expense', 'total_stock', 'customer_balance', 'suppliers', 'getWeeklyPurchase', 'getWeeklyExpense', 'getDailySale', 'getDailyReturn'))
            ->with($this->meta, $this->data);
        // return view('admin.home')->with($this->meta);
    }

    // get lest 7 days purchase details
    public function getWeeklyPurchase() {
        $output = [];

        $business_id = Auth::user()->business_id;
        // select date
        $fromDate = Carbon::yesterday();
        $toDate = Carbon::today()->subDays(7);

        // get from DB
        $data = Purchase::where('business_id', $business_id)->whereBetween('created_at', [
            $toDate->format('Y-m-d') . " 00:00:00",
            $fromDate->format('Y-m-d') . " 23:59:59"
        ])
            ->get()
            ->groupBy(function($row) {
                return Carbon::parse($row->created_at)->format('D');
            })
            ->map(function ($row) {
                return $row->sum('grand_total');
            });

        $output['data'] = array_values($data->toArray());
        $output['labels'] = array_keys($data->toArray());

        return $output;
    }

    public function getWeeklyExpense(){
        $output = [];

        $business_id = Auth::user()->business_id;
        // select date
        $fromDate = Carbon::yesterday();
        $toDate = Carbon::today()->subDays(7);

        // get from DB
        $data = Expenditure::where('business_id', $business_id)->whereBetween('created_at', [
            $toDate->format('Y-m-d') . " 00:00:00",
            $fromDate->format('Y-m-d') . " 23:59:59"
        ])->get()
            ->groupBy(function($row) {
                return Carbon::parse($row->created_at)->format('D');
            })
            ->map(function ($row) {
                return $row->sum('amount');
            });

        $output['data'] = array_values($data->toArray());
        $output['labels'] = array_keys($data->toArray());

        return $output;
    }

    public function getDailySale(){
        $output = [];

        $business_id = Auth::user()->business_id;
        // select date
        $fromDate = Carbon::yesterday();
        $toDate = Carbon::today()->subDays(30);

        // get from DB
        $data = Sale::where('business_id', $business_id)->selectRaw("*, (subtotal + ((subtotal * vat)/100)) - CASE WHEN discount_type='flat' THEN discount ELSE (subtotal * discount)/100 END as grand_total")->whereBetween('created_at', [
            $toDate->format('Y-m-d') . " 00:00:00",
            $fromDate->format('Y-m-d') . " 23:59:59"
        ])->get()
            ->groupBy(function($row) {
                return Carbon::parse($row->created_at)->format('d');
            })
            ->map(function ($row) {
                return $row->sum('grand_total');
            });

        $output['data'] = array_values($data->toArray());
        $output['labels'] = array_keys($data->toArray());

        return $output;
    }

    public function getDailyReturn(){
        $output = [];

        $business_id = Auth::user()->business_id;
        // select date
        $fromDate = Carbon::yesterday();
        $toDate = Carbon::today()->subDays(30);

        // get from DB
        $data = SaleReturn::where('business_id', $business_id)->whereBetween('created_at', [
            $toDate->format('Y-m-d') . " 00:00:00",
            $fromDate->format('Y-m-d') . " 23:59:59"
        ])->get()
            ->groupBy(function($row) {
                return Carbon::parse($row->created_at)->format('d');
            })
            ->map(function ($row) {
                return $row->sum('return_product_price_total');
            });

        $output['data'] = array_values($data->toArray());
        $output['labels'] = array_keys($data->toArray());

        return $output;
    }

    public function generateToken() {
        return view('user.generate-token')->with($this->meta);
    }

}
