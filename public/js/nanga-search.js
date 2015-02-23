(function ($) {
    'use strict';
    $(function () {
        $(document).on('submit', '#vg-ajax-search-form', function () {
            var $form = $(this);
            var $input = $form.find('input[name="s"]');
            var query = $input.val();
            var $content = $('#content');
            var container = $('#page');
            console.log(query);
            $.ajax({
                type: 'post',
                url: vg_app.ajax_url,
                data: {
                    action: 'vg_ajax_search',
                    query: query
                },
                beforeSend: function () {
                    $input.prop('disabled', true);
                    container.css('opacity', '0.5');
                },
                success: function (response) {
                    $input.prop('disabled', false);
                    container.css('opacity', '1');
                    $content.html(response);
                }
            });
            return false;
        })
    });
    $(window).load(function () {
    });
})(jQuery);
