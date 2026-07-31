@extends($activeTemplate . 'layouts.master')
@section('content')
<h4 class="mb-4">
    <a href="{{ route('user.cash.out.history') }}">
        <span class="icon" title="@lang('CashOut History')">
            <i class="las la-arrow-circle-left"></i>
        </span>
        {{ __($pageTitle) }}
    </a>
</h4>
    @include('Template::user.cash_out.receipt')
@endsection

@push('script')
    <script>
        "use strict";
        (function ($) {
            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush