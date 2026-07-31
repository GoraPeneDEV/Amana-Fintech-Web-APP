@extends('admin.layouts.app')
@section('panel')
    @php
        $showUserType = request()->routeIs('admin.report.notification.history');
    @endphp
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Search Username" filterBoxLocation="reports.filter_form">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th> {{ $userType != 'all' ? ucfirst(__($userType)) : __('User') }}</th>
                                    @if ($userType == 'all')
                                        <th>@lang('User Type')</th>
                                    @endif
                                    <th>@lang('Sent')</th>
                                    <th>@lang('Sender')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            @if ($log->user_id != 0)
                                                <x-admin.other.user_info :user="$log->user" />
                                            @elseif($log->agent_id != 0)
                                                <x-admin.other.agent_info :agent="$log->agent" />
                                            @elseif($log->merchant_id != 0)
                                                <x-admin.other.merchant_info :merchant="$log->merchant" />
                                            @endif
                                        </td>
                                        @if ($userType == 'all')
                                        <td>
                                            @if ($log->user_id != 0)
                                                @lang('User')
                                            @elseif ($log->agent_id != 0)
                                                @lang('Agent')
                                            @elseif ($log->merchant_id != 0)
                                                @lang('Merchant')
                                            @else
                                                @lang('N/A')
                                            @endif
                                        </td>
                                    @endif
                                        <td>
                                            {{ showDateTime($log->created_at) }}
                                            <br>
                                            {{ diffForHumans($log->created_at) }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ keyToTitle($log->notification_type) }}</span> <br>
                                            @lang('via') {{ __($log->sender) }}
                                        </td>
                                        <td>
                                            @if ($log->subject)
                                                {{ __($log->subject) }}
                                            @else
                                                @lang('N/A')
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->notification_type == 'email')
                                                <button class="btn  btn-outline--primary notifyDetail"
                                                    data-type="{{ $log->notification_type }}"
                                                    data-message="{{ route('admin.report.email.details', $log->id) }}"
                                                    data-sent_to="{{ $log->sent_to }}">
                                                    <i class="las la-info-circle"></i>
                                                    @lang('Detail')
                                                </button>
                                            @else
                                                <button class="btn  btn-outline--primary notifyDetail"
                                                    data-type="{{ $log->notification_type }}"
                                                    data-message="{{ $log->message }}"
                                                    data-image="{{ asset(getFilePath('push') . '/' . $log->image) }}"
                                                    data-sent_to="{{ $log->sent_to }}">
                                                    <i class="las la-info-circle"></i>
                                                    @lang('Detail')
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($logs->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($logs) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="notifyDetailModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Notification Details')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <h3 class="text-center mb-3">@lang('To'): <span class="sent_to"></span></h3>
            <div class="detail"></div>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>
@endsection


@push('script')
    <script>
        $('.notifyDetail').on('click', function() {
            var message = ''
            if ($(this).data('image')) {
                message += `<img src="${$(this).data('image')}" class="w-100 mb-2" alt="image">`;
            }
            message += $(this).data('message');
            var sent_to = $(this).data('sent_to');
            var modal = $('#notifyDetailModal');
            if ($(this).data('type') == 'email') {
                var message = `<iframe src="${message}" height="500" width="100%" title="Iframe Example"></iframe>`
            }
            $('.detail').html(message)
            $('.sent_to').text(sent_to)
            modal.modal('show');
        });
    </script>
@endpush
