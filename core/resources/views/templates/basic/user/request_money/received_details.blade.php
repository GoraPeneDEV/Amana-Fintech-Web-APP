@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('user.request.money.received.history') }}">
            <span class="icon" title="@lang('Received Requests History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __($pageTitle) }}
        </a>
    </h4>
    <div class="row">
        <div class="col-lg-6">
            <form action="{{ route('user.request.money.received.store', $requestMoney->id) }}" method="post"
                class="send-money-form has-otp-form no-submit-loader">
                @csrf
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="form--group">
                            <label class="form--label">@lang('To')</label>
                            <div class="input-group style-two">
                                <input type="text" class="form--control form-control user"
                                    placeholder="@lang('Enter username or phone number')" name="user"
                                    value="{{ @$requestMoney->requestSender->mobile ?? @$requestMoney->requestReceiver->username }}"
                                    disabled>
                                <span class="input-group-text">
                                    <i class="fa-solid fa-address-book"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form--group">
                            <label class="form--label">@lang('Enter Amount')</label>
                            <div class="input-group input--amount border-0">
                                <input type="text" class="form--control sm-style form-control"
                                    placeholder="@lang('0.00')" value="{{ getAmount($requestMoney->amount) }}"
                                    name="amount" disabled>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                            <span class="fw-medium mt-2">
                                @lang('Send Money Limit:')
                                <span
                                    class="text--base fw-bold">{{ gs('cur_sym') }}{{ showAmount($requestMoneyCharge->min_limit, currencyFormat: false) }}
                                    -
                                    {{ gs('cur_sym') }}{{ showAmount($requestMoneyCharge->max_limit, currencyFormat: false) }}</span>
                            </span>
                        </div>
                        <x-otp_verification remark="request_money_received" />

                    </div>
                </div>
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="deposit-wrapper">
                            <div class="deposit-wrapper-info">
                                <span class="title">
                                    @lang('Processing Charge')

                                    <button type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="@lang('Processing Charge'): {{ gs('cur_sym') }}{{ getAmount($requestMoneyCharge->fixed_charge) }}+{{ getAmount($requestMoneyCharge->percent_charge) }}%">
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
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn--base text-start w-100">
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
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            let percentCharge = parseFloat("{{ $requestMoneyCharge->percent_charge }}" || 0);
            let fixedCharge = parseFloat("{{ $requestMoneyCharge->fixed_charge }}" || 0);

            calculation();


            $("input[name=amount]").on('input', function() {
                calculation();
            });
            $(".quick-amount").on('click', function() {
                $("input[name=amount]").val(parseInt($(this).data("amount")));
                calculation();
            });

            function calculation() {
                const amount = parseFloat($('body').find(`input[name="amount"]`).val() || 0);
                const totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                const totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                const totalAmount = parseFloat(amount + totalPercentCharge + fixedCharge);
                $(".processing-fee").text(totalCharge.toFixed(2));
                $(".final-amount").text(totalAmount.toFixed(2));
            }

            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
