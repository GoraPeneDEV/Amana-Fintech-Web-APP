@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @php echo $cookie?->data_values?->description @endphp
                </div>
            </div>
        </div>
    </section>
@endsection



@push("style")
<style>
    h4{
        margin-bottom: 1rem;
        }
    
</style>
@endpush