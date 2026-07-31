@extends($activeTemplate . 'layouts.app')
@section('app-content')
    <div class="dashboard position-relative merchant-dashboard">
        <div class="dashboard__inner flex-wrap">
            @include('Template::partials.merchant_sidebar')
            <div class="dashboard__right">
                @include('Template::partials.merchant_header')
                <div class="dashboard-body">
                    <div class="container-fluid">
                        <div class="flex-between mb-4 breadcrumb-plugins-wrapper">
                            <h4> {{ __($pageTitle) }} </h4>
                            <div>
                                @stack('breadcrumb-plugins')
                            </div>
                        </div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
