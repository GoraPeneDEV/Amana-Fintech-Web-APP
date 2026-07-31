@php
    $faqContent = @getContent('faq.content', true)->data_values;
    $faqElements = @getContent('faq.element');
@endphp

<section class="faq-section my-120">
    <div class="container">
        <div class="flex-between gap-3">
            <div class="section-heading style-left mb-0">
                <h1 class="section-heading__title">
                    {{ __(@$faqContent->heading) }}
                </h1>
            </div>
        </div>
        <div class="faq-wrapper">
            @foreach ($faqElements as $faqElement)
                <div class="faq-item">
                    <span class="faq-item-icon">
                        @php echo @$faqElement->data_values->icon @endphp
                    </span>
                    <div class="faq-item-content">
                        <h6 class="faq-item-title">
                            {{ __(@$faqElement->data_values->question) }}
                        </h6>
                        <span class="faq-item-desc">
                            {{ __(@$faqElement->data_values->answer) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center my-3 my-md-0">
            <button class="btn btn--light btn--md load-more" type="button">
                @lang('Load More') <i class="fa-solid fa-arrow-down"></i>
            </button>
        </div>
        <div class="faq-card">
            <div class="faq-card-info">
                <div class="thumb">
                    <img class="fit-image" src="{{ siteFavicon() }}" alt="">
                </div>
                <div class="content">
                    <h6 class="title">@lang('Still have question?')</h6>
                    <p class="desc">
                        @lang('Can’t find the answers you’re looking for?')
                        <span class="fw-medium text--dark">
                            @lang('Please chat to our friendly team').
                        </span>
                    </p>
                </div>
            </div>
            <div class="faq-card-btn">
                <a href="{{ route('contact') }}" class="btn btn--base btn--md">
                    @lang('Get in touch')
                </a>
            </div>
        </div>
    </div>
</section>
