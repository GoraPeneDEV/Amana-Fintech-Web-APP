@extends($activeTemplate . 'layouts.agent')
@section('content')
    <div class="row">
        <div class="col-xxl-9 col-lg-10">
            <div class="card custom--card">
                <div class="card-body">
                    <form class="user-data" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="row gy-4 hide-editable profile-edit-wrapper">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn--light btn--sm edit-profile">
                                    <span class="me-2">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </span>
                                    @lang('Edit')
                                </button>
                            </div>
                            <div class="col-lg-4 text-center">
                                <div class="upload-thumb">
                                    <div class="upload-thumb-img">
                                        <input type="file" name="image" class="d-none" id="profile" accept="image/*">
                                        <img src="{{ $agent->image_src }}" alt="" id="profile-preview">
                                    </div>
                                    <label class="upload-thumb-btn d-none" for="profile">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-image-up">
                                            <path
                                                d="M10.3 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10l-3.1-3.1a2 2 0 0 0-2.814.014L6 21" />
                                            <path d="m14 19.5 3-3 3 3" />
                                            <path d="M17 22v-5.5" />
                                            <circle cx="9" cy="9" r="2" />
                                        </svg>
                                    </label>
                                </div>
                                <div class="user-address">
                                    <h6 class="mb-2">@lang('Address')</h6>
                                    <div class="user-address-item">
                                        <p>{{ __($agent->full_address) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 ps-lg-5">
                                <div class="user-data-top">
                                    <h6 class="name">{{ __($agent->full_name) }}</h6>
                                    <ul class="verify-badge flex-align gap-3 mt-2">
                                        <li class="verify-badge-item">
                                            @if ($agent->ev)
                                                <span class="icon text--success">
                                                    <i class="fa-solid fa-check-circle"></i>
                                                </span>
                                            @else
                                                <span class="icon text--danger">
                                                    <i class="fa-solid  fa-times-circle"></i>
                                                </span>
                                            @endif
                                            <span class="text">@lang('Email')</span>
                                        </li>
                                        <li class="verify-badge-item">
                                            @if ($agent->sv)
                                                <span class="icon text--success">
                                                    <i class="fa-solid fa-check-circle"></i>
                                                </span>
                                            @else
                                                <span class="icon text--danger">
                                                    <i class="fa-solid  fa-times-circle"></i>
                                                </span>
                                            @endif
                                            <span class="text">@lang('Mobile')</span>
                                        </li>
                                        <li class="verify-badge-item">
                                            @if ($agent->kv == Status::YES)
                                                <span class="icon text--success">
                                                    <i class="fa-solid fa-check-circle"></i>
                                                </span>
                                            @else
                                                <span class="icon text--danger">
                                                    <i class="fa-solid  fa-times-circle"></i>
                                                </span>
                                            @endif
                                            <span class="text">@lang('KYC')</span>
                                        </li>
                                    </ul>
                                </div>

                                <h6 class="mb-3">@lang('Full Information')</h6>
                                <ul class="user-data-list">
                                    <li class="user-data-item">
                                        <p class="title">@lang('First Name')</p>
                                        <span class="devide">:</span>
                                        <span class="text">{{ __($agent->firstname) }}</span>
                                        <input type="text" class="form-control form--control sm-style" name="firstname"
                                            value="{{ $agent->firstname }}" required>
                                    </li>
                                    <li class="user-data-item">
                                        <p class="title">@lang('Last Name')</p>
                                        <span class="devide">:</span>
                                        <span class="text">{{ __($agent->lastname) }}</span>
                                        <input type="text" class="form-control form--control sm-style" name="lastname"
                                            value="{{ $agent->lastname }}" required>
                                    </li>
                                    <li class="user-data-item">
                                        <p class="title">@lang('E-mail Address')</p>
                                        <span class="devide">:</span>
                                        <span class="text">{{ $agent->email }}</span>
                                        <input class="form-control form--control sm-style" value="{{ $agent->email }}"
                                            readonly>
                                    </li>
                                    <li class="user-data-item">
                                        <p class="title">@lang('Mobile Number')</p>
                                        <span class="devide">:</span>
                                        <span class="text">{{ $agent->mobileNumber }}</span>
                                        <input class="form-control form--control sm-style" value="{{ $agent->mobile }}"
                                            readonly>
                                    </li>
                                    <li class="user-data-item">
                                        <p class="title">@lang('Address')</p>
                                        <span class="devide">:</span>
                                        <span class="text">{{ __($agent->address) }}</span>
                                        <input type="text" class="form-control form--control sm-style" name="address"
                                            value="{{ @$agent->address }}">
                                    </li>
                                    <li class="user-data-item">
                                        <p class="title">@lang('State')</p>
                                        <span class="devide">:</span>
                                        <span class="text">{{ __($agent->state) }}</span>
                                        <input type="text" class="form-control form--control sm-style" name="state"
                                            value="{{ @$agent->state }}">
                                    </li>
                                    <li class="user-data-item">
                                        <p class="title">@lang('Zip Code')</p>
                                        <span class="devide">:</span>
                                        <span class="text">{{ __($agent->zip) }}</span>
                                        <input type="text" class="form-control form--control sm-style" name="zip"
                                            value="{{ @$agent->zip }}">
                                    </li>
                                    <li class="user-data-item">
                                        <p class="title">@lang('City')</p>
                                        <span class="devide">:</span>
                                        <span class="text">{{ __($agent->city) }}</span>
                                        <input type="text" class="form-control form--control sm-style" name="city"
                                            value="{{ @$agent->city }}">
                                    </li>
                                    <li class="user-data-item">
                                        <p class="title">@lang('Country')</p>
                                        <span class="devide">:</span>
                                        <span class="text">{{ __(@$agent->country_name) }}</span>
                                        <input class="form-control form--control sm-style"
                                            value="{{ @$agent->country_name }}" disabled>
                                    </li>
                                    <li class="user-data-item mt-4 d-flex gap-2 flex-wrap">
                                        <div class="">
                                            <button type="button" class="btn btn-dark btn--md submit--btn w-100 profile-edit-cancel-btn">
                                                <i class="fa-regular fa-circle-xmark"></i> @lang('Cancel')
                                            </button>
                                        </div>
                                        <div class=" flex-grow-1">
                                            <button type="submit" class="btn btn--base btn--md submit--btn w-100">
                                                <i class="fa-regular fa-paper-plane"></i> @lang('Submit')
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('#profile').change(function(event) {

                var file = event.target.files[0];
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#profile-preview').attr('src', e.target.result).fadeOut().fadeIn();
                };

                if (file) {
                    reader.readAsDataURL(file);
                }
            });
        })(jQuery);
    </script>
@endpush
