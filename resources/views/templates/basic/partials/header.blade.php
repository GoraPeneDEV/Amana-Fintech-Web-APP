<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-xl navbar-light justify-content-between">
            <a class="navbar-brand logo" href="{{ route('home') }}">
                <img src="{{ siteLogo() }}" alt="logo">
            </a>
            <button class="header-button navbar-toggler" type="button">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>
            <div class="custom-nav flex-grow-1 border-0" tabindex="-1" id="offcanvasDarkNavbar">
                <div class="custom-nav-header d-xl-none">
                    <a class="logo navbar-brand" href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}" alt="">
                    </a>
                    <button type="button" class="btn-close header-close">
                    </button>
                </div>
                <div class="custom-nav-body">
                    <ul class="navbar-nav nav-menu align-items-xl-center justify-content-end w-100">
                        <li class="nav-item  {{ menuActive('home') }}">
                            <a class="nav-link" href="{{ route('home') }}">@lang('Home')</a>
                        </li>
                        @foreach ($pages as $page)
                            <li class="nav-item {{ menuActive('pages', $page->slug) }}">
                                <a class="nav-link" href="{{ route('pages', $page->slug) }}">
                                    {{ __($page->name) }}
                                </a>
                            </li>
                        @endforeach
                        <li class="nav-item {{ menuActive('blogs') }}">
                            <a class="nav-link" href="{{ route('blogs') }}">@lang('Blog')</a>
                        </li>
                        <li class="nav-item {{ menuActive('contact') }}">
                            <a class="nav-link" href="{{ route('contact') }}">@lang('Contact')</a>
                        </li>
                        <li class="nav-item d-xl-none d-block">
                            <a href="#download-app" class="btn w-100 btn--md pill btn--base">@lang('Download App')</a>
                        </li>
                        <li class="nav-item d-xl-none d-block">
                            @auth
                                <a href="{{ route('user.home') }}" class="btn w-100 btn--md pill btn-outline--dark">
                                    <span class="icon">
                                        <i class="fa-regular fa-circle-user"></i>
                                    </span>
                                    @lang('Dashboard')
                                </a>
                            @else
                                <a href="{{ route('user.login') }}" class="btn w-100 btn--md pill btn-outline--dark">
                                    <span class="icon">
                                        <i class="fa-regular fa-circle-user"></i>
                                    </span>
                                    @lang('Login')
                                </a>
                            @endauth
                        </li>
                    </ul>
                </div>
            </div>

            <div class="header-right d-none d-xl-flex">
                <a href="#download-app" class="btn btn--md pill btn--base">@lang('Download App')</a>
                @auth
                    <a href="{{ route('user.home') }}" class="btn btn--md pill btn-outline--dark">
                        <span class="icon">
                            <i class="fa-regular fa-circle-user"></i>
                        </span>
                        @lang('Dashboard')
                    </a>
                @else
                    <a href="{{ route('user.login') }}" class="btn btn--md pill btn-outline--dark">
                        <span class="icon">
                            <i class="fa-regular fa-circle-user"></i>
                        </span>
                        @lang('Login')
                    </a>
                @endauth
            </div>
        </nav>
    </div>
</header>
