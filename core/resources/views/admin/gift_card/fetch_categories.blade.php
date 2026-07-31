@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout :renderTableFilter="false">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="check-all">
                                        <label for="check-all" class="ms-1 mb-0">@lang('Name')</label>
                                    </th>
                                    <th>@lang('Unique Id')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @php
                                    $counter = 0;
                                @endphp
                                @foreach ($apiCategories as $item)
                                    @php
                                        $item = (object) $item;
                                    @endphp
                                    @if (!in_array($item->id, $existingCategoryUniqueIds))
                                        @php
                                            $counter++;
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="categories[]" value="{{ $item->name }}"
                                                    id="category-{{ $item->name }}" form="confirmation-form"
                                                    class="name">
                                                <label for="category-{{ $item->name }}"
                                                    class="ms-1 mb-0">{{ $item->name }}</label>
                                            </td>
                                            <td>{{ $item->id }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if ($counter == 0)
                                    <x-admin.ui.table.empty_message />
                                @endif
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <button type="button" class="my-5 btn btn--sm btn-outline--primary d-none confirmationBtn"
                data-question="@lang('Are you sure to add this categories?')" data-action="{{ route('admin.gift.card.categories.save') }}"> <i
                    class="lab la-telegram-plane"></i>@lang('Add Selected Categories')</button>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-sm btn--success  confirmationBtn disabled me-2" disabled
        data-question="@lang('Are You sure to add these selected categories?')" data-action="{{ route('admin.gift.card.categories.save') }}">
        <i class="lab la-telegram-plane me-1"></i> @lang('Add Selected Categories')
    </button>
    <x-back_btn route="{{ route('admin.gift.card.category.all') }}" />
@endpush

@push('script')
    <script>
        "use strict";

        (function($) {

            $("#check-all").on('click', function() {
                if ($(this).is(':checked')) {
                    $(".name").prop('checked', true);
                } else {
                    $(".name").prop('checked', false);
                }
                updateDOM();
            });

            $(".name").on('change', function() {
                updateDOM();
            })

            function updateDOM() {
                if ($('.name:checked').length > 0) {
                    $('.confirmationBtn').removeClass('disabled').attr('disabled', false);
                } else {
                    $('.confirmationBtn').addClass('disabled').attr('disabled', true);
                }
            }

        })(jQuery);
    </script>
@endpush
