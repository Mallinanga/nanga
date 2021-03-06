<?php

namespace Nanga\ThirdParty;

class Jetpack
{

    public static function init()
    {
        if ( ! class_exists('Jetpack')) {
            return;
        }
        if (nanga_site_in_development()) {
            // add_filter('jetpack_is_staging_site', '__return_true');
            add_filter('jetpack_development_mode', '__return_true');
        }
        add_action('wp_enqueue_scripts', [self::class, 'assets'], 20);
        add_filter('jetpack_get_default_modules', '__return_empty_array');
        add_filter('jetpack_implode_frontend_css', '__return_false');
        add_filter('wpl_is_enabled_sitewide', '__return_false');
        add_action('nanga_settings_tab_content_extend', [self::class, 'modules']);
    }

    public static function assets()
    {
        wp_dequeue_script('devicepx');
    }

    public static function modules()
    {
        echo '<h2>Jetpack Modules</h2> ';
        echo '<p>Enable or disable modules <a href="' . admin_url('admin.php?page=jetpack_modules&activated=true') . '">here</a>.</p>';
    }
}
