@extends('layouts.user')

@section('title', $title)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-3">
                <h1 class="text-center pt-5 pb-4 d-none d-print-block">Zaman Enterprise</h1>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Product Report </h5>
                        <span class="d-none d-print-block">{{ date('d-m-Y') }}</span>
                        <div class="btn-group print-none" role="group" aria-level="Action area">
                            <a href="{{ route('productReport.index') }}" class="btn btn-info" title="Refesh list.">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0 print-none">
                        <div class="card card-body">
                            <form action="{{ route('productReport.index') }}" method="GET">
                                <input type="hidden" name="search" value="1">

                                <div class="form-row col-md-12">
                                    <div class="form-group col-md-3 required">
                                        <label for="from-date">From date</label>
                                        <input type="date" class="form-control" name="from_date" value="{{ date(request()->from_date) ?? '' }}" placeholder="From date" id="from-date" required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="to-date">To date</label>
                                        <input type="date" class="form-control" name="to_date" value="{{ date(request()->to_date) ?? '' }}" placeholder="To date" id="to-date">
                                    </div>

                                    <div class="form-group col-md-4 required">
                                        <label for="to-date">Product</label>
                                        <select name="product_id" id="product" class="form-control" required>
                                            <option value="" selected disabled>Choose one</option>
                                            @foreach($records['products'] as $product)
                                                <option {{ (request()->product_id == $product->id) ? 'selected' : '' }} value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-2" style="margin-top: 30px">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> &nbsp;
                                            Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(request()->search)
                        {{-- report header --}}
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="m-0">{{ date('j F, Y', strtotime(request()->from_date)) }} To {{ date('j F, Y', strtotime(request()->to_date)) }} Dates Purchase Reports</h5>
                                </div>

                                <div class="btn-group print-none" role="group" aria-level="Action area">
                                    <a href="#" onclick="window.print();" class="btn btn-warning" title="Print.">
                                        <i class="fa fa-print" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <table class="table table-hover table-sm">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th>Supplier</th>
                                        <th>Voucher</th>
                                        <th>Product</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Amount (BDT)</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @forelse($purchases as $purchase)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}.</td>
                                            <td>{{ $purchase->party->name ?? '' }}</td>
                                            <td><a href="{{ route('purchase.show', $purchase->id) }}">{{ $purchase->voucher_no }}</a></td>
                                            @foreach($purchase->details as $details)
                                                <td>{{ $details->product->name ?? '' }}</td>
                                                <td class="text-right">{{ $details->purchase_total_quantities_in_unit['display'] ?? '' }}</td>
                                                <td class="text-right">{{ $details->line_total }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No purchase available.</td>
                                        </tr>
                                    @endforelse

                                    <tr>
                                        <th colspan="4" class="text-right">Total </th>
                                        <th class="text-right">{{ $total_purchase_quantity_in_unit['display'] ?? '' }}</th>
                                        <th class="text-right">{{ number_format($total_purchase_price, 2) }}</th>
                                    </tr>
                                    </tbody>
                                </table>
                                <!-- paginate -->
                                <div class="float-right">{{ $purchases->appends(Request::except('page'))->links() }}</div>
                            </div>
                        </div>
                        <hr>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="m-0">{{ date('j F, Y', strtotime(request()->from_date)) }} To {{ date('j F, Y', strtotime(request()->to_date)) }} Dates Purchase Return Reports</h5>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <table class="table table-hover table-sm">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th>Supplier</th>
                                        <th>Voucher</th>
                                        <th>Product</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Amount (BDT)</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @forelse($purchase_returns as $returns)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}.</td>
                                            <td>{{ $returns->purchase->party->name ?? '' }}</td>
                                            <td><a href="{{ route('purchase.show', $returns->purchase->id) }}">{{ $returns->purchase->voucher_no }}</a></td>
                                            {{-- Added [] for error on Isobgul Bhushi22g  --}}
                                            @foreach($returns->purchaseReturnProducts as $details)
                                                <td>{{ $details->product->name ?? '' }}</td>
                                                <td class="text-right">{{ $details->total_return_product_quantity_in_unit['display'] ?? '' }}</td>
                                                <td class="text-right">{{ $details->total_return_product_quantity }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No purchase return available.</td>
                                        </tr>
                                    @endforelse

                                    <tr>
                                        <th colspan="4" class="text-right">Total </th>
                                        <th class="text-right">{{ $total_purchase_return_quantity_in_unit['display'] ?? '' }}</th>
                                        <th class="text-right">{{ number_format($total_purchase_return_price, 2) }}</th>
                                    </tr>
                                    </tbody>
                                </table>
                                <!-- paginate -->
                                <div class="float-right">{{ $purchases->appends(Request::except('page'))->links() }}</div>
                            </div>
                        </div>
                        <hr>

                        {{-- sale report header --}}
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="m-0">{{ date('j F, Y', strtotime(request()->from_date)) }} To {{ date('j F, Y', strtotime(request()->to_date)) }} Dates Sale Reports</h5>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <table class="table table-hover table-sm">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th>Customer</th>
                                        <th>Invoice Number</th>
                                        <th>Product</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Discount Per Unit</th>
                                        <th class="text-right">Grand Total</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @forelse($sales as $sale)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}.</td>
                                            <td>{{ $sale->customer->name ?? '' }}</td>
                                            <td><a href="{{ route('invoice.generate', $sale->invoice_no) }}">{{ $sale->invoice_no }}</a></td>
                                            @foreach($sale->saleDetails as $details)
                                                <td>{{ $details->product->name ?? '' }}</td>
                                                <td class="text-right">{{ $details->total_quantity_in_format['display'] }}</td>
                                                <td class="text-right">{{ $details->discount }} {{ ($details->discount_type == 'percentage') ? '%' : 'Tk' }}</td>
                                                <td class="text-right">{{ $details->line_total }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No sales available.</td>
                                        </tr>
                                    @endforelse

                                    <tr>
                                        <th colspan="4" class="text-right">Total </th>
                                        <th class="text-right">{{ $total_sale_quantity_in_unit['display'] ?? '' }}</th>
                                        <th></th>
                                        <th class="text-right">{{ number_format($total_sale_price, 2) }}</th>
                                    </tr>
                                    </tbody>
                                </table>

                                <!-- paginate -->
                                <div class="float-right">{{ $sales->appends(Request::except('page'))->links() }}</div>
                            </div>
                        </div>

                        {{-- sale return report header --}}
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="m-0">{{ date('j F, Y', strtotime(request()->from_date)) }} To {{ date('j F, Y', strtotime(request()->to_date)) }} Dates Sale Return Reports</h5>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <table class="table table-hover table-sm">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th>Customer</th>
                                        <th>Invoice Number</th>
                                        <th>Product</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Grand Total</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @forelse($sale_returns as $returns)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}.</td>
                                            <td>{{ $returns->sale->customer->name ?? '' }}</td>
                                            <td><a href="{{ route('invoice.generate', $returns->sale->invoice_no) }}">{{ $returns->sale->invoice_no }}</a></td>
                                            @foreach($returns->returnProducts as $details)
                                                <td>{{ $details->product->name ?? '' }}</td>
                                                <td class="text-right">{{ $details->total_return_quantity_in_unit['display'] }}</td>
                                                {{--                                            <td class="text-right">{{ $details->discount }} {{ ($details->discount_type == 'percentage') ? '%' : 'Tk' }}</td>--}}
                                                <td class="text-right">{{ $details->return_product_price_total }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No sales returns available.</td>
                                        </tr>
                                    @endforelse

                                    <tr>
                                        <th colspan="4" class="text-right">Total </th>
                                        <th class="text-right">{{ $total_sale_return_quantity_in_unit['display'] ?? '' }}</th>
                                        <th class="text-right">{{ number_format($total_sale_return_price, 2) }}</th>
                                    </tr>
                                    </tbody>
                                </table>

                                <!-- paginate -->
                                <div class="float-right">{{ $sale_returns->appends(Request::except('page'))->links() }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
