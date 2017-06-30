<?php

namespace Nanga\Features;

class Rewrites
{

    public static function init()
    {
        remove_action('template_redirect', 'redirect_canonical');
        remove_action('template_redirect', 'wp_old_slug_redirect');
        remove_action('template_redirect', 'wp_redirect_admin_locations', 1000);
        remove_filter('template_redirect', 'wp_shortlink_header', 11);
    }
}
