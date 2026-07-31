@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog my-120">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                @foreach ($blogs as $blog)
                    @include('Template::partials.blog', ['blog' => $blog])
                @endforeach
            </div>
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
