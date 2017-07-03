<?php

namespace Nanga\Features;

class Heartbeat
{

    public static function init()
    {
        // add_action('admin_init', [self::class, 'disable'], 1);
        // add_filter('heartbeat_settings', [self::class, 'settings']);
    }

    public static function disable()
    {
        wp_deregister_script('heartbeat');
    }

    public static function settings($settings)
    {
        // error_log(print_r($settings, true));

        return $settings;
    }
}
