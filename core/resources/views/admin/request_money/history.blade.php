@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx, username">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Sender')</th>
                                    <th class="text-start">@lang('Receiver')</th>
                                    <th>@lang('Trx') | @lang('Time')</th>
                                    <th>@lang('Amount') | @lang('Charge')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Note')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($requestMoneys as $trx)
                                    <tr>
                                        <td>
                                            <x-admin.other.user_info :user="$trx->requestSender" />
                                        </td>
                                        <td>
                                            <x-admin.other.user_info :user="$trx->requestReceiver" />
                                        </td>
                                        <td>
                                            <div>
                                                <span class="text--primary">{{ $trx->trx }}</span><br>
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
                                            @php echo $trx->requestMoneyStatus @endphp
                                        </td>
                                        <td>
                                            {{ __($trx->note ?? '-') }}
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($requestMoneys->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($requestMoneys) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
