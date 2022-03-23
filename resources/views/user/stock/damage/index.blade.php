@extends('layouts.user')

@section('title', $title)
@section('content')
<div class="container">
    <div class="accordion" id="damages">
        <div class="col-md-12 py-3">
            <h1 class="text-center pt-5 pb-4 d-none d-print-block">Haat Store</h1>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">All Damage Product</h5>
                    <span class="d-none d-print-block">{{ date('d/m/Y') }}</span>
                    <div class="action-area print-none">
                        <a href="#" onclick="window.print();" title="Print" class="btn btn-warning"><i aria-hidden="true" class="fa fa-print"></i></a>
                        <button class="btn btn-info" type="button" title="Search product" data-toggle="collapse" data-target="#searchCollapse" aria-expanded="false" aria-controls="collapseSearch">
                            <i class="fa fa-search"></i>
                        </button>
                        <a href="{{ route('damageStock.index') }}" class="btn btn-primary" title="Refresh">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('stock.index') }}" class="btn btn-primary" title="All Stock">
                            <i class="fa fa-list" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="{{ request()->search ? 'active' : 'collapse' }} p-3" id="searchCollapse">
                    <form action="{{ route('damageStock.index') }}" method="GET">
                        <input type="hidden" name="search" value="1">

                        <div class="form-row">
                            <div class="col-md-3">
                                <label for="from_date">From Date</label>
                                <input type="date" id="from_date" value="{{ (request()->search) ? request()->from_date : '' }}" name="from_date" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label for="to_date">To Date</label>
                                <input type="date" name="to_date" value="{{ date('Y-m-d') }}" id="to_date" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="product_name">Product name</label>
                                <select name="product_id" id="product_id" class="form-control">
                                    <option value="">Choose One</option>
                                    @foreach($products as $product)
                                        <option {{ (request()->search && request()->product_id == $product->id) ? 'selected' : '' }} value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
{{--                                <input type="text" name="product_name" class="form-control" placeholder="Enter product name">--}}
                            </div>

                            <div class="col-md-2 mb-3 text-right">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary" id="button-addon" title="search">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Warehouse Name</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Operator</th>
                            <th>Date</th>
                            <th class="text-right">Price (BDT)</th>
                            <th class="text-right print-none">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($damage_stocks as $damage)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $damage->warehouses->title }}</td>
                                <td>{{ $damage->product->name }}</td>
                                <td>
                                    {{ \App\Helpers\Converter::convert($damage->quantity, $damage->product->unit_code, 'd')['display'] }}
                                </td>

                                <td>{{ $damage->operator->name }}</td>
                                <td>{{ $damage->created_at->format('d/m/Y') }}</td>
                                <td class="text-right">
                                    {{ number_format(($damage->quantity * $damage->product->purchase_price_with_cost), 2) }}
                                </td>

                                <td class="text-right print-none">
                                    <a href="{{ route('damage.edit', $damage->id) }}" class="btn btn-primary" title="add damage product.">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </a>

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No damage available</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            <!-- paginate -->
            <div class="float-right mx-2">
                {{ $damage_stocks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
