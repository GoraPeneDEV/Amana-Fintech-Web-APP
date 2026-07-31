@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('user.bank.transfer.history') }}">
            <span class="icon" title="@lang('Bank Transfer History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __(@$pageTitle) }}
        </a>
    </h4>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <form action="{{ route('user.bank.transfer.store') }}" method="post"
                class="send-money-form has-otp-form no-submit-loader">
                @csrf
                <div class="short-wrapper mx-0">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="card custom--card p-0 overflow-hidden h-100">
                                <div class="card-body px-3 px-sm-4">
                                    <div class="bill-select-wrapper">
                                        <h6 class="mt-3">@lang('Saved Bank')</h6>
                                        @forelse ($savedBanks as $savedBank)
                                            <div class="bill-select-item ">
                                                <div class="bill-select d-flex justify-content-between flex-wrap  ">
                                                    <div class="left  flex-fill">
                                                        <span class="thumb">
                                                            <img src="{{ getImage(getFilePath('bank_transfer') . '/' . @$savedBank->bank->image) }}"
                                                                alt="">
                                                        </span>
                                                        <span class="content">
                                                            <span class="title">{{ __(@$savedBank->bank->name) }}</span>
                                                            <span>{{ __(@$savedBank->account_holder) }}</span> -
                                                            <span>{{ __(@$savedBank->account_number) }}</span>
                                                        </span>

                                                    </div>
                                                    <div
                                                        class="right d-flex gap-1 flex-wrap flex-fill justify-content-start justify-content-xxl-end">
                                                        <button type="button"
                                                            class="btn btn-outline--danger btn--sm confirmationBtn"
                                                            data-question='@lang('Are you sure delete this account?')'
                                                            data-action="{{ route('user.bank.transfer.account.delete', $savedBank->id) }}">
                                                            <i class="fa fa-times"></i>
                                                            @lang('Remove')
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn--light btn--sm save-account-select"
                                                            data-user-bank-id="{{ $savedBank->id }}"
                                                            data-bank-id="{{ @$savedBank->bank->id }}"
                                                            data-percent-charge="{{ @$savedBank->bank->percent_charge }}"
                                                            data-fixed-charge="{{ @$savedBank->bank->fixed_charge }}"
                                                            data-bank-name="{{ __(@$savedBank->bank->name) }}">
                                                            <i class="fa fa-paper-plane"></i> @lang('Transfer')
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            @include('Template::partials.empty_message')
                                        @endforelse
                                        <h6 class="mt-3">@lang('All Bank')</h6>
                                        @forelse ($allBank as $bank)
                                            <div class="bill-select-item">
                                                <div class="bill-select d-flex justify-content-between flex-wrap  ">
                                                    <span class="left flex-fill">
                                                        <span class="thumb">
                                                            <img src="{{ getImage(getFilePath('bank_transfer') . '/' . $bank->image) }}"
                                                                alt="">
                                                        </span>
                                                        <span class="content">
                                                            <span class="title">{{ __($bank->name) }}</span>
                                                        </span>
                                                    </span>
                                                    <div
                                                        class="right d-flex gap-1 flex-wrap flex-fill justify-content-start justify-content-xxl-end">
                                                        <button type="button"
                                                            class="right btn btn--light btn--sm add-account"
                                                            data-bank-id="{{ $bank->id }}"
                                                            data-form-id="{{ $bank->form_id }}"
                                                            data-bank-name="{{ __($bank->name) }}">
                                                            <i class="fa fa-user-plus"></i> @lang('Account')
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn--light btn--sm save-account-select"
                                                            data-user-bank-id="0" data-bank-id="{{ $bank->id }}"
                                                            data-percent-charge="{{ $bank->percent_charge }}"
                                                            data-fixed-charge="{{ $bank->fixed_charge }}"
                                                            data-bank-name="{{ __(@$bank->name) }}">
                                                            <i class="fa fa-paper-plane"></i> @lang('One Time Transfer')
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            @include('Template::partials.empty_message')
                                        @endforelse

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card custom--card mb-3">
                                <div class="card-body">
                                    <div class="account-details"></div>
                                    <input type="hidden" name="bank_account_id" class="bank-account-id">
                                    <input type="hidden" class="fixed-charge">
                                    <input type="hidden" class="percent-charge">
                                    <div class="form--group">
                                        <label class="form--label">@lang('Enter Amount')</label>
                                        <div class="input-group input--amount border-0">
                                            <input type="number" step="any" class="form--control sm-style form-control"
                                                placeholder="@lang('0.00')" value="{{ old('amount') }}" name="amount"
                                                required>
                                            <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                        </div>
                                        <div class="d-flex gap-1 justify-content-between  flex-wrap mt-1">
                                            <span>
                                                @lang('Limit:')
                                                <span>
                                                    {{ gs('cur_sym') }}{{ showAmount($bankTransferCharge->min_limit, currencyFormat: false) }}
                                                    -
                                                    {{ gs('cur_sym') }}{{ showAmount($bankTransferCharge->max_limit, currencyFormat: false) }}
                                                </span>
                                            </span>
                                            <span>
                                                @lang('Available Balance'):
                                                {{ gs('cur_sym') }}{{ showAmount(auth()->user()->balance, currencyFormat: false) }}
                                            </span>
                                        </div>
                                    </div>
                                    <x-otp_verification remark="bank_transfer" />
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

    {{-- Account Modal  --}}
    <div id="account-modal" class="modal fade custom--modal fade" tabindex="-1" role="dialog"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title bank-name-title">@lang('New Bank Account')</h4>
                    <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('user.bank.transfer.account.store') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="bank_id" class="bank-id">
                        <div class="form-group">
                            <label class="form--label">@lang('Account Holder')</label>
                            <input type="text" class="form--control" placeholder="@lang('Account Holder')"
                                name="account_holder" required>
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Account Number')</label>
                            <input type="text" class="form--control" placeholder="@lang('Account Number')"
                                name="account_number" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--base btn--md w-100">
                                @lang('Save Now')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal :isFrontend="true" />
@endsection
@push('style')
    <style>
        .biller-details {
            display: none;
        }

        .bill-type.active {
            border: 1px solid hsl(var(--base));

        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            const $loader = $('.full-page-loader');
            const $accountModal = $("#account-modal");

            $('.add-account').on('click', function() {
                const formId = $(this).data('form-id');
                $('.bank-id').val($(this).data('bank-id'));
                $('.bank-name-title').text($(this).data('bank-name'));
                $accountModal.modal('show');
            });

            $('.save-account-select').on('click', function() {
                var userBankId = $(this).data('user-bank-id');
                var bankId = $(this).data('bank-id');

                $('.fixed-charge').val($(this).data('fixed-charge'));
                $('.percent-charge').val($(this).data('percent-charge'));
                $('.bank-account-id').val(userBankId);

                var fixedCharge = parseFloat($(this).data('fixed-charge')) || 0;
                var percentCharge = parseFloat($(this).data('percent-charge')) || 0;

                if (percentCharge > 0 || fixedCharge > 0) {
                    percentCharge = parseFloat($(this).data('percent-charge')) || 0;
                    fixedCharge = parseFloat($(this).data('fixed-charge')) || 0;
                } else {

                    percentCharge = parseFloat("{{ $bankTransferCharge->percent_charge }}" || 0);
                    fixedCharge = parseFloat("{{ $bankTransferCharge->fixed_charge }}" || 0);
                }

                $('.charge-info').attr('data-bs-title',
                        `Charge: {{ gs('cur_sym') }}${fixedCharge.toFixed(2)}+${percentCharge.toFixed(0)}%`)
                    .tooltip(
                        'dispose')
                    .tooltip();

                getAccountDetails(userBankId, bankId);
            });

            function getAccountDetails(userBankId, bankId) {
                const action = "{{ route('user.bank.transfer.account.details', ':id') }}";

                $.ajax({
                    url: action.replace(":id", bankId),
                    type: "GET",
                    data: {
                        user_bank_id: userBankId
                    },
                    dataType: "JSON",
                    beforeSend: function() {
                        $loader.removeClass('d-none');
                    },
                    complete: function() {
                        $loader.addClass('d-none');
                    },
                    success: function(response) {
                        if (response.status === "success" && response.data && response.data.content) {
                            $('.account-details').html(response.data.content);
                            calculation();
                        } else {
                            notify('error', response.message || "@lang('Something went wrong')");
                            setTimeout(() => {
                                // window.location.reload();
                            }, 1000);
                        }
                    }
                });
            }

            $(".search-bank").on("input", function() {
                let filter = $(this).val().toLowerCase();
                $(".save-account-select").each(function() {
                    let name = $(this).data("bank-name").toLowerCase();
                    $(this).toggle(name.includes(filter));
                });
            });

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
                    percentCharge = parseFloat("{{ $bankTransferCharge->percent_charge }}") || 0;
                    fixedCharge = parseFloat("{{ $bankTransferCharge->fixed_charge }}") || 0;
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
