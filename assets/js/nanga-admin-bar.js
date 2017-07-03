(function ($) {
    function nangaDebugToggle() {
        var html = $('html');
        if (html.attr('style')) {
            html.removeAttr('style');
        } else {
            html.attr('style', 'margin-top:0!important;');
        }
        $('#wpadminbar').toggle();
        $('#nanga-debug-footer').hide();
    }

    $(function () {
        $('#nanga-bar-toggle').on('click', function (e) {
            if (localStorage.getItem('nanga-debug')) {
                localStorage.removeItem('nanga-debug');
            } else {
                localStorage.setItem('nanga-debug', 'hide');
            }
            nangaDebugToggle();
            e.preventDefault();
        });
        if (localStorage.getItem('nanga-debug')) {
            nangaDebugToggle();
        }
        // $('#nanga-debug-footer').show();

        $(document).on('heartbeat-send', function (e, data) {
            data['wp_heart_throb'] = 'beat';
        });
        $(document).on('heartbeat-tick', function (e, data) {
            var throbber;
            if (!data['wp_heart_throb']) {
                return;
            }
            throbber = $('#wp-admin-bar-nanga-heartbeat .ab-icon');
            throbber.toggleClass('beat');
            setTimeout(function () {
                throbber.toggleClass('beat');
            }, 10000);
        });
    });
})(jQuery);
