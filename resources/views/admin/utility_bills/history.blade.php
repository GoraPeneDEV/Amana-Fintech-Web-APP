@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            @if (request()->routeIs('admin.utility.bill.all'))
                @include('admin.utility_bills.widget')
            @endif
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx, username">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Company Name')</th>
                                    <th>@lang('Trx') | @lang('Time')</th>
                                    <th>@lang('Amount') | @lang('Charge')</th>
                                    <th>@lang('Total Amount')</th>
                                    @if (request()->routeIs('admin.utility.bill.all'))
                                        <th>@lang('Status')</th>
                                    @endif
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($bills as $bill)
                                    <tr>
                                        <td>
                                            <x-admin.other.user_info :user="$bill->user" />
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ __(@$bill->company->name) }}</span>
                                                <span>{{ __(@$bill->company->category->formatted_name) }}</span>
                                            </div>

                                        </td>
                                        <td>
                                            <div>
                                                <a class="d-block"
                                                    href="{{ route('admin.report.transaction', ['search' => $bill->trx]) }}">
                                                    {{ $bill->trx }}
                                                </a>
                                                <span title="{{ diffForHumans($bill->created_at) }}">
                                                    {{ showDateTime($bill->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showAmount($bill->amount) }}</span>
                                                <span class="text--success" title="@lang('Charge')">
                                                    {{ showAmount($bill->charge) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ showAmount($bill->total) }}
                                        </td>
                                        @if (request()->routeIs('admin.utility.bill.all'))
                                            <td> @php echo $bill->statusBadge; @endphp </td>
                                        @endif

                                        <td>
                                            <x-admin.ui.btn.details data-data="{{ $bill }}" class="details-btn"
                                                tag="btn" />
                                        </td>

                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($bills->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($bills) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="viewModal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Utility Bill Details')</h4>
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
            <h1 class="modal-title">@lang('Reject Utility Bill Confirmation')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="" method="POST">
                @csrf
                <div class="alert alert--warning mb-3">
                    @lang('Are you sure to reject this utility  bill?')
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
                
                if (data.admin_billdback && data.admin_billdback.length) { // 2 => Rejected
                    $('.reason').removeClass('d-none').html(`
                        <div class="alert alert--danger mb-3">
                            <span class="d-block">
                                <i class="fas fa-exclamation-triangle text--danger"></i>
                                @lang('Rejected Reason')
                            </span>
                            <span class="text--dark">${data.admin_billdback}</span>
                        </div>
                    `)
                } else {
                    $('.reason').addClass('d-none')
                }

                var html = `
                    <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                            <span>@lang('Company Name')</span>
                            <span>${data?.company?.name}</span>
                    </li>
                    <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                            <span>@lang('Unique ID')</span>
                            <span>${data.unique_id || 'N/A'}</span>
                    </li>
                    <li class="list-group-item px-0 justify-content-between d-flex flex-wrap">
                            <span>@lang('Reference')</span>
                            <span>${data.reference || 'N/A'}</span>
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

                modal.find('.list-group').html(html);
                modal.modal('show');
            });

            $(".rejectModal").on('click', function(e) {
                const id = $(this).data('id');
                const action = "{{ route('admin.utility.bill.reject', ':id') }}";
                $('#rejectModal').find('form').attr('action', action.replace(":id", id));
                $('#rejectModal').modal('show');

            });
        })(jQuery);
    </script>
@endpush
