@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('user.investment.history') }}">
            <span class="icon" title="@lang('Investment History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __($pageTitle) }}
        </a>
    </h4>
        <div class="investment-card__wrapper">
            @foreach ($plans as $plan)
                <div class="card custom--card investment-card" data-plan-id="{{ $plan->id }}"
                    data-fixed-amount="{{ $plan->fixed_amount }}" data-min-invest="{{ $plan->min_invest }}"
                    data-max-invest="{{ $plan->max_invest }}">
                    <div class="card-body">
                        <div class="form--group mb-2">
                            <h4 class=" investment-title">{{ __($plan->name) }}</h4>
                            <p class="desc mb-4 fs-14">{{ __($plan->description) }}</p>
                        </div>
                        <div class="range-box p-3 rounded-3 mb-3">
                            <div class="d-flex justify-content-between small mb-2">
                                @if ($plan->fixed_amount > 0)
                                    <span>@lang('Investment Amount')</span>
                                    <span class="fw-bold">
                                        {{ gs('cur_sym') }}{{ getAmount($plan->fixed_amount) }}
                                        <small>(@lang('FIXED'))</small>
                                    </span>
                                @else
                                    <span>@lang('Investment Amount')</span>
                                    <span class="fw-bold">
                                        {{ gs('cur_sym') }}{{ getAmount($plan->min_invest) }}
                                        -
                                        {{ gs('cur_sym') }}{{ getAmount($plan->max_invest) }}
                                    </span>
                                @endif
                            </div>
                            <div class="slider-range mb-1"></div>
                            <span class="selected_amount fw-bold"></span>
                            <input type="hidden" name="invest_amount">
                        </div>

                        <div class="invest-pricing-info mb-2">
                            <div class="invest-pricing-info__item">
                                @if ($plan->interest_type == Status::INTEREST_TYPE_FIXED)
                                    <div class="price-title text-primary">
                                        {{ gs('cur_sym') }}{{ getAmount($plan->interest_amount) }}
                                    </div>
                                    <small>@lang('Interest Amount')</small>
                                @else
                                    <div class="price-title text-primary">
                                        {{ getAmount($plan->interest_amount) }}%
                                    </div>
                                    <small>@lang('Interest Rate')</small>
                                @endif
                            </div>
                            <div class="invest-pricing-info__item">
                                <div class="price-title">{{ __($plan->investTime->name) }}</div>
                                <small>@lang('Interest Period')</small>
                            </div>
                            <div class="invest-pricing-info__item">
                                <div class="price-title">
                                    @if ($plan->return_type == Status::INVEST_REPEAT)
                                        {{ __($plan->repeat_times) }}x
                                    @else
                                        @lang('Lifetime')
                                    @endif
                                </div>
                                <small>@lang('Times')</small>
                            </div>
                        </div>
                        <ul class="investment-list mb-3">
                            <li class="d-flex justify-content-between">
                                <span>@lang('Return Type')</span>
                                <span class="fw-semibold">
                                    {{ $plan->return_type == Status::INVEST_REPEAT ? __('Repeat') : __('Lifetime') }}
                                </span>
                            </li>

                            <li class="d-flex justify-content-between">
                                <span>@lang('Capital Back')</span>
                                <span
                                    class="fw-semibold {{ $plan->capital_back == Status::YES ? 'text--success' : 'text--danger' }}">
                                    {{ $plan->capital_back == Status::YES ? __('Yes') : __('No') }}
                                </span>
                            </li>

                            <li class="d-flex justify-content-between">
                                <span>@lang('Amount Per Interest')</span>
                                @if ($plan->return_type == Status::INVEST_REPEAT)
                                    <span class="fw-semibold amount-per-interest"
                                        data-interest="{{ $plan->interest_amount }}"
                                        data-type="{{ $plan->interest_type }}">
                                        {{ gs('cur_sym') }}0
                                    </span>
                                @else
                                    <span class="fw-semibold amount-per-interest-lifetime"
                                        data-interest="{{ $plan->interest_amount }}"
                                        data-type="{{ $plan->interest_type }}">
                                        {{ gs('cur_sym') }}0
                                    </span>
                                @endif
                            </li>


                            <li class="d-flex justify-content-between pb-0">
                                <span>@lang('Total Return')</span>
                                @if ($plan->return_type == Status::INVEST_REPEAT)
                                    <div class="text-end">
                                        <span class="fw-semibold total-return" data-interest="{{ $plan->interest_amount }}"
                                            data-type="{{ $plan->interest_type }}"
                                            data-repeat="{{ $plan->return_type == Status::INVEST_REPEAT ? $plan->repeat_times : 0 }}">
                                            {{ gs('cur_sym') }}0
                                        </span>
                                        @if ($plan->capital_back)
                                            <br>
                                            <span class="fs-12">@lang('With Capital')</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="fw-semibold">@lang('Unlimited')</span>
                                @endif
                            </li>
                        </ul>
                        <a href="{{ route('user.investment.show', $plan->id) }}"
                            class="btn btn--gray w-100 btn--md invest-now-btn" data-plan-id="{{ $plan->id }}">
                            @lang('Invest Now')
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    @if ($plans->hasPages())
        {{ paginateLinks($plans) }}
    @endif
