@php
    $user = auth('agent')->user();
@endphp
<div class="sidebar-menu">
    <span class="sidebar-menu__close d-lg-none d-block"><i class="fas fa-times"></i></span>
    <div class="sidebar-logo">
        <a href="{{ route('agent.home') }}" class="sidebar-logo__link">
            <img src="{{ siteLogo() }}" alt="logo">
        </a>
    </div>
    <div class="d-xl-none">
        <div class="dropdown user-dropdown">
            <button class="lang-box-btn" data-bs-toggle="dropdown">
                <span class="user-info">
                    <span class="user-info-wrapper">
                        <span class="user-info-thumb">
                            <img class="fit-image" src="{{ $user->image_src }}" alt="">
                        </span>
                        <span class="user-info-content text-start">
                            <span class="name">{{ __($user->fullname) }}</span>
                            <span class="phone">{{ $user->mobileNumber }}</span>
                        </span>
                    </span>
                </span>
            </button>
            <ul class="dropdown-menu">
                <li class="user-dropdown-item">
                    <a href="{{ route('agent.profile.setting') }}" class="user-dropdown-link">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                fill="none">
                                <path d="M14 8.99988H18" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                                <path d="M14 12.4999H17" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                                <rect x="2" y="2.99988" width="20" height="18" rx="5"
                                    stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                <path d="M5 15.9999C6.20831 13.4188 10.7122 13.249 12 15.9999" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M10.5 8.99988C10.5 10.1044 9.60457 10.9999 8.5 10.9999C7.39543 10.9999 6.5 10.1044 6.5 8.99988C6.5 7.89531 7.39543 6.99988 8.5 6.99988C9.60457 6.99988 10.5 7.89531 10.5 8.99988Z"
                                    stroke="currentColor" stroke-width="1.5" />
                            </svg>
                        </span>
                        <span class="text">@lang('Profile')</span>
                    </a>
                </li>
                <li class="user-dropdown-item">
                    <a href="{{ route('agent.change.password') }}" class="user-dropdown-link">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                fill="none">
                                <path
                                    d="M4.26781 18.8447C4.49269 20.515 5.87613 21.8235 7.55966 21.9009C8.97627 21.966 10.4153 22 12 22C13.5847 22 15.0237 21.966 16.4403 21.9009C18.1239 21.8235 19.5073 20.515 19.7322 18.8447C19.879 17.7547 20 16.6376 20 15.5C20 14.3624 19.879 13.2453 19.7322 12.1553C19.5073 10.485 18.1239 9.17649 16.4403 9.09909C15.0237 9.03397 13.5847 9 12 9C10.4153 9 8.97627 9.03397 7.55966 9.09909C5.87613 9.17649 4.49269 10.485 4.26781 12.1553C4.12105 13.2453 4 14.3624 4 15.5C4 16.6376 4.12105 17.7547 4.26781 18.8447Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path d="M7.5 9V6.5C7.5 4.01472 9.51472 2 12 2C14.4853 2 16.5 4.01472 16.5 6.5V9"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M16 15.49V15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M12 15.49V15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M8 15.49V15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="text">@lang('Change PIN')</span>
                    </a>
                </li>
                <li class="user-dropdown-item">
                    <a href="{{ route('agent.twofactor') }}" class="user-dropdown-link">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                fill="none">
                                <path
                                    d="M10 10.5V9C10 7.89543 10.8954 7 12 7C13.1046 7 14 7.89543 14 9V10.5M11.25 16H12.75C13.9228 16 14.5092 16 14.9131 15.69C15.0171 15.6102 15.1102 15.5171 15.19 15.4131C15.5 15.0092 15.5 14.4228 15.5 13.25C15.5 12.0772 15.5 11.4908 15.19 11.0869C15.1102 10.9829 15.0171 10.8898 14.9131 10.81C14.5092 10.5 13.9228 10.5 12.75 10.5H11.25C10.0772 10.5 9.49082 10.5 9.08686 10.81C8.98286 10.8898 8.88977 10.9829 8.80997 11.0869C8.5 11.4908 8.5 12.0772 8.5 13.25C8.5 14.4228 8.5 15.0092 8.80997 15.4131C8.88977 15.5171 8.98286 15.6102 9.08686 15.69C9.49082 16 10.0772 16 11.25 16Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                <path
                                    d="M21 11.1833V8.28029C21 6.64029 21 5.82028 20.5959 5.28529C20.1918 4.75029 19.2781 4.49056 17.4507 3.9711C16.2022 3.6162 15.1016 3.18863 14.2223 2.79829C13.0234 2.2661 12.424 2 12 2C11.576 2 10.9766 2.2661 9.77771 2.79829C8.89839 3.18863 7.79784 3.61619 6.54933 3.9711C4.72193 4.49056 3.80822 4.75029 3.40411 5.28529C3 5.82028 3 6.64029 3 8.28029V11.1833C3 16.8085 8.06277 20.1835 10.594 21.5194C11.2011 21.8398 11.5046 22 12 22C12.4954 22 12.7989 21.8398 13.406 21.5194C15.9372 20.1835 21 16.8085 21 11.1833Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </span>
                        <span class="text">@lang('2FA Security')</span>
                    </a>
                </li>
                <li class="user-dropdown-item">
                    <a href="{{ route('agent.notification.setting') }}" class="user-dropdown-link">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                fill="none">
                                <path
                                    d="M2.52992 14.394C2.31727 15.7471 3.268 16.6862 4.43205 17.1542C8.89481 18.9486 15.1052 18.9486 19.5679 17.1542C20.732 16.6862 21.6827 15.7471 21.4701 14.394C21.3394 13.5625 20.6932 12.8701 20.2144 12.194C19.5873 11.2975 19.525 10.3197 19.5249 9.27941C19.5249 5.2591 16.1559 2 12 2C7.84413 2 4.47513 5.2591 4.47513 9.27941C4.47503 10.3197 4.41272 11.2975 3.78561 12.194C3.30684 12.8701 2.66061 13.5625 2.52992 14.394Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M9 21C9.79613 21.6219 10.8475 22 12 22C13.1525 22 14.2039 21.6219 15 21"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="text">@lang('Notification Setting')</span>
                    </a>
                </li>
                <li class="devide"></li>
                <li class="user-dropdown-item">
                    <a href="{{ route('agent.transactions') }}" class="user-dropdown-link">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                height="20" fill="none">
                                <path
                                    d="M4.5795 8.60699L2 8.45417C3.849 3.70488 9.15764 0.999849 14.3334 2.34477C19.8461 3.77722 23.1205 9.26153 21.647 14.5943C20.4283 19.0051 16.3433 21.9307 11.8479 22"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M12 22C6.5 22 2 17 2 11" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="0.5 3" />
                                <path
                                    d="M13.6039 9.72177C13.2524 9.35267 12.3906 8.48536 11.0292 9.10111C9.66784 9.71686 9.45159 11.698 11.5108 11.9085C12.4416 12.0036 13.0484 11.7981 13.6039 12.3794C14.1595 12.9607 14.2627 14.5774 12.8425 15.013C11.4222 15.4487 10.502 14.7292 10.2545 14.5041M11.9078 8.01953V8.81056M11.9078 15.1471V16.0195"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="text">@lang('All Transaction')</span>
                    </a>
                </li>
                <li class="user-dropdown-item">
                    <a href="{{ route('ticket.index') }}" class="user-dropdown-link">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                height="20" fill="none">
                                <path d="M5.5 19V8.5C5.5 4.91015 8.41015 2 12 2C15.5899 2 18.5 4.91015 18.5 8.5V19"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M18.8688 22H5.13104C4.51972 22 4.21406 22 4.06951 21.7924C3.92497 21.5848 4.02157 21.2845 4.21477 20.684C4.561 19.6077 4.9089 19 6.14897 19H17.8508C19.0907 19 19.4386 19.6077 19.785 20.6838C19.9784 21.2844 20.0751 21.5847 19.9305 21.7924C19.786 22 19.4803 22 18.8688 22Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M10.5 11H13.5" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M4.5 8H19.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="text">@lang('Support Ticket')</span>
                    </a>
                </li>
                <li class="user-dropdown-item">
                    <a href="{{ route('agent.logout') }}" class="user-dropdown-link">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                height="20" fill="none">
                                <path
                                    d="M14 3.09502C13.543 3.03241 13.0755 3 12.6 3C7.29807 3 3 7.02944 3 12C3 16.9706 7.29807 21 12.6 21C13.0755 21 13.543 20.9676 14 20.905"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path
                                    d="M13.5 14.5C12.9943 14.0085 11 12.7002 11 12M13.5 9.5C12.9943 9.99153 11 11.2998 11 12M11 12L21 12"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="text">@lang('Logout')</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <form action="#" class="sidebar-search">
        <button type="button" class="icon" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none">
                <path
                    d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M20.9992 21.0002L16.6992 16.7002" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <input type="text" class="form--control search-sidebar" placeholder="@lang('Search Service')...">
        <span class="bar">/</span>
    </form>
    <ul class="sidebar-menu-list mt-4">
        <li class="menu-title">@lang('Main')</li>
        <li class="sidebar-menu-list__item {{ menuActive('agent.home') }}">
            <a href="{{ route('agent.home') }}" class="sidebar-menu-list__link">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                        fill="none">
                        <path
                            d="M5.04271 1.04199C5.79143 1.04197 6.41577 1.04195 6.91142 1.10858C7.43458 1.17893 7.90692 1.33365 8.28647 1.71321C8.66602 2.09277 8.82077 2.5651 8.8911 3.08826C8.95777 3.58391 8.95768 4.20825 8.95768 4.95698V6.71033C8.95768 7.45905 8.95777 8.08342 8.8911 8.57908C8.82077 9.10224 8.66602 9.57458 8.28647 9.95408C7.90692 10.3337 7.43458 10.4884 6.91142 10.5587C6.41577 10.6254 5.79143 10.6253 5.04271 10.6253H4.95602C4.2073 10.6253 3.58293 10.6254 3.08728 10.5587C2.56412 10.4884 2.09179 10.3337 1.71223 9.95408C1.33267 9.57458 1.17795 9.10224 1.10761 8.57908C1.04097 8.08342 1.04099 7.45908 1.04102 6.71035V4.95699C1.04099 4.20828 1.04097 3.58391 1.10761 3.08826C1.17795 2.5651 1.33267 2.09277 1.71223 1.71321C2.09179 1.33365 2.56412 1.17893 3.08728 1.10858C3.58293 1.04195 4.20727 1.04197 4.95599 1.04199H5.04271Z"
                            fill="currentColor" />
                        <path
                            d="M5.85373 12.708C6.22412 12.708 6.53331 12.708 6.78716 12.7253C7.05141 12.7433 7.30101 12.7822 7.543 12.8824C8.10452 13.115 8.55068 13.5612 8.78327 14.1227C8.88352 14.3647 8.92235 14.6143 8.94035 14.8785C8.95768 15.1323 8.95768 15.4416 8.95768 15.8119V15.8541C8.95768 16.2244 8.95768 16.5337 8.94035 16.7875C8.92235 17.0518 8.88352 17.3013 8.78327 17.5433C8.55068 18.1048 8.10452 18.551 7.543 18.7836C7.30101 18.8838 7.05141 18.9227 6.78716 18.9407C6.53331 18.958 6.22412 18.958 5.85373 18.958H4.14497C3.77458 18.958 3.46539 18.958 3.21155 18.9407C2.9473 18.9227 2.69769 18.8838 2.4557 18.7836C1.89417 18.551 1.44805 18.1048 1.21546 17.5433C1.11522 17.3013 1.07636 17.0518 1.05832 16.7875C1.04101 16.5337 1.04101 16.2244 1.04102 15.8541V15.8119C1.04101 15.4416 1.04101 15.1323 1.05832 14.8785C1.07636 14.6143 1.11522 14.3647 1.21546 14.1227C1.44805 13.5612 1.89417 13.115 2.4557 12.8824C2.69769 12.7822 2.9473 12.7433 3.21155 12.7253C3.46539 12.708 3.77458 12.708 4.14497 12.708H5.85373Z"
                            fill="currentColor" />
                        <path
                            d="M15.0427 9.375C15.7914 9.375 16.4158 9.37492 16.9114 9.44158C17.4346 9.51192 17.9069 9.66667 18.2864 10.0463C18.666 10.4258 18.8208 10.8981 18.8911 11.4212C18.9578 11.9169 18.9577 12.5412 18.9577 13.29V15.0433C18.9577 15.7921 18.9578 16.4164 18.8911 16.9121C18.8208 17.4353 18.666 17.9076 18.2864 18.2871C17.9069 18.6667 17.4346 18.8214 16.9114 18.8917C16.4158 18.9584 15.7914 18.9583 15.0427 18.9583H14.956C14.2073 18.9583 13.5829 18.9584 13.0873 18.8917C12.5641 18.8214 12.0918 18.6667 11.7123 18.2871C11.3327 17.9076 11.1779 17.4353 11.1076 16.9121C11.0409 16.4164 11.041 15.7921 11.041 15.0433V13.29C11.041 12.5412 11.0409 11.9169 11.1076 11.4212C11.1779 10.8981 11.3327 10.4258 11.7123 10.0463C12.0918 9.66667 12.5641 9.51192 13.0873 9.44158C13.5829 9.37492 14.2073 9.375 14.956 9.375H15.0427Z"
                            fill="currentColor" />
                        <path
                            d="M15.8538 1.04199C16.2241 1.04198 16.5333 1.04198 16.7872 1.0593C17.0514 1.07733 17.301 1.1162 17.543 1.21643C18.1045 1.44903 18.5507 1.89515 18.7833 2.45668C18.8835 2.69867 18.9223 2.94828 18.9403 3.21253C18.9577 3.46637 18.9577 3.77556 18.9577 4.14594V4.18804C18.9577 4.55843 18.9577 4.86762 18.9403 5.12147C18.9223 5.38572 18.8835 5.63532 18.7833 5.87731C18.5507 6.43883 18.1045 6.88496 17.543 7.11755C17.301 7.21778 17.0514 7.25666 16.7872 7.27468C16.5333 7.292 16.2241 7.292 15.8538 7.29199H14.1449C13.7746 7.292 13.4653 7.292 13.2115 7.27468C12.9473 7.25666 12.6977 7.21778 12.4557 7.11755C11.8942 6.88496 11.448 6.43883 11.2154 5.87731C11.1152 5.63532 11.0763 5.38572 11.0583 5.12147C11.041 4.86762 11.041 4.55843 11.041 4.18804V4.14595C11.041 3.77557 11.041 3.46637 11.0583 3.21253C11.0763 2.94828 11.1152 2.69867 11.2154 2.45668C11.448 1.89515 11.8942 1.44903 12.4557 1.21643C12.6977 1.1162 12.9473 1.07733 13.2115 1.0593C13.4653 1.04198 13.7746 1.04198 14.1449 1.04199H15.8538Z"
                            fill="currentColor" />
                    </svg>
                </span>
                <span class="text">@lang('My Dashboard')</span>
            </a>
        </li>

        @if (moduleIsEnable('add_money', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('agent.add.money.*') }}">
                <a href="{{ route('agent.add.money.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                            fill="none">
                            <path
                                d="M22 11C22 7.46252 22 5.69377 20.9472 4.5129C20.7788 4.32403 20.5932 4.14935 20.3925 3.99087C19.1379 3 17.2586 3 13.5 3H10.5C6.74142 3 4.86213 3 3.60746 3.99087C3.40678 4.14935 3.22119 4.32403 3.0528 4.5129C2 5.69377 2 7.46252 2 11C2 14.5375 2 16.3062 3.0528 17.4871C3.22119 17.676 3.40678 17.8506 3.60746 18.0091C4.86213 19 6.74142 19 10.5 19H12"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M18.5 21L18.5 14M15 17.5H22" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" />
                            <path d="M5.5 11H5.49102" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M14.5 11C14.5 12.3807 13.3807 13.5 12 13.5C10.6193 13.5 9.5 12.3807 9.5 11C9.5 9.61929 10.6193 8.5 12 8.5C13.3807 8.5 14.5 9.61929 14.5 11Z"
                                stroke="currentColor" stroke-width="1.5" />
                        </svg>
                    </span>
                    <span class="text">@lang('Add Money')</span>
                </a>
            </li>
        @endif

        @if (moduleIsEnable('cash_in', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('agent.cash.in*') }}">
                <a href="{{ route('agent.cash.in.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                            fill="none">
                            <path
                                d="M16 14C16 14.8284 16.6716 15.5 17.5 15.5C18.3284 15.5 19 14.8284 19 14C19 13.1716 18.3284 12.5 17.5 12.5C16.6716 12.5 16 13.1716 16 14Z"
                                stroke="currentColor" stroke-width="1.5" />
                            <path
                                d="M10 7H16C18.8284 7 20.2426 7 21.1213 7.87868C22 8.75736 22 10.1716 22 13V15C22 17.8284 22 19.2426 21.1213 20.1213C20.2426 21 18.8284 21 16 21H10C6.22876 21 4.34315 21 3.17157 19.8284C2 18.6569 2 16.7712 2 13V11C2 7.22876 2 5.34315 3.17157 4.17157C4.34315 3 6.22876 3 10 3H14C14.93 3 15.395 3 15.7765 3.10222C16.8117 3.37962 17.6204 4.18827 17.8978 5.22354C18 5.60504 18 6.07003 18 7"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>

                    </span>
                    <span class="text">@lang('Cash In')</span>
                </a>
            </li>
        @endif

        <li class="sidebar-menu-list__item {{ menuActive('agent.withdraw*') }}">
            <a href="{{ route('agent.withdraw.history') }}" class="sidebar-menu-list__link">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                        fill="none">
                        <path
                            d="M18.9349 13.9453L18.2646 10.2968C17.9751 8.72096 17.8303 7.93303 17.257 7.46651C16.6837 7 15.8602 7 14.2132 7H9.78685C8.1398 7 7.31628 7 6.74298 7.46651C6.16968 7.93303 6.02492 8.72096 5.73538 10.2968L5.06506 13.9453C4.46408 17.2162 4.16359 18.8517 5.08889 19.9259C6.01419 21 7.72355 21 11.1423 21H12.8577C16.2765 21 17.9858 21 18.9111 19.9259C19.8364 18.8517 19.5359 17.2162 18.9349 13.9453Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M12 10.5V17M9.5 15L12 17.5L14.5 15" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M21 11C21.1568 10.9209 21.2931 10.8212 21.4142 10.6955C22 10.0875 22 9.10893 22 7.15176C22 5.1946 22 4.21602 21.4142 3.60801C20.8284 3 19.8856 3 18 3L6 3C4.11438 3 3.17157 3 2.58579 3.60801C2 4.21602 2 5.1946 2 7.15176C2 9.10893 2 10.0875 2.58579 10.6955C2.70688 10.8212 2.84322 10.9209 3 11"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </span>
                <span class="text">@lang('Manage Withdraw')</span>
            </a>
        </li>


        <li class="sidebar-menu-list__item {{ menuActive('agent.statement.*') }}">
            <a href="{{ route('agent.statement.history') }}" class="sidebar-menu-list__link">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                        fill="none">
                        <path
                            d="M20.4999 10.5V10C20.4999 6.22876 20.4999 4.34315 19.3284 3.17157C18.1568 2 16.2712 2 12.4999 2H11.5C7.72883 2 5.84323 2 4.67166 3.17156C3.50009 4.34312 3.50007 6.22872 3.50004 9.99993L3.5 14.5C3.49997 17.7874 3.49996 19.4312 4.40788 20.5375C4.57412 20.7401 4.75986 20.9258 4.96242 21.0921C6.06877 22 7.71249 22 10.9999 22"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M7.5 7H16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M7.5 12H13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path
                            d="M20.5 20L20.5 17C20.5 15.5706 19.1569 14 17.5 14C15.8431 14 14.5 15.5706 14.5 17L14.5 20.5C14.5 21.3284 15.1716 22 16 22C16.8284 22 17.5 21.3284 17.5 20.5V17"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="text">@lang('Manage Statement')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item {{ menuActive('agent.transaction*') }}">
            <a href="{{ route('agent.transactions') }}" class="sidebar-menu-list__link">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                        fill="none">
                        <path
                            d="M4.5795 8.60699L2 8.45417C3.849 3.70488 9.15764 0.999849 14.3334 2.34477C19.8461 3.77722 23.1205 9.26153 21.647 14.5943C20.4283 19.0051 16.3433 21.9307 11.8479 22"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 22C6.5 22 2 17 2 11" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="0.5 3" />
                        <path
                            d="M13.6039 9.72177C13.2524 9.35267 12.3906 8.48536 11.0292 9.10111C9.66784 9.71686 9.45159 11.698 11.5108 11.9085C12.4416 12.0036 13.0484 11.7981 13.6039 12.3794C14.1595 12.9607 14.2627 14.5774 12.8425 15.013C11.4222 15.4487 10.502 14.7292 10.2545 14.5041M11.9078 8.01953V8.81056M11.9078 15.1471V16.0195"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="text">@lang('All Transaction')</span>
            </a>
        </li>

        <li class="menu-title">@lang('Setting & Support')</li>
        <li class="sidebar-menu-list__item {{ menuActive('agent.profile.setting') }}">
            <a href="{{ route('agent.profile.setting') }}" class="sidebar-menu-list__link">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                        fill="none">
                        <path d="M14 8.99988H18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M14 12.4999H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <rect x="2" y="2.99988" width="20" height="18" rx="5" stroke="currentColor"
                            stroke-width="1.5" stroke-linejoin="round" />
                        <path d="M5 15.9999C6.20831 13.4188 10.7122 13.249 12 15.9999" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M10.5 8.99988C10.5 10.1044 9.60457 10.9999 8.5 10.9999C7.39543 10.9999 6.5 10.1044 6.5 8.99988C6.5 7.89531 7.39543 6.99988 8.5 6.99988C9.60457 6.99988 10.5 7.89531 10.5 8.99988Z"
                            stroke="currentColor" stroke-width="1.5" />
                    </svg>

                </span>
                <span class="text">@lang('My Profile')</span>
            </a>
        </li>


        <li class="sidebar-menu-list__item {{ menuActive('agent.change.password') }}">
            <a href="{{ route('agent.change.password') }}" class="sidebar-menu-list__link">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                        fill="none">
                        <path
                            d="M4.26781 18.8447C4.49269 20.515 5.87613 21.8235 7.55966 21.9009C8.97627 21.966 10.4153 22 12 22C13.5847 22 15.0237 21.966 16.4403 21.9009C18.1239 21.8235 19.5073 20.515 19.7322 18.8447C19.879 17.7547 20 16.6376 20 15.5C20 14.3624 19.879 13.2453 19.7322 12.1553C19.5073 10.485 18.1239 9.17649 16.4403 9.09909C15.0237 9.03397 13.5847 9 12 9C10.4153 9 8.97627 9.03397 7.55966 9.09909C5.87613 9.17649 4.49269 10.485 4.26781 12.1553C4.12105 13.2453 4 14.3624 4 15.5C4 16.6376 4.12105 17.7547 4.26781 18.8447Z"
                            stroke="currentColor" stroke-width="1.5" />
                        <path d="M7.5 9V6.5C7.5 4.01472 9.51472 2 12 2C14.4853 2 16.5 4.01472 16.5 6.5V9"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M16 15.49V15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 15.49V15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M8 15.49V15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>

                </span>
                <span class="text">@lang('Change PIN')</span>
            </a>
        </li>


        <li class="sidebar-menu-list__item {{ menuActive('agent.twofactor') }}">
            <a href="{{ route('agent.twofactor') }}" class="sidebar-menu-list__link">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                        fill="none">
                        <path
                            d="M10 10.5V9C10 7.89543 10.8954 7 12 7C13.1046 7 14 7.89543 14 9V10.5M11.25 16H12.75C13.9228 16 14.5092 16 14.9131 15.69C15.0171 15.6102 15.1102 15.5171 15.19 15.4131C15.5 15.0092 15.5 14.4228 15.5 13.25C15.5 12.0772 15.5 11.4908 15.19 11.0869C15.1102 10.9829 15.0171 10.8898 14.9131 10.81C14.5092 10.5 13.9228 10.5 12.75 10.5H11.25C10.0772 10.5 9.49082 10.5 9.08686 10.81C8.98286 10.8898 8.88977 10.9829 8.80997 11.0869C8.5 11.4908 8.5 12.0772 8.5 13.25C8.5 14.4228 8.5 15.0092 8.80997 15.4131C8.88977 15.5171 8.98286 15.6102 9.08686 15.69C9.49082 16 10.0772 16 11.25 16Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                        <path
                            d="M21 11.1833V8.28029C21 6.64029 21 5.82028 20.5959 5.28529C20.1918 4.75029 19.2781 4.49056 17.4507 3.9711C16.2022 3.6162 15.1016 3.18863 14.2223 2.79829C13.0234 2.2661 12.424 2 12 2C11.576 2 10.9766 2.2661 9.77771 2.79829C8.89839 3.18863 7.79784 3.61619 6.54933 3.9711C4.72193 4.49056 3.80822 4.75029 3.40411 5.28529C3 5.82028 3 6.64029 3 8.28029V11.1833C3 16.8085 8.06277 20.1835 10.594 21.5194C11.2011 21.8398 11.5046 22 12 22C12.4954 22 12.7989 21.8398 13.406 21.5194C15.9372 20.1835 21 16.8085 21 11.1833Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>

                </span>
                <span class="text">@lang('2FA Security')</span>
            </a>
        </li>


        <li class="sidebar-menu-list__item {{ menuActive('ticket.*') }}">
            <a href="{{ route('ticket.index') }}" class="sidebar-menu-list__link">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                        fill="none">
                        <path d="M5.5 19V8.5C5.5 4.91015 8.41015 2 12 2C15.5899 2 18.5 4.91015 18.5 8.5V19"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path
                            d="M18.8688 22H5.13104C4.51972 22 4.21406 22 4.06951 21.7924C3.92497 21.5848 4.02157 21.2845 4.21477 20.684C4.561 19.6077 4.9089 19 6.14897 19H17.8508C19.0907 19 19.4386 19.6077 19.785 20.6838C19.9784 21.2844 20.0751 21.5847 19.9305 21.7924C19.786 22 19.4803 22 18.8688 22Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M10.5 11H13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M4.5 8H19.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="text">@lang('Support Ticket')</span>
            </a>
        </li>


        <li class="sidebar-menu-list__item {{ menuActive('agent.notification.setting') }}">
            <a href="{{ route('agent.notification.setting') }}" class="sidebar-menu-list__link">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                        fill="none">
                        <path
                            d="M2.52992 14.394C2.31727 15.7471 3.268 16.6862 4.43205 17.1542C8.89481 18.9486 15.1052 18.9486 19.5679 17.1542C20.732 16.6862 21.6827 15.7471 21.4701 14.394C21.3394 13.5625 20.6932 12.8701 20.2144 12.194C19.5873 11.2975 19.525 10.3197 19.5249 9.27941C19.5249 5.2591 16.1559 2 12 2C7.84413 2 4.47513 5.2591 4.47513 9.27941C4.47503 10.3197 4.41272 11.2975 3.78561 12.194C3.30684 12.8701 2.66061 13.5625 2.52992 14.394Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M9 21C9.79613 21.6219 10.8475 22 12 22C13.1525 22 14.2039 21.6219 15 21"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="text">@lang('Notification Setting')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">
            <a href="{{ route('agent.logout') }}" class="sidebar-menu-list__link log-out mt-2">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                        fill="none">
                        <path
                            d="M14 3.09502C13.543 3.03241 13.0755 3 12.6 3C7.29807 3 3 7.02944 3 12C3 16.9706 7.29807 21 12.6 21C13.0755 21 13.543 20.9676 14 20.905"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path
                            d="M13.5 14.5C12.9943 14.0085 11 12.7002 11 12M13.5 9.5C12.9943 9.99153 11 11.2998 11 12M11 12L21 12"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="text">@lang('Logout')</span>
            </a>
        </li>
    </ul>
</div>