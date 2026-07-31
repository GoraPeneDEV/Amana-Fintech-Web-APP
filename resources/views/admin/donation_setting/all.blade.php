@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($donations as $donation)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="table-thumb d-none d-lg-block">
                                            <img src="{{ getImage(getFilePath('donation') . '/' . $donation->image) }}"
                                                alt="operator">
                                        </span>
                                        {{ __($donation->name) }}
                                    </div>
                                </td>
                                <td>
                                    <x-admin.other.status_switch :status="$donation->status" :action="route('admin.donation.charity.status', $donation->id)" title="Charity" />
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        <x-admin.ui.btn.edit class="editBtn" data-resource="{{ json_encode($donation) }}"
                                            data-image-path="{{ getImage(getFilePath('donation') . '/' . $donation->image) }}" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($donations->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($donations) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>

    <x-admin.ui.modal id="settingModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Add Charity')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="{{ route('admin.donation.charity.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>@lang('Image')</label>
                    <x-image-uploader :size="getFileSize('donation')" name="image" :required="true" />
                </div>
                <div class="form-group ">
                    <label>@lang('Name')</label>
                    <input class="form-control" type="text" name="name" required>
                </div>
                <div class="form-group ">
                    <label>@lang('Details')</label>
                    <textarea name="details" id="details" maxlength="255" class="form-control" rows="5" required></textarea>
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
            let defaultImage = `{{ getImage(getFilePath('donation')) }}`;
            let modal = $("#settingModal");
            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Charity')`);
                modal.find('[name=image]').attr('required', true).closest('.form-group').find('label:first')
                    .addClass('required');
                modal.find('.image-upload img').attr('src', defaultImage);

                modal.find('form').trigger('reset');
                modal.find('form').attr('action',"{{ route('admin.donation.charity.save') }}")
                modal.modal('show');
            });

            $(".editBtn").on('click', function(e) {

                const resource = $(this).data('resource');
                const imagepath = $(this).data('imagePath');

                modal.find('.modal-title').text("@lang('Edit Charity')");
                const actionUrl = "{{ route('admin.donation.charity.save', ':id') }}".replace(
                    ':id',
                    resource.id);
                modal.find('form').attr('action', actionUrl);
                modal.find('input[name="name"]').val(resource.name);
                modal.find('textarea[name="details"]').val(resource.details);
                modal.find('[name=image]').attr('required', false).closest('.form-group').find('label:first')
                    .removeClass('required');
                modal.find('.image-upload img').attr('src', imagepath)
                modal.modal('show');
            });


        })(jQuery);
    </script>
@endpush
