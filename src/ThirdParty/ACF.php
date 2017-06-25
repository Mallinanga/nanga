<?php

namespace Nanga\ThirdParty;

class ACF
{

    public static function init()
    {
        if ( ! function_exists('acf_add_options_page')) {
            return;
        }
        add_filter('acf/settings/show_admin', [self::class, 'show']);
    }

    public static function show($show)
    {
        return current_user_can('manage_options') && nanga_site_in_development();
    }
}
