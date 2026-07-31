@php
    $breadcrumbContent = getContent('breadcrumb.content', true);
@endphp

<div class="breadcrumb-section bg-img py-60"
    data-background-image="{{ frontendImage('breadcrumb', @$breadcrumbContent->data_values->background_image) }}">
    <div class="container">
        <h2 class="text-center">{{ __($pageTitle) }}</h2>
    </div>
</div>