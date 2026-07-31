@extends($activeTemplate . 'layouts.agent')
@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card custom--card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Withdraw Via') {{ $withdraw->method->name }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert--primary mb-3">
                        <p class="mb-0"><i class="las la-info-circle"></i> @lang('You are requesting')
                            <b>{{ showAmount($withdraw->amount) }}</b> @lang('for withdraw.') @lang('The admin will send you')
                            <b class="text--success">{{ showAmount($withdraw->final_amount, currencyFormat: false) . ' ' . $withdraw->currency }}
                            </b> @lang('to your account.')
                        </p>
                    </div>
                    <form action="{{ route('agent.withdraw.submit') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            @php echo $withdraw->method->description; @endphp
                        </div>
                        <x-ovo-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}" :filledData="$saveAccountData" />
                        @if (auth('agent')->user()->ts)
                            <div class="form-group">
                                <label class="form--label">@lang('Google Authenticator Code')</label>
                                <input type="text" name="authenticator_code" class="form-control form--control" required>
                            </div>
                        @endif
                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
