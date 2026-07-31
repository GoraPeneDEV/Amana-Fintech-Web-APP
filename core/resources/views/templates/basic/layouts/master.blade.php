@extends($activeTemplate . 'layouts.app')
@section('app-content')
    <div class="dashboard position-relative">
        <div class="dashboard__inner flex-wrap">
            @include('Template::partials.auth_sidebar')
            <div class="dashboard__right">
                @include('Template::partials.auth_header')
                <div class="dashboard-body">
                    <div class="container-fluid">
                        <div class="flex-between mb-4 breadcrumb-plugins-wrapper gap-2">
                            <h4> {{ __($pageTitle) }} </h4>
                            <div class="breadcrump-wrapper-inner">
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
