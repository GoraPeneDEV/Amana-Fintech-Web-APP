@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx, username"
                        filterBoxLocation="reports.transaction_filter_form">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th> {{ $userType != 'all' ? ucfirst(__($userType)) : __('User') }}</th>
                                    @if ($userType == 'all')
                                        <th>@lang('User Type')</th>
                                    @endif
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Charge')</th>
                                    <th>@lang('Post Balance') | @lang('Detail')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            @if ($trx->user_id != 0)
                                                <x-admin.other.user_info :user="$trx->user" />
                                            @elseif ($trx->agent_id != 0)
                                                <x-admin.other.agent_info :agent="$trx->agent" />
                                            @elseif ($trx->merchant_id != 0)
                                                <x-admin.other.merchant_info :merchant="$trx->merchant" />
                                            @else
                                                @lang('N/A')
                                            @endif
                                        </td>
                                        @if ($userType == 'all')
                                            <td>
                                                @if ($trx->user_id != 0)
                                                    @lang('User')
                                                @elseif ($trx->agent_id != 0)
                                                    @lang('Agent')
                                                @elseif ($trx->merchant_id != 0)
                                                    @lang('Merchant')
                                                @else
                                                    @lang('N/A')
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                        </td>
                                        <td>
                                            <strong>{{ $trx->trx }}</strong>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                                {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ showAmount($trx->charge) }}
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    {{ showAmount($trx->post_balance) }}
                                                </span>
                                                <span class="fs-14">
                                                    {{ __($trx->details) }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($transactions->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($transactions) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
