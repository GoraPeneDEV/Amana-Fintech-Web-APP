@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="gift-card-wrapper">
        @forelse ($products as $product)
            <a class="gift-card" href="{{ route('user.gift.card.show', $product->id) }}">
                <div class="card-body text-center">
                    @if (!empty($product->logo_urls))
                        <img src="{{ $product->logo_urls[0] }}" alt="{{ $product->product_name }}">
                    @endif
                    <h6 class="card-name">
                        {{ strLimit($product->product_name, 20) }}
                    </h6>
                    <span class="mt-3 text--dark">
                        @if ($product->denomination_type == 'RANGE')
                            {{ getAmount($product->min_recipient_denomination) }}
                            {{ $product->recipient_currency_code }}
                            -
                            {{ getAmount($product->max_recipient_denomination) }}
                            {{ $product->recipient_currency_code }}
                        @else
                            @forelse (@$product->fixed_recipient_denominations ?? [] as $fix)
                                {{ getAmount($fix) }} {{ $product->recipient_currency_code }}
                                @if (!$loop->last)
                                    ,
                                @endif
                                @if ($loop->index == 3)
                                    @break
                                @endif
                            @empty
                                @lang('N/A')
                            @endforelse
                        @endif
                    </span>
                </div>
            </a>
        @empty
            <div class="empty-message">
                <p class="empty-message-icon">
                    <img src="{{ asset('assets/images/no-data.gif') }}" alt="">
                </p>
                <p class="empty-message-text">
                    <span class="d-block">@lang('No gift card found')</span>
                    <span class="d-block fs-13">@lang('There are no available data to display on this table at the moment.')</span>
                </p>
            </div>
        @endforelse
    </div>
    @if ($products->hasPages())
        <div class="mt-4">
            {{ paginateLinks($products) }}
        </div>
    @endif
@endsection

@push('breadcrumb-plugins')
    <form method="GET" class="d-flex gap-2 no-submit-loader align-items-center flex-wrap ">
        <div class="flex-fill">
            <select name="country_id" class="form-control select2 countryFilter">
                <option value="">@lang('All Countries')</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" @selected(request('country_id') == $country->id)>
                        {{ __($country->name) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-fill">
            <select name="category_id" class="form-control select2 categoryFilter">
                <option value="">@lang('All Categories')</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ __($category->name) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-fill">
            <div class="input-group">
                <input type="search" name="search" class="form-control form--control" value="{{ request('search') }}"
                    placeholder="@lang('Search...')">
                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
        <div class="flex-fill">
            <a class="btn btn--base" href="{{ route('user.gift.card.history') }}">
                <span class="icon" title="@lang('Gift History')">
                    <i class="las la-arrow-circle-left"></i>
                </span>
                @lang('Purchase History')
            </a>
        </div>
    </form>
@endpush

@push('style')
    <style>
        .gift-card img {
            max-height: 60px;
            display: block;
            text-align: center;
            margin: 0 auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .gift-card {
            border: 1px solid #e9e9e9;
            padding: 1rem;
            border-radius: 10px;
            background-color: #fff;
            transition: all .3s ease;
        }

        @media screen and (max-width: 575px) {
            .gift-card {
                padding: 0.5rem;
            }
        }

        .gift-card .card-name {
            font-weight: 500;
        }

        .gift-card:hover {
            background-color: hsl(var(--black)/0.01);
        }

        .gift-card:hover .card-name {
            color: hsl(var(--base));
        }

        .gift-card-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 12px;
            width: 100%;
        }

        @media screen and (max-width: 991px) {
            .gift-card-wrapper {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        @media screen and (max-width: 991px) {
            .gift-card-wrapper {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            }
        }

        @media screen and (max-width: 991px) {
            .gift-card-wrapper {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        @media screen and (max-width: 575px) {
            .gift-card-wrapper {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            }
        }

        @media screen and (max-width: 400px) {
            .gift-card-wrapper {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }
        }

        .select2+.select2-container .select2-selection,
        .img-select2+.select2-container .select2-selection {
            border: 0;
            background-color: #fff !important;
            width: 100%;
        }

        .breadcrumb-plugins-wrapper .select2-dropdown {
            min-width: 200px;
        }

        @media screen and (max-width: 400px) {

            .no-submit-loader,
            .breadcrumb-plugins-wrapper {
                flex-direction: column !important;
            }

            .breadcrump-wrapper-inner {
                width: 100%;
            }

            .breadcrump-wrapper-inner .flex-fill {
                width: 100% !important;
            }

            .breadcrump-wrapper-inner .btn.btn--base {
                width: 100%;
            }

        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $(".countryFilter, .categoryFilter").on('change', function() {
                $(this).closest('form').submit();
            })
        })(jQuery);
    </script>
@endpush
