@extends('admin.layouts.app')
@section('panel')
    <form action="{{ route('admin.bank.transfer.charge.setting.update') }}" method="POST" enctype="multipart/form-data"
        id="charge-form">
        @csrf
        <div class="row  gy-4">
            <div class="col-xxl-4 col-sm-6">
                <x-admin.ui.card class="h-100">
                    <x-admin.ui.card.header>
                        <h4 class="card-title">@lang('Range')</h4>
                    </x-admin.ui.card.header>
                    <x-admin.ui.card.body>
                        <div class="form-group">
                            <label class="form-label">@lang('Minimum Amount')</label>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control minAmount" name="min_limit"
                                    value="{{ getAmount($charge->min_limit) }}" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">@lang('Maximum Amount')</label>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control maxAmount" name="max_limit"
                                    value="{{ getAmount($charge->max_limit) }}" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                            <span class="max-amount-error-message text--danger"></span>
                        </div>
                    </x-admin.ui.card.body>
                </x-admin.ui.card>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <x-admin.ui.card class="h-100">
                    <x-admin.ui.card.header>
                        <h4 class="card-title">@lang('Charge')</h4>
                    </x-admin.ui.card.header>
                    <x-admin.ui.card.body>
                        <div class="form-group">
                            <label class="form-label">@lang('Fixed Charge')</label>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control" name="fixed_charge"
                                    value="{{ getAmount($charge->fixed_charge) }}" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Percent Charge')</label>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control" name="percent_charge"
                                    value="{{ getAmount($charge->percent_charge) }}" required>
                                <div class="input-group-text">%</div>
                            </div>
                        </div>
                    </x-admin.ui.card.body>
                </x-admin.ui.card>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <x-admin.ui.card class="h-100">
                    <x-admin.ui.card.header>
                        <h4 class="card-title">@lang('Daily Limit')</h4>
                    </x-admin.ui.card.header>
                    <x-admin.ui.card.body>
                        <div class="form-group">
                            <label class="form-label">
                                @lang('Daily Limit')
                            </label>
                            <span title="@lang('Put -1 if you don\'t want a limit')">
                                <i class="las la-info-circle"></i>
                            </span>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control" name="daily_limit"
                                    value="{{ getAmount($charge->daily_limit) }}" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                    </x-admin.ui.card.body>
                </x-admin.ui.card>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <x-admin.ui.card class="h-100">
                    <x-admin.ui.card.header>
                        <h4 class="card-title">@lang('Monthly Limit')</h4>
                    </x-admin.ui.card.header>
                    <x-admin.ui.card.body>
                        <div class="form-group">
                            <label class="form-label">
                                @lang('Monthly Limit')
                            </label>
                            <span title="@lang('Put -1 if you don\'t want a limit')">
                                <i class="las la-info-circle"></i>
                            </span>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control" name="monthly_limit"
                                    value="{{ getAmount($charge->monthly_limit) }}" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                    </x-admin.ui.card.body>
                </x-admin.ui.card>
            </div>
            <div class="col-xxl-4 col-sm-6">
                <x-admin.ui.card class="h-100">
                    <x-admin.ui.card.header>
                        <h4 class="card-title">@lang('Charge Cap')</h4>
                    </x-admin.ui.card.header>
                    <x-admin.ui.card.body>
                        <div class="form-group">
                            <label class="form-label">
                                @lang('Maximum Charge Cap')
                            </label>
                            <span title="@lang('Put -1 if you don\'t want a charge cap')">
                                <i class="las la-info-circle"></i>
                            </span>
                            <div class="input-group input--group">
                                <input type="number" step="any" class="form-control" name="cap"
                                    value="{{ getAmount($charge->cap) }}" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                    </x-admin.ui.card.body>
                </x-admin.ui.card>
            </div>
        </div>
    </form>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex gap-2">
        <x-back_btn route="{{ route('admin.dashboard') }}" />
        <button type="submit" class="btn btn--primary " form="charge-form">
            <span class="me-1"><i class="fa-regular fa-paper-plane"></i></span>
            @lang('Save Changes')
        </button>
    </div>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $("form").on('submit', function(e) {
                let $this = $(this);
                let $submitBtn = $('body').find(`button[type=submit]`);
                let oldHtml = $submitBtn.html();

                $submitBtn.addClass('disabled').attr("disabled", true).html(`
            <div class="button-loader d-flex gap-2 flex-wrap align-items-center justify-content-center">
                <div class="spinner-border"></div><span>Loading...</span>
            </div>
        `);
            });
        })(jQuery);
    </script>
@endpush
