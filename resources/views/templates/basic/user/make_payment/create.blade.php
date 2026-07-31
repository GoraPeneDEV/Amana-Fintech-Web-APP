@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center justify-content-xl-start">
        <div class="col-xxl-6 col-xl-8 col-lg-8">
            <h4 class="mb-4">
                <a href="{{ route('user.make.payment.history') }}">
                    <span class="icon" title="@lang('Payment History')">
                        <i class="las la-arrow-circle-left"></i>
                    </span>
                    {{ __($pageTitle) }}
                </a>
            </h4>
            <form action="{{ route('user.make.payment.store') }}" method="post"
                class="send-money-form has-otp-form no-submit-loader">
                @csrf
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="form--group form-group">
                            <label class="form--label">@lang('Merchant')</label>
                            <div class="input-group style-two">
                                <input type="text" class="form--control form-control merchant"
                                    placeholder="@lang('Enter merchant username or phone')" name="merchant" value="{{ old('merchant') }}" required>
                                <span class="input-group-text">
                                    <i class="fa-solid fa-address-book"></i>
                                </span>
                            </div>

                            <div class="send-list">
                                @foreach ($latestMakePayments as $latestMakePayment)
                                    <div class="send-list-item mt-3">
                                        <span class="icon">
                                            <i class="fa-solid fa-user-large"></i>
                                        </span>
                                        <span class="number">{{ @$latestMakePayment->merchant->mobileNumber }}</span>
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
                            <div class="flex-align gap-2 mt-3">
                                @foreach (gs('quick_amounts') ?? [] as $amount)
                                    <span class="suggest-amount quick-amount" data-amount="{{ getAmount($amount) }}">
                                        {{ gs('cur_sym') }}{{ showAmount($amount, currencyFormat: false) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <x-otp_verification remark="make_payment" />
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
            $(".send-list-item").on("click", function() {
                var number = $(this).find(".number").text().trim();
                $(".merchant").val(number);
            });

            $(".quick-amount").on('click', function() {
                $("input[name=amount]").val(parseInt($(this).data("amount")));
            });

            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
