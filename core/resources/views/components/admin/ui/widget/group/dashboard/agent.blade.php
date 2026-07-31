@props(['widget'])
<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.one :url="route('admin.agents.all')" variant="primary" title="Total Agent" :value="$widget['total_agent']"
            icon="las la-agent" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.one :url="route('admin.agents.active')" variant="success" title="Active Agent" :value="$widget['active_agent']"
            icon="las la-user-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.one :url="route('admin.agents.email.unverified')" variant="warning" title="Email Unverified Agent" :value="$widget['email_unverified_agent']"
            icon="lar la-envelope" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.one :url="route('admin.agents.mobile.unverified')" variant="danger" title="Mobile Unverified Agent" :value="$widget['mobile_unverified_agent']"
            icon="las la-comment-slash" />
    </div>
</div>