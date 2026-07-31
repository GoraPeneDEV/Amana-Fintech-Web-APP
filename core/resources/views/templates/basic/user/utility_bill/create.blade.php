@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('user.utility.bill.history') }}">
            <span class="icon" title="@lang('Bill History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __(@$pageTitle) }}
        </a>
    </h4>
    <div class="row">
        <div class="col-md-5">
            <div class="card custom--card">
                <div class="card-body">
                    <form class="util-bill-form no-submit-loader has-otp-form"
                        action="{{ route('user.utility.bill.store') }}" method="POST">
                        @csrf
                        <div class="util-bill-form__header">
                            <ul class="util-bill-step-list">
                                <li class="util-bill-step-list__item active">
                                    <a class="util-bill-step-list__step" href="#">1</a>
                                </li>
                                <li class="util-bill-step-list__item">
                                    <a class="util-bill-step-list__step" href="#">2</a>
                                </li>
                                <li class="util-bill-step-list__item">
                                    <a class="util-bill-step-list__step" href="#">3</a>
                                </li>
                            </ul>
                        </div>
                        <div class="util-bill-form__body">
                            <div class="step-1">
                                <div class="form-group">
                                    <label class="form--label">@lang('Select Country')</label>
                                    <div class="select2--container select2--country">
                                        <select name="country" class="form-control form--control img-select2 country">
                                            @foreach ($counties as $country)
                                                <option value="{{ $country->id }}" data-src="{{ $country->flag_url }}">
                                                    {{ __(@$country->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form--label">@lang('Select Bill Type')</label>
                                    <div class="select2--container select2--country">
                                        <select name="category" class="form-control form--control img-select2 category">
                                            @foreach ($billCategory as $category)
                                                <option value="{{ $category->id }}"
                                                    data-src="{{ getImage(getFilePath('utility') . '/' . $category->image) }}">
                                                    {{ __(@$category->formattedName) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex gap-2 justify-content-between flex-wrap">
                                        <label class="form--label">@lang('Select Biller Company')</label>
                                        <span class="text--info cursor-pointer save-account">@lang('Saved Accounts')</span>
                                    </div>
                                    <div class="select2--container select2--country">
                                        <select name="company_id" id="company_id"
                                            class="form-control form--control img-select2 company">
                                            <option value="">@lang('Select Company')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="step-2" style="display:none">
                                <div class="form-group">
                                    <label class="form--label required">
                                        @lang('Unique ID') / @lang('Meter Number') / @lang('Subscriber Number')
                                    </label>
                                    <input type="text" class="form--control" name="unique_id" required>
                                </div>

                                <div class="form-group">
                                    <label class="form--label required">@lang('Reference')</label>
                                    <input type="text" class="form--control" name="reference" required>
                                </div>
                                <div class="form-group">
                                    <label class="form--label">@lang('Enter Amount')</label>
                                    <div class="input-group input--amount border-0">
                                        <input type="number" class="form--control sm-style form-control"
                                            placeholder="@lang('0.00')" name="amount" required>
                                        <input type="hidden" name="amount_id">
                                        <span class="input-group-text"></span>
                                    </div>
                                    <span class="mt-2">
                                        <span class="amount-limit"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="step-3" style="display:none">
                                <div class="form-group">
                                    <ul class="list-group list-group-flush">
                                        <li class="d-flex gap-2 flex-wrap justify-content-between list-group-item">
                                            <span>@lang('Amount')</span>
                                            <span>
                                                <span class="amount"></span> {{ __(gs('cur_text')) }}
                                            </span>
                                        </li>
                                        <li class="d-flex gap-2 flex-wrap justify-content-between list-group-item">
                                            <span>@lang('Charge')</span>
                                            <span>
                                                <span class="charge"></span> {{ __(gs('cur_text')) }}
                                            </span>
                                        </li>
                                        <li class="d-flex gap-2 flex-wrap justify-content-between list-group-item">
                                            <span>@lang('Total Amount')</span>
                                            <span>
                                                <span class="total"></span> {{ __(gs('cur_text')) }}
                                            </span>
                                        </li>
                                        <li
                                            class="d-flex gap-2 flex-wrap justify-content-between list-group-item text--danger">
                                            <span>@lang('Delivery Amount')</span>
                                            <span>
                                                <span class="delivery-amount"></span>
                                            </span>
                                        </li>
                                    </ul>
                                </div>

                                <div>
                                    <x-otp_verification remark="utility_bill" />
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="save_information"
                                            name="save_information" checked>
                                        <label class="form-check-label" for="save_information">
                                            @lang('Save Information')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="util-bill-form__footer">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                <button type="button" class="btn btn-outline--grbtn prev-step disabled" disabled>
                                    <i class="fa-solid fa-undo"></i>
                                    @lang('Prev')
                                </button>
                                <button type="button" class="btn btn--grbtn next-step">
                                    <i class="fa-regular fa-paper-plane"></i>
                                    <span class="next-step-text">@lang('Continue')</span>
                                </button>
                                <button type="button" class="btn btn--grbtn d-none submit-btn">
                                    <i class="fa-regular fa-paper-plane"></i>
                                    <span class="next-step-text">@lang('Confirm')</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- company modal --}}
    <div id="save-modal" class="modal fade custom--modal fade modal-lg" tabindex="-1" role="dialog"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title company-name-title">@lang('Saved Information')</h4>
                    <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($userCompanies as $userCompany)
                            <li class="list-group-item d-flex  justify-content-between gap-2 flex-wrap">
                                <span>{{ __($userCompany->company->name) }}</span>
                                <span class="d-flex gap-4 flex-wrap">
                                    <span>
                                        {{ __($userCompany->unique_id) }}
                                    </span>
                                    <span>
                                        <button type="button" class="text--success me-2 save-information"
                                            data-company='@json($userCompany->company)'
                                            data-unique-id="{{ $userCompany->unique_id }}">
                                            <i class="fa-regular fa-paper-plane"></i>
                                        </button>
                                        <button type="button" class="text--danger confirmationBtn"
                                            data-action="{{ route('user.utility.bill.company.delete', $userCompany->id) }}"
                                            data-question="@lang('Are you sure delete this saved company information?')">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <x-confirmation-modal :isFrontend="true" />
@endsection



@push('script')
    <script>
        "use strict";
        (function($) {

            const $loader = $('.full-page-loader');
            const $modal = $('#save-modal');
            var fixedCharge, percentCharge;
            var company = null;
            var fixedCharge, percentCharge, minAmount, maxAmount, amount;

            var step = 1;
            var oldStep = step;
            var userCompany = false;

            $('.breadcrumb-plugins-wrapper').remove();

            $("#confirmationModal").find('.modal-dialog').removeClass('modal-dialog-centered')


            $('form').on('submit', function(e) {
                e.preventDefault();
            });

            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var $state = $(
                    '<span class="img-flag-inner"><img src="' + $(state.element).attr('data-src') +
                    '" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            };

            $('.img-select2').each((index, el) => {
                $(el).select2({
                    dropdownParent: $(el).parents('.select2--container'),
                    templateResult: formatState,
                    templateSelection: formatState,
                    width: "100%"
                });
            });

            function updateStepIndicator() {
                if (step <= 1) {
                    $('.prev-step').addClass('disabled').attr('disabled', true);
                } else {
                    $('.prev-step').removeClass('disabled').attr('disabled', false);
                }

                if (step == 3) {
                    $('.submit-btn').attr('type', 'submit').removeClass('d-none');
                    $('.next-step').addClass('d-none');
                } else {
                    $('.submit-btn').attr('type', 'button').addClass('d-none');
                    $('.next-step').removeClass('d-none');
                }

                if (parseInt(step) > parseInt(oldStep)) {
                    $('.util-bill-step-list__item').eq(step - 1).addClass('active');
                } else {
                    $('.util-bill-step-list__item').eq(oldStep - 1).removeClass('active');
                }
            }

            $('.next-step').on('click', function() {


                var validationCheck = false;
                if (step == 1) {
                    validationCheck = stepOneValidation();
                }
                if (step == 2) {
                    validationCheck = stepTwoValidation();
                }
                if (validationCheck) {
                    oldStep = step
                    step++;
                    $(`.step-${oldStep}`).fadeOut(300, function() {
                        $(`.step-${step}`).fadeIn(300);
                    });
                    updateStepIndicator();
                } else {
                    notify('error', "@lang('Please fill all the required fields.')")
                }
            });

            $('body').on('click', ".prev-step", function() {
                oldStep = step
                step--;
                $(`.step-${oldStep}`).fadeOut(300, function() {
                    $(`.step-${step}`).fadeIn(300);
                });
                updateStepIndicator();
            });

            function stepOneValidation() {
                return $("select[name=country]").val()?.length && $("select[name=category]").val()
                    ?.length &&
                    $("select[name=company_id]").val()?.length;
            }

            function stepTwoValidation() {

                if (!$("input[name=unique_id]").val().length && !$("input[name=reference]").val().length) {
                    return false;
                }

                amount = parseFloat($("input[name=amount]").val());

                if (!amount) {
                    notify('error', "@lang('Please enter amount properly')");
                    return false;
                }


                if (company.denomination_type == 'RANGE') {
                    if (amount < minAmount || amount > maxAmount) {
                        notify('error', "@lang('Amount must be between') " + minAmount + " - " + maxAmount + " " + company
                            .currency_code);
                        return false;
                    }
                } else {
                    const amounts = (company?.fixed_amounts || []).map(item => parseFloat(item.amount).toFixed(2));
                    if (!amounts.includes(amount.toFixed(2))) {
                        notify('error', "@lang('Invalid amount selected')");
                        return false;
                    }
                }

                const totalCharge = fixedCharge + (amount / 100 * percentCharge);
                const totalAmount = parseFloat(amount + totalCharge);

                $('.amount').text(amount.toFixed(2));

                if (percentCharge > 0) {
                    $('.charge').text(
                        ` ${fixedCharge.toFixed(2)} + ${percentCharge.toFixed(2)}% = ${totalCharge.toFixed(2)}`);
                } else {
                    $('.charge').text(totalCharge.toFixed(2));
                }


                $('.total').text(`${totalAmount.toFixed(2)}`);
                $('.delivery-amount').text(
                    `${parseFloat(amount* parseFloat(company.rate).toFixed(2)).toFixed(2)} ${company.currency_code}`
                );
                return true;
            }


            $('.country, .category').on('change', function() {
                const countryId = $('.country').val();
                const categoryId = $('.category').val();

                if (countryId && categoryId) {
                    fetchCompanies(countryId, categoryId);
                }
            }).change();

            function fetchCompanies(countryId, categoryId) {
                if (userCompany) return false;
                $.ajax({
                    url: "{{ route('user.utility.bill.get.companies') }}",
                    type: "GET",
                    data: {
                        country_id: countryId,
                        category_id: categoryId
                    },
                    beforeSend: function() {
                        $('.company').html('<option>Loading...</option>');
                    },
                    success: function(response) {

                        if (response.status == 'success') {
                            let options = '<option value="" selected disabled>@lang('Select Company')</option>';
                            $.each(response.data.companies, function(index, company) {
                                if (company.image) {
                                    var imageSrc = "{{ asset(getFilePath('utility')) }}" + "/" +
                                        company.image;
                                } else {
                                    var imageSrc = "{{ getImage(getFilePath('utility')) }}";
                                }
                                options += `<option value="${company.id}" data-src="${imageSrc}" data-resource='${JSON.stringify(company)}'>
                            ${company.name}
                        </option>`;
                            });
                            $('.company').html(options);
                        } else {
                            notify('error', response.message);
                            $('.company').html('<option>No companies found</option>');
                        }
                    },
                    error: function() {
                        notify('error', "@lang('Something went wrong')");
                        $('.company').html('<option>Error loading companies</option>');
                    }
                });
            }

            $('body').on('change', ".company", function() {

                if (!userCompany) {
                    company = $(this).find('option:selected').data('resource');
                }

                minAmount = parseFloat(company.minimum_amount);
                maxAmount = parseFloat(company.maximum_amount);

                fixedCharge = parseFloat(company.fixed_charge);
                percentCharge = parseFloat(company.percent_charge);


                minAmount = Number.isFinite(minAmount) ? minAmount : (parseFloat(
                    "{{ $utilityCharge->min_limit }}"));

                maxAmount = Number.isFinite(maxAmount) ? maxAmount : (parseFloat(
                    "{{ $utilityCharge->max_limit }}"));


                fixedCharge = Number.isFinite(fixedCharge) ? fixedCharge : (parseFloat(
                    "{{ $utilityCharge->fixed_charge }}"));

                percentCharge = Number.isFinite(percentCharge) ? percentCharge : parseFloat(
                    "{{ $utilityCharge->percent_charge }}");


                if (company.denomination_type == 'RANGE') {
                    $('.amount-limit').html(minAmount.toFixed(2) + ' - ' + maxAmount.toFixed(2) + " " +
                        "{{ gs('cur_text') }}");
                } else {
                    let amountHtml = "";
                    $.each(company.fixed_amounts, function(i, amount) {
                        amountHtml += `<span title="${amount.description}" class="fixed-amount  suggest-amount" data-amount="${parseFloat(amount.amount).toFixed(2)}" data-id="${amount.id}">
                                {{ gs('cur_sym') }}${parseFloat(amount.amount).toFixed(2)}
                        </span>
                        `
                    });
                    $('.amount-limit').html(
                        `<div class="d-flex gap-2 justify-content-start align-items-center flex-wrap">${amountHtml}</div>`
                    );

                }

                $('.currency').text(company?.currency_code);
            });

            $('.save-account').on('click', function() {
                $modal.modal('show');
            });

            $('.save-information').on('click', function() {
                company = $(this).data('company');
                userCompany = true;

                $("select[name=country]").val(company.country_id).change();
                $("select[name=category]").val(company.category_id).change();
                $("select[name=company_id]").html(`<option value="${company.id}">${company.name}</option>`)
                    .val(company.id).change();
                $('input[name=unique_id]').val($(this).data('unique-id'));
                $modal.modal('hide');
                $('.next-step').trigger('click');

            });

            $('body').on('click', ".fixed-amount", function() {
                $('input[name=amount]').val(parseFloat($(this).data('amount')).toFixed(2));
                $('input[name=amount_id]').val($(this).data('id'));

            });

        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .biller-details {
            display: none;
        }

        .bill-type-wrapper .bill-type {
            cursor: pointer;
        }

        .bill-type.active {
            border: 1px solid hsl(var(--base));

        }

        .no-results {
            display: none;
        }

        .util-bill-step-list {
            --gap: 8px;
            max-width: 368px;
            margin-inline: auto;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            gap: var(--gap);
        }

        .util-bill-step-list__item:not(:last-child) {
            -webkit-box-flex: 1;
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
        }

        .util-bill-step-list__item:not(:last-child)::after {
            content: "";
            display: inline-block;
            width: 100%;
            height: 1px;
            border-bottom: 1px dashed hsl(var(--border-color));
            margin-left: var(--gap);
        }

        .util-bill-step-list__item:not(.active) .util-bill-step-list__step {
            position: relative;
            z-index: 1;
            background: var(--gr-color);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .util-bill-step-list__item:not(.active) .util-bill-step-list__step::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: inherit;
            border: 2px solid transparent;
            background: var(--gr-color) border-box;
            -webkit-mask: -webkit-gradient(linear, left top, left bottom, color-stop(0, #fff)) padding-box, -webkit-gradient(linear, left top, left bottom, color-stop(0, #fff));
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            mask: -webkit-gradient(linear, left top, left bottom, color-stop(0, #fff)) padding-box, -webkit-gradient(linear, left top, left bottom, color-stop(0, #fff));
            mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            z-index: -1;
        }

        .util-bill-step-list__item.active .util-bill-step-list__step {
            color: hsl(var(--white));
            background: var(--gr-color);
        }

        .util-bill-step-list__item.active .util-bill-step-list__step .util-bill-step-list__item.active~.util-bill-step-list__item .util-bill-step-list__step {
            background-color: hsl(var(--section-bg));
        }


        .util-bill-step-list__step {
            --size: 32px;
            width: var(--size);
            height: var(--size);
            border-radius: 50%;
            display: -webkit-inline-box;
            display: -ms-inline-flexbox;
            display: inline-flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            font-size: calc(var(--size) / 2);
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            -ms-flex-negative: 0;
            flex-shrink: 0;
        }

        @media screen and (max-width: 575px) {
            .util-bill-step-list__step {
                --size: 28px;
            }
        }

        .util-bill-form {
            max-width: 100%;
        }

        .util-bill-form>*:not(:last-child) {
            margin-bottom: 48px;
        }

        @media screen and (max-width: 575px) {
            .util-bill-form>*:not(:last-child) {
                margin-bottom: 24px;
            }
        }

        .select2--country .select2-dropdown {
            width: 100% !important;
            left: 0px !important;
        }

        .select2--country .select2-container:has(.img-flag-inner) {
            left: 0px !important;
            padding-right: 0px !important;
            min-width: unset !important;
        }

        .select2--country .select2-container--default .select2-selection--single .select2-selection__arrow {
            right: 10px !important;
        }
    </style>
@endpush
