@props(['widget'])
<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.one :url="route('admin.merchants.all')" variant="primary" title="Total Merchant" :value="$widget['total_agent']"
            icon="las la-agent" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.one :url="route('admin.merchants.active')" variant="success" title="Active Merchant" :value="$widget['active_agent']"
            icon="las la-user-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.one :url="route('admin.merchants.email.unverified')" variant="warning" title="Email Unverified Merchant" :value="$widget['email_unverified_agent']"
            icon="lar la-envelope" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.one :url="route('admin.merchants.mobile.unverified')" variant="danger" title="Mobile Unverified Merchant" :value="$widget['mobile_unverified_agent']"
            icon="las la-comment-slash" />
    </div>
</div>