@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header">
            <form class="table-search no-submit-loader">
                <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                    placeholder="@lang('Search...')">
                <button class="icon px-3" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table--responsive--xl">
                <thead>
                    <tr>
                        <th>@lang('Request From')</th>
                        <th>@lang('Transaction ID')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Amount | Charge')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Note')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requestedMoneys as $requestedMoney)
                        <tr>

                            <td>
                                <div class="customer">
                                    <div class="customer__thumb">
                                        <img src="{{ @$requestedMoney->requestSender->image_src }}" class="fit-image"
                                            alt="">
                                    </div>
                                    <div class="customer__content">
                                        <h6 class="customer__name">{{ __(@$requestedMoney->requestSender->fullname) }}</h6>
                                        <a href="{{ route('user.request.money.history', ['search' => @$requestedMoney->requestSender->mobile]) }}"
                                            class="customer__text"> {{ __(@$requestedMoney->requestSender->mobile) }}</a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text--base fw-bold">
                                    {{ __($requestedMoney->trx) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    {{ showDateTime($requestedMoney->created_at) }}<br>
                                    {{ diffForHumans($requestedMoney->created_at) }}
                                </div>
                            </td>
                            <td>
                                <div>
                                    {{ showAmount($requestedMoney->amount) }}<br>
                                    <span class="text--danger">{{ showAmount($requestedMoney->charge) }}</span>
                                </div>
                            </td>
                            <td> @php echo $requestedMoney->requestMoneyStatus @endphp </td>
                            <td>
                                {{ __($requestedMoney->note ?? '-') }}
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a href="{{ route('user.request.money.received.details.view', $requestedMoney->id) }}"
                                        class="btn btn--info btn--sm" data-bs-toggle="tooltip" title="@lang('Details')">
                                        <i class="las la-eye"></i>
                                    </a>
                                    
                                    @if ($requestedMoney->status == Status::PENDING)
                                        <a href="{{ route('user.request.money.received.details', $requestedMoney->id) }}"
                                            class="btn btn--success btn--sm" data-bs-toggle="tooltip" title="@lang('Accept')">
                                            <i class="las la-check-circle"></i>
                                        </a>
                                        <button type="button" class="btn btn--danger btn--sm confirmationBtn"
                                            data-question='@lang('Are you sure rejected this money request?')'
                                            data-action="{{ route('user.request.money.reject', $requestedMoney->id) }}" data-bs-toggle="tooltip" title="@lang('Reject')">
                                            <i class="las la-times-circle"></i>
                                        </button>
                                    @endif
                                   
                                </div>

                            </td>
                        </tr>

                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>

            @if ($requestedMoneys->hasPages())
                {{ paginateLinks($requestedMoneys) }}
            @endif
        </div>
    </div>
    <x-confirmation-modal :isFrontend="true" />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.request.money.history') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-list"></i></span>
        @lang('Request Money History')
    </a>
@endpush
