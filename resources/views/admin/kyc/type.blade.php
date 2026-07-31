@extends('admin.layouts.app')
@section('panel')
<div class="row">

    <!-- User KYC Card -->
    <div class="col-xxl-4 col-lg-4 col-sm-6 config-col">
        <div class="system-configure">
            <div class="system-configure__header d-flex justify-content-between align-items-center">
                <div class="system-configure__title d-flex align-items-center gap-2">
                    <div class="icon"><i class="las la-user"></i></div>
                    <h6 class="mb-0 config-name">@lang('User KYC Configuration')</h6>
                </div>
                <div class="form-check form-switch form--switch pl-0 form-switch-success">
                    <a href="{{ route('admin.kyc.setting', ['type' => 'user']) }}" class="btn btn-outline--primary mt-2"><i class="las la-cog"></i> @lang('Generate')</a>
                </div>
            </div>
            <div class="system-configure__content">
                <p class="desc">
                    @lang('Easily generate a detailed KYC (Know Your Customer) form to securely verify user identity, ensuring compliance and trust while protecting sensitive information.')
                </p>
            </div>
        </div>
    </div>

    <!-- Agent KYC Card -->
    <div class="col-xxl-4 col-lg-4 col-sm-6 config-col">
        <div class="system-configure">
            <div class="system-configure__header d-flex justify-content-between align-items-center">
                <div class="system-configure__title d-flex align-items-center gap-2">
                    <div class="icon"><i class="las la-user-secret"></i></div>
                    <h6 class="mb-0 config-name">@lang('Agent KYC Configuration')</h6>
                </div>
                <div class="form-check form-switch form--switch pl-0 form-switch-success">
                    <a href="{{ route('admin.kyc.setting', ['type' => 'agent']) }}" class="btn btn-outline--primary mt-2"><i class="las la-cog"></i> @lang('Generate')</a>
                </div>
            </div>
            <div class="system-configure__content">
                <p class="desc">
                    @lang('Effortlessly create a comprehensive KYC form to securely verify agent identity, fostering transparency and building a reliable network of trusted agents.')
                </p>
            </div>
        </div>
    </div>

    <!-- Merchant KYC Card -->
    <div class="col-xxl-4 col-lg-4 col-sm-6 config-col">
        <div class="system-configure">
            <div class="system-configure__header d-flex justify-content-between align-items-center">
                <div class="system-configure__title d-flex align-items-center gap-2">
                    <div class="icon"><i class="las la-user-tie"></i></div>
                    <h6 class="mb-0 config-name">@lang('Merchant KYC Configuration')</h6>
                </div>
                <div class="form-check form-switch form--switch pl-0 form-switch-success">
                    <a href="{{ route('admin.kyc.setting', ['type' => 'merchant']) }}" class="btn btn-outline--primary mt-2"><i class="las la-cog"></i> @lang('Generate')</a>
                </div>
            </div>
            <div class="system-configure__content">
                <p class="desc">
                    @lang('Quickly design a robust KYC form to securely validate merchant identity, enabling seamless onboarding and safeguarding transactions within your platform.')
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
