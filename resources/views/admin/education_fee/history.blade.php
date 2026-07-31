@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            @include('admin.education_fee.widget')
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx, username">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Educational Institutions')</th>
                                    <th>@lang('Trx') | @lang('Time')</th>
                                    <th>@lang('Amount') | @lang('Charge')</th>
                                    <th>@lang('Total Amount')</th>
                                    @if (request()->routeIs('admin.education.fee.all'))
                                        <th>@lang('Status')</th>
                                    @endif
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($fees as $fee)
                                    <tr>
                                        <td>
                                            <x-admin.other.user_info :user="$fee->user" />
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __(@$fee->institution->name) }}</span>
                                                <span>{{ __(@$fee->institution->category->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <a class="d-block"
                                                    href="{{ route('admin.report.transaction', ['search' => $fee->trx]) }}">
                                                    {{ $fee->trx }}
                                                </a>
                                                <span title="{{ diffForHumans($fee->created_at) }}">
                                                    {{ showDateTime($fee->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showAmount($fee->amount) }}</span>
                                                <span class="text--success" title="@lang('Charge')">
                                                    {{ showAmount($fee->charge) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ showAmount($fee->total) }}
                                        </td>
                                        @if (request()->routeIs('admin.education.fee.all'))
                                            <td> @php echo $fee->statusBadge; @endphp </td>
                                        @endif

                                        @if ($fee->status == Status::PENDING)
                                            <td class="dropdown">
                                                <button class=" btn btn-outline--primary" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="la la-cog ms-1"></i> @lang('Action')
                                                </button>
                                                <div class="dropdown-menu dropdown">
                                                    <button type="button"
                                                        class="dropdown-list d-block w-100 text-start details-btn"
                                                        data-data="{{ $fee }}">
                                                        <span class="me-2">
                                                            <i class="fas fa-eye text--primary"></i>
                                                        </span>
                                                        @lang('Details')
                                                    </button>
                                                    <x-permission_check permission="approve education fee">
                                                        <button type="button"
                                                            class="dropdown-list d-block confirmationBtn w-100 text-start"
                                                            data-question="@lang('Are you sure to approve this education fee?')"
                                                            data-action="{{ route('admin.education.fee.approve', $fee->id) }}">
                                                            <span class="me-2">
                                                                <i class="fas fa-check-circle text--success"></i>
                                                            </span>
                                                            @lang('Approve')
                                                        </button>
                                                    </x-permission_check>
                                                    <x-permission_check permission="reject education fee">
                                                        <button type="button"
                                                            class="dropdown-list d-block rejectModal w-100 text-start"
                                                            data-id="{{ $fee->id }}">
                                                            <span class="me-2">
                                                                <i class="fas fa-times-circle text--danger"></i>
                                                            </span>
                                                            @lang('Reject')
                                                        </button>
                                                    </x-permission_check>
                                                </div>
                                            </td>
                                        @else
                                            <td>
                                                <x-admin.ui.btn.details data-data="{{ $fee }}" class="details-btn"
                                                    tag="btn" />
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($fees->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($fees) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="viewModal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Education Fee Details')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="" method="POST">
                @csrf
                <input type="hidden" name="id" required>
                <ul class="list-group list-group-flush mb-3">
                </ul>
                <div class="reason d-none"></div>
            </form>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>


    <x-confirmation-modal />

    <x-admin.ui.modal id="rejectModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Reject Education Fee Confirmation')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="" method="POST">
                @csrf
                <div class="alert alert--warning mb-3">
                    @lang('Are you sure to reject this education fee?')
                </div>
                <div class="form-group">
                    <label class="mt-2">@lang('Reason for Rejection')</label>
                    <textarea name="message" maxlength="255" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                </div>
                <div class="form-group">
                    <x-admin.ui.btn.modal />
                </div>
            </form>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.details-btn').on('click', function() {
                var modal = $('#viewModal');
                var data = $(this).data('data');
                var userData = data.user_data;

                if (data.admin_feedback && data.admin_feedback.length) { // 2 => Rejected
                    $('.reason').removeClass('d-none').html(`
                        <div class="alert alert--danger mb-3">
                            <span class="d-block">
                                <i class="fas fa-exclamation-triangle text--danger"></i>
                                @lang('Rejected Reason')
                            </span>
                            <span class="text--dark">${data.admin_feedback}</span>
                        </div>
                    `)
                } else {
                    $('.reason').addClass('d-none')
                }

                var html = `
                    <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                            <span>@lang('Educational Institution')</span>
                            <span>${data.institution.name}</span>
                    </li>
                    <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                        <span>@lang('Amount')</span>
                        <span class="text--primary">{{ gs('cur_sym') }}${parseFloat(data.amount).toFixed(2)}</span>
                    </li>
                    <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                        <span>@lang('Charge')</span>
                        <span class="text--success">{{ gs('cur_sym') }}${parseFloat(data.charge).toFixed(2)}</span>
                    </li>
                    <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                        <span>@lang('Total Amount')</span>
                        <span class="text--primary">{{ gs('cur_sym') }}${parseFloat(data.total).toFixed(2)}</span>
                    </li>
                `;

                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                        <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                            <span>${element.name}</span>
                            <span>${element.value ?? 'N/A'}</span>
                        </li>`;
                    }
                    if (element.type == 'file') {
                        html += `
                        <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                            <span>${element.name}</span>
                            <a href="{{ route('home') . '/' . getFilePath('verify') }}/${element.value}" download class='fw-bold'>@lang('Attachment')</a>
                        </li>`;
                    }
                });

                modal.find('.list-group').html(html);
                modal.modal('show');
            });

            $(".rejectModal").on('click', function(e) {
                const id = $(this).data('id');
                const action = "{{ route('admin.education.fee.reject', ':id') }}";
                $('#rejectModal').find('form').attr('action', action.replace(":id", id));
                $('#rejectModal').modal('show');

            });
        })(jQuery);
    </script>
@endpush
