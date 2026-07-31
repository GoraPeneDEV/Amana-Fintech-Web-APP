@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Category')</th>
                            <th>@lang('Charge')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($institutions as $institution)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="table-thumb d-none d-lg-block">
                                            <img src="{{ getImage(getFilePath('education_fee') . '/' . $institution->image) }}"
                                                alt="education_fee">
                                        </span>
                                        {{ __($institution->name) }}
                                    </div>
                                </td>
                                <td>{{ __($institution->category->name) }}</td>
                                <td>
                                    @if (!$institution->fixed_charge && !$institution->percent_charge)
                                        <span class="badge badge--primary"
                                            title="No specific charge is set for this organization. Therefore, the global microfinance payment fee of {{ showAmount($charge->fixed_charge) }} plus {{ getAmount($charge->percent_charge) }}% will apply.">
                                            @lang('Not Set')
                                        </span>
                                    @else
                                        {{ showAmount($institution->fixed_charge) }}
                                        +{{ getAmount($institution->percent_charge) }}%
                                    @endif
                                </td>

                                <td>
                                    <x-admin.other.status_switch :status="$institution->status" :action="route('admin.education.institute.status', $institution->id)" title="Institution" />
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">

                                        <x-admin.ui.btn.edit class="editBtn" data-resource="{{ json_encode($institution) }}"
                                            data-image-path="{{ getImage(getFilePath('education_fee') . '/' . $institution->image) }}" />

                                        <a href="{{ route('admin.education.institute.configure', $institution->id) }}" type="button"
                                            class="btn  btn-outline--info">
                                            <span class=" btn--icon"><i class="la la-tools"></i></span>@lang('Configure')
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($institutions->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($institutions) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>

    <x-admin.ui.modal id="settingModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Add Institution')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="{{ route('admin.education.institute.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>@lang('Image')</label>
                    <x-image-uploader :size="getFileSize('education_fee')" name="image" :required="true" />
                </div>
                <div class="form-group ">
                    <label>@lang('Name')</label>
                    <input class="form-control" type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>@lang('Category')</label>
                    <select class="form-control select2" name="category_id" required>@lang('Select')
                        <option disabled selected>@lang('Selct')</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ isset($selectedCategoryId) && $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                {{ __($category->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        @lang('Fixed Charge')

                        <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="@lang('To use the global education fee charges, keep both Percent Charge and Fixed Charge empty or set to 0.')">
                        </i>
                    </label>
                    <div class="input-group input--group">
                        <input type="number" step="any" class="form-control" name="fixed_charge"
                            value="{{ old('fixed_charge') }}">
                        <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        @lang('Percent Charge')

                        <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="@lang('To use the global education fee charges, keep both Percent Charge and Fixed Charge empty or set to 0.')">
                        </i>
                    </label>
                    <div class="input-group input--group">
                        <input type="number" step="any" class="form-control" name="percent_charge"
                            value="{{ old('percent_charge') }}">
                        <div class="input-group-text">%</div>
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
            let defaultImage = `{{ getImage(getFilePath('education_fee')) }}`;
            let modal = $("#settingModal");

            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Institution')`);
                modal.find('[name=image]').attr('required', true).closest('.form-group').find('label:first')
                    .addClass('required');
                modal.find('.image-upload img').attr('src', defaultImage);

                modal.find('form').attr('action', "{{ route('admin.education.institute.save') }}");
                modal.find('form').trigger('reset');

                modal.modal('show');
            });

            $(".editBtn").on('click', function(e) {

                const resource = $(this).data('resource');
                const imagepath = $(this).data('imagePath');

                modal.find('.modal-title').text("@lang('Edit Institution')");
                const actionUrl = "{{ route('admin.education.institute.save', ':id') }}".replace(':id',
                    resource.id);
                modal.find('form').attr('action', actionUrl);
                modal.find('input[name="name"]').val(resource.name);
                modal.find('input[name="fixed_charge"]').val(getAmount(resource.fixed_charge));
                modal.find('input[name="percent_charge"]').val(getAmount(resource.percent_charge));
                modal.find('select[name="category_id"]').val(resource.category_id).trigger('change');
                modal.find('[name=image]').attr('required', false).closest('.form-group').find('label:first')
                    .removeClass('required');
                modal.find('.image-upload img').attr('src', imagepath)
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
