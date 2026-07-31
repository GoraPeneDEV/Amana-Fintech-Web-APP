@php
    $today = now()->format('Y-m-d');
@endphp

<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.cashin.history') }}?date={{ $today }}" variant="info"
            title="Today" :value="$widget['today']" icon="las la-calendar-day" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.cashin.history') }}?date={{ now()->subDay()->format('Y-m-d') }}"
            variant="primary" title="Yesterday" :value="$widget['yesterday']" icon="las la-calendar-alt" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four
            url="{{ route('admin.cashin.history') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
            variant="success" title="This Week" :value="$widget['this_month']" icon="las la-calendar-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ route('admin.cashin.history') }}" variant="danger" title="Total  Cashout"
            :value="$widget['all']" icon="las la-calendar-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.seven url="{{ route('admin.cashin.history') }}?date={{ $today }}" variant="success"
            title="Today Agent Commission" :value="$widget['today_charge']" icon="las la-calendar-day" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.seven url="{{ route('admin.cashin.history') }}?date={{ now()->subDay()->format('Y-m-d') }}"
            variant="secondary" title="Yesterday Agent Commission" :value="$widget['yesterday_charge']" icon="las la-calendar-alt" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.seven
            url="{{ route('admin.cashin.history') }}?date={{ now()->startOfMonth()->format('Y-m-d') }}to{{ $today }}"
            variant="warning" title="This Month Agent Commission" :value="$widget['this_month_charge']" icon="las la-calendar-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.seven url="{{ route('admin.cashin.history') }}" variant="primary" title="Total Agent Commission"
            :value="$widget['all_charge']" icon="las la-calendar-check" />
    </div>
</div>
