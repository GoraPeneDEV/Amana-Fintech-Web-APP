@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            @include('admin.donation.widget')
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx, username">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Donation for')</th>
                                    <th>@lang('Trx') | @lang('Time')</th>
                                    <th>@lang('Amount') | @lang('Post Balance')</th>
                                    <th>@lang('Identity') | @lang('Reference')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            <x-admin.other.user_info :user="$trx->user" />
                                        </td>
                                        <td>
                                            {{ $trx->donationFor->name }}
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
                                                <span>
                                                    {{ showAmount($trx->post_balance) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    @if ($trx->hide_identity == Status::YES)
                                                        <span class="badge badge--warning">@lang('Hide Identity')</span>
                                                    @else
                                                        <span class="badge badge--success">@lang('Visible Identity')</span>
                                                    @endif
                                                </span>
                                                <span>{{ $trx->reference ?? __('N/A') }}</span>
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
