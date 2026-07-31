@extends($activeTemplate . 'layouts.merchant')
@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card custom--card">
                <div class="card-body">
                    <form action="{{ route('merchant.kyc.submit') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <x-ovo-form identifier="act" identifierValue="merchant_kyc" />
                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">
                              <i class="fa fa-paper-plane"></i>  @lang('Submit')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
