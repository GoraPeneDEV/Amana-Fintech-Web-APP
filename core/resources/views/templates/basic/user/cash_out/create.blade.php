@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center justify-content-xl-start">
        <div class="col-xxl-6 col-xl-8 col-lg-8">
            <h4 class="mb-4">
                <a href="{{ route('user.cash.out.history') }}">
                    <span class="icon" title="@lang('Cash Out History')">
                        <i class="las la-arrow-circle-left"></i>
                    </span>
                    {{ __($pageTitle) }}
                </a>
            </h4>
            <form action="{{ route('user.cash.out.store') }}" method="post"
                class="send-money-form has-otp-form no-submit-loader">
                @csrf
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="form--group form-group">
                            <label class="form--label">@lang('Agent')</label>
                            <div class="input-group style-two">
                                <input type="text" class="form--control form-control agent"
                                    placeholder="@lang('Enter agent username or phone')" name="agent" value="{{ old('agent') }}" required>
                                <span class="input-group-text">
                                    <i class="fa-solid fa-address-book"></i>
                                </span>
                            </div>
                            <div class="send-list">
                                @foreach ($latestCashOuts as $cashOut)
                                    <div class="send-list-item mt-3">
                                        <span class="icon">
                                            <i class="fa-solid fa-user-large"></i>
                                        </span>
                                        <span class="number">{{ @$cashOut->receiverAgent->mobileNumber }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form--group form-group">
                            <div class="d-flex gap-1 justify-content-between  flex-wrap">
                                <label class="form--label">@lang('Enter Amount')</label>
                                <span>
                                    @lang('Available Balance'):
                                    {{ gs('cur_sym') }}{{ showAmount(auth()->user()->balance, currencyFormat: false) }}
                                </span>
                            </div>
                            <div class="input-group input--amount border-0">
                                <input type="number" step="any" class="form--control sm-style form-control"
                                    placeholder="@lang('0.00')" value="{{ old('amount') }}" name="amount" required>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                            <span class="fw-medium mt-2">
                                <span  class=" fw-bold">{{ gs('cur_sym') }}{{ showAmount($cashOutCharge->min_limit, currencyFormat: false) }}
                                    -
                                    {{ gs('cur_sym') }}{{ showAmount($cashOutCharge->max_limit, currencyFormat: false) }}</span>
                            </span>
                            <div class="flex-align gap-2 mt-3">
                                @foreach (gs('quick_amounts') ?? [] as $amount)
                                    <span class="suggest-amount quick-amount" data-amount="{{ getAmount($amount) }}">
                                        {{ gs('cur_sym') }}{{ showAmount($amount, currencyFormat: false) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <x-otp_verification remark="cash_out" />

                    </div>
                </div>
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="deposit-wrapper">
                            <div class="deposit-wrapper-info">
                                <span class="title">
                                    @lang('Processing Charge')
                                    <button type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="@lang('Processing Charge'): {{ gs('cur_sym') }}{{ getAmount($cashOutCharge->fixed_charge) }}+{{ getAmount($cashOutCharge->percent_charge) }}%">
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

            let percentCharge = parseFloat("{{ $cashOutCharge->percent_charge }}" || 0);
            let fixedCharge = parseFloat("{{ $cashOutCharge->fixed_charge }}" || 0);

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

            $(".send-list-item").on("click", function() {
                var number = $(this).find(".number").text().trim();
                $(".agent").val(number);
            });

            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
