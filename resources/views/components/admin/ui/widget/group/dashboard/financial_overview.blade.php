@props(['widget'])
<div class="row responsive-row">
    <x-permission_check :permission="['view user add money', 'view merchant withdraw']">
        <div class="col-xxl-6">
            <div class="card shadow-none h-100">
                <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap border-0">
                    <h5 class="card-title">@lang(' Financial Overview')</h5>
                    <ul class="nav nav-pills payment-history" id="pills-tab" role="tablist">
                        <x-permission_check permission="view user add money">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="pill"
                                    data-bs-target="#pills-deposit-user" type="button" role="tab">
                                    @lang('User Add Money')
                                </button>
                            </li>
                        </x-permission_check>
                        <x-permission_check permission="view merchant withdraw">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-withdraw-user"
                                    type="button" role="tab" aria-controls="pills-withdraw-user"
                                    aria-selected="false">
                                    @lang('Merchant Withdraw')
                                </button>
                            </li>
                        </x-permission_check>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <x-permission_check permission="view user add money">
                            <div class="tab-pane fade show active" id="pills-deposit-user" role="tabpanel">
                                <div class="widget-card-wrapper custom-widget-wrapper">
                                    <div class="row g-0">
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--success">
                                                <a href="{{ route('admin.deposit.list') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <span class="widget-icon">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    </span>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Total Add Money')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_deposit_amount_user'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--warning">
                                                <a href="{{ route('admin.deposit.pending') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-spinner"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Pending Add Money')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_deposit_pending_user'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--danger">
                                                <a href="{{ route('admin.deposit.rejected') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-ban"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Rejected Add Money')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_deposit_rejected_user'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--primary">
                                                <a href="{{ route('admin.deposit.list') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon ">
                                                        <i class="fas fa-percentage"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Add Money Charge')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_deposit_charge_user'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--warning">
                                                <a href="{{ route('admin.deposit.pending') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-spinner"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Pending Add Money Count')</p>
                                                        <h6 class="widget-amount">
                                                            {{ $widget['total_deposit_pending_count_user'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--danger">
                                                <a href="{{ route('admin.deposit.rejected') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-ban"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Rejected Add Money Count')</p>
                                                        <h6 class="widget-amount">
                                                            {{ $widget['total_deposit_rejected_count_user'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-permission_check>
                        <x-permission_check permission="view merchant withdraw">
                            <div class="tab-pane fade" id="pills-withdraw-user" role="tabpanel">
                                <div class="widget-card-wrapper custom-widget-wrapper">
                                    <div class="row g-0">
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--success">
                                                <a href="{{ route('admin.withdraw.merchant.data.all') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Total Withdrawal')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_withdraw_amount_merchant'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--warning">
                                                <a href="{{ route('admin.withdraw.merchant.data.pending') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-spinner"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Pending Withdrawal')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_withdraw_pending_merchant'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                            <div class="widget-card widget--danger">
                                                <a href="{{ route('admin.withdraw.merchant.data.rejected') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-ban"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Rejected Withdrawal')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_withdraw_rejected_merchant'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                            <div class="widget-card widget--primary">
                                                <a href="{{ route('admin.withdraw.merchant.data.all') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-percentage"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Withdrawal Charge')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_withdraw_charge_merchant'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>'
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                            <div class="widget-card widget--warning">
                                                <a href="{{ route('admin.withdraw.merchant.data.pending') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-spinner"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Pending Withdrawal Count')</p>
                                                        <h6 class="widget-amount">
                                                            {{ $widget['total_withdraw_pending_count_merchant'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--danger">
                                                <a href="{{ route('admin.withdraw.merchant.data.rejected') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-ban"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Rejected Withdrawal Count')</p>
                                                        <h6 class="widget-amount">
                                                            {{ $widget['total_withdraw_rejected_count_merchant'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-permission_check>
                    </div>
                </div>
            </div>
        </div>
    </x-permission_check>
    <x-permission_check :permission="['view agent add money', 'view agent withdraw']">
        <div class="col-xxl-6">
            <div class="card shadow-none h-100">
                <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap border-0">
                    <h5 class="card-title">@lang('Agent Financial Overview')</h5>
                    <ul class="nav nav-pills payment-history" id="pills-tab" role="tablist">
                        <x-permission_check permission="view agent add money">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-deposit"
                                    type="button" role="tab">
                                    @lang('Add Money')
                                </button>
                            </li>
                        </x-permission_check>
                        <x-permission_check permission="view agent withdraw">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-withdraw"
                                    type="button" role="tab" aria-controls="pills-withdraw"
                                    aria-selected="false">
                                    @lang('Withdrawals')
                                </button>
                            </li>
                        </x-permission_check>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <x-permission_check permission="view agent add money">
                            <div class="tab-pane fade show active" id="pills-deposit" role="tabpanel">
                                <div class="widget-card-wrapper custom-widget-wrapper">
                                    <div class="row g-0">
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--success">
                                                <a href="{{ route('admin.agent.deposit.list') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <span class="widget-icon">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    </span>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Total Add Money')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_deposit_amount_agent'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--warning">
                                                <a href="{{ route('admin.agent.deposit.pending') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-spinner"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Pending Add Money')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_deposit_pending_agent'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--danger">
                                                <a href="{{ route('admin.agent.deposit.rejected') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-ban"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Rejected Add Money')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_deposit_rejected_agent'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--primary">
                                                <a href="{{ route('admin.agent.deposit.list') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon ">
                                                        <i class="fas fa-percentage"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Add Money Charge')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_deposit_charge_agent'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--warning">
                                                <a href="{{ route('admin.agent.deposit.pending') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-spinner"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Pending Add Money Count')</p>
                                                        <h6 class="widget-amount">
                                                            {{ $widget['total_deposit_pending_count_agent'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--danger">
                                                <a href="{{ route('admin.agent.deposit.rejected') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-ban"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Rejected Add Money Count')</p>
                                                        <h6 class="widget-amount">
                                                            {{ $widget['total_deposit_rejected_count_agent'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-permission_check>
                        <x-permission_check permission="view agent withdraw">
                            <div class="tab-pane fade" id="pills-withdraw" role="tabpanel">
                                <div class="widget-card-wrapper custom-widget-wrapper">
                                    <div class="row g-0">
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--success">
                                                <a href="{{ route('admin.withdraw.data.all') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Total Withdrawal')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_withdraw_amount_agent'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="widget-card widget--warning">
                                                <a href="{{ route('admin.withdraw.data.pending') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-spinner"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Pending Withdrawal')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_withdraw_pending_agent'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                            <div class="widget-card widget--danger">
                                                <a href="{{ route('admin.withdraw.data.rejected') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-ban"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Rejected Withdrawal')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_withdraw_rejected_agent'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                            <div class="widget-card widget--primary">
                                                <a href="{{ route('admin.withdraw.data.all') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-percentage"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Withdrawal Charge')</p>
                                                        <h6 class="widget-amount">
                                                            {{ gs('cur_sym') }}{{ showAmount($widget['total_withdraw_charge_agent'], currencyFormat: false) }}
                                                            <span class="currency">{{ __(gs('cur_text')) }}</span>
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>'
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                            <div class="widget-card widget--warning">
                                                <a href="{{ route('admin.withdraw.data.pending') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-spinner"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Pending Withdrawal Count')</p>
                                                        <h6 class="widget-amount">
                                                            {{ $widget['total_withdraw_pending_count_agent'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                            <div class="widget-card widget--danger">
                                                <a href="{{ route('admin.withdraw.data.rejected') }}"
                                                    class="widget-card-link"></a>
                                                <div class="widget-card-left">
                                                    <div class="widget-icon">
                                                        <i class="fas fa-ban"></i>
                                                    </div>
                                                    <div class="widget-card-content">
                                                        <p class="widget-title fs-14">@lang('Rejected Withdrawal Count')</p>
                                                        <h6 class="widget-amount">
                                                            {{ $widget['total_withdraw_rejected_count_agent'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <span class="widget-card-arrow">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-permission_check>
                    </div>
                </div>
            </div>
        </div>
    </x-permission_check>
</div>
