@php
    $ctaContent = @getContent('cta.content', true)->data_values;
    $ctaElements = @getContent('cta.element');
@endphp

<section class="cta" id="download-app">
    <div class="container">
        <div class="cta-wrapper">
            <div class="row align-items-end">
                <div class="col-lg-6">
                    <div class="cta-content">
                        <h1 class="cta-title">{{ __(@$ctaContent->heading) }}</h1>
                        <div class="cta-app">
                            <a href="{{ @$ctaContent->app_store_link }}" target="_blank" class="cta-app-item">
                                <img src="{{ frontendImage('cta', @$ctaContent->play_store_image) }}" alt="">
                            </a>
                            <a href="{{ @$ctaContent->play_store_link }}" class="cta-app-item">
                                <img src="{{ frontendImage('cta', @$ctaContent->app_store_image) }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="cta-thumb">
                        <img class="fit-image" src="{{ frontendImage('cta', @$ctaContent->image) }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
