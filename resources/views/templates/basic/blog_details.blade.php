@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-detials my-60">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-xl-9 col-lg-8">
                    <div class="blog-details">
                        <div class="blog-details__thumb">
                            <img src="{{ frontendImage('blog', @$blog->data_values->image) }}" alt="image">
                        </div>
                        <div class="blog-details__content">
                            <span class="blog-item__date mb-2">
                                <span class="blog-item__date-icon"><i class="las la-clock"></i></span>
                                {{ showDateTime(@$blog->created_at, 'd M, Y') }}
                            </span>
                            <h3 class="blog-details__title"> {{ __($blog->data_values->title) }} </h3>
                            <div class="blog-details__desc">
                                @php echo $blog->data_values->description @endphp
                            </div>
                            <div class="blog-details__share mt-4 d-flex align-items-center flex-wrap">
                                <ul class="social-list">
                                    <li class="social-list__item">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}%2Fblog%2F{{ __(@$blog->data_values->title) }}"
                                            target="_blank" class="social-list__link flex-center facebook">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a href="https://twitter.com/intent/tweet?text={{ __(@$blog->data_values->title) }}&amp;url={{ url()->current() }}"
                                            target="_blank" class="social-list__link flex-center twitter">
                                            <i class="fab fa-x-twitter"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a href="https://pinterest.com/pin/create/bookmarklet/?media={{ frontendImage('blog', @$blog->data_values->image, '965x450') }}g&url={{ url()->current() }}%2Fblog%2F{{ __(@$blog->data_values->title) }}"
                                            target="_blank" class="social-list__link flex-center pinterest">
                                            <i class="fab fa-pinterest-p"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ url()->current() }}%2Fblog%2F{{ __(@$blog->data_values->title) }}"
                                            target="_blank" class="social-list__link flex-center linkedin">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <div class="blog-sidebar-wrapper">
                        <div class="blog-sidebar">
                            <h4 class="blog-sidebar__title mb-3"> @lang('Latest Blog') </h4>
                            <div class="row gy-4 gy-lg-0">
                                @foreach ($latestBlogs as $latestBlog)
                                    <div class="col-xsm-6 col-sm-6 col-lg-12">
                                        <div class="latest-blog">
                                            <div class="latest-blog__thumb">
                                                <a href="{{ route('blog.details', $latestBlog->slug) }}">
                                                    <img src="{{ frontendImage('blog', @$latestBlog->data_values->image) }}"
                                                        class="fit-image" alt="">
                                                </a>
                                            </div>
                                            <div class="latest-blog__content">
                                                <h6 class="latest-blog__title">
                                                    <a href="{{ route('blog.details', $latestBlog->slug) }}">
                                                        {{ __(@$latestBlog->data_values->title) }}
                                                    </a>
                                                </h6>
                                                <span class="latest-blog__date fs-13">
                                                    {{ showDateTime(@$blog->created_at, 'd M, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush

@push("style")
<style>
    h4{
        margin-bottom: 1rem;
        }
    
</style>
@endpush