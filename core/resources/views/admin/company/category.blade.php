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
                        @forelse($utilityBills as $utility)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="table-thumb d-none d-lg-block">
                                            <img src="{{ getImage(getFilePath('utility') . '/' . $utility->image) }}" alt="utility">
                                        </span>
                                        {{ __($utility->formatted_name) }}
                                    </div>
                                </td>
                                <td>
                                    <x-admin.other.status_switch :status="$utility->status" :action="route('admin.bill.category.status', $utility->id)" title="Company" />
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">

                                        <x-admin.ui.btn.edit class="editBtn" data-resource="{{ json_encode($utility) }}" data-image-path="{{ getImage(getFilePath('utility') . '/' . $utility->image) }}" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($utilityBills->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($utilityBills) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>

    <x-admin.ui.modal id="settingModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Add Company')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="{{ route('admin.bill.category.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>@lang('Image')</label>
                    <x-image-uploader :size="getFileSize('utility')" name="image" :required="true" />
                </div>
                <div class="form-group ">
                    <label>@lang('Name')</label>
                    <input class="form-control" type="text" name="name" required>
                </div>
                <div class="form-group">
                    <x-admin.ui.btn.modal />
                </div>
            </form>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>

    <x-confirmation-modal />


    <x-admin.ui.modal id="import-modal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Import Information')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body class="py-4">
            <div class="alert alert--warning mb-4">
                <p class="fw-600 py-2">
                    @lang('Utility bill categories are retrieved via the API once you have correctly configured a Utility Bill Provider. Only categories that do not already exist in your database will be imported. Please note that no categories can be imported until at least one Utility Bill Provider has been set up.')
                </p>
            </div>
            <a href="{{ route('admin.bill.category.import') }}" class="btn btn-outline--success">
                <i class="las la-download"></i> @lang('Start Import')
            </a>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-outline--success" data-bs-toggle="modal" data-bs-target="#import-modal">
        <i class="las la-download"></i> @lang('Import via API')
    </button>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            let defaultImage = `{{ getImage(getFilePath('utility')) }}`;
            let modal = $("#settingModal");


            $(".editBtn").on('click', function(e) {

                const resource = $(this).data('resource');
                const imagepath = $(this).data('imagePath');

                modal.find('.modal-title').text("@lang('Edit Company')");
                const actionUrl = "{{ route('admin.bill.category.save', ':id') }}".replace(':id',
                    resource.id);
                modal.find('form').attr('action', actionUrl);
                modal.find('input[name="name"]').val(resource.name);
                modal.find('[name=image]').attr('required', false).closest('.form-group').find('label:first')
                    .removeClass('required');
                modal.find('.image-upload img').attr('src', imagepath)
                modal.modal('show');
            });



        })(jQuery);
    </script>
@endpush
