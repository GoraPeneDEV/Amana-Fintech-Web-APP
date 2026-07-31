@php
    $operatingCountries = App\Models\OperatingCountry::active()->get();
    $info               = json_decode(json_encode(getIpInfo()), true);
    $mobileCode         = @implode(',', $info['code']);
    $countries          = json_decode(file_get_contents(resource_path('views/partials/country.json')));
@endphp

<div class="select2--container position-relative">
    <select name="country" class="form-control country-select" data-minimum-results-for-search="-1">
        @foreach ($operatingCountries as $country)
            <option value="{{ $country->id }}"
                data-image-src="{{ asset('assets/images/country/' . strtolower($country->code) . '.svg') }}"
                @selected($country->code == $mobileCode )>
                {{ $country->dial_code }}
            </option>
        @endforeach
    </select>
</div>

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush
@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var $state = $(
                    '<span class="img-flag-inner"><img src="' + $(state.element).attr('data-image-src') +
                    '" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            };
            $('.country-select')
                .select2({
                    templateResult: formatState,
                    templateSelection: formatState,
                    dropdownParent: $('.select2--container'),
                });
        })(jQuery);
    </script>
@endpush
