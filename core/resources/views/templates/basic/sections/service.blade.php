@php
    $serviceElements = getContent('service.element', orderById: true);
@endphp

<section class="feature-section my-120">
    <div class="container">
        <div class="row gy-4">
            @foreach ($serviceElements as $serviceElement)
                <div class="{{ @$serviceElement->data_values->width }}">
                    <div class="feature-item" style="background-color:{{ @$serviceElement->data_values->background_color }} ">
                        <div class="feature-item-top">
                            <h1 class="feature-item-title">{{ __(@$serviceElement->data_values->heading) }}</h1>
                            <div class="feature-item-thumb">
                                <img src="{{ frontendImage('service', @$serviceElement->data_values->service_image) }}"
                                    alt="image">
                            </div>
                        </div>
                        <p class="feature-item-desc">
                            {{ __(@$serviceElement->data_values->short_description) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
