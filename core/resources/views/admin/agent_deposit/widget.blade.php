<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ url()->route('admin.agent.deposit.successful', request()->all()) }}" variant="success"
            title="Successful Add Money" :value="$widget['successful']" icon="las la-check-circle" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ url()->route('admin.agent.deposit.pending', request()->all()) }}" variant="warning"
            title="Pending Add Money" :value="$widget['pending']" icon="las la-spinner" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ url()->route('admin.agent.deposit.rejected', request()->all()) }}" variant="danger"
            title="Rejected Add Money" :value="$widget['rejected']" icon="las la-ban" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="{{ url()->route('admin.agent.deposit.initiated', request()->all()) }}" variant="primary"
            title="Initiated Add Money" :value="$widget['initiated']" icon="las la-money-check-alt" />
    </div>
</div>
