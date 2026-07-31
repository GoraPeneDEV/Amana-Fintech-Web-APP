@props(['remark', 'user', 'payment'])
@if (gs('otp_verification'))
    <div class="form--group">
        <label class="form--label">@lang('Verification Type')</label>
        <div class="d-flex gap-3 flex-wrap mt-2">
            @foreach (gs('supported_otp_type') ?? [] as $type)
                <div class="flex-fill">
                    <label for="{{ $type }}" class="single-operator data-0 bundle-0 pin-1 w-100" data-index="0">
                        <input id="{{ $type }}" type="radio" name="verification_type" value="{{ $type }}"
                            required @checked($loop->first)>
                        @if ($type == 'email')
                            <span class="img">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                    height="20" color="#6b7280" fill="none">
                                    <path
                                        d="M2 4.5L8.91302 8.41697C11.4616 9.86101 12.5384 9.86101 15.087 8.41697L22 4.5"
                                        stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                    <path d="M15 17.5C15 17.5 15.5 17.5 16 18.5C16 18.5 17.5882 16 19 15.5"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M22 17C22 19.7614 19.7614 22 17 22C14.2386 22 12 19.7614 12 17C12 14.2386 14.2386 12 17 12C19.7614 12 22 14.2386 22 17Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path
                                        d="M9.10247 2.03664L9.12134 2.7864L9.10247 2.03664ZM2.01577 9.03952L2.7656 9.05548L2.01577 9.03952ZM14.9078 2.03665L14.9267 1.28689L14.9078 2.03665ZM21.9945 9.03953L22.7444 9.02357L21.9945 9.03953ZM9.08361 19.7498C9.4977 19.7602 9.84182 19.433 9.85224 19.0189C9.86266 18.6048 9.53543 18.2607 9.12135 18.2502L9.08361 19.7498ZM2.01577 11.9971L2.7656 11.9812L2.01577 11.9971ZM21.2446 10.508C21.2389 10.9222 21.5701 11.2626 21.9842 11.2682C22.3984 11.2739 22.7388 10.9428 22.7444 10.5286L21.2446 10.508ZM9.12134 2.7864C11.0502 2.73786 12.96 2.73787 14.8889 2.78641L14.9267 1.28689C12.9726 1.23771 11.0377 1.2377 9.08361 1.28688L9.12134 2.7864ZM1.26594 9.02355C1.24469 10.022 1.24469 11.0146 1.26594 12.0131L2.7656 11.9812C2.7448 11.004 2.7448 10.0327 2.7656 9.05548L1.26594 9.02355ZM9.08361 1.28688C7.531 1.32595 6.28463 1.35541 5.28825 1.52921C4.25805 1.70892 3.42092 2.05359 2.71382 2.76454L3.77736 3.82232C4.20198 3.39538 4.71823 3.1513 5.54602 3.0069C6.40763 2.8566 7.52383 2.8266 9.12134 2.7864L9.08361 1.28688ZM2.7656 9.05548C2.79879 7.49669 2.82388 6.41086 2.96979 5.56924C3.10953 4.76325 3.34968 4.25233 3.77736 3.82232L2.71382 2.76454C2.0098 3.4724 1.66774 4.29846 1.49184 5.31301C1.32212 6.29193 1.29816 7.5102 1.26594 9.02355L2.7656 9.05548ZM14.8889 2.78641C16.4865 2.82661 17.6027 2.85661 18.4643 3.00691C19.2921 3.15131 19.8083 3.3954 20.2329 3.82233L21.2965 2.76456C20.5894 2.05361 19.7522 1.70894 18.722 1.52923C17.7257 1.35542 16.4793 1.32596 14.9267 1.28689L14.8889 2.78641ZM22.7444 9.02357C22.7121 7.51022 22.6882 6.29195 22.5184 5.31302C22.3426 4.29848 22.0005 3.47242 21.2965 2.76456L20.2329 3.82233C20.6606 4.25235 20.9008 4.76326 21.0405 5.56926C21.1864 6.41087 21.2115 7.49671 21.2447 9.0555L22.7444 9.02357ZM9.12135 18.2502C7.52384 18.21 6.40763 18.18 5.54602 18.0297C4.71823 17.8853 4.20198 17.6413 3.77737 17.2143L2.71383 18.2721C3.42092 18.983 4.25806 19.3277 5.28825 19.5074C6.28463 19.6812 7.53101 19.7107 9.08361 19.7498L9.12135 18.2502ZM1.26594 12.0131C1.29816 13.5264 1.32213 14.7447 1.49185 15.7236C1.66774 16.7382 2.0098 17.5642 2.71383 18.2721L3.77737 17.2143C3.34968 16.7843 3.10954 16.2734 2.9698 15.4674C2.82389 14.6258 2.79879 13.5399 2.7656 11.9812L1.26594 12.0131ZM21.9945 10.5183C22.7444 10.5286 22.7444 10.5286 22.7444 10.5286C22.7444 10.5286 22.7444 10.5285 22.7444 10.5285C22.7445 10.5285 22.7445 10.5284 22.7445 10.5284C22.7445 10.5282 22.7445 10.528 22.7445 10.5278C22.7445 10.5273 22.7445 10.5266 22.7445 10.5257C22.7445 10.5239 22.7445 10.5213 22.7446 10.5178C22.7447 10.5109 22.7448 10.5007 22.745 10.4877C22.7453 10.4615 22.7457 10.4236 22.7462 10.3764C22.7472 10.2821 22.7484 10.1502 22.7491 9.99997C22.7507 9.70139 22.7507 9.3235 22.7444 9.02357L21.2447 9.0555C21.2506 9.33464 21.2507 9.69615 21.2492 9.99219C21.2484 10.1393 21.2472 10.2685 21.2463 10.361C21.2458 10.4071 21.2454 10.4441 21.2451 10.4694C21.2449 10.4821 21.2448 10.4918 21.2447 10.4983C21.2447 10.5016 21.2446 10.5041 21.2446 10.5057C21.2446 10.5065 21.2446 10.5071 21.2446 10.5075C21.2446 10.5077 21.2446 10.5079 21.2446 10.5079C21.2446 10.508 21.2446 10.508 21.2446 10.508C21.2446 10.508 21.2446 10.508 21.2446 10.508C21.2446 10.508 21.2446 10.508 21.9945 10.5183Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                            <div class="d-flex gap-0 flex-column">
                                <span class="title">{{ __(ucfirst($type)) }}</span>
                                <span>{{ @$user->email }}</span>
                            </div>
                        @else
                            <span class="img">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                    height="20" color="#6b7280" fill="none">
                                    <path
                                        d="M5 8C5.0104 5.35561 5.10801 3.94101 6.02389 3.02513C7.04901 2 8.69893 2 11.9988 2C15.2986 2 16.9485 2 17.9736 3.02513C18.8895 3.94101 18.9871 5.35561 18.9975 8M5 16C5.0104 18.6444 5.10801 20.059 6.02389 20.9749C7.04901 22 8.69893 22 11.9988 22C15.2986 22 16.9485 22 17.9736 20.9749C18.8895 20.059 18.9871 18.6444 18.9975 16"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M11 19H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M16 10L17.2265 11.0572C17.7422 11.5016 18 11.7239 18 12C18 12.2761 17.7422 12.4984 17.2265 12.9428L16 14"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M8 10L6.77346 11.0572C6.25782 11.5016 6 11.7239 6 12C6 12.2761 6.25782 12.4984 6.77346 12.9428L8 14"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M13 9L11 15" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div class="d-flex gap-0 flex-column">
                                <span class="title">{{ __(ucfirst($type)) }}</span>
                                <span>{{ @$user->mobileNumber }}</span>
                            </div>
                        @endif
                    </label>
                </div>
            @endforeach
        </div>
    </div>
