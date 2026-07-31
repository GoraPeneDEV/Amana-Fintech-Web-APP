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
                <div class="account-form__content text-center">
                    <h2 class="account-form__title">@lang('2FA Verification')</h2>
                </div>
                <form action="{{ route('user.2fa.verify') }}" method="POST" class="submit-form verify-form">
                    @csrf
                    <div class="otp-inner">
                        <input type="text" maxlength="1" class="otp-input" autofocus />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                    </div>
                    <div class="form-btn">
                        <button type="submit" class="btn btn--grbtn w-100">@lang('Continue')</button>
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
                $(".verify-form").submit();
            }
        });
    </script>
@endpush
