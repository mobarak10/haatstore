@extends('layouts.user')

@section('title', __('contents.product'))

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-3">
                <h1 class="pt-0 pb-2 d-none d-print-block">@lang('contents.company_name') </h1>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">@lang('contents.product_records')</h5>
                        @if(Route::currentRouteName() == 'product.search')
                            <small>Found {{ $products->total() }} products according to your search</small>
                        @endif

                        <span class="d-none d-print-block">{{ date('F j, Y') }}</span>

                        <div class="action-area print-none" aria-label="Action area">
                            <a href="#" onclick="window.print();" title="Print" class="btn btn-warning"><i aria-hidden="true" class="fa fa-print"></i></a>
                            <a href="{{ route('product.index') }}" class="btn btn-info" title="Refresh">
                                <i class="fa fa-refresh"></i>
                            </a>
                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#product-search-form">
                                <i class="fa fa-search"></i>
                            </button>
                            <a href="{{ route('product.create') }}" class="btn btn-primary" title="Create new product">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="collapse {{ (Route::currentRouteName() == 'product.search') ? 'show' : '' }}" id="product-search-form">
                            <div class="card-body">
                                <form action="{{ route('product.search') }}" method="GET">
                                    <product-search-company-brand-category
                                        :suppliers="{{ $suppliers }}"
                                        :categories="{{ $categories }}"
                                        @isset($searched_query)
                                        :searched-query="{{ $searched_query }}"
                                        @endisset></product-search-company-brand-category>

                                    <div class="form-row">
                                        <div class="col-md-4 mb-3">
                                            <label for="warehouse">Warehouse</label>
                                            <select name="warehouse_id" id="warehouse" class="form-control">
                                                <option value="">All Warehouses</option>
                                                @foreach($warehouses as $warehouse)
                                                    <option {{ (isset($searched_query['warehouse_id']) AND $searched_query['warehouse_id'] == $warehouse->id) ? 'selected' : '' }} value="{{ $warehouse->id }}">{{ $warehouse->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="name">Product name</label>
                                            <input type="text" name="name" class="form-control" id="name" value="{{ $searched_query['name'] ?? '' }}" placeholder="Enter Product Name">
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="barcode">Barcode</label>
                                            <input type="text" name="barcode" class="form-control" id="barcode" value="{{ $searched_query['barcode'] ?? '' }}" placeholder="PRDXXXX">
                                        </div>
{{--                                        <div>--}}
{{--                                            <button class="btn btn-primary btn-block" type="submit">Search</button>--}}
{{--                                        </div>--}}
                                    </div>
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> &nbsp;
                                            Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th class="print-none">@lang('contents.code')</th>
                                    <th class="print-none">@lang('contents.barcode')</th>
                                    <th>@lang('contents.name')</th>
                                    <th class="text-right">@lang('contents.purchase_price')</th>
                                    <th class="text-right">@lang('contents.wholesale_price')</th>
                                    <th class="text-right">@lang('contents.retail_price')</th>
                                    <th class="text-right print-none">@lang('contents.action')</th>
                                </tr>
                                </thead>

                                <tbody>
                                @php
                                    $serial = \App\Helpers\PaginationSerial::serial($products); //get the start integer

                                @endphp
                                @forelse($products as $product)
                                    <tr>
                                        <td title="{{ ++$serial }}">{{ $serial }}</td>
                                        <td class="print-none">{{ $product->code }}</td>
                                        <td class="print-none">{{ $product->barcode }}</td>
                                        <td><a href="{{ route('product.show', $product->id) }}" title="{{ $product->name }}" target="_blank">{{ $product->name }}</a></td>
                                        <td class="text-right">{{ number_format($product->purchase_price, 3) }}</td>
                                        <td class="text-right">{{ number_format($product->wholesale_price, 3) }}</td>
                                        <td class="text-right">{{ number_format($product->retail_price, 3) }}</td>
                                        <td class="text-right print-none">
                                            <a href="{{ route('product.show', $product->id) }}" class="btn btn-sm btn-primary" title="Show product information.">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-sm btn-primary" title="Change cash information.">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </a>

                                            <a href="{{ route('product.index') }}" class="btn btn-sm btn-danger" title="Trash" onClick="if(confirm('Are you sure, You want to delete this record?')){event.preventDefault();document.getElementById('delete-form-{{ $product->id }}').submit();} else {event.preventDefault();}">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </a>

                                            <form action="{{ route('product.destroy', $product->id) }}" method="post" id="delete-form-{{ $product->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No product available</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- paginate -->
                        <div class="float-right mx-2">
                            {{ $products->appends($_GET)->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
