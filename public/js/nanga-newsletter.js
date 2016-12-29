(function ($) {
    $("#newsletter-form")
        .submit(function (e) {
            e.preventDefault();
            var $form = $(this),
                email = $form.find('input[name="email"]').val(),
                list = $form.find('input[name="list"]').val(),
                nonce = vg_newsletter_form._nonce;
            var container = $(this).closest('.vg-container');
            container.css('opacity', '0.5');
            $.post(vg_app.ajax_url, {
                    email: email,
                    list: list,
                    action: 'vg_newsletter_subscribe',
                    _nonce: nonce
                },
                function (data) {
                    container.css('opacity', '1');
                    if (data) {
                        if (data === "Some fields are missing.") {
                            $(".newsletter__messages").text(vg_newsletter_form.msg_missing);
                        }
                        else if (data === "Invalid email address.") {
                            $(".newsletter__messages").text(vg_newsletter_form.msg_invalid_email);
                        }
                        else if (data === "Invalid list ID.") {
                            $(".newsletter__messages").text(vg_newsletter_form.msg_invalid_list);
                        }
                        else if (data === "Already subscribed.") {
                            $(".newsletter__messages").text(vg_newsletter_form.msg_alreay_subscribed);
                        } else {
                            $(".newsletter__messages").text(vg_newsletter_form.msg_thanks);
                        }
                    } else {
                        $(".newsletter__messages").text(vg_newsletter_form.msg_error);
                    }
                }
            );
        })
        .keypress(function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                $(this).submit();
            }
        });
    $("#newsletter-form__submit").click(function (e) {
        e.preventDefault();
        $("#newsletter-form").submit();
    });
}(jQuery));
