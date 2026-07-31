@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-4">
        <a href="{{ route('user.send.money.history') }}">
            <span class="icon" title="@lang('Send Money History')">
                <i class="las la-arrow-circle-left"></i>
            </span>
            {{ __($pageTitle) }}
        </a>
    </h4>
    @include('Template::user.send_money.receipt')
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
    </script>
@endpush
