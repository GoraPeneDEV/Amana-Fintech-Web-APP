@extends($activeTemplate . 'layouts.app')
@section('app-content')
    @if (gs('registration'))
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
                        <h2 class="account-form__title">{{ __(@$authContent->register_heading) }}</h2>
                        <p class="account-form__desc">{{ __(@$authContent->register_subheading) }}</p>
                    </div>
                    <form action="{{ route('agent.register') }}" method="POST" class="verify-gcaptcha">
                        @csrf
                        <div class="form-group">
                            <label class="form--label">@lang('Mobile Number')</label>
                            <div class="input-group">
                                <span class="input-group-text country-code">
                                    <x-country />
                                </span>
                                <input type="number" required class="form--control form-control"
                                    placeholder="@lang('Enter Mobile Number')" name="mobile_number" value="{{ old('mobile_number') }}">
                            </div>
                        </div>
                        <x-captcha />
                        @if (gs('agree'))
                            @php
                                $policyPages = getContent('policy_pages.element', false, orderById: true);
                            @endphp
                            <div class="form-group">
                                <div class="form--check">
                                    <input type="checkbox" class="form-check-input" id="agree"
                                        @checked(old('agree')) name="agree" required>

                                    <label for="agree" class="form-check-label user-select-none">
                                        @lang('I agree with')
                                        @foreach ($policyPages as $policy)
                                            <a class="text--base" href="{{ route('policy.pages', $policy->slug) }}"
                                                target="_blank">{{ __($policy->data_values->title) }}</a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="mb-3">
                            <button type="submit" class="btn btn--grbtn w-100">
                                @lang('Continue') <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                        <div class="mb-3">
                            @lang('Already have an account?') <a href="{{ route('agent.login') }}"
                                class="text--base fw-bold">@lang('Login Now')
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    @else
        @include($activeTemplate . 'partials.registration_disabled')
    @endif
@endsection
