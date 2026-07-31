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
                <div class="account-form__content text-center mb-4">
                    <h2 class="account-form__title">@lang('Verify Code')</h2>
                    <p class="account-form__desc">
                        @lang('Please check your mobile number for the verification code and enter it below')
                    </p>
                </div>
                <form action="{{ route('agent.password.verify.code') }}" method="POST" class="submit-form">
                    @csrf
                    <input type="hidden" name="mobile_number" value="{{ $mobile }}">
                    <div class="otp-inner mb-4">
                        <input type="text" maxlength="1" class="otp-input" autofocus />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                    </div>
                    <div class="mb-4">
                        <button type="submit" class="btn btn--grbtn w-100">
                            @lang('Verify') <i class="las la-check-circle"></i>
                        </button>
                    </div>
                    <div class="text--dark  fw-medium">
                        @lang('Please check including your Junk/Spam Folder. if not found, you can ')
                        <a href="{{ route('agent.password.request') }}"
                            class="text--base fw-bold ms-1 cursor-select-none resend-link">
                            @lang('Try to send again')
                        </a>
                    </div>
                    <input type="hidden" name="code">
                </form>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        //manage otp input
        $(".otp-input").on("input paste keydown keyup", function() {
            let otpCode = "";
            let allFilled = true;

            $(".otp-input").each(function() {
                if ($(this).val().length !== 1) {
                    allFilled = false;
                }
                otpCode += $(this).val();
            });

            $("input[name=code]").val(otpCode);

            if (allFilled) {
                $(".submit-form").submit();
            }
        });
    </script>
@endpush
