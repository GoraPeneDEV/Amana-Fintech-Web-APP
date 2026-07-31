<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">

    @stack('style-lib')

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}">

    @stack('style')
    <link rel="stylesheet"
        href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ gs('base_color') }}&secondColor={{ gs('secondary_color') }}&merchant={{ gs('merchant_panel_color') }}&agent={{ gs('agent_panel_color') }}">
</head>
@php echo loadExtension('google-analytics') @endphp

<body>

    <div class="preloader">
        <img src="{{ getImage(getFilePath('preloader') . '/' . gs('preloader_image')) }}" alt="image">
    </div>

    <div class="body-overlay"></div>
    <div class="sidebar-overlay"></div>
    <a class="scroll-top"><i class="fas fa-angle-up"></i></a>

    @yield('app-content')


    <div class="full-page-loader d-none">
        <div class="lds-spinner">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    @stack('end-content')



    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>

    @php
        $pusherConfig = gs('pusher_config');
    @endphp
    <script>
        window.my_pusher = {
            'app_key': "{{ base64_encode(@$pusherConfig->app_key) }}",
            'app_cluster': "{{ base64_encode(@$pusherConfig->cluster) }}",
            'base_url': "{{ route('home') }}"
        }
    </script>


    @stack('script-lib')

    <script src="{{ asset('assets/global/js/global.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>
    @php echo loadExtension('tawk-chat') @endphp

    @include('Template::partials.cookie')

    @include('partials.notify')

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')

    <script>
        (function($) {
            "use strict";

            //plicy
            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            //show cookie card
            setTimeout(function() {
                $('.cookies-card').removeClass('hide');
            }, 2000);
        })(jQuery);
    </script>


</body>

</html>
