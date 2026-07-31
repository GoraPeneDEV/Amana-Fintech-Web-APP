@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('user.virtual.card.list') }}">
            <span class="icon" title="@lang('Card List')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __($pageTitle) }}
        </a>
    </h4>
    <div class="row gy-4">
        <div class="col-xl-5 col-md-6">
            <div class="card custom--card mb-3">
                <div class="card-body">
                    <div class="virtual-card {{ $bgClass }} w-100 mb-4 single-virtual-card"
                        href="{{ route('user.virtual.card.view', $card->card_id) }}">
                        <div class="virtual-card-body ">
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
                                <h5 class="text">{{ printVirtualCardNumber($card) }}</h5>
                                <button class="icon card-confidential" type="button">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
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
                                <span class="text cvc-number">•••</span>
                            </div>
                        </div>
                    </div>
                    <div class="saving-card-btn flex-align gap-3 mb-3">
                        <button data-bs-toggle="modal" data-bs-target="#fund-modal" class="btn btn--success flex-grow-1"
                            @disabled($card->status != Status::VIRTUAL_CARD_ACTIVE)>
                            <i class="las la-plus-circle"></i> @lang('Add Fund')
                        </button>
                        <button class="btn btn--danger flex-grow-1 confirmationBtn" data-question="@lang('Are you sure to permanently close this virtual card?')"
                            data-action="{{ route('user.virtual.card.cancel', $card->id) }}" @disabled($card->status == Status::VIRTUAL_CARD_CLOSED)>
                            <i class="las la-times-circle"></i> @lang('Close')
                        </button>
                    </div>
                </div>
            </div>
            <div class="card custom--card mb-3">
                <div class="card-header">
                    <h4 class="card-title">@lang('Card Holder Information') </h4>
                </div>
                <hr class="mt-0 pt-0 mb-4">
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span>@lang('Name')</span>
                            <span>{{ __(@$card->cardHolder->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span>@lang('Email')</span>
                            <span>{{ __(@$card->cardHolder->email) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span>@lang('Mobile Number')</span>
                            <span>{{ @$card->cardHolder->phone_number }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span>@lang('Address')</span>
                            <span>{{ __(@$card->cardHolder->address) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span>@lang('State')</span>
                            <span>{{ __(@$card->cardHolder->state) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span>@lang('Postal Code')</span>
                            <span>{{ __(@$card->cardHolder->postal_code) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap ps-0">
                            <span>@lang('Country')</span>
                            <span>{{ __(@$card->cardHolder->country) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-md-6">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="card-title">@lang('Card Transactions')</h6>
                </div>
                <div class="card-body">
                    <ul class="transection-list">
                        @forelse($transactions as $trx)
                            <li class="transection-item">
                                <div class="left">
                                    <div class="icon">
                                        <img src="{{ $card->brand_image_src }}" alt="">
                                    </div>
                                    <div class="content">
                                        <p class="number">{{ printVirtualCardNumber($card) }}</p>
                                        <p class="name">{{ __($card->brand) }}</p>
                                    </div>
                                </div>
                                <div class="right">
                                    <p class="ammount">
                                        <span
                                            class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                            {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                        </span>
                                    </p>
                                    <span class="date mt-1">{{ showDateTime($trx->created_at) }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="transection-item justify-content-center">
                                <div class="empty-message">
                                    <p class="empty-message-icon">
                                        <img src="{{ asset('assets/images/no-data.gif') }}" alt="">
                                    </p>
                                    <p class="empty-message-text">
                                        <span class="d-block">@lang('No transaction found')</span>
                                        <span class="d-block fs-13">@lang('There are no available data to display on this card at the moment.')</span>
                                    </p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal custom--modal fade" id="fund-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Add Fund to Virtual Card')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.virtual.card.add.fund', $card->id) }}" method="POST" class="fund-form">
                        @csrf
                        <div class="form-group">
                            <label class="form--label">@lang('Amount')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                <input type="number" step="any" class="form-control form--control" name="amount"
                                    required autocomplete="off">
                            </div>
                            <div class="d-flex flex-wrap gap-1 justify-content-between">
                                <span class="mt-2">@lang('Limit'):
                                    <strong>
                                        {{ showAmount($charge->min_limit, currencyFormat: false) }} -
                                        {{ showAmount($charge->max_limit, currencyFormat: false) }}
                                    </strong>

                                    {{ __(gs('cur_text')) }}
                                </span>
                                <span class="mt-2">@lang('Available'):
                                    <strong>{{ showAmount($user->balance, currencyFormat: false) }}</strong>
                                    {{ __(gs('cur_text')) }}
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                                    <span>@lang('Amount')</span>
                                    <span>
                                        <strong class="amount">@lang('0.00')</strong>
                                        {{ __(gs('cur_text')) }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                                    <span>@lang('Processing Fee')</span>
                                    <span>
                                        <strong class="charge">@lang('0.00')</strong>
                                        {{ __(gs('cur_text')) }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                                    <span>@lang('Total Amount')</span>
                                    <span>
                                        <strong class="total">@lang('0.00')</strong>
                                        {{ __(gs('cur_text')) }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">
                                <i class="fas fa-paper-plane"></i> @lang('Submit')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal custom--modal fade" id="pin-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Get Card Number')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.virtual.card.confidential', $card->id) }}" method="POST"
                        class="pin-form">
                        @csrf
                        <div class="mb-3">
                            <div class="alert alert--warning  fw-bold">
                                @lang('Please enter your PIN to access your card confidential data.')
                            </div>
                        </div>
                        <div class="form-group">
                            <x-pin justifyClass="justify-content-center" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">
                                <i class="fas fa-paper-plane"></i> @lang('Get Now')
                            </button>
                        </div>
                    </form>
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
            $(`input[name=amount]`).on('input', function() {
                const amount = parseFloat($(this).val() || 0);
                const fixedCharge = parseFloat("{{ $charge->fixed_charge }}");
                const percentCharge = parseFloat("{{ $charge->percent_charge }}");
                const totalCharge = fixedCharge + (amount * percentCharge / 100);
                const totalAmount = amount + totalCharge;

                $('.amount').text(amount.toFixed(2));
                $('.charge').text(totalCharge.toFixed(2));
                $('.total').text(totalAmount.toFixed(2));

                const minAmount = parseFloat("{{ $charge->min_limit }}");
                const maxAmount = parseFloat("{{ $charge->max_limit }}");

                if (amount < minAmount || amount > maxAmount) {
                    $('#fund-modal')
                        .find(`button[type=submit]`)
                        .attr('disabled', true)
                        .addClass('disabled');
                } else {
                    $('#fund-modal')
                        .find(`button[type=submit]`)
                        .attr('disabled', false)
                        .removeClass('disabled');
                }
            });

            $('body').on('submit', ".fund-form", function(e) {
                e.preventDefault();
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
                    complete: function() {
                        $submitBtn.html(`
                            <i class="fas fa-paper-plane"></i> @lang('Submit')
                        `).removeClass('disabled').attr('disabled', false);
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            notify('success', response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

            $('body').on('submit', ".pin-form", function(e) {
                e.preventDefault();
                const formData = new FormData($(this)[0]);
                const action = $(this).attr('action');
                const $this = $(this);
                const $submitBtn = $this.find(`button[type=submit]`);
                const oldHtml = $submitBtn.html();
                const $cardElement = $('body').find(".single-virtual-card");

                $.ajax({
                    type: "POST",
                    url: action,
                    data: formData,
                    processData: false,
                    contentType: false,
                    complete: function() {
                        $submitBtn.html(`
                            <i class="fas fa-paper-plane"></i> @lang('Get Now')
                        `).removeClass('disabled').attr('disabled', false);
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $("#pin-modal").modal('hide');
                            $cardElement.addClass('shake');
                            $cardElement.find(`.virtual-card-number .text`).text(atob(response.data
                                .number).replace(/(.{4})/g, '$1 ').trim());
                            $cardElement.find(`.cvc-number`).text(atob(response.data.cvc));
                            $cardElement.find('.card-confidential')
                                .html(`<i class="fa-regular fa-clone"></i>`)
                                .removeClass('card-confidential')
                                .off('click')
                                .addClass('copyBoard');

                            setTimeout(() => {
                                $cardElement.removeClass('shake');
                            }, 1000);
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

            $(".card-confidential").on('click', function() {
                $("#pin-modal").modal('show');
            });

            $('.breadcrumb-plugins-wrapper').remove();


            $('body').on('click', ".copyBoard", function() {
                const $this = $(this);
                const text = $('.virtual-card-number').find('.text').text();

                const oldHtml = $this.html();

                const tempTextArea = document.createElement('textarea');
                tempTextArea.value = text;
                tempTextArea.style.width = 0;
                tempTextArea.style.height = 0;

                document.body.appendChild(tempTextArea);


                tempTextArea.select();
                tempTextArea.setSelectionRange(0, 99999);

                navigator.clipboard.writeText(text).then(function() {
                    $this.html(`<i class="las la-check-double fw-bold me-2"></i> Copied`);
                    setTimeout(function() {
                        $this.html(oldHtml);
                    }, 1500);
                }).catch(function(error) {
                    console.error('Copy failed!', error);
                });

                document.body.removeChild(tempTextArea);
            });

        })(jQuery);
    </script>
@endpush