@endif

@push('end-content')
    <div id="otp-modal" class="modal fade custom--modal fade" tabindex="-1" role="dialog" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('OTP VERIFICATION')</h4>
                </div>
                <form method="POST" class="verify-form  no-submit-loader">
                    @csrf
                    <div class="modal-body">
                        <div class="otp-html d-none">
                            <div class="form-group">
                                <div class="alert alert--warning notice fw-bold otp-message text-center"></div>
                            </div>
                            <div class="form-group">
                                <div id="otp-timer" class="otp-timer my-5" data-second="{{ gs('otp_expiration') }}"></div>
                            </div>
                            <div class="my-4">
                                <input type="hidden" name="otp">
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                <div class="otp-inner">
                                    <input type="text" maxlength="1" class="otp-input" autofocus />
                                    <input type="text" maxlength="1" class="otp-input" />
                                    <input type="text" maxlength="1" class="otp-input" />
                                    <input type="text" maxlength="1" class="otp-input" />
                                    <input type="text" maxlength="1" class="otp-input" />
                                    <input type="text" maxlength="1" class="otp-input" />
                                </div>
                                <a href="javascript:void(0)"
                                    class="resend-otp resend-otp-link  mt-2 d-none">@lang('Resend Code')</a>
                            </div>
                        </div>
                        <div class="pin-html p-2 d-none">
                            <div class="form-group">
                                <label class="form--label mb-2">@lang('Enter your PIN')</label>
                                <x-pin autoSubmit="false" />
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--base btn--md w-100">
                                <i class="fa fa-paper-plane"></i> @lang('Submit')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/timer.js') }}"></script>
