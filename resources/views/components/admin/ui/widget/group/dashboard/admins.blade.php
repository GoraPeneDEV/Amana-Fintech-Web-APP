@props(['widget'])
<div class="row responsive-row">
    <div class="col-xxl-4 col-sm-6">
        <x-admin.ui.widget.six :url="route('admin.admins.all')" variant="primary" title="Total Admins" :value="$widget['total_admins']"
            icon="las la-users-cog" />
    </div>
    <div class="col-xxl-4 col-sm-6">
        <x-admin.ui.widget.six :url="route('admin.admins.active')" variant="success" title="Active Admins" :value="$widget['active_admins']"
            icon="las la-user-check" />
    </div>
    <div class="col-xxl-4 col-sm-6">
        <x-admin.ui.widget.six :url="route('admin.admins.email.unverified')" variant="warning" title="Email Unverified Admins" :value="$widget['email_unverified_admins']"
            icon="lar la-envelope" />
    </div>
</div>
