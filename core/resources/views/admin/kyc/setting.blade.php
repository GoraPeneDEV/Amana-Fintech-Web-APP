@extends('admin.layouts.app')
@section('panel')
    <div class="submitRequired form-change-alert d-none">
        <i class="fas fa-exclamation-triangle"></i>
        @lang('You\'ve to click on the submit button to apply the changes')
    </div>
    <div class="row">
        <div class="col-12">
            <form method="post" action="{{ route('admin.kyc.setting.update', $type) }}">
                @csrf
                <x-generated-form :form=$form generateTitle="Generate KYC Form for {{ ucfirst($type) }}"
                    formTitle="KYC Form for {{ ucfirst($type) }}"
                    formSubtitle="Securely verify {{ $type }} identity through our simple KYC form."
                    generateSubTitle="Quickly generate KYC forms for easy {{ $type }} identity verification."
                    :randerbtn=true />
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back_btn route="{{ route('admin.kyc.type') }}" />
@endpush
