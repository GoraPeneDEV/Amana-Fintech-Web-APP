@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header">
            <form class="table-search no-submit-loader">
                <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}" placeholder="@lang('Search...')">
                <button class="icon px-3" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table--responsive--xl">
                <thead>
                    <tr>
                        <th>@lang('Trx') | @lang('Time')</th>
                        <th>@lang('Product') | @lang('Quantity')</th>
                        <th>@lang('Unit Price')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($giftCardPurchases as $giftCard)
                        <tr>
                            <td>
                                <div>
                                    <span class="d-block">{{ $giftCard->trx }}</span>
                                    <span title="{{ diffForHumans($giftCard->created_at) }}">
                                        {{ showDateTime($giftCard->created_at) }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="d-block">{{ __(@$giftCard->giftCard->product_name) }}</span>
                                    <span title="@lang('Quantity')">@lang('Quantity: ')
                                        {{ $giftCard->quantity }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="d-block">
                                        {{ getAmount($giftCard->amount) }} {{@$giftCard->giftCard->recipient_currency_code}}</span>
                                    </span>
                                    <span>
                                        {{ getAmount(@$giftCard->rate * $giftCard->amount) }} {{@$giftCard->giftCard->sender_currency_code}}</span>
                                    </span>
                                </div>
                            </td>
                          
                            <td>
                                @php echo $giftCard->statusBadge @endphp
                            </td>
                            <td>
                                <a href="{{ route('user.gift.card.details', $giftCard->id) }}" class="btn btn--light btn--sm">@lang('Details')</a>
                            </td>
                        </tr>
                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>
            @if ($giftCardPurchases->hasPages())
                {{ paginateLinks($giftCardPurchases) }}
            @endif
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.gift.card.create') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('New Gift Card')
    </a>
@endpush
