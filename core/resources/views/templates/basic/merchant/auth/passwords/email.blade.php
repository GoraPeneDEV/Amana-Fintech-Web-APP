@extends($activeTemplate . 'layouts.app')
@section('app-content')
    @php
        $authContent = @getContent('auth_section.content', true)->data_values;
    @endphp
    <section class="account bg-img"
        data-background-image="{{ frontendImage('auth_section', @$authContent->background_image) }}">
        <div class="container">
            <div class="account-form">
                <a href="{{ route('home') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="account-form__content">
                    <h2 class="account-form__title">{{ __($pageTitle) }}</h2>
                    <p class="account-form__desc">
                        @lang('To recover your account please provide your mobile number to find your account.')
                    </p>
                </div>
                <form method="POST" action="{{ route('merchant.password.email') }}" class="verify-gcaptcha">
                    @csrf
                    <div class="form-group">
                        <label class="form--label">@lang('Mobile Number')</label>
                        <div class="input-group">
                            <span class="input-group-text country-code">
                                <x-country />
                            </span>
                            <input type="number" required class="form--control form-control"
                                placeholder="@lang('Enter your mobile number')" name="mobile_number" value="{{ old('mobile_number') }}"
                                autofocus="off">
                        </div>
                    </div>
                    <x-captcha />
                    <div class="form-group">
                        <button type="submit" class="btn btn--grbtn w-100">
                            @lang('Submit') <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
