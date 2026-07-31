@php
    $counterContent  = @getContent('counter.content', true)->data_values;
    $counterElements = @getContent('counter.element');
@endphp

<section class="statistic-section bg-img" data-background-image="{{ frontendImage('counter', @$counterContent->image) }}">
    <div class="container">
        <div class="counter-wrapper">
            @foreach ($counterElements as $counterElement)
                <div class="counter-item">
                    <h1 class="counter-item__number">
                        <span class="odometer" data-odometer-final=" {{ @$counterElement->data_values->count }}"></span>
                        {{ __(@$counterElement->data_values->abbreviation) }}
                    </h1>
                    <p class="counter-item__title">
                        {{ __(@$counterElement->data_values->title) }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/viewport.jquery.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/odometer.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/odometer.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $(".counter-item").each(function() {
                $(this).isInViewport(function(status) {
                    if (status === "entered") {
                        for (var i = 0; i < document.querySelectorAll(".odometer").length; i++) {
                            var el = document.querySelectorAll('.odometer')[i];
                            el.innerHTML = el.getAttribute("data-odometer-final");
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
