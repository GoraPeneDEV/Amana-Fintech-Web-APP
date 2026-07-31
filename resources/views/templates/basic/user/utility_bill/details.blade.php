@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('user.utility.bill.history') }}">
            <span class="icon" title="@lang('Utility Bill History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __($pageTitle) }}
        </a>
    </h4>
    @include('Template::user.utility_bill.receipt')
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
