@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('admin.promotion.offer.store', $offer->id ?? 0) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <x-admin.ui.card>
                    <x-admin.ui.card.header>
                        <h4 class="card-title">@lang('General Information')</h4>
                    </x-admin.ui.card.header>
                    <x-admin.ui.card.body>
                        <div class="row">

                            <div class="col-6">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <x-image-uploader :size="getFileSize('offer')" name="image" :imagePath="@$offer->image_src" :required="false" />
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Offer Name')</label>
                                    <input type="text" class="form-control" name="offer_name"
                                        value="{{ old('offer_name', @$offer->name) }}" placeholder="@lang('Type Here')..."
                                        required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Discount Type')</label>
                                    <select class="form-control" name="discount_type" required>
                                        <option value="">@lang('Select Discount Type')</option>
                                        <option value="1"
                                            {{ old('discount_type', @$offer->discount_type) == '1' ? 'selected' : '' }}>
                                            @lang('Fixed')
                                        </option>
                                        <option value="2"
                                            {{ old('discount_type', @$offer->discount_type) == '2' ? 'selected' : '' }}>
                                            @lang('Percentage')
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Amount')</label>
                                    <div class="input-group input--group">
                                        <input type="number" class="form-control" name="amount"
                                            value="{{ old('amount', getAmount(@$offer->amount)) }}"
                                            placeholder="@lang('Type Here')..." required>
                                        <span class="input-group-text">
                                            <span id="discount_type_text">
                                                {{ @$offer ? ($offer->discount_type == 1 ? gs()->cur_text : '%') : gs()->cur_text }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Minimum Payment')</label>
                                    <div class="input-group input--group">
                                        <input type="number" class="form-control" name="min_payment"
                                            value="{{ old('min_payment', getAmount(@$offer->min_payment)) }}"
                                            placeholder="@lang('Type Here')..." required>
                                        <span class="input-group-text">{{ gs()->cur_text }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Maximum Discount')</label>
                                    <div class="input-group input--group">
                                        <input type="number" class="form-control" name="maximum_discount"
                                            value="{{ old('maximum_discount', getAmount(@$offer->maximum_discount)) }}"
                                            placeholder="@lang('Type Here')...">
                                        <span class="input-group-text">{{ gs()->cur_text }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Start Date')</label>
                                    <input type="text" name="start_date" class="date-picker form-control"
                                        data-language='en' data-position='bottom left'
                                        value="{{ old('start_date', showDateTime(@$offer->start_date)) }}"
                                        placeholder="@lang('Select Date')" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('End Date')</label>
                                    <input type="text" name="end_date" class="date-picker form-control"
                                        data-language='en' data-position='bottom left'
                                        value="{{ old('end_date', showDateTime(@$offer->end_date)) }}"
                                        placeholder="@lang('Select Date')" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label>@lang('Link')</label>
                                    <input type="text" class="form-control" name="link"
                                        value="{{ old('link', @$offer->link) }}" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>@lang('Select Merchant')</label>
                                    <select class="form-control select2" name="merchant_id" required>
                                        <option value="" disabled selected>@lang('Select')</option>
                                        @foreach ($merchants as $merchant)
                                            <option value="{{ $merchant->id }}"
                                                {{ @$offer && $offer->merchant_id == $merchant->id ? 'selected' : '' }}>
                                                {{ __($merchant->username) }}({{ $merchant->mobileNumber }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control" name="description" rows="3" required>{{ old('description', @$offer->description) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-admin.ui.btn.submit />
                            </div>
                        </div>
                    </x-admin.ui.card.body>
                </x-admin.ui.card>
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back_btn route="{{ route('admin.promotion.offer.index') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/flatpickr.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/flatpickr.min.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            $(".date-picker").flatpickr();

            $('#discount_type').change(function() {
                var discountType = $(this).val();
                var textElement = $('#discount_type_text');
                if (discountType == 1) {
                    textElement.text('{{ gs()->cur_text }}');
                } else if (discountType == 2) {
                    textElement.text('%');
                } else {
                    textElement.text('{{ gs()->cur_text }}');
                }
            });

        })(jQuery)
    </script>
@endpush
