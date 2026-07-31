@extends($activeTemplate . 'layouts.app')
@section('app-content')
    @php
        $authContent = @getContent('auth_section.content', true)->data_values;

    @endphp
    <section class="account bg-img"
        data-background-image="{{ frontendImage('auth_section', @$authContent->background_image) }}">
        <div class="container">
            <div class="account-form">
                <div class="account-form__content">
                    <h2 class="account-form__title">{{ __($pageTitle) }}</h2>
                    <p class="account-form__desc">
                        @lang('To recover your account please provide your mobile number to find your account.')
                    </p>
                </div>
                <form method="POST" action="{{ route('user.password.update') }}">
                    @csrf
                    <input type="hidden" name="mobile" value="{{ $mobile }}">
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        <label class="form--label">@lang('PIN')</label>
                        <x-pin name="pin" autoSubmit="false" justifyClass="justify-content-start" />
                    </div>
                    <div class="form-group">
                        <label class="form--label">@lang('Confirm PIN')</label>
                        <x-pin name="pin_confirmation" autoSubmit="false" justifyClass="justify-content-start" />
                    </div>
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
