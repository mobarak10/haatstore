@extends('layouts.user')

@section('title', $title)

@push('style')
    <link href="{{ asset('public/css/invoice.css') }}" rel="stylesheet">
    <link href="{{ asset('public/fonts/font.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container">
        <!-- Print btn -->
        <div class="print pb-3">
            <div class="btn-group">
                <button class="btn" onclick="window.print()">
                    <i class="fa fa-print"></i>
                </button>

                {{-- <a class="btn btn-success" href="{{ route('pos.create') }}" title="Back to POS.">
                    <i class="fa fa-chevron-left" aria-hidden="true"></i>
                    &nbsp; Back
                </a> --}}
            </div>
        </div>
        <!-- End of the Print btn -->

        <div class="row">
            <!-- Invoice -->
            <div class="invoice print-none">
                <!-- Invoice header -->
                <div class="invoice-header">
                    <div class="row align-items-center justify-content-between print-none">
                        <div class="col-4">
                            <div class="logo">
                                <img src="{{ asset($business->media->real_path ?? '') }}" class="img-fluid">
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="text">
                                <strong class="text-white">{{ $business->name ?? '' }}</strong>
                                <span>Phone: {{ $business->phone ?? ''}} </span>
                                <span>Address: {{ $business->address ?? '' }}</span>
                                <span>Email: {{ $business->email ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-none d-print-block text-center w-100 text-dark">
                        <h5 class="mb-0" style="font-size: 16px"> Haat Store </h5>
                        <p class="mb-0" style="font-size: 10px">Phone: 01320370001</p>
                        <p class="mb-0" style="font-size: 12px">Address: 81 Power House Road, Kewatkhali, Mymensingh
                            Email: info@haatstore.com</p>
                    </div>
                </div>
                <!-- End of the invoice header -->

                <!-- Client details -->
                <div class="client-details">
                    <div class="row">
                        <div class="col-3">
                            <div class="single">
                                <div class="title">Billed to</div>
                                <span>{{ $due_manage->party->name }}</span>
                                <span>{{ $due_manage->party->address }}</span>
                            </div>
                        </div>

                        <div class="col-4 pl-4">

                        </div>

                        <div class="col-5">
                            <div class="single text-right">
                                <div class="single">
                                    <div class="title">Date of issue</div>
                                    <span>{{ $due_manage->date->format('F d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of the client details -->

                <!-- Description -->
                <div class="description">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Description</th>
                            <th>Transaction By</th>
                            <th>Transaction Type</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Balance</th>
                        </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td class="text-wrap">
                                    {{ $due_manage->description }}
                                </td>
                                <td>{{ ($due_manage->cash_id) ? 'Cash' : 'Bank' }}</td>
                                <td>{{ ucfirst($due_manage->payment_type) }}</td>
                                <td class="text-right">{{ number_format($due_manage->amount, 2) }}</td>
                                <td class="text-right">{{ number_format($due_manage->current_balance, 2) }} {{ $due_manage->current_balance >=0 ? 'Rec' : 'Pay' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- End of the description -->

            <!-- Footer -->
                <div class="footer">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <p>Thank you for your business</p>
                        </div>
                        <div class="col-6">
                            <div class="signature">
                                Authorized sign
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of the footer -->

            </div>
            <!-- End of the invoice -->

        </div>

        <!-- Bill paper -->
        <div class="bill-paper">
            <div class="brand mb-1">
                Haat Store
            </div>
            <div class="details">
                <ul>
                    <li>Phone: 01320370001</li>
                    <li>Address: 81 Power House Road, Kewatkhali, Mymensingh
                        Email: info@haatstore.com</li>
                </ul>
            </div>
            <div class="d-flex justify-content-between py-1" style="border-bottom:1px dashed #000;">
                <div>DATE: {{ $due_manage->created_at->format('d/m/y') }}</div>
            </div>
            <div class="details text-center">
                <ul>
                    <li>Name: {{ $due_manage->party->name }}</li>
                    @if($due_manage->party->phone)
                        <li>Mobile: {{ $due_manage->party->phone }}</li>
                    @endif
                </ul>
            </div>
            <div class="memo mb-1">
                <table class="table table-borderless mb-1">
                    <thead>
                    <tr style="border-bottom:1px dashed #000;">
                        <th>Description</th>
                        <th>Transaction By</th>
                        <th class="text-right">	Transaction Type</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Balance</th>
                    </tr>
                    </thead>
                    <tbody>

                    <div>
                        <tr>
                            <td colspan="4">{{ $due_manage->description }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"
                                class="text-right">{{ ($due_manage->cash_id) ? 'Cash' : 'Bank' }}
                            </td>
                            <td class="text-right">{{ ucfirst($due_manage->payment_type) }}</td>
                            <td class="text-right">{{ number_format($due_manage->amount, 2) }}</td>
                            <td class="text-right">{{ number_format($due_manage->current_balance, 2) }} {{ $due_manage->current_balance >=0 ? 'Rece' : 'Pay' }}</td>
                        </tr>
                    </div>

                    </tbody>
                </table>
            </div>

            <strong class="text-center d-block pt-1">Thank You for Your Business</strong>
        </div>
        <!-- End of the bill paper -->

    </div>
@endsection
