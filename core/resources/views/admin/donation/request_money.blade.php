@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.widget.group.dashboard.demo_five :widget=$totals />
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx, username">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Sender')</th>
                                    <th>@lang('Receiver')</th>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Charge')</th>
                                    <th>@lang('Total')</th>
                                    <th>@lang('Sender Post Balance')</th>
                                    <th>@lang('Receiver Post Balance')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            <x-admin.other.user_info :user="$trx->user" />
                                        </td>
                                        <td>
                                            <x-admin.other.user_info :user="$trx->receiverUser" />
                                        </td>
                                        <td>
                                            <a
                                                href="{{ route('admin.report.transaction', ['search' => $trx->trx]) }}">{{ $trx->trx }}</a>
                                        </td>
                                        <td>
                                            {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                        </td>
                                        <td>
                                            {{ showAmount($trx->amount) }}
                                        </td>

                                        <td>
                                            {{ showAmount($trx->charge) }}
                                        </td>

                                        <td>
                                            {{ showAmount($trx->total_amount) }}
                                        </td>

                                        <td>
                                            {{ showAmount($trx->sender_post_balance) }}
                                        </td>
                                        <td>
                                            {{ showAmount($trx->receiver_post_balance) }}
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
