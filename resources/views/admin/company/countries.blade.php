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

                                    <th>@lang('Total Companies')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($countries as $country)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img class="country-image" src="{{ $country->flag_url }}">
                                                <span>
                                                    <span class="d-block">{{ __($country->name) }}</span>
                                                    <small class="fs-12"> {{ $country->iso_name }}</small>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $country->companies_count }}
                                        </td>
                                        <td>
                                            <x-admin.other.status_switch :status="$country->status" :action="route('admin.airtime.country.status', $country->id)"
                                                title="Country" />
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.utility.bill.company.all', $country->id) }}"
                                                class = "btn  btn-outline--primary">
                                                <i class="las la-list me-1"></i>@lang('Companies')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($countries->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($countries) }}
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
    <x-admin.ui.btn.add text="Fetch More Country" :href="route('admin.utility.bill.company.fetch.countries')" />
@endpush



@push('style')
    <style>
        .country-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
@endpush
