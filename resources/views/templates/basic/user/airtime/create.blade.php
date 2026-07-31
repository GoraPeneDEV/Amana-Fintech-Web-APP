@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center justify-content-xl-start">
        <div class="col-xxl-6 col-xl-8 col-lg-8">
            <h4 class="mb-4">
                <a href="{{ route('user.airtime.history') }}">
                    <span class="icon" title="@lang('Airtime History')">
                        <i class="las la-arrow-circle-left"></i>
                    </span>
                    {{ __($pageTitle) }}
                </a>
            </h4>
            <div class="setting-wrapper multi-form-inner">
                <div class="card custom--card p-0 overflow-hidden">
                    <div class="card-body p-3 p-sm-4">
                        <form action="{{ route('user.airtime.store') }}" method="post"
                            class="multi-form send-money-form no-submit-loader">
                            @csrf
                            <input type="hidden" name="operator" class="operator-id">
                            <div class="multi-form__body">
                                <div class="multi-form__item active">
                                    <div class="multi-form__item_left">
                                        <span class="step-circle"></span>
                                    </div>
                                    <div class="multi-form__item_right">
                                        <div class="input--group multi-form-input">
                                            <label class="form--label">@lang('Add Phone Number')</label>
                                            <div class="form-inner position-relative">
                                                <div class="input-group bg-light">
                                                    <span class="input-group-text country-code">
                                                        <div class="select2--container position-relative">
                                                            <select class="form-control img-select2"
                                                                data-minimum-results-for-search="-1" name="country"
                                                                data-width="100%">
                                                                @foreach ($countries as $country)
                                                                    <option value='{{ $country->id }}'
                                                                        data-country='@json($country)'
                                                                        data-src="{{ $country->flag_url }}"
                                                                        @selected($loop->first)>
                                                                        {{ __(@$country->calling_codes[0]) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </span>
                                                    <input type="number" class="form--control form-control"
                                                        name="mobile_number" value="{{ old('mobile_number') }}">
                                                </div>
                                                <img src="" alt="@lang('image')" class="multi-form-img">
                                            </div>
                                            <div class="d-flex flex-wrap justify-content-between mt-2 multi-form--note">
                                                <span class="fs-14 text--muted operator-name">
                                                </span>
                                                <span class="fs-14 text--muted check-operator">@lang('Not the operator?')
                                                    <button type="button" data-bs-toggle="modal"
                                                        data-bs-target="#operator-modal">
                                                        @lang('Check Operators')
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="multi-form__item">
                                    <div class="multi-form__item_left">
                                        <span class="step-circle"></span>
                                    </div>
                                    <div class="multi-form__item_right">
                                        <div class="input--group multi-form-input">
                                            <label class="form--label">@lang('Add Amount')</label>
                                            <input type="number" step="any" class="form--control form-control"
                                                placeholder="@lang('0.00')" name="amount" value="{{ old('amount') }}"
                                                disabled>
                                        </div>
                                        <span class="mt-2 text--muted fs-14 amount-limit d-none">
                                            @lang('Amount has to be between')
                                            <span class="amount"></span>
                                            <span>{{ __(gs('cur_text')) }}</span>
                                        </span>
                                        <div class="flex-align gap-2 mt-3 suggest-amount-wrapper d-none">
                                            @foreach (gs('quick_amounts') ?? [] as $amount)
                                                <span class="suggest-amount quick-amount"
                                                    data-amount="{{ getAmount($amount) }}">
                                                    {{ gs('cur_sym') }}{{ showAmount($amount, currencyFormat: false) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="multi-form__item">
                                    <div class="multi-form__item_left">
                                        <span class="step-circle"></span>
                                    </div>
                                    <div class="d-flex flex-column gap-2 w-100">
                                        <div class="multi-form__item_right multi-form-details-wrapper">
                                            <div class="input--group multi-form-details">
                                                <div class="thumb">
                                                    <img class="fit-image airtime-multi-form-img"
                                                        src="assets/images/thumbs/multi-image.png" alt="@lang('image')">
                                                </div>
                                                <ul class="content">
                                                    <li>
                                                        <span class="text">@lang('Mobile Number:')</span>
                                                        <span class="text airtime-number"></span>
                                                    </li>
                                                    <li>
                                                        <span class="text">@lang('Amount:')</span>
                                                        <span class="text amount"></span>
                                                    </li>
                                                    <li>
                                                        <span class="text">@lang('Delivery Amount:')</span>
                                                        <span class="text delivery-amount"></span>
                                                    </li>
                                                </ul>
                                            </div>

                                        </div>
                                        <div class="verification-type-wrapper d-none">
                                            <x-otp_verification remark="mobile_recharge" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="multi-form__footer">
                                <div class="multi-form__btn">
                                    <button type="button"
                                        class="btn btn--base w-100 align-items-center justify-content-between confirm-number">
                                        <span>
                                            @lang('Confirm Number')
                                        </span>
                                        <i class="fa-solid fa-arrow-right-long"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal custom--modal fade" id="operator-modal">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">@lang('Select Operator')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs custom--tab modify-with-operator" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active operator-filter-btn" type="button" data-show="all">
                                @lang('All')
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link operator-filter-btn" type="button" data-show="data">
                                @lang('Data')
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link operator-filter-btn" type="button" data-show="bundle">
                                @lang('Bundle')
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link operator-filter-btn" type="button" data-show="pin">
                                @lang('PIN')
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="single-operator-wrapper"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-0 modify-with-operator">
                    <button type="button" class="btn btn--base operator-confirm">
                        <i class="las la-check-circle"></i> @lang('Confirm')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: -2px !important;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            const $operatorModal = $("#operator-modal");
            const $operatorWrapper = $operatorModal.find(".single-operator-wrapper");
            const $selectOperatorWrapper = $('body').find(".select-operator");
            const $suggestAmountWrapper = $('body').find(".suggest-amount-wrapper");
            const operators = [];
            const $loader = $('.full-page-loader');
            const $mobileInput = $('input[name=mobile_number]');
            const $confirmBtn = $(".confirm-number");

            var selectedOperator;

            $confirmBtn.prop('disabled', true);
            $mobileInput.on('input', function() {
                const hasNumber = $(this).val().trim().length > 0;
                $confirmBtn.prop('disabled', !hasNumber);
            });

            $('.breadcrumb-plugins-wrapper').remove();
            $(".suggest-data").on('click', function() {
                const $this = $(this);
                $('body').find(`input[name=${$this.data('name')}]`).val($this.find('.data').text().trim());
            });

            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var $state = $(
                    '<span class="img-flag-inner"><img src="' + $(state.element).attr('data-src') +
                    '" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            };


            $('.img-select2').select2({
                dropdownParent: $('.img-select2').parents('.select2--container'),
                templateResult: formatState,
                templateSelection: formatState,
                width: "100%"
            });

            $("select[name=country]").on('change', function() {
                const country = $(this).find('option:selected').data('country') ?? null;
                $(".dial-code").text(country.calling_codes[0]);

                operators.length = 0;
                operators.push(...country.operators);
                operatorHtml();

                $('.multi-form').find('button')
                    .last()
                    .attr('type', 'button')
                    .removeClass('confirm-amount')
                    .addClass('confirm-number')
                    .find('span')
                    .text("{{ __('Confirm Number') }}")
                    .trigger('click');

                const $stepOneElement = $(".multi-form__item").eq(0);
                const $stepTwoElement = $(".multi-form__item").eq(1);
                const $stepThreeElement = $(".multi-form__item").eq(2);

                $stepTwoElement.removeClass('has-after');
                $stepTwoElement.removeClass('active');
                $stepThreeElement.removeClass('active');

                const $form = $('.multi-form');

                $form.find('.operator-name').text('');
                $form.find('.multi-form-img').addClass('d-none');
                $form.find('input[name="amount"]').val('').prop('disabled', true);
                $form.find('.suggest-amount-wrapper, .check-operator, .amount-limit').addClass('d-none');

            }).change();

            $("body").on('click', ".operator-filter-btn", function() {
                $('.operator-filter-btn').removeClass('active');
                $(this).addClass('active');

                if ($(this).data('show') == 'all') {
                    $operatorWrapper.find('.single-operator').removeClass('d-none');
                } else {
                    $operatorWrapper.find('.single-operator').addClass('d-none');
                    $operatorWrapper.find(`.${$(this).data('show')}-1`).removeClass('d-none');
                }
            });

            $("body").on('click', ".operator-confirm", function() {
                const $selectedOperator = $operatorModal.find('.single-operator.active');
                if ($selectedOperator.length <= 0) {
                    notify("error", "Please select the operator");
                    return;
                }

                const operatorId = $selectedOperator.data('operator');
                selectedOperator = operators.find(operator => operator.unique_id == operatorId);

                setSingleOperatorHtml();
                $operatorModal.modal('hide');
            });

            $("body").on('click', ".single-operator", function() {
                $(this).addClass('active').siblings().removeClass('active');
            });

            function operatorHtml() {
                let html = ``;
                if (operators.length > 0) {
                    operators.forEach((operator, index) => {
                        html += `<label for="${operator.id}" class="single-operator data-${operator.data} bundle-${operator.bundle} pin-${operator.pin}" data-operator='${operator.unique_id}' >
                        <input id="${operator.id}" type="radio" name="operator">
                        <span class="img">
                            <img src="${operator.logo_urls[0]}" alt="">
                            </span>
                        <span class="title">${operator.name}</span>
                    </label>`;
                    });
                    $(".modify-with-operator").removeClass('d-none');

                } else {
                    html = `
                    <div class="empty-message">
                        <p class="empty-message-icon">
                            <img src="{{ asset('assets/images/no-data.gif') }}" alt="">
                        </p>
                        <p class="empty-message-text">
                        </p>
                    </div>
                    `
                    $(".modify-with-operator").addClass('d-none');
                }
                $operatorWrapper.html(html);
            }

            $("body").on('click', ".quick-amount", function() {
                $("input[name=amount]").val(parseFloat($(this).data("amount")).toFixed(2)).trigger('change');
            });

            function setSingleOperatorHtml() {
                if (selectedOperator != undefined && Object.keys(selectedOperator).length > 0) {

                    const $stepOneElement = $(".multi-form__item").first();
                    const logoUrl = selectedOperator.logoUrls ? selectedOperator.logoUrls[0] : selectedOperator
                        .logo_urls[0];

                    $stepOneElement.find('.multi-form-img').removeClass('d-none').attr('src', logoUrl);
                    $stepOneElement.find('.operator-name').text(selectedOperator.name);
                    $stepOneElement.find('.check-operator').removeClass('d-none');

                    $selectOperatorWrapper.find('.title').text(selectedOperator.name);
                    $selectOperatorWrapper.find('img').attr('src', logoUrl);
                    $selectOperatorWrapper.find('.thumb,.btn-wrapper').removeClass('d-none');

                    $('body').find('.operator-id').val(selectedOperator.id);

                    const minAmount = selectedOperator.min_amount ? selectedOperator.min_amount : selectedOperator
                        .minAmount;
                    const maxAmount = selectedOperator.max_amount ? selectedOperator.max_amount : selectedOperator
                        .maxAmount;

                    if (minAmount && maxAmount) {
                        $(".amount-limit").removeClass('d-none');
                        $(".amount-limit").find('.amount').text(
                            `${parseFloat(minAmount).toFixed(2)}-${parseFloat(maxAmount).toFixed(2)}`
                        );
                    } else {

                        $(".amount-limit").addClass('d-none');
                    }

                    var amountHtml = ``;

                    const quickAmounts = validJsonParse(selectedOperator.suggested_amounts);
                    const fixedAmounts = validJsonParse(selectedOperator.fixed_amounts);
                    const fixedAmountsDescriptions = validJsonParse(selectedOperator.fixed_amounts_descriptions);


                    if (quickAmounts && quickAmounts.length > 0) {
                        quickAmounts.forEach(amount => {
                            amountHtml += `<span class="suggest-amount quick-amount" data-amount="${parseFloat(amount).toFixed(2)}">
                                    {{ gs('cur_sym') }}${parseFloat(amount).toFixed(2)}
                            </span>
                            `
                        });
                    } else if (fixedAmounts && fixedAmounts.length > 0) {
                        if (fixedAmountsDescriptions && Object.keys(fixedAmountsDescriptions).length > 0) {
                            Object.entries(fixedAmountsDescriptions).forEach(([key, value]) => {
                                amountHtml += `<span class="suggest-amount quick-amount flex-fill" data-amount="${parseFloat(key).toFixed(2)}">
                                <div class="d-flex gap-1 justify-content-center align-items-center flex-column">
                                    {{ gs('cur_sym') }}${parseFloat(key).toFixed(2)}
                                    <p class="fs-14">${value}</p>
                                    </div>
                            </span>`;

                            });
                        } else {
                            fixedAmounts.forEach(amount => {

                                amountHtml += `<span class="suggest-amount quick-amount" data-amount="${parseFloat(amount).toFixed(2)}">
                                {{ gs('cur_sym') }}${parseFloat(amount).toFixed(2)}(${amount.description})
                            </span>
                            `
                            });
                        }
                    } else {
                        amountHtml += `@foreach (gs('quick_amounts') ?? [] as $amount)
                            <span class="suggest-amount quick-amount" data-amount="{{ getAmount($amount) }}">
                                {{ gs('cur_sym') }}{{ showAmount($amount, currencyFormat: false) }}
                            </span>
                        @endforeach`
                    }
                    $suggestAmountWrapper.html(amountHtml);
                } else {
                    $selectOperatorWrapper.find('.title').text("{{ __('Select Operator') }}");
                    $selectOperatorWrapper.find('.thumb,.btn-wrapper').addClass('d-none');
                    $(".amount-limit").addClass('d-none');
                    $('body').find('.operator-id').val("");
                    var amountHtml = ``;
                    amountHtml += `@foreach (gs('quick_amounts') ?? [] as $amount)
                            <span class="suggest-amount quick-amount" data-amount="{{ getAmount($amount) }}">
                                {{ gs('cur_sym') }}{{ showAmount($amount, currencyFormat: false) }}
                            </span>
                        @endforeach`
                    $suggestAmountWrapper.html(amountHtml);
                }
            }


            function validJsonParse(data) {
                if (typeof data == 'object' || typeof data == 'array') return data;

                try {
                    data = JSON.parse(data);
                } catch {
                    data = [];
                }
                return data;
            }

            $("body").on('click', ".confirm-number", function(e) {

                const mobileNumber = $('input[name=mobile_number]').val();
                const country = $('select[name=country]').val();
                const $this = $(this);

                if (!country) {
                    notify("error", "Please enter the mobile number properly");
                    return false;
                }
                if (!mobileNumber) {
                    $('body').find('.mobile_number').focus();
                    return false;
                }

                $.ajax({
                    type: "GET",
                    url: "{{ route('user.airtime.number.look.up') }}",
                    data: {
                        mobile_number: mobileNumber,
                        country: country
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $loader.removeClass('d-none');
                    },
                    complete: function() {
                        $loader.addClass('d-none');
                    },
                    success: function(response) {
                        if (response.remark == 'success') {

                            const $stepOneElement = $(".multi-form__item").first();
                            const $stepTwoElement = $(".multi-form__item").eq(1);

                            $stepOneElement.addClass('has-after');
                            $stepTwoElement.addClass('active');
                            $stepTwoElement.find('input:disabled').prop('disabled', false);
                            $this.addClass('confirm-amount').removeClass('confirm-number');
                            $this.find('span').text("{{ __('Confirm Amount') }}");

                            $suggestAmountWrapper.removeClass('d-none');

                            selectedOperator = operators.find(operator => operator.unique_id ==
                                response.data.operator.id);

                            setSingleOperatorHtml();
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

            $("body").on('click', ".confirm-amount", function(e) {
                const amount = parseFloat($('input[name=amount]').val());
                const $this = $(this);
                const operatorId = $('body').find('.operator-id').val();

                if (!amount) {
                    notify("error", "Please enter amount properly");
                    return false;
                }

                $.ajax({
                    type: "GET",
                    url: "{{ route('user.airtime.amount.validate') }}",
                    data: {
                        amount: amount,
                        operator: operatorId
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $loader.removeClass('d-none');
                    },
                    complete: function() {
                        $loader.addClass('d-none');
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            selectedOperator = response.data.operator;

                            const rate = parseFloat(selectedOperator.rate) || 1;

                            const $stepThreeElement = $(".multi-form__item").eq(2);
                            const $stepTwoElement = $(".multi-form__item").eq(1);
                            const userNumber = $('input[name="mobile_number"]').val();
                            const selectedAmount = parseFloat($('input[name="amount"]').val()) || 0;


                            const deliveryAmount = selectedAmount * rate;
                            const logoUrls = Array.isArray(selectedOperator.logo_urls) ?
                                selectedOperator.logo_urls : JSON.parse(selectedOperator
                                    .logo_urls || '[]');
                            const logoUrl = logoUrls[0] || '';
                            const currencySymbol = "{{ gs('cur_sym') }}";
                            const destinationCurrencySymbol = selectedOperator
                                .destination_currency_symbol ?? currencySymbol;

                            $stepThreeElement.find('.airtime-multi-form-img').attr('src', logoUrl);
                            $stepThreeElement.find('.airtime-number').text(userNumber);
                            $stepThreeElement.find('.amount').text(currencySymbol + selectedAmount
                                .toFixed(2));

                            $stepThreeElement.find('.delivery-amount').text(
                                destinationCurrencySymbol + deliveryAmount.toFixed(2));

                            $stepTwoElement.addClass('has-after active');
                            $stepThreeElement.addClass('active');
                            $this.find('span').text("{{ __('Confirm Airtime') }}");
                            $this.removeClass('confirm-amount');
                            $this.attr('type', 'submit');
                            $this.closest('form').addClass('has-otp-form');
                            $(".verification-type-wrapper").removeClass('d-none');

                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

            $('input[name=amount]').on('input change', function() {
                if (selectedOperator && Object.keys(selectedOperator).length > 0) {

                    const amount = parseFloat($(this).val());
                    const rate = parseFloat(selectedOperator.rate) || 1;

                    const deliveryAmount = amount * rate;
                    const currencySymbol = "{{ gs('cur_sym') }}";
                    const destinationCurrencySymbol = selectedOperator
                        .destination_currency_symbol ?? currencySymbol;
                    $('.amount').text(currencySymbol + amount.toFixed(2));
                    $('.delivery-amount').text(destinationCurrencySymbol + deliveryAmount.toFixed(2));
                }
            });

        })(jQuery);
    </script>
@endpush
