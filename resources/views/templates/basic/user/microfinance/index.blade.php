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
                        <th>@lang('Organization')</th>
                        <th>@lang('Transaction ID')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Amount | Charge')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($microFinances as $microFinance)
                        <tr>

                            <td>
                                <div class="customer">
                                    <div class="customer__thumb">
                                        <img src="{{ getImage(getFilePath('microfinance') . '/' . @$microFinance->ngo->image) }}"
                                            class="fit-image" alt="">
                                    </div>
                                    <div class="customer__content">
                                        <h6 class="customer__name">{{ __(@$microFinance->ngo->name) }}</h6>
                                    </div>
                                </div>

                            </td>
                            <td>
                                <span class="text--base fw-bold">
                                    {{ __($microFinance->trx) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    {{ showDateTime($microFinance->created_at) }}<br>
                                    {{ diffForHumans($microFinance->created_at) }}
                                </div>
                            </td>

                            <td>
                                <div>
                                    <span class="fw-bold">{{ showAmount($microFinance->amount) }}</span><br>
                                    <span class="text--danger">{{ showAmount($microFinance->charge) }}</span>
                                </div>
                            </td>
                            <td>
                                @php echo $microFinance->statusBadge @endphp
                            </td>
                            <td>
                                <a href="{{ route('user.microfinance.details', $microFinance->id) }}"
                                    class="btn btn--light btn--sm">
                                   <i class="fa fa-eye"></i> @lang('Details')
                                </a>
                            </td>

                        </tr>

                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>

            @if ($microFinances->hasPages())
                {{ paginateLinks($microFinances) }}
            @endif
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.microfinance.create') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('Microfinance')
    </a>
@endpush
