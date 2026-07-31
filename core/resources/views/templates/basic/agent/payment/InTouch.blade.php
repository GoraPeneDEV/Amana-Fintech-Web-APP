<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ gs('site_name') }}</title>
    <style>
        body { font-family: -apple-system, Arial, sans-serif; background: #F8FAFC; margin: 0; padding: 0; }
        .wrap { max-width: 420px; margin: 60px auto; padding: 32px 24px; background: #fff; border-radius: 16px; box-shadow: 0 8px 24px rgba(30,41,59,.08); text-align: center; }
        .spinner { width: 44px; height: 44px; margin: 0 auto 20px; border: 4px solid #E2E8F0; border-top-color: #2B5BEE; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        h1 { font-size: 17px; color: #1E293B; margin: 0 0 8px; }
        p { font-size: 13.5px; color: #475569; line-height: 1.5; margin: 0 0 4px; }
        .amount { font-size: 22px; font-weight: 700; color: #1E293B; margin: 14px 0; }
        .phone { font-weight: 700; color: #1E293B; }
        .state { margin-top: 18px; font-size: 12px; color: #94A3B8; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="spinner"></div>
    <h1>Confirm the payment on your phone</h1>
    <p>InTouch sent a mobile money confirmation request to <span class="phone">{{ $data->phone ?? '' }}</span>.</p>
    <div class="amount">{{ showAmount($data->amount ?? 0, currencyFormat: false) }} {{ $data->currency ?? '' }}</div>
    <p>Approve it on your phone to finish adding money to your {{ gs('site_name') }} wallet.</p>
    <div class="state" id="state">Waiting for confirmation&hellip;</div>
</div>

<script>
    (function () {
        var statusUrl = @json($data->status_url ?? '');
        var stateEl = document.getElementById('state');
        var attempts = 0;
        var maxAttempts = 60; // ~4 minutes at 4s interval

        function poll() {
            attempts++;
            fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.status === 'success') {
                        stateEl.textContent = 'Payment confirmed, redirecting...';
                        window.location.href = data.success_url;
                        return;
                    }
                    if (data.status === 'failed') {
                        stateEl.textContent = 'Payment failed or was cancelled, redirecting...';
                        window.location.href = data.failed_url;
                        return;
                    }
                    if (attempts >= maxAttempts) {
                        stateEl.textContent = 'Still waiting for your confirmation. You can close this page and check your transaction history later.';
                        return;
                    }
                    setTimeout(poll, 4000);
                })
                .catch(function () {
                    if (attempts < maxAttempts) { setTimeout(poll, 4000); }
                });
        }

        poll();
    })();
</script>
</body>
</html>
