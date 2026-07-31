@extends($activeTemplate . 'layouts.merchant')
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
                        <th>@lang('Amount')| @lang('Charge')</th>
                        <th>@lang('Post Balance')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                <div class="customer">
                                    <div class="customer__thumb">
                                        <img src="{{ @$payment->user->image_src }}" class="fit-image"
                                            alt="">
                                    </div>
                                    <div class="customer__content">
                                        <h6 class="customer__name">{{ __(@$payment->user->fullname) }}</h6>
                                        <a href="{{ route('merchant.payment.list', ['search' => @$payment->user->mobile]) }}"
                                            class="customer__text"> {{ __(@$payment->user->mobile) }}</a>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="text--base fw-bold">
                                    {{ __($payment->trx) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    {{ showDateTime($payment->created_at) }}<br>
                                    {{ diffForHumans($payment->created_at) }}
                                </div>
                            </td>

                            <td>
                                <div>
                                    {{ showAmount($payment->amount) }}<br>
                                    <span class="text--danger">{{ showAmount($payment->charge) }}</span>
                                </div>
                            </td>

                            <td>
                                {{ showAmount($payment->merchant_post_balance) }}
                            </td>

                            <td>
                                <a href="{{ route('merchant.payment.details', $payment->id) }}"
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

            @if ($payments->hasPages())
                {{ paginateLinks($payments) }}
            @endif
        </div>
    </div>
@endsection


