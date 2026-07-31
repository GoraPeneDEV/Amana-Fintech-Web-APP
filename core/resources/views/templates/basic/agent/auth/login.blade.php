@extends($activeTemplate . 'layouts.app')
@section('app-content')
    @php
        $authContent = @getContent('auth_section.content', true)->data_values;
    @endphp
    <section class="account bg-img"
        data-background-image="{{ frontendImage('auth_section', @$authContent->background_image) }}">
        <div class="container">
            <div class="account-form">
                <div class="d-flex gap-2 justify-content-between mb-4">
                    <a href="{{ route('home') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    @if (gs('qrcode_login'))
                        <x-qrcodelogin :qrCode="$qrCode" guard="agent" />
                    @endif
                </div>
                <div class="account-form__content">
                    <h2 class="account-form__title">{{ __(@$authContent->login_heading) }}</h2>
                    <p class="account-form__desc">{{ __(@$authContent->login_subheading) }}</p>
                </div>
                <form method="POST" action="{{ route('agent.login') }}" class="verify-gcaptcha">
                    @csrf
                    <div class="form-group">
                        <label class="form--label">@lang('Mobile Number')</label>
                        <div class="input-group">
                            <span class="input-group-text country-code">
                                <x-country />
                            </span>
                            <input type="number" required class="form--control form-control"
                                placeholder="@lang('Enter your mobile number')" name="mobile_number" value="{{ old('mobile_number') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="flex-between mb-1">
                            <label class="form--label">@lang('Enter PIN')</label>
                            <a href="{{ route('agent.password.request') }}"
                                class="fw-medium fs-14 text-danger text-decoration-underline">@lang('Forgot PIN?')</a>
                        </div>
                        <div class="input-inner">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <path d="M12 16.5V14.5" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path
                                        d="M4.26781 18.8447C4.49269 20.515 5.87613 21.8235 7.55966 21.9009C8.97627 21.966 10.4153 22 12 22C13.5847 22 15.0237 21.966 16.4403 21.9009C18.1239 21.8235 19.5073 20.515 19.7322 18.8447C19.8789 17.7547 20 16.6376 20 15.5C20 14.3624 19.8789 13.2453 19.7322 12.1553C19.5073 10.485 18.1239 9.17649 16.4403 9.09909C15.0237 9.03397 13.5847 9 12 9C10.4153 9 8.97627 9.03397 7.55966 9.09909C5.87613 9.17649 4.49269 10.485 4.26781 12.1553C4.12105 13.2453 4 14.3624 4 15.5C4 16.6376 4.12105 17.7547 4.26781 18.8447Z"
                                        stroke="currentColor" stroke-width="1.5" />
                                    <path d="M7.5 9V6.5C7.5 4.01472 9.51472 2 12 2C14.4853 2 16.5 4.01472 16.5 6.5V9"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </span>
                            <input type="password" required class="form--control form-control"
                                placeholder="@lang('Enter your PIN')" name="pin">
                        </div>
                    </div>
                    <x-captcha />
                    <div class="mb-3">
                        <button type="submit" class="btn btn--grbtn w-100">
                            @lang('Login') <i class="fa fa-angle-right"></i>
                        </button>
                    </div>
                    <div class="mb-3">
                        @lang('Don’t have an account?') <a href="{{ route('agent.register') }}"
                            class="text--base fw-bold">@lang('Register Now')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
