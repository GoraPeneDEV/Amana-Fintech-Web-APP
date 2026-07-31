@extends('admin.layouts.app')
@section('panel')
    <form action="{{ route('admin.virtual.card.provider.configuration.update', $gateway->code) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="row gy-4">
            <div class="col-12">
                <div class="alert alert--info d-flex" role="alert">
                    <div class="alert__icon">
                        <i class="las la-info"></i>
                    </div>
                    <div class="alert__content">
                        <h6 class="mb-1 text--info">@lang('Virtual Card Issuance via Stripe')</h6>
                        <p>
                            @lang("The platform uses Stripe to generate virtual cards, supporting currency provided below. Ensure your platform’s currency matches Stripe’s; otherwise, card generation won't be possible. Update your Stripe API configuration below and verify all credentials.")
                            <span class="cursor-pointer" data-bs-target="#modal" data-bs-toggle="modal">
                                <i>@lang('Stripe Supported Currency')</i>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <x-admin.ui.card>
                    <x-admin.ui.card.body>
                        <div class="row justify-content-center mb-5">
                            <div class="col-lg-6">
                                <div class="payment-method-item">
                                    <div class="payment-method-item__left">
                                        <x-image-uploader
                                            imagePath="{{ getImage(getFilePath('gateway') . '/' . $gateway->image, getFileSize('gateway')) }}"
                                            type="gateway" :required=false />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center gy-4">
                            <div class="col-sm-10">
                                <div class="row justify-content-center">
                                    @if ($gateway->code < 1000 && $gateway->extra)
                                        @foreach ($gateway->extra as $key => $param)
                                            <div class="col-sm-6 form-group">
                                                <label for="form-label">{{ __(@$param->title) }}</label>
                                                <div class="input-group input--group">
                                                    <input type="text" class="form-control"
                                                        value="{{ route($param->value) }}" readonly>
                                                    <span class="input-group-text cursor-pointer copyBtn"
                                                        data-copy="{{ route($param->value) }}">
                                                        <i class="fas fa-copy me-1"></i>@lang('Copy')</span>
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @foreach ($parameters->where('global', true) as $key => $param)
                                        <div class="form-group col-sm-6">
                                            <label>{{ __(@$param->title) }}</label>
                                            <input type="text" class="form-control" name="{{ $key }}"
                                                value="{{ @$param->value }}" required>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-end">
                                    <x-admin.ui.btn.submit />
                                </div>
                            </div>
                        </div>
                    </x-admin.ui.card.body>
                </x-admin.ui.card>
            </div>
        </div>
    </form>

    <x-admin.ui.modal id="modal">
        <x-admin.ui.modal.header>
            <h4 class="modal-title">@lang('Stripe Supported Currency')</h4>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <div class="my-4 d-flex gap-3 flex-wrap">
                @foreach ($gateway->supported_currencies as $currency)
                    <span class="badge badge--primary fs-14">{{ __($currency) }}</span>
                @endforeach
            </div>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>
@endsection
