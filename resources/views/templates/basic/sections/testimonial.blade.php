@php
    $testimonialContent = @getContent('testimonial.content', true)->data_values;
    $testimonialElements = @getContent('testimonial.element');
@endphp

<section class="testimonials mb-120">
    <div class=" container">
        <div class="section-heading style-left">
            <h1 class="section-heading__title">
                {{ __(@$testimonialContent->heading) }}
            </h1>
        </div>
        <div class="testimonial-slider">
            @foreach ($testimonialElements as $testimonialElement)
                <div class="testimonial-item">
                    <div class="testimonial-item__wrapper">
                        <div class="testimonial-item__top">
                            <div class="testimonial-item__info">
                                <div class="testimonial-item__thumb">
                                    <img src="{{ frontendImage('testimonial', @$testimonialElement->data_values->client_image) }}"
                                        class="fit-image" alt="">
                                </div>
                                <div class="testimonial-item__details">
                                    <h4 class="testimonial-item__name">
                                        {{ __(@$testimonialElement->data_values->name) }}
                                    </h4>
                                    <span class="testimonial-item__designation">
                                        {{ __(@$testimonialElement->data_values->designation) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-item__content">
                            <div class="row gy-4 align-items-center">
                                <div class="col-xxl-4 col-lg-5">
                                    <h1 class="testimonial-item__title">
                                        {{ __(@$testimonialElement->data_values->title) }}
                                    </h1>
                                </div>
                                <div class="col-xxl-8 col-lg-7">
                                    <q class="testimonial-item__desc">
                                        {{ __(@$testimonialElement->data_values->message) }}
                                    </q>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.testimonial-slider').slick({
                slidesToScroll: 1,
                autoplay: false,
                autoplaySpeed: 2000,
                speed: 1500,
                dots: true,
                pauseOnHover: true,
                arrows: false,
                prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-long-arrow-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fas fa-long-arrow-right"></i></button>',
                slidesToShow: 1,
                fade: true,
                arrows: true,
                speed: 500,
            });
        })(jQuery);
    </script>
@endpush