@endsection

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/jquery-ui.min.js') }}"></script>
@endpush


@push('script')
    <script>
        "use strict";
        (function($) {
            $('.breadcrumb-plugins-wrapper').remove();



            $('.investment-card').each(function() {

                const $card = $(this);
                const fixedAmount = parseFloat($card.data('fixed-amount'));
                const minInvest = parseFloat($card.data('min-invest'));
                const maxInvest = parseFloat($card.data('max-invest'));

                const $slider = $card.find('.slider-range');
                const $selectedSpan = $card.find('.selected_amount');
                const $hiddenInput = $card.find('input[type="hidden"]');



                const middleAmount = Math.round((minInvest + maxInvest) / 2);
                let defaultAmount = fixedAmount > 0 ? fixedAmount : middleAmount;
                let amount = defaultAmount;

                $selectedSpan.text("{{ gs('cur_sym') }}" + defaultAmount.toFixed(2));
                $hiddenInput.val(defaultAmount.toFixed(2));



                $slider.slider({
                    range: "min",
                    min: minInvest,
                    max: maxInvest,
                    value: defaultAmount,
                    slide: fixedAmount > 0 ? null : function(event, ui) {
                        $selectedSpan.text("{{ gs('cur_sym') }}" + ui.value.toFixed(2));
                        $hiddenInput.val(ui.value.toFixed(2));
                        updateAmounts(ui.value, $card);
                    }
                });


                $hiddenInput.on('input', function() {
                    let val = parseFloat($(this).val());
                    if (isNaN(val)) val = minInvest;
                    if (val < minInvest) val = minInvest;
                    if (val > maxInvest) val = maxInvest;

                    $(this).val(val.toFixed(2));
                    $slider.slider('value', val);
                    $selectedSpan.text("{{ gs('cur_sym') }}" + val.toFixed(2));
                    updateAmounts(val);
                });


                if (fixedAmount > 0) {
                    $slider.slider('disable');
                    $slider.find('.ui-slider-range').css('width', '100%');
                    $slider.find('.ui-slider-handle').css('left', '100%');
                    $slider.find('.ui-slider-handle').css('pointer-events', 'none');
                }

                updateAmounts(defaultAmount, $card);

            });

            function updateAmounts(amount, $card) {


                const $totalReturn = $card.find('.total-return');
                const $amountRepeat = $card.find('.amount-per-interest');
                const $amountLifetime = $card.find('.amount-per-interest-lifetime');
                const $investBtn = $card.find('.invest-now-btn');

                const repeat = $totalReturn.length ? parseFloat($totalReturn.data('repeat')) : 0;


                let interestRepeat = $amountRepeat.length ? parseFloat($amountRepeat.data('interest')) : 0;
                let typeRepeat = $amountRepeat.length ? $amountRepeat.data('type') : null;

                let interestLifetime = $amountLifetime.length ? parseFloat($amountLifetime.data('interest')) :
                    0;
                let typeLifetime = $amountLifetime.length ? $amountLifetime.data('type') : null;


                if ($amountRepeat.length) {
                    let perInterest = 0;
                    let total = 0;
                    if (typeRepeat == '{{ Status::INTEREST_TYPE_FIXED }}') {
                        perInterest = interestRepeat;
                        total = perInterest * repeat;
                    } else {
                        perInterest = amount * (interestRepeat / 100);
                        total = perInterest * repeat;
                    }
                    $amountRepeat.text("{{ gs('cur_sym') }}" + perInterest.toFixed(2));
                    if ($totalReturn.length) $totalReturn.text("{{ gs('cur_sym') }}" + total.toFixed(2));
                }


                if ($amountLifetime.length) {
                    let perInterest = 0;
                    if (typeLifetime == '{{ Status::INTEREST_TYPE_PERCENT }}') {
                        perInterest = amount * (interestLifetime / 100);
                    } else {
                        perInterest = amount + interestLifetime;
                    }
                    $amountLifetime.text("{{ gs('cur_sym') }}" + perInterest.toFixed(2));
                }

                if ($investBtn.length) {
                    $investBtn.attr('href', function() {
                        return $(this).attr('href').split('?')[0] + '?invest_amount=' + amount
                            .toFixed(2);
                    });
                }
            }


        })(jQuery);
    </script>
