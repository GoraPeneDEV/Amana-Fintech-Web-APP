@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout :renderTableFilter="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Discount Type')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Expire Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($offers as $offer)
                                    <tr>
                                        <td>{{ $offer->name }} </td>
                                        <td>
                                            @if ($offer->discount_type == 1)
                                                <span class="text--small badge font-weight-normal badge--primary">
                                                    {{ $offer->offerType }}</span>
                                            @else
                                                <span class="text--small badge font-weight-normal badge--dark">
                                                    {{ $offer->offerType }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ getAmount($offer->amount) }}
                                            {{ $offer->discount_type == 1 ? gs()->cur_text : '%' }}
                                        </td>
                                        <td>
                                            <x-admin.other.status_switch :status="$offer->status" :action="route('admin.promotion.offer.status', $offer->id)"
                                                title="Offer" />
                                        </td>
                                        <td>
                                            {{ showDateTime($offer->end_date, 'd M, Y') }} @if (now()->gt($offer->end_date))
                                                <span class="text--danger">(@lang('Expired'))</span>
                                            @endif
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.edit :href="route('admin.promotion.offer.edit', $offer->id)" />
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($offers->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($offers) }}
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
    <x-admin.ui.btn.add :href="route('admin.promotion.offer.create')" />
@endpush
