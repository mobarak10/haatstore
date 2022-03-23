@extends('layouts.user')

@section('title', $title)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-3">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">@lang('contents.update_capital_record')</h5>

                        <div class="btn-group" role="group" aria-label="Action area">
                            <a href="{{ route('capital.index') }}" class="btn btn-primary" title="All Cash">
                                <i class="fa fa-list" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{route('capital.update', $capital->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="form-row">
                                <div class="form-group col-md-6 required">
                                    <label for="date">@lang('contents.date') </label>
                                    <input type="date" value="{{ $capital->date }}" class="form-control" id="date" name="date" required>
                                </div>

                                <div class="form-group col-md-6 required">
                                    <label for="capital_name">@lang('contents.capital_name') </label>
                                    <input type="text" value="{{ $capital->capital_name }}" class="form-control" id="capital-name" name="capital_name" placeholder="Character Only" required>
                                </div>
                            </div>

                            <div class='form-row' >
                                <div class="form-group col-md-6 required">
                                    <label for="amount">@lang('contents.capital_amount') </label>
                                    <input type="number" value="{{ $capital->amount }}" name="amount" class="form-control" id="amount" placeholder="0.00" step="any" required>
                                </div>

                                <div class="form-group col-md-6 required">
                                    <label for="cash_id">Select cash</label>
                                    <select name="cash_id" id="cash_id" class="form-control" required>
                                        <option selected disabled>---</option>
                                    @foreach($cashes as $cash)
                                        <option value="{{ $cash->id }}" {{ (old('cash_id',$capital->cash_id) == $cash->id) ? 'selected' : '' }}>{{ $cash->title }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class='form-row' >
                                <div class="form-group col-md-12">
                                    <label for="description">@lang('contents.description')</label>
                                    <textarea class="form-control" value="{{ old('description') }}" name="description" id="description" placeholder="Description">{{ $capital->description }}</textarea>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="reset" class="btn btn-danger">@lang('contents.reset')</button>
                                <button type="submit" class="btn btn-primary">@lang('contents.update')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- main-panel ends -->
@endsection
