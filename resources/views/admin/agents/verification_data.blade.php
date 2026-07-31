@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-admin.ui.card>
                <x-admin.ui.card.body>
                    @if ($agent->verification_data)
                        <ul class="list-group list-group-flush">
                            @foreach ($agent->verification_data as $val)
                                @continue(!$val->value)
                                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                    {{ __($val->name) }}
                                    <span>
                                        @if ($val->type == 'checkbox')
                                            {{ implode(',', $val->value) }}
                                        @elseif($val->type == 'file')
                                            @if ($val->value)
                                                <a
                                                    href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}"><i
                                                        class="fa-regular fa-file"></i> @lang('Attachment') </a>
                                            @else
                                                @lang('No File')
                                            @endif
                                        @else
                                            <p>{{ __($val->value) }}</p>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <h5 class="text-center">@lang('Verification data not found')</h5>
                    @endif

                    
                </x-admin.ui.card.body>
            </x-admin.ui.card>
        </div>
    </div>

    <x-admin.ui.modal id="kycRejectionModal">
        <x-admin.ui.modal.header>
            <h1 class="modal-title">@lang('Reject Verification Documents')</h1>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </x-admin.ui.modal.header>
        <x-admin.ui.modal.body>
            <form action="{{ route('admin.agents.data.reject', $agent->id) }}" method="POST">
                @csrf
                <div class="alert alert-primary p-3">
                    @lang('If you reject these documents, the agent will be able to re-submit new documents and these documents will be replaced by new documents.')
                </div>
                <div class="form-group">
                    <label>@lang('Rejection Reason')</label>
                    <textarea class="form-control" name="reason" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <x-admin.ui.btn.modal />
                </div>
            </form>
        </x-admin.ui.modal.body>
    </x-admin.ui.modal>

    <x-confirmation-modal />
@endsection
