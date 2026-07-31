@php
    $blogContent = @getContent('blog.content', true)->data_values;
    $blogElements = @getContent('blog.element',limit:3);
@endphp

<section class="blog my-120">
    <div class="container">
        <div class="flex-between gap-3">
            <div class="section-heading mb-0">
                <h1 class="section-heading__title">
                    {{ __(@$blogContent->heading) }}
                </h1>
            </div>
            <a href="{{ route('blogs') }}" class="view-btn">
                @lang('View All')
                <span class="icon">
                    <i class="las la-arrow-circle-right"></i>
                </span>
            </a>
        </div>
        <div class="row gy-4 justify-content-center">
            @foreach ($blogElements as $blogElement)
                @include('Template::partials.blog', ['blog' => $blogElement])
            @endforeach
        </div>
    </div>
</section>
