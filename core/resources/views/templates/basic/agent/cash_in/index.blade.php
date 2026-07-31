@extends($activeTemplate . 'layouts.agent')
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
                        <th>@lang('User')</th>
                        <th>@lang('Transaction ID')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Amount | Commission')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cashIns as $cashIn)
                        <tr>

                            <td>
                                <div class="customer">
                                    <div class="customer__thumb">
                                        <img src="{{ @$cashIn->user->image_src }}" class="fit-image" alt="">
                                    </div>
                                    <div class="customer__content">
                                        <h6 class="customer__name">{{ __(@$cashIn->user->fullname) }}</h6>
                                        <a href="{{ route('agent.cash.in.history', ['search' => @$cashIn->user->mobile]) }}"
                                            class="customer__text"> {{ __(@$cashIn->user->mobile) }}</a>
                                    </div>
                                </div>

                            </td>
                            <td>
                                <span class="text--base fw-bold">
                                    {{ __($cashIn->trx) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    {{ showDateTime($cashIn->created_at) }}<br>
                                    {{ diffForHumans($cashIn->created_at) }}
                                </div>
                            </td>
                            <td>
                                <div>
                                    {{ showAmount($cashIn->amount) }}<br>
                                    <span class="text--success">{{ showAmount($cashIn->commission) }}</span>
                                </div>

                            </td>
                            <td>
                                <a href="{{ route('agent.cash.in.details', $cashIn->id) }}" class="btn btn--light btn--sm">
                                    <i class="fa fa-eye"></i> @lang('Details')
                                </a>
                            </td>

                        </tr>

                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>

            @if ($cashIns->hasPages())
                {{ paginateLinks($cashIns) }}
            @endif
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('agent.cash.in.create') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('Cash In')
    </a>
@endpush
