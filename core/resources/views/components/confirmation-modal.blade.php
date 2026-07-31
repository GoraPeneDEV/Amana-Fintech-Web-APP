@props(['isFrontend' => false])

<div id="confirmationModal" class="modal fade custom--modal" tabindex="-1" role="dialog"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" id="confirmation-form">
                @csrf
                <div class="modal-body  py-4 px-5">
                    <div class="text-center mb-4">
                        <h1 class=" text--warning mb-0"><i class="la la-warning"></i></h1>
                        <h4 class="mb-2">@lang('Please Confirm!')</h4>
                        <p class="question"></p>
                    </div>
                    <div class="d-flex gap-3 flex-wrap pt-2 pb-3">
                        <div class="flex-fill">
                            <button type="button"
                                class="btn w-100 @if (!$isFrontend) btn--secondary btn-large @else btn-outline--dark @endif "
                                data-bs-dismiss="modal">
                                <i class="fa-regular fa-circle-xmark"></i> @lang('No')
                            </button>
                        </div>
                        <div class="flex-fill">
                            <button type="submit"
                                class="btn w-100   @if ($isFrontend) btn-outline--base @else btn--primary btn-large @endif">
                                <i class="fa-regular fa-check-circle"></i> @lang('Yes')
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.confirmationBtn', function() {
                var modal = $('#confirmationModal');
                let data = $(this).data();
                modal.find('.question').text(`${data.question}`);
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
