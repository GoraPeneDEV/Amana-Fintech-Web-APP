<script src="{{ asset('assets/global/js/firebase/firebase-8.3.2.js') }}"></script>

<script>
    "use strict";

    var permission    = null;
    var authRoute     = null;
    var authenticated = false;

    @auth
        authRoute = "{{ route('user.add.device.token') }}";
        authenticated = true;
    @endauth

    @auth('agent')
        authRoute = "{{ route('agent.add.device.token') }}";
        authenticated = true;
    @endauth

    @auth('merchant')
        authRoute = "{{ route('merchant.add.device.token') }}";
        authenticated = true;
    @endauth

    var pushNotify = @json(gs('pn'));
    var firebaseConfig = @json(gs('firebase_config'));

    function pushNotifyAction() {
        permission = Notification.permission;

        if (!('Notification' in window)) {
            notify('info', 'Push notifications not available in your browser. Try Chromium.')
        } else if (permission === 'denied' || permission == 'default') { //Notice for users dashboard
            $('.notice').append(`
            <div class="alert alert--base mb-4">
                <div class="alert__icon">
                    <i class="fa fa-info"></i>
                </div>
                <div class="alert__content">
                    <h6 class="alert__title">@lang('Please Allow / Reset Browser Notification')</h6>
                    <p class="alert__desc">
                        @lang('If you want to get push notification then you have to allow notification from your browser')
                    </p>
                </div>
            </div>
            `);
        }
    }

    //If enable push notification from admin panel
    if (pushNotify == 1) {
        pushNotifyAction();
    }

    //When users allow browser notification
    if (permission != 'denied' && firebaseConfig) {

        //Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        navigator.serviceWorker.register("{{ asset('assets/global/js/firebase/firebase-messaging-sw.js') }}")

            .then((registration) => {
                messaging.useServiceWorker(registration);

                function initFirebaseMessagingRegistration() {
                    messaging
                        .requestPermission()
                        .then(function() {
                            return messaging.getToken()
                        })
                        .then(function(token) {
                            $.ajax({
                                url: authRoute,
                                type: 'POST',
                                data: {
                                    token: token,
                                    '_token': "{{ csrf_token() }}"
                                },
                                success: function(response) {},
                                error: function(err) {},
                            });
                        }).catch(function(error) {});
                }

                messaging.onMessage(function(payload) {
                    const title = payload.notification.title;
                    const options = {
                        body: payload.notification.body,
                        icon: payload.data.icon,
                        image: payload.notification.image,
                        click_action: payload.data.click_action,
                        vibrate: [200, 100, 200]
                    };
                    new Notification(title, options);
                });

                //For authenticated users
                if (authenticated) {
                    initFirebaseMessagingRegistration();
                }

            });

    }
</script>
