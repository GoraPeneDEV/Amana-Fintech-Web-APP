@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card custom--card">
                <div class="card-body">
                    <form method="post">
                        @csrf
                        <div class="form-group">
                            <label class="form--label">@lang('Current PIN')</label>
                            <x-pin name="current_pin" autoSubmit="false" justifyClass="justify-content-start" />
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('New PIN')</label>
                            <x-pin name="pin" autoSubmit="false" justifyClass="justify-content-start" />
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Confirm PIN')</label>
                            <x-pin name="pin_confirmation" autoSubmit="false" justifyClass="justify-content-start" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
