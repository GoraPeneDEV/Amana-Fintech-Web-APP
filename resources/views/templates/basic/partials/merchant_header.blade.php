@php
    $user = auth('merchant')->user();
    $selectedLang = $languages->where('code', config('app.locale') ?? 'en')->first();

@endphp
<div class="dashboard-header">
    <div class="container-fluid">
        <div class="dashboard-header-wrapper">
            <div class="dashboard-header-left">
                <div class="d-xl-none">
                    <button class="navigation-bar">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="d-none d-xl-block">
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
                                <a href="{{ route('merchant.profile.setting') }}" class="user-dropdown-link">
                                    <span class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                            height="20" fill="none">
                                            <path d="M14 8.99988H18" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                            <path d="M14 12.4999H17" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                            <rect x="2" y="2.99988" width="20" height="18" rx="5"
                                                stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                            <path d="M5 15.9999C6.20831 13.4188 10.7122 13.249 12 15.9999"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M10.5 8.99988C10.5 10.1044 9.60457 10.9999 8.5 10.9999C7.39543 10.9999 6.5 10.1044 6.5 8.99988C6.5 7.89531 7.39543 6.99988 8.5 6.99988C9.60457 6.99988 10.5 7.89531 10.5 8.99988Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </span>
                                    <span class="text">@lang('Profile Setting')</span>
                                </a>
                            </li>
                            <li class="user-dropdown-item">
                                <a href="{{ route('merchant.change.password') }}" class="user-dropdown-link">
                                    <span class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                            height="20" fill="none">
                                            <path
                                                d="M4.26781 18.8447C4.49269 20.515 5.87613 21.8235 7.55966 21.9009C8.97627 21.966 10.4153 22 12 22C13.5847 22 15.0237 21.966 16.4403 21.9009C18.1239 21.8235 19.5073 20.515 19.7322 18.8447C19.879 17.7547 20 16.6376 20 15.5C20 14.3624 19.879 13.2453 19.7322 12.1553C19.5073 10.485 18.1239 9.17649 16.4403 9.09909C15.0237 9.03397 13.5847 9 12 9C10.4153 9 8.97627 9.03397 7.55966 9.09909C5.87613 9.17649 4.49269 10.485 4.26781 12.1553C4.12105 13.2453 4 14.3624 4 15.5C4 16.6376 4.12105 17.7547 4.26781 18.8447Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                            <path
                                                d="M7.5 9V6.5C7.5 4.01472 9.51472 2 12 2C14.4853 2 16.5 4.01472 16.5 6.5V9"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M16 15.49V15.5" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12 15.49V15.5" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M8 15.49V15.5" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span class="text">@lang('Change PIN')</span>
                                </a>
                            </li>
                            <li class="user-dropdown-item">
                                <a href="{{ route('merchant.twofactor') }}" class="user-dropdown-link">
                                    <span class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                            height="20" fill="none">
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
                                <a href="{{ route('merchant.notification.setting') }}" class="user-dropdown-link">
                                    <span class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                            height="20" fill="none">
                                            <path
                                                d="M2.52992 14.394C2.31727 15.7471 3.268 16.6862 4.43205 17.1542C8.89481 18.9486 15.1052 18.9486 19.5679 17.1542C20.732 16.6862 21.6827 15.7471 21.4701 14.394C21.3394 13.5625 20.6932 12.8701 20.2144 12.194C19.5873 11.2975 19.525 10.3197 19.5249 9.27941C19.5249 5.2591 16.1559 2 12 2C7.84413 2 4.47513 5.2591 4.47513 9.27941C4.47503 10.3197 4.41272 11.2975 3.78561 12.194C3.30684 12.8701 2.66061 13.5625 2.52992 14.394Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M9 21C9.79613 21.6219 10.8475 22 12 22C13.1525 22 14.2039 21.6219 15 21"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span class="text">@lang('Notification Setting')</span>
                                </a>
                            </li>
                            <li class="devide"></li>
                            <li class="user-dropdown-item">
                                <a href="{{ route('merchant.transactions') }}" class="user-dropdown-link">
                                    <span class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                            height="20" fill="none">
                                            <path
                                                d="M4.5795 8.60699L2 8.45417C3.849 3.70488 9.15764 0.999849 14.3334 2.34477C19.8461 3.77722 23.1205 9.26153 21.647 14.5943C20.4283 19.0051 16.3433 21.9307 11.8479 22"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M12 22C6.5 22 2 17 2 11" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                stroke-dasharray="0.5 3" />
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
                                            <path
                                                d="M5.5 19V8.5C5.5 4.91015 8.41015 2 12 2C15.5899 2 18.5 4.91015 18.5 8.5V19"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M18.8688 22H5.13104C4.51972 22 4.21406 22 4.06951 21.7924C3.92497 21.5848 4.02157 21.2845 4.21477 20.684C4.561 19.6077 4.9089 19 6.14897 19H17.8508C19.0907 19 19.4386 19.6077 19.785 20.6838C19.9784 21.2844 20.0751 21.5847 19.9305 21.7924C19.786 22 19.4803 22 18.8688 22Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M10.5 11H13.5" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M4.5 8H19.5" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span class="text">@lang('Support Ticket')</span>
                                </a>
                            </li>
                            <li class="user-dropdown-item">
                                <a href="{{ route('merchant.withdraw.account.setting') }}" class="user-dropdown-link">
                                    <span class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20"
                                            height="20" fill="none">
                                            <path
                                                d="M18.9349 13.9453L18.2646 10.2968C17.9751 8.72096 17.8303 7.93303 17.257 7.46651C16.6837 7 15.8602 7 14.2132 7H9.78685C8.1398 7 7.31628 7 6.74298 7.46651C6.16968 7.93303 6.02492 8.72096 5.73538 10.2968L5.06506 13.9453C4.46408 17.2162 4.16359 18.8517 5.08889 19.9259C6.01419 21 7.72355 21 11.1423 21H12.8577C16.2765 21 17.9858 21 18.9111 19.9259C19.8364 18.8517 19.5359 17.2162 18.9349 13.9453Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M12 10.5V17M9.5 15L12 17.5L14.5 15" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M21 11C21.1568 10.9209 21.2931 10.8212 21.4142 10.6955C22 10.0875 22 9.10893 22 7.15176C22 5.1946 22 4.21602 21.4142 3.60801C20.8284 3 19.8856 3 18 3L6 3C4.11438 3 3.17157 3 2.58579 3.60801C2 4.21602 2 5.1946 2 7.15176C2 9.10893 2 10.0875 2.58579 10.6955C2.70688 10.8212 2.84322 10.9209 3 11"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                        </svg>
                                    </span>
                                    <span class="text">@lang('Withdraw Setting')</span>
                                </a>
                            </li>
                            <li class="user-dropdown-item">
                                <a href="{{ route('merchant.logout') }}" class="user-dropdown-link">
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
            </div>

            @if (gs('multi_language'))
                <div class="dashboard-header-right">
                    <div class="dropdown lang-box">
                        <button class="lang-box-btn" data-bs-toggle="dropdown">
                            <span class="thumb">
                                <img class="fit-image" src="{{ @$selectedLang->image_src }}" alt="usa">
                            </span>
                            <span class="text">{{ __(@$selectedLang->name) }}</span>
                            <span class="icon">
                                <i class="fas fa-angle-down"></i>
                            </span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach ($languages as $language)
                                <li class="lang-box-item" data-code="en">
                                    <a href="{{ route('lang', $language->code) }}" class="lang-box-link">
                                        <div class="thumb">
                                            <img src="{{ __(@$language->image_src) }}" alt="usa">
                                        </div>
                                        <span class="text">{{ __(@$language->name) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
