(function ($) {
    function nangaDebugToggle() {
        var html = $('html');
        if (html.attr('style')) {
            html.removeAttr('style');
        } else {
            html.attr('style', 'margin-top:0 !important;');
        }
        $('#wpadminbar').toggle();
        $('#nanga-debug-footer').hide();
    }

    $(function () {
        $('.nanga-debug-link').on('click', function (e) {
            $('#nanga-debug-footer').show();
            e.preventDefault();
        });
        $('#nanga-bar-toggle').on('click', function () {
            if (localStorage.getItem('nanga-debug')) {
                localStorage.removeItem('nanga-debug');
            } else {
                localStorage.setItem('nanga-debug', 'hide');
            }
            nangaDebugToggle();
        });
        if (localStorage.getItem('nanga-debug')) {
            nangaDebugToggle();
        }
        // $('#nanga-debug-footer').show();
    });
})(jQuery);
