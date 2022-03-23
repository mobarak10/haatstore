@extends('layouts.user')

@section('title', __('contents.purchase'))

@section('content')
    <div class="container">
        <form action="{{ route('purchase.store') }}" method="POST">
            @csrf
            <product-purchase-component></product-purchase-component>
        </form>
    </div>
@endsection
