@extends($activeTemplate . 'layouts.master')
@section('content')
    <form method="POST" action="{{ route('user.virtual.card.store') }}" enctype="multipart/form-data" class="card-form no-submit-loader">
        <h4 class="mb-4">
            <a href="{{ route('user.virtual.card.list') }}">
                <span class="icon" title="@lang('Card List')">
                    <i class="las la-arrow-circle-left"></i>
                </span>
                {{ __($pageTitle) }}
            </a>
        </h4>
        <div class="row gy-4">
            <div class="col-xxl-5 col-lg-6">
                @csrf
                <div class="card custom--card">
                    <div class="card-body">
                        <div class="form--group">
                            <label class="form--label required">
                                @lang('Card Holder')
                            </label>
                            <span class="d-block fs-13 mb-3">
                                <i>
                                    @lang('Choose an existing cardholder if available, or create a new cardholder by providing the required information')
                                </i>
                            </span>
                            <div class="d-flex gap-3 flex-wrap">
                                <label for="card_holder_existing" class="single-operator d-flex flex-column flex-fill">
                                    <input id="card_holder_existing" type="radio" name="card_holder_type" value="{{ Status::VIRTUAL_CARD_HOLDER_EXISTING }}">
                                    <span class="img">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/man.png') }}" alt="">
                                    </span>
                                    <span class="title">@lang('Existing Card Holder')</span>
                                </label>
                                <label for="card_holder_new" class="single-operator d-flex flex-column flex-fill">
                                    <input id="card_holder_new" type="radio" name="card_holder_type" value="{{ Status::VIRTUAL_CARD_HOLDER_NEW }}">
                                    <span class="img">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/order.png') }}" alt="">
                                    </span>
                                    <span class="title">@lang('Create New Card Holder')</span>
                                </label>
                            </div>
                        </div>
                        <div class="form--group existing-card-holder">
                            <label class="form--label required">
                                @lang('Choose Card Holder')
                            </label>
                            <select name="card_holder" class="form-control select2 form--control">
                                <option value="" selected disabled>@lang('Select One')</option>
                                @foreach ($cardHolders as $cardHolder)
                                    <option value="{{ $cardHolder->id }}">
                                        {{ __($cardHolder->name) }} - {{ $cardHolder->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form--group">
                            <label class="form--label required">
                                @lang('Usability Type')
                            </label>
                            <span class="d-block fs-13 mb-3">
                                <i>
                                    @lang('Generate One-time or Reusable virtual cards. One-time cards expire after a single use, while Reusable cards stay active until expiry.')
                                </i>
                            </span>
                            <div class="d-flex gap-5 gap-md-2 flex-wrap">
                                <label for="usability_type_reuseable" class="single-operator d-flex flex-column flex-fill">
                                    <input id="usability_type_reuseable" type="radio" name="usability_type" value="{{ Status::VIRTUAL_CARD_REUSEABLE }}">
                                    <span class="img">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/recycle.png') }}" alt="">
                                    </span>
                                    <span class="title">@lang('Reusable')</span>
                                </label>
                                <label for="usability_type_onetime" class="single-operator d-flex flex-column flex-fill">
                                    <input id="usability_type_onetime" type="radio" name="usability_type" value="{{ Status::VIRTUAL_CARD_ONETIME }}">
                                    <span class="img">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/easy-to-use.png') }}" alt="">
                                    </span>
                                    <span class="title">@lang('One Time')</span>
                                </label>
                            </div>
                        </div>
                        <div class="form--group">
                            <button class="btn btn--base w-100">
                                <i class="fas fa-paper-plane"></i> @lang('Submit')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-7 col-lg-6  card-holder-information">
                <div class="card custom--card">
                    <div class="card-header d-flex gap-1  flex-column">
                        <h4 class="card-title mb-0">
                            @lang('Card Holder Information')
                        </h4>
                        <span class="fs-13 mb-2">
                            <i>
                                @lang('Please provide the correct information for creating a virtual card. Stripe verifies the submitted details using its own verification mechanism.')
                            </i>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form--group">
                                <label class="form--label required">@lang('Legal Name')</label>
                                <span class="fs-13 d-block">@lang('The full legal name of the cardholder as it appears on government-issued documents like a driver’s license or passport.')</span>
                            </div>

                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <input type="text" class="form-control form--control" name="first_name" value="{{ old('first_name') }}" placeholder="@lang('First Name')">
                            </div>
                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <input type="text" class="form-control form--control" name="last_name" value="{{ old('last_name') }}" placeholder="@lang('Last Name')">
                            </div>

                            <div class="form--group">
                                <label class="form--label required">@lang('Name on Card')</label>
                                <span class="fs-13 d-block">@lang('The name of an virtual cards. Once set,it cannot be changed.')</span>
                            </div>

                            <div class="col-xxl-12 form--group">
                                <input type="text" class="form-control form--control" name="card_name" value="{{ old('card_name') }}" placeholder="@lang('Card Name')">
                            </div>

                            <div class="form--group">
                                <label class="form--label required">@lang('Billing Address')</label>
                                <span class="fs-13 d-block">@lang('Billing address for all cards issued to the cardholder')</span>
                            </div>

                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <input type="text" class="form-control form--control" name="address" value="{{ old('address') }}" placeholder="@lang('Address')">
                            </div>

                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <input type="text" class="form-control form--control" name="city" value="{{ old('city') }}" placeholder="@lang('City')">
                            </div>
                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <input type="text" class="form-control form--control" name="state" value="{{ old('state') }}" placeholder="@lang('State')">
                            </div>
                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <input type="text" class="form-control form--control" name="zip_code" value="{{ old('zip_code') }}" placeholder="@lang('Postal Code')">
                            </div>
                            <div class="form--group">
                                <label class="form--label required">@lang('Contact Information')</label>
                                <span class="fs-13 d-block">@lang('Provide either an email address or a phone number for the cardholder. This is required for digital wallet support.')</span>
                            </div>
                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <input type="email" class="form-control form--control" name="email" value="{{ old('email') }}" placeholder="@lang('Email')">
                            </div>
                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <div class="input-group border-0 bg-light">
                                    <span class="input-group-text country-code">
                                        <x-country />
                                    </span>
                                    <input type="number" class="form--control form-control" placeholder="@lang('Enter mobile Number')" name="mobile_number" value="{{ old('mobile_number') }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="col-lg-12 form--group">
                                    <label class="form--label required">@lang('Date of Birth')</label>
                                    <div class="d-flex gap-2">
                                        <div class="d-flex w-50">
                                            <select class="form-control form--control form-select" name="birthday_month">
                                                <option selected disabled>@lang('Month')</option>
                                                <option value="1">@lang('January')</option>
                                                <option value="2">@lang('February')</option>
                                                <option value="3">@lang('March')</option>
                                                <option value="4">@lang('April')</option>
                                                <option value="5">@lang('May')</option>
                                                <option value="6">@lang('June')</option>
                                                <option value="7">@lang('July')</option>
                                                <option value="8">@lang('August')</option>
                                                <option value="9">@lang('September')</option>
                                                <option value="10">@lang('October')</option>
                                                <option value="11">@lang('November')</option>
                                                <option value="12">@lang('December')</option>
                                            </select>
                                        </div>
                                        <div class="d-flex">
                                            <input type="number" class="form-control form--control" name="birthday" placeholder="@lang('Day')" maxlength="2">
                                        </div>
                                        <div class="d-flex">
                                            <input type="text" class="form-control form--control" name="birthday_year" placeholder="@lang('Year')" maxlength="4">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form--group">
                                <label class="form--label required">@lang('Government-issued ID')</label>
                                <span class="fs-13 d-block">@lang('This information is used to confirm the cardholder identity')</span>
                            </div>
                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <label class="form--label required">@lang('Upload Front')</label>
                                <input type="file" class="form-control form--control" name="document_front">
                            </div>
                            <div class="col-xxl-6 col-xl-12 form--group col-md-6">
                                <label class="form--label required">@lang('Upload Back')</label>
                                <input type="file" class="form-control form--control" name="document_back">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            $('input[name=card_holder_type]').on('change', function() {
                const existingStatus = parseInt("{{ Status::VIRTUAL_CARD_HOLDER_EXISTING }}");
                const selectType = parseInt($(this).val());

                if (selectType === existingStatus) {
                    $(".card-holder-information").fadeOut();
                    $(".existing-card-holder").fadeIn();

                    $(".card-holder-information").find('input').attr('required', false);
                    $(".existing-card-holder").find('select[name=card_holder]').attr('required', true);
                } else {
                    $(".card-holder-information").fadeIn();
                    $(".existing-card-holder").fadeOut();

                    $(".card-holder-information").find('input').attr('required', true);

                    $(".existing-card-holder").find('select[name=card_holder]').attr('required', false);
                }

            });

            $('body').on('submit', ".card-form", function(e) {
                e.preventDefault();

                const $loader = $('.full-page-loader');
                const formData = new FormData($(this)[0]);
                const action = $(this).attr('action');
                const $this = $(this);
                const $submitBtn = $this.find(`button[type=submit]`);
                const oldHtml = $submitBtn.html();

                $.ajax({
                    type: "POST",
                    url: action,
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $loader.removeClass('d-none');
                    },
                    complete: function() {
                        $loader.addClass('d-none');
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            notify('success', response.message);
                            setTimeout(() => {
                                let redirectRoute =
                                    "{{ route('user.virtual.card.view', ':id') }}";
                                window.location = redirectRoute.replace(":id", response.data.card_id);
                            }, 1000);
                        } else {
                            notify('error', response.message || "@lang('Something went wrong')");
                        }
                    }
                });
            });

            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .single-operator .img {
            border: 0 !important;
        }

        .single-operator {
            gap: 0;
        }

        .single-operator .title {
            color: unset;
            font-size: unset;
        }

        .card-holder-information,
        .existing-card-holder {
            display: none;
        }
    </style>
@endpush
