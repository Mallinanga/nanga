(function ($) {
    wp.customize('blogname', function (value) {
        value.bind(function (to) {
            $('.site-title a').text(to);
        });
    });
    wp.customize('blogdescription', function (value) {
        value.bind(function (to) {
            $('.site-description').text(to);
        });
    });
    wp.customize('site_color', function (value) {
        value.bind(function (to) {
            $('a').css({
                'color': to,
                'text-decoration': 'none'
            });
        });
    });
    wp.customize('site_secondary_color', function (value) {
        value.bind(function (to) {
            $('a:hover').css({
                'color': to
            });
        });
    });
})(jQuery);
