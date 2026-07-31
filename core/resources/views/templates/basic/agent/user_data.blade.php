@extends($activeTemplate . 'layouts.app')
@section('app-content')
    @php
        $authContent = @getContent('auth_section.content', true)->data_values;
    @endphp
    <section class="account bg-img"
        data-background-image="{{ frontendImage('auth_section', @$authContent->background_image) }}">
        <div class="container">
            <div class="account-form">
                <div class="flex-between gap-2 mb-3">
                    <button type="button" class="back-btn mb-0 prev-btn">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <span class="text--base  fw-medium step-title">@lang('Personal Information')</span>
                </div>
                <div class="verify-step-wrapper mb-4">
                    <span class="verify-step active"></span>
                    <span class="verify-step"></span>
                    <span class="verify-step"></span>
                </div>
                <form method="POST" action="{{ route('agent.data.submit') }}">
                    @csrf
                    <div class="step-1">
                        <div class="form-group">
                            <label class="form--label">@lang('First Name')</label>
                            <input type="text" class=" form--control" name="firstname" value="{{ old('firstname') }}"
                                required placeholder="@lang('e.g. John')" required>
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Last Name')</label>
                            <input type="text" class=" form--control" name="lastname" value="{{ old('lastname') }}"
                                required placeholder="@lang('e.g. Doe')" required>
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Username')</label>
                            <input type="text" class="form--control" placeholder="@lang('e.g. johndoe')" required
                                value="{{ old('username') }}" name="username">
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Email')</label>
                            <input type="email" class="form--control" placeholder="@lang('e.g. johndoe@gmail.com')" required
                                value="{{ old('email') }}" name="email">
                        </div>
                    </div>
                    <div class="step-2 d-none">
                        <div class="form-group">
                            <label class="form--label">@lang('PIN')</label>
                            <x-pin autoSubmit="false" justifyClass="justify-content-start" />
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Confirm PIN')</label>
                            <x-pin autoSubmit="false" justifyClass="justify-content-start" name="pin_confirmation" />
                        </div>
                    </div>
                    <div class="step-3 d-none">
                        <div class="form-group">
                            <label class="form--label">@lang('Address')</label>
                            <input type="text" class="form-control form--control" name="address"
                                value="{{ old('address') }}" placeholder="@lang('Enter address')">
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('State')</label>
                            <input type="text" class="form-control form--control" name="state"
                                value="{{ old('state') }}" placeholder="@lang('Enter state')">
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Zip Code')</label>
                            <input type="text" class="form-control form--control" name="zip"
                                value="{{ old('zip') }}" placeholder="@lang('Enter zip')">
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('City')</label>
                            <input type="text" class="form-control form--control" name="city"
                                value="{{ old('city') }}" placeholder="@lang('Enter city')">
                        </div>
                    </div>
                    <button type="button" class="btn btn--grbtn w-100 continue-btn">
                        @lang('Continue') <i class="fa fa-angle-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {
            let currentStep = 1;
            let stepTwoValid = false;
            const pinLength = parseInt("{{ gs('user_pin_digits') }}");
            $(".continue-btn").on('click', function() {
                if (currentStep == 1) {
                    stepOneValidation()
                } else if (currentStep == 2) {
                    stepTwoValidation();
                }
            });

            function stepOneValidation() {
                const isValid = validateRequiredFields('.step-1');
                if (!isValid) return;

                var url = "{{ route('agent.checkUser') }}";
                var data = {
                    username: $('[name=username]').val(),
                    email: $('[name=email]').val(),
                    _token: '{{ csrf_token() }}'
                };
                $.post(url, data)
                    .done(function(response) {
                        if (response.status === 'error') {
                            $(`[name=${response.data.field}]`)
                                .parent()
                                .append(`<span class="text--danger mt-2 validation-error">
                            <i>${response.data.error_message}</i>
                        </span>`);
                        } else {
                            incrementStep();
                        }
                    });

            }

            function stepTwoValidation() {
                const isValid = validateRequiredFields('.step-2');
                if (!isValid) {
                    stepTwoValid = false;
                    return
                };
                stepTwoValid = true;
                incrementStep();
            }

            function validateRequiredFields(parent) {
                let isValid = true;

                $(parent).find('[required]').each(function() {
                    const $element = $(this);
                    const value = $element.val().trim();
                    const fieldName = $element.attr('name');
                    const errorContainer = $element.parent();

                    // Remove existing validation messages
                    errorContainer.find('.validation-error').remove();

                    if (value.length === 0) {
                        errorContainer.append(`<span class="text--danger mt-2 validation-error">
                                    <i>The ${$element.attr('name')} field is required</i>
                                   </span>`);
                        isValid = false;
                    }
                    if (value.length > 0) {
                        if (fieldName == "email" && !isValidEmail(value)) {
                            errorContainer.append(`<span class="text--danger mt-2 validation-error">
                                        <i>@lang('Please enter a valid email address')</i>
                                       </span>`);
                            isValid = false;
                        }
                        if (fieldName == "username" && value.length < 6) {
                            errorContainer.append(`<span class="text--danger mt-2 validation-error">
                                        <i>@lang('The username at least 6 character')</i>
                                       </span>`);
                            isValid = false;
                        }
                    }

                    if (fieldName == "pin" && value.length != pinLength) {
                        errorContainer.append(`<span class="text--danger mt-2 validation-error">
                                        <i>The pin must be ${pinLength} digits</i>
                                       </span>`);
                        isValid = false;
                    }
                    if (fieldName == "pin_confirmation" && value.length != pinLength) {
                        errorContainer.append(`<span class="text--danger mt-2 validation-error">
                                        <i>The confirmation pin must be ${pinLength} digits</i>
                                       </span>`);
                        isValid = false;
                    }
                });

                return isValid;
            }

            function isValidEmail(email) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailPattern.test(email);
            }

            function incrementStep() {
                const $currentStep = $(`.step-${currentStep}`);
                const $nextStep = $(`.step-${currentStep + 1}`);

                $currentStep.fadeOut(300, function() {
                    if (currentStep == 2 && !stepTwoValid) {
                        return;
                    }
                    $(this).addClass('d-none');
                    currentStep++;
                    $nextStep.removeClass('d-none').hide().fadeIn(300);
                    $(".verify-step-wrapper")
                        .find(`.verify-step:nth-child(${currentStep})`)
                        .addClass('active');
                    changeTitle();
                });
            }


            $('.prev-btn').on('click', function() {
                if (currentStep <= 1) {
                    window.location = "{{ route('home') }}";
                } else {
                    const $currentStep = $(`.step-${currentStep}`);
                    const $prevStep = $(`.step-${currentStep - 1}`);

                    $currentStep.fadeOut(300, function() {
                        $(this).addClass('d-none');
                        currentStep--;
                        $prevStep.removeClass('d-none').hide().fadeIn(300);
                        changeTitle();
                    });
                }
            });

            function changeTitle() {
                $(".continue-btn")
                    .attr('type', 'button')
                    .html(
                        `@lang('Continue') <i class="fa fa-angle-right"></i>`
                    );

                if (currentStep == 1) {
                    $(".step-title").text("@lang('Personal Information')");
                } else if (currentStep == 2) {
                    $(".step-title").text("@lang('Setup PIN')");
                } else {
                    $(".step-title").text("@lang('Your Address')");
                    $(".continue-btn")
                        .attr('type', 'submit')
                        .html(
                            `@lang('Submit') <i class="fa fa-paper-plane"></i>`
                        );
                }
            }


        })(jQuery);
    </script>
@endpush
