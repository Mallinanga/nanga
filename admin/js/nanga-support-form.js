(function ($) {
    'use strict';
    $(function () {
        var formMessages;
        var form;
        $.support.cors = true;
        form = $('#support-request-form');
        formMessages = $('.support-request-container__messages');
        //$.ajax({
        //    headers: {
        //        //"accept": "image/png",
        //        //"content-type": "application/json"
        //    },
        //    beforeSend: function (xhr) {
        //        //xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
        //        //xhr.setRequestHeader("Accept", "application/javascript");
        //        //xhr.setRequestHeader("Content-Type", "application/json");
        //    },
        //    //crossDomain: true,
        //    //type: "GET",
        //    url: ""
        //})
        //    .done(function (data) {
        //        console.log(data);
        //        //var items = [];
        //        //$.each(data, function (key, val) {
        //        //    items.push("<li id='" + key + "'>" + val + "</li>");
        //        //});
        //        //    html: items.join("")
        //        //}).appendTo("body");
        //    })
        //    .success(function (data) {
        //        console.log(data);
        //    });
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
            })
                .done(function () {
                    container.css('opacity', '1');
                    formMessages.text(nanga_support_request.msg_success);
                    form.remove();
                })
                .fail(function () {
                    container.css('opacity', '1');
                    formMessages.text(nanga_support_request.msg_error);
                    form.remove();
                });
        });
    });
    $(window).load(function () {
    });
})(jQuery);
