@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                   <form action=" {{ route('payments.pay') }}" method="POST" id="paymentForm">
                        @csrf

                        <div class="row">
                            <div class="col-auto">
                                    <label for="value">How much you want to pay?</label>
                                    <input type="number" required name="value" id="value" min="5" step="0.01" class="form-control" value="{{ mt_rand(500, 10000) / 100 }}">
                                    <small class="form-text text-muted">
                                        Use values with ip to two decimal positions, using dot "."
                                    </small>
                            </div>
                            <div class="col-auto">
                                <label for="currency">Currency</label>
                                <select name="currency" id="currency" class="custom-select form-control" required>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->iso }}">{{ strtoupper($currency->iso) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <label for="paymentPlatform"> Select the desired payment platform: </label>
                                <div class="form-group " id="toggler" >
                                    <div class="btn-group " data-bs-toggle="buttons">
                                        @foreach( $platforms as $platform )
                                        <label class="btn btn-outline-secondary rounded m-2 p-1" data-bs-toggle="collapse" data-bs-target="#{{ $platform->name }}Collapse"  aria-pressed="false" aria-controls="{{ $platform->name }}Collapse">
                                            <input class="btn-check" type="radio" name="payment_platform" value="{{ $platform->id }}" required>
                                            <img src="{{ asset($platform->image) }}" alt="" class="img-thumbnail">
                                        </label>
                                        @endforeach
                                    </div>
                                    @foreach ($platforms as $platform)
                                        <div
                                            id="{{ $platform->name }}Collapse"
                                            class="collapse"
                                            data-bs-parent="#toggler"
                                        >
                                            @includeIf('components.'.strtolower($platform->name).'-collapse')
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" id="payButton" class="btn btn-primary">Pay</button>
                        </div>
                   </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
