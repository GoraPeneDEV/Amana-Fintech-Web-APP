@extends('admin.layouts.app')

@section('panel')
    <div class="row responsive-row">
        @foreach ($modules->where('user_type', 'USER') as $k => $module)
            <div class="col-xxl-3 col-lg-4 col-sm-6 config-col">
                <div class="system-configure">
                    <div class="system-configure__header d-flex justify-content-between align-items-center">
                        <div class="system-configure__title d-flex align-items-center gap-2">
                            <div class="icon"><i class="{{ $module->icon }}"></i></div>
                            <h6 class="mb-0 config-name">{{ __($module->title) }}</h6>
                        </div>
                        <div class="form-check form-switch form--switch pl-0 form-switch-success">
                            <input class="form-check-input configuration-switch" type="checkbox" role="switch"
                                id="{{ $k }}" data-key="{{ $module->id }}" @checked($module->status)
                                data-configuration="{{ $module }}">
                        </div>
                    </div>
                    <div class="system-configure__content">
                        <p class="desc">
                            @if ($module->status)
                                {{ __(@$module->description_disabled) }}
                            @else
                                {{ __(@$module->description_enabled) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-12">
            <h4 class="m-0">@lang('Agent Module')</h4>
        </div>
        @foreach ($modules->where('user_type', 'AGENT') as $k => $module)
            <div class="col-xxl-3 col-lg-4 col-sm-6 config-col">
                <div class="system-configure">
                    <div class="system-configure__header d-flex justify-content-between align-items-center">
                        <div class="system-configure__title d-flex align-items-center gap-2">
                            <div class="icon"><i class="{{ $module->icon }}"></i></div>
                            <h6 class="mb-0 config-name">{{ __($module->title) }}</h6>
                        </div>
                        <div class="form-check form-switch form--switch pl-0 form-switch-success">
                            <input class="form-check-input configuration-switch" type="checkbox" role="switch"
                                id="{{ $k }}" data-key="{{ $module->id }}" @checked($module->status)
                                data-configuration="{{ $module }}">
                        </div>
                    </div>
                    <div class="system-configure__content">
                        <p class="desc">
                            @if ($module->status)
                                {{ __(@$module->description_disabled) }}
                            @else
                                {{ __(@$module->description_enabled) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
        
    </div>

    <x-confirmation-modal />
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {
            $(".configuration-switch").on('change', function(e) {

                const url = "{{ route('admin.module.update', ':key') }}";
                const key = $(this).data('key');
                const configuration = $(this).data('configuration');
                const $this = $(this);
                const isChecked = $this.is(':checked');
                $.ajax({
                    type: "get",
                    url: url.replace(":key", key),
                    data: "data",
                    success: function(resp) {
                        if (resp.success) {
                            if (resp.new_status) {
                                notify('success', `${configuration.title} enabled successfully`);
                                $this.closest(".system-configure").find('.desc').text(configuration
                                    .description_disabled);
                            } else {
                                notify('success', `${configuration.title} disabled successfully`);
                                $this.closest(".system-configure").find('.desc').text(configuration
                                    .description_enabled);
                            }
                        } else {
                            notify('error', resp.message);
                            $this.attr('checked', !isChecked)
                        }
                    },
                    error: function(resp) {
                        $this.attr('checked', !isChecked)
                    }
                });
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .flex-thumb-wrapper .thumb {
            width: 50px;
            height: 50px;
        }

        .gateway-status {
            position: absolute;
            right: 16px;
            top: 16px;
        }

        .divider {
            position: relative;
            border-bottom: 1px solid hsl(var(--primary));
            margin-bottom: 70px;
            margin-top: 70px;
        }

        .divider:before {
            position: absolute;
            content: '';
            width: 30px;
            height: 30px;
            border: 1px solid hsl(var(--primary));
            left: 50%;
            margin-left: -15px;
            top: 50%;
            background: #fff;
            margin-top: -15px;
            -webkit-transform: rotate(45deg);
            -moz-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .divider:after {
            position: absolute;
            content: '';
            width: 20px;
            height: 20px;
            border: 1px solid hsl(var(--primary));
            left: 50%;
            margin-left: -10px;
            top: 50%;
            background: hsl(var(--primary));
            margin-top: -10px;
            -webkit-transform: rotate(45deg);
            -moz-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }
    </style>
@endpush
