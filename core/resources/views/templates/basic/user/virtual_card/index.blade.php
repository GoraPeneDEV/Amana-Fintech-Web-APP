@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-4">
        @forelse ($cards as $card)
            @php
                $bgClass = $loop->odd ? 'bg-one' : 'bg-two';
            @endphp
            <div class="col-lg-4 col-sm-6">
                <div class="virtual-card  w-100  {{ $bgClass }}">
                    <div class="virtual-card-body">
                        <div class="virtual-card-top">
                            <div class="left">
                                <span class="title">@lang('Balance')</span>
                                <h5 class="balanece">{{ showAmount($card->balance) }}</h5>
                            </div>
                            <div>
                                <div class="logo">
                                    <img src="{{ $card->brand_image_src }}" alt="">
                                </div>
                                <div class="mt-2">
                                    @php echo $card->statusBadge  @endphp
                                </div>
                            </div>
                        </div>
                        <div class="virtual-card-number">
                            <h5 class="text">
                                {{ printVirtualCardNumber($card) }}
                            </h5>
                            <a class="icon card-view-btn" type="button"
                                href="{{ route('user.virtual.card.view', $card->id) }}?bg={{ $bgClass }}">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <div class="virtual-card-footer">
                        <div class="virtual-card-info">
                            <span class="title">@lang('Card Name')</span>
                            <span class="text">{{ __(@$card->cardHolder->name) }}</span>
                        </div>
                        <div class="virtual-card-info">
                            <span class="title">@lang('Expiration Date')</span>
                            <span class="text">{{ $card->exp_month }}/{{ $card->exp_year }}</span>
                        </div>
                        <div class="virtual-card-info">
                            <span class="title">@lang('CVV')</span>
                            <span class="text">•••</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-message">
                    <p class="empty-message-icon">
                        <img src="{{ asset('assets/images/no-data.gif') }}" alt="">
                    </p>
                    <p class="empty-message-text">
                        <span class="d-block">@lang('No virtual card found.')</span>
                        <span class="d-block fs-13">@lang('No virtual card has been created yet. Please make your first virtual card by clicking the button below.')</span>
                        <a href="{{ route('user.virtual.card.new') }}" class="btn btn--base btn--md mt-4">
                            @lang('Create My First Card')
                        </a>
                    </p>
                </div>
            </div>
        @endforelse
    </div>
@endsection

@if ($cards->count())
    @push('breadcrumb-plugins')
        <a href="{{ route('user.virtual.card.new') }}" class="btn btn--base btn--md">
            <span class="icon"><i class="fa fa-plus-circle"></i></span>
            @lang('New Card')
        </a>
    @endpush
@endif
