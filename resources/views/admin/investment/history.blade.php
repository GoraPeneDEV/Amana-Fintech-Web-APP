@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            @include('admin.investment.widget')
            <x-admin.ui.card class="table-has-filter">
                <x-admin.ui.card.body :paddingZero="true">
                    <x-admin.ui.table.layout searchPlaceholder="Trx, username">
                        <x-admin.ui.table>
                            <x-admin.ui.table.header>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Plan Name')</th>
                                    <th>@lang('Interest Period')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Trx') | @lang('Time')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </x-admin.ui.table.header>
                            <x-admin.ui.table.body>
                                @forelse($investments as $investment)
                                    <tr>
                                        <td>
                                            <x-admin.other.user_info :user="$investment->user" />
                                        </td>
                                        <td>
                                            <div><span>{{ $investment->plan->name }}</span></div>
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
                                            <div class="text-end text-xl-center">
                                                @php echo $investment->statusBadge @endphp
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">{{ showAmount($investment->invest_amount) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block">
                                                    {{ $investment->trx }}
                                                </span>
                                                <span title="{{ diffForHumans($investment->created_at) }}">
                                                    {{ showDateTime($investment->created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                                <a href="{{ route('admin.investment.details', $investment->id) }}"
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
                        @if ($investments->hasPages())
                            <x-admin.ui.table.footer>
                                {{ paginateLinks($investments) }}
                            </x-admin.ui.table.footer>
                        @endif
                    </x-admin.ui.table.layout>
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>
@endsection
