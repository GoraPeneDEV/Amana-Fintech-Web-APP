@extends($activeTemplate . 'layouts.agent')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('agent.cash.in.history') }}">
            <span class="icon" title="@lang('Cash In History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __($pageTitle) }}
        </a>
    </h4>
    @include('Template::agent.cash_in.receipt')
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
