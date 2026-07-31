@extends($activeTemplate . 'layouts.agent')
@section('content')
    <div class="row justify-content-center justify-content-xl-start">
        <div class="col-12">
            <h4 class="mb-4">
                <a href="{{ route('agent.withdraw.account.setting') }}">
                    <span class="icon" title="@lang('Back to Setting')">
                        <i class="las la-arrow-circle-left"></i>
                    </span>
                    {{ __($pageTitle) }}
                </a>
            </h4>
        </div>
        <div class="col-xxl-6 col-xl-8 col-lg-8">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h4>@lang('Save New Account')</h4>
                    <hr>
                    <form method="POST" action="{{ route('agent.withdraw.account.save.data') }}"
                        class="withdraw-setting-form">
                        @csrf
                        <input type="hidden" name="method_id" value="{{ @$withdrawMethod->id }}">
                        <div class="form-group">
                            <label class="form--label">@lang('Unique Name')</label>
                            <input type="text" name="name" class="form--control" value="{{ old('name') }}" required>
                            <small class="fs-13">
                                <i>@lang('Set a unique identifier to recognize your account during withdrawals.')</i>
                            </small>
                        </div>
                        <x-ovo-form identifier="id" identifierValue="{{ @$withdrawMethod->form_id }}" :noFileType="true" />
                        <button type="submit" class="btn btn--grbtn w-100">
                            <i class="fa fa-paper-plane"></i> @lang('Save')
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xxl-6 col-xl-8 col-lg-8">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <h4>@lang('Saved Account')</h4>
                    <hr>
                    <ul class="list-group list-group-flush mb-4">
                        @forelse ($savedAccounts as $savedAccount)
                            <li class="list-group-item d-flex gap-2 flex-wrap justify-content-between ps-0">
                                <span class="d-flex gap-2 flex-wrap pe-5 pe-md-0">
                                    <span class="section-bg notification-icon d-none d-md-block text-center">
                                        <i class="las la-info-circle"></i>
                                    </span>
                                    <span>
                                        <span class="fs-18">
                                            {{ __(@$savedAccount->name) }}
                                        </span>
                                        <span class="d-block fs-14">@lang('Last updated : ')
                                            {{ showDateTime($savedAccount->updated_at, 'd M Y') }}</span>
                                    </span>
                                </span>
                                <span>
                                    <a href="{{ route('agent.withdraw.account.edit', $savedAccount->id) }}"
                                        class="btn btn--success btn--sm"><i class="las la-edit"></i>@lang('Edit')
                                    </a>
                                    <button class="btn btn--danger btn--sm confirmationBtn"
                                        data-question="@lang('Are you sure to delete this save account?')"
                                        data-action="{{ route('agent.withdraw.account.delete', $savedAccount->id) }}"><i
                                            class="las la-trash"></i>@lang('Delete')
                                    </button>
                                </span>
                            </li>
                        @empty
                            @include('Template::partials.empty_message')
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal :isFrontend="true" />
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
