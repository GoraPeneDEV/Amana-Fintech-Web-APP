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
                        @lang("The API provider's currency code, the company's currency code, and the system currency must all match; otherwise, the automatic import of utility biller companies will not be possible. Please review your system currency code and refer to the company currency code provided below.")
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12">
            <form action="{{ route('admin.utility.bill.company.companies.save', $country->id) }}" method="POST"
                id="biller-form">
                @csrf
                <x-admin.ui.card>
                    <x-admin.ui.card.body :paddingZero=true>
                        <x-admin.ui.table.layout :renderTableFilter="false">
                            <x-admin.ui.table>
                                <x-admin.ui.table.header>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="check-all">
                                        </th>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Category') | @lang('Service Type')</th>
                                        <th>@lang('Transaction Amount')</th>
                                        <th>@lang('Charge')</th>
                                        <th>@lang('Rate')</th>
                                    </tr>
                                </x-admin.ui.table.header>
                                <x-admin.ui.table.body>
                                    @php
                                        $counter = 0;
                                    @endphp
                                    @foreach ($billers as $item)
                                        @if (!in_array($item->id, $existingCompanyNames))
                                            @php
                                                $counter++;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="isoName" name="biller_ids[]"
                                                        value="{{ $item->id ?? '' }}">
                                                </td>
                                                <td>
                                                    {{ $item->name ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    <div>
                                                        <span class="d-block">
                                                            {{ $item->type ?? 'N/A' }}
                                                        </span>
                                                        <span>
                                                            {{ $item->serviceType ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($item->denominationType == 'RANGE')
                                                        {{ showAmount(@$item->minInternationalTransactionAmount ?? 0) }} -
                                                        {{ showAmount($item->maxInternationalTransactionAmount ?? 0) }}
                                                    @else
                                                        <span class="badge badge--primary fix-amount cursor-pointer"
                                                            data-amounts='@json(@$item->internationalFixedAmounts)'>
                                                            @lang('FIXED')
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ showAmount($item->internationalTransactionFee ?? 0) }}
                                                </td>
                                                <td>
                                                    1 {{ __(gs('cur_text')) }} = {{ getAmount($item->fx->rate ?? 0) }}
                                                    {{ $item->countryCode }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if ($counter == 0)
                                        <tr class="text-center empty-message-row">
                                            <td colspan="100" class="text-center">
                                                <div class="p-5">
                                                    <img src="{{ asset('assets/images/empty_box.png') }}"
                                                        class="empty-message">
                                                    <span class="d-block">@lang('No billers available')</span>
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
            </form>
        </div>
    </div>

    <x-admin.ui.modal id="amount-modal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Fixed Amount')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>

            <ul class="list-group list-group-flush"></ul>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn--sm btn-outline--primary confirmationBtn disabled" disabled
            form="biller-form">
            <i class="lab la-telegram-plane me-1"></i>@lang('Add Selected Companies')
        </button>
        <x-back_btn route="{{ route('admin.utility.bill.company.all', $country->id) }}" />
    </div>
@endpush

@push('script')
    <script>
        "use strict";

        (function($) {
            $("#check-all").on('click', function() {
                if ($(this).is(':checked')) {
                    $(".isoName:not(:disabled)").prop('checked', true);
                } else {
                    $(".isoName:not(:disabled)").prop('checked', false);
                }
                updateDOM();
            });

            $(".isoName").on('change', function() {
                updateDOM();
            });

            function updateDOM() {
                if ($('.isoName:checked').length > 0) {
                    $('.confirmationBtn').removeClass('disabled').attr('disabled', false);
                } else {
                    $('.confirmationBtn').addClass('disabled').attr('disabled', true);
                }
            }

            $('.fix-amount').on('click', function(e) {
                const amounts = $(this).data('amounts');

                let html = "";
                $.each(amounts, function(i, amount) {
                    html += `
                    <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                        <span>${amount?.description}</span>
                        <span class="text--primary"> {{ gs('cur_sym') }}${parseFloat(amount?.amount)} </span>
                    </li>
                    `
                });

                $("#amount-modal").find('ul').html(html);
                $("#amount-modal").modal('show');

            });

        })(jQuery);
    </script>
@endpush
