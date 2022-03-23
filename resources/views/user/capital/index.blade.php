@extends('layouts.user')

@section('title', 'Capital')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-3">

                <h1 class="text-center pt-5 pb-4 d-none d-print-block">Haat Store</h1>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">@lang('contents.capital_records')</h5>
                        <span class="d-none d-print-block">{{ date('Y-m-d') }}</span>
                        <div class="action-area print-none">
                            <a href="#" onclick="window.print();" title="Print" class="btn btn-warning"><i aria-hidden="true" class="fa fa-print"></i>
                            </a>

                            <a href="{{ route('capital.index') }}" class="btn btn-success" title="Refresh.">
                                <i class="fa fa-refresh"></i>
                            </a>

                            <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#capital-search">
                                <i class="fa fa-search"></i>
                            </button>

                            <a href="{{ route('capital.create') }}" class="btn btn-primary" title="Create new capital">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="collapse col-md-12" id="capital-search">
                            <form action="{{ route('capital.index') }}" method="GET">
                                <input type="hidden" name="search" value="1">

                                <div class="row">

                                    <div class="col-md-3">
                                        <label for="from-date" class="form-label">From date</label>
                                        <input type="date" name="from_date" value="{{ request()->from_date ?? '' }}" class="form-control" id="from-date">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="to-date" class="form-label">To date</label>
                                        <input type="date" name="to_date" value="{{ request()->to_date ?? '' }}" class="form-control" id="to-date">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="capital_name">Name</label>
                                        <input type="text" class="form-control" name="condition[capital_name]" placeholder="enter name" id="capital-name">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> &nbsp;
                                            Search
                                        </button>
                                     </div>
                                </div>
                            </form>
                        </div>

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('contents.date')</th>
                                <th>@lang('contents.capital_name')</th>
                                <th>@lang('contents.added_to')</th>
                                <th class="text-right">@lang('contents.amount') (@lang('contents.bdt'))</th>
                                <th class="text-right print-none">@lang('contents.action')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($capitals as $capital)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $capital->date }}</td>
                                    <td>{{ $capital->capital_name }}</td>
                                    <td>{{ $capital->cash->title }}</td>
                                    <td class="text-right">{{ number_format($capital->amount, 2) }}</td>
                                    <td class="text-right print-none">
                                        <a href="{{ route('capital.show', $capital->id) }}" class="btn btn-primary" title="Capital details.">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>

                                        <a href="{{ route('capital.edit', $capital->id) }}" class="btn btn-success" title="Change Capital information.">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>

                                        <a href="{{ route('capital.index') }}" class="btn btn-danger" title="Trash" onClick="if(confirm('Are you sure, You want to delete this record?')){event.preventDefault();document.getElementById('delete-form-{{ $capital->id }}').submit();} else {event.preventDefault();}">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </a>

                                        <form action="{{ route('capital.destroy', $capital->id) }}" method="post" id="delete-form-{{ $capital->id }}" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No capitals available</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="4" class="text-right">@lang('contents.total')</td>
                                <td class="text-right">{{ $capitals->sum('amount') }}</td>
                                <td class="print-none"></td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- paginate -->
                        <div class="float-right mx-2">
                            {{ $capitals->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
