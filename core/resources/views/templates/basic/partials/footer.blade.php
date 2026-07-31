@php
    $footerContent = @getContent('footer.content', true)->data_values;
    $policyPages = getContent('policy_pages.element', false, orderById: true);
    $socialIcons = getContent('social_icon.element', false, orderById: true);
    $selectedLang = $languages->where('code', config('app.locale') ?? 'en')->first();
@endphp


<footer class="footer-area bg-img"
    data-background-image="{{ frontendImage('footer', @$footerContent->background_image) }}">
    <div class="footer-top">
        <div class="container">
            <div class="newsletter">
                <div class="row gy-3 align-items-center justify-content-between">
                    <div class="col-lg-6">
                        <h2 class="newsletter-title">
                            {{ __(@$footerContent->heading) }}
                        </h2>
                    </div>
                    <div class="col-lg-6 col-xl-5">
                        <form class="newsletter-form no-submit-loader">
                            <div class="newsletter-form-wrapper">
                                <input type="email" class="form--control md-style" placeholder="@lang('Enter your email')"
                                    required name="email">
                                <button class="btn btn--md" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                        viewBox="0 0 32 32" fill="none">
                                        <path
                                            d="M28.7971 3.39001C28.1739 2.71883 27.1967 2.46743 26.2631 2.37744C25.2821 2.28289 24.1073 2.34577 22.8424 2.51168C20.3057 2.84441 17.2409 3.61461 14.2969 4.56227C11.3511 5.51052 8.47951 6.65145 6.329 7.74455C5.25872 8.28856 4.33044 8.83901 3.6556 9.36773C3.31958 9.631 3.01295 9.91459 2.7823 10.2164C2.5623 10.5043 2.33084 10.9139 2.33402 11.4077C2.34238 12.7038 3.22504 13.6198 4.16502 14.2346C5.12444 14.8622 6.37396 15.3351 7.61614 15.7055C8.87164 16.0799 10.1919 16.3701 11.327 16.6053C11.4014 16.6206 11.55 16.6514 11.7322 16.689C12.4183 16.8307 12.7613 16.9017 13.0838 16.8051C13.4063 16.7086 13.6543 16.4607 14.15 15.9649L19.0579 11.0571C19.5785 10.5364 20.4228 10.5364 20.9435 11.0571C21.4641 11.5778 21.4641 12.422 20.9435 12.9427L16.3667 17.5195C15.8612 18.025 15.6085 18.2777 15.5129 18.6057C15.4172 18.9337 15.4944 19.2818 15.6487 19.9782C16.2465 22.6778 16.7657 24.9086 17.2837 26.4094C17.5863 27.2859 17.9267 28.0447 18.3577 28.6043C18.8076 29.1883 19.4196 29.6295 20.2251 29.6651C20.7265 29.6873 21.1435 29.4583 21.4293 29.2458C21.7307 29.0221 22.014 28.7218 22.2768 28.3937C22.8048 27.7339 23.3596 26.8211 23.9116 25.7662C25.0209 23.6462 26.1956 20.8037 27.1896 17.8794C28.1831 14.9563 29.0109 11.9074 29.4097 9.37339C29.6087 8.10965 29.7071 6.93601 29.6524 5.95295C29.6005 5.02067 29.4019 4.04133 28.7971 3.39001Z"
                                            fill="currentColor" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="footer-top-wrapper">
                <div class="footer-top-left">
                    <div class="row gy-4 justify-content-between">
                        <div class="col-xsm-6 col-sm-4 col-lg-3">
                            <div class="footer-item">
                                <h4 class="footer-item__title">@lang('Quick Link')</h4>
                                <ul class="footer-menu">
                                    <li class="footer-menu__item">
                                        <a href="{{ route('home') }}" class="footer-menu__link">@lang('Home')</a>
                                    </li>
                                    <li class="footer-menu__item">
                                        <a href="" class="footer-menu__link">@lang('Service')</a>
                                    </li>
                                    <li class="footer-menu__item">
                                        <a href="{{ route('blogs') }}" class="footer-menu__link">@lang('Blog')</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xsm-6 col-sm-4">
                            <div class="footer-item">
                                <h4 class="footer-item__title">@lang('Legal & Policies')</h4>
                                <ul class="footer-menu">
                                    @foreach ($policyPages as $policyPage)
                                        <li class="footer-menu__item">
                                            <a href="{{ route('policy.pages', $policyPage->slug) }}"
                                                class="footer-menu__link">
                                                {{ __($policyPage->data_values->title) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="footer-item">
                                <h4 class="footer-item__title">@lang('Join to') {{ __(gs('site_name')) }}</h4>
                                <ul class="footer-menu">
                                    @if (gs('registration'))
                                        <li class="footer-menu__item">
                                            <a href="{{ route('user.register') }}"
                                                class="footer-menu__link">@lang('Join as User')
                                            </a>
                                        </li>
                                    @endif
                                    @if (gs('merchant_registration'))
                                        <li class="footer-menu__item">
                                            <a href="{{ route('merchant.register') }}"
                                                class="footer-menu__link">@lang('Join as Merchant')
                                            </a>
                                        </li>
                                    @endif

                                    @if (gs('agent_registration'))
                                        <li class="footer-menu__item">
                                            <a href="{{ route('agent.register') }}"
                                                class="footer-menu__link">@lang('Join as Agent')
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-top-right">
                    <ul class="social-list">
                        @foreach ($socialIcons as $socialIcon)
                            <li class="social-list__item">
                                <a href="{{ @$socialIcon->data_values->url }}" target="_blank"
                                    class="social-list__link">
                                    @php echo @$socialIcon->data_values->social_icon @endphp
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="footer-lang">
                        @if (gs('multi_language'))
                            <div class="dropdown lang-box style-top">
                                <button class="lang-box-btn" data-bs-toggle="dropdown">
                                    <span class="thumb">
                                        <img class="fit-image" src="{{ @$selectedLang->image_src }}" alt="usa">
                                    </span>
                                    <span class="text">{{ @$selectedLang->name }}</span>
                                    <span class="icon">
                                        <i class="fas fa-angle-down"></i>
                                    </span>
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach ($languages as $language)
                                        <li class="lang-box-item" data-code="en">
                                            <a href="{{ route('lang', $language->code) }}" class="lang-box-link">
                                                <div class="thumb">
                                                    <img src="{{ $language->image_src }}" alt="usa">
                                                </div>
                                                <span class="text">{{ $language->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bottom-footer py-3">
        <div class="container text-center">
            <div class="bottom-footer-text">
                ©{{ date('Y') }} <a href="{{ route('home') }}">{{ gs('site_name') }}</a>. @lang('All rights reserved.')
            </div>
        </div>
    </div>
</footer>

@push('script')
    <script>
        "use strict";
        (function($) {
            $(".newsletter-form").on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData($(this)[0]);
                formData.append("_token", "{{ csrf_token() }}")
                $.ajax({
                    url: `{{ route('subscribe') }}`,
                    method: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $(".newsletter-form")
                            .find(`button[type="submit"]`)
                            .attr('disabled', true)
                            .addClass('disabled');
                    },
                    complete: function() {
                        $(".newsletter-form")
                            .find(`button[type="submit"]`)
                            .attr('disabled', false)
                            .removeClass('disabled');
                    },
                    success: function(resp) {

                        if (resp.status == "success") {
                            $(".newsletter-form").trigger('reset');
                            notify('success', resp.message);
                        } else {
                            notify('error', resp.message);
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
