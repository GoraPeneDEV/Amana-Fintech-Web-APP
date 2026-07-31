@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout>
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Trx') | @lang('Time')</th>
                                    <th>@lang('Product')</th>
                                    <th>@lang('Recipient')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Discount') | @lang('Charge')</th>
                                    <th>@lang('Total')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            <x-admin.other.user_info :user="$trx->user" />
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

                                        <td>{{ $trx->giftCard->product_name }}</td>
                                        <td>{{ $trx->recipient_email }}</td>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    {{ getAmount(@$trx->rate * $trx->amount) }} {{@$trx->giftCard->sender_currency_code}}
                                                    ({{ getAmount($trx->amount) }} {{@$trx->giftCard->recipient_currency_code}})
                                                </span>
                                                <span title="@lang('Quantity')">
                                                    @lang('Quantity: '){{ $trx->quantity }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showAmount($trx->discount) }}</span>
                                            <span class="text--success" title="@lang('Charge')">
                                                {{ showAmount($trx->charge) }}</span>
                                        </td>
                                        <td>
                                            {{ showAmount($trx->total) }}
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
