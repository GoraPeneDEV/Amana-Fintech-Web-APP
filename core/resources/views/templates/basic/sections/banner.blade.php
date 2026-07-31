@php
    $bannerContent = @getContent('banner.content', true)->data_values;
@endphp
<section class="banner-section">
    <div class="container">
        <div class="row gy-5 gx-lg-0 align-items-center flex-sm-column-reverse flex-lg-row-reverse">
            <div class="col-lg-6">
                <div class="banner-image">
                    <img class="fit-image" src="{{ frontendImage('banner', @$bannerContent->banner_image) }}"
                        alt="image">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="banner-content">
                    <h1 class="banner-content__title">
                        {{ __(@$bannerContent->heading) }}
                    </h1>
                    <div class="banner-content__wrapper">
                        <a href="{{ @$bannerContent->button_url }}" class="btn btn--lg btn--grbtn pill"
                            data-highlight="-2_-0" data-highlight-class="fst-italic fw-light">
                            {{ __(@$bannerContent->button_text) }}
                        </a>
                        <div class="banner-rating">
                            <img src="{{ frontendImage('banner', @$bannerContent->review_image) }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('style')
    <style>
        .banner-section::after {
            background-image: url("{{ frontendImage('banner', @$bannerContent->background_image) }}");
        }
    </style>
@endpush
