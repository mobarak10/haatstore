@extends('layouts.user')

@section('title', $title)

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <h1 class="text-center pt-5 pb-4 d-none d-print-block">Haat Store</h1>
                <div class="card current-stock">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="d-none d-print-block">Profit Loss Report</h4>
                        <h5 class="m-0 print-none">Profit Loss Report</h5>
                        <div class="action-area print-none" role="group" aria-label="Action area">
                            <a href="{{ route('profitLoss.index') }}" class="btn btn-primary" title="Refresh">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </a>
                            <a href="#" onclick="window.print();" title="Print" class="btn btn-warning"><i aria-hidden="true" class="fa fa-print"></i></a>
                        </div>
                    </div>

                    <!-- search form start -->
                    <div class="card-body print-none">
                        <form action="{{ route('profitLoss.index') }}" method="GET" class="row">
                            <input type="hidden" name="search" value="1">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5 required">
                                        <label for="fromdate">From Date</label>
                                        <input type="date" id="fromdate" name="fromDate" class="form-control" required value="{{ request()->fromDate ?? date('Y-m-d') }}">
                                    </div>

                                    <div class="col-md-5 required">
                                        <label for="todate">To Date</label>
                                        <input type="date" id="todate" name="toDate" class="form-control" required value="{{ request()->toDate ?? date('Y-m-d') }}">
                                    </div>

                                    <div class="col-md-2" style="padding-top: 30px">
                                        <button type="submit" class="btn btn-primary" type="button" title="search">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if(request()->search)
                        <div class="row m-2">
                            <div class="col-lg-4">
                                <a href="#" class="item-box color-1">
                                    <div class="content-box pb-3">
                                        <img src="{{ asset('public/images/bg-img/subtotal.png') }}" style="width: 100px" alt="">
                                        <div class="content">
                                            <p class="title">Total Sale Price (BDT)</p>
                                            <p style="padding-right: 5px;" class="number">{{ number_format($total_sale, 2) }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-4">
                                <a href="#" class="item-box color-2">
                                    <div class="content-box pb-3">
                                        <img src="{{ asset('public/images/bg-img/discount.png') }}" style="width: 100px" alt="">
                                        <div class="content">
                                            <p class="title">Total Discount (BDt)</p>
                                            <p class="number">{{ number_format($total_discount, 2) }}</p>
                                            {{--                                    <span>@lang('contents.sum_all_bank_balance')</span>--}}
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-4 ">
                                <a href="#" class="item-box color-3">
                                    <div class="content-box pb-3">
                                        {{--                                    <i class="fa fa-stack-exchange" aria-hidden="true"></i>--}}
                                        <img src="{{ asset('public/images/bg-img/grand_total.png') }}" style="width: 100px" alt="">
                                        <div class="content">
                                            <p class="title">Grand Total (BDT)</p>
                                            <p class="number">
                                                {{ number_format($total_grand_total, 2) }}
                                            </p>

                                            {{--                                        <span>@lang('contents.sum_all_product')</span>--}}
                                        </div>
                                    </div>
                                    {{--                                <div>--}}
                                    {{--                                    <svg width="249" height="50">--}}
                                    {{--                                        <g>--}}
                                    {{--                                            <path d="M0,25Q17.983333333333334,40.541666666666664,20.75,40.625C24.9,40.75,37.35,27.8125,41.5,26.25S58.1,24.625,62.25,25S78.85000000000001,30.625,83,30S99.59999999999998,20.8125,103.74999999999999,18.75S120.35000000000001,10.625,124.5,9.375S141.09999999999997,5,145.24999999999997,6.25S161.85,20.9375,166,21.875S182.6,16.5625,186.75,15.625S203.34999999999997,12.1875,207.49999999999997,12.5S224.1,17.5,228.25,18.75Q231.01666666666668,19.583333333333332,249,25L249,50Q231.01666666666668,50,228.25,50C224.1,50,211.64999999999998,50,207.49999999999997,50S190.9,50,186.75,50S170.15,50,166,50S149.39999999999998,50,145.24999999999997,50S128.65,50,124.5,50S107.89999999999999,50,103.74999999999999,50S87.14999999999999,50,83,50S66.4,50,62.25,50S45.65,50,41.5,50S24.9,50,20.75,50Q17.983333333333334,50,0,50Z" class="area" fill="rgba(255,255,255,0.5)"></path>--}}
                                    {{--                                        </g>--}}
                                    {{--                                    </svg>--}}
                                    {{--                                </div>--}}
                                </a>
                            </div>

                            <div class="col-lg-4">
                                <a href="#" class="item-box color-5">
                                    <div class="content-box pb-3">
                                        <img src="{{ asset('public/images/bg-img/return.png') }}" style="width: 100px" alt="">
                                        <div class="content">
                                            <p class="title">Total Return (BDT)</p>
                                            <p class="number">
                                                {{ number_format($return_product_price_total, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-4">
                                <a href="#" class="item-box color-4">
                                    <div class="content-box pb-3">
                                        <img src="{{ asset('public/images/bg-img/sale.png') }}" style="width: 100px" alt="">
                                        <div class="content">
                                            <p class="title">Total Sale Without Return (BDT)</p>
                                            <p class="number">
                                                @php
                                                    $total_sale_without_return = $total_grand_total - $return_product_price_total;
                                                @endphp
                                                {{ number_format($total_grand_total - $return_product_price_total, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-4">
                                <a href="#" class="item-box color-6">
                                    <div class="content-box pb-3">
                                        <img src="{{ asset('public/images/bg-img/purchase.png') }}" style="width: 100px" alt="">
                                        <div class="content">
                                            <p class="title">Total Purchase Price (BDT)</p>
                                            <p class="number">
                                                {{ number_format($total_purchase_price - $return_product_price_total, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-6 mx-auto">
                                <a href="#" class="item-box color-8">
                                    <div class="content-box pb-3 justify-content-center">
                                        <img src="{{ asset('public/images/bg-img/damage.png') }}" style="width: 100px" alt="">
                                        <div class="content">
                                            <p class="title">Total Damage (BDT)</p>
                                            <p class="number">
                                                {{ number_format($total_damage_amount, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-6 mx-auto">
                                <a href="#" class="item-box color-7">
                                    <div class="content-box pb-3 justify-content-center">
                                        @php
                                            $total_loss_profit = ($total_sale_without_return - ($total_purchase_price - $return_product_price_total) - $total_damage_amount);
                                        @endphp
                                        @if($total_loss_profit > 0)
                                            <img src="{{ asset('public/images/bg-img/total-profit.png') }}" style="width: 100px" alt="">
                                        @else
                                            <img src="{{ asset('public/images/bg-img/total_loss.png') }}" style="width: 100px" alt="">
                                        @endif
                                        <div class="content">
                                            <p class="title">Total {{ ($total_loss_profit > 0) ? 'Profit' : 'Loss' }} (BDT)</p>
                                            <p class="number">
                                                {{ number_format(abs($total_loss_profit), 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

