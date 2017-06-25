<?php

namespace Nanga\ThirdParty;

class WPRedis
{

    public static function init()
    {
        if ( ! function_exists('wp_cache_add_non_persistent_groups')) {
            return;
        }
        $actors = apply_filters('nanga_cache_bad_actors', [
            'bad-actor',
        ]);
        if ( ! empty($actors)) {
            wp_cache_add_non_persistent_groups($actors);
        }
    }
}
