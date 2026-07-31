<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="javascript:void(0)" variant="info" title="Current Balance" :value="$widget['current_balance']" icon="las la-money-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="javascript:void(0)" variant="info" title="Total Deposit" :value="$widget['total_deposit']" icon="las la-money-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="javascript:void(0)" variant="info" title="Total Payment" :value="$widget['total_payment']" icon="las la-money-check" />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four url="javascript:void(0)" variant="info" title="Transaction Count" :value="$widget['trx_count']" :currency="false" icon="las la-money-check" />
    </div>
</div>
