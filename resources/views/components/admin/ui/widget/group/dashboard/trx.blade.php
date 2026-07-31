@props(['widget'])
<div class="row responsive-row">
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four :url="route('admin.report.transaction')" variant="primary" title="Total Transaction" :value="$widget['total_trx']"
            icon="la la-exchange-alt" :currency=false />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four :url="route('admin.report.user.transaction')" variant="warning" title="Total User Transaction" :value="$widget['total_trx_user']"
            icon="la la-exchange-alt" :currency=false />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four :url="route('admin.report.agent.transaction')" variant="primary" title="Total Agent Transaction" :value="$widget['total_trx_agent']"
            icon="la la-exchange-alt" :currency=false />
    </div>
    <div class="col-xxl-3 col-sm-6">
        <x-admin.ui.widget.four :url="route('admin.report.merchant.transaction')" variant="warning" title="Total Merchant Transaction" :value="$widget['total_trx_merchant']"
            icon="la la-exchange-alt" :currency=false />
    </div>
</div>
