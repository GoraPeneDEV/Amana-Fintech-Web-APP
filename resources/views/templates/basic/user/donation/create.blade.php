@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('user.donation.history') }}">
            <span class="icon" title="@lang('Donation History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __($pageTitle) }}
        </a>
    </h4>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <form action="{{ route('user.donation.store') }}" method="post"
                class="send-money-form has-otp-form no-submit-loader">
                @csrf
                <div class="short-wrapper mx-0">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="card custom--card p-0 overflow-hidden mb-4">
                                <div class="card-header p-3 p-sm-4">
                                    <div class="table-search">
                                        <input type="text" class="form--control search-organization"
                                            placeholder="@lang('Search for organization')">
                                        <button class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path
                                                    d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M20.9992 21.0002L16.6992 16.7002" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                @if ($latestDonation->count() > 0)
                                    <div class="card-body px-3 px-sm-4 mb-4">
                                        <h6 class="mt-3">@lang('Recently Donated Organization')</h6>
                                        <div class="bill-select-wrapper">
                                            @forelse ($latestDonation as $donation)
                                                <div class="bill-select-item"
                                                    data-org-name="{{ __($donation->donationFor->name) }}"
                                                    data-org-image="{{ getImage(getFilePath('donation') . '/' . $donation->donationFor->image) }}"
                                                    data-org-id="{{ $donation->id }}">
                                                    <div class="bill-select">
                                                        <span class="left">
                                                            <span class="thumb">
                                                                <img src="{{ getImage(getFilePath('donation') . '/' . $donation->donationFor->image) }}"
                                                                    alt="">
                                                            </span>
                                                            <span class="content">
                                                                <span
                                                                    class="title">{{ __($donation->donationFor->name) }}</span>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                            @empty
                                                @include('Template::partials.empty_message')
                                            @endforelse
                                        </div>
                                        <div class="no-results">
                                            @include('Template::partials.empty_message')
                                        </div>

                                    </div>
                                @endif
                            </div>

                            <div class="card custom--card p-0 overflow-hidden">
                                <div class="card-body px-3 px-sm-4">
                                    <h6 class="mt-3">@lang('All Organizations')</h6>
                                    <div class="bill-select-wrapper">
                                        @forelse($allOrganization as $organization)
                                            <div class="bill-select-item" data-org-name="{{ __($organization->name) }}"
                                                data-org-image="{{ getImage(getFilePath('donation') . '/' . $organization->image) }}"
                                                data-org-id="{{ $organization->id }}">

                                                <div class="bill-select">
                                                    <span class="left">
                                                        <span class="thumb">
                                                            <img src="{{ getImage(getFilePath('donation') . '/' . $organization->image) }}"
                                                                alt="">
                                                        </span>
                                                        <span class="content">
                                                            <span class="title">{{ __($organization->name) }}</span>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            @include('Template::partials.empty_message')
                                        @endforelse
                                    </div>
                                    <div class="no-results">
                                        @include('Template::partials.empty_message')
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="col-md-6">
                            <div class="card custom--card mb-3 organization-details">
                                <div class="card-body">
                                    <h5 class="mb-3">@lang('Organization')</h5>
                                    <div class="bill-select select">
                                        <span class="left">
                                            <span class="thumb">
                                                <img class="organization-image" src="" alt="">
                                            </span>
                                            <span class="content">
                                                <span class="organization-title title"></span>
                                            </span>
                                        </span>
                                    </div>
                                    <input type="hidden" name="charity_id" class="organization-id">
                                </div>
                            </div>
                            <div class="card custom--card mb-3">
                                <div class="card-body">
                                    <h5 class="mb-3">@lang('Sender Details')</h5>
                                    <div class="form--group">
                                        <label class="form--label">@lang('Name')</label>
                                        <input type="text" class="form--control form-control"
                                            placeholder="@lang('e.g. John Doe')" name="name" value="{{ old('name') }}">
                                    </div>
                                    <div class="form--group">
                                        <label class="form--label">@lang('Email')</label>
                                        <input type="email" class="form--control form-control"
                                            placeholder="@lang('e.g. johndoe@mail.com')" name="email" value="{{ old('email') }}">
                                    </div>
                                    <div class="form--group">
                                        <div class="d-flex gap-1 justify-content-between  flex-wrap">
                                            <label class="form--label">@lang('Enter Amount')</label>
                                            <span>
                                                @lang('Available Balance'):
                                                {{ gs('cur_sym') }}{{ showAmount(auth()->user()->balance, currencyFormat: false) }}
                                            </span>
                                        </div>
                                        <div class="input-group input--amount border-0">
                                            <input type="number" class="form--control sm-style form-control"
                                                placeholder="@lang('0.00')" value="{{ old('amount') }}"
                                                name="amount">
                                            <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                        </div>
                                        <div class="flex-align gap-2 mt-3">
                                            @foreach (gs('quick_amounts') ?? [] as $amount)
                                                <span class="suggest-amount quick-amount"
                                                    data-amount="{{ getAmount($amount) }}">
                                                    {{ gs('cur_sym') }}{{ showAmount($amount, currencyFormat: false) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form--group">
                                        <label class="form--label">@lang('Reference (Optional)')</label>
                                        <input type="text" class="form--control form-control"
                                            placeholder="@lang('Enter reference')" name="reference"
                                            value="{{ old('reference') }}">
                                    </div>

                                    <div class="form--group">
                                        <div class="flex-align gap-2 lh-1">
                                            <div class="form--radio">
                                                <input id="disclose" type="radio" name="hide_identity"
                                                    class="form-check-input mt-0" value="1">
                                            </div>
                                            <label class="fw-medium" for="disclose">
                                                @lang('Don’t want to disclose my Identity')
                                            </label>
                                        </div>
                                    </div>
                                    <x-otp_verification remark="donation" />
                                </div>
                            </div>


                            <button type="submit" class="btn btn--base text-start w-100">
                                <span class="flex-between">
                                    @lang('Continue')
                                    <span class="icon">
                                        <i class="fas fa-arrow-right-long"></i>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
@push('style')
    <style>
        .organization-details {
            display: none;
        }

        .no-results {
            display: none;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            $('.bill-select-item').on('click', function() {
                var organizationName = $(this).data('org-name');
                var organizationImage = $(this).data('org-image');
                var organizationId = $(this).data('org-id');
                $('.organization-title').text(organizationName);
                $('.organization-image').attr('src', organizationImage);
                $('.organization-id').val(organizationId);
                $('.organization-details').fadeIn();
            });

            $(".search-organization").on("input", function() {
                let filter = $(this).val().toLowerCase();
                let visibleCount = 0;

                $(".bill-select-item").each(function() {
                    let name = $(this).data("org-name").toLowerCase();
                    let isVisible = name.includes(filter);
                    $(this).toggle(isVisible);
                    if (isVisible) visibleCount++;
                });
                if (visibleCount === 0) {
                    $(".no-results").show();
                } else {
                    $(".no-results").hide();
                }
            });

            $(".quick-amount").on('click', function() {
                $("input[name=amount]").val(parseInt($(this).data("amount")));
            });

            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
