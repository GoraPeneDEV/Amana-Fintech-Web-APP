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
                        <th>@lang('Amount')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donations as $donation)
                        <tr>

                            <td>
                                <div class="customer">
                                    <div class="customer__thumb">
                                        <img src="{{ getImage(getFilePath('donation') . '/' . @$donation->donationFor->image) }}"
                                            class="fit-image" alt="">
                                    </div>
                                    <div class="customer__content">
                                        <h6 class="customer__name">{{ __(@$donation->donationFor->name) }}</h6>
                                    </div>
                                </div>

                            </td>
                            <td>
                                <span class="text--base fw-bold">
                                    {{ __($donation->trx) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    {{ showDateTime($donation->created_at) }}<br>
                                    {{ diffForHumans($donation->created_at) }}
                                </div>
                            </td>

                            <td>
                               <span class="fw-bold">{{ showAmount($donation->amount) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('user.donation.details', $donation->id) }}"
                                    class="btn btn--light btn--sm">@lang('Details')</a>
                            </td>

                        </tr>

                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>

            @if ($donations->hasPages())
                {{ paginateLinks($donations) }}
            @endif
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.donation.create') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('New Donate')
    </a>
@endpush
