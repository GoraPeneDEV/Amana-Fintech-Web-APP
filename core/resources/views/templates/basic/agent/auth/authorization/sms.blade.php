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
                    <h2 class="account-form__title">@lang('Mobile Verification')</h2>
                    <p class="account-form__desc">
                        @lang('We have sent a code to')
                        <span class="number">{{ showMobileNumber(auth('agent')->user()->mobileNumber) }}</span>
                    </p>
                </div>
                <form action="{{ route('agent.verify.mobile') }}" method="POST" class="submit-form verify-form">
                    @csrf
                    <div class="otp-inner">
                        <input type="text" maxlength="1" class="otp-input" autofocus />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                        <input type="text" maxlength="1" class="otp-input" />
                    </div>
                    <div class="text--dark text-center fw-medium mt-4 mb-3">
                        @lang("Didn't receive the code?")
                        <a href="javascript:void(0)" class="text--base fw-bold ms-1 cursor-select-none resend-link">
                            @lang('Resend the Code')
                        </a>
                        <p class="fst-italic resent-countdown">
                            @lang('You can request a new code after') <span id="countdown" class="timer text--base"></span>
                            @lang('seconds').
                        </p>
                    </div>
                    <button type="submit" class="btn btn--grbtn w-100">
                        <i class="fa fa-paper-plane"></i> @lang('Continue')
                    </button>
                    <input type="hidden" name="code">
                </form>
            </div>
        </div>
    </section>

 
@endsection

@push('script')
    <script>
        var distance = Number("{{ @$user->ver_code_send_at->addMinutes(2)->timestamp - time() }}");
        var x = setInterval(function() {
            distance--;
            document.getElementById("countdown").innerHTML = distance;

            if (distance <= 0) {
                clearInterval(x);
                $(".resend-link").removeClass('cursor-select-none').attr('href',
                    "{{ route('agent.send.verify.code', 'sms') }}");
                $(".resent-countdown").addClass('d-none');
            }
        }, 1000);


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
@push('style')
    <style>
        .cursor-select-none {
            cursor: no-drop;
        }
    </style>
@endpush
