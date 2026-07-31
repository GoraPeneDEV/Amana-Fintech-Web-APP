@extends($activeTemplate . 'layouts.merchant')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('merchant.withdraw.history') }}">
            <span class="icon" title="@lang('Withdraw History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            @lang('Withdraw Money')
        </a>
    </h4>
    <div class="col-lg-9">
        <form action="{{ route('merchant.withdraw.money') }}" method="post" class="withdraw-form">
            @csrf
            <div class="gateway-card custom--card">
                <div class="row justify-content-center gy-sm-4 gy-3">
                    <div class="col-12">
                        <h5 class="payment-card-title">@lang('Payment Methods')</h5>
                    </div>
                    <div class="col-lg-6">
                        <div class="payment-system-list is-scrollable gateway-option-list">
                            @foreach ($withdrawMethod as $data)
                                <label for="{{ titleToKey($data->name) }}"
                                    class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                    <div class="payment-item__info">
                                        <span class="payment-item__check"></span>
                                        <span class="payment-item__name">{{ __($data->name) }}</span>
                                    </div>
                                    <div class="payment-item__thumb">
                                        <img class="payment-item__thumb-img"
                                            src="{{ getImage(getFilePath('withdrawMethod') . '/' . $data->image) }}"
                                            alt="@lang('payment-thumb')">
                                    </div>
                                    @php
                                        $requestedMethod = request('method');
                                        $isChecked =
                                            $requestedMethod == $data->id ||
                                            old('method_code', null) == $data->id ||
                                            (!$requestedMethod && !old('method_code') && $loop->first);
                                    @endphp
                                    <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}"
                                        hidden data-gateway='@json($data)'
                                        data-save-accounts='@json(@$data->saveAccounts)' type="radio" name="method_code"
                                        value="{{ $data->id }}" @checked($isChecked)
                                        data-min-amount="{{ showAmount($data->merchant_min_limit) }}"
                                        data-max-amount="{{ showAmount($data->merchant_max_limit) }}">
                                </label>
                            @endforeach
                            @if ($withdrawMethod->count() > 4)
                                <button type="button" class="payment-item__btn more-gateway-option">
                                    <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                                    <span class="payment-item__btn__icon"><i class="fas fa-chevron-down"></i></i></span>
                                </button>
                            @endif
                            <div class="save-accounts-list d-none">
                                <div class="form-group mt-3">
                                    <label class="form--label">@lang('Select Saved Account')</label>
                                    <select name="save_account_id" class="form--control select2"
                                        data-minimum-results-for-search="-1">
                                        <option value="" disabled selected>@lang('Select one')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="payment-system-list p-3">
                            <div class="deposit-info">
                                <div class="deposit-info__title">
                                    <p class="text mb-0">@lang('Amount')</p>
                                </div>
                                <div class="deposit-info__input">
                                    <div class="deposit-info__input-group input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input type="text" class="form-control form--control amount bg-transparent"
                                            name="amount" placeholder="@lang('00.00')" value="{{ old('amount') }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="deposit-info">
                                <div class="deposit-info__title">
                                    <p class="text has-icon"> @lang('Limit')</p>
                                </div>
                                <div class="deposit-info__input">
                                    <p class="text"><span class="gateway-limit">@lang('0.00')</span> </p>
                                </div>
                            </div>
                            <div class="deposit-info">
                                <div class="deposit-info__title">
                                    <p class="text has-icon">@lang('Processing Charge')
                                        <span data-bs-toggle="tooltip" title="@lang('Processing charge for withdraw method')"
                                            class="proccessing-fee-info"><i class="las la-info-circle"></i> </span>
                                    </p>
                                </div>
                                <div class="deposit-info__input">
                                    <p class="text">{{ gs('cur_sym') }}<span
                                            class="processing-fee">@lang('0.00')</span>
                                        {{ __(gs('cur_text')) }}
                                    </p>
                                </div>
                            </div>
                            <div class="deposit-info total-amount pt-3">
                                <div class="deposit-info__title">
                                    <p class="text">@lang('Receivable')</p>
                                </div>
                                <div class="deposit-info__input">
                                    <p class="text">{{ gs('cur_sym') }}<span
                                            class="final-amount">@lang('0.00')</span>
                                        {{ __(gs('cur_text')) }}</p>
                                </div>
                            </div>
                            <div class="deposit-info gateway-conversion d-none total-amount pt-2">
                                <div class="deposit-info__title">
                                    <p class="text">@lang('Conversion')
                                    </p>
                                </div>
                                <div class="deposit-info__input">
                                    <p class="text"></p>
                                </div>
                            </div>
                            <div class="deposit-info conversion-currency d-none total-amount pt-2">
                                <div class="deposit-info__title">
                                    <p class="text">
                                        @lang('In') <span class="gateway-currency"></span>
                                    </p>
                                </div>
                                <div class="deposit-info__input">
                                    <p class="text">
                                        <span class="in-currency"></span>
                                    </p>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--base w-100" disabled>
                                @lang('Confirm Withdraw')
                            </button>
                            <div class="info-text pt-3">
                                <p class="text">@lang('Safely withdraw your funds using our highly secure process and various withdrawal method')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('merchant.withdraw.history') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-list"></i></span>
        @lang('Withdraw History')
    </a>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            var amount = parseFloat($('.amount').val() || 0);
            var gateway, minAmount, maxAmount;

            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val());
                if (!amount) {
                    amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', function(e) {
                showSaveAccounts();
                gatewayChange();
            });

            function showSaveAccounts() {
                let gatewayElement = $('.gateway-input:checked');

                if (!gatewayElement.length) return;

                let saveAccounts = gatewayElement.data('save-accounts');
                let saveAccountsElement = $('.save-accounts-list');
                let saveAccountsSelect = $('select[name=save_account_id]');

                if (saveAccounts.length == 0) {
                    saveAccountsElement.addClass('d-none');
                } else {
                    saveAccountsElement.removeClass('d-none');
                    $('.payment-system-list').removeClass('is-scrollable');
                }

                saveAccountsSelect.empty();
                saveAccountsSelect.append('<option value="">@lang('Select save account')</option>');
                saveAccounts.forEach((sub) => {
                    saveAccountsSelect.append(`<option value="${sub.id}">${sub.name}</option>`);
                });
            }

            showSaveAccounts();

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                gateway = gatewayElement.data('gateway');
                minAmount = gatewayElement.data('min-amount');
                maxAmount = gatewayElement.data('max-amount');

                let processingFeeInfo =
                    `${parseFloat(gateway.merchant_percent_charge).toFixed(2)}% with ${parseFloat(gateway.merchant_fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for processing fees`
                $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);

                calculation();
            }

            gatewayChange();

            $(".more-gateway-option").on("click", function(e) {
                let paymentList = $(".gateway-option-list");
                paymentList.find(".gateway-option").removeClass("d-none");
                $(this).addClass('d-none');
                paymentList.animate({
                    scrollTop: (paymentList.height() - 60)
                }, 'slow');
            });

            function calculation() {
                if (!gateway) return;
                $(".gateway-limit").text(minAmount + " - " + maxAmount);
                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.merchant_percent_charge);
                    fixedCharge = parseFloat(gateway.merchant_fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) - totalPercentCharge - fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.merchant_min_limit) || amount > Number(gateway.merchant_max_limit)) {
                    $(".withdraw-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".withdraw-form button[type=submit]").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}") {
                    $('.withdraw-form').addClass('adjust-height')
                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.withdraw-form').removeClass('adjust-height')
                }
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            $('.gateway-input').change();
            $('.breadcrumb-plugins-wrapper').remove();

        })(jQuery);
    </script>
@endpush
