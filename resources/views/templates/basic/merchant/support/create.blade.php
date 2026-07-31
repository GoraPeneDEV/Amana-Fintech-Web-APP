@extends($activeTemplate . 'layouts.merchant')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body">
                    <form action="{{ route('ticket.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form--label">@lang('Subject')</label>
                                <input type="text" name="subject" value="{{ old('subject') }}"
                                    class="form-control form--control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form--label">@lang('Priority')</label>
                                <select name="priority" class="form-select form--control select2" required data-minimum-results-for-search="-1">
                                    <option value="3">@lang('High')</option>
                                    <option value="2">@lang('Medium')</option>
                                    <option value="1">@lang('Low')</option>
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <label class="form--label">@lang('Message')</label>
                                <textarea name="message" id="inputMessage" rows="6" class="form-control form--control" required>{{ old('message') }}</textarea>
                            </div>
                            <div class="col-md-12 form-group">
                                <div class="row fileUploadsContainer">
                                </div>
                                <div class="d-flex gap-3 mb-2">
                                    <button type="button" class="btn btn--light addAttachment">
                                        <i class="fas fa-plus"></i>
                                        @lang('Add Attachment')
                                    </button>
                                    <button class="btn btn--base " type="submit"><i class="fa-regular fa-paper-plane"></i>
                                        @lang('Submit')
                                    </button>
                                </div>
                                <p><span class="text--info">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('ticket.index') }}" class="btn  btn--base btn--md">
        <i class="fa fa-forward"></i> @lang('All Ticket')
    </a>
@endpush


@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group style-two">
                                <input type="file" name="attachments[]" class="form-control form--control style--sm" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text text-white removeFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
