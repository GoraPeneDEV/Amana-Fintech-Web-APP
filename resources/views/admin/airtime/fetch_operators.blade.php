@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout :renderTableFilter="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <th>
                                    <input type="checkbox" id="check-all">
                                    <label for="check-all" class="ms-1 mb-0">@lang('Name')</label>
                                </th>
                                <th>@lang('Bundle')</th>
                                <th>@lang('Data')</th>
                                <th>@lang('Pin')</th>
                                <th>@lang('Local Amount')</th>
                                <th>@lang('Denomination Type')</th>
                                <th>@lang('Commission')</th>
                                <th>@lang('Action')</th>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @php
                                    $counter = 0;
                                @endphp
                                @foreach ($reloadlySupportedOperators as $item)
                                    @php
                                        $item = (object) $item;
                                    @endphp


                                    @if (!in_array($item->operatorId, $existingOperatorIds))
                                        @php
                                            $counter++;
                                            unset($item->id);
                                            unset($item->country);
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="operators[]" class="operatorId"
                                                    value="{{ $item->operatorId }}" id="operator-{{ $item->operatorId }}"
                                                    form="confirmation-form">
                                                <label for="operator-{{ $item->operatorId }}"
                                                    class="ms-1 mb-0">{{ $item->name }}</label>
                                            </td>
                                            <td>@php  echo showBadge($item->bundle) @endphp</td>
                                            <td>@php  echo showBadge($item->data) @endphp</td>
                                            <td>@php  echo showBadge($item->pin) @endphp</td>
                                            <td>@php  echo showBadge($item->supportsLocalAmounts) @endphp</td>
                                            <td>{{ $item->denominationType }}</td>
                                            <td>{{ $item->commission }}%</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline--dark detailBtn"
                                                    data-resource="{{ json_encode($item) }}">
                                                    <i class="las la-eye me-0"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if ($counter == 0)
                                    <tr class="text-center empty-message-row">
                                        <td colspan="100%" class="text-center">
                                            <div class="p-5">
                                                <img src="{{ asset('assets/images/empty_box.png') }}" class="empty-message">
                                                <span class="d-block">{{ __($emptyMessage) }}</span>
                                                <span class="d-block fs-13 text-muted">@lang('No more operators available for this country')</span>
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

    <x-admin.ui.modal id="infoModal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Operator Details')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Name')</span>
                    <span class="name"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Bundle')</span>
                    <span class="bundle"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Data')</span>
                    <span class="data"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Pin')</span>
                    <span class="pin"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Support Local Amount')</span>
                    <span class="supportsLocalAmounts"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Support Geographical Recharge Plans')</span>
                    <span class="supportsGeographicalRechargePlans"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Denomination Type')</span>
                    <span class="denominationType"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Destination Currency Code')</span>
                    <span class="destinationCurrencyCode"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Destination Currency Symbol')</span>
                    <span class="destinationCurrencySymbol"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>
                        @lang('Commission')
                        <i class="las la-info-circle text--info" title="@lang('Commissions (%) are automatically calculated and applied to your account using the formula: Balance - Sales + Commissions in Reloadly.')"></i>
                    </span>
                    <span class="commission"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>
                        @lang('International Discount')
                        <i class="las la-info-circle text--info" title="@lang('These are discounts applied when user are making a top-up to a mobile number registered in any country besides the country your Reloadly account.')"></i>
                    </span>
                    <span class="internationalDiscount"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>
                        @lang('Local Discount')
                        <i class="las la-info-circle text--info" title="@lang('These discounts are applicable to top-ups made to a mobile number that is registered in the same country of origin as your Reloadly account.')"></i>
                    </span>
                    <span class="localDiscount"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>
                        @lang('Most Popular Amount')
                        <i class="las la-info-circle text--info" title="@lang('The most popular international top-up amount for this specific operator.')"></i>
                    </span>
                    <span class="mostPopularAmount"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>
                        @lang('Most Popular Local Amount')
                        <i class="las la-info-circle text--info" title="@lang('The most popular local top-up amount for this specific operator.')"></i>
                    </span>
                    <span class="mostPopularLocalAmount"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>
                        @lang('Minimum Amount')
                        <i class="las la-info-circle text--info" title="@lang('If the denomination type is set to a range and users select different origin number from your Reloadly account, they will need to top up at least the minimum amount specified.')"></i>
                    </span>
                    <span class="minAmount"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Maximum Amount')
                        <i class="las la-info-circle text--info" title="@lang('If the denomination type is set to a range and users select different origin number from your Reloadly account, they can top up the maximum amount specified.')"></i>
                    </span>
                    <span class="maxAmount"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>
                        @lang('Local Minimum Amount')
                        <i class="las la-info-circle text--info" title="@lang('If the denomination type is set to a range and users select the same origin number as your Reloadly account, they will need to top up at least the minimum amount specified.')"></i>
                    </span>

                    <span class="localMinAmount"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Local Max Amount')
                        <i class="las la-info-circle text--info" title="@lang('If the denomination type is set to a range and users select the same origin number as your Reloadly account, they can top up the minimum amount specified.')"></i>
                    </span>
                    <span class="localMaxAmount"></span>
                </li>

                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>
                        @lang('Foreign Exchange Rate')
                        <i class="las la-info-circle text--info" title="@lang('This exchange rate will be applicable while user select different origin number.')"></i>
                    </span>
                    <span class="fx"></span>
                </li>

                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('International Fees')</span>
                    <span class="international_fees"></span>
                </li>

                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('International Fees Percentage')</span>
                    <span class="international_fees_percentage"></span>
                </li>

                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Local Fees')</span>
                    <span class="local_fees"></span>
                </li>

                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Local Fees Percentage')</span>
                    <span class="local_fees_percentage"></span>
                </li>

                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Geographical Recharge Plans')</span>
                    <span class="geographicalRechargePlans"></span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                    <span>@lang('Status')</span>
                    <span class="status"></span>
                </li>
            </ul>

            <div class="amount_descriptions">
                <div class="heading">
                    <h6>@lang('Fixed Amounts')</h6>
                </div>
                <ul class="list-group list-group-flush fixedAmounts"></ul>
            </div>
            <div class="amount_descriptions">
                <div class="heading">
                    <h6>@lang('Local Fixed Amounts')</h6>
                </div>
                <ul class="list-group list-group-flush localFixedAmounts"></ul>
            </div>
            <div class="amount_descriptions">
                <div class="heading">
                    <h6>@lang('Suggested Amounts')</h6>
                </div>
                <ul class="list-group list-group-flush suggestedAmounts"></ul>
            </div>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>


    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-sm btn--success  confirmationBtn disabled me-1" disabled
        data-question="@lang('Are You sure to add this operators?')" data-action="{{ route('admin.airtime.operators.save', $country->iso_name) }}">
        <i class="lab la-telegram-plane me-1"></i>@lang('Add Selected Operators')
    </button>
    <x-back_btn route="{{ route('admin.airtime.countries') }}" />
