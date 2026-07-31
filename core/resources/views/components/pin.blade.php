@props(['name' => 'pin', 'autoSubmit' => true, 'justifyClass' => "justify-content-start" ])
@php
    $parentClass = "$name-code-wrapper";
@endphp

<div class="otp-inner mb-4 {{ $justifyClass }}  flex-wrap {{ $parentClass }}">
    @for ($i = 1; $i <= (gs('user_pin_digits') ?? 6); $i++)
        <input type="password" maxlength="1" class="otp-input" />
    @endfor
    <input type="hidden" name="{{ $name }}" class="input" required>
</div>

@push('script')
    <script>
        "use strict";
        (function($) {
            const $parentElement = $('body').find(".{{ $parentClass }}");
            const $codeElements = $parentElement.find('.otp-input');
            const $inputElement = $parentElement.find("input[type=hidden]");
            const isAutoSubmit = JSON.parse(("{{ $autoSubmit }}").toString());

            //manage otp input
            $parentElement.on("input paste keydown keyup", ".otp-input", function() {

                let otpCode = "";
                let allFilled = true;

                $codeElements.each(function() {
                    if ($(this).val().length !== 1) {
                        allFilled = false;
                    }
                    otpCode += $(this).val();
                });

                $inputElement.val(otpCode);

                if (isAutoSubmit && allFilled) {

                    $parentElement.closest('form').submit();
                }
            });

        })(jQuery);
    </script>
@endpush
