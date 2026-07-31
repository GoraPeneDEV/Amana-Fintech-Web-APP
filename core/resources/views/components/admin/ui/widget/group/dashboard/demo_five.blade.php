@props(['widget'])
<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url=# variant="info" title=" Today" :value="$widget['today']" icon="las la-calendar-day" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url=# variant="primary" title="This Month" :value="$widget['this_month']" icon="las la-calendar-alt" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url=# variant="success" title="This Week" :value="$widget['this_week']"
            icon="las la-calendar-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url=# variant="danger" title="This Year" :value="$widget['this_year']"
            icon="las la-calendar-check" />
    </div>
</div>

<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url=# variant="danger" title=" Yesterday" :value="$widget['yesterday']"
            icon="las la-calendar-day" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url=# variant="warning" title="Previous Month" :value="$widget['previous_month']"
            icon="las la-calendar-alt" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url=# variant="secondary" title="Previous Week" :value="$widget['previous_week']"
            icon="las la-infinity" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url=# variant="primary" title="All Time" :value="$widget['all']" icon="las la-infinity" />
    </div>
</div>
