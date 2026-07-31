@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <x-admin.ui.card>
                <x-admin.ui.card.body :paddingZero=true>
                    <x-admin.ui.table.layout :renderExportButton="true">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            {{ __($category->name) }}
                                        </td>
                                        <td>
                                            <x-admin.other.status_switch :status="$category->status" :action="route('admin.gift.card.category.status', $category->id)"
                                                title="Category" />
                                        </td>
                                        <td>
                                            <x-admin.ui.btn.edit class="editBtn" data-category="{{ $category }}" />
                                        </td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($categories->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($categories) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="categoryModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Edit Category')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="" method="POST">
                @csrf
                <div class="form-group ">
                    <label>@lang('Name')</label>
                    <input class="form-control" type="text" name="name" required>
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
    <x-admin.ui.btn.add text="Fetch More Categories" :href="route('admin.gift.card.fetch.categories')" />
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            let modal = $("#categoryModal");

            $(".editBtn").on('click', function(e) {

                const category = $(this).data('category');

                modal.find('.modal-title').text("@lang('Edit Category')");
                const actionUrl = "{{ route('admin.gift.card.edit.category', ':id') }}".replace(':id',
                    category.id);
                modal.find('form').attr('action', actionUrl);
                modal.find('input[name="name"]').val(category.name);
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
