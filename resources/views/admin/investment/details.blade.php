@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-lg-6">
            <x-admin.ui.card class="h-100">
                <x-admin.ui.card.header>
                    <h4 class="card-title">@lang('Investment Information') </h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Investment Plan') <span>{{ __(@$investment->plan->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Invested Amount') <span>{{ showAmount($investment->invest_amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Capital Back')
                            @if ($investment->capital_back)
                                <span class="text--success"> @lang('Yes')</span>
                            @else
                                <span class="text--danger">@lang('No')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Investment Time') <span>{{ showDateTime($investment->created_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Trx') <span>{{ $investment->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Status') <span>
                                <span>@php echo $investment->statusBadge  @endphp</span>
                            </span>
                        </li>
                    </ul>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>

        <div class="col-lg-6">
            <x-admin.ui.card class="h-100">
                <x-admin.ui.card.header>
                    <h4 class="card-title">@lang('Interest Information') </h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Per Interest Amount')
                            <span>
                                {{ showAmount($investment->per_interest_amount) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Total Interest Amount')
                            <span>
                                @if ($investment->total_interest_amount == Status::LIFETIME)
                                    @lang('Lifetime  Interest')
                                @else
                                    {{ showAmount($investment->total_interest_amount) }}
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Total Interest Amount Paid')
                            <span>
                                {{ showAmount($investment->total_interest_amount_get) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Total Interest Times')
                            <span>
                                @if ($investment->total_repeat == Status::LIFETIME)
                                    @lang('Lifetime  Interest')
                                @else
                                    {{ __($investment->total_repeat) }} @lang('Times')
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Total Interest Paid')
                            <span>
                                {{ __($investment->total_repeat_get) }} @lang('Times')
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Last Interest At') <span>{{ showDateTime($investment->last_return_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            @lang('Next Interest At') <span>{{ showDateTime($investment->next_return_at) }}</span>
                        </li>
                    </ul>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.header>
                    <h4 class="card-title">@lang('Investment Interest') </h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout :renderTableFilter="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
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
                                            <div class="text-end text-xl-center">
                                                <span>
                                                    {{ showAmount(optional($investmentInterest->transactions)->post_balance) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span>
                                                {{ __(optional($investmentInterest->transactions)->details) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($investmentInterests->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($investmentInterests) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
