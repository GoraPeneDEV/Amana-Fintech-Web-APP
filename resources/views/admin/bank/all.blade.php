@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Charge')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($banks as $bank)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="table-thumb d-none d-lg-block">
                                            <img src="{{ getImage(getFilePath('bank_transfer') . '/' . $bank->image) }}"
                                                alt="bank_transfer">
                                        </span>
                                        {{ __($bank->name) }}
                                    </div>
                                </td>
                                <td>
                                    @if (!$bank->fixed_charge && !$bank->percent_charge)
                                        <span class="badge badge--primary"
                                            title="No specific charge is set for this bank. Therefore, the global bank transfer fee of {{ showAmount($charge->fixed_charge) }} plus {{ getAmount($charge->percent_charge) }}% will apply.">
                                            @lang('Not Set')
                                        </span>
                                    @else
                                        {{ showAmount($bank->fixed_charge) }} +{{ getAmount($bank->percent_charge) }}%
                                    @endif
                                </td>
                                <td>
                                    <x-admin.other.status_switch :status="$bank->status" :action="route('admin.setting.bank.transfer.status', $bank->id)" title="Bank" />
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        <x-admin.ui.btn.edit class="editBtn" data-resource="{{ json_encode($bank) }}"
                                            data-image-path="{{ getImage(getFilePath('bank_transfer') . '/' . $bank->image) }}" />

                                        <a href="{{ route('admin.setting.bank.transfer.configure', $bank->id) }}"
                                            type="button" class="btn  btn-outline--info">
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
                @if ($banks->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($banks) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>

    <x-admin.ui.modal id="settingModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Add Bank')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="{{ route('admin.setting.bank.transfer.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>@lang('Image')</label>
                    <x-image-uploader :size="getFileSize('bank_transfer')" name="image" :required="true" />
                </div>
                <div class="form-group ">
                    <label>@lang('Name')</label>
                    <input class="form-control" type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>
                        @lang('Fixed Charge')
                        <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="@lang('If you specify either a Percent Charge or a Fixed Charge, the fee will be calculated based on the specific charges set for this bank. However, if both fields are left empty or set to 0, the fee will be calculated using the Global Bank Transfer Charge.')">
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
                            title="@lang('If you specify either a Percent Charge or a Fixed Charge, the fee will be calculated based on the specific charges set for this bank. However, if both fields are left empty or set to 0, the fee will be calculated using the Global Bank Transfer Charge.')">
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

            let defaultImage = `{{ getImage(getFilePath('bank_transfer')) }}`;
            let modal = $("#settingModal");

            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add New Bank')`);
                modal.find('[name=image]').attr('required', true).closest('.form-group').find('label:first')
                    .addClass('required');
                modal.find('form').attr('action', "{{ route('admin.setting.bank.transfer.save') }}");
                modal.find('form').trigger('reset');
                modal.find('.image-upload img').attr('src', defaultImage)
                modal.modal('show');
            });

            $(".editBtn").on('click', function(e) {

                const resource = $(this).data('resource');
                const imagepath = $(this).data('imagePath');

                modal.find('.modal-title').text("@lang('Edit Bank')");
                const actionUrl = "{{ route('admin.setting.bank.transfer.save', ':id') }}".replace(':id',
                    resource.id);
                modal.find('form').attr('action', actionUrl);
                modal.find('input[name="name"]').val(resource.name);
                modal.find('input[name="fixed_charge"]').val(getAmount(resource.fixed_charge));
                modal.find('input[name="percent_charge"]').val(getAmount(resource.percent_charge));
                modal.find('[name=image]').attr('required', false).closest('.form-group').find('label:first')
                    .removeClass('required');
                modal.find('.image-upload img').attr('src', imagepath)
                modal.modal('show');
            });

        

        })(jQuery);
    </script>
@endpush
