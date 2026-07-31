@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Charge')</th>
                            <th>@lang('Rate')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($companies as $company)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="table-thumb d-none d-lg-block">
                                            <img src="{{ getImage(getFilePath('utility') . '/' . $company->image) }}">
                                        </span>
                                        <span>
                                            <span class="d-block">
                                                {{ __($company->name) }}
                                            </span>
                                            <span class="fs-11">{{ __(@$company->category->formatted_name) }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if (@$company->denomination_type == 'RANGE')
                                        <span> {{ showAmount(@$company->minimum_amount) }} -
                                            {{ showAmount(@$company->maximum_amount) }}</span>
                                    @else
                                        <span class="badge badge--primary fix-amount cursor-pointer"
                                            data-amounts='@json($company->fixed_amounts)'>
                                            @lang('FIXED')
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if (!$company->fixed_charge && !$company->percent_charge)
                                        <span class="badge badge--primary"
                                            title="No specific charge is set for this company. Therefore, the global utility bill charge of {{ showAmount($charge->fixed_charge) }} plus {{ getAmount($charge->percent_charge) }}% will apply.">
                                            @lang('Not Set')
                                        </span>
                                    @else
                                        {{ showAmount($company->fixed_charge) }}
                                        + {{ getAmount($company->percent_charge) }}%
                                    @endif
                                </td>
                                <td>
                                    1 {{ __(gs('cur_text')) }} = {{ getAmount($company->rate) }}
                                    {{ $company->currency_code }}
                                </td>
                                <td>
                                    <x-admin.other.status_switch :status="$company->status" :action="route('admin.utility.bill.company.status', $company->id)" title="Company" />
                                </td>
                                <td>
                                    <x-admin.ui.btn.edit class="editBtn" data-resource="{{ json_encode($company) }}"
                                        data-image-path="{{ getImage(getFilePath('utility') . '/' . $company->image) }}" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($companies->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($companies) }}
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
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-12">
                        <label>@lang('Image')</label>
                        <x-image-uploader :size="getFileSize('utility')" name="image" :required="true" />
                    </div>
                    <div class="form-group col-12">
                        <label>@lang('Name')</label>
                        <input class="form-control" type="text" name="name" required>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>
                            @lang('Minimum Bill Amount')
                            <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="@lang('To use the global utility bill payment limit, keep both minimum amount and maximum amount empty.')">
                            </i>
                        </label>
                        <div class="input-group input--group">
                            <input type="number" step="any" class="form-control" name="minimum_amount"
                                value="{{ old('minimum_amount') }}">
                            <div class="input-group-text currency-code"> {{ __(gs('cur_text')) }}</div>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>
                            @lang('Maximum Bill Amount')
                            <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="@lang('To use the global utility bill payment limit, keep both minimum amount and maximum amount empty.')">
                            </i>
                        </label>
                        <div class="input-group input--group">
                            <input type="number" step="any" class="form-control" name="maximum_amount"
                                value="{{ old('maximum_amount') }}">
                            <div class="input-group-text currency-code"> {{ __(gs('cur_text')) }}</div>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>
                            @lang('Fixed Charge')
                            <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="@lang('To use the global utility bill charges, keep both Percent Charge and Fixed Charge empty.')">
                            </i>
                        </label>
                        <div class="input-group input--group">
                            <input type="number" step="any" class="form-control" name="fixed_charge"
                                value="{{ old('fixed_charge') }}">
                            <div class="input-group-text currency-code"> {{ __(gs('cur_text')) }}</div>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label>
                            @lang('Percent Charge')
                            <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="@lang('To use the global utility bill charges, keep both Percent Charge and Fixed Charge empty.')">
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
                </div>

            </form>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>

    <x-admin.ui.modal id="amount-modal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Fixed Amount')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>

            <ul class="list-group list-group-flush"></ul>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>


    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.utility.bill.company.fetch.companies', $country->id) }}" class="btn btn-outline--success">
            <i class="las la-download"></i> @lang('Fetch Companies')
        </a>
        <x-back_btn route="{{ route('admin.utility.bill.company.countries') }}" />
    </div>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            let defaultImage = `{{ getImage(getFilePath('utility')) }}`;
            let modal = $("#settingModal");


            $('.fix-amount').on('click', function(e) {
                const amounts = $(this).data('amounts');

                let html = "";
                $.each(amounts, function(i, amount) {
                    html += `
                    <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                        <span>${amount?.description}</span>
                        <span class="text--primary"> {{ gs('cur_sym') }}${parseFloat(amount?.amount)} </span>
                    </li>
                    `
                });

                $("#amount-modal").find('ul').html(html);
                $("#amount-modal").modal('show');

            });

            $(".editBtn").on('click', function(e) {
                const resource = $(this).data('resource');
                const imagepath = $(this).data('imagePath');

                modal.find('.modal-title').text("@lang('Edit Company')");
                const actionUrl = "{{ route('admin.utility.bill.company.save', ':id') }}".replace(':id',
                    resource.id);
                modal.find('form').attr('action', actionUrl);
                modal.find('input[name="name"]').val(resource.name);
                modal.find('select[name="category_id"]').val(resource.category_id).trigger('change');
                modal.find('input[name="fixed_charge"]').val(getAmount(resource.fixed_charge));
                modal.find('input[name="percent_charge"]').val(getAmount(resource.percent_charge));
                modal.find('input[name="minimum_amount"]').val(getAmount(resource.minimum_amount));
                modal.find('input[name="maximum_amount"]').val(getAmount(resource.maximum_amount));
                modal.find('[name=image]').attr('required', false).closest('.form-group').find('label:first')
                    .removeClass('required');
                modal.find('.image-upload img').attr('src', imagepath);
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