@endpush



@push('style')
    <style>
        .invest-pricing-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .invest-pricing-info__item:first-child {
            grid-column: span 2;
        }

        @media (max-width: 575.98px) {
            .invest-pricing-info {
                grid-template-columns: 1fr;
            }

            .invest-pricing-info__item:first-child {
                grid-column: span 1;
            }
        }

        .invest-pricing-info__item {
            background-color: #F2F2F2;
            padding: 7px 5px;
            border-radius: 6px;
            text-align: center;
        }

        .price-title {
            font-size: 16px;
            font-weight: 700;
        }

        .invest-pricing-info__item small {
            font-weight: 500;
        }

        .ui-state-default {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: block;
            border: none;
            border-radius: 50%;
            background-color: hsl(var(--base));
            outline: none;
            cursor: pointer;
            top: -7px;
            position: absolute;
            z-index: 1;
        }

        .ui-state-default::after {
            position: absolute;
            content: "";
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: hsl(var(--white));
            top: 3px;
            left: 3px;
            display: block;
        }

        .ui-widget.ui-widget-content {
            position: relative;
            height: 18px;
            border: none;
        }

        .ui-widget.ui-widget-content::after {
            position: absolute;
            content: "";
            top: 0;
            left: 0;
            height: 6px;
            background: #F2F2F2;
            width: 100%;
            border-radius: 15px;
        }

        .ui-slider-range {
            height: 6px;
            background: hsl(var(--base)/0.6);
            position: relative;
            z-index: 1;
        }

        #slider-range {
            cursor: pointer;
        }

        #slider-range .ui-corner-all:nth-child(2):not(:last-child) {
            display: none !important;
        }

        .price-range__title {
            font-family: var(--heading-font);
        }

        .range-box {
            border: 1px solid #ddd;
            margin-bottom: 24px;
            display: block;
        }

        .invest-pricing-info__item:first-child {
            background: hsl(var(--info)/0.1);
        }

        .invest-pricing-info__item:nth-child(2) {
            background: hsl(var(--success)/0.1);
        }

        .invest-pricing-info__item:nth-child(3) {
            background: hsl(var(--warning)/0.1);
        }

        @media screen and (max-width: 575px) {

            .investment-list,
            .range-box {
                font-size: 14px;
            }

        }
    </style>
@endpush
