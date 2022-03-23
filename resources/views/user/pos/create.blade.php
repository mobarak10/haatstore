@extends('layouts.user')

@section('title', $title)

@push('style')
    <link href="{{ asset('public/css/pos.css') }}" rel="stylesheet">
@endpush
@section('content')
    <!-- retail sale -->
    <retail-sale :warehouses="{{ $warehouses }}" :cashes="{{ $cashes }}" :customers="{{ $customers }}" :bank-accounts="{{ $bank_accounts }}" />
@endsection

