(function ($) {
    $(function () {
        var form = $('#ajax-contact');
        var formMessages = $('.contact-container__messages');
        $(form).submit(function (event) {
            event.preventDefault();
            var formData = $(form).serialize() + "&action=vg_contact_form_submit&_nonce=" + vg_contact_form._nonce;
            var container = $(this).closest('.vg-container');
            container.css('opacity', '0.5');
            $.ajax({
                url: vg_app.ajax_url,
                type: 'POST',
                data: formData
            })
                .done(function (response) {
                    container.css('opacity', '1');
                    $(formMessages).removeClass('error');
                    $(formMessages).addClass('success');
                    $(formMessages).text(response);
                    $('#name').val('');
                    $('#email').val('');
                    $('#message').val('');
                })
                .fail(function (data) {
                    container.css('opacity', '1');
                    $(formMessages).removeClass('success');
                    $(formMessages).addClass('error');
                    if (data.responseText !== '') {
                        $(formMessages).text(data.responseText);
                    } else {
                        $(formMessages).text('Oops! An error occured and your message could not be sent.');
                    }
                });
        });
    });
})(jQuery);
