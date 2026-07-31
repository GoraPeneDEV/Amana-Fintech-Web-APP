@php
    $user = auth()->user();
@endphp
<div class="sidebar-menu">
    <span class="sidebar-menu__close d-lg-none d-block"><i class="fas fa-times"></i></span>
    <div class="sidebar-logo">
        <a href="{{ route('user.home') }}" class="sidebar-logo__link">
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
                        <span class="user-info-content">
                            <span class="name">{{ __($user->fullname) }}</span>
                            <span class="phone">{{ $user->mobile }}</span>
                        </span>
                        <span class="user-info-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="none">
                                <g clip-path="url(#clip0_2366_4233)">
                                    <path
                                        d="M7.52853 2.19519C7.65355 2.07021 7.82309 2 7.99986 2C8.17664 2 8.34618 2.07021 8.4712 2.19519L12.4712 6.19519C12.4885 6.21246 12.5047 6.23071 12.5199 6.24986L12.5239 6.25519L12.5345 6.26986L12.5625 6.30919L12.5685 6.31919L12.5732 6.32586L12.5825 6.34386L12.5985 6.37319L12.6032 6.38452L12.6099 6.39786L12.6179 6.41919L12.6279 6.44186L12.6325 6.45852L12.6379 6.47186L12.6412 6.48919L12.6492 6.51386L12.6519 6.53252L12.6559 6.54919L12.6579 6.56652L12.6619 6.58852L12.6632 6.60852L12.6652 6.62719V6.64452L12.6665 6.66652L12.6652 6.68852V6.70586L12.6632 6.72319L12.6619 6.74452L12.6585 6.76386L12.6559 6.78386L12.6519 6.79986L12.6492 6.81986L12.6412 6.84319L12.6379 6.86119L12.6325 6.87386L12.6279 6.89119L12.6179 6.91319L12.6099 6.93586L12.6032 6.94786L12.5985 6.95986L12.5825 6.98852L12.5732 7.00719L12.5685 7.01319L12.5625 7.02386L12.5345 7.06252L12.5265 7.07519L12.5239 7.07719L12.5199 7.08386C12.4892 7.12173 12.4546 7.15616 12.4165 7.18652L12.4105 7.19052L12.3959 7.20119L12.3572 7.22919L12.3465 7.23519L12.3405 7.23986L12.3219 7.24919L12.2932 7.26519L12.2812 7.26986L12.2692 7.27652L12.2465 7.28452L12.2245 7.29452L12.2085 7.29852L12.1945 7.30452L12.1765 7.30786L12.1525 7.31586L12.1332 7.31852L12.1172 7.32252L12.0985 7.32452L12.0779 7.32852L12.0565 7.32986L12.0392 7.33186H12.0219L11.9999 7.33319H3.99986C3.40653 7.33319 3.10853 6.61519 3.52853 6.19519L7.52853 2.19519Z"
                                        fill="currentColor" />
                                    <path
                                        d="M12 8.66699L12.022 8.66833H12.0393L12.0567 8.67033L12.078 8.67166L12.0987 8.67566L12.1173 8.67766L12.1333 8.68166L12.1533 8.68433L12.1767 8.69233L12.1947 8.69566L12.2073 8.70099L12.2247 8.70566L12.2467 8.71566L12.2693 8.72366L12.2813 8.73033L12.2933 8.73499L12.322 8.75099L12.3407 8.76033L12.3467 8.76499L12.3573 8.77099L12.3913 8.79566L12.4087 8.80699L12.4107 8.80966L12.4173 8.81366C12.4553 8.84418 12.4897 8.87884 12.52 8.91699L12.524 8.92299L12.534 8.93633L12.5627 8.97633L12.5687 8.98699L12.5733 8.99299L12.5827 9.01166L12.5987 9.04033L12.602 9.04899L12.61 9.06433L12.618 9.08699L12.628 9.10899L12.6327 9.12633L12.638 9.13966L12.6413 9.15699L12.6493 9.18099L12.652 9.20033L12.656 9.21633L12.658 9.23499L12.662 9.25566L12.6633 9.27699L12.6653 9.29433V9.31166L12.6667 9.33366L12.6653 9.35566V9.37299L12.6633 9.39033L12.662 9.41166L12.658 9.43233L12.656 9.45099L12.652 9.46699L12.6493 9.48699L12.6413 9.51033L12.638 9.52833L12.6327 9.54099L12.628 9.55833L12.618 9.58033L12.61 9.60299L12.6033 9.61499L12.5987 9.62699L12.5827 9.65566L12.5733 9.67433L12.5687 9.68033L12.5627 9.69099L12.5347 9.72966L12.5267 9.74233L12.524 9.74433L12.52 9.75099L12.4713 9.80499L8.47134 13.805C8.34632 13.93 8.17678 14.0002 8 14.0002C7.82323 14.0002 7.65369 13.93 7.52867 13.805L3.52867 9.80499C3.10867 9.38499 3.406 8.66699 4 8.66699H12Z"
                                        fill="currentColor" />
                                </g>
                                <defs>
                                    <clipPath>
                                        <rect width="16" height="16" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                    </span>
                </span>
            </button>
            <ul class="dropdown-menu">
                <li class="user-dropdown-item">
                    <a href="{{ route('user.profile.setting') }}" class="user-dropdown-link">
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
                        <span class="text">@lang('Profile Setting')</span>
                    </a>
                </li>
                <li class="user-dropdown-item">
                    <a href="{{ route('user.change.password') }}" class="user-dropdown-link">
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
                    <a href="{{ route('user.twofactor') }}" class="user-dropdown-link">
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
                    <a href="{{ route('user.notification.setting') }}" class="user-dropdown-link">
                        <span class="icon">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                    height="20" fill="none">
                                    <path
                                        d="M2.52992 14.394C2.31727 15.7471 3.268 16.6862 4.43205 17.1542C8.89481 18.9486 15.1052 18.9486 19.5679 17.1542C20.732 16.6862 21.6827 15.7471 21.4701 14.394C21.3394 13.5625 20.6932 12.8701 20.2144 12.194C19.5873 11.2975 19.525 10.3197 19.5249 9.27941C19.5249 5.2591 16.1559 2 12 2C7.84413 2 4.47513 5.2591 4.47513 9.27941C4.47503 10.3197 4.41272 11.2975 3.78561 12.194C3.30684 12.8701 2.66061 13.5625 2.52992 14.394Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M9 21C9.79613 21.6219 10.8475 22 12 22C13.1525 22 14.2039 21.6219 15 21"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </span>
                        </span>
                        <span class="text">@lang('Notification Setting')</span>
                    </a>
                </li>
                <li class="devide"></li>
                <li class="user-dropdown-item">
                    <a href="{{ route('user.transactions') }}" class="user-dropdown-link">
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
                    <a href="{{ route('user.logout') }}" class="user-dropdown-link">
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
        <li class="sidebar-menu-list__item {{ menuActive('user.home') }}">
            <a href="{{ route('user.home') }}" class="sidebar-menu-list__link">
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
                <span class="text">@lang('Dashboard')</span>
            </a>
        </li>

        @if (moduleIsEnable('add_money', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.deposit.*') }}">
                <a href="{{ route('user.deposit.index') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <g clip-path="url(#clip0_6728_954)">
                                <path
                                    d="M9.16602 7.91699H10.416C11.1063 7.91699 11.666 8.47666 11.666 9.16699M9.16602 7.91699H7.91602C7.22566 7.91699 6.66602 8.47666 6.66602 9.16699V9.58366C6.66602 10.274 7.22566 10.8337 7.91602 10.8337H10.416C11.1063 10.8337 11.666 11.3933 11.666 12.0837V12.5003C11.666 13.1907 11.1063 13.7503 10.416 13.7503H9.16602M9.16602 7.91699V6.66699M9.16602 13.7503H7.91602C7.22566 13.7503 6.66602 13.1907 6.66602 12.5003M9.16602 13.7503V15.0003"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M9.99935 3.37878C 9.72577 3.34853 9.44768 3.33301 9.16602 3.33301C5.02388 3.33301 1.66602 6.69087 1.66602 10.833C1.66602 14.9751 5.02388 18.333 9.16602 18.333C13.3081 18.333 16.666 14.9751 16.666 10.833C16.666 10.5513 16.6505 10.2733 16.6203 9.99967"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M15.4167 1.66699V7.50033M18.3333 4.58366H12.5" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </g>
                            <defs>
                                <clipPath>
                                    <rect width="20" height="20" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="text">@lang('Add Money')</span>
                </a>
            </li>
        @endif

        @if (moduleIsEnable('send_money', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.send.money.*') }}">
                <a href="{{ route('user.send.money.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path
                                d="M9.935 3.9917C13.9136 2.63538 15.9028 1.95722 16.9728 3.02718C18.0427 4.09714 17.3646 6.08643 16.0083 10.065L15.0847 12.7743C14.0431 15.8298 13.5222 17.3576 12.6637 17.484C12.4329 17.518 12.194 17.4976 11.9656 17.4243C11.1162 17.1516 10.6672 15.5408 9.76925 12.3192C9.57008 11.6046 9.4705 11.2473 9.24366 10.9743C9.17783 10.8952 9.10483 10.8222 9.02566 10.7563C8.75275 10.5295 8.39541 10.4299 7.68087 10.2308C4.45926 9.33275 2.84845 8.88375 2.57575 8.03439C2.50243 7.80603 2.48197 7.56711 2.51596 7.33629C2.6424 6.47777 4.17016 5.95695 7.22566 4.91532L9.935 3.9917Z"
                                stroke="currentColor" stroke-width="1.5" />
                        </svg>
                    </span>
                    <span class="text">@lang('Send Money')</span>
                </a>
            </li>
        @endif

        @if (moduleIsEnable('cash_out', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.cash.out.*') }}">
                <a href="{{ route('user.cash.out.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path
                                d="M16.4551 10.8337C17.1142 9.88882 17.5007 8.73966 17.5007 7.50032C17.5007 4.27867 14.889 1.66699 11.6673 1.66699C8.44565 1.66699 5.83398 4.27866 5.83398 7.50032C5.83398 8.39499 6.03539 9.24257 6.39534 10.0003"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M11.6667 5.00033C10.7462 5.00033 10 5.55997 10 6.25033C10 6.94068 10.7462 7.50033 11.6667 7.50033C12.5872 7.50033 13.3333 8.05997 13.3333 8.75033C13.3333 9.44066 12.5872 10.0003 11.6667 10.0003M11.6667 5.00033C12.3923 5.00033 13.0097 5.34816 13.2385 5.83366M11.6667 5.00033V4.16699M11.6667 10.0003C10.941 10.0003 10.3237 9.65249 10.0948 9.16699M11.6667 10.0003V10.8337"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path
                                d="M2.5 11.667H4.49568C4.74081 11.667 4.98257 11.7222 5.20181 11.8283L6.90346 12.6517C7.1227 12.7577 7.36446 12.8129 7.60958 12.8129H8.47842C9.31875 12.8129 10 13.4722 10 14.2853C10 14.3182 9.9775 14.3471 9.94483 14.3561L7.82739 14.9416C7.44756 15.0466 7.04083 15.01 6.6875 14.839L4.86843 13.9589M10 13.7503L13.8273 12.5744C14.5058 12.363 15.2392 12.6137 15.6642 13.2022C15.9716 13.6277 15.8464 14.2372 15.3987 14.4955L9.13575 18.1091C8.73742 18.3389 8.26745 18.395 7.8293 18.265L2.5 16.6836"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="text">@lang('Cash Out')</span>
                </a>
            </li>
        @endif

        @if (moduleIsEnable('mobile_recharge', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.airtime.*') }}">
                <a href="{{ route('user.airtime.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                            fill="none">
                            <path
                                d="M17.002 6C16.9152 4.58055 16.6769 3.67665 16.023 3.02513C14.9943 2 13.3385 2 10.0269 2C6.71528 2 5.05949 2 4.03072 3.02513C3.00195 4.05025 3.00195 5.70017 3.00195 9V15C3.00195 18.2998 3.00195 19.9497 4.03072 20.9749C5.05949 22 6.71528 22 10.0269 22C13.3385 22 14.9943 22 16.023 20.9749C16.6769 20.3233 16.9152 19.4194 17.002 18"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M10.002 19H10.011" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M18.4724 8.98633L20.7231 11.1928C21.0208 11.5112 21.1112 12.3519 20.8208 12.6418L18.4724 14.9863M10.998 12.0428H20.341"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="text">@lang('Mobile Recharge')</span>
                </a>
            </li>
        @endif


        @if (moduleIsEnable('utility_bill', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.utility.bill.*') }}">
                <a href="{{ route('user.utility.bill.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path
                                d="M16.6673 7.1543V11.2505C16.6673 14.3932 16.6673 15.9646 15.691 16.9408C15.0176 17.6143 14.0609 17.8233 12.5007 17.888M3.33398 7.1543V11.2505C3.33398 14.3932 3.33398 15.9646 4.31029 16.9408C5.19619 17.8268 6.57201 17.9088 9.16715 17.9164C9.6274 17.9178 10.0007 17.5441 10.0007 17.0838V14.5838"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M18.3327 8.75062L14.7134 5.28022C12.4912 3.14939 11.3801 2.08398 9.99935 2.08398C8.6186 2.08398 7.50752 3.14939 5.28531 5.28022L1.66602 8.75062"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path
                                d="M11.6677 7.5V9.5833M8.33433 9.5833V7.5M7.08811 10.3166C7.05541 9.92088 7.39685 9.5833 7.82976 9.5833H12.1755C12.6084 9.5833 12.9498 9.92088 12.9172 10.3166L12.8277 11.3978C12.7635 12.175 12.4823 12.9241 12.0112 13.573L11.7192 13.9752C11.4434 14.355 10.9786 14.5833 10.4808 14.5833H9.52441C9.02666 14.5833 8.56191 14.355 8.28606 13.9752L7.99401 13.573C7.52288 12.9241 7.2417 12.175 7.17746 11.3978L7.08811 10.3166Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>

                    </span>
                    <span class="text">@lang('Utility Bill')</span>
                </a>
            </li>
        @endif

        @if (moduleIsEnable('gift_card', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.gift.card.*') }}">
                <a href="{{ route('user.gift.card.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none">
                            <rect x="2" y="5" width="20" height="14" rx="2" ry="2"
                                stroke="currentColor" stroke-width="1.5" />
                            <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor"
                                stroke-width="1.5" />
                            <path d="M12 5 C10 3, 7 3, 7 6 C7 8, 10 9, 12 5" stroke="currentColor" stroke-width="1.5"
                                fill="none" />
                            <path d="M12 5 C14 3, 17 3, 17 6 C17 8, 14 9, 12 5" stroke="currentColor"
                                stroke-width="1.5" fill="none" />
                            <text x="6" y="15" font-size="5" fill="currentColor" font-family="Arial"
                                font-weight="bold">$</text>
                        </svg>
                    </span>
                    <span class="text">@lang('Gift Card')</span>
                </a>
            </li>
        @endif



        @if (moduleIsEnable('bank_transfer', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.bank.transfer.*') }}">
                <a href="{{ route('user.bank.transfer.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                            fill="none">
                            <path
                                d="M2 6C2 4.59987 2 3.8998 2.27248 3.36502C2.51217 2.89462 2.89462 2.51217 3.36502 2.27248C3.8998 2 4.59987 2 6 2C7.40013 2 8.1002 2 8.63498 2.27248C9.10538 2.51217 9.48783 2.89462 9.72752 3.36502C10 3.8998 10 4.59987 10 6V18C10 19.4001 10 20.1002 9.72752 20.635C9.48783 21.1054 9.10538 21.4878 8.63498 21.7275C8.1002 22 7.40013 22 6 22C4.59987 22 3.8998 22 3.36502 21.7275C2.89462 21.4878 2.51217 21.1054 2.27248 20.635C2 20.1002 2 19.4001 2 18V6Z"
                                stroke="currentColor" stroke-width="1.5" />
                            <path
                                d="M16 22C18.3389 22 19.5083 22 20.3621 21.4635C20.8073 21.1838 21.1838 20.8073 21.4635 20.3621C22 19.5083 22 18.3389 22 16V8C22 5.66111 22 4.49167 21.4635 3.63789C21.1838 3.19267 20.8073 2.81621 20.3621 2.53647C19.5083 2 18.3389 2 16 2"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path
                                d="M18 12H10M18 12C18 11.2998 16.0057 9.99153 15.5 9.5M18 12C18 12.7002 16.0057 14.0085 15.5 14.5"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="text">@lang('Bank Transfer')</span>
                </a>
            </li>
        @endif

        @if (moduleIsEnable('investment', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.investment.*') }}">
                <a href="{{ route('user.investment.all') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <i class="las la-file-invoice-dollar"></i>
                    </span>
                    <span class="text">@lang('Investment')</span>
                </a>
            </li>
        @endif

        @if (moduleIsEnable('virtual_card', $enableModules))
            <li class="menu-title">@lang('Virtual Card')</li>
            <li class="sidebar-menu-list__item {{ menuActive('user.virtual.card.new') }}">
                <a href="{{ route('user.virtual.card.new') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                            fill="none">
                            <path
                                d="M12.5 20H10.5C6.74142 20 4.86213 20 3.60746 19.0091C3.40678 18.8506 3.22119 18.676 3.0528 18.4871C2 17.3062 2 15.5375 2 12C2 8.46252 2 6.69377 3.0528 5.5129C3.22119 5.32403 3.40678 5.14935 3.60746 4.99087C4.86213 4 6.74142 4 10.5 4H13.5C17.2586 4 19.1379 4 20.3925 4.99087C20.5932 5.14935 20.7788 5.32403 20.9472 5.5129C21.8394 6.51358 21.9755 7.93642 21.9963 10.5"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M18.5 20L18.5 13M15 16.5H22" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" />
                            <path d="M2 9H22" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="text">@lang('New Card')</span>
                </a>
            </li>
            <li class="sidebar-menu-list__item {{ menuActive('user.virtual.card.list') }}">
                <a href="{{ route('user.virtual.card.list') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path
                                d="M1.66602 9.99967C1.66602 7.05177 1.66602 5.57782 2.54335 4.59376C2.68367 4.43637 2.83833 4.2908 3.00557 4.15873C4.05112 3.33301 5.6172 3.33301 8.74935 3.33301H11.2493C14.3815 3.33301 15.9476 3.33301 16.9931 4.15873C17.1603 4.2908 17.315 4.43637 17.4553 4.59376C18.3327 5.57782 18.3327 7.05177 18.3327 9.99967C18.3327 12.9476 18.3327 14.4215 17.4553 15.4056C17.315 15.563 17.1603 15.7085 16.9931 15.8406C15.9476 16.6663 14.3815 16.6663 11.2493 16.6663H8.74935C5.6172 16.6663 4.05112 16.6663 3.00557 15.8406C2.83833 15.7085 2.68367 15.563 2.54335 15.4056C1.66602 14.4215 1.66602 12.9476 1.66602 9.99967Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M8.33398 13.333H9.58398" stroke="currentColor" stroke-width="1.5"
                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12.084 13.333H15.0007" stroke="currentColor" stroke-width="1.5"
                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M1.66602 7.5H18.3327" stroke="currentColor" stroke-width="1.5"
                                stroke-linejoin="round" />
                        </svg>

                    </span>
                    <span class="text">@lang('My Virtual Cards')</span>
                </a>
            </li>
        @endif

        <li class="menu-title">@lang('More Services')</li>

        @if (moduleIsEnable('make_payment', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.make.payment.*') }}">
                <a href="{{ route('user.make.payment.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path
                                d="M3.33398 15.5385V6.71221C3.33398 4.33387 3.33398 3.1447 4.06622 2.40585C4.79845 1.66699 5.97696 1.66699 8.33398 1.66699H11.6673C14.0243 1.66699 15.2028 1.66699 15.9351 2.40585C16.6673 3.1447 16.6673 4.33387 16.6673 6.71221V15.5385C16.6673 16.7982 16.6673 17.4281 16.2823 17.676C15.6532 18.0812 14.6807 17.2315 14.1916 16.9231C13.7874 16.6682 13.5854 16.5407 13.3611 16.5334C13.1187 16.5254 12.9131 16.6477 12.4764 16.9231L10.884 17.9273C10.4544 18.1982 10.2397 18.3337 10.0007 18.3337C9.76165 18.3337 9.5469 18.1982 9.11732 17.9273L7.52493 16.9231C7.12078 16.6682 6.9187 16.5407 6.69443 16.5334C6.45208 16.5254 6.24643 16.6477 5.80971 16.9231C5.32061 17.2315 4.34804 18.0812 3.71894 17.676C3.33398 17.4281 3.33398 16.7982 3.33398 15.5385Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M13.3327 5H6.66602" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8.33268 8.33301H6.66602" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M12.084 8.22917C11.3937 8.22917 10.834 8.71883 10.834 9.32292C10.834 9.927 11.3937 10.4167 12.084 10.4167C12.7743 10.4167 13.334 10.9063 13.334 11.5104C13.334 12.1145 12.7743 12.6042 12.084 12.6042M12.084 8.22917C12.6282 8.22917 13.0912 8.5335 13.2628 8.95833M12.084 8.22917V7.5M12.084 12.6042C11.5397 12.6042 11.0767 12.2998 10.9052 11.875M12.084 12.6042V13.3333"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </span>
                    <span class="text">@lang('Make Payment')</span>
                </a>
            </li>
        @endif
        @if (moduleIsEnable('request_money', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.request.money.*') }}">
                <a href="{{ route('user.request.money.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <g clip-path="url(#clip0_6728_984)">
                                <path
                                    d="M10.4337 2.5H9.59868C9.1296 2.50697 8.66252 2.52569 8.20367 2.55614C4.7108 2.7879 1.92855 5.60449 1.69962 9.1405C1.65482 9.83242 1.65482 10.549 1.69962 11.241C1.783 12.5288 2.35359 13.7212 3.02534 14.7282C3.41537 15.433 3.15797 16.3128 2.75171 17.0812C2.45878 17.6353 2.31232 17.9124 2.42992 18.1125C2.54752 18.3127 2.8102 18.3191 3.33557 18.3318C4.37452 18.3571 5.07511 18.0631 5.63122 17.6537C5.94663 17.4216 6.10433 17.3055 6.21302 17.2922C6.32172 17.2788 6.53562 17.3667 6.96335 17.5426C7.34778 17.7007 7.79415 17.7982 8.20367 17.8253C9.39285 17.9042 10.637 17.9044 11.8286 17.8253C15.1802 17.603 17.9176 15.0007 18.3327 11.6667"
                                    stroke="currentColor " stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M12.991 7.20715C12.467 7.51428 11.0932 8.14141 11.93 8.92616C12.3387 9.30949 12.794 9.58366 13.3664 9.58366H16.6324C17.2047 9.58366 17.66 9.30949 18.0688 8.92616C18.9055 8.14141 17.5317 7.51428 17.0077 7.20715C15.779 6.48694 14.2198 6.48694 12.991 7.20715Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path
                                    d="M16.6673 3.33366C16.6673 4.25413 15.9212 5.00033 15.0007 5.00033C14.0802 5.00033 13.334 4.25413 13.334 3.33366C13.334 2.41318 14.0802 1.66699 15.0007 1.66699C15.9212 1.66699 16.6673 2.41318 16.6673 3.33366Z"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path d="M7.08398 12.4997H12.9173M7.08398 8.33301H8.75065" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </g>
                            <defs>
                                <clipPath>
                                    <rect width="20" height="20" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>

                    </span>
                    <span class="text">@lang('Request Money')</span>
                </a>
            </li>
        @endif



        @if (moduleIsEnable('donation', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.donation.*') }}">
                <a href="{{ route('user.donation.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                            fill="none">
                            <path
                                d="M19.7453 13C20.5362 11.8662 21 10.4872 21 9C21 5.13401 17.866 2 14 2C10.134 2 7 5.134 7 9C7 10.0736 7.24169 11.0907 7.67363 12"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M14 6C12.8954 6 12 6.67157 12 7.5C12 8.32843 12.8954 9 14 9C15.1046 9 16 9.67157 16 10.5C16 11.3284 15.1046 12 14 12M14 6C14.8708 6 15.6116 6.4174 15.8862 7M14 6V5M14 12C13.1292 12 12.3884 11.5826 12.1138 11M14 12V13"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path
                                d="M3 14H5.39482C5.68897 14 5.97908 14.0663 6.24217 14.1936L8.28415 15.1816C8.54724 15.3089 8.83735 15.3751 9.1315 15.3751H10.1741C11.1825 15.3751 12 16.1662 12 17.142C12 17.1814 11.973 17.2161 11.9338 17.2269L9.39287 17.9295C8.93707 18.0555 8.449 18.0116 8.025 17.8064L5.84211 16.7503M12 16.5L16.5928 15.0889C17.407 14.8352 18.2871 15.136 18.7971 15.8423C19.1659 16.3529 19.0157 17.0842 18.4785 17.3942L10.9629 21.7305C10.4849 22.0063 9.92094 22.0736 9.39516 21.9176L3 20.0199"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>

                    </span>
                    <span class="text">@lang('Donation')</span>
                </a>
            </li>
        @endif

        @if (moduleIsEnable('education_fee', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.education.fee.*') }}">
                <a href="{{ route('user.education.fee.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <g clip-path="url(#clip0_6728_1010)">
                                <path
                                    d="M4.16663 5.83301L3.15096 6.51012C2.42287 6.99552 2.05882 7.23822 1.86153 7.60835C1.66423 7.97848 1.6657 8.41351 1.66862 9.28351C1.67214 10.3309 1.68188 11.3982 1.70883 12.4781C1.77278 15.0403 1.80477 16.3213 2.74677 17.2633C3.68877 18.2054 4.98715 18.2378 7.58391 18.3028C9.19943 18.3431 10.8006 18.3431 12.416 18.3028C15.0128 18.2378 16.3112 18.2054 17.2532 17.2633C18.1952 16.3213 18.2272 15.0403 18.2911 12.4781C18.3181 11.3982 18.3278 10.3309 18.3313 9.28351C18.3343 8.41351 18.3357 7.97847 18.1384 7.60835C17.9411 7.23822 17.5771 6.99552 16.8489 6.51012L15.8333 5.83301"
                                    stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                <path
                                    d="M1.66602 8.33301L7.42687 11.7895C8.68018 12.5415 9.30685 12.9175 9.99935 12.9175C10.6918 12.9175 11.3185 12.5415 12.5718 11.7895L18.3327 8.33301"
                                    stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                <path
                                    d="M4.16602 10.0003V5.00033C4.16602 3.42898 4.16602 2.6433 4.65417 2.15515C5.14233 1.66699 5.92801 1.66699 7.49935 1.66699H12.4994C14.0707 1.66699 14.8564 1.66699 15.3445 2.15515C15.8327 2.6433 15.8327 3.42898 15.8327 5.00033V10.0003"
                                    stroke="currentColor" stroke-width="1.5" />
                                <path
                                    d="M8.13436 5.23013C8.80476 4.84936 9.38993 5.0028 9.74143 5.24723C9.88551 5.34744 9.9576 5.39755 10 5.39755C10.0424 5.39755 10.1144 5.34744 10.2586 5.24723C10.6101 5.0028 11.1953 4.84936 11.8657 5.23013C12.7455 5.72985 12.9446 7.37844 10.9151 8.76934C10.5286 9.03425 10.3353 9.16667 10 9.16667C9.66468 9.16667 9.47143 9.03425 9.08485 8.76934C7.05543 7.37844 7.25452 5.72985 8.13436 5.23013Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </g>
                            <defs>
                                <clipPath id="clip0_6728_1010">
                                    <rect width="20" height="20" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="text">@lang('Education Fee')</span>
                </a>
            </li>
        @endif

        @if (moduleIsEnable('microfinance', $enableModules))
            <li class="sidebar-menu-list__item {{ menuActive('user.microfinance.*') }}">
                <a href="{{ route('user.microfinance.create') }}" class="sidebar-menu-list__link">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                            fill="none">
                            <path
                                d="M2 8.56907C2 7.37289 2.48238 6.63982 3.48063 6.08428L7.58987 3.79744C9.7431 2.59915 10.8197 2 12 2C13.1803 2 14.2569 2.59915 16.4101 3.79744L20.5194 6.08428C21.5176 6.63982 22 7.3729 22 8.56907C22 8.89343 22 9.05561 21.9646 9.18894C21.7785 9.88945 21.1437 10 20.5307 10H3.46928C2.85627 10 2.22152 9.88944 2.03542 9.18894C2 9.05561 2 8.89343 2 8.56907Z"
                                stroke="currentColor" stroke-width="1.5" />
                            <path d="M4 10V18.5M8 10V18.5" stroke="currentColor" stroke-width="1.5" />
                            <path d="M11 18.5H5C3.34315 18.5 2 19.8431 2 21.5C2 21.7761 2.22386 22 2.5 22H11"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M21.5 14.5L14.5 21.5" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="15.25" cy="15.25" r="0.75" stroke="currentColor"
                                stroke-width="1.5" />
                            <circle cx="20.75" cy="20.75" r="0.75" stroke="currentColor"
                                stroke-width="1.5" />
                        </svg>
                    </span>
                    <span class="text">@lang('Microfinance')</span>
                </a>
            </li>
        @endif

        <li class="sidebar-menu-list__item">
            <a href="{{ route('user.logout') }}" class="sidebar-menu-list__link log-out mt-2">
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                        fill="none">
                        <path
                            d="M9.16602 2.5L8.61385 2.69487C6.46491 3.45333 5.39042 3.83257 4.77822 4.69785C4.16602 5.56313 4.16602 6.70258 4.16602 8.9815V11.0185C4.16602 13.2974 4.16602 14.4368 4.77822 15.3022C5.39042 16.1674 6.46491 16.5467 8.61385 17.3052L9.16602 17.5"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path
                            d="M17.4993 10.0003H9.16602M17.4993 10.0003C17.4993 9.41683 15.8374 8.3266 15.416 7.91699M17.4993 10.0003C17.4993 10.5838 15.8374 11.6741 15.416 12.0837"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="text">@lang('Logout')</span>
            </a>
        </li>
    </ul>
</div>
