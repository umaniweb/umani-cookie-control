(function ($) {
    'use strict';

    $(function () {
        // Initialize all color pickers
        $('.umani-color-picker').wpColorPicker();

        // Initialize all code editors
        if (typeof wp !== 'undefined' && wp.codeEditor && typeof cm_settings !== 'undefined') {
            $('.umani-code-editor').each(function () {
                wp.codeEditor.initialize($(this), cm_settings);
            });
        }

        // Banner active toggle
        var config = window.umaniCCAdmin || {};
        var $checkbox = $('#' + config.bannerActiveId);
        var $sections = $('.banner-active');

        if ($checkbox.length) {
            function toggleSections() {
                if ($checkbox.is(':checked')) {
                    $sections.slideDown();
                } else {
                    $sections.slideUp();
                }
            }

            toggleSections();
            $checkbox.on('change', toggleSections);
        }
    });
})(jQuery);
