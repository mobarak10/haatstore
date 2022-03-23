<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;

use App\Models\Sale;
use App\Models\DamageStock;
use App\Models\Product;
use App\Models\SaleDetails;
use App\Models\Unit;
use Auth;

class ProfitLossController extends Controller {
    private $meta = [
        'title'   => 'Profit Loss',
        'menu'    => 'reports',
        'submenu' => '',

    ];

    public function __construct() {
        $this->middleware('auth');
    }

    public function index(){

        // $saleDetails = SaleDetails::with(['product' => function($query) {
        //     // $query->addSelect([
        //     //     'unit' => Product::with(['unit' => function($unit_query) use($query) {
        //     //         $unit_query->whereColumn('unit_id', $query->unit_id)
        //     //         ->limit(1);
        //     //     }])
        //     // ]);
        //     $query->select('id', 'name');
        // }])
        // ->select('id', 'product_id')
        // ->whereBetween('created_at', ['2022-02-07'." 00:00:00", '2022-02-08'." 23:59:59"])
        // ->get();

        // $details = $saleDetails->each(function ($item) {
        //     $item->setAppends([]);
        //     $item->product->setAppends([]);
        // });


        // // set accessor null
        // return $details;

        $total_sale                        = $this->totalSaleAmount();
        $total_discount                    = $this->totalDiscountAmount();
        $total_grand_total                 = $this->totalGrandTotalAmount();
        $return_product_price_total        = $this->totalReturnProductPriceAmount();
        $total_purchase_price              = $this->totalSaleProductPurchasePrice();
        $total_damage_amount               = $this->totalDamageProductAmount();

        return view('user.reports.profit_loss.index', compact(
            'total_sale',
            'total_purchase_price',
            'total_damage_amount',
            'total_discount',
            'total_grand_total',
            'return_product_price_total'
        ))
            ->with($this->meta);
    }

    /**
     * total sale price without due
    **/
    public function totalSaleAmount(){
        $sales = $this->saleQuery();

        return $sales->sum('subtotal');
    }

    /**
     * get total discount amount
     * @return mixed
     */
    public function totalDiscountAmount()
    {
        $sales = $this->saleQuery();

        return $sales->sum('total_discount');
    }

    /**
     * get total grant total amount
     * @return mixed
     */
    public function totalGrandTotalAmount()
    {
        $sales = $this->saleQuery();

        return $sales->sum('grand_total');
    }

    public function totalReturnProductPriceAmount()
    {
        $sales = $this->saleQuery();

        return $sales->sum('return_product_price_total');
    }

    public function saleQuery()
    {
        $fromDate    = request()->fromDate . ' 00:00:00';
        $toDate      = request()->toDate  . ' 23:59:00';
        $business_id = Auth::user()->business_id;

        return Sale::with('saleDetails.quantities', 'saleReturns')
            ->where('business_id', $business_id)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();
    }


    /**
     * @return float|int
     * total sale product purchase price
     */
    public function totalSaleProductPurchasePrice()
    {
        $sales = $this->saleQuery();

        $grand_total_purchase_price = 0;

        foreach($sales as $sale){
            if($sale->total_purchase_price){
                $grand_total_purchase_price += $sale->total_purchase_price;
            }else{
                foreach($sale->saleDetails as $details){
                    $grand_total_purchase_price += ($details->purchase_price * $details->total_quantity);
                }
            }
        }

        return $grand_total_purchase_price;

    }


    /**
     * Total damage product price
    **/
    public function totalDamageProductAmount(){
        $fromDate    = request()->fromDate . ' 00:00:00';
        $toDate      = request()->toDate  . ' 23:59:00';
    	$business_id = Auth::user()->business_id;
        // sum of all damage product price
        $damages = DamageStock::where('business_id', $business_id)
        						->whereBetween('created_at', [$fromDate, $toDate])
        						->get();

        return $damages->sum('stock_price');
    }
}
