@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout :renderExportButton="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Country Code')</th>
                                    <th>@lang('Country Name')</th>
                                    <th>@lang('Bill Type')</th>
                                    <th>@lang('Service Type')</th>
                                    <th>@lang('Local Currency Symbol')</th>
                                    <th>@lang('Minimum Trx Amount')</th>
                                    <th>@lang('Maximum Trx Amount')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($billers as $biller)
                                    <tr>
                                        <td>
                                            <div>
                                                {{ __($biller->name) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $biller->country_code }}</div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ __($biller->country_name) }}
                                            </div>
                                        </td>
                                        <td>{{ $biller->type }}</td>
                                        <td>
                                            {{ $biller->service_type }}
                                        </td>
                                        <td>
                                            {{ $biller->local_transaction_currency_code }}
                                        </td>
                                        <td>
                                            {{ $biller->min_local_transaction_amount }}
                                        </td>
                                        <td>
                                            {{ $biller->max_local_transaction_amount }}
                                        </td>
                                        <td>
                                            <x-admin.other.status_switch :status="$biller->status" :action="route('admin.utility.bill.biller.status', $biller->id)"
                                                title="Biller" />
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($billers->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($billers) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-admin.ui.btn.add text="Fetch More Billers" :href="route('admin.utility.bill.fetch.billers')" />
@endpush
