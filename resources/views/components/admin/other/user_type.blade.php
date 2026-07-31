<div class="form-group">
    <label class="form-label">@lang('User Type')</label>
    <select class="form-select select2" name="user_type" data-minimum-results-for-search="-1">
        <option>@lang('All')</option>
        <option value="user_id" @selected(request()->user_type == 'user_id')>@lang('User')</option>
        <option value="agent_id" @selected(request()->user_type == 'agent_id')> @lang('Agent')</option>
        <option value="merchant_id" @selected(request()->user_type == 'merchant_id')> @lang('Merchant')</option>
    </select>
</div>