@endpush

@push('script')
    <script>
        "use strict";

        (function($) {
            $("#check-all").on('click', function() {
                if ($(this).is(':checked')) {
                    $(".operatorId").prop('checked', true);
                } else {
                    $(".operatorId").prop('checked', false);
                }
                updateDOM();
            });

            $(".operatorId").on('change', function() {
                updateDOM();
            })

            function updateDOM() {
                if ($('.operatorId:checked').length > 0) {
                    $('.confirmationBtn').removeClass('disabled').attr('disabled', false);
                } else {
                    $('.confirmationBtn').addClass('disabled').attr('disabled', true);
                }
            }

            $('.detailBtn').on('click', function() {
                let resource = $(this).data('resource');

                let modal = $('#infoModal');
                let senderCur = resource.senderCurrencyCode;
                let destinationCur = resource.destinationCurrencyCode;

                modal.find('.name').text(resource.name);

                modal.find('.bundle').html(showBadge(resource.bundle));
                modal.find('.data').html(showBadge(resource.data));
                modal.find('.pin').html(showBadge(resource.pin));

                modal.find('.supportsLocalAmounts').html(showBadge(resource.supportsLocalAmounts));

                modal.find('.supportsGeographicalRechargePlans').html(showBadge(resource
                    .supportsGeographicalRechargePlans));

                modal.find('.denominationType').text(resource.denominationType);

                modal.find('.destinationCurrencyCode').text(destinationCur);
                modal.find('.destinationCurrencySymbol').text(resource.destinationCurrencySymbol);

                modal.find('.commission').text(`${resource.commission}%`);

                modal.find('.internationalDiscount').text(`${resource.internationalDiscount}%`);
                modal.find('.localDiscount').text(`${resource.localDiscount}%`);
                modal.find('.mostPopularAmount').text(resource.mostPopularAmount ?
                    `${resource.mostPopularAmount} ${senderCur}` : '--');
                modal.find('.mostPopularLocalAmount').text(resource.mostPopularLocalAmount ?
                    `${resource.mostPopularLocalAmount} ${destinationCur}` : '--');

                modal.find('.minAmount').text(resource.minAmount ? `${resource.minAmount} ${senderCur}` : '--');
                modal.find('.maxAmount').text(resource.maxAmount ? `${resource.maxAmount} ${senderCur}` : '--');

                modal.find('.localMinAmount').text(resource.localMinAmount ?
                    `${resource.localMinAmount} ${destinationCur}` : '--');
                modal.find('.localMaxAmount').text(resource.localMaxAmount ?
                    `${resource.localMaxAmount} ${destinationCur}` : '--');

                modal.find('.fx').text(`1 ${senderCur} = ${resource.fx.rate} ${resource.fx.currencyCode}`);

                modal.find('.fixedAmounts').html(showAmountData(resource.fixedAmountsDescriptions, resource
                    .fixedAmounts, senderCur));
                modal.find('.localFixedAmounts').html(showAmountData(resource.localFixedAmountsDescriptions,
                    resource.localFixedAmounts, destinationCur));
                modal.find('.suggestedAmounts').html(showAmountData(resource.suggestedAmountsMap, resource
                    .suggestedAmounts, senderCur));

                modal.find('.international_fees').text(resource.fees.international ?
                    `${resource.fees.international} ${senderCur}` : '--');
                modal.find('.international_fees_percentage').text(resource.fees.internationalPercentage ?
                    `${resource.fees.internationalPercentage}%` : '--');
                modal.find('.local_fees').text(resource.fees.local ?
                    `${resource.fees.local} ${destinationCur}` : '--');
                modal.find('.local_fees_percentage').text(resource.fees.localPercentage ?
                    `${resource.fees.localPercentage}%` : '--');

                modal.find('.geographicalRechargePlans').text(showArrayData(resource
                    .geographicalRechargePlans));
                modal.find('.status').text(resource.status);

                modal.find('.modal-title').text(resource.name);
                modal.modal('show');
            });

            function showAmountData(obj, arr, curText) {
                var html = '';
                if (!jQuery.isEmptyObject(obj)) {
                    html += `<li class="list-group-item px-0 d-flex justify-content-between flex-wrap gap-1">
                            <span>@lang('Amount')</span>
                            <span>@lang('Description')</span>
                        </li>`;

                    $.each(obj, function(key, value) {
                        html += `<li class="list-group-item px-0 d-flex justify-content-between flex-wrap gap-1">
                                <span>${key} ${curText}</span>
                                <span>${value}</span>
                            </li>`;
                    });
                } else if (arr.length > 0) {
                    html +=
                        `<li class="list-group-item px-0"><span>${arr.join(` ${curText}, `)} ${curText}</span></li>`;
                } else {
                    html = '--';
                }

                return html;
            }


            function showArrayData(arr, curText = null) {
                if (arr.length < 1) {

                    return '--';
                }

                var html = arr.join(` ${curText}, `);
                html += ' ' + curText;
                return html;
            }

            function showBadge(status) {
                var cls, badgeText;
                if (status) {
                    cls = 'badge badge--success';
                    badgeText = "@lang('Yes')";

                } else {
                    cls = 'badge badge--danger';
                    badgeText = "@lang('No')";
                }

                return `<span class="${cls}">${badgeText}</span>`;
            }
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        tr.already-exist {
            background-color: #ebebeb;
        }

        .amount_descriptions {
            padding: 10px 0;
            border-top: 1px solid #ebebeb;
        }

        .amount_descriptions:last-child {
            border-bottom: none;
        }
    </style>
@endpush
