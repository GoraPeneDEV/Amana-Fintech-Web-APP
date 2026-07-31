@extends($activeTemplate . 'layouts.' . $layouts)
@section($section)
    <div class="{{ $extraClass }}">
        <div class="{{ $rowClass }}">
            <div class="col-lg-6">
                <div class="card  custom--card">
                    <div class="card-body">
                        <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert--base mb-4">
                                        <p class="mb-0"><i class="las la-info-circle"></i> @lang('You are requesting')
                                            <b>{{ showAmount($data['amount']) }}</b> @lang('to deposit.') @lang('Please pay')
                                            <b>{{ showAmount($data['final_amount'], currencyFormat: false) . ' ' . $data['method_currency'] }}
                                            </b> @lang('for successful payment.')
                                        </p>
                                    </div>
                                    <div class="mb-4">@php echo  $data->gateway->description @endphp</div>
                                </div>
                                <x-ovo-form identifier="id" identifierValue="{{ $gateway->form_id }}" />
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Pay Now')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
