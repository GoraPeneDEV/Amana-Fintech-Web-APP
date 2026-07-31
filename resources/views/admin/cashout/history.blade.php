@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            @include('admin.cashout.widget')
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx, username">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th class="text-start">@lang('Agent')</th>
                                    <th>@lang('Trx') | @lang('Time')</th>
                                    <th>@lang('Amount') | @lang('Charge')</th>
                                    <th>@lang('Total Amount') | @lang('Agent Commission')</th>
                                    <th>@lang('User & Agent Post Balance')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            <x-admin.other.user_info :user="$trx->user" />
                                        </td>
                                        <td>
                                            <x-admin.other.agent_info :agent="$trx->receiverAgent" />
                                        </td>
                                        <td>
                                            <div>
                                                <a class="d-block"
                                                    href="{{ route('admin.report.transaction', ['search' => $trx->trx]) }}">
                                                    {{ $trx->trx }}
                                                </a>
                                                <span title="{{ diffForHumans($trx->created_at) }}">
                                                    {{ showDateTime($trx->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showAmount($trx->amount) }}</span>
                                                <span class="text--success" title="@lang('Charge')">
                                                    {{ showAmount($trx->charge) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showAmount($trx->total_amount) }}</span>
                                                <span class="text--warning" title="@lang('Agent Commission')">
                                                    {{ showAmount($trx->commission) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="text--danger d-block" title="@lang('User Post Balance')">
                                                    {{ showAmount($trx->user_post_balance) }}</span>
                                                <span class="text--success" title="@lang('Agent Post Balance')">
                                                    {{ showAmount($trx->agent_post_balance) }}
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
