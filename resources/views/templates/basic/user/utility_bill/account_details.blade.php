<div class="form-group">
    <label class="form--label required">@lang('Unique ID')/ @lang('Meter Number') / @lang('Subscriber Number')</label>
    <input type="text" class="form--control" name="unique_id" required value="{{ @$company->unique_id }}" readonly>
</div>
<div class="form-group">
    <label class="form--label required">@lang('Reference')</label>
    <input type="text" class="form--control" name="reference" required value="">
</div>

<x-ovo-form identifier="id" identifierValue="{{ @$company->company->form_id }}" :filledData="$company->user_data" />
<input type="hidden" name="company_id" value="{{ @$company->company->id }}">
