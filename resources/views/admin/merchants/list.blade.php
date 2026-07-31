@extends('admin.layouts.app')
@section('panel')
    @include('admin.merchants.widget')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search merchants" filterBoxLocation="merchants.filter">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Merchant')</th>
                            <th>@lang('Email-Mobile')</th>
                            <th>@lang('Country')</th>
                            <th>@lang('Joined At')</th>
                            <th>@lang('Balance')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($merchants as $merchant)
                            <tr>
                                <td>
                                    <x-admin.other.merchant_info :merchant="$merchant" />
                                </td>
                                <td>
                                    <div>
                                        <strong class="d-block">
                                            {{ $merchant->email }}
                                        </strong>
                                        <small>{{ $merchant->mobileNumber }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold" title="{{ @$merchant->country_name }}">
                                            {{ $merchant->country_code }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong class="d-block ">{{ showDateTime($merchant->created_at) }}</strong>
                                        <small class="d-block"> {{ diffForHumans($merchant->created_at) }}</small>
                                    </div>
                                </td>
                                <td>{{ showAmount($merchant->balance) }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        <x-admin.ui.btn.details :href="route('admin.merchants.detail', $merchant->id)" />
                                        @if (request()->routeIs('admin.merchants.kyc.pending'))
                                            <a href="{{ route('admin.merchants.kyc.details', $merchant->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline--dark">
                                                <i class="las la-user-check"></i> @lang('KYC Data')
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($merchants->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($merchants) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>
@endsection

