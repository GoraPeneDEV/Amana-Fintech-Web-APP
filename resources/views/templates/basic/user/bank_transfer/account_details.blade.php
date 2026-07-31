<div class="form--group">
    <label class="form--label required">@lang('Account Holder')</label>
    <div class="input-group border-0">
        <input type="text" class="form--control form-control" name="account_holder"
            value="{{ @$account->account_holder }}" @readonly(!is_null(@$account)) required>
    </div>
</div>
<div class="form--group">
    <label class="form--label required">@lang('Account Number')</label>
    <div class="input-group border-0">
        <input type="text" class="form--control form-control" name="account_number"
            value="{{ @$account->account_number }}" required @readonly(!is_null(@$account))>
    </div>
</div>

<x-ovo-form identifier="act" identifierValue="{{ 'bank_transfer_' . $bank->id }}" :filledData="@$bank->form->form_data" />

<input type="hidden" name="bank_id" value="{{ $bank->id }}">
<input type="hidden" name="user_bank_id" value="{{ @$account->id }}">
