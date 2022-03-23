@extends('layouts.user')

@section('title', 'Customer')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-3">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">@lang('contents.update_customer')</h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="col-12 py-2">
                            <form action="{{ route('customer.update', $customer->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="form-row">
                                    <div class="form-group col-md-6 required">
                                        <label for="name">@lang('contents.customer_name')</label>
                                        <input type="text" name="name" value="{{ $customer->name }}" class="form-control" id="name" placeholder="Name">
                                    </div>
                                    <div class="form-group col-md-6 required">
                                        <label for="phone">@lang('contents.phone')</label>
                                        <input type="text" name="phone" value="{{ $customer->phone }}" class="form-control" id="phone" placeholder="Phone">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="email">@lang('contents.email')</label>
                                        <input type="email" name="email" value="{{ $customer->email }}" class="form-control" id="email" placeholder="Email">
                                    </div>
                                    <div class="form-group col-md-6 required">
                                        <label for="balance">@lang('contents.balance')</label>
                                        <input type="number" name="balance" step="any" class="form-control" id="balance" value="{{ $customer->balance }}" placeholder="Balance">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="category">Category</label>
                                        <select name="category" id="category" class="form-control">
                                            <option selected disabled>Choose One</option>
                                            <option {{ ($customer->category == 'platinum' ? 'selected' : '') }} value="platinum">Platinum</option>
                                            <option {{ ($customer->category == 'gold' ? 'selected' : '') }} value="gold">Gold</option>
                                            <option {{ ($customer->category == 'silver' ? 'selected' : '') }} value="silver">Silver</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="card_number">Card Number</label>
                                        <input type="text" value="{{ $customer->card_number }}" readonly name="card_number" class="form-control" id="card_number" placeholder="Card number">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="contact-person">@lang('contents.contact_person')</label>
                                        <input type="text" name="contact_person" value="{{ $customer->getMetaValue('contact_person') }}" class="form-control" id="contact-person" placeholder="Contact Person">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="contact-person-number">@lang('contents.contact_person_phone')</label>
                                        <input type="text" name="contact_person_phone" value="{{ $customer->getMetaValue('contact_person_phone') }}" class="form-control" id="contact-person-number" placeholder="Enter Phone Number">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="address">@lang('contents.address')</label>
                                        <textarea class="form-control" name="address" id="address" placeholder="Address">{{ $customer->address }}</textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="description">@lang('contents.description')</label>
                                        <textarea class="form-control" name="description" id="description" placeholder="Description">{{ $customer->description }}</textarea>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label for="avatar">@lang('contents.photo_media_id')</label>
                                        <input type="number" name="thumbnail" class="form-control" value="{{ $customer->thumbnail }}" id="avatar" placeholder="Enter Media Code">
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
    </div>
@endsection
