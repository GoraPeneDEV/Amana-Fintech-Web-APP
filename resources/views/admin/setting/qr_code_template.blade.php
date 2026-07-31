@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        @if (gs('qr_code_template'))
            <div class="col-xl-5 form-group">
                <x-admin.ui.card>
                    <x-admin.ui.card.body>
                        <div class="p-3 bg--white">
                            <div class="">
                                <img src="{{ getImage(getFilePath('qr_code_template') . '/' . gs('qr_code_template'), '2480x3508') }}"
                                    class="b-radius--10 w-100">
                            </div>
                        </div>
                    </x-admin.ui.card.body>
                </x-admin.ui.card>
            </div>
        @endif
        <div class="col-xl-{{ gs('qr_code_template') ? 4 : 6 }} form-group">
            <div class="custom">
                <div class="file_upload">
                    <form class="form-data border-primary" enctype="multipart/form-data" id="form" method="post">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Upload New Template Image')</label>
                            <x-image-uploader name="qr_code_template" :imagePath="getImage(
                                getFilePath('qr_code_template') . '/' . @gs('qr_code_template'),
                                getFileSize('qr_code_template'),
                            )" :size="getFileSize('qr_code_template')"
                                :required="false" />
                        </div>
                        <div class="form-group">
                            <x-admin.ui.btn.submit class="w-100" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
