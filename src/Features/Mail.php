<?php

namespace Nanga\Features;

class Mail
{

    public static function init()
    {
        // wp_password_change_notification also gets an override in /includes/helpers.php
        remove_action('after_password_reset', 'wp_password_change_notification');
    }
}
