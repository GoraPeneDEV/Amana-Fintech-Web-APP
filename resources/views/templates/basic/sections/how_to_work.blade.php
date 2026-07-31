@php
    $howToContent = @getContent('how_to_work.content', true)->data_values;
    $howToElements = @getContent('how_to_work.element', orderById: true);
@endphp

<section class="how-works">
    <div class="how-works-cta bg-img"
        data-background-image="{{ frontendImage('how_to_work', @$howToContent->background_image) }}">
        <div class="container">
            <h5 class="how-works-cta-name">
                {{ __(@$howToContent->title_one) }}
            </h5>
            <h2 class="how-works-cta-desc">
                {{ __(@$howToContent->heading) }}
            </h2>
            <p class="how-works-cta-btn">
                <a href="{{ route('user.register') }}" class="btn btn--lg btn--grbtn pill">
                    @lang('Sign Up for Free')
                    <span class="ms-2">
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </span>
                </a>
            </p>
        </div>
    </div>
    <div class="container">
        <h1 class="how-works-title"> {{ __(@$howToContent->title_two) }}</h1>
    </div>
    <div class="how-works-widget">
        <div class="container">
            <div class="row gy-4">
                @foreach ($howToElements as $howToElement)
                    <div class="col-lg-4 col-md-6">
                        <div class="howwork-card">
                            <div class="howwork-card-wrapper">
                                <h1 class="howwork-card-count">
                                    {{ $loop->iteration }}
                                </h1>
                                <p class="howwork-card-icon">
                                    <img src="{{ frontendImage('how_to_work', @$howToElement->data_values->image) }}"
                                        alt="">
                                </p>
                                <h2 class="howwork-card-title">{{ __(@$howToElement->data_values->title) }}</h2>
                                <p class="howwork-card-desc">
                                    {{ __(@$howToElement->data_values->short_description) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
