<?php

namespace Nanga\ThirdParty;

class Timber
{

    public static function init()
    {
        if ( ! class_exists('Timber')) {
            return;
        }
        add_filter('timber/cache/location', self::cacheLocation());
        /*
        if (class_exists('TimberLoader')) {
            TimberCommand::clear_cache_timber();
            TimberCommand::clear_cache_twig();
        }
        */
    }

    private static function cacheLocation()
    {
        return WP_CONTENT_DIR . '/cache/timber';
    }
}
