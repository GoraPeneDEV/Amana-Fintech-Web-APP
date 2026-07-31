@props(['admin'])
@if ($admin)
    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end justify-content-md-start">
        <span class="table-thumb d-none d-lg-block">
            @if (@$admin->image)
                <img src="{{ $admin->image_src }}" alt="admin">
            @else
                <span class="name-short-form">
                    {{ __(@$admin->full_name_short_form ?? 'N/A') }}
                </span>
            @endif
        </span>
        <div>
            <strong class="d-block">
                {{ __(@$admin->fullname) }}
            </strong>
            <a class="fs-13">{{ @$admin->username }}</a>
        </div>
    </div>
@else
    <span>@lang('N/A')</span>
@endif
