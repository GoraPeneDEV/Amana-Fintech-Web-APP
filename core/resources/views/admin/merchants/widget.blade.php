<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four :url="route('admin.merchants.all')" variant="primary" title="Total Merchants" :value="$widget['all']"
            icon="las la-user-secret" :currency=false />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.merchants.all') }}?date={{ now()->toDateString() }}" variant="danger"
            title="Merchants Joined Today" :value="$widget['today']" icon="las la-clock" :currency=false />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four
            url="{{ route('admin.merchants.all') }}?date={{ now()->subDays(7)->toDateString() }}to{{ now()->toDateString() }}"
            variant="success" title="Merchants Joined Last Week" :value="$widget['week']" icon="las la-calendar" :currency=false />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four
            url="{{ route('admin.merchants.all') }}?date={{ now()->subDays(30)->toDateString() }}to{{ now()->toDateString() }}"
            variant="primary" title="Merchants Joined Last Month" :value="$widget['month']" icon="las la-calendar-plus"
            :currency=false />
    </div>
</div>
