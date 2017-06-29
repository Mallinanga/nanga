<?php

namespace Nanga\Supports;

class AirplaneMode
{

    public static function init()
    {
        $enabled = apply_filters('nanga_airplane_mode', true);
        if ( ! $enabled) {
            return;
        }
        if (defined('WP_ACCESSIBLE_HOSTS')) {
            return;
        }
        if ( ! defined('WP_HTTP_BLOCK_EXTERNAL')) {
            define('WP_HTTP_BLOCK_EXTERNAL', true);
        }
        $allowed = apply_filters('nanga_allowed_hosts', [
            '*.amazonaws.com',
            '*.cloudfront.net',
            '*.googleapis.com',
            '*.optimus.io',
            '*.woocommerce.com',
            '*.wordpress.org',
            'api.github.com',
            'connect.advancedcustomfields.com',
            'www.wpallimport.com',
        ]);
        define('WP_ACCESSIBLE_HOSTS', implode(',', $allowed));
    }
}
