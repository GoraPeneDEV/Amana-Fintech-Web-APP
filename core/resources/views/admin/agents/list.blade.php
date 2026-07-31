@extends('admin.layouts.app')
@section('panel')
    @include('admin.agents.widget')
    <x-admin.ui.card class="table-has-filter">
        <x-admin.ui.card.body :paddingZero="true">
            <x-admin.ui.table.layout searchPlaceholder="Search agents" filterBoxLocation="agents.filter">
                <x-admin.ui.table>
                    <x-admin.ui.table.header>
                        <tr>
                            <th>@lang('Agent')</th>
                            <th>@lang('Email-Mobile')</th>
                            <th>@lang('Country')</th>
                            <th>@lang('Joined At')</th>
                            <th>@lang('Balance')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </x-admin.ui.table.header>
                    <x-admin.ui.table.body>
                        @forelse($agents as $agent)
                            <tr>
                                <td>
                                    <x-admin.other.agent_info :agent="$agent" />
                                </td>
                                <td>
                                    <div>
                                        <strong class="d-block">
                                            {{ $agent->email }}
                                        </strong>
                                        <small>{{ $agent->mobileNumber }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold" title="{{ @$agent->country_name }}">
                                            {{ $agent->country_code }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong class="d-block ">{{ showDateTime($agent->created_at) }}</strong>
                                        <small class="d-block"> {{ diffForHumans($agent->created_at) }}</small>
                                    </div>
                                </td>
                                <td>{{ showAmount($agent->balance) }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        <x-admin.ui.btn.details :href="route('admin.agents.detail', $agent->id)" />
                                        @if (request()->routeIs('admin.agents.kyc.pending'))
                                            <a href="{{ route('admin.agents.kyc.details', $agent->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline--dark">
                                                <i class="las la-user-check"></i> @lang('KYC Data')
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.ui.table.empty_message />
                        @endforelse
                    </x-admin.ui.table.body>
                </x-admin.ui.table>
                @if ($agents->hasPages())
                    <x-admin.ui.table.footer>
                        {{ paginateLinks($agents) }}
                    </x-admin.ui.table.footer>
                @endif
            </x-admin.ui.table.layout>
        </x-admin.ui.card.body>
    </x-admin.ui.card>
@endsection
