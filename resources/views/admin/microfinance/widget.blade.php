@php
    $today = now()->format('Y-m-d');
@endphp
<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.microfinance.pending') }}" variant="warning" title="Pending"
            :value="$widget['pending']" icon="las la-calendar-day" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.microfinance.approved') }}" variant="success" title="Approved"
            :value="$widget['approved']" icon="las la-calendar-alt" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.microfinance.rejected') }}" variant="danger" title="Rejected"
            :value="$widget['rejected']" icon="las la-calendar-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.microfinance.all') }}" variant="primary" title="Total"
            :value="$widget['all']" icon="las la-calendar-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.seven url="{{ route('admin.microfinance.approved') }}?date={{ $today }}" variant="success"
            title=" Today Charge" :value="$widget['today_charge']" icon="las la-calendar-day" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.seven
            url="{{ route('admin.microfinance.approved') }}?date={{ now()->subDay()->format('Y-m-d') }}" variant="secondary"
            title="Yesterday Charge" :value="$widget['yesterday_charge']" icon="las la-calendar-alt" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.seven
            url="{{ route('admin.microfinance.approved') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
            variant="warning" title="This Month Charge" :value="$widget['this_month_charge']" icon="las la-calendar-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.seven url="{{ route('admin.microfinance.approved') }}" variant="primary" title="Total Charge"
            :value="$widget['all_charge']" icon="las la-calendar-check" />
    </div>
</div>
