@extends($activeTemplate . 'layouts.master')
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
                        <th>@lang('Mobile Number') | @lang('Operator')</th>
                        <th>@lang('Trx') | @lang('Time')</th>
                        <th>@lang('Amount') | @lang('Charge')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topUps as $topUp)
                        <tr>
                            <td>
                                <div>
                                    <span class="d-block">
                                        <span class="me-1">{{ @$topUp->dial_code }}</span>
                                        <span>{{ @$topUp->mobile_number }}</span>
                                    </span>
                                    <span class="d-block">
                                        {{ __(@$topUp->operator->name) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="d-block">{{ $topUp->trx }}</span>
                                    <span title="{{ diffForHumans($topUp->created_at) }}">
                                        {{ showDateTime($topUp->created_at) }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="d-block">{{ showAmount($topUp->amount) }}</span>
                                    <span class="text--success" title="@lang('Charge')">
                                        {{ showAmount($topUp->charge) }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>
            @if ($topUps->hasPages())
                {{ paginateLinks($topUps) }}
            @endif
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.airtime.create') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('New TopUp')
    </a>
@endpush
