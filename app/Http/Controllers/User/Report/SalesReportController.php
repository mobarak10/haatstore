<?php

namespace App\Http\Controllers\User\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Sale;
use Auth;

class SalesReportController extends Controller {
    private $meta = [
        'title'   => 'Sales Report',
        'menu'    => 'reports',
        'submenu' => 'sales-report'
    ];

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $records = Sale::query();
        $format = 'm-Y';

        $records = $records->where('business_id', Auth::user()->business_id)
            ->latest()
            ->get()
            ->groupBy(function($item) use ($format) {
                return $item->created_at->format($format);
            });

        return view('user.reports.sales.monthly', compact('records'))
            ->with($this->meta);
    }

    public function daily($period) {
        $format = 'd-m-Y';
        $period = explode('-', request()->period);

        $records = Sale::where('business_id', Auth::user()->business_id)
            ->whereMonth('created_at', '=', $period[0])
            ->whereYear('created_at', '=', $period[1])
            ->latest()
            ->get()
            ->groupBy(function($item) use ($format) {
                return $item->created_at->format($format);
            });

        return view('user.reports.sales.daily', compact('records'))->with($this->meta);
    }

    public function details($period) {
        $records = Sale::where('business_id', Auth::user()->business_id)
            ->whereDay('created_at', '=', $period)
            ->latest()
            ->get();

        return view('user.reports.sales.details', compact('records'))->with($this->meta);
    }

}
