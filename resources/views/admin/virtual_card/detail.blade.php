@extends('admin.layouts.app')
@section('panel')
@include('admin.virtual_card.widget')
    <div class="row gy-4">
        <div class="col-xxl-4 col-xl-6 col-md-6 ">
            <x-admin.ui.card class="h-100">
                <x-admin.ui.card.header>
                    <h4 class="card-title">@lang('User Information') </h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Name')</span>
                            <span class="fs-14">{{ __(@$card->user->full_name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Username')</span>
                            <span class="fs-14"><a
                                    href="{{ route('admin.users.detail', $card->user_id) }}">{{ __('@' . @$card->user->username) }}</a></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Mobile Number')</span>
                            <span class="fs-14">{{ @$card->user->mobile }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Address')</span>
                            <span class="fs-14">{{ __(@$card->user->address) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('City')</span>
                            <span class="fs-14">{{ __(@$card->user->city) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('State')</span>
                            <span class="fs-14">{{ __(@$card->user->state) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Zip Code')</span>
                            <span class="fs-14">{{ __(@$card->user->zip) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Country')</span>
                            <span class="fs-14">{{ __(@$card->user->country_name) }}</span>
                        </li>

                    </ul>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>

        <div class="col-xxl-4 col-xl-6 col-md-6 ">
            <x-admin.ui.card class="h-100">
                <x-admin.ui.card.header>
                    <h4 class="card-title">@lang('Card Holder Information') </h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body>
                    <ul class="list-group list-group-flush">

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Card Holder ID')</span>
                            <span class="fs-14">{{ __(@$card->cardHolder->card_holder_id) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Name')</span>
                            <span class="fs-14">{{ __(@$card->cardHolder->name) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Email')</span>
                            <span class="fs-14">{{ __(@$card->cardHolder->email) }}</span>
                        </li>


                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Mobile Number')</span>
                            <span class="fs-14">{{ @$card->cardHolder->phone_number }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Address')</span>
                            <span class="fs-14">{{ __(@$card->cardHolder->address) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('State')</span>
                            <span class="fs-14">{{ __(@$card->cardHolder->state) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Postal Code')</span>
                            <span class="fs-14">{{ __(@$card->cardHolder->postal_code) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Country')</span>
                            <span class="fs-14">{{ __(@$card->cardHolder->country) }}</span>
                        </li>

                    </ul>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
        <div class="col-xxl-4 col-xl-12 col-md-">
            <x-admin.ui.card class="h-100">
                <x-admin.ui.card.header>
                    <h4 class="card-title">@lang('Card Information') </h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Card Number')</span>
                            <span class="fs-14">{{ printVirtualCardNumber($card) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Expire Month')</span>
                            <span class="fs-14">{{ $card->exp_month }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Expire Year')</span>
                            <span class="fs-14">{{ $card->exp_year }}</span>
                        </li>


                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Card Type')</span>
                            <span class="fs-14">{{ __($card->card_type) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Brand')</span>
                            <span class="fs-14">{{ __($card->brand) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Balance')</span>
                            <span class="fs-14 text--primary">{{ showAmount($card->balance) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Created At')</span>
                            <span class="fs-14">{{ showDateTime($card->created_at) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span class="fs-14 text-muted">@lang('Status')</span>
                            <span class="fs-14">@php echo $card->statusBadge  @endphp</span>
                        </li>
                    </ul>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
        <div class="col-12">
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.header>
                    <h4 class="card-title">@lang('Latest Transactions') </h4>
                </x-admin.ui.card.header>
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx"
                        filterBoxLocation="reports.transaction_filter_form">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            <strong>{{ $trx->trx }}</strong>
                                        </td>
                                        <td>
                                            {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                                {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ showAmount($trx->post_balance) }}
                                        </td>

                                        <td>{{ __($trx->details) }}</td>
                                    </tr>
                                @empty
                                    <x-admin.ui.table.empty_message />
                                @endforelse
                            </x-admin.ui.table.body>
                        </x-admin.ui.table>
                        @if ($transactions->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($transactions) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
