@php
    $today = now()->format('Y-m-d');
@endphp

<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.donation.all') }}?date={{ $today }}" variant="info"
            title="Today" :value="$widget['today']" icon="las la-calendar-day" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.donation.all') }}?date={{ now()->subDay()->format('Y-m-d') }}"
            variant="primary" title="Yesterday" :value="$widget['yesterday']" icon="las la-calendar-alt" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four
            url="{{ route('admin.donation.all') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
            variant="success" title="This Month" :value="$widget['this_month']" icon="las la-calendar-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.donation.all') }}" variant="danger" title="Total Donation"
            :value="$widget['all']" icon="las la-calendar-check" />
    </div>
</div>