@endpush
@push('script')
    <script>
        "use strict";
        (function($) {

            const $otpModal = $('#otp-modal');
            const $loader = $('.full-page-loader');
            var alertOnRefresh = false;
            var otpCode = "";

            var distance = Number("{{ gs('otp_expiration') }}");

            function resentCountDown(remainingTime) {
                let count = 0;
                const intervalId = setInterval(() => {
                    count++;
                    if (count >= distance) {
                        $(".resend-otp-link").removeClass('d-none');
                        clearInterval(intervalId);
                    }
                }, 1000);
            }

            $("body").on("input paste keydown keyup", ".otp-input", function() {
                otpCode = "";
                let allFilled = true;

                $('body').find(".otp-input").each(function() {
                    if ($(this).val().length !== 1) {
                        allFilled = false;
                    }
                    otpCode += $(this).val();
                });
                $(`input[name=otp]`).val(otpCode);
            });

            $('.has-payment-verification-form').on('submit', function(e) {
                e.preventDefault();

                alertOnRefresh = false;
                const formData = new FormData($(this)[0]);

                formData.append('remark', "{{ $remark }}");
                const action = $(this).attr('action');
                const $this = $(this);
                const $submitBtn = $this.find(`button[type=submit]`);
                const oldHtml = $submitBtn.html();

                $.ajax({
                    type: "POST",
                    url: action,
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $loader.removeClass('d-none');
                    },
                    complete: function() {
                        setTimeout(() => {
                            $loader.addClass('d-none');
                        }, 500);
                    },
                    success: function(response) {
                        setTimeout(() => {
                            if (response.status == 'success') {
                                if (response.data.next_step == 'otp') {
                                    alertOnRefresh = true;
                                    $(".otp-message").text(response.data.code_sent_message)
                                    prepareOtpHtml();
                                } else if (response.data.next_step == 'pin') {
                                    alertOnRefresh = true;
                                    preparePinHtml();
                                } else {
                                    notify('error', "@lang('Something went wrong')");
                                }
                            } else {
                                notify('error', response.message);
                            }
                        }, 500);
                    }

                });
            });

            $('body').on('submit', ".otp-verification", function(e) {
                e.preventDefault();

                if (otpCode.length != 6) {
                    notify('error', "@lang('Please enter a valid 6-digit OTP')");
                    return false;
                }

                alertOnRefresh = false;
                const formData = new FormData($(this)[0]);
                formData.append('otp', otpCode);

                formData.append('remark', "{{ $remark }}");
                const action = $(this).attr('action');
                const $this = $(this);
                const $submitBtn = $this.find(`button[type=submit]`);
                const oldHtml = $submitBtn.html();

                $.ajax({
                    type: "POST",
                    url: action,
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $loader.removeClass('d-none');
                    },
                    complete: function() {
                        setTimeout(() => {
                            $loader.addClass('d-none');
                        }, 500);
                    },
                    success: function(response) {
                        setTimeout(() => {
                            if (response.status == 'success') {
                                alertOnRefresh = true;
                                preparePinHtml($this);
                            } else {
                                notify('error', response.message);
                            }
                        }, 500);
                    },
                    error: function(e) {
                        notify('error', "@lang('An error occurred while processing your request')", e);
                    }

                });

            });

            $('body').on('submit', ".pin-verification", function(e) {
                e.preventDefault();

                const formData = new FormData($(this)[0]);
                formData.append('remark', "{{ $remark }}");

                alertOnRefresh = false;

                const action = $(this).attr('action');
                const $this = $(this);
                const $submitBtn = $this.find(`button[type=submit]`);
                const oldHtml = $submitBtn.html();

                $.ajax({
                    type: "POST",
                    url: action,
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $loader.removeClass('d-none');
                    },
                    complete: function() {
                        setTimeout(() => {
                            $loader.addClass('d-none');
                        }, 500);
                    },
                    success: function(response) {
                        setTimeout(() => {
                            if (response.status) {
                                alertOnRefresh = false;
                                notify(response.status, response.message);
                                setTimeout(() => {
                                    if (response.data.redirect_type == 'new_url') {
                                        window.location.href = response.data
                                            .redirect_url;
                                    } else {
                                        window.location.reload();
                                    }
                                }, 1500);
                            } else {
                                notify('error', response.message || "@lang('Something went wrong')");
                            }
                        }, 500);
                    }
                });
            });

            $('.resend-otp-link').on('click', function() {
                alertOnRefresh = false;

                let remark = "{{ $remark }}";
                let verificationType = $('.verification_type').val();
                let userId = $('input[name=user_id]').val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('payment.resend.code') }}",
                    data: {
                        remark: remark,
                        verification_type: verificationType,
                        user_id: userId,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $loader.removeClass('d-none');
                    },
                    complete: function() {
                        setTimeout(() => {
                            $loader.addClass('d-none');
                        }, 500);
                    },
                    success: function(response) {
                        setTimeout(() => {
                            if (response.status == 'success') {
                                $(".resend-otp-link").addClass('d-none');
                                prepareOtpHtml();
                            } else {
                                notify('error', response.message);
                            }
                        }, 500);
                    }
                });
            });

            function prepareOtpHtml() {
                startTimer();
                resentCountDown();

                $loader.addClass('d-none');
                $otpModal.find('.modal-title').text('OTP VERIFICATION');
                $otpModal.find('.otp-html').removeClass('d-none');
                $otpModal.find('.verify-form').attr('action', "{{ route('payment.verify.otp') }}");
                $otpModal.find('.verify-form').addClass('otp-verification');
                $otpModal.modal('show');
                let verificationType = $('.form-select').val();
            }

            function preparePinHtml() {

                $loader.addClass('d-none');
                $otpModal.find('.modal-title').text('PIN CONFIRMATION');
                $otpModal.find('.otp-html').addClass('d-none');
                $otpModal.find('.pin-html').removeClass('d-none');
                $otpModal.find('.verify-form').removeClass('otp-verification').addClass("pin-verification");
                $otpModal.find('.verify-form').attr("action", "{{ route('payment.verify.pin') }}");
                $otpModal.modal('show');
            }

            window.addEventListener('beforeunload', function(event) {
                if (alertOnRefresh) {
                    event.preventDefault();
                    event.returnValue = '';
                    var confirmationMessage = 'Are you sure you want to refresh this page?';
                    (event || window.event).returnValue = confirmationMessage;
                    return confirmationMessage;
                }
            });


        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        /* //timer css */

        .base-timer {
            position: relative;
            width: 150px;
            height: 150px;
            margin: auto;
        }

        .base-timer__svg {
            transform: scaleX(-1);
        }

        .base-timer__circle {
            fill: none;
            stroke: none;
        }

        .base-timer__path-elapsed {
            stroke-width: 6px;
            stroke: #efefef;
        }

        .base-timer__path-remaining {
            stroke-width: 4px;
            stroke-linecap: round;
            transform: rotate(90deg);
            transform-origin: center;
            transition: 1s linear all;
            fill-rule: nonzero;
            stroke: currentColor;
        }

        .base-timer__path-remaining.green {
            color: hsl(var(--success));
        }

        .base-timer__path-remaining.orange {
            color: hsl(var(--warning));
        }

        .base-timer__path-remaining.red {
            color: hsl(var(--danger));
        }

        .base-timer__label {
            position: absolute;
            width: 150px;
            height: 150px;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }


        .resend-otp-link {
            margin-left: 2.2rem;
        }
    </style>
@endpush
