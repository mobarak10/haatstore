@extends('layouts.user')

@section('title', $title)

@push('style')
    <link href="{{ asset('public/css/stock.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card current-stock">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        @if(request()->search)
                            <div>
                                <h5 class="m-0"><span>Cash Book Report {{ request()->date }} </span></h5>
                                <h5 class="m-0 d-none d-print-block"><span>User: {{ Illuminate\Support\Facades\Auth::user()->name }} </span></h5>
                            </div>
                        @else
                            <div>
                                <h5 class="m-0"><span>Cash Book Report {{ date('Y-m-d') }} </span></h5>
                                <h5 class="m-0 d-none d-print-block"><span>User: {{ Illuminate\Support\Facades\Auth::user()->name }} </span></h5>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-center pt-4 d-none d-print-block">Haat Store</h1>
                            <h5 class="text-center pb-4 d-none d-print-block">Cash Book</h5>
                            <hr>
                        </div>
                        <div>
                            <h5 class="m-0 d-none d-print-block"><span>Print Time: {{ date("h:i:sa") }} </span></h5>
                            <h5 class="m-0 d-none d-print-block"><span>Print Date: {{ date("Y-M-d") }} </span></h5>
                        </div>
                        <div class="action-area print-none" role="group" aria-label="Action area">
                            <a href="{{ route('cashBook.index') }}" class="btn btn-primary" title="Refresh">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </a>
                            <a href="#" onclick="window.print();" title="Print" class="btn btn-warning">
                                <i aria-hidden="true" class="fa fa-print"></i>
                            </a>
                        </div>
                    </div>

                    <!-- search form start -->
                    <div class="card-body print-none">
                        <form action="{{ route('cashBook.index') }}" method="GET" class="row">
                            <input type="hidden" name="search" value="1">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label for="business">Date</label>
                                        <input type="date" class="form-control" name="date" value="{{ (request()->search) ? request()->date : date('Y-m-d') }}" placeholder="Enter date for search">
                                    </div>
                                    <div class="col-md-2" style="padding-top: 30px">
                                        <button type="submit" class="btn btn-primary" title="search">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- search form end -->
                    <div class="card-body p-2">
                        <!-- search form start -->
                        <div class="form-row col-md-12 mx-0">

                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="ml-3">Income Details</h4>
                                    <h4 class="mr-3">Amount</h4>
                                </div>
                                <table class="table table-striped table-sm">
                                    <tr>
                                        <th style="font-size: larger">Opening Balance: </th>
                                        <th class="text-right">{{ number_format($closing_balance->amount ?? 0, 2) }}</th>
                                    </tr>

                                    <tr>
                                        <th style="font-size: larger">Received: </th>
                                        <th></th>
                                    </tr>
                                    @php
                                        $total_sale = 0;
                                        $total_income_transaction = 0;
                                        $total_due_receive = 0;
                                        $total_capital = 0;
                                    @endphp

                                        <tr>
                                            <td>Total Sale</td>
                                            <td class="text-right">{{ number_format(($sales->sum('tendered') - $sales->sum('change')), 2) }}</td>
                                        </tr>
                                        @php
                                            $total_sale = ($sales->sum('tendered') - $sales->sum('change'));
                                        @endphp
                                    <tr>
                                        <th style="font-size: larger">Transaction: </th>
                                        <th></th>
                                    </tr>

                                    <tr>
                                        <td>Transaction Bank to Cash</td>
                                        <td class="text-right">{{ number_format($transaction_form_bank->sum('amount'), 2) }}</td>
                                    </tr>
                                    @php
                                        $total_income_transaction = $transaction_form_bank->sum('amount');
                                    @endphp


                                    <tr>
                                        <th style="font-size: larger">Due Receive: </th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td>Total Due Receive</td>
                                        <td class="text-right">{{ number_format($due_receive->sum('amount'), 2) }}</td>
                                    </tr>
                                    @php
                                        $total_due_receive = $due_receive->sum('amount');
                                    @endphp

                                    <tr>
                                        <th style="font-size: larger">Capital Added: </th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td>Total Amount</td>
                                        <td class="text-right">{{ number_format($capital->sum('amount'), 2) }}</td>
                                    </tr>
                                    @php
                                        $total_capital = $capital->sum('amount');
                                    @endphp

                                    <tr>
                                        <td>Grand Total</td>
                                        <td class="text-right">
                                            @php
                                                $total_income = ($closing_balance->amount ?? 0) + $total_sale + $total_income_transaction + $total_due_receive + $total_capital;
                                            @endphp
                                            {{ number_format($total_income, 2) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="ml-3">Expense Details</h4>
                                    <h4 class="mr-3">Amount</h4>
                                </div>
                                <table class="table table-bordered table-sm">
                                    @php
                                        $total_expense = 0;
                                        $total_due_paid = 0;
                                        $total_purchase_paid = 0;
                                        $total_expanse_transaction = 0;
                                    @endphp
                                    <tr>
                                        <th style="font-size: larger">Expenses:</th>
                                        <th></th>
                                    </tr>

                                    <tr>
                                        <td>Total Expense</td>
                                        <td class="text-right">{{ number_format(($expenses->sum('amount')), 2) }}</td>
                                    </tr>
                                    @php
                                        $total_expense = $expenses->sum('amount')
                                    @endphp

                                    <tr>
                                        <th style="font-size: larger">Due Paid:</th>
                                        <th></th>
                                    </tr>

                                    <tr>
                                        <td>Total Due Paid</td>
                                        <td class="text-right">{{ number_format(($due_paid->sum('amount')), 2) }}</td>
                                    </tr>
                                    @php
                                        $total_due_paid = $due_paid->sum('amount');
                                    @endphp

                                    <tr>
                                        <th style="font-size: larger">Purchases:</th>
                                        <th></th>
                                    </tr>

                                    <tr>
                                        <td>Total Purchase</td>
                                        <td class="text-right">{{ number_format($purchases->sum('paid'), 2) }}</td>
                                    </tr>
                                    @php
                                        $total_purchase_paid = $purchases->sum('paid');
                                    @endphp

                                    <tr>
                                        <th>Total Sale Return: </th>
                                        <td class="text-right">{{ number_format($total_sale_return,2) }}</td>
                                    </tr>

                                    <tr>
                                        <th style="font-size: larger">Transaction:</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td>Transfer Cash To Bank</td>
                                        <td class="text-right">{{ number_format(($transaction_form_cash->sum('amount')), 2) }}</td>
                                    </tr>
                                    @php
                                        $total_expanse_transaction = $transaction_form_cash->sum('amount');
                                    @endphp

                                    <tr>
                                        <td>Grand Total</td>
                                        @php
                                            $total_expense = $total_sale_return
                                                            + $total_expense
                                                            + $total_due_paid
                                                            + $total_expanse_transaction
                                                            + $total_purchase_paid;
                                        @endphp
                                        <td class="text-right">
                                            {{ number_format($total_expense, 2)
                                            }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-12">
                                <table class="table table-striped table-sm">
                                    <tr>
                                        <td class="font-weight-bold">Total Due Sale:</td>
                                        <td class="text-right font-weight-bold">{{ number_format($sales->sum('due'), 2) }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12">
                                <table class="table table-bordered table-sm">
                                    <tr>
                                        <td class="text-right">Cash in hand:</td>
                                        @if(request()->search)
                                            <td class="text-right">
                                                {{ number_format($total_income - $total_expense, 2) }}
                                            </td>
                                        @else
                                            <td class="text-right">
                                                {{ number_format($cashes->sum('amount'), 2) }}
                                            </td>
                                        @endif
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12 print-none">
                                <div class="text-right">

                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newCashModal" title="Create new cash">
                                        <span class="d-block">Cash Close</span>
                                    </button>
                                </div>
                            </div>

                            <!-- New cash modal start -->
                            <div class="modal fade print-none" id="newCashModal" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('cashBook.storeBalance') }}" method="post">
                                            @csrf

                                            <div class="modal-header">
                                                <h5 class="modal-title" id="insertModalLabel">Closing Balance</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group required">
                                                    <label for="date">Date</label>
                                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" id="date" required>
                                                </div>

                                                <div class="form-group required">
                                                    <input type="text" name="amount" readonly class="form-control" value="{{ $cashes->sum('amount') }}" id="amount" placeholder="0.00" step="any" required>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('contents.close')</button>
                                                <button type="submit" class="btn btn-primary">@lang('contents.save')</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- New cash modal end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
