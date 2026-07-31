@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout :renderExportButton="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="check-all">
                                        <label for="check-all" class="ms-1 mb-0">@lang('Name')</label>
                                    </th>
                                    <th>@lang('Brand')</th>
                                    <th>@lang('Denomination Type')</th>
                                    <th>@lang('Recipient Amount')</th>
                                    <th>@lang('Sender Amount')</th>
                                    <th>@lang('Exchange Rate')</th>
                                </tr>
                            </x-admin.ui.table.header>

                            <x-admin.ui.table.body>
                                @php $counter = 0; @endphp
                                @foreach ($apiProducts as $item)
                                    @php
                                        $item = (object) $item;
                                        $exists = in_array($item->productId, $existingProductIds ?? []);
                                    @endphp
                                    @if (!$exists)
                                        @php $counter++; @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="products[]" value="{{ $item->productId }}"
                                                    id="product-{{ $item->productId }}" form="confirmation-form"
                                                    class="product-checkbox">
                                                <label for="product-{{ $item->productId }}" class="ms-1 mb-0">
                                                    {{ $item->productName }}
                                                </label>
                                            </td>
                                            <td>{{ $item->brand['brandName'] ?? '-' }}</td>
                                            <td>{{ ucfirst($item->denominationType ?? '-') }}</td>
                                            <td>
                                                @if ((strtoupper($item->denominationType) ?? '') == 'RANGE')
                                                    {{ getAmount($item->minRecipientDenomination) }} -
                                                    {{ getAmount($item->maxRecipientDenomination) }}
                                                    {{ $item->recipientCurrencyCode }}
                                                @else
                                                    @forelse (@$item->fixedRecipientDenominations ?? [] as $fix)
                                                        {{ getAmount($fix) }} {{ $item->recipientCurrencyCode }}
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @empty
                                                        @lang('N/A')
                                                    @endforelse
                                                @endif
                                            </td>
                                            <td>
                                                @if ((strtoupper($item->denominationType) ?? '') == 'RANGE')
                                                    {{ getAmount($item->minSenderDenomination) }} -
                                                    {{ getAmount($item->maxSenderDenomination) }}
                                                    {{ $item->senderCurrencyCode }}
                                                @else
                                                    @forelse (@$item->fixedSenderDenominations ?? [] as $fix)
                                                        {{ getAmount($fix) }} {{ $item->senderCurrencyCode }}
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @empty
                                                        @lang('N/A')
                                                    @endforelse
                                                @endif
                                            </td>
                                            <td>
                                                1 {{ $item->recipientCurrencyCode }} =
                                                {{ getAmount($item->recipientCurrencyToSenderCurrencyExchangeRate) }}
                                                {{ $item->senderCurrencyCode }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if ($counter == 0)
                                    <x-admin.ui.table.empty_message text="No new gift card products available" />
                                @endif
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>


    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn--sm btn-outline--primary  confirmationBtn" disabled
        data-question="@lang('Are you sure you want to add these products?')" data-action="{{ route('admin.gift.card.products.store', $country->id) }}">
        <i class="lab la-telegram-plane"></i> @lang('Add Selected Products')
    </button>
@endpush

@push('script')
    <script>
        "use strict";

        (function($) {
            $("#check-all").on('click', function() {
                $(".product-checkbox").prop('checked', $(this).is(':checked'));
                updateButtonState();
            });

            $(".product-checkbox").on('change', function() {
                updateButtonState();
            });

            function updateButtonState() {
                if ($('.product-checkbox:checked').length > 0) {
                    $('.confirmationBtn').removeClass('disabled d-none').attr('disabled', false);
                } else {
                    $('.confirmationBtn').addClass('disabled d-none').attr('disabled', true);
                }
            }
        })(jQuery);
    </script>
@endpush
