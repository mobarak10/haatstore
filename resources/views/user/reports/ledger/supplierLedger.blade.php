@extends('layouts.user')

@section('title', 'Supplier Ledger')

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
                                <h5 class="m-0"><span>Ledger {{ request()->from_date }} to {{ request()->to_date }} </span></h5>
                                <h5 class="m-0 d-none d-print-block"><span>User: {{ Illuminate\Support\Facades\Auth::user()->name }} </span></h5>
                                <h5 class="m-0 d-none d-print-block">Party Name: {{ $party->name }}</h5>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-center pt-4 d-none d-print-block">Omer Ceramics</h1>
                            <h5 class="text-center pb-4 d-none d-print-block">Ledger Report</h5>
                            <hr>
                        </div>
                        <div>
                            <h5 class="m-0 d-none d-print-block"><span>Print Time: {{ date("h:i:sa") }} </span></h5>
                            <h5 class="m-0 d-none d-print-block"><span>Print Date: {{ date("Y-M-d") }} </span></h5>
                        </div>
                        <div class="action-area print-none" role="group" aria-label="Action area">
                            <a href="{{ route('report.supplierLedger') }}" class="btn btn-primary" title="Refresh">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </a>
                            <a href="#" onclick="window.print();" title="Print" class="btn btn-warning">
                                <i aria-hidden="true" class="fa fa-print"></i>
                            </a>
                        </div>
                    </div>

                    <!-- search form start -->
                    <div class="card-body print-none">
                        <form action="{{ route('report.supplierLedger') }}" method="GET" class="row">
                            <input type="hidden" name="search" value="1">
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-3 required">
                                    <label for="from_date">From Date</label>
                                    <input type="date" name="from_date" id="from_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                                </div>

                                <div class="form-group col-md-3 required">
                                    <label for="to_date">To Date</label>
                                    <input type="date" name="to_date" id="to_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                                </div>

                                <div class="form-group col-md-4 required">
                                    <label for="car_id">Customer</label>
                                    <select name="party_id" class="form-control" required>
                                        <option value="">Choose One</option>
                                        @foreach($parties as $party)
                                            <option {{ (request()->party_id == $party->id) ? 'selected' : '' }} value="{{ $party->id }}">{{ $party->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-2 text-right" style="margin-top: 30px">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i> &nbsp;
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- search form end -->
                    @if(request()->search)
                        <div class="card-body p-2">
                            <!-- search form start -->
                            <div class="form-row col-md-12 mx-0">

                                <div class="col-sm-12">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Particular</th>
                                            <th class="text-right">Debit</th>
                                            <th class="text-right">Credit</th>
                                            <th class="text-right">Balance</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @php
                                            $total_debit = 0;
                                            $total_credit = 0;
                                            $total_balance = 0;
                                            $first_debit = 0;
                                            $first_credit = 0;
                                        @endphp
                                        <tr>
                                            <td>1.</td>
                                            <td></td>
                                            <td>Opening Balance</td>
                                            @if($first_balance)
                                                @if($first_balance->debit > 0)
                                                    @php
                                                        $first_credit = ($first_balance->balance - $first_balance->debit);
                                                    @endphp
                                                    <td colspan="3" class="text-right">{{ number_format(($first_balance->balance - $first_balance->debit), 2) }}</td>
                                                @elseif($first_balance->credit > 0)
                                                    @php
                                                        $first_debit = ($first_balance->balance + $first_balance->credit);
                                                    @endphp
                                                    <td colspan="3" class="text-right">{{ number_format(($first_balance->balance + $first_balance->credit), 2) }}</td>
                                                @endif
                                            @endif
                                        </tr>
                                        @forelse($party_ledgers as $ledger)
                                            <tr>
                                                <td>{{ $loop->iteration + 1 }}</td>
                                                <td>{{ $ledger->date->format('d-M-Y') }}</td>
                                                <td style="max-width: 200px" class="text-wrap">{{ $ledger->description }} {{ $ledger->date->format('d.m.Y') }}</td>
                                                <td class="text-right">{{ number_format($ledger->debit, 2) }}</td>
                                                <td class="text-right">{{ number_format($ledger->credit, 2) }}</td>
                                                <td class="text-right">{{ ($ledger->balance < 0) ? '('.number_format($ledger->balance, 2).')' : number_format($ledger->balance, 2) }}</td>
                                            </tr>
                                            @php
                                                $total_debit += $ledger->debit;
                                                $total_credit += $ledger->credit;
                                                $total_balance += $ledger->balance;
                                            @endphp
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No ledger available</td>
                                            </tr>
                                        @endforelse
                                        <tr>
                                            <th colspan="3" class="text-right">Total Balance</th>
                                            <td class="text-right">
                                                {{ number_format($total_debit + $first_debit, 2) }}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($total_credit - $first_credit, 2) }}
                                            </td>
                                            <td class="text-right">{{ number_format($last_balance->balance ?? 0, 2) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <h5 class="text-right">Closing Balance: {{ ($last_balance->balance ?? 0 >= 0) ? number_format($last_balance->balance ?? 0, 2) : '('.number_format($last_balance->balance ?? 0, 2).')' }}</h5>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
