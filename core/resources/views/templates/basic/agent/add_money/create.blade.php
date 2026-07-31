@extends($activeTemplate . 'layouts.agent')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('agent.add.money.history') }}">
            <span class="icon" title="@lang('Add Money History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            @lang('Add Money')
        </a>
    </h4>
    <div class="row">
        <div class="col-lg-6">
            <form action="{{ route('agent.add.money.store') }}" method="post" class="deposit-form">
                @csrf
                <input type="hidden" name="currency">
                <input type="hidden" name="gateway">
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="form--group">
                            <label class="form--label">@lang('Payment Method')</label>
                            <button class="select-btn" type="button" data-bs-toggle="modal"
                                data-bs-target="#payment-gateway-modal">
                                <span>@lang('Select Payment Method')</span>
                                <span class="icon">
                                    <i class="fas fa-caret-down"></i>
                                </span>
                            </button>
                        </div>
                        <div class="form--group mb-0">
                            <label class="form--label">@lang('Amount')</label>
                            <div class="input-group input--amount border-0">
                                <input type="number" step="any" class="form--control sm-style form-control"
                                    placeholder="@lang('0.00')" value="{{ old('amount') }}" name="amount" required>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                            <span class="fw-medium mt-2 limit-wrapper d-none">
                                @lang('Limit'):
                                <span class="text--base fw-bold limit"></span>
                            </span>
                            <div class="flex-align gap-2 mt-3">
                                @foreach (gs('quick_amounts') ?? [] as $amount)
                                    <span class="suggest-amount quick-amount" data-amount="{{ getAmount($amount) }}">
                                        {{ gs('cur_sym') }}{{ showAmount($amount, currencyFormat: false) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="deposit-wrapper">
                            <div class="deposit-wrapper-info">
                                <span class="title">
                                    @lang('Processing Charge')
                                    <button type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="@lang('Processing Charge')" class="charge-info">
                                        <i class="las la-info-circle"></i>
                                    </button>
                                </span>
                                <span class="amount">
                                    <span>{{ gs('cur_sym') }}</span><span class="processing-fee">@lang('0.00')</span>
                                </span>
                            </div>
                            <div class="deposit-wrapper-total">
                                <span class="title">
                                    @lang('Total')
                                </span>
                                <span class="amount">
                                    <span>{{ gs('cur_sym') }}</span><span class="final-amount">@lang('0.00')</span>
                                </span>
                            </div>
                            <div class="conversion-currency d-none">
                                <div class="deposit-wrapper-total">
                                    <span class="title">
                                        @lang('Conversion')
                                    </span>
                                    <span class="amount">
                                        <span class="rate">
                                        </span>
                                    </span>
                                </div>
                                <div class="deposit-wrapper-total in-currency">
                                </div>
                            </div>
                            <div class="crypto-message mt-3 d-none">
                                @lang('Conversion with') <span class="gateway-currency"></span> @lang('and final value will Show on next step')
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn--base w-100">
                    <span class="flex-between">
                        @lang('Continue')
                        <span class="icon">
                            <i class="fas fa-arrow-right-long"></i>
                        </span>
                    </span>
                </button>
            </form>
        </div>
    </div>

    <div class="modal custom--modal fade payment-gateway-modal" id="payment-gateway-modal" tabindex="-1" role="dialog"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6>@lang('Select Payment Method')</h6>
                    <button class="btn btn--base btn--sm payment-gateway-confirm-btn disabled" disabled type="button">
                        @lang('Confirm') <i class="las la-check-circle"></i>
                    </button>
                </div>
                <div class="modal-body pt-0 pb-4">
                    <div class="payment-system-list is-scrollable">
                        @foreach ($gatewayCurrency as $data)
                            <label for="{{ $loop->index }}" class="payment-item  gateway-option"
                                data-min-amount="{{ showAmount($data->min_amount) }}"
                                data-max-amount="{{ showAmount($data->max_amount) }}"
                                data-percent-charge="{{ getAmount($data->percent_charge) }}"
                                data-fixed-charge="{{ getAmount($data->fixed_charge) }}"
                                data-rate="{{ getAmount($data->rate) }}" data-gateway="{{ $data->method_code }}"
                                data-currency="{{ $data->currency }}" data-crypto="{{ $data->method->crypto }}"
                                data-name="{{ __($data->name) }}" />
                            <span class="payment-item__info">
                                <span class="payment-item__check"></span>
                                <span class="payment-item__name">{{ __($data->name) }}</span>
                            </span>
                            <span class="payment-item__thumb">
                                <img class="payment-item__thumb-img"
                                    src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                    alt="payment-thumb">
                            </span>
                            <input class="payment-item__radio gateway-input" name="gateway" id="{{ $loop->index }}"
                                type="radio" hidden="" value="{{ $data->method_code }}">
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            let percentCharge = 0;
            let fixedCharge = 0;
            let rate = 0;
            let currency;
            let gateway;
            let crypto;

            $(".payment-gateway-confirm-btn").on('click', function() {

                const $paymentGatewayElement = $(".payment-item.active");
                const minAmount = $paymentGatewayElement.data('min-amount');
                const maxAmount = $paymentGatewayElement.data('max-amount');

                fixedCharge = parseFloat($paymentGatewayElement.data('fixed-charge') || 0);
                percentCharge = parseFloat($paymentGatewayElement.data('percent-charge') || 0);
                rate = parseFloat($paymentGatewayElement.data('rate') || 0);
                currency = $paymentGatewayElement.data('currency');
                crypto = $paymentGatewayElement.data('crypto');
                gateway = $paymentGatewayElement.data('gateway');


                $(".gateway-currency").text(currency);
                $("input[name=currency]").val(currency);
                $("input[name=gateway]").val(gateway);


                $('.charge-info')
                    .attr('data-bs-title',
                        `Charge: {{ gs('cur_sym') }}${fixedCharge.toFixed(2)}+${percentCharge.toFixed(0)}%`)
                    .tooltip('dispose')
                    .tooltip();

                if ($paymentGatewayElement.length == 1) {
                    $('.limit-wrapper').removeClass('d-none');
                    $('.limit-wrapper').find('.limit').text(`${minAmount} - ${maxAmount}`);
                    calculation();
                    $(".select-btn").find(`span`).first().text($paymentGatewayElement.data('name'));
                    $('#payment-gateway-modal').modal('hide');
                } else {
                    notify("error", "@lang('Please select payment gateway')");
                }
            });

            $(".payment-item").on('click', function() {
                $(".payment-gateway-confirm-btn").removeClass("disabled").attr("disabled", false);
                $(this).addClass('active').siblings().removeClass('active');
            });

            $("input[name=amount]").on('input', function() {
                calculation();
            });
            $(".quick-amount").on('click', function() {
                $("input[name=amount]").val(parseInt($(this).data("amount")));
                calculation();
            });

            function calculation() {
                if (!currency) return;
                const amount = parseFloat($('body').find(`input[name="amount"]`).val() || 0);
                const totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                const totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                const totalAmount = parseFloat(amount + totalPercentCharge + fixedCharge);

                $(".processing-fee").text(totalCharge.toFixed(2));
                $(".final-amount").text(totalAmount.toFixed(2));

                if (currency != "{{ gs('cur_text') }}" && crypto != 1) {
                    $(".conversion-currency").removeClass('d-none');
                    $(".conversion-currency")
                        .find('.rate')
                        .html(
                            `1 {{ __(gs('cur_text')) }} = <span>${rate.toFixed(2)}</span>  <span class="method_currency">${currency}</span>`
                        );

                    $(".conversion-currency")
                        .find('.in-currency')
                        .html(`
                    <span class="title">
                        @lang('In') ${currency}
                    </span>
                    <span class="amount">${parseFloat(totalAmount * rate).toFixed(crypto == 1 ? 8 : 2)}</span>
                    `);
                } else {
                    $(".conversion-currency").addClass('d-none');
                }

                if (crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }
            }
            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
