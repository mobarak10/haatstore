@extends('layouts.user')

@section('title', $title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center pt-5 pb-4 d-none d-print-block">Haat Store </h1>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@lang('contents.all_sales')</h5>
                    <span class="d-none d-print-block">{{ date('d-m-Y') }}</span>
                    <span class="d-none d-print-block">{{ date('h:i:s A') }}</span>

                    <div>
                        <!-- for refresh -->
                        <a href="{{ route('pos.index') }}" class="btn btn-primary print-none" title="Refresh">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </a>

                        <!-- for collaps search -->
                        <button class="btn btn-info print-none" type="button" title="Search product" data-toggle="collapse" data-target="#searchCollapse" aria-expanded="false" aria-controls="collapseSearch">
                            <i class="fa fa-search"></i>
                        </button>

                        <!-- for print -->
                        <a href="#" onclick="window.print();" title="Print" class="btn btn-warning print-none">
                            <i aria-hidden="true" class="fa fa-print"></i>
                        </a>
                    </div>
                </div>

                <div class="card card-body">
                    <div class="collapse align-items-center" id="searchCollapse">
                        <form action="{{ route('pos.index') }}" method="GET">
                            <input type="hidden" name="search" value="1">

                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label for="date-from">@lang('contents.date') (@lang('contents.from'))</label>
                                    <input type="date" class="form-control" name="date[from]" value="{{ request()->date['from'] ?? '' }}" id="date-form">
                                </div>

                                <div class="form-group col-md-2">
                                    <label for="date-to">@lang('contents.date') (@lang('contents.to'))</label>
                                    <input type="date" class="form-control" name="date[to]" value="{{ request()->date['to'] ?? ''}}" id="date-to">
                                </div>

                                <div class="form-group col-md-2">
                                    <label for="phone">@lang('contents.customer_phone')</label>
                                    <input type="text" name="condition[phone]" value="{{ request()->condition['phone'] ?? '' }}" placeholder="enter number" class="form-control" id="phone">
                                </div>

                                <div class="form-group col-md-2">
                                    <label for="invoice_no">@lang('contents.invoice_no')</label>
                                    <input type="text" class="form-control" name="condition[invoice_no]" value="{{ request()->condition['invoice_no'] ?? '' }}" placeholder="xxxxxxxx" id="invoice_no">
                                </div>

                                <div class="form-group col-md-2">
                                    <label for="user">@lang('contents.sales_man')</label>
                                    <select name="condition[salesman_id]" class="form-control">
                                        <option value="" selected disabled>Choose one</option>
                                        @foreach($employees as $employee)
                                            <option {{ ((request()->condition['salesman_id'] ?? '') == $employee->id) ? 'selected' : '' }} value="{{ $employee->id }}">
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-2 text-right">
                                    <label>&nbsp;</label>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i> &nbsp;
                                        @lang('contents.search')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>@lang('contents.date')</th>
                                    <th>@lang('contents.invoice_no')</th>
{{--                                    <th>@lang('contents.customer')</th>--}}
                                    <th class="text-right">@lang('contents.total')</th>
                                    <th class="text-right">Discount</th>
                                    <th class="text-right">Grand Total</th>
                                    <th class="text-right">Purchase Total</th>
                                    <th class="text-right">Paid</th>
                                    <th class="text-right">Due</th>
                                    <th class="text-right">Return</th>
{{--                                    <th class="text-center print-none">@lang('contents.sales_man')</th>--}}
{{--                                    <th class="text-center print-none">@lang('contents.status')</th>--}}
                                    <th class="text-right print-none">@lang('contents.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $sub_total = 0.00;
                                $grand_total = 0.00;
                                $grand_total_purchase = 0.00;
                                $total_paid = 0.00;
                                $total_due = 0.00;
                                $total_discount = 0.00;
                                $total_return = 0.00;
                                @endphp

                                @forelse($sales as $sale)
                                <tr class="{{ ($sale->return_product_price_total != 0) ? 'table-danger' : '' }}">
                                    <td class="text-center">{{ $loop->iteration }}.</td>
                                    <td>{{ $sale->created_at->format('d F, Y') }}</td>
                                    <td>
                                        <a href="{{ route('invoice.generate', $sale->invoice_no) }}" title="View Invoice"
                                            target="_blank">
                                            {{ $sale->invoice_no }}
                                        </a>
                                    </td>
{{--                                    <td>{{ $sale->customer->name }}</td>--}}
                                    <td class="text-right">
                                        @php
                                        $sub_total += $sale->subtotal;
                                        @endphp

                                        {{ number_format($sale->subtotal, 2) }}
                                    </td>

                                    <td class="text-right">
{{--                                        @php--}}
{{--                                            $total_discount += $sale->discount;--}}
{{--                                        @endphp--}}
                                        @if($sale->discount_type === 'flat')
                                            @php
                                                $total_discount += $sale->discount;
                                            @endphp
                                            {{ ($sale->discount != 0.00) ? number_format($sale->discount, 2) : '-' }}
                                        @else
                                            @php
                                                $total_discount += (($sale->subtotal * $sale->discount) / 100);
                                            @endphp
                                            {{ ((($sale->subtotal * $sale->discount) / 100) != 0.00) ? number_format(($sale->subtotal * $sale->discount) / 100, 2) : '-' }}
                                        @endif
{{--                                        {{ ($sale->discount != 0.00) ? number_format($sale->discount, 2) : '-' }}--}}
                                    </td>

                                    <td class="text-right">
                                        @php
                                            $grand_total += $sale->grand_total;
                                        @endphp

                                        {{ number_format($sale->grand_total, 2) }}
                                    </td>

                                    <td class="text-right">
                                        @php
                                            $grand_total_purchase += $sale->total_purchase_price;
                                        @endphp

                                        {{ number_format($sale->total_purchase_price, 2) }}
                                    </td>

                                    <td class="text-right">
                                        @php
                                            $total_paid += ($sale->tendered - $sale->change);
                                        @endphp

                                        {{ ($sale->tendered != 0.00) ? number_format(($sale->tendered - $sale->change), 2) : '-' }}
                                    </td>

                                    <td class="text-right">
                                        @php
                                            $total_due += $sale->due
                                        @endphp

                                        {{ ($sale->due != 0.00) ? number_format($sale->due, 2) : '-' }}
                                    </td>

                                    <td class="text-right">
                                        @php
                                            $total_return += $sale->return_product_price_total
                                        @endphp

                                        {{ ($sale->return_product_price_total > 0) ? number_format($sale->return_product_price_total, 2) : '-' }}
                                    </td>
{{--                                    <td class="text-center">{{ $sale->salesman->name }}</td>--}}
{{--                                    <td class="text-center">--}}
{{--                                        <button onclick="if(confirm('Are you sure to change status to {{ $sale->delivered ? 'pending' : 'delivered' }}')){ document.getElementById('deliver-{{ $loop->iteration }}').submit() }" type="button" class="btn btn-{{ $sale->delivered ? 'success' : 'danger' }}">--}}
{{--                                            {{ $sale->delivered ? 'Delivered' : 'Pending' }}--}}
{{--                                        </button>--}}

{{--                                        <form method="POST" id="deliver-{{ $loop->iteration }}" action="{{ route('pos.deliver', $sale->id) }}" style="display: none;">--}}
{{--                                            @csrf--}}
{{--                                        </form>--}}
{{--                                    </td>--}}
                                    <td class="text-right print-none">
                                        <button data-toggle="modal" data-target="#addDiscountModal-{{ $loop->index }}" class="btn btn-sm btn-primary" title="add discount">
                                            <i class="fa fa-plus"></i>
                                        </button>

                                        {{--Modal--}}
                                        <div class="modal fade" id="addDiscountModal-{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('pos.discount', $sale->id) }}" method="post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="insertModalLabel">Add Discount</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <div class="form-group text-left required">
                                                                <label for="discount">Discount</label>
                                                                <input type="text" name="discount" class="form-control" value="{{ old('discount') }}" id="discount" placeholder="Enter discount">
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

                                        <button onclick="if(confirm('Are you sure to change status to {{ $sale->delivered ? 'pending' : 'delivered' }}')){ document.getElementById('deliver-{{ $loop->iteration }}').submit() }" type="button" class="btn btn-sm btn-{{ $sale->delivered ? 'success' : 'warning' }}">
                                            <i class="fa fa-{{ ($sale->delivered) ? 'check-circle-o' : 'ban' }}"></i>
                                        </button>

                                        <form method="POST" id="deliver-{{ $loop->iteration }}" action="{{ route('pos.deliver', $sale->id) }}" style="display: none;">
                                            @csrf
                                        </form>
                                        <a href="{{ route('pos.return', $sale->invoice_no) }}" class="btn btn-sm btn-danger" title="Return">
                                            <i class="fa fa-undo"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No Sale available.</td>
                                </tr>
                                @endforelse
                                <tr>
                                    <th colspan="3" class="text-right">Total</th>
                                    <th class="text-right">{{ number_format($sub_total, 2) }}</th>
                                    <th class="text-right">{{ number_format($total_discount, 2) }}</th>
                                    <th class="text-right">{{ number_format($sales->sum('grand_total'), 2) }}</th>
                                    <th class="text-right">{{ number_format($sales->sum('total_purchase_price'), 2) }}</th>
                                    <th class="text-right">{{ number_format($total_paid, 2) }}</th>
                                    <th class="text-right">{{ number_format($total_due, 2) }}</th>
                                    <th class="text-right">{{ number_format($total_return, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-right">
                {{ $sales->appends($_GET)->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
