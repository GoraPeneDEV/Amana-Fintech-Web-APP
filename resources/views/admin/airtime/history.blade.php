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
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Mobile Number')</th>
                                    <th>@lang('Amount') | @lang('Charge')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Details')</th>
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

                                        <td>{{ $trx->operator->name }}</td>
                                        <td>
                                            <span class="d-block">
                                                <span class="me-1">{{ @$trx->dial_code }}</span>
                                                <span>{{ @$trx->mobile_number }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showAmount($trx->amount) }}</span>
                                                <span class="text--success" title="@lang('Charge')">
                                                    {{ showAmount($trx->charge) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ showAmount($trx->post_balance) }}
                                        </td>
                                        <td>{{ __($trx->details) }}</td>
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
