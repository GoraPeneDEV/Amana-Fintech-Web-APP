@extends($activeTemplate . 'layouts.agent')
@section('content')
    <div class="row justify-content-center justify-content-xl-start">
        <div class="col-xxl-6 col-lg-10">
            <div class="card custom--card">
                <div class="card-body">
                    <form method="POST">
                        @csrf
                        <ul class="list-group list-group-flush mb-4">
                            @forelse ($withdrawMethods as $withdrawMethod)
                                <li class="list-group-item d-flex gap-2 flex-wrap justify-content-between ps-0">
                                    <span class="d-flex gap-2 flex-wrap pe-5 pe-md-0">
                                        <span class="section-bg withdrawmethod-img text-center">
                                            <img src="{{ getImage(getFilePath('withdrawMethod') . '/' . $withdrawMethod->image) }}"
                                                alt="image">
                                        </span>
                                        <span>
                                            <span class="fs-18">
                                                {{ @$withdrawMethod->name }}
                                            </span>
                                            <span class="d-block fs-14">@lang('Last updated : ')
                                                {{ showDateTime($withdrawMethod->updated_at, 'd M Y') }}</span>
                                        </span>
                                    </span>
                                    <span>
                                        <a href="{{ route('agent.withdraw.index') }}?method={{ $withdrawMethod->id }}"
                                            class="btn btn--base btn--sm"><i class="la la-arrow-circle-right"></i>
                                            @lang('Proceed to Withdraw')
                                        </a>
                                        <a href="{{ route('agent.withdraw.account.save', $withdrawMethod->id) }}"
                                            class="btn btn-dark btn--sm">
                                            <i class="las la-tools"></i> @lang('Save Account')
                                        </a>
                                    </span>
                                </li>
                            @empty
                                <li class="list-group-item text-center">{{ @$emptyMessage }}</li>
                            @endforelse
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
