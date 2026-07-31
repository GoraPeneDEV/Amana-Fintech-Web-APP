@php
    $investAmount = request()->get('invest_amount', '');
@endphp
@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center justify-content-xl-start">
        <div class="col-xxl-6 col-xl-8 col-lg-8">
            <form action="{{ route('user.investment.store') }}" method="POST"
                class="purchase-form no-submit-loader has-otp-form">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="justify-content-start">
                                <label class="single-operator flex-column justify-content-start align-items-start gap-2">
                                    <h4>{{ __($plan->name) }}</h4>
                                    <span class="d-block">{{ __(@$plan->investTime->name) }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="form--group form-group mb-3">
                            <label class="form--label">@lang('Amount')</label>
                            <div class="input-group input--amount">
                                <input type="number" class="form--control form-control" placeholder="@lang('0.00')"
                                    name="invest_amount" value="{{ $investAmount }}"
                                    @if ($plan->invest_type == Status::INVEST_TYPE_FIXED) readonly @endif required>
                                <span class="input-group-text">{{ gs('cur_text') }}</span>
                            </div>
                            @if ($plan->invest_type == Status::INVEST_TYPE_RANGE)
                                <div class="d-flex justify-content-between gap-2 flex-wrap mt-2">
                                    <span class="fw-medium">
                                        @lang('Range'):
                                        <span class="text--base fw-bold">
                                            {{ showAmount($plan->min_invest) }}
                                            -
                                            {{ showAmount($plan->max_invest) }}
                                        </span>
                                    </span>
                                    <span>
                                        @lang('Available Balance'):
                                        <span class="text--base fw-bold">
                                            {{ showAmount(auth()->user()->balance) }}
                                        </span>
                                    </span>
                                </div>
                            @else
                                <div class="mt-2">
                                    <span>
                                        @lang('Available Balance'):
                                        <span class="text--base fw-bold">
                                            {{ showAmount(auth()->user()->balance) }}
                                        </span>
                                    </span>
                                </div>
                            @endif

                        </div>
                        <x-otp_verification remark="investment" />
                    </div>
                </div>
                <button type="submit" class="btn btn--base text-start w-100 confirm-btn">
                    <span class="flex-between">
                        @lang('Confirm')
                        <span class="icon">
                            <i class="fas fa-arrow-right-long"></i>
                        </span>
                    </span>
                </button>
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn--base btn--md" href="{{ route('user.investment.all') }}">
        <span class="icon">
            <i class="las la-arrow-circle-left"></i>
        </span>
        @lang('Back')
    </a>
@endpush
