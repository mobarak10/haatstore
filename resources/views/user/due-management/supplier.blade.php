@extends('layouts.user')
@section('title', $title)
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">
                            Supplier Due Management
                        </h5>

                        <div class="btn-group" role="group" aria-label="Action area">
                            <a href="{{ route('dueManagement.create', 'supplier') }}" class="btn btn-primary" title="">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Supplier Name</th>
                                <th>Amount</th>
                                <th>Payment Type</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="text-right">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($manage_dues as $due)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $due->party->name }}.</td>
                                    <td>{{ $due->amount }}</td>
                                    <td>{{ ucfirst(trans($due->payment_type)) }}</td>
                                    <td>{{ $due->date }}</td>
                                    <td>
                                        {{ $due->description }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('dueManagement.show', $due->id) }}" title="show details" class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No due manage available</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <!-- paginate -->
                        <div class="float-right mx-2">
                            {{ $manage_dues->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
