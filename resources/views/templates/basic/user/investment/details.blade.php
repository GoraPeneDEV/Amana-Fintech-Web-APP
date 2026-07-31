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
    <div class="row mb-4 gy-4 justify-content-center">
        <div class="col-lg-6">
            <div class="card custom--card h-100">
                <h5 class="mb-3">@lang('Investment Information')</h5>
                <div class="card-body">
                    <ul class="list-group common-list">
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Investment Plan') <span>{{ __(@$investment->plan->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Invested Amount') <span>{{ showAmount($investment->invest_amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Capital Back')
                            @if ($investment->capital_back)
                                <span class="text--success"> @lang('Yes')</span>
                            @else
                                <span class="text--danger">@lang('No')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Investment Time') <span>{{ showDateTime($investment->created_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Trx') <span>{{ $investment->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Status') <span>
                                <span>@php echo $investment->statusBadge  @endphp</span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card custom--card">
                <h5 class="mb-3">@lang('Interest Information')</h5>
                <div class="card-body">
                    <ul class="list-group common-list">
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Per Interest Amount')
                            <span>
                                {{ showAmount($investment->per_interest_amount) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Total Interest Amount')
                            <span>
                                @if ($investment->total_interest_amount == Status::LIFETIME)
                                    @lang('Lifetime  Interest')
                                @else
                                    {{ showAmount($investment->total_interest_amount) }}
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Total Interest Amount Get')
                            <span>
                                {{ showAmount($investment->total_interest_amount_get) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Total Interest Times')
                            <span>
                                @if ($investment->total_repeat == Status::LIFETIME)
                                    @lang('Lifetime  Interest')
                                @else
                                    {{ __($investment->total_repeat) }} @lang('Times')
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Total Interest Get')
                            <span>
                                {{ __($investment->total_repeat_get) }} @lang('Times')
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Last Interest At') <span>{{ showDateTime($investment->last_return_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Next Interest At') <span>{{ showDateTime($investment->next_return_at) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card custom--card">
            <div class="card-header">
                <h4 class="card-title mb-3">@lang('Investment Interest History')</h4>
                <form class="table-search no-submit-loader">
                    <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                        placeholder="@lang('Search by transactions')">
                    <button class="icon px-3" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="card-body p-0">
                <table class="table table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Trx')</th>
                            <th>@lang('Transacted')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($investmentInterests as $investmentInterest)
                            <tr>
                                <td>
                                    <div class="text-start">
                                        <span>
                                            {{ __($investmentInterest->trx) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-end text-xl-center">
                                        {{ showDateTime($investmentInterest->created_at) }}<br>{{ diffForHumans($investmentInterest->created_at) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-end text-xl-center">
                                        <span class="text--success fw-bold">
                                            + {{ showAmount($investmentInterest->amount) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span>
                                        {{ __($investmentInterest->transactions->details) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            @include('Template::partials.empty_message')
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
