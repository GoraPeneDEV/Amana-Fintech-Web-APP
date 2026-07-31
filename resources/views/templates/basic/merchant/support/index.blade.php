@extends($activeTemplate . 'layouts.merchant')
@section('content')
    <div class="card custom--card">
        <div class="card-header">
            <form class="table-search no-submit-loader">
                <input name="search" value="{{ request()->search }}" type="text" class="form--control"
                    placeholder="@lang('Ticket Number')">
                <button class="icon px-3" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table--responsive--xl">
                <thead>
                    <tr>
                        <th>@lang('Ticket')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Priority')</th>
                        <th>@lang('Last Reply')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supports as $support)
                        <tr>
                            <td>
                                <div>
                                    <a href="{{ route('ticket.view', $support->ticket) }}"
                                        class="fw-bold d-block text-decoration-underline">
                                        #{{ $support->ticket }}
                                    </a>
                                    <span class="fs-14">
                                        <i>{{ __($support->subject) }}</i>
                                    </span>
                                </div>
                            </td>
                            <td> @php echo $support->statusBadge; @endphp </td>
                            <td>
                                @if ($support->priority == Status::PRIORITY_LOW)
                                    <span class="badge badge--dark">@lang('Low')</span>
                                @elseif($support->priority == Status::PRIORITY_MEDIUM)
                                    <span class="badge  badge--warning">@lang('Medium')</span>
                                @elseif($support->priority == Status::PRIORITY_HIGH)
                                    <span class="badge badge--danger">@lang('High')</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <span class="d-block"> {{ showDateTime($support->last_reply) }} </span>
                                    <span class="fs-14">
                                        <i> {{ diffForHumans($support->last_reply) }} </i>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn--light btn--sm">
                                    <i class="las la-eye"></i> @lang('View')
                                </a>
                            </td>
                        </tr>
                    @empty
                        @include('Template::partials.empty_message')
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('ticket.open') }}" class="btn btn--base btn--md">
        <span class="icon"><i class="fa fa-plus-circle"></i></span>
        @lang('Open Ticket')
    </a>
@endpush
