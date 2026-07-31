@extends($activeTemplate . 'layouts.merchant')
@section('content')
    <div class="row justify-content-center justify-content-xl-start">
        <div class="col-12">
            <h4 class="mb-4">
                <a href="{{ route('merchant.withdraw.account.save', $withdrawMethod->id) }}">
                    <span class="icon" title="@lang('Back to Save List')">
                        <i class="las la-arrow-circle-left"></i>
                    </span>
                    {{ __(@$pageTitle) }}
                </a>
            </h4>
        </div>
        <div class="col-xxl-6 col-xl-8 col-lg-8">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <form method="POST" action="{{ route('merchant.withdraw.account.save.data', @$accountData->id) }}"
                        class="withdraw-setting-form">
                        @csrf
                        <input type="hidden" name="method_id" value="{{ @$withdrawMethod->id }}">
                        <div class="form-group">
                            <label class="form--label">@lang('Account Name')</label>
                            <input type="text" name="name" class="form--control"
                                value="{{ old('name', @$accountData->name) }}" placeholder="@lang('Enter a unique name')" required>
                        </div>
                        <x-ovo-form identifier="id" identifierValue="{{ @$withdrawMethod->form_id }}" :filledData="@$accountData?->data"
                            :noFileType="true" />
                        <button type="submit" class="btn btn--base w-100">
                            <i class="fa fa-paper-plane"></i> @lang('Update')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            var $form = $('.withdraw-setting-form');
            var $submitBtn = $form.find("button[type='submit']");
            var $allInputs = $form.find('input, select, textarea');

            var $realInputs = $allInputs.filter(function() {
                var $el = $(this);
                var name = $el.attr('name');
                var type = $el.attr('type');

                return (
                    name !== '_token' &&
                    type !== 'hidden' &&
                    $el.is(':visible') &&
                    !$el.is(':disabled')
                );
            });
            if ($realInputs.length <= 1) {
                $submitBtn.prop('disabled', true).addClass('disabled');
            }

            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
