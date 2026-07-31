<div class="form-group">
    <label class="form--label required">@lang('Unique ID')/ @lang('Meter Number') / @lang('Subscriber Number')</label>
    <input type="text" class="form--control" name="unique_id" required>
</div>
@if ($hideFile != 'yes')
    <div class="form-group">
        <label class="form--label required">@lang('Reference')</label>
        <input type="text" class="form--control" name="reference" required>
    </div>
@endif
