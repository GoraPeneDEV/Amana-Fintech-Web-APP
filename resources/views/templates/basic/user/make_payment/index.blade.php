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
                        <th>@lang('Merchant')</th>
                        <th>@lang('Transaction ID')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Amount')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($makePayments as $makePayment)
                        <tr>
                            <td>
                                <div class="customer">
                                    <div class="customer__thumb">
                                        <img src="{{ @$makePayment->merchant->image_src }}" class="fit-image"
                                            alt="">
                                    </div>
                                    <div class="customer__content">
                                        <h6 class="customer__name">{{ __(@$makePayment->merchant->fullname) }}</h6>
                                        <a href="{{ route('user.make.payment.history', ['search' => @$makePayment->merchant->mobile]) }}"
                                            class="customer__text"> {{ __(@$makePayment->merchant->mobile) }}</a>
                                    </div>
                                </div>

                            </td>
                            <td>
                                <span class="text--base fw-bold">
                                    {{ __($makePayment->trx) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    {{ showDateTime($makePayment->created_at) }}<br>
                                    {{ diffForHumans($makePayment->created_at) }}
                                </div>
                            </td>

                            <td>
                                {{ showAmount($makePayment->amount) }}
                            </td>
                            <td>
                                <a href="{{ route('user.make.payment.details', $makePayment->id) }}"
                                    class="btn btn--light btn--sm">
                                   <i class="fa fa-eye"></i> @lang('Details')
                                </a>
                            </td>
                        </tr>
                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>

            @if ($makePayments->hasPages())
                {{ paginateLinks($makePayments) }}
            @endif
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('user.make.payment.create') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('New Make Payment')
    </a>
@endpush
