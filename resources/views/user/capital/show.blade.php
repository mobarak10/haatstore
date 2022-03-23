@extends('layouts.user')

@section('title', 'Capital')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-3">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="m-0">Capital amount Details</h5>
                            <small></small>
                        </div>

                        <div class="btn-group" role="group" aria-label="Action area">
                            <a href="{{ route('capital.index') }}" class="btn btn-primary" title="All cash">
                                <i class="fa fa-list" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <!-- cash ledger -->
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>@lang('contents.date')</th>
                                <th>Name</th>
                                <th>@lang('contents.description')</th>
                                <th class="text-right">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $capital->date }}</td>
                                    <td class="w-25">{{ $capital->capital_name }}</td>
                                    <td class="text-wrap w-50">{{ $capital->description }}</td>
                                    <td class="text-right">{{ number_format($capital->amount, 2) }}</td>
                                </tr>

                            </tbody>
                        </table>

                        <!-- paginate -->
                        {{-- <div class="float-right mx-2">
                            {{ $cash_details->links() }}
                        </div> --}}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
