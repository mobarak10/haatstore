<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th class="font-bold" scope="col" width="5%">SL</th>
        <th class="font-bold" scope="col">Name</th>
        <th class="font-bold" scope="col">Barcode</th>
        <th class="font-bold" scope="col">Category</th>
        <th class="font-bold" scope="col">Brand</th>
        <th scope="col">Quantity (Unit)</th>
        <th scope="col" class="text-right">Purchase Price(Unit)</th>
        <th scope="col" class="text-right">Total Purchase Price</th>
        <th scope="col" class="text-right">Sale Price(Unit)</th>
        <th scope="col" class="text-right">Total Sale Price</th>
    </tr>
    </thead>
    <tbody>

    @forelse($products as $product)
    <tr>
        <td scope="row">{{ $loop->iteration }}.</td>
        <td>{{ $product->name }}</td>
        <td>{{ $product->barcode }}</td>
        <td>{{ $product->category->name }}</td>
        <td>{{ $product->brand->name }}</td>
        <td>
            {{ (count($product->stock) > 0) ? \App\Helpers\Converter::convert($product->stock->sum('quantity'), $product->unit->code, 'd')['display'] : '-' }}
        </td>
        <td class="text-right">
            {{ (count($product->stock) > 0) ? $product->stock[0]['avarage_purchase_price'] : $product->purchase_price_with_cost }}
        </td>

        <td class="text-right">
            {{ $product->stock->sum('quantity') * ((count($product->stock) > 0) ? $product->stock[0]['avarage_purchase_price'] : $product->purchase_price_with_cost) }}
        </td>

        <td class="text-right">
            {{ $product->retail_price }}
        </td>

        <td class="text-right">
            {{ $product->stock->sum('quantity') * $product->retail_price }}
        </td>

    </tr>
    @empty
    <tr>
        <td class="text-center" colspan="10">No Stock Avaiable</td>
    </tr>
    @endforelse

    </tbody>
</table>
