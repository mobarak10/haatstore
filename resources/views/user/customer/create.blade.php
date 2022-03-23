@extends('layouts.user')

@section('title', 'Customer')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-3">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">@lang('contents.create_new_customer')</h5>

                    <a href="{{ route('customer.index') }}" class="btn btn-primary" title="Show All">
                        <i class="fa fa-list"></i>
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="col-12 py-2">
                        <form action="{{ route('customer.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6 required">
                                    <label for="name">@lang('contents.customer_name')</label>
                                    <input type="text" value="{{ old('name') }}" name="name" required class="form-control" id="name" placeholder="Name">
                                </div>
                                <div class="form-group col-md-6 required">
                                    <label for="phone">@lang('contents.phone')</label>
                                    <input type="text" value="{{ old('phone') }}" name="phone" required class="form-control" id="phone" placeholder="Phone">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email">@lang('contents.email')</label>
                                    <input type="email" value="{{ old('email') }}" name="email" class="form-control" id="email" placeholder="Email">
                                </div>
                                <div class="form-group col-md-6 required">
                                    <label for="balance">@lang('contents.balance')</label>
                                    <div class="input-group mb-3">
                                        <input type="number" value="{{ old('balance') }}" required name="balance" step="any" class="form-control" id="balance" value="0.00" placeholder="Balance">
                                        <div class="input-group-append">
                                            <select name="balance_status" class="form-control">
                                                <option selected value="receivable">Receivable</option>
                                                <option value="payable">Payable</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option selected disabled>Choose One</option>
                                        <option value="platinum">Platinum</option>
                                        <option value="gold">Gold</option>
                                        <option value="silver">Silver</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="card_number">Card Number</label>
                                    <input type="text" value="{{ old('card_number') }}" name="card_number" class="form-control" id="card_number" placeholder="Card number">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="contact-person">@lang('contents.contact_person')</label>
                                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="form-control" id="contact-person" placeholder="Contact Person">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="contact-person-number">@lang('contents.contact_person_phone')</label>
                                    <input type="text" value="{{ old('contact_person_phone') }}" name="contact_person_phone" class="form-control" id="contact-person-number" placeholder="Enter Phone Number">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="division">Division</label>
                                    <input class="form-control" value="{{ old('division') }}" name="division" id="division" placeholder="Enter division" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="district">Zila</label>
                                    <input class="form-control" value="{{ old('district') }}" name="district" id="district" placeholder="Enter district" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="thana">Upozila</label>
                                    <input class="form-control" value="{{ old('thana') }}" name="thana" id="thana" placeholder="Enter thana" />
                                </div>
                                <div class="form-group col-6">
                                    <label for="logo">@lang('contents.logo_media_id')</label>
                                    <input type="number" value="{{ old('thumbnail') }}" name="thumbnail" class="form-control" id="logo" placeholder="Enter Media Code">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="address">@lang('contents.address')</label>
                                    <textarea class="form-control" value="{{ old('address') }}" name="address" id="address" placeholder="Address"></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="description">@lang('contents.description')</label>
                                    <textarea class="form-control" value="{{ old('description') }}" name="description" id="description" placeholder="Description"></textarea>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="reset" class="btn btn-danger">@lang('contents.reset')</button>
                                <button type="submit" class="btn btn-primary">@lang('contents.save')</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
