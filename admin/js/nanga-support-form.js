(function ($) {
    'use strict';
    $(function () {
        var formMessages;
        var form;
        form = $('#support-request-form');
        formMessages = $('.support-request-container__messages');
        /*
        @todo
        $.ajax({
            type: 'GET',
            url: ''
        }).done(function (data) {
            var items;
            items = [];
            $.each(data, function (key, val) {
                console.log(val);
                items.push("<div id='support-request-no" + key + "'>" + val.post_title + "</div>");
            });
            formMessages.append(items);
        });
        */
        $(form).submit(function (event) {
            var container;
            var formData;
            event.preventDefault();
            formData = $(form).serialize();
            container = $(this).closest('.vg-container');
            container.css('opacity', '0.5');
            $.ajax({
                headers: {
                    "accept": "application/javascript"
                },
                url: form.attr('action'),
                type: 'POST',
                data: formData
            }).done(function () {
                container.css('opacity', '1');
                formMessages.text(nanga_support_request.msg_success);
                form.remove();
            }).fail(function () {
                container.css('opacity', '1');
                formMessages.text(nanga_support_request.msg_error);
                form.remove();
            });
        });
    });
})(jQuery);
