'use strict';
(function ($) {
    // ==========================================
    //      Start Document Ready function
    // ==========================================
    $(document).ready(function () {
        //============================ Scroll To Top Icon Js Start =========
        (() => {
            const btn = $('.scroll-top');
            $(window).on('scroll', function () {
                if ($(window).scrollTop() >= 100) {
                    $('.header').addClass('fixed-header');
                    btn.addClass('show');
                } else {
                    $('.header').removeClass('fixed-header');
                    btn.removeClass('show');
                }
            });

            btn.on('click', function (e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, '300');
            });
        })()

        // ========================== Add Attribute For Bg Image Js Start =====================
        $('.bg-img').css('background-image', function () {
            return `url(${$(this).data('background-image')})`;
        });
        // ========================== Add Attribute For Bg Image Js End =====================

        // ================== Password Show Hide Js Start ==========
        $('.toggle-password').on('click', function () {
            $(this).toggleClass('fa-eye');
            var input = $($(this).attr('id'));
            if (input.attr('type') == 'password') {
                input.attr('type', 'text');
            } else {
                input.attr('type', 'password');
            }
        });
        // =============== Password Show Hide Js End =================

        // ================== Sidebar Menu Js Start ===============
        // Sidebar Dropdown Menu Start
        $('.has-dropdown > a').click(function () {
            $('.sidebar-submenu').slideUp(200);
            if ($(this).parent().hasClass('active')) {
                $('.has-dropdown').removeClass('active');
                $(this).parent().removeClass('active');
            } else {
                $('.has-dropdown').removeClass('active');
                $(this).next('.sidebar-submenu').slideDown(200);
                $(this).parent().addClass('active');
            }
        });
        // Sidebar Dropdown Menu End

        // Sidebar Icon & Overlay js
        $('.navigation-bar').on('click', function () {
            $('.sidebar-menu').addClass('show-sidebar');
            $('.sidebar-overlay').addClass('show');
        });

        $('.sidebar-menu__close, .sidebar-overlay').on('click', function () {
            $('.sidebar-menu').removeClass('show-sidebar');
            $('.sidebar-overlay').removeClass('show');
        });



        // calculate height
        function setHeight(hvar, idname) {

            let headerSelect = document.getElementsByClassName(`${idname}`)[0];

            if (headerSelect) {
                let headerHeight = headerSelect.clientHeight;
                document.documentElement.style.setProperty(`${hvar}`, `${headerHeight}px`);
            }
        }

        setHeight('--header-h', 'header')
        setHeight('--dh-h', 'dashboard-header')

        // load more
        var faqWrapper = $('.faq-wrapper');
        var loadBtn = $('.load-more');
        if (faqWrapper.height() < 410) {
            loadBtn.attr('style', 'display: none !important;');
            faqWrapper.addClass("expanded")
        }

        loadBtn.on('click', function () {
            if (faqWrapper.hasClass('expanded')) {
                faqWrapper.removeClass('expanded').css('max-height', '412px');
                $(this).html('Show More <i class="fa-solid fa-arrow-down"></i>');
            } else {
                var scrollHeight = faqWrapper.prop('scrollHeight');
                faqWrapper.addClass('expanded').css('max-height', scrollHeight + 'px');
                $(this).html('Show Less <i class="fa-solid fa-arrow-up"></i>');
            }
        });

        // otp
        const $inputs = $('.otp-input');

        $inputs.each(function (index) {
            $(this).on('input', function () {
                const value = $(this).val();
                if (value.length === 1 && index < $inputs.length - 1) {
                    $inputs.eq(index + 1).focus();
                }
            });

            $(this).on('keydown', function (e) {
                if (e.key === 'Backspace' && $(this).val() === '' && index > 0) {
                    $inputs.eq(index - 1).focus();
                }
            });
        });

        $inputs.eq(0).on('paste', function (e) {
            const pasteData = e.originalEvent.clipboardData.getData('text').trim();
            if (/^\d{6}$/.test(pasteData)) {
                $inputs.each(function (i) {
                    $(this).val(pasteData[i] || '');
                });
                $inputs.last().focus();
            }
            e.preventDefault();
        });

        // otp end

        // filter
        $('.showFilterBtn').on('click', function () {
            $('.responsive-filter-card').toggleClass('d-block');
        });
        $('.close-filter-btn').on('click', function () {
            $('.responsive-filter-card').removeClass('show');
        });

        // tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))


    });

    // ==========================================
    //      End Document Ready function
    // ==========================================

    // ========================= Preloader Js Start =====================
    $(window).on('load', function () {
        $('.preloader').fadeOut();
    });
    // ========================= Preloader Js End=====================


    //data highlight js$(document).ready(function() {
    $('[data-highlight]').each(function () {
        const $this = $(this);
        let originalText = $this.text().trim().split(' ');
        let textLength = originalText.length;
        const highlight = $this.data('highlight').toString();
        const highlightToArray = highlight.split(',');
        const hightLLightClass = $this.data('highlight-class') || "text--base";
        // Loop through each highlight range
        $.each(highlightToArray, function (i, element) {
            const index = element.toString().split('_');
            var startIndex = index[0];
            var endIndex = index.length > 1 ? index[1] : startIndex;
            if (startIndex < 0) {
                startIndex = textLength - Math.abs(startIndex);
            }
            if (endIndex < 0) {
                endIndex = textLength - Math.abs(endIndex);
            }
            const startIndexValue = originalText[startIndex];
            const endIndexValue = originalText[endIndex];
            if (startIndex === endIndex) {
                originalText[startIndex] = `<span class="${hightLLightClass}">${startIndexValue}</span>`;
            } else {
                originalText[startIndex] = `<span class="${hightLLightClass}">${startIndexValue}`;
                originalText[endIndex] = `${endIndexValue}</span>`;
            }
        });
        $this.html(originalText.join(' '))
    });


    //user sidebar search
    $('.search-sidebar').on(`input`, function () {
        let keyword = $(this).val().toLowerCase();
        let elements = $(".sidebar-menu-list").find('.sidebar-menu-list__item');
        if (keyword.length) {
            $(".sidebar-menu-list").find('.menu-title').addClass('d-none')
        } else {
            $(".sidebar-menu-list").find('.menu-title').removeClass('d-none')
        }
        $.each(elements, function (i, element) {
            const $element = $(element);
            let targetText = $element.find('.sidebar-menu-list__link .text').text()
                .toLowerCase();
            if (targetText.includes(keyword)) {
                $element.removeClass('d-none');
            } else {
                $element.addClass('d-none');
            }
        });
    });



    // header 
    $('.header-button').on('click', function () {
        $('.custom-nav').addClass('show');
        $('.sidebar-overlay').addClass('show');
    });

    $('.header-close, .sidebar-overlay').on('click', function () {
        $('.custom-nav').removeClass('show');
        $('.sidebar-overlay').removeClass('show');
    });

    // edit profile
    $('.edit-profile').on("click", function () {
        $('.profile-edit-wrapper').removeClass('hide-editable')
        $('.user-data-item .text').addClass('d-none')
        $('.upload-thumb-btn').removeClass('d-none');
        $(this).addClass('d-none')
    });


    // edit profile close
    $('.profile-edit-cancel-btn').on("click", function () {
        $('.profile-edit-wrapper').addClass('hide-editable')
        $('.user-data-item .text').removeClass('d-none');
        $('.edit-profile').removeClass('d-none');
        $('.upload-thumb-btn').addClass('d-none');

    });

    // let formStepIndex = 0;

    // $('.multi-form__btn button').on('click', function (e) {
    //     e.preventDefault();

    //     const totalSteps = $('.multi-form__item').length;

    //     if (formStepIndex < totalSteps - 1) {
    //         formStepIndex++;
    //         updateStatus();
    //     } else {
    //         console.log('Form complete');
    //     }
    // });
    // function updateStatus() {
    //     $('.multi-form__item').eq(formStepIndex - 1).addClass('has-after');
    //     const currentStep = $('.multi-form__item').eq(formStepIndex);
    //     currentStep.addClass('active');
    //     currentStep.find('input:disabled').prop('disabled', false);
    // }

})(jQuery);
