@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card custom--card">
                        <div class="card-body text-center">
                            <img class="ban-image" src="{{ getImage($activeTemplateTrue . 'images/ban.jpg') }}" alt="">
                            <h2 class="text-center text--danger mb-3">@lang('YOUR ACCOUNT IS BANNED')</h2>
                            <div class="d-flex gap-2 flex-wrap justify-content-center mb-3">
                                <p class="fw-bold mb-1">@lang('Ban reason was'):</p>
                                <p>{{ $user->ban_reason }}</p>
                            </div>
                            <hr>
                            <div class="d-flex gap-3 flex-wrap justify-content-center">
                                <a href="{{ route('home') }}" class="btn btn--base">
                                    <i class="fa fa-globe"></i>
                                    @lang('Browse') {{ __(gs('site_name')) }}
                                </a>
                                <a href="{{ route('merchant.logout') }}" class="btn btn-outline--base">
                                    <i class="fas fa-sign-out "></i>
                                    @lang('Logout')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .ban-image {
            max-height: 400px;
        }
    </style>
@endpush
