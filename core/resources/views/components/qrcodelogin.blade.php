@props(['qrCode', 'guard'])
<div class="qr-code-thumb position-relative">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="50" height="50" color="#6b7280" fill="none">
        <path
            d="M3 6C3 4.58579 3 3.87868 3.43934 3.43934C3.87868 3 4.58579 3 6 3C7.41421 3 8.12132 3 8.56066 3.43934C9 3.87868 9 4.58579 9 6C9 7.41421 9 8.12132 8.56066 8.56066C8.12132 9 7.41421 9 6 9C4.58579 9 3.87868 9 3.43934 8.56066C3 8.12132 3 7.41421 3 6Z"
            stroke="#6b7280" stroke-width="1.5"></path>
        <path
            d="M3 18C3 16.5858 3 15.8787 3.43934 15.4393C3.87868 15 4.58579 15 6 15C7.41421 15 8.12132 15 8.56066 15.4393C9 15.8787 9 16.5858 9 18C9 19.4142 9 20.1213 8.56066 20.5607C8.12132 21 7.41421 21 6 21C4.58579 21 3.87868 21 3.43934 20.5607C3 20.1213 3 19.4142 3 18Z"
            stroke="#6b7280" stroke-width="1.5"></path>
        <path d="M3 12L9 12" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M12 3V8" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path
            d="M15 6C15 4.58579 15 3.87868 15.4393 3.43934C15.8787 3 16.5858 3 18 3C19.4142 3 20.1213 3 20.5607 3.43934C21 3.87868 21 4.58579 21 6C21 7.41421 21 8.12132 20.5607 8.56066C20.1213 9 19.4142 9 18 9C16.5858 9 15.8787 9 15.4393 8.56066C15 8.12132 15 7.41421 15 6Z"
            stroke="#6b7280" stroke-width="1.5"></path>
        <path
            d="M21 12H15C13.5858 12 12.8787 12 12.4393 12.4393C12 12.8787 12 13.5858 12 15M12 17.7692V20.5385M15 15V16.5C15 17.9464 15.7837 18 17 18C17.5523 18 18 18.4477 18 19M16 21H15M18 15C19.4142 15 20.1213 15 20.5607 15.44C21 15.8799 21 16.5881 21 18.0043C21 19.4206 21 20.1287 20.5607 20.5687C20.24 20.8898 19.7767 20.9766 19 21"
            stroke="#6b7280" stroke-width="1.5" stroke-linecap="round"></path>
    </svg>
    <div class="qr-code-loading d-none">
        <div class="spinner-border text-primary" role="status">
        </div>
    </div>
</div>

@push('end-content')
    <div class="modal custom--modal fade qr-modal" id="qr-modal">
        <div class="modal-dialog modal-dialog-centered modal-xl max-w-reset">
            <div class="modal-content">
                <div class="modal-header bg-img"
                    data-background-image="{{ asset($activeTemplateTrue . 'images/modal_shape.png') }}">
                    <h4 class="modal-title">@lang('LOGIN WITH QRCODE')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body p-2 p-md-5">
                    <div class="row justify-content-between align-items-center flex-row-reverse gy-3">
                        <div class="col-lg-5">
                            <div class="py-5">
                                <div class="position-relative text-center ">
                                    <div class="qrcode-canvas-style" id="qrcode-canvas"></div>
                                    <div class="qr-code-loading d-none">
                                        <div class="spinner-border text-primary" role="status">
                                        </div>
                                    </div>
                                </div>
                                <p class="qr-modal-note fs-12 mt-3">
                                    <i>
                                        @lang('Scan this QR code using your app for a smooth and secure login experience.')
                                    </i>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="qr-modal__content ps-2 ps-md-4">
                                <h2 class ="qr-modal__content-title">@lang('Login into') {{ __(gs('site_name')) }}</h2>
                                <p class="qr-modal__content-desc">
                                    @lang('Securely log in to your account by scanning the QR code using the our app on your mobile device.')
                                </p>
                                <ul class="qr-modal__content-list">
                                    <li class="qr-modal__content-item">
                                        <span class="count">1.</span> @lang('Open the ' . gs('site_name') . ' app on your mobile device.')
                                    </li>
                                    <li class="qr-modal__content-item d-flex align-items-center gap-2 flex-wrap">
                                        <span class="count">2.</span> @lang('Tap the') <i class="la la-user"></i>
                                        <strong>@lang('Profile')</strong> @lang('icon.')
                                    </li>
                                    <li class="qr-modal__content-item d-flex align-items-center gap-2 flex-wrap">
                                        <span class="count">3.</span> @lang('Navigate to the') <i class="la la-shield-alt"></i>
                                        <strong>@lang('Security')</strong> @lang('section.')
                                    </li>
                                    <li class="qr-modal__content-item d-flex align-items-center gap-2 flex-wrap">
                                        <span class="count">4.</span> @lang('Select the') <i class="la la-qrcode"></i>
                                        <strong>@lang('QR Code Login')</strong> @lang('option.')
                                    </li>
                                    <li class="qr-modal__content-item">
                                        <span class="count">5.</span> <span>@lang('Use your phone to scan the QR code displayed on this screen.')</span>
                                    </li>
                                    <li class="qr-modal__content-item">
                                        <span class="count">6.</span> <span>@lang('Confirm the login request on your device to complete the process.')</span>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-img justify-content-center"
                    data-background-image="{{ asset($activeTemplateTrue . 'images/modal_shape.png') }}">
                    <h4>{{ __(gs('site_name')) }}</h4>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . '/js/pusher.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . '/js/broadcasting.js') }}?v=1.02"></script>
    <script src="{{ asset($activeTemplateTrue . '/js/qr-code-styling.js') }}"></script>
@endpush


@push('script')
    <script type="text/javascript"></script>
    <script>
        "use strict";
        (function($) {

            var qrcode = "{{ $qrCode }}";
            const guard = "{{ $guard }}";

            $('.qr-code-thumb').on('click', function() {
                $("#qr-modal").modal('show');
            });

            pusherConnection(`${guard}-qr-code-login`, "qr-code-login", function(data) {
                if (data.data.qr_code != qrcode) return false;

                $(".qr-code-loading").removeClass('d-none');
                $(".qr-code-thumb").addClass('has-loading');

                const user = data.data.user;
                const action = '{{ route($guard . '.qrcode.login', ':user') }}';
                $.ajax({
                    type: "POST",
                    url: action.replace(":user", user),
                    data: {
                        _token: "{{ csrf_token() }}",
                        s_token: data.data.s_token,
                        qrcode
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            setTimeout(() => {
                                window.location = '{{ route($guard . '.home') }}';
                            }, 2000);
                        } else {
                            $(".qr-code-loading").addClass('d-none');
                            $(".qr-code-thumb").removeClass('has-loading');
                            $(".qr-code-loading").addClass('d-none');
                            notify('error', response.message);
                        }

                    }
                });
            });

            pusherConnection(`${guard}-qr_code_reset`, "qr_code_reset", function(data) {
                $(".qr-code-loading").removeClass('d-none');
                $(".qr-code-thumb").addClass('has-loading');
                $(".qr-code-thumb").find('img').attr('src', data.data.qr_image);

                qrcode = data.data.qr_code;

                qrCodeInit();
                setTimeout(() => {
                    $(".qr-code-loading").addClass('d-none');
                    $(".qr-code-thumb").removeClass('has-loading');
                }, 2000);

            });

            //qr code init
            function qrCodeInit() {

                const canvasElement = document.getElementById("qrcode-canvas");
                canvasElement.innerHTML = "";

                const qrCodeObject = new QRCodeStyling({
                    width: 300,
                    height: 300,
                    type: "svg",
                    data: qrcode,
                    image: "{{ siteFavicon() }}",
                    dotsOptions: {
                        color: "#{{ gs('base_color') }}",
                        type: "rounded"
                    },
                    backgroundOptions: {
                        color: "transparent",
                    },
                    imageOptions: {
                        crossOrigin: "anonymous",
                        margin: 20
                    }
                });

                qrCodeObject.append(canvasElement);;
            }

            qrCodeInit();

        })(jQuery);
    </script>
@endpush
