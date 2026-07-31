@extends($activeTemplate . 'layouts.app')
@section('app-content')
    @stack('fbComment')
    @include('Template::partials.header')
    <main>
        @if (!request()->routeIs('home'))
            @include('Template::partials.breadcrumb')
        @endif
        @yield('content')
    </main>
    @include('Template::partials.footer')
@endsection
