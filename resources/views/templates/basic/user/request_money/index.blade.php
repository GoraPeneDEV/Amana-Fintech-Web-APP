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
                        <th>@lang('Request To')</th>
                        <th>@lang('Transaction ID')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Amount')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requestMoneys as $requestMoney)
                        <tr>

                            <td>
                                <div class="customer">
                                    <div class="customer__thumb">
                                        <img src="{{ @$requestMoney->requestReceiver->image_src }}" class="fit-image"
                                            alt="">
                                    </div>
                                    <div class="customer__content">
                                        <h6 class="customer__name">{{ __(@$requestMoney->requestReceiver->fullname) }}</h6>
                                        <a href="{{ route('user.request.money.history', ['search' => @$requestMoney->requestReceiver->mobile]) }}"
                                            class="customer__text"> {{ __(@$requestMoney->requestReceiver->mobile) }}</a>
                                    </div>
                                </div>

                            </td>
                            <td>
                                <span class="text--base fw-bold">
                                    {{ __($requestMoney->trx) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    {{ showDateTime($requestMoney->created_at) }}<br>
                                    {{ diffForHumans($requestMoney->created_at) }}
                                </div>
                            </td>

                            <td>
                                <div>
                                    {{ showAmount($requestMoney->amount) }}<br>
                                </div>

                            </td>
                            <td>
                                @php echo $requestMoney->requestMoneyStatus @endphp
                            </td>
                            <td>
                                <a href="{{ route('user.request.money.details', $requestMoney->id) }}"
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

            @if ($requestMoneys->hasPages())
                {{ paginateLinks($requestMoneys) }}
            @endif
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex gap-3 flex-wrap">
        <a href="{{ route('user.request.money.create') }}" class="btn btn--base btn--md">
            <span class="icon"><i class="fa fa-plus-circle"></i></span>
            @lang('New Request Money')
        </a>
        <a href="{{ route('user.request.money.received.history') }}" class="btn btn-outline--base btn--md">
            <span class="icon"><i class="fa fa-list"></i></span>
            @lang('Received Requests')
        </a>
    </div>
@endpush
