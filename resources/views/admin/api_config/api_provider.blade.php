@extends('admin.layouts.app')
@section('panel')
    <div class="row responsive-row">
        @foreach ($providers as $k => $provider)
            <div class="col-xl-4 col-xxl-3 col-sm-6 gateway-col">
                <x-admin.ui.card>
                    <x-admin.ui.card.body>
                        <div class="text-center mb-3">
                            <div class="thumb mb-2">
                                <img src="{{ getImage(getFilePath('api') . '/' . $provider->provider . '.png') }}"
                                    class="thumb-img">
                            </div>
                            <h4 class="ms-2 fs-18 mb-2">{{ __($provider->title) }}</h4>
                            <p class="mb-2">
                                {{ __($provider->description) }}
                            </p>
                            <p>
                                @lang('Currently Integrated') :
                                @foreach ($provider->module as $module)
                                    <span class="fw-bold">{{ __(keyToTitle($module)) }}</span>
                                    @if ($loop->last)
                                        ,
                                    @else
                                        @lang('and')
                                    @endif
                                @endforeach
                            </p>
                        </div>
                        <div class="d-flex gap-2  flex-wrap justify-content-center align-items-center">
                            <button type="button" class="btn  btn-outline--primary configure-btn"
                                data-provider='@json($provider)'>
                                <span class=" btn--icon"><i class="la la-tools"></i></span>@lang('Configure')
                            </button>
                        </div>
                    </x-admin.ui.card.body>
                </x-admin.ui.card>
            </div>
        @endforeach
    </div>

    <x-admin.ui.modal id="configure-modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">
                @lang('Configure')
                <span class="provider-name"></span>
            </h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <ul class="nav nav-pills custom--tab mb-3 configure-tab" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="credentials-tab" data-bs-toggle="pill" data-bs-target="#pills-home"
                        type="button" role="tab">
                        @lang('Credentials')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="instruction-tab" data-bs-toggle="pill" data-bs-target="#instruction"
                        type="button" role="tab">@lang('Instruction')
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="credentials-tab">
                    <form method="POST" class="config-modal no-submit-loader"
                        action="{{ route('admin.api.setting.update', $provider->id) }}">
                        @csrf
                        <div class="config-html"></div>
                        <div class="mb-3">
                            <x-admin.ui.btn.submit />
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="instruction" role="tabpanel" aria-labelledby="instruction-tab">
                    <div class="instruction ps-2 py-3">

                    </div>
                </div>
            </div>

        </x-admin.ui.modal.body>
    </x-admin.ui.modal>

    <x-confirmation-modal />
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {

            const $modal = $("#configure-modal");

            $(".configure-btn").on('click', function() {
                const provider = $(this).data('provider');
                const configList = provider.config

                let html = ``;

                $.each(configList, function(i, config) {
                    let isRequired = config.is_required ? 'required' : '';
                    let htmlField = '';

                    if (config.name === 'environment') {
                        htmlField = `
                            <div class="form-group">
                                <label class="form-label ${isRequired}">Environment</label>
                                <select class="form-control form-select" name="${config.name}" ${isRequired}>
                                    <option value="sandbox" ${config.value == 'sandbox' ? 'selected' : ''}>SandBox</option>
                                    <option value="production" ${config.value == 'production' ? 'selected' : ''}>Production</option>
                                </select>
                            </div>`;
                    } else if (config.is_copy) {
                        htmlField = `
                            <div class="form-group">
                                <label class="form-label ${isRequired}">${config.title}</label>
                                <div class="input-group input--group">
                                    <input type="text" class="form-control" name="${config.name}" value="${config.value}" ${isRequired}>
                                    <span class="input-group-text cursor-pointer copyBtn" data-copy="${config.value}">
                                        <i class="fas fa-copy me-1"></i>@lang('Copy')
                                    </span>
                                </div>
                            </div>`;
                    } else {
                        htmlField = `
                            <div class="form-group">
                                <label class="form-label ${isRequired}">${config.title}</label>
                                <input type="text" class="form-control" name="${config.name}" value="${config.value}" ${isRequired}>
                            </div>`;
                    }
                    html += htmlField;
                });

                html += `<h6>Module Enable</h6>`;

                $.each(provider.status, function(indexInArray, status) {
                    html += `
                        <div class="form-group">
                            <label class="form-label required">${toTitleCase(indexInArray)}</label>
                            <select class="form-control" name="status[${indexInArray}]">
                                <option value="1" ${status == 1 ? 'selected' : ' '}>Yes</option>
                                <option value="0" ${status == 0 ? 'selected' : ' '}>No</option>
                            </select>
                        </div>
                    `
                });


                $modal.find('.config-html').html(html);
                $modal.find('.provider-name').text(provider.title);
                $modal.find('.instruction').html(provider.instruction);
                $modal.modal('show')
            });


            $(".config-modal").on('submit', function(e) {
                e.preventDefault();

                const $this = $(this);
                const $submitBtn = $this.find(`button[type=submit]`);
                const action = $this.attr('action');
                const formData = new FormData($this[0]);
                const originalBtnHtml = $submitBtn.html();

                $.ajax({
                    type: "POST",
                    url: action,
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $submitBtn.addClass('disabled').attr('disabled', true)
                            .html(`<div class="button-loader d-flex gap-2 flex-wrap align-items-center justify-content-center">
                                <div class="spinner-border"></div><span>Loading...</span>
                            </div>`)
                    },
                    complete: function(response) {
                        $submitBtn.removeClass('disabled')
                            .attr('disabled', false)
                            .html(originalBtnHtml);
                    },
                    success: function(response) {
                        notify(response.status, response.message);
                        if (response.status == 'success') {
                            $modal.modal('hide');
                        }
                    },
                    error: function(xhr) {
                        notify("error", xhr.responseJSON && xhr.responseJSON.message ? xhr .responseJSON.message : "Something went wrong.");
                    },

                });
            });

            $('#credentials-tab').on('click', function(e) {
                $('.modal').removeClass('modal-xl').addClass('modal-lg');
                $('.modal').find('.modal-title').text('{{ __('Setup Credentials') }}');
            });
            $('#instruction-tab').on('click', function(e) {
                $('.modal').removeClass('modal-lg').addClass('modal-xl');
                $('.modal').find('.modal-title').text('{{ __('Instruction') }}');
            });

            function toTitleCase(input) {
                const spaced = input
                    .replace(/([a-z0-9])([A-Z])/g, '$1 $2') 
                    .replace(/[_\-]+/g, ' ') 
                    .toLowerCase();

                // Capitalize each word
                return spaced.replace(/\b\w/g, char => char.toUpperCase());
            }


        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        .thumb img {
            max-width: 100px;
            border-radius: 5px;
        }

        .configure-tab {
            width: fit-content;
            padding: 8px !important;
            background: hsl(var(--bg-color));
            border-radius: 8px;
        }

        .configure-tab .nav-link {
            color: hsl(var(--sidebar-active));
            font-weight: 500;
            font-size: 15px;
        }

        .configure-tab .nav-link.active {
            background: hsl(var(--white));
            color: hsl(var(--black))
        }
    </style>
@endpush
