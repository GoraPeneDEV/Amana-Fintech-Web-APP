@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="alert alert--info d-flex" role="alert">
                <div class="alert__icon">
                    <i class="las la-info"></i>
                </div>
                <div class="alert__content">
                    <p class="fw-600">
                        @lang("The API provider's currency code must match the system currency; otherwise, automatic country/operator fetching and the Airtime module may not function correctly.")
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout :renderTableFilter="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="check-all">
                                        <label for="check-all" class="ms-1 mb-0">@lang('Name')</label>
                                    </th>
                                    <th>@lang('ISO')</th>
                                    <th>@lang('Currency Name')</th>
                                    <th>@lang('Currency Code')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @php
                                    $counter = 0;
                                @endphp
                                @foreach ($apiCountries as $item)
                                    @php
                                        $item = (object) $item;
                                    @endphp
                                    @if (!in_array($item->isoName, $existingCountryCodes))
                                        @php
                                            $counter++;
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="countries[]" value="{{ $item->isoName }}"
                                                    id="country-{{ $item->isoName }}" form="confirmation-form"
                                                    class="isoName">
                                                <label for="country-{{ $item->isoName }}"
                                                    class="ms-1 mb-0">{{ $item->name }}</label>
                                            </td>
                                            <td>{{ $item->isoName }}</td>
                                            <td>{{ $item->currencyName }}</td>
                                            <td>{{ $item->currencyCode }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if ($counter == 0)
                                    <tr class="text-center empty-message-row">
                                        <td colspan="100%" class="text-center">
                                            <div class="p-5">
                                                <img src="{{ asset('assets/images/empty_box.png') }}"
                                                    class="empty-message">
                                                <span class="d-block">@lang('No country available')</span>
                                                <span class="d-block fs-13 text-muted">@lang('There are no available data to display on this table at the moment.')</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>

                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <button type="button" class="my-5 btn btn--sm btn-outline--primary d-none confirmationBtn"
                data-question="@lang('Are you sure to add this countries?')" data-action="{{ route('admin.airtime.countries.save') }}"> <i
                    class="lab la-telegram-plane"></i>@lang('Add Selected Countries')</button>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-sm btn--success  confirmationBtn disabled" disabled
        data-question="@lang('Are You sure to add these selected countries?')" data-action="{{ route('admin.utility.bill.company.countries.save') }}">
        <i class="lab la-telegram-plane me-1"></i> @lang('Add Selected Country')
    </button>

    <x-back_btn route="{{ route('admin.airtime.countries') }}" />
@endpush

@push('script')
    <script>
        "use strict";

        (function($) {

            $("#check-all").on('click', function() {
                if ($(this).is(':checked')) {
                    $(".isoName").prop('checked', true);
                } else {
                    $(".isoName").prop('checked', false);
                }
                updateDOM();
            });

            $(".isoName").on('change', function() {
                updateDOM();
            })

            function updateDOM() {
                if ($('.isoName:checked').length > 0) {
                    $('.confirmationBtn').removeClass('disabled').attr('disabled', false);
                } else {
                    $('.confirmationBtn').addClass('disabled').attr('disabled', true);
                }
            }

        })(jQuery);
    </script>
@endpush
