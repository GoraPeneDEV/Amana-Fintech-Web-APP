@php
    $today = now()->format('Y-m-d');
@endphp

<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.investment.history') }}" variant="info" title="Total Investment Count"
            :value="$widget['total_invest_count']" icon="las la-calendar-day" :currency=false />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.investment.history') }}" variant="primary"
            title="Total Investment Amount" :value="$widget['total_invest_amount']" icon="las la-calendar-alt" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.investment.interest.history') }}" variant="success"
            title="Total Interest Paid" :value="$widget['total_paid']" icon="las la-calendar-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.investment.history') }}?date={{ $today }}" variant="success"
            title="Today Investment Amount" :value="$widget['today_invest_amount']" icon="las la-calendar-check" />
    </div>
</div>
