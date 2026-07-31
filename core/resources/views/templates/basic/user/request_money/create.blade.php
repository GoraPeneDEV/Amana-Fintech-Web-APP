@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center justify-content-xl-start">
        <div class="col-xxl-6 col-xl-8 col-lg-8">
            <h4 class="mb-4">
                <a href="{{ route('user.request.money.history') }}">
                    <span class="icon" title="@lang('Request Money History')">
                        <i class="las la-arrow-circle-left"></i>
                    </span>
                    {{ __($pageTitle) }}
                </a>
            </h4>
            <form action="{{ route('user.request.money.store') }}" method="post"
                class="send-money-form has-otp-form no-submit-loader">
                @csrf
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="form--group form-group">
                            <label class="form--label">@lang('To')</label>
                            <div class="input-group style-two">
                                <input type="text" class="form--control form-control user"
                                    placeholder="@lang('Enter username or phone number')" name="user" value="{{ old('user') }}" required>
                                <span class="input-group-text">
                                    <i class="fa-solid fa-address-book"></i>
                                </span>
                            </div>
                            <div class="send-list">
                                @foreach ($latestRequestMoney as $requestMoney)
                                    <div class="send-list-item mt-3">
                                        <span class="icon">
                                            <i class="fa-solid fa-user-large"></i>
                                        </span>
                                        <span class="number">{{ @$requestMoney->requestReceiver->mobile }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form--group form-group">
                            <label class="form--label">@lang('Enter Amount')</label>
                            <div class="input-group input--amount border-0">
                                <input type="number" class="form--control sm-style form-control"
                                    placeholder="@lang('0.00')" value="{{ old('amount') }}" name="amount" required>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>
                        <div class="form--group form-group">
                            <label class="form--label">@lang('Note')</label>
                            <textarea class="form--control" name="note" placeholder="@lang('Enter note')"></textarea>
                        </div>
                        <x-otp_verification remark="request_money" />
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
                $(".user").val(number);
            });

            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
