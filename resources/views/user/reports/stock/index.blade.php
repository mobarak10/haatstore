@extends('layouts.user')

@section('title', 'Stock Report')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Stock Report</h5>

                        <div class="btn-group print-none" role="group" aria-label="Action area">
                            <a href="{{ route('report.stockReport') }}" class="btn btn-primary" title="Refresh">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </a>
                            <a href="#" onclick="window.print();" title="Print" class="btn btn-warning"><i aria-hidden="true" class="fa fa-print"></i></a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <form action="{{ route('report.stockReport') }}" method="GET">
                            <input type="hidden" name="search" value="1">

                            <div class="form-row col-md-12 mt-1 print-none">
                                <div class="form-group col-md-5 required">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" name="date" value="{{ (request()->search) ? date(request()->date) : date('Y-m-d') }}" placeholder="Enter date" id="date" required>
                                </div>

                                <div class="form-group col-md-5">
                                    <label for="product">Product</label>
                                    <select name="product_id" id="product" class="form-control">
                                        <option value="" selected>Choose one</option>
                                        @foreach($search_products as $product)
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
                        @if(request()->search)
                            <table class="table table-bordered" style="font-size: 12px">
                                <thead>
                                <tr>
                                    <th scope="col">SI</th>
                                    <th scope="col" style="min-width: 140px">Product Name</th>
                                    <th scope="col">Current Stock</th>
                                    <th scope="col">Damage</th>
                                    <th scope="col">Sale</th>
                                    <th scope="col">Sale Return</th>
                                    <th scope="col">Purchase</th>
                                    <th scope="col">Purchase Return</th>
                                    <th scope="col" style="min-width: 180px">{{ request()->date }} Stock</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total_stock = 0;
                                @endphp
                                @forelse($products as $product)
                                    <tr>
                                        <td scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $product->name ?? '' }}</td>
                                        <td>
                                            @php
                                                $total_current_stock = 0;
                                            @endphp
                                            @forelse($product->stock ?? [] as $stock)
                                                @php
                                                    $total_stock += $stock->quantity;
                                                    $total_current_stock += $stock->quantity;
                                                @endphp
                                            @empty
                                            @endforelse
                                            {{ \App\Helpers\Converter::convert($total_current_stock, $product->unit_code, 'd')['display'] }}
                                        </td>
                                        <td>
                                            @php
                                                $total_damage_stock = 0;
                                            @endphp
                                            @forelse($product->damageStock ?? [] as $stock)
                                                @php
                                                    $total_stock += $stock->quantity;
                                                    $total_damage_stock += $stock->quantity;
                                                @endphp
                                            @empty
                                            @endforelse
                                            {{ \App\Helpers\Converter::convert($total_damage_stock, $product->unit_code, 'd')['display'] }}
                                        </td>

                                        <td>
                                            @php
                                                $total_stock += $product->saleDetailsWarehouse->sum('quantity');
                                            @endphp
                                            {{ \App\Helpers\Converter::convert($product->saleDetailsWarehouse->sum('quantity'), $product->unit_code, 'd')['display'] }}
                                        </td>

                                        <td>
                                            @php
                                                $total_stock -= $product->saleReturnQuantity->sum('quantity');
                                            @endphp
                                            {{ \App\Helpers\Converter::convert($product->saleReturnQuantity->sum('quantity'), $product->unit_code, 'd')['display'] }}
                                        </td>

                                        <td>
                                            @php
                                                $total_stock -= $product->purchaseQuantity->sum('quantity');
                                            @endphp
                                            {{ \App\Helpers\Converter::convert($product->purchaseQuantity->sum('quantity'), $product->unit_code, 'd')['display'] }}
                                        </td>

                                        <td>
                                            @php
                                                $total_stock += $product->purchaseReturnQuantity->sum('quantity');
                                            @endphp
                                            {{ \App\Helpers\Converter::convert($product->purchaseReturnQuantity->sum('quantity'), $product->unit_code, 'd')['display'] }}
                                        </td>

                                        <td>
                                            {{ \App\Helpers\Converter::convert($total_stock, $product->unit_code, 'd')['display'] }}
                                        </td>
                                        @php
                                            $total_stock = 0;
                                        @endphp
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No stock available</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <!-- paginate -->
                            <div class="float-right m-3">{{ $products->appends(request()->query())->links() }}</div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#product').select2({
                width: 600,
                height: 100
            });
        });
    </script>
@endpush
