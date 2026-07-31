@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header">
            <form class="table-search no-submit-loader">
                <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                    placeholder="@lang('Search by transactions')">
                <button class="icon px-3" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table--responsive--xl">
                <thead>
                    <tr>
                        <th>@lang('Plan | Transaction')</th>
                        <th>@lang('Interest Period')</th>
                        <th>@lang('Amount')</th>
                        <th>@lang('Initiated')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($investments as $investment)
                        <tr>
                            <td>
                                <div class="text-start">
                                    <span class="fw-bold">
                                        <span class="text-primary">
                                            {{ __($investment->plan->name) }}
                                        </span>
                                    </span>
                                    <br>
                                    <small> {{ __($investment->trx) }} </small>
                                </div>
                            </td>
                            <td>
                                <div class="text-end text-xl-center">
                                    {{ __($investment->plan->investTime->name) }}
                                    <br>
                                    @if ($investment->total_repeat == Status::LIFETIME)
                                        @lang('Lifetime Repeat')
                                    @else
                                        {{ __($investment->total_repeat) }} @lang('Times')
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ showAmount($investment->invest_amount) }}
                            </td>
                            <td>
                                <div class="text-end text-xl-center">
                                    {{ showDateTime($investment->created_at) }}<br>{{ diffForHumans($investment->created_at) }}
                                </div>
                            </td>
                            <td>
                                <div class="text-end text-xl-center">
                                    @php echo $investment->statusBadge @endphp
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('user.investment.details', $investment->id) }}"
                                    class="btn btn--light btn--sm">
                                    <i class="fa fa-eye"></i> @lang('Details')
                                </a>
                            </td>
                        </tr>
                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>
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
    <a href="{{ route('user.investment.all') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('New Investment')
    </a>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        } else {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <span>${element.name}</span>
                                <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                            </li>`;
                        }
                    });
                }

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
