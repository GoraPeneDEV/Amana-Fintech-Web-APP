@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout :renderTableFilter="false">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Banner Image')</th>
                            <th>@lang('Link')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($banners as $banner)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="table-thumb">
                                            <img src="{{ getImage(getFilePath('banner') . '/' . $banner->image) }}"
                                                alt="banner">
                                        </span>
                                    </div>
                                </td>
                                <td><a href="{{ $banner->link }}">{{ $banner->link }}</a></td>
                                <td>
                                    <x-admin.other.status_switch :status="$banner->status" :action="route('admin.banner.status', $banner->id)" title="Banner" />
                                </td>
                                <td>
                                    <x-admin.ui.btn.edit class="editBtn" data-resource="{{ json_encode($banner) }}"
                                        data-image-path="{{ getImage(getFilePath('banner') . '/' . $banner->image) }}" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($banners->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($banners) }}
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
            <form action="{{ route('admin.banner.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>@lang('Image')</label>
                    <x-image-uploader :size="getFileSize('banner')" name="image" :required="true" />
                </div>
                <div class="form-group">
                    <label>@lang('Link Type')</label>
                    <select name="link_type" class="form-control select2" data-minimum-results-for-search="-1" required>
                        <option value="1">@lang('Web View')</option>
                        <option value="2">@lang('Module')</option>
                    </select>
                </div>
                <div class="form-group module-select">
                    <label>@lang('Module')</label>
                    <select name="module" class="form-control select2" data-minimum-results-for-search="-1" required>
                        <option value="0" disabled selected>@lang('Select')</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module->slug }}">
                                {{ __($module->title) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group link-field">
                    <label>@lang('Link')</label>
                    <input class="form-control" type="text" name="link" required>
                </div>

                <div class="form-group">
                    <label>@lang('Description')</label>
                    <textarea class="form-control" name="description" rows="3" id="description" required></textarea>
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

            $('select[name="link_type"]').on('change', function() {
                var selectedValue = $(this).val();

                if (selectedValue == '1') {
                    $('.link-field').show();
                    $('.link-field input').attr('required', true);
                    $('.module-select').hide();
                    $('.module-select select').removeAttr('required').val('');
                } else if (selectedValue == '2') {
                    $('.module-select').show();
                    $('.module-select select').attr('required', true);
                    $('.link-field').hide();
                    $('.link-field input').removeAttr('required').val('');
                } else {
                    $('.link-field, .module-select').hide();
                    $('.link-field input').removeAttr('required').val('');
                    $('.module-select select').removeAttr('required').val('');
                }
            });

            $('select[name="link_type"]').change();

            let defaultImage = `{{ getImage(getFilePath('banner')) }}`;
            let modal = $("#settingModal");
            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add New Banner')`);
                modal.find('[name=image]').attr('required', true).closest('.form-group').find('label:first')
                    .addClass('required');
                modal.find('.image-upload img').attr('src', defaultImage);
                modal.find('form').trigger('reset');
                modal.find('form').attr('action', "{{ route('admin.banner.save') }}")
                modal.modal('show');
            });

            $(".editBtn").on('click', function(e) {
                const resource = $(this).data('resource');
                const imagepath = $(this).data('imagePath');

                modal.find('.modal-title').text("@lang('Edit Banner')");
                const actionUrl = "{{ route('admin.banner.save', ':id') }}".replace(':id', resource.id);
                modal.find('form').attr('action', actionUrl);

                // Set link_type first
                modal.find('select[name="link_type"]').val(resource.type).trigger('change');

                // After link_type change event completes, set the appropriate field value
                if (resource.type == 1) {
                    modal.find('input[name="link"]').val(resource.link).attr('required', true);
                    modal.find('select[name="module"]').val('').removeAttr('required');
                } else if (resource.type == 2) {
                    modal.find('select[name="module"]').val(resource.link).attr('required', true).trigger(
                        'change');
                    modal.find('input[name="link"]').val('').removeAttr('required');
                }


                modal.find('textarea[name="description"]').val(resource.description);
                modal.find('[name=image]').attr('required', false)
                    .closest('.form-group')
                    .find('label:first')
                    .removeClass('required');
                modal.find('.image-upload img').attr('src', imagepath);
                modal.modal('show');

            });

        })(jQuery);
    </script>
@endpush
