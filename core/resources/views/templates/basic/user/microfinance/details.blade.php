@extends($activeTemplate . 'layouts.master')
@section('content')
<h4 class="mb-4">
    <a href="{{ route('user.microfinance.history') }}">
        <span class="icon" title="@lang('Microfinance History')">
            <i class="las la-arrow-circle-left"></i>
        </span>
        {{ __($pageTitle) }}
    </a>
</h4>
    @include('Template::user.microfinance.receipt')
@endsection

@push('script')
    <script>
        "use strict";
        (function ($) {
            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
        
    </script>
@endpush