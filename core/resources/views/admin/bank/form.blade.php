@extends('admin.layouts.app')
@section('panel')
    <div class="submitRequired form-change-alert d-none">
        <i class="fas fa-exclamation-triangle"></i>
        @lang('You\'ve to click on the submit button to apply the changes')
    </div>
    <div class="row">
        <div class="col-12">
            <form method="post" action="{{ route('admin.setting.bank.transfer.configure', $bank->id) }}">
                @csrf
                <x-generated-form :form=$form generateTitle="Configuration for {{ $bank->name }}"
                    formTitle="Adjust Settings for {{ $bank->name }}"
                    :randerbtn=true />
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back_btn route="{{ route('admin.setting.bank.transfer.all') }}" />
@endpush
