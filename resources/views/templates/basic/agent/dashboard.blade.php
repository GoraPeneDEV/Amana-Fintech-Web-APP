@extends($activeTemplate . 'layouts.agent')
@section('content')
    @php
        $kyc = getContent('kyc.content', true);
    @endphp
    <div class="notice"></div>
    <div class="row justify-content-center gy-4">
        @if (auth('agent')->user()->kv == Status::KYC_UNVERIFIED && auth('agent')->user()->kyc_rejection_reason)
            <div class="col-12">
                <div class="alert alert--danger" role="alert">
                    <div class="alert__icon">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="alert__content">
                        <h6 class="alert__title">@lang('KYC Verification Rejected')</h6>
                        <p class="alert__desc">
                            {{ __(@$kyc->data_values->reject) }}
                            <a href="{{ route('agent.kyc.form') }}">@lang('Click Here to Re-submit Documents')</a>,
                            <button type="button" class="text--danger" href="{{ route('agent.kyc.form') }}"
                                data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Show Reject Reason')</button>.
                        </p>
                    </div>
                </div>
            </div>
        @elseif(auth('agent')->user()->kv == Status::KYC_UNVERIFIED)
            <div class="col-12">
                <div class="alert alert--info" role="alert">
                    <div class="alert__icon">
                        <i class="fa fa-info"></i>
                    </div>
                    <div class="alert__content">
                        <h6 class="alert__title">@lang('KYC Verification Required')</h6>
                        <p class="alert__desc">
                            {{ __(@$kyc->data_values->required) }} <a
                                href="{{ route('agent.kyc.form') }}">@lang('Click Here to Submit Documents')</a>
                        </p>
                    </div>
                </div>
            </div>
        @elseif(auth('agent')->user()->kv == Status::KYC_PENDING)
            <div class="col-12">
                <div class="alert alert--warning" role="alert">
                    <div class="alert__icon">
                        <i class="fa fa-info"></i>
                    </div>
                    <div class="alert__content">
                        <h6 class="alert__title">@lang('KYC Verification Pending')</h6>
                        <p class="alert__desc">
                            {{ __(@$kyc->data_values->pending) }}
                            <a href="{{ route('agent.kyc.data') }}">@lang('See KYC Data')
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-lg-12">
            <div class="card custom--card mb-4">
                <div class="card-body">
                    <div class="mywallet">
                        <div class="mywallet-left">
                            <div class="mywallet-title">
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M18.7077 5.14068C18.7931 5.4585 18.8239 5.80758 18.835 6.21961C18.981 6.2325 19.1214 6.24776 19.2563 6.26586C20.1631 6.38748 20.9639 6.65265 21.6051 7.29235C22.2463 7.93206 22.512 8.73099 22.6339 9.63562C22.7501 10.4973 22.75 11.586 22.75 12.9063V14.9938C22.75 16.3141 22.7501 17.4028 22.6339 18.2645C22.512 19.1691 22.2463 19.968 21.6051 20.6077C20.9639 21.2474 20.1631 21.5126 19.2563 21.6342C18.3927 21.7501 17.3014 21.75 15.978 21.75H9.97398C8.19196 21.75 6.75559 21.75 5.6259 21.5985C4.45303 21.4412 3.4655 21.1046 2.68119 20.3222C1.89687 19.5397 1.55952 18.5544 1.40184 17.3843C1.24995 16.2573 1.24998 14.8242 1.25 13.0464V5.17508C1.25 3.55965 2.56262 2.25009 4.18182 2.25009L14.089 2.25005C14.8663 2.24963 15.4435 2.24931 15.9436 2.38298C17.2926 2.7436 18.3462 3.79483 18.7077 5.14068ZM15.4372 4.26652C15.22 4.20845 14.9259 4.20007 13.9541 4.20007H4.18138C3.64164 4.20007 3.2041 4.63659 3.2041 5.17507C3.2041 5.71354 3.64164 6.15006 4.18138 6.15006H15.9776C16.2901 6.15005 16.5897 6.15005 16.8763 6.15156C16.8674 5.89948 16.8505 5.76142 16.8193 5.64537C16.6386 4.97244 16.1117 4.44683 15.4372 4.26652ZM17.5 12C18.6046 12 19.5 12.8954 19.5 14C19.5 15.1046 18.6046 16 17.5 16C16.3954 16 15.5 15.1046 15.5 14C15.5 12.8954 16.3954 12 17.5 12Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <h6>@lang('Available Balance')</h6>
                            </div>
                            <h2 class="mywallet-balance">{{ showAmount(auth('agent')->user()->balance) }}</h2>
                        </div>

                        <button class="mywallet-btn" data-bs-toggle="modal" data-bs-target="#qr-modal">
                            <span class="thumb">
                                <img src="{{ $qrCodeUrl }}" alt="qr_code">
                            </span>
                        </button>
                    </div>
                    <div class="row g-3">
                        @if (moduleIsEnable('add_money', $enableModules))
                            <div class="col-xsm-12 col-sm-6 col-lg-3">
                                <a href="{{ route('agent.add.money.history') }}" class="dashboard-widget">
                                    <div class="dashboard-widget__link">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                    <div class="dashboard-widget__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="40"
                                            height="40" fill="none">
                                            <path
                                                d="M22 11C22 7.46252 22 5.69377 20.9472 4.5129C20.7788 4.32403 20.5932 4.14935 20.3925 3.99087C19.1379 3 17.2586 3 13.5 3H10.5C6.74142 3 4.86213 3 3.60746 3.99087C3.40678 4.14935 3.22119 4.32403 3.0528 4.5129C2 5.69377 2 7.46252 2 11C2 14.5375 2 16.3062 3.0528 17.4871C3.22119 17.676 3.40678 17.8506 3.60746 18.0091C4.86213 19 6.74142 19 10.5 19H12"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M18.5 21L18.5 14M15 17.5H22" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" />
                                            <path d="M5.5 11H5.49102" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M14.5 11C14.5 12.3807 13.3807 13.5 12 13.5C10.6193 13.5 9.5 12.3807 9.5 11C9.5 9.61929 10.6193 8.5 12 8.5C13.3807 8.5 14.5 9.61929 14.5 11Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </div>
                                    <div class="dashboard-widget__content">
                                        <h5 class="dashboard-widget__title">@lang('Add Money')</h5>
                                        <span class="dashboard-widget__text">
                                            @lang('Add money to your account')
                                        </span>
                                    </div>
                                    <div class="dashboard-widget__shape">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                            viewBox="0 0 40 40" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M23.4033 2.10398C23.537 2.10679 23.6373 2.22673 23.617 2.35889C23.452 3.34916 23.3442 3.98246 23.2077 4.44748C23.0802 4.88221 22.9667 5.02274 22.8782 5.10124C22.7808 5.18763 22.6293 5.28171 22.1888 5.34258C21.7065 5.40924 21.0482 5.41621 19.9998 5.41621C18.9513 5.41621 18.293 5.40924 17.8107 5.34258C17.3703 5.28171 17.2187 5.18763 17.1213 5.10124C17.0328 5.02274 16.9195 4.88223 16.7918 4.44748C16.6556 3.98324 16.5479 3.35128 16.3833 2.36383C16.3637 2.22866 16.4665 2.10671 16.603 2.10384C17.5987 2.08299 18.6975 2.08299 19.9068 2.08301H20.0928C21.3048 2.08299 22.406 2.08299 23.4033 2.10398ZM13.4334 2.30809C13.6613 2.27746 13.8687 2.43881 13.9017 2.66639L13.9117 2.73589L13.9158 2.76211L13.9275 2.83236C14.0782 3.73666 14.2092 4.52328 14.3937 5.15188C14.5922 5.82824 14.8899 6.46334 15.4629 6.97169C16.0555 7.49748 16.738 7.71836 17.4692 7.81941C18.1585 7.91468 19.0092 7.91659 20.0005 7.91659C20.9918 7.91659 21.8425 7.91468 22.5318 7.81941C23.2628 7.71836 23.9455 7.49748 24.5382 6.97169C25.111 6.46334 25.4087 5.82824 25.6072 5.15188C25.7917 4.52328 25.9228 3.73666 26.0735 2.83238L26.0852 2.76213L26.1005 2.66281C26.135 2.43694 26.3415 2.27766 26.568 2.30809C28.2952 2.54031 29.7175 3.03281 30.8427 4.15804C31.9678 5.28328 32.4605 6.70563 32.6927 8.43274C32.9173 10.1046 32.9173 12.2362 32.9173 14.9071V25.093C32.9173 27.764 32.9173 29.8955 32.6927 31.5673C32.4605 33.2945 31.9678 34.7168 30.8427 35.842C29.7175 36.9673 28.2952 37.4598 26.568 37.692C24.8962 37.9168 22.7645 37.9167 20.0935 37.9167H19.9078C17.2368 37.9167 15.1052 37.9168 13.4334 37.692C11.7062 37.4598 10.2839 36.9673 9.15865 35.842C8.03342 34.7168 7.5409 33.2945 7.3087 31.5673C7.08394 29.8955 7.08395 27.764 7.08399 25.093V14.9071C7.08395 12.2362 7.08394 10.1045 7.3087 8.43274C7.5409 6.70563 8.03342 5.28328 9.15865 4.15804C10.2839 3.03281 11.7062 2.54031 13.4334 2.30809ZM17.084 33.333C17.084 32.6427 17.6437 32.083 18.334 32.083H21.6673C22.3577 32.083 22.9173 32.6427 22.9173 33.333C22.9173 34.0233 22.3577 34.583 21.6673 34.583H18.334C17.6437 34.583 17.084 34.0233 17.084 33.333Z"
                                                fill="currentColor" />
                                        </svg>
                                    </div>
                                </a>
                            </div>
                        @endif
                        <div class="col-xsm-12 col-sm-6 col-lg-3">
                            <a href="{{ route('agent.withdraw.history') }}" class="dashboard-widget">
                                <div class="dashboard-widget__link">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="dashboard-widget__icon">
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
                                </div>
                                <div class="dashboard-widget__content">
                                    <h5 class="dashboard-widget__title">@lang('Withdraw')</h5>
                                    <span class="dashboard-widget__text">
                                        @lang('Withdraw money from your account')
                                    </span>
                                </div>
                                <div class="dashboard-widget__shape">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                        viewBox="0 0 40 40" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M23.4033 2.10398C23.537 2.10679 23.6373 2.22673 23.617 2.35889C23.452 3.34916 23.3442 3.98246 23.2077 4.44748C23.0802 4.88221 22.9667 5.02274 22.8782 5.10124C22.7808 5.18763 22.6293 5.28171 22.1888 5.34258C21.7065 5.40924 21.0482 5.41621 19.9998 5.41621C18.9513 5.41621 18.293 5.40924 17.8107 5.34258C17.3703 5.28171 17.2187 5.18763 17.1213 5.10124C17.0328 5.02274 16.9195 4.88223 16.7918 4.44748C16.6556 3.98324 16.5479 3.35128 16.3833 2.36383C16.3637 2.22866 16.4665 2.10671 16.603 2.10384C17.5987 2.08299 18.6975 2.08299 19.9068 2.08301H20.0928C21.3048 2.08299 22.406 2.08299 23.4033 2.10398ZM13.4334 2.30809C13.6613 2.27746 13.8687 2.43881 13.9017 2.66639L13.9117 2.73589L13.9158 2.76211L13.9275 2.83236C14.0782 3.73666 14.2092 4.52328 14.3937 5.15188C14.5922 5.82824 14.8899 6.46334 15.4629 6.97169C16.0555 7.49748 16.738 7.71836 17.4692 7.81941C18.1585 7.91468 19.0092 7.91659 20.0005 7.91659C20.9918 7.91659 21.8425 7.91468 22.5318 7.81941C23.2628 7.71836 23.9455 7.49748 24.5382 6.97169C25.111 6.46334 25.4087 5.82824 25.6072 5.15188C25.7917 4.52328 25.9228 3.73666 26.0735 2.83238L26.0852 2.76213L26.1005 2.66281C26.135 2.43694 26.3415 2.27766 26.568 2.30809C28.2952 2.54031 29.7175 3.03281 30.8427 4.15804C31.9678 5.28328 32.4605 6.70563 32.6927 8.43274C32.9173 10.1046 32.9173 12.2362 32.9173 14.9071V25.093C32.9173 27.764 32.9173 29.8955 32.6927 31.5673C32.4605 33.2945 31.9678 34.7168 30.8427 35.842C29.7175 36.9673 28.2952 37.4598 26.568 37.692C24.8962 37.9168 22.7645 37.9167 20.0935 37.9167H19.9078C17.2368 37.9167 15.1052 37.9168 13.4334 37.692C11.7062 37.4598 10.2839 36.9673 9.15865 35.842C8.03342 34.7168 7.5409 33.2945 7.3087 31.5673C7.08394 29.8955 7.08395 27.764 7.08399 25.093V14.9071C7.08395 12.2362 7.08394 10.1045 7.3087 8.43274C7.5409 6.70563 8.03342 5.28328 9.15865 4.15804C10.2839 3.03281 11.7062 2.54031 13.4334 2.30809ZM17.084 33.333C17.084 32.6427 17.6437 32.083 18.334 32.083H21.6673C22.3577 32.083 22.9173 32.6427 22.9173 33.333C22.9173 34.0233 22.3577 34.583 21.6673 34.583H18.334C17.6437 34.583 17.084 34.0233 17.084 33.333Z"
                                            fill="currentColor" />
                                    </svg>

                                </div>
                            </a>
                        </div>
                        <div class="col-xsm-12 col-sm-6 col-lg-3">
                            <a href="{{ route('agent.statement.history') }}" class="dashboard-widget">
                                <div class="dashboard-widget__link">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="dashboard-widget__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="40"
                                        height="40" fill="none">
                                        <path
                                            d="M20.4999 10.5V10C20.4999 6.22876 20.4999 4.34315 19.3284 3.17157C18.1568 2 16.2712 2 12.4999 2H11.5C7.72883 2 5.84323 2 4.67166 3.17156C3.50009 4.34312 3.50007 6.22872 3.50004 9.99993L3.5 14.5C3.49997 17.7874 3.49996 19.4312 4.40788 20.5375C4.57412 20.7401 4.75986 20.9258 4.96242 21.0921C6.06877 22 7.71249 22 10.9999 22"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M7.5 7H16.5" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M7.5 12H13.5" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M20.5 20L20.5 17C20.5 15.5706 19.1569 14 17.5 14C15.8431 14 14.5 15.5706 14.5 17L14.5 20.5C14.5 21.3284 15.1716 22 16 22C16.8284 22 17.5 21.3284 17.5 20.5V17"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="dashboard-widget__content">
                                    <h5 class="dashboard-widget__title">@lang('Statement')</h5>
                                    <span class="dashboard-widget__text">
                                        @lang('View your statement')
                                    </span>
                                </div>
                                <div class="dashboard-widget__shape">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                        viewBox="0 0 40 40" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M23.4033 2.10398C23.537 2.10679 23.6373 2.22673 23.617 2.35889C23.452 3.34916 23.3442 3.98246 23.2077 4.44748C23.0802 4.88221 22.9667 5.02274 22.8782 5.10124C22.7808 5.18763 22.6293 5.28171 22.1888 5.34258C21.7065 5.40924 21.0482 5.41621 19.9998 5.41621C18.9513 5.41621 18.293 5.40924 17.8107 5.34258C17.3703 5.28171 17.2187 5.18763 17.1213 5.10124C17.0328 5.02274 16.9195 4.88223 16.7918 4.44748C16.6556 3.98324 16.5479 3.35128 16.3833 2.36383C16.3637 2.22866 16.4665 2.10671 16.603 2.10384C17.5987 2.08299 18.6975 2.08299 19.9068 2.08301H20.0928C21.3048 2.08299 22.406 2.08299 23.4033 2.10398ZM13.4334 2.30809C13.6613 2.27746 13.8687 2.43881 13.9017 2.66639L13.9117 2.73589L13.9158 2.76211L13.9275 2.83236C14.0782 3.73666 14.2092 4.52328 14.3937 5.15188C14.5922 5.82824 14.8899 6.46334 15.4629 6.97169C16.0555 7.49748 16.738 7.71836 17.4692 7.81941C18.1585 7.91468 19.0092 7.91659 20.0005 7.91659C20.9918 7.91659 21.8425 7.91468 22.5318 7.81941C23.2628 7.71836 23.9455 7.49748 24.5382 6.97169C25.111 6.46334 25.4087 5.82824 25.6072 5.15188C25.7917 4.52328 25.9228 3.73666 26.0735 2.83238L26.0852 2.76213L26.1005 2.66281C26.135 2.43694 26.3415 2.27766 26.568 2.30809C28.2952 2.54031 29.7175 3.03281 30.8427 4.15804C31.9678 5.28328 32.4605 6.70563 32.6927 8.43274C32.9173 10.1046 32.9173 12.2362 32.9173 14.9071V25.093C32.9173 27.764 32.9173 29.8955 32.6927 31.5673C32.4605 33.2945 31.9678 34.7168 30.8427 35.842C29.7175 36.9673 28.2952 37.4598 26.568 37.692C24.8962 37.9168 22.7645 37.9167 20.0935 37.9167H19.9078C17.2368 37.9167 15.1052 37.9168 13.4334 37.692C11.7062 37.4598 10.2839 36.9673 9.15865 35.842C8.03342 34.7168 7.5409 33.2945 7.3087 31.5673C7.08394 29.8955 7.08395 27.764 7.08399 25.093V14.9071C7.08395 12.2362 7.08394 10.1045 7.3087 8.43274C7.5409 6.70563 8.03342 5.28328 9.15865 4.15804C10.2839 3.03281 11.7062 2.54031 13.4334 2.30809ZM17.084 33.333C17.084 32.6427 17.6437 32.083 18.334 32.083H21.6673C22.3577 32.083 22.9173 32.6427 22.9173 33.333C22.9173 34.0233 22.3577 34.583 21.6673 34.583H18.334C17.6437 34.583 17.084 34.0233 17.084 33.333Z"
                                            fill="currentColor" />
                                    </svg>
                                </div>
                            </a>
                        </div>
                        <div class="col-xsm-12 col-sm-6 col-lg-3">
                            <a href="{{ route('agent.transactions') }}" class="dashboard-widget">
                                <div class="dashboard-widget__link">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="dashboard-widget__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="40"
                                        height="40" fill="none">
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
                                </div>
                                <div class="dashboard-widget__content">
                                    <h5 class="dashboard-widget__title">@lang('Transaction History')</h5>
                                    <span class="dashboard-widget__text">
                                        @lang('View your transaction history')
                                    </span>
                                </div>
                                <div class="dashboard-widget__shape">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                        viewBox="0 0 40 40" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M23.4033 2.10398C23.537 2.10679 23.6373 2.22673 23.617 2.35889C23.452 3.34916 23.3442 3.98246 23.2077 4.44748C23.0802 4.88221 22.9667 5.02274 22.8782 5.10124C22.7808 5.18763 22.6293 5.28171 22.1888 5.34258C21.7065 5.40924 21.0482 5.41621 19.9998 5.41621C18.9513 5.41621 18.293 5.40924 17.8107 5.34258C17.3703 5.28171 17.2187 5.18763 17.1213 5.10124C17.0328 5.02274 16.9195 4.88223 16.7918 4.44748C16.6556 3.98324 16.5479 3.35128 16.3833 2.36383C16.3637 2.22866 16.4665 2.10671 16.603 2.10384C17.5987 2.08299 18.6975 2.08299 19.9068 2.08301H20.0928C21.3048 2.08299 22.406 2.08299 23.4033 2.10398ZM13.4334 2.30809C13.6613 2.27746 13.8687 2.43881 13.9017 2.66639L13.9117 2.73589L13.9158 2.76211L13.9275 2.83236C14.0782 3.73666 14.2092 4.52328 14.3937 5.15188C14.5922 5.82824 14.8899 6.46334 15.4629 6.97169C16.0555 7.49748 16.738 7.71836 17.4692 7.81941C18.1585 7.91468 19.0092 7.91659 20.0005 7.91659C20.9918 7.91659 21.8425 7.91468 22.5318 7.81941C23.2628 7.71836 23.9455 7.49748 24.5382 6.97169C25.111 6.46334 25.4087 5.82824 25.6072 5.15188C25.7917 4.52328 25.9228 3.73666 26.0735 2.83238L26.0852 2.76213L26.1005 2.66281C26.135 2.43694 26.3415 2.27766 26.568 2.30809C28.2952 2.54031 29.7175 3.03281 30.8427 4.15804C31.9678 5.28328 32.4605 6.70563 32.6927 8.43274C32.9173 10.1046 32.9173 12.2362 32.9173 14.9071V25.093C32.9173 27.764 32.9173 29.8955 32.6927 31.5673C32.4605 33.2945 31.9678 34.7168 30.8427 35.842C29.7175 36.9673 28.2952 37.4598 26.568 37.692C24.8962 37.9168 22.7645 37.9167 20.0935 37.9167H19.9078C17.2368 37.9167 15.1052 37.9168 13.4334 37.692C11.7062 37.4598 10.2839 36.9673 9.15865 35.842C8.03342 34.7168 7.5409 33.2945 7.3087 31.5673C7.08394 29.8955 7.08395 27.764 7.08399 25.093V14.9071C7.08395 12.2362 7.08394 10.1045 7.3087 8.43274C7.5409 6.70563 8.03342 5.28328 9.15865 4.15804C10.2839 3.03281 11.7062 2.54031 13.4334 2.30809ZM17.084 33.333C17.084 32.6427 17.6437 32.083 18.334 32.083H21.6673C22.3577 32.083 22.9173 32.6427 22.9173 33.333C22.9173 34.0233 22.3577 34.583 21.6673 34.583H18.334C17.6437 34.583 17.084 34.0233 17.084 33.333Z"
                                            fill="currentColor" />
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-wrapper">
                <div class="table-wrapper-header">
                    <h4>@lang('Latest Transactions')</h4>
                    <div class="table-search-right">
                        <form class="table-search no-submit-loader">
                            <input type="search" name="search" class="form-control form--control"
                                value="{{ request()->search }}" placeholder="@lang('Search...')">
                            <button class="icon px-3" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="table-wrapper-body">
                    <table class="table table--responsive--xl">
                        <thead>
                            <tr>
                                <th>@lang('Trx')</th>
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Charge')</th>
                                <th>@lang('Post Balance')</th>
                                <th>@lang('Detail')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        <strong>{{ $trx->trx }}</strong>
                                    </td>
                                    <td>
                                        {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                    </td>
                                    <td>
                                        <span
                                            class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                            {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ showAmount($trx->charge) }}
                                    </td>
                                    <td>
                                        {{ showAmount($trx->post_balance) }}
                                    </td>
                                    <td>{{ __($trx->details) }}</td>
                                </tr>
                            @empty
                                @include('Template::partials.empty_message')
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-wrapper-footer d-none"></div>
            </div>
        </div>
    </div>
    @if (auth('agent')->user()->kv == Status::KYC_UNVERIFIED && auth('agent')->user()->kyc_rejection_reason)
        <div class="modal fade custom--modal" id="kycRejectionReason">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ auth('agent')->user()->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="modal custom--modal fade qr-modal" id="qr-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-img "
                    data-background-image="{{ asset($activeTemplateTrue . 'images/modal_shape.png') }}">
                    <h4 class="modal-title">@lang('My QR Code')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="qr-modal-thumb text-center">
                        <img class="mx-auto" src="{{ $qrCodeUrl }}" alt="QR">
                    </div>
                    <p class="qr-modal-note fs-14">
                        <i>
                            @lang('Scan this QR code to make your transactions quick and easy!')
                        </i>
                    </p>
                    <div class="qr-modal-form mt-4 mb-4">
                        <h6 class="title">@lang('My Number')</h6>
                        <div class="number">

                            @foreach (str_split(auth('agent')->user()->mobile) as $digit)
                                <span>{{ $digit }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-img justify-content-center"
                    data-background-image="{{ asset($activeTemplateTrue . 'images/modal_shape.png') }}">
                    <h4 class="">{{ __(gs('site_name')) }}</h4>
                </div>
            </div>
        </div>
    </div>
@endsection
