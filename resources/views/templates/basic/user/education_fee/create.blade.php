@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('user.education.fee.history') }}">
            <span class="icon" title="@lang('Education Fee History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __($pageTitle) }}
        </a>
    </h4>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="bill-type-wrapper mb-4">
                @foreach ($categories as $category)
                    <div class="bill-type bg-white" data-category-id="{{ $category->id }}">
                        <div class="thumb bg-base">
                            <img src="{{ getImage(getFilePath('category') . '/' . $category->image) }}" alt="">
                        </div>
                        <span class="text">{{ __($category->name) }}</span>
                    </div>
                @endforeach
            </div>
            <form action="{{ route('user.education.fee.store') }}" method="post"
                class="send-money-form has-otp-form no-submit-loader">
                @csrf
                <div class="short-wrapper mx-0">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="card custom--card p-0 overflow-hidden mb-4">
                                <div class="card-header p-3 p-sm-4">
                                    <div class="table-search">
                                        <input type="text" class="form--control search-institute"
                                            placeholder="@lang('Search for institute')">
                                        <button class="icon" type="button">
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
                            </div>

                            <div class="card custom--card p-0 overflow-hidden">
                                <div class="card-body px-3 px-sm-4">
                                    <h6 class="mt-3">@lang('All Institutions')</h6>
                                    <div class="bill-select-wrapper">
                                        @forelse($institutions as $institute)
                                            <div class="bill-select-item" data-institute-name="{{ __($institute->name) }}"
                                                data-institute-image="{{ getImage(getFilePath('education_fee') . '/' . $institute->image) }}"
                                                data-institute-id="{{ $institute->id }}"
                                                data-category-id="{{ $institute->category_id }}"
                                                data-category-name="{{ __(@$institute->category->name) }}"
                                                data-form-id="{{ __(@$institute->form_id) }}"
                                                data-fixed-charge="{{ $institute->fixed_charge }}"
                                                data-percent-charge="{{ $institute->percent_charge }}">
                                                <div class="bill-select">
                                                    <span class="left">
                                                        <span class="thumb">
                                                            <img src="{{ getImage(getFilePath('education_fee') . '/' . $institute->image) }}"
                                                                alt="">
                                                        </span>
                                                        <span class="content">
                                                            <span class="title">{{ __($institute->name) }}</span>
                                                            <span>{{ __(@$institute->category->name) }}</span>
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
                            <div class="card custom--card mb-3 institute-details">
                                <div class="card-body">
                                    <h5 class="mb-3">@lang('Institute')</h5>
                                    <div class="bill-select select">
                                        <span class="left">
                                            <span class="thumb">
                                                <img class="institute-image" src="" alt="">
                                            </span>
                                            <span class="content">
                                                <span class="institute-title title"></span>
                                                <span class="institute-category-name"></span>
                                            </span>
                                        </span>
                                    </div>
                                    <input type="hidden" name="institution_id" class="institute-id">
                                    <input type="hidden" class="fixed-charge">
                                    <input type="hidden" class="percent-charge">
                                </div>
                            </div>
                            <div class="card custom--card mb-3">
                                <div class="card-body">

                                    <div class="ovo-form"></div>

                                    <div class="form--group">
                                        <label class="form--label">@lang('Enter Amount')</label>
                                        <div class="input-group input--amount border-0">
                                            <input type="number" step="any" class="form--control sm-style form-control"
                                                placeholder="@lang('0.00')" value="{{ old('amount') }}" name="amount"
                                                required>
                                            <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                        </div>
                                        <div class="d-flex gap-1 justify-content-between  flex-wrap mt-2">
                                            <span>
                                                @lang('Limit:')
                                                <span>
                                                    {{ gs('cur_sym') }}{{ showAmount($educationCharge->min_limit, currencyFormat: false) }}
                                                    -
                                                    {{ gs('cur_sym') }}{{ showAmount($educationCharge->max_limit, currencyFormat: false) }}
                                                </span>
                                            </span>
                                            <span>
                                                @lang('Available Balance'):
                                                {{ gs('cur_sym') }}{{ showAmount(auth()->user()->balance, currencyFormat: false) }}
                                            </span>
                                        </div>
                                    </div>
                                    <x-otp_verification remark="education_fee" />
                                </div>
                            </div>

                            <div class="card custom--card mb-3">
                                <div class="card-body">
                                    <div class="deposit-wrapper">
                                        <div class="deposit-wrapper-info">
                                            <span class="title">
                                                @lang('Processing Charge')
                                                <button type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="@lang('Processing Charge')" class="charge-info">
                                                    <i class="las la-info-circle"></i>
                                                </button>
                                            </span>
                                            <span class="amount">
                                                <span>{{ gs('cur_sym') }}</span><span
                                                    class="processing-fee">@lang('0.00')</span>
                                            </span>
                                        </div>
                                        <div class="deposit-wrapper-total">
                                            <span class="title">
                                                @lang('Total')
                                            </span>
                                            <span class="amount">
                                                <span>{{ gs('cur_sym') }}</span><span
                                                    class="final-amount">@lang('0.00')</span>
                                            </span>
                                        </div>
                                    </div>
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
        .institute-details {
            display: none;
        }

        .bill-type-wrapper .bill-type {
            cursor: pointer;
        }

        .bill-type.active {
            border: 1px solid hsl(var(--base));

        }

        .no-results{
            display: none;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            const $loader = $('.full-page-loader');

            $('.bill-select-item').on('click', function() {
                var instituteName = $(this).data('institute-name');
                var instituteImage = $(this).data('institute-image');
                var instituteCategory = $(this).data('category-name');
                var instituteId = $(this).data('institute-id');
                var formId = $(this).data('form-id');
                var fixedCharge = parseFloat($(this).data('fixed-charge'));
                var percentCharge = parseFloat($(this).data('percent-charge'));

                $('.institute-title').text(instituteName);
                $('.institute-image').attr('src', instituteImage);
                $('.institute-category-name').text(instituteCategory);
                $('.institute-id').val(instituteId);
                $('.fixed-charge').val(fixedCharge);
                $('.percent-charge').val(percentCharge);
                $('input[name=amount]').val('');
                $(".processing-fee").text('0.00');
                $(".final-amount").text('0.00');
                $('.institute-details').show();

                if (percentCharge > 0 || fixedCharge > 0) {
                    percentCharge = percentCharge;
                    fixedCharge = fixedCharge;
                } else {
                    percentCharge = parseFloat("{{ $educationCharge->percent_charge }}") || 0;
                    fixedCharge = parseFloat("{{ $educationCharge->fixed_charge }}") || 0;
                }

                $('.charge-info').attr('data-bs-title',
                        `Charge: {{ gs('cur_sym') }}${fixedCharge.toFixed(2)}+${percentCharge.toFixed(0)}%`)
                    .tooltip(
                        'dispose')
                    .tooltip();

                getDetailsForm(formId);
            });

            function getDetailsForm(formId) {
                $.ajax({
                    url: "{{ route('user.education.fee.form', ':id') }}".replace(':id', formId),
                    type: "GET",
                    beforeSend: function() {
                        $loader.removeClass('d-none');
                    },
                    complete: function() {
                        $loader.addClass('d-none');
                    },
                    success: function(response) {
                        if (response.status === "success" && response.data && response.data.content) {
                            $('.ovo-form').html(response.data.content);
                        } else {
                            notify('error', response.message || "@lang('Something went wrong')");
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    }
                });
            }
            $(".bill-type").on("click", function() {
                var selectedCategory = $(this).attr("data-category-id");
                $(".bill-type").removeClass("active");
                $(this).addClass("active");
                $('.search-institute').val('');

                filterInstitutes();
            });

            $(".search-institute").on("input", function() {
                filterInstitutes();
            });

            function filterInstitutes() {
                let selectedCategory = $(".bill-type.active").attr("data-category-id");
                let filter = $(".search-institute").val().toLowerCase();
                let visibleCount = 0;

                $(".bill-select-item").each(function() {
                    let instituteCategory = $(this).attr("data-category-id");
                    let name = $(this).data("institute-name").toLowerCase();
                    let isVisible = !selectedCategory ? name.includes(filter) : (instituteCategory ===
                        selectedCategory && name.includes(filter));
                    $(this).toggle(isVisible);
                    if (isVisible) visibleCount++;
                });

                if (visibleCount === 0) {
                    $(".no-results").show();
                } else {
                    $(".no-results").hide();
                }
            }


            $("input[name=amount]").on('input', function() {
                calculation();
            });


            function calculation() {
                let percentCharge = parseFloat($('.percent-charge').val() || 0);
                let fixedCharge = parseFloat($('.fixed-charge').val() || 0);

                if (percentCharge > 0 || fixedCharge > 0) {
                    percentCharge = percentCharge;
                    fixedCharge = fixedCharge;
                } else {
                    percentCharge = parseFloat("{{ $educationCharge->percent_charge }}") || 0;
                    fixedCharge = parseFloat("{{ $educationCharge->fixed_charge }}") || 0;
                }
                const amount = parseFloat($('body').find(`input[name="amount"]`).val() || 0);
                const totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                const totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                const totalAmount = parseFloat(amount + totalPercentCharge + fixedCharge);
                $(".processing-fee").text(totalCharge.toFixed(2));
                $(".final-amount").text(totalAmount.toFixed(2));
            }

            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
