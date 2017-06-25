(function ($) {
    $(function () {
        $('.score').on('click', function () {
            var link = $(this);
            link.addClass('is-loading');
            $.post('/wp-admin/admin-ajax.php', {action: 'vg_pagespeed_fetch_score', url: link.attr('data-url')}, function (response, status) {
                link.removeClass('is-loading score dashicons dashicons-image-rotate').text(response.data);
            });
        });
        $('.nanga-pagespeed__actions').on('click', function () {
            var report = $(this).data('report');
            $('.nanga-pagespeed__report--' + report).toggle();
        });
    });
})(jQuery);
