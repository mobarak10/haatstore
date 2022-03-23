@extends('layouts.pdf')
@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="mt-2 text-center">
                        <h5 class="mb-0 center" style="font-size: 25px"> <strong>Haat Store</strong> </h5>
                        <p class="mb-0" style="font-size: 18px"><strong>Product Stock</strong></p>
                        <p class="mb-0" style="font-size: 15px">{{ Carbon\Carbon::now()->format('j F, Y h:i:s a') }}</p>
                    </div>

                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th scope="col">SI</th>
                                <th scope="col" style="max-width: 200px;">Product Name</th>
                                <th scope="col">Brand</th>
                                <th scope="col">Quantity (Unit)</th>
                                <th scope="col" class="text-right">Price (Purchase)</th>
                                <th scope="col" class="text-right">Price (Sale)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total_purchase_sft = 0.00;
                                $total_sale_sft = 0.00;
                                $total_purchase_price = 0.00;
                                $total_sale_price = 0.00;
                            @endphp
                            @forelse($print_products as $product)
                                <tr style="border-bottom: 2px dashed black">
                                    <td scope="row">{{ $loop->iteration }}</th>
                                    <td style="max-width: 200px;">{{ $product->name }}</td>
                                    <td style="white-space: initial;">
                                        {{ $product->brand->name }}
                                    </td>
                                    <td style="white-space: initial;">
                                        @if($product->stock)
                                            {{ \App\Helpers\Converter::convert($product->stock->sum('quantity'), $product->unit->code, 'd')['display'] }}
                                        @else
                                            {{ '-' }}
                                        @endif
                                    </td>

                                    <td class="text-right" style="white-space: initial;">
                                        {{ $product->purchase_price_with_cost }}
                                    </td>

                                    <td class="text-right" style="white-space: initial;">
                                        {{ $product->retail_price }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No stock available</td>
                                </tr>
                            @endforelse
                            <tr>
                                <th colspan="4" class="text-right">@lang('contents.total') </th>
                                <th class="text-right">{{ number_format($total_purchase_price, 2) }}</th>
                                <th class="text-right">{{ number_format($total_sale_price, 2) }}</th>
                                <th></th>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
