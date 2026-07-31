@php
    $footerContent = @getContent('footer.content', true)->data_values;
@endphp

@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="my-60">
        <div class="container">
            <div class="row">
                <div class="co-12">
                    @php
                        echo $policy->data_values->details;
                    @endphp
                </div>
            </div>
        </div>
    </div>
@endsection


@push("style")
<style>
    h4{
        margin-bottom: 1rem;
        }
    
</style>
@endpush