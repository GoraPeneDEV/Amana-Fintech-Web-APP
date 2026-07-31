Pusher.logToConsole = true;

const PUSHER_APP_KEY = window.atob(window.my_pusher.app_key);
const PUSHER_CLUSTER = window.atob(window.my_pusher.app_cluster);
const BASE_URL       = my_pusher.base_url;

var pusher = new Pusher(PUSHER_APP_KEY, {
    cluster: PUSHER_CLUSTER,
});

const pusherConnection = (channelName,eventName, callback) => {
    pusher.connection.bind('connected', () => {
        const SOCKET_ID = pusher.connection.socket_id;
        const CHANNEL_NAME = `private-${channelName}`;
        pusher.config.authEndpoint = `${BASE_URL}/pusher/auth/${SOCKET_ID}/${CHANNEL_NAME}`;
        let channel = pusher.subscribe(CHANNEL_NAME);
        channel.bind('pusher:subscription_succeeded', function () {
            channel.bind(eventName, function (data) {
                callback(data);
            })
        });
    });
};

