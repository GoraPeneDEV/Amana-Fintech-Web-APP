<div class="col-lg-4 col-md-6">
    <div class="blog-item">
        <div class="blog-item__thumb">
            <a href="{{ route('blog.details', $blog->slug) }}" class="link">
                <img src="{{ frontendImage('blog', 'thumb_' . @$blog->data_values->image) }}" class="fit-image"
                    alt="">
            </a>
        </div>
        <div class="blog-item__content">
            <h6 class="blog-item__date">
                {{ showDateTime(@$blog->created_at, 'm.d.Y') }}
            </h6>
            <h4 class="blog-item__title">
                <a href="{{ route('blog.details', $blog->slug) }}" class="link">
                    {{ strLimit(__(@$blog->data_values->title), 70) }}
                </a>
            </h4>
            <p class="blog-item__desc">
                {{ __(strLimit(strip_tags(@$blog->data_values->description), 115)) }}
            </p>
            <a href="{{ route('blog.details', $blog->slug) }}" class="blog-item__link">
                @lang('Read More') <i class="fa-solid fa-arrow-right-long"></i>
            </a>
        </div>
    </div>
</div>
