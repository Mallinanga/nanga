<?php

namespace Nanga\ThirdParty;

class WPML
{

    public static function init()
    {
        if ( ! class_exists('SitePress')) {
            return;
        }
        global $sitepress;
        remove_action('wp_head', [$sitepress, 'meta_generator_tag']);
        add_action('admin_init', [self::class, 'user']);
        // add_action('wp_before_admin_bar_render', [self::class, 'node']);
        // add_filter('wpml_hide_management_column', '__return_false');
    }

    public static function node()
    {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('WPML_ALS');
    }

    public static function user()
    {
        global $sitepress;
        remove_action('show_user_profile', [$sitepress, 'show_user_options']);
    }
}
