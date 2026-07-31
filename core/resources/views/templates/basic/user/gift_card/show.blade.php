@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center justify-content-xl-start">
        <div class="col-xxl-6 col-xl-8 col-lg-8">
            <form action="{{ route('user.gift.card.purchase', $product->id) }}" method="POST"
                class="purchase-form no-submit-loader">
                @csrf
                <div class="card custom--card mb-3">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="justify-content-start">
                                <label class="single-operator">
                                    <span class="img">
                                        @if (!empty($product->logo_urls) && isset($product->logo_urls[0]))
                                            <img src="{{ $product->logo_urls[0] }}">
                                        @endif
                                    </span>
                                    <span class="title">{{ __($product->product_name) }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="form--group form-group">
                            <label class="form--label">@lang('To')</label>
                            <input type="email" name="email" class="form--control form-control"
                                placeholder="@lang('Enter recipient email')" value="{{ old('email') }}" required>
                        </div>
                        <div class="form--group form-group">
                            <label class="form--label">@lang('From')</label>
                            <input type="text" name="name" class="form--control form-control"
                                placeholder="@lang('Enter your name')" value="{{ old('name', $user?->fullname) }}" required>
                        </div>
                        <div class="form--group form-group">
                            <label class="form--label">@lang('Enter Amount')</label>
                            @if ($product->denomination_type == 'RANGE')
                                <input type="number" class="form--control form-control range-amount"
                                    data-min="{{ getAmount($product->min_recipient_denomination) }}"
                                    data-max="{{ getAmount($product->max_recipient_denomination) }}"
                                    placeholder="@lang('0.00')" value="{{ old('amount') }}" name="amount" required>
                                <span class="mt-2">
                                    {{ getAmount($product->min_recipient_denomination) }}
                                    {{ $product->recipient_currency_code }}
                                    -
                                    {{ getAmount($product->max_recipient_denomination) }}
                                    {{ $product->recipient_currency_code }}
                                </span>
                            @else
                                <input type="number" class="form--control form-control" placeholder="@lang('0.00')"
                                    value="{{ old('amount') }}" name="amount" required readonly>
                                <div class="mt-3">
                                    @forelse (@$product->fixed_recipient_denominations ?? [] as $fix)
                                        <span class="badge badge--info price-badge" data-amount="{{ getAmount($fix) }}">
                                            {{ getAmount($fix) }} {{ $product->recipient_currency_code }}
                                        </span>
                                    @empty
                                        @lang('N/A')
                                    @endforelse
                                </div>
                            @endif
                        </div>
                        <div class="form--group form-group">
                            <label class="form--label">@lang('Quantity')</label>
                            <input type="number" name="quantity" class="form--control form-control" min="1"
                                value="{{ old('quantity', 1) }}" required>
                        </div>
                        <div class="verification-type-wrapper d-none">
                            <x-otp_verification remark="gift_card" />
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn--base text-start w-100 buy-preview">
                    <span class="flex-between">
                        @lang('Buy Now')
                        <span class="icon">
                            <i class="fas fa-arrow-right-long"></i>
                        </span>
                    </span>
                </button>
            </form>
        </div>
    </div>

    <div class="modal fade custom--modal fade" id="prev-modal">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-img"
                    data-background-image="{{ asset($activeTemplateTrue . 'images/modal_shape.png') }}">
                    <h4 class="modal-title">@lang('Purchase Preview')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 ps-0">
                            <span>@lang('Recipient')</span>
                            <span class="receipt-email"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 ps-0">
                            <span>@lang('From')</span>
                            <span class="form-name"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 ps-0">
                            <span>@lang('Unit Price')</span>
                            <span>
                                <span class="unit-price"></span>
                                
                                {{ __(gs('cur_text')) }}
                                (<span class="amount"></span>{{ __($product->recipient_currency_code) }})
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 ps-0">
                            <span>@lang('Quantity')</span>
                            <span class="quantity">1</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 ps-0">
                            <span>@lang('Subtotal')</span>
                            <span>
                                <span class="subtotal"></span>
                                <span>{{ __(gs('cur_text')) }}</span>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 ps-0">
                            <span>@lang('Total Fee')</span>
                            <span>
                                <span class="fee"></span>
                                <span>
                                    {{ __(gs('cur_text')) }}
                                </span>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 ps-0">
                            <span>@lang('Total')</span>
                            <span>
                                <span class="total"></span>
                                <span>{{ __(gs('cur_text')) }}</span>
                            </span>
                        </li>
                    </ul>
                    <button type="button" class="btn btn--base text-start w-100 mt-3 confirm-btn">
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
    </div>
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {

            $(".confirm-btn").on('click', function(e) {
                e.preventDefault(); // stop default submit
                const form = $(".purchase-form");
                form.unbind("submit");
                form.addClass('has-otp-form');
                $('body').find('.has-otp-form').trigger('submit');
            });

            $(".range-amount").on('input', function() {
                const max = parseFloat($(this).data('max'));
                const min = parseFloat($(this).data('min'));
                const value = parseFloat($(this).val());

                if (!value) return false;

                if (value < min) {
                    $(".buy-preview").attr('disabled', true).addClass('disabled');
                    notify('error', `Please enter amount between ${min} and ${max}`);
                    return false;
                }
                if (value > max) {
                    $(".buy-preview").attr('disabled', true).addClass('disabled');
                    notify('error', `Please enter amount between ${min} and ${max}`);
                    return false;
                }

                $(".buy-preview").attr('disabled', false).removeClass('disabled');

            });

            $('.price-badge').on('click', function() {
                $('input[name=amount]').val($(this).data('amount'));
            });

            $('.buy-preview').on('click', function() {

                const email = $('input[name=email]').val();
                const name = $('input[name=name]').val();
                const quantity = parseInt($('input[name=quantity]').val());
                const amount = parseFloat($('input[name=amount]').val());
                const exchangeRate = parseFloat("{{ $product->recipient_to_sender_exchange_rate }}");
                const fixedFee = parseInt("{{ $product->sender_fee }}");
                const percentFee = parseInt("{{ $product->sender_fee_percentage }}");
                const fee = fixedFee + (amount * (percentFee / 100));


                if (!email || !email.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/)) {
                    notify("error", "@lang('Please enter a valid recipient email')");
                    return false;
                }

                if (!name || !name.match(/^[a-zA-Z ]+$/)) {
                    notify("error", "@lang('Please enter your valid name')");
                    return false;
                }

                if (!quantity || quantity <= 0) {
                    notify("error", "@lang('Please enter a valid quantity')");
                    return false;
                }

                const unitPrice = parseFloat(exchangeRate*amount);
                const subtotal = parseFloat(unitPrice * quantity);
                const totalFee = parseFloat(fee * quantity);
                const total =  parseFloat(totalFee + subtotal);

                $(".quantity").text(quantity);
                $(".unit-price").text(unitPrice.toFixed(2));
                $(".subtotal").text(subtotal.toFixed(2));
                $(".amount").text(amount.toFixed(2));
                $(".total").text(total.toFixed(2));

                if (quantity == 1) {
                    $(".fee").text(totalFee.toFixed(2));
                } else {
                    $(".fee").text(`${fee} x ${quantity} = ${totalFee.toFixed(2)}`);
                }

                $(".receipt-email").text(email);
                $(".form-name").text(name);
                $("#prev-modal").modal('show');
            });

        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .single-operator .img {
            width: 150px;
            height: 100px;
            border-radius: 16px;
            overflow: hidden;
            display: grid;
            place-content: center;
            border: 1px solid hsl(var(--border-color));
            -ms-flex-negative: 0;
            flex-shrink: 0;
        }

        .single-operator .img img {
            max-width: 150px;
            width: 100%;
        }

        @media screen and (max-width: 1399px) {
            .single-operator .img {
                width: 48px;
                height: 48px;
            }
        }

        .price-badge {
            cursor: pointer;
        }
    </style>
@endpush


@push('breadcrumb-plugins')
    <a class="btn btn--base btn--md" href="{{ route('user.gift.card.create') }}">
        <span class="icon">
            <i class="las la-arrow-circle-left"></i>
        </span>
        @lang('Back')
    </a>
@endpush
