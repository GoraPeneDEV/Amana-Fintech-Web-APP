@extends($activeTemplate . 'layouts.agent')
@section('content')
<div class="card custom--card">
    <div class="card-header">
        <form class="table-search no-submit-loader">
            <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                placeholder="@lang('Search...')">
            <button class="icon px-3" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Gateway | Transaction')</th>
                    <th>@lang('Initiated')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Conversion')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdraws as $withdraw)
                    @php
                        $details = [];
                        foreach ($withdraw->withdraw_information ?? [] as $key => $info) {
                            $details[] = $info;
                            if ($info->type == 'file') {
                                $details[$key]->value = route(
                                    'agent.download.attachment',
                                    encrypt(getFilePath('verify') . '/' . $info->value),
                                );
                            }
                        }
                    @endphp
                    <tr>
                        <td>
                            <div>
                                <span class="fw-bold"><span class="text--base">
                                        {{ __(@$withdraw->method->name) }}</span></span>
                                <br>
                                <small>{{ $withdraw->trx }}</small>
                            </div>
                        </td>
                        <td class="text-end text-xl-center">
                            <div>
                                {{ showDateTime($withdraw->created_at) }} <br>
                                {{ diffForHumans($withdraw->created_at) }}
                            </div>
                        </td>
                        <td class="text-end text-xl-center">
                            <div>
                                {{ showAmount($withdraw->amount) }} - <span class="text--danger"
                                    data-bs-toggle="tooltip"
                                    title="@lang('Processing Charge')">{{ showAmount($withdraw->charge) }}
                                </span>
                                <br>
                                <strong data-bs-toggle="tooltip" title="@lang('Amount after charge')">
                                    {{ showAmount($withdraw->amount - $withdraw->charge) }}
                                </strong>
                            </div>
                        </td>
                        <td class="text-end text-xl-center">
                            <div>
                                {{ showAmount(1) }} =
                                {{ showAmount($withdraw->rate, currencyFormat: false) }}
                                {{ __($withdraw->currency) }}
                                <br>
                                <strong>{{ showAmount($withdraw->final_amount, currencyFormat: false) }}
                                    {{ __($withdraw->currency) }}</strong>
                            </div>
                        </td>
                        <td class="text-end text-xl-center">
                            <div>
                                @php echo $withdraw->statusBadge @endphp
                            </div>
                        </td>
                        <td>
                            <button class="btn btn--light btn--sm  detailBtn"
                                data-user_data="{{ json_encode($details) }}"
                                @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                                <i class="fas fa-eye"></i> @lang('Details')
                            </button>
                        </td>
                    </tr>
                @empty
                    @include('Template::partials.empty_message')
                @endforelse
            </tbody>
        </table>
        @if ($withdraws->hasPages())
            {{ paginateLinks($withdraws) }}
        @endif
    </div>
</div>

<div id="detailModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Details')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group userData mb-2 list-group-flush">
                </ul>
                <div class="feedback"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('agent.withdraw.index') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('New Withdraw')
    </a>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                    } else {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                        </li>`;
                    }
                });
                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);

                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            

        })(jQuery);
    </script>
@endpush
