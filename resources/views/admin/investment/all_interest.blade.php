@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx" filterBoxLocation="reports.filter_form">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('TRX') | @lang('Investment') </th>
                                    <th class="text-start">@lang('Transacted')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($investmentInterests as $investmentInterest)
                                    <tr>
                                        <td>
                                            <x-admin.other.user_info :user="@$investmentInterest->investment->user" />
                                        </td>

                                        <td>
                                            <div class="text-start">
                                                <span class="d-block">
                                                    {{ $investmentInterest->trx }}
                                                </span>
                                                <span>
                                                    <a
                                                        href="{{ route('admin.investment.details', $investmentInterest->investment_id) }}">
                                                        {{ __(@$investmentInterest->investment->plan->name) }} -
                                                        {{ $investmentInterest->investment->trx }}
                                                    </a>
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
