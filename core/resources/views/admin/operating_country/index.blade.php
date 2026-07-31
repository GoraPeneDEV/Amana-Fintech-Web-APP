@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout :renderExportButton="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th> @lang('Country') </th>
                                    <th> @lang('Mobile Number Digit') </th>
                                    <th> @lang('Dial Code') </th>
                                    <th> @lang('Status') </th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($countries as $operatingCountry)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img class="country-image"
                                                    src="{{ asset('assets/images/country/' . strtolower($operatingCountry->code) . '.svg') }}"
                                                    alt="">
                                                {{ __($operatingCountry->name) }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ __($operatingCountry->mobile_number_digit) }} @lang('Digit')
                                        </td>
                                        <td>
                                            +{{ __($operatingCountry->dial_code) }}
                                        </td>
                                        <td>
                                            <x-admin.other.status_switch :status="$operatingCountry->status" :action="route('admin.operating.country.status', $operatingCountry->id)"
                                                title="OperatingCountry" />
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.edit tag="button" :data-resource="$operatingCountry" />
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($countries->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($countries) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title"></h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="{{ route('admin.operating.country.save') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="required">@lang('Country')</label>
                    <select name="country" class="form-control select2">
                        <option value="" selected disabled>@lang('Select One')</option>
                        @foreach ($allCountries as $k => $allCountry)
                            <option value="{{ $k }}">{{ __($allCountry->country) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="required">
                        @lang('Mobile Number Digits')
                    </label>
                    <span title="@lang('Specify the required mobile number digits without country code')">
                        <i class="las la-info-circle"></i>
                    </span>
                    <div class="input-group input--group">
                        <input type="text" class="form-control" name="mobile_number_digit"
                            value="{{ old('mobile_number_digit') }}">
                        <span class="input-group-text">
                            @lang('Digits')
                        </span>
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



@push('script')
    <script>
        (function($) {
            "use strict";
            const $modal = $("#modal");

            $(".edit-btn").on('click', function(e) {
                const data = $(this).data('resource');
                const action = "{{ route('admin.operating.country.save', ':id') }}";

                $("input[name='mobile_number_digit']").val(data.mobile_number_digit);
                $("select[name='country']").val(data.code);
                $("select[name='country']").closest('.form-group').addClass("d-none");

                $modal.find(".modal-title").text("@lang('Edit Operating Country')");
                $modal.find('form').attr('action', action.replace(':id', data.id));
                $modal.modal("show");
            });

            $(".add-btn").on('click', function(e) {
                const action = "{{ route('admin.operating.country.save') }}";
                $modal.find(".modal-title").text("@lang('Add Operating Country')");
                $("select[name='country']").closest('.form-group').removeClass("d-none");
                $modal.find('form').trigger('reset');
                $modal.find('form').attr('action', action);
                $modal.modal("show");
            });
        })(jQuery);
    </script>
@endpush


@push('modal')
    <x-confirmation-modal />
@endpush

@push('breadcrumb-plugins')
    <x-admin.ui.btn.add tag="button" />
@endpush

@push('style')
    <style>
        .country-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
@endpush
