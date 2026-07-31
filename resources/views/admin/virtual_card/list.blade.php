@extends('admin.layouts.app')
@section('panel')
    <x-admin.ui.card>
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout :renderTableFilter="false">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Card Holder')</th>
                            <th>@lang('Card Number')</th>
                            <th>@lang('Card Brand')</th>
                            <th>@lang('Card Balance')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($cards as $card)
                            <tr>
                                <td>
                                    <x-admin.other.user_info :user="$card->user" />
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap  flex-column">
                                        <span>
                                            {{ __(@$card->cardHolder->name) }}
                                        </span>
                                        <span>
                                            {{ __(@$card->cardHolder->phone_number) }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ printVirtualCardNumber($card) }}</td>
                                <td>
                                    <img src="{{ $card->brand_image_src }}" alt="" class="mw-100">
                                </td>
                                <td>{{ showAmount($card->balance) }}</td>
                                <td>@php echo $card->statusBadge @endphp </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        <a href="{{ route('admin.virtual.card.detail', $card->id) }}"
                                            class=" btn btn-outline--primary">
                                            <i class="las la-info-circle"></i>
                                            @lang('Details')
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($cards->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($cards) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>
@endsection
