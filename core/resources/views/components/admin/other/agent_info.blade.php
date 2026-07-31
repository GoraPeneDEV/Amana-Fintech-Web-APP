@props(['agent'])
@if ($agent)
    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end justify-content-md-start">
        <span class="table-thumb d-none d-lg-block">
            @if (@$agent->image)
                <img src="{{ $agent->image_src }}" alt="agent">
            @else
                <span class="name-short-form">
                    {{ __(@$agent->full_name_short_form ?? 'N/A') }}
                </span>
            @endif
        </span>
        <div class="text-start">
            <strong class="d-block">
                {{ __(@$agent->fullname) }}
            </strong>
            <a class="fs-13 " href="{{ route('admin.agents.detail', $agent->id) }}">{{ @$agent->username }}</a>
        </div>
    </div>
@else
    <span>@lang('N/A')</span>
@endif
