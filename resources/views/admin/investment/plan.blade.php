@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search invest plan">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Invest Amount')</th>
                            <th>@lang('Interest')</th>
                            <th>@lang('Time')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($investPlans as $investPlan)
                            <tr>
                                <td>
                                    {{ $investPlan->name }}
                                </td>
                                @if ($investPlan->invest_type == Status::INVEST_TYPE_RANGE)
                                    <td>
                                        {{ showAmount($investPlan->min_invest) }} -
                                        {{ showAmount($investPlan->max_invest) }}
                                    </td>
                                @elseif($investPlan->invest_type == Status::INVEST_TYPE_FIXED)
                                    <td>
                                        {{ showAmount($investPlan->fixed_amount) }}
                                    </td>
                                @endif
                                <td>
                                    @if ($investPlan->interest_type == Status::INTEREST_TYPE_FIXED)
                                        {{ showAmount($investPlan->interest_amount) }}
                                    @elseif ($investPlan->interest_type == Status::INTEREST_TYPE_PERCENT)
                                        {{ getAmount($investPlan->interest_amount) }}%
                                    @endif
                                </td>
                                <td>
                                    <span>
                                        {{ optional($investPlan->investTime)->time }}
                                        @lang('Hours')
                                    </span>
                                </td>
                                <td>
                                    <x-admin.other.status_switch :status="$investPlan->status" :action="route('admin.investment.plan.status', $investPlan->id)" title="invest plan" />
                                </td>
                                <td>
                                    <x-admin.ui.btn.edit class="editBtn" data-resource="{{ json_encode($investPlan) }}" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($investPlans->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($investPlans) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>

    <x-admin.ui.modal id="settingModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title"></h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="{{ route('admin.investment.plan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label">@lang('Name')</label>
                            <div class="input-group input--group">
                                <input class="form-control" type="text" name="name" value="{{ old('name') }}"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label">@lang('Description')</label>
                            <div class="input-group input--group">
                                <input class="form-control" type="text" value="{{ old('description') }}"
                                    name="description" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label">@lang('Investment Type')</label>
                            <select class="form-control select2" data-minimum-results-for-search="-1" name="invest_type"
                                id="investType" required>
                                <option value="{{ Status::INVEST_TYPE_RANGE }}">@lang('Range')</option>
                                <option value="{{ Status::INVEST_TYPE_FIXED }}" @selected(old('invest_type') == Status::INVEST_TYPE_FIXED)>
                                    @lang('Fixed')
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row amount-row">
                    <div class="col-lg-12" id="fixedAmountBox">
                        <div class="form-group">
                            <label class="form-label">@lang('Invest Amount')</label>
                            <div class="input-group input--group">
                                <input type="number" class="form-control" name="fixed_amount"
                                    value="{{ old('fixed_amount') }}" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 " id="minInvestBox">
                        <div class="form-group">
                            <label class="form-label">@lang('Minimum Invest Amount')</label>
                            <div class="input-group input--group">
                                <input type="number" class="form-control" name="min_invest"
                                    value="{{ old('min_invest') }}" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" id="maxInvestBox">
                        <div class="form-group">
                            <label class="form-label">@lang('Maximum Invest Amount')</label>
                            <div class="input-group input--group">
                                <input type="number" class="form-control" name="max_invest"
                                    value="{{ old('max_invest') }}" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Interest Type')</label>
                            <select class="form-control select2" data-minimum-results-for-search="-1" name="interest_type"
                                required>
                                <option value="{{ Status::INTEREST_TYPE_PERCENT }}">@lang('Percentage')</option>
                                <option value="{{ Status::INTEREST_TYPE_FIXED }}" @selected(old('interest_type') == Status::INTEREST_TYPE_FIXED)>
                                    @lang('Fixed')
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label interestTitle">@lang('Interest Amount')</label>
                            <div class="input-group input--group" id="interestInputGroup">
                                <input class="form-control" type="number" name="interest_amount" step="0.01"
                                    min="0" required value="{{ old('interest_amount') }}">
                                <div class="input-group-text" id="interestSuffix">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Time')</label>
                            <select name="time_id" id="time_id" class="form-control select2"
                                data-minimum-results-for-search="-1" required>
                                @foreach ($investTimes as $investTime)
                                    <option value="{{ $investTime->id }}" @selected(old('time_id') == $investTime->id)>
                                        {{ __($investTime->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Return Type')</label>
                            <select class="form-control select2" data-minimum-results-for-search="-1" name="return_type"
                                id="returnType" required>
                                <option value="{{ Status::INVEST_LIFETIME }}">@lang('Lifetime')</option>
                                <option value="{{ Status::INVEST_REPEAT }}" @selected(old('return_type') == Status::INVEST_REPEAT)>
                                    @lang('Repeat')
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row repeat-row">
                    <div class="col-lg-6" id="repeatTime">
                        <div class="form-group">
                            <label>@lang('How Many Times')</label>
                            <span title="@lang('Enter the total number of return cycles applicable to this investment plan.')">
                                <i class="las la-info-circle"></i>
                            </span>
                            <input class="form-control" type="number" name="repeat_times"
                                value="{{ old('repeat_times') }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6" id="capitalBack">
                        <div class="form-group">
                            <label class="form-label">@lang('Capital Back')</label>
                            <span title="@lang('Choose whether the invested capital will be returned to the user after the plan is completed.')">
                                <i class="las la-info-circle"></i>
                            </span>
                            <select class="form-control select2" data-minimum-results-for-search="-1" name="capital_back"
                                required>
                                <option value="{{ Status::NO }}">@lang('No')</option>
                                <option value="{{ Status::YES }}" @selected(Status::YES == old('capital_back'))>@lang('Yes')
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <x-admin.ui.btn.modal />
                </div>
            </form>
        </x-admin.ui.modal.body>

    </x-admin.ui.modal>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-admin.ui.btn.add class="addBtn" />
@endpush

@push('script')
    <script>
        "use strict";

        (function($) {
            $(document).ready(function() {
                const modal = $("#settingModal");

                const fixedAmountBox = $('#fixedAmountBox').clone();
                const minInvestBox = $('#minInvestBox').clone();
                const maxInvestBox = $('#maxInvestBox').clone();
                const repeatTimeBox = $('#repeatTime').clone();
                const capitalBackBox = $('#capitalBack').clone();

                function toggleInvestFields(scope = $(document)) {
                    const investType = scope.find('select[name="invest_type"]').val();

                    if (investType === '1') {
                        scope.find('#minInvestBox, #maxInvestBox').hide().find('input').prop('required', false);
                        if (!scope.find('#fixedAmountBox').length) {
                            scope.find('.amount-row').prepend(fixedAmountBox.clone());
                        }
                        scope.find('#fixedAmountBox').show().find('input').prop('required', true);
                    } else {
                        scope.find('#fixedAmountBox').hide().find('input').prop('required', false);
                        if (!scope.find('#minInvestBox').length) {
                            scope.find('.amount-row').prepend(minInvestBox.clone());
                        }
                        if (!scope.find('#maxInvestBox').length) {
                            scope.find('.amount-row').prepend(maxInvestBox.clone());
                        }
                        scope.find('#minInvestBox, #maxInvestBox').show().find('input').prop('required', true);
                    }
                }

                function toggleReturnFields(scope = $(document)) {
                    const returnType = scope.find('select[name="return_type"]').val();

                    if (returnType === '1') {
                        if (!scope.find('#repeatTime').length) scope.find('.repeat-row').append(repeatTimeBox
                            .clone());
                        if (!scope.find('#capitalBack').length) scope.find('.repeat-row').append(capitalBackBox
                            .clone());
                        scope.find('#repeatTime, #capitalBack').show().find('input, select').prop('required',
                            true);
                    } else {
                        scope.find('#repeatTime, #capitalBack').hide().find('input, select').prop('required',
                            false);
                    }
                }

                function updateInterestSuffix(scope = $(document)) {
                    const type = scope.find('select[name="interest_type"]').val();
                    const suffix = type === '0' ? '%' : "{{ __(gs('cur_text')) }}";

                    const labelText = type === '0' ?
                        "@lang('Interest Percentage')" :
                        "@lang('Interest Amount')";

                    scope.find('.interestTitle').text(labelText);

                    scope.find('#interestSuffix').text(suffix);
                }


                toggleInvestFields();
                toggleReturnFields();
                updateInterestSuffix();

                $('#investType').change(function() {
                    toggleInvestFields($(document));
                });
                $('#returnType').change(function() {
                    toggleReturnFields($(document));
                });
                $('select[name="interest_type"]').change(function() {
                    updateInterestSuffix($(document));
                });

                modal.on('change', 'select[name="invest_type"]', function() {
                    toggleInvestFields(modal);
                });
                modal.on('change', 'select[name="return_type"]', function() {
                    toggleReturnFields(modal);
                });
                modal.on('change', 'select[name="interest_type"]', function() {
                    updateInterestSuffix(modal);
                });


                $('.addBtn').on('click', function() {
                    modal.find('.modal-title').text(`@lang('Add New Investment Plan')`);
                    modal.find('form').attr('action', "{{ route('admin.investment.plan.store') }}");
                    modal.find('form').trigger('reset');
                    modal.find('select[name="invest_type"]').val('0');
                    modal.find('select[name="return_type"]').val('0');
                    toggleInvestFields(modal);
                    toggleReturnFields(modal);
                    updateInterestSuffix(modal);
                    modal.modal('show');
                });

                $('.editBtn').on('click', function() {
                    const resource = $(this).data('resource');
                    modal.find('.modal-title').text(`@lang('Edit Investment Plan')`);
                    const actionUrl = "{{ route('admin.investment.plan.update', ':id') }}".replace(
                        ':id',
                        resource.id);
                    modal.find('form').attr('action', actionUrl);
                    modal.find('input[name="name"]').val(resource.name);
                    modal.find('input[name="description"]').val(resource.description);
                    modal.find('select[name="invest_type"]').val(resource.invest_type).trigger(
                        'change.select2');
                    modal.find('input[name="fixed_amount"]').val(getAmount(resource.fixed_amount));
                    modal.find('input[name="min_invest"]').val(getAmount(resource.min_invest));
                    modal.find('input[name="max_invest"]').val(getAmount(resource.max_invest));
                    modal.find('select[name="interest_type"]').val(resource.interest_type).trigger(
                        'change.select2');
                    modal.find('input[name="interest_amount"]').val(getAmount(resource
                        .interest_amount));
                    modal.find('select[name="time_id"]').val(resource.time_id).trigger(
                        'change.select2');
                    modal.find('select[name="return_type"]').val(resource.return_type).trigger(
                        'change.select2');
                    modal.find('input[name="repeat_times"]').val(resource.repeat_times);
                    modal.find('select[name="capital_back"]').val(resource.capital_back).trigger(
                        'change.select2');

                    toggleInvestFields(modal);
                    toggleReturnFields(modal);
                    updateInterestSuffix(modal);

                    modal.modal('show');
                });

            });
        })(jQuery);
    </script>
@endpush
