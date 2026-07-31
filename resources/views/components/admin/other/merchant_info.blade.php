@props(['merchant'])
@if ($merchant)
    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end justify-content-md-start">
        <span class="table-thumb d-none d-lg-block">
            @if (@$merchant->image)
                <img src="{{ $merchant->image_src }}" alt="merchant">
            @else
                <span class="name-short-form">
                    {{ __(@$merchant->full_name_short_form ?? 'N/A') }}
                </span>
            @endif
        </span>
        <div class="text-start">
            <strong class="d-block">
                {{ __(@$merchant->fullname) }}
            </strong>
            <a class="fs-13" href="{{ route('admin.merchants.detail', $merchant->id) }}">{{ @$merchant->username }}</a>
        </div>
    </div>
@else
    <span>@lang('N/A')</span>
@endif
