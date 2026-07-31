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
                                    <th>@lang('Brand')</th>
                                    <th>@lang('Denomination Type')</th>
                                    <th>@lang('Recipient Amount')</th>
                                    <th>@lang('Sender Amount')</th>
                                    <th>@lang('Exchange Rate')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            {{ $product->product_name }}
                                        </td>
                                        <td>{{ $product->brand_name }}</td>
                                        <td>{{ ucfirst($product->denomination_type) }}</td>
                                        <td>
                                            @if ($product->denomination_type == 'RANGE')
                                                {{ getAmount($product->min_recipient_denomination) }} -
                                                {{ getAmount($product->max_recipient_denomination) }}
                                                {{ $product->recipient_currency_code }}
                                            @else
                                                @forelse (@$product->fixed_recipient_denominations ?? [] as $fix)
                                                    {{ getAmount($fix) }} {{ $product->recipient_currency_code }}
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @empty
                                                    @lang('N/A')
                                                @endforelse
                                            @endif
                                        </td>
                                        <td>
                                            @if ($product->denomination_type == 'RANGE')
                                                {{ getAmount($product->min_sender_denomination) }} -
                                                {{ getAmount($product->max_sender_denomination) }}
                                                {{ $product->sender_currency_code }}
                                            @else
                                                @forelse (@$product->fixed_sender_denominations?? [] as $fix)
                                                    {{ getAmount($fix) }} {{ $product->sender_currency_code }}
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @empty
                                                    @lang('N/A')
                                                @endforelse
                                            @endif
                                        </td>
                                        <td>
                                            1 {{ $product->recipient_currency_code }} =
                                            {{ getAmount($product->recipient_to_sender_exchange_rate) }}
                                            {{ $product->sender_currency_code }}
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end">

                                                <x-admin.other.status_switch :status="$product->status" :action="route('admin.gift.card.product.status', $product->id)"
                                                    title="Gift Card Product" />
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>

                        @if ($products->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($products) }}
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
    <x-admin.ui.btn.add text="Fetch Cards" :href="route('admin.gift.card.products.fetch', $country->id)" />
@endpush
