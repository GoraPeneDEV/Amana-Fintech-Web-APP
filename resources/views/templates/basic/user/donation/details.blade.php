@extends($activeTemplate . 'layouts.master')
@section('content')
<h4 class="mb-4">
    <a href="{{ route('user.donation.history') }}">
        <span class="icon" title="@lang('Donation  History')">
            <i class="las la-arrow-circle-left"></i>
        </span>
        {{ __($pageTitle) }}
    </a>
</h4>
    @include('Template::user.donation.receipt')
@endsection

@push('script')
    <script>
        "use strict";
        (function ($) {
            $('.breadcrumb-plugins-wrapper').remove();
        })(jQuery);
        
    </script>
@endpush