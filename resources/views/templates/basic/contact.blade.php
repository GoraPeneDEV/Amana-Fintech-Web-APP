@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $contactContent = @getContent('contact_us.content', true)->data_values;
        $socialIcons = getContent('social_icon.element', false, orderById: true);
    @endphp

    <section class="contact-top mt-60">
        <div class="container">
            <div class="row gy-3 align-items-center">
                <div class="col-lg-6">
                    <h1>{{ __(@$contactContent->heading) }}</h1>
                </div>
                <div class="col-lg-6">
                    <div class="contact-top-right">
                        <ul class="social-list">
                            @foreach ($socialIcons as $socialIcon)
                                <li class="social-list__item">
                                    <a href="{{ @$socialIcon->data_values->url }}" target="_blank" class="social-list__link">
                                        @php echo @$socialIcon->data_values->social_icon @endphp
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact-section mb-60">
        <div class="container">
            <div class="contact-section-wrapper">
                <div class="row flex-lg-row-reverse">
                    <div class="col-lg-6">
                        <div class="contact-form">
                            <form method="post" class="verify-gcaptcha">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label class="form--label">@lang('First Name')</label>
                                        <input name="firstname" type="text" class="form--control"
                                            value="{{ old('firstname', @$user->firstname) }}" @readonly($user && $user->profile_complete) required
                                            placeholder="@lang('Enter your first name')">
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label class="form--label">@lang('Last Name')</label>
                                        <input name="lastname" type="text" class="form--control"
                                            value="{{ old('lastname', @$user->lastname) }}" @readonly($user && $user->profile_complete) required
                                            placeholder="@lang('Enter your last name')">
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <label class="form--label">@lang('Email')</label>
                                        <input name="email" type="email" class="form--control"
                                            value="{{ old('email', @$user->email) }}" @readonly($user) required
                                            placeholder="@lang('Enter your email')">
                                    </div>
                                    <div class="form-group">
                                        <label class="form--label">@lang('Subject')</label>
                                        <input name="subject" type="text" class="form--control"
                                            value="{{ old('subject') }}" required placeholder="@lang('Enter your subject')">
                                    </div>
                                    <div class="form-group">
                                        <label class="form--label">@lang('Message')</label>
                                        <textarea name="message" class="form--control" required placeholder="@lang('Write your message')">{{ old('message') }}</textarea>
                                    </div>
                                    <x-captcha />
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-paper-plane"></i> @lang('Submit')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="contact-map">
                            <iframe src="{{ @$contactContent->map_url }}" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="my-60">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-sm-6">
                    <div class="contact-info">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M23.4033 2.10398C23.537 2.10679 23.6373 2.22673 23.617 2.35889C23.452 3.34916 23.3442 3.98246 23.2077 4.44748C23.0802 4.88221 22.9667 5.02274 22.8782 5.10124C22.7808 5.18763 22.6293 5.28171 22.1888 5.34258C21.7065 5.40924 21.0482 5.41621 19.9998 5.41621C18.9513 5.41621 18.293 5.40924 17.8107 5.34258C17.3703 5.28171 17.2187 5.18763 17.1213 5.10124C17.0328 5.02274 16.9195 4.88223 16.7918 4.44748C16.6556 3.98324 16.5479 3.35128 16.3833 2.36383C16.3637 2.22866 16.4665 2.10671 16.603 2.10384C17.5987 2.08299 18.6975 2.08299 19.9068 2.08301H20.0928C21.3048 2.08299 22.406 2.08299 23.4033 2.10398ZM13.4334 2.30809C13.6613 2.27746 13.8687 2.43881 13.9017 2.66639L13.9117 2.73589L13.9158 2.76211L13.9275 2.83236C14.0782 3.73666 14.2092 4.52328 14.3937 5.15188C14.5922 5.82824 14.8899 6.46334 15.4629 6.97169C16.0555 7.49748 16.738 7.71836 17.4692 7.81941C18.1585 7.91468 19.0092 7.91659 20.0005 7.91659C20.9918 7.91659 21.8425 7.91468 22.5318 7.81941C23.2628 7.71836 23.9455 7.49748 24.5382 6.97169C25.111 6.46334 25.4087 5.82824 25.6072 5.15188C25.7917 4.52328 25.9228 3.73666 26.0735 2.83238L26.0852 2.76213L26.1005 2.66281C26.135 2.43694 26.3415 2.27766 26.568 2.30809C28.2952 2.54031 29.7175 3.03281 30.8427 4.15804C31.9678 5.28328 32.4605 6.70563 32.6927 8.43274C32.9173 10.1046 32.9173 12.2362 32.9173 14.9071V25.093C32.9173 27.764 32.9173 29.8955 32.6927 31.5673C32.4605 33.2945 31.9678 34.7168 30.8427 35.842C29.7175 36.9673 28.2952 37.4598 26.568 37.692C24.8962 37.9168 22.7645 37.9167 20.0935 37.9167H19.9078C17.2368 37.9167 15.1052 37.9168 13.4334 37.692C11.7062 37.4598 10.2839 36.9673 9.15865 35.842C8.03342 34.7168 7.5409 33.2945 7.3087 31.5673C7.08394 29.8955 7.08395 27.764 7.08399 25.093V14.9071C7.08395 12.2362 7.08394 10.1045 7.3087 8.43274C7.5409 6.70563 8.03342 5.28328 9.15865 4.15804C10.2839 3.03281 11.7062 2.54031 13.4334 2.30809ZM17.084 33.333C17.084 32.6427 17.6437 32.083 18.334 32.083H21.6673C22.3577 32.083 22.9173 32.6427 22.9173 33.333C22.9173 34.0233 22.3577 34.583 21.6673 34.583H18.334C17.6437 34.583 17.084 34.0233 17.084 33.333Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <span class="title">@lang('Contact Number')</span>
                        <h4 class="desc">
                            <a href="tel:{{ str_replace(['-',' '],'',@$contactContent->contact_number) }}">{{ @$contactContent->contact_number }}</a>
                        </h4>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="contact-info">

                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"
                                fill="none">
                                <path
                                    d="M9.37337 10.9737C9.7563 11.5482 9.60109 12.3242 9.02669 12.7072L6.99535 14.0614C6.24815 14.5595 5.75929 14.887 5.40177 15.1769C5.06455 15.4504 4.91785 15.6338 4.82619 15.8058C4.78955 15.8745 4.75639 15.9511 4.72724 16.0457L15.4982 22.5083C16.773 23.2733 17.6507 23.7981 18.38 24.1409C19.0837 24.4718 19.5559 24.5861 20 24.5861C20.4442 24.5861 20.9164 24.4718 21.62 24.1409C22.3494 23.7981 23.227 23.2733 24.5019 22.5083L35.2727 16.0458C35.2435 15.9511 35.2104 15.8745 35.1737 15.8058C35.082 15.6338 34.9354 15.4504 34.5982 15.1769C34.2407 14.887 33.7519 14.5595 33.0045 14.0614L30.9732 12.7072C30.3989 12.3242 30.2437 11.5482 30.6265 10.9737C31.0095 10.3993 31.7857 10.2441 32.36 10.6271L34.4369 12.0116C35.1262 12.4711 35.7137 12.8628 36.1729 13.2352C36.6624 13.6321 37.077 14.0616 37.3799 14.6298C37.7142 15.2569 37.8325 15.9022 37.8804 16.6018C37.9175 17.1436 37.9154 17.7863 37.9129 18.5314L37.9127 18.5723C37.9057 20.6716 37.886 22.8161 37.8319 24.9886L37.8295 25.0859C37.7677 27.5653 37.7177 29.5654 37.4339 31.1748C37.136 32.8638 36.5639 34.2383 35.3904 35.4118C34.2144 36.5878 32.8289 37.1589 31.1255 37.4564C29.4999 37.7404 27.4747 37.7909 24.9604 37.8538L24.8634 37.8563C21.6115 37.9374 18.3885 37.9374 15.1367 37.8563L15.0396 37.8538C12.5253 37.7909 10.5002 37.7404 8.87455 37.4564C7.1711 37.1589 5.78564 36.5878 4.60969 35.4118C3.43619 34.2383 2.86399 32.8638 2.5661 31.1748C2.28227 29.5654 2.23239 27.5653 2.17054 25.0859L2.1681 24.9886C2.11387 22.8161 2.09435 20.6718 2.08729 18.5723L2.08715 18.5318C2.08464 17.7864 2.08247 17.1436 2.11957 16.6017C2.16745 15.9022 2.28577 15.2569 2.62002 14.6298C2.92292 14.0616 3.33755 13.6321 3.82709 13.2352C4.28624 12.8628 4.87374 12.4712 5.56305 12.0116L7.63994 10.6271C8.21434 10.2441 8.99044 10.3993 9.37337 10.9737Z"
                                    fill="currentColor" />
                                <path
                                    d="M25.0873 2.08301C26.5848 2.08296 27.8335 2.08293 28.8248 2.21619C29.8712 2.35688 30.8158 2.66633 31.5748 3.42544C32.334 4.18456 32.6435 5.12923 32.7842 6.17554C32.9175 7.16684 32.9173 8.41553 32.9173 9.91298V19.9997C32.9173 20.69 32.3577 21.2497 31.6673 21.2497C30.977 21.2497 30.4173 20.69 30.4173 19.9997V9.99968C30.4173 8.39299 30.4147 7.31373 30.3065 6.50866C30.203 5.73956 30.0243 5.41041 29.8072 5.19321C29.59 4.97601 29.2608 4.79731 28.4917 4.69391C27.6867 4.58568 26.6073 4.58301 25.0007 4.58301H15.0007C13.394 4.58301 12.3147 4.58568 11.5096 4.69391C10.7405 4.79731 10.4114 4.97601 10.1942 5.19321C9.97699 5.41041 9.79829 5.73956 9.69489 6.50866C9.58665 7.31373 9.58399 8.39299 9.58399 9.99968V19.9997C9.58399 20.69 9.02435 21.2497 8.33399 21.2497C7.64364 21.2497 7.08399 20.69 7.08399 19.9997V9.91301C7.08394 8.41556 7.0839 7.16684 7.21717 6.17554C7.35785 5.12923 7.6673 4.18456 8.42642 3.42544C9.18554 2.66633 10.1302 2.35688 11.1765 2.21619C12.1678 2.08293 13.4165 2.08296 14.9139 2.08301H25.0873Z"
                                    fill="currentColor" />
                                <path
                                    d="M15.6548 9.37372C17.358 8.40639 18.9505 8.69915 20.0035 9.34062C21.0563 8.69915 22.6488 8.40639 24.352 9.37372C25.7363 10.16 26.455 11.7724 26.2032 13.5106C25.9512 15.2512 24.7645 17.0457 22.5403 18.57L22.4375 18.6407C21.7498 19.1138 21.0668 19.5837 20.0035 19.5837C18.94 19.5837 18.257 19.1138 17.5693 18.6407L17.4665 18.57C15.2424 17.0457 14.0557 15.2512 13.8036 13.5106C13.5518 11.7724 14.2705 10.16 15.6548 9.37372Z"
                                    fill="currentColor" />
                            </svg>

                        </div>
                        <span class="title">@lang('Email')</span>
                        <h4 class="desc">
                            <a
                                href="mailto:{{ @$contactContent->email_address }}">{{ @$contactContent->email_address }}</a>
                        </h4>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="contact-info">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-map-pin-house">
                                <path
                                    d="M15 22a1 1 0 0 1-1-1v-4a1 1 0 0 1 .445-.832l3-2a1 1 0 0 1 1.11 0l3 2A1 1 0 0 1 22 17v4a1 1 0 0 1-1 1z" />
                                <path d="M18 10a8 8 0 0 0-16 0c0 4.993 5.539 10.193 7.399 11.799a1 1 0 0 0 .601.2" />
                                <path d="M18 22v-3" />
                                <circle cx="10" cy="10" r="3" />
                            </svg>
                        </div>
                        <span class="title">@lang('Address')</span>
                        <h4 class="desc">
                            <span>
                                {{ @$contactContent->location }}
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
