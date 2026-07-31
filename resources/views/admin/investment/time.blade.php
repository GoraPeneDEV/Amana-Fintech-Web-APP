@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search invest time">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Time')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($investTimes as $investTime)
                            <tr>
                                <td>
                                    {{ __($investTime->name) }}
                                </td>
                                <td>
                                    {{ $investTime->time }} @lang('Hours')
                                </td>
                                <td>
                                    <x-admin.other.status_switch :status="$investTime->status" :action="route('admin.investment.time.status', $investTime->id)" title="invest time" />
                                </td>
                                <td>
                                    <x-admin.ui.btn.edit class="editBtn" data-resource="{{ json_encode($investTime) }}" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($investTimes->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($investTimes) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>

    <x-admin.ui.modal id="settingModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title"></h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="{{ route('admin.investment.time.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label">@lang('Time Name')</label>
                            <div class="input-group input--group">
                                <input class="form-control" type="text" name="name" value="{{ old('name') }}"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label">@lang('Time in Hours')</label>
                            <span title="@lang('Enter the duration of the investment in hours.')">
                                <i class="las la-info-circle"></i>
                            </span>
                            <div class="input-group input--group">
                                <input type="number" class="form-control" name="time" value="{{ old('time') }}"
                                    required>
                                <div class="input-group-text">@lang('Hours')</div>
                            </div>
                        </div>
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
            let modal = $("#settingModal");
            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add New Investment Time')`);
                modal.find('form').attr('action', "{{ route('admin.investment.time.store') }}");
                modal.find('form').trigger('reset');
                modal.modal('show');
            });

            $(".editBtn").on('click', function(e) {
                const resource = $(this).data('resource');
                modal.find('.modal-title').text(`@lang('Edit Investment Time')`);
                modal.find('form').attr('action', "{{ route('admin.investment.time.store') }}");
                const actionUrl = "{{ route('admin.investment.time.update', ':id') }}".replace(':id', resource
                    .id);

                modal.find('form').attr('action', actionUrl);
                modal.find('input[name="name"]').val(resource.name);
                modal.find('input[name="time"]').val(resource.time);
                modal.modal('show');
            });


        })(jQuery);
    </script>
@endpush
