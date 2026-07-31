@extends($activeTemplate . 'layouts.agent')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn--base showFilterBtn">
                    <i class="las la-filter"></i>
                </button>
            </div>
            <div class="card responsive-filter-card mb-4 custom--card">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4 flex-column flex-md-row">
                            <div class="flex-grow-1">
                                <label class="form-label">@lang('Transaction Number')</label>
                                <input type="search" name="search" value="{{ request()->search }}"
                                    class="form-control form--control" placeholder="@lang('Search...')">
                            </div>
                            <div class="flex-grow-1 select2-parent">
                                <label class="form-label d-block">@lang('Type')</label>
                                <select name="trx_type" class="form-select form--control select2"
                                    data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                                    <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1 select2-parent">
                                <label class="form-label d-block">@lang('Remark')</label>
                                <select class="form-select form--control select2" name="remark"
                                    data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    @foreach ($remarks as $remark)
                                        <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>
                                            {{ __(keyToTitle($remark->remark)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--base w-100">
                                    <i class="las la-filter"></i>
                                    @lang('Filter')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card custom--card">
                <div class="card-body p-0">
                    <table class="table table--responsive--xl ">
                        <thead>
                            <tr>
                                <th>@lang('Trx')</th>
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Charge')</th>
                                <th>@lang('Post Balance')</th>
                                <th>@lang('Detail')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        <strong>{{ $trx->trx }}</strong>
                                    </td>
                                    <td>
                                        <div class="text-end text-md-center">
                                            {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span
                                                class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                                {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        {{ showAmount($trx->charge) }}
                                    </td>
                                    <td>
                                        {{ showAmount($trx->post_balance) }}
                                    </td>
                                    <td>{{ __($trx->details) }}</td>
                                </tr>
                            @empty
                                @include('Template::partials.empty_message')
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

