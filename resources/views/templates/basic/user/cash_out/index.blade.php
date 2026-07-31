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
                        <th>@lang('Agent')</th>
                        <th>@lang('Transaction ID')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Amount | Charge')</th>
                        <th>@lang('Action')</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($cashOuts as $cashOut)
                        <tr>
                            <td>
                                <div class="customer">
                                    <div class="customer__thumb">
                                        <img src="{{ @$cashOut->receiverAgent->image_src }}" class="fit-image"
                                            alt="">
                                    </div>
                                    <div class="customer__content">
                                        <h6 class="customer__name">{{ __(@$cashOut->receiverAgent->fullname) }}</h6>
                                        <a href="{{ route('user.cash.out.history', ['search' => @$cashOut->receiverAgent->mobile]) }}"
                                            class="customer__text"> {{ __(@$cashOut->receiverAgent->mobile) }}</a>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="text--base fw-bold">
                                    {{ __($cashOut->trx) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    {{ showDateTime($cashOut->created_at) }}<br>
                                    {{ diffForHumans($cashOut->created_at) }}
                                </div>
                            </td>

                            <td>
                                <div>
                                    {{ showAmount($cashOut->amount) }}<br>
                                    <span class="text--danger">{{ showAmount($cashOut->charge) }}</span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('user.cash.out.details', $cashOut->id) }}"
                                    class="btn btn--light btn--sm">
                                  <i class="fa fa-eye"></i>  @lang('Details')
                                </a>
                            </td>

                        </tr>

                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>

            @if ($cashOuts->hasPages())
                {{ paginateLinks($cashOuts) }}
            @endif
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('user.cash.out.create') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('New Cash Out')
    </a>
@endpush
