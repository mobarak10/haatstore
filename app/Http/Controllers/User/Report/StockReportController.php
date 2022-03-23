<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Unit;
use Auth;
use Illuminate\Support\Carbon;

class StockReportController extends Controller
{
    private $meta = [
        'title'   => 'Stock Report',
        'menu'    => 'reports',
        'submenu' => 'stock'
    ];

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $business_id = Auth::user()->business_id;
        $products = '';
        $units = Unit::all();
        $search_products = Product::where('business_id', $business_id)->orderBy('name')->get();

        if(request()->search) {
            $from_date = request()->date;
            $to_date = Carbon::today()->toDateString();

            if (request()->product_id != null){
                $products = Product::where('business_id', $business_id)
                    ->where('id', request()->product_id)
                    ->with('stock')
                    ->with(
                        ['damageStock' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->with(
                        ['saleDetailsWarehouse' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->with(
                        ['saleReturnQuantity' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->with(
                        ['purchaseQuantity' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->with(
                        ['purchaseReturnQuantity' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->paginate(27);
            }
            else{
                $products = Product::where('business_id', $business_id)
                    ->with('stock')
                    ->with(
                        ['damageStock' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->with(
                        ['saleDetailsWarehouse' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->with(
                        ['saleReturnQuantity' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->with(
                        ['purchaseQuantity' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->with(
                        ['purchaseReturnQuantity' => function($query) use ($to_date, $from_date) {
                            $query->whereBetween('created_at', [
                                $from_date . " 23:59:59",
                                $to_date . " 00:00:00"
                            ]);
                        }])
                    ->paginate(27);
            }
        }

        return view('user.reports.stock.index', compact(
            'products',
            'search_products',
            'units'))
            ->with($this->meta);
    }

}
