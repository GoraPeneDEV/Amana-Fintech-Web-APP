@extends($activeTemplate . 'layouts.agent')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('agent.cash.in.history') }}">
            <span class="icon" title="@lang('Cash In History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __($pageTitle) }}
        </a>
    </h4>
    <div class="row">
        <div class="col-lg-6">
            <form action="{{ route('agent.cash.in.store') }}" method="post"
                class="send-money-form cash-in-form no-submit-loader">
                @csrf
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <input type="hidden" name="remark" value="cash_in">
                        <div class="form--group">
                            <label class="form--label">@lang('To')</label>
                            <div class="input-group style-two">
                                <input type="text" class="form--control form-control user"
                                    placeholder="@lang('Enter username or phone number')" name="user" value="{{ old('user') }}" required>
                                <span class="input-group-text">
                                    <i class="fa-solid fa-address-book"></i>
                                </span>
                            </div>
                            <div class="send-list">
                                @foreach ($latestCashIn as $cashIn)
                                    <div class="send-list-item mt-3">
                                        <span class="icon">
                                            <i class="fa-solid fa-user-large"></i>
                                        </span>
                                        <span class="number">{{ @$cashIn->user->mobile }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form--group">
                            <label class="form--label">@lang('Enter Amount')</label>
                            <div class="input-group input--amount border-0">
                                <input type="number" class="form--control sm-style form-control"
                                    placeholder="@lang('0.00')" value="{{ old('amount') }}" name="amount" required>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                            <span class="fw-medium mt-2 d-flex justify-content-between flex-wrap gap-2">
                                <span>
                                    @lang('Limit:')
                                    <span>{{ gs('cur_sym') }}{{ showAmount($cashInCharge->min_limit, currencyFormat: false) }}
                                        -
                                        {{ gs('cur_sym') }}{{ showAmount($cashInCharge->max_limit, currencyFormat: false) }}</span>
                                </span>
                                <span>
                                    @lang('Available Balance'):
                                    {{ gs('cur_sym') }}{{ showAmount(auth('agent')->user()->balance, currencyFormat: false) }}
                                </span>
                            </span>
                            <div class="flex-align gap-2 mt-3">
                                @foreach (gs('quick_amounts') ?? [] as $amount)
                                    <span class="suggest-amount quick-amount" data-amount="{{ getAmount($amount) }}">
                                        {{ gs('cur_sym') }}{{ showAmount($amount, currencyFormat: false) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="form--group">
                            <label class="form--label">@lang('PIN')</label>
                            <div class="input-group style-two">
                                <input type="password" class="form--control form-control" placeholder="@lang('Enter PIN')"
                                    name="pin" required>
                                <span class="input-group-text">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn--base text-start w-100">
                    <span class="flex-between">
                        @lang('Confirm Now')
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

            const $loader = $('.full-page-loader');

            $(".quick-amount").on('click', function() {
                $("input[name=amount]").val(parseInt($(this).data("amount")));
            });

            $(".send-list-item").on("click", function() {
                var number = $(this).find(".number").text().trim();
                $(".user").val(number);
            });

            $('.breadcrumb-plugins-wrapper').remove();

            $('body').on('submit', ".cash-in-form", function(e) {
                e.preventDefault();

                const formData = new FormData($(this)[0]);
                const action = $(this).attr('action');
                const $this = $(this);
          
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
                        }, 1000);
                    },
                    success: function(response) {
                        setTimeout(() => {
                            if (response.status == 'success') {
                                notify('success', response.message);
                                setTimeout(() => {
                                    if (response.data.redirect_type == 'new_url') {
                                        window.location.href = response.data.redirect_url;
                                    } else {
                                        window.location.reload();
                                    }
                                }, 1000);
                            } else {
                                notify('error', response.message || "@lang('Something went wrong')");

                            }
                        }, 1000);
                    }
                });
            });


        })(jQuery);
    </script>
@endpush
