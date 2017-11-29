<?php

namespace Nanga\ThirdParty;

class Yoast
{

    public static function init()
    {
        if ( ! class_exists('WPSEO_Frontend')) {
            return;
        }
        add_filter('user_has_cap', [self::class, 'capabilities']);
        add_filter('wpseo_allow_xml_sitemap_ping', function () {
            return nanga_site_in_production();
        });
        add_filter('wpseo_bulk_edit_roles', function ($roles) {
            return ['administrator'];
        });
        // add_filter('disable_wpseo_json_ld_search', '__return_true');
        /*
        add_filter('wpseo_json_ld_output', function ($data) {
            $data = [];

            return $data;
        }, 10, 1);
        */
        // remove_action('wp_enqueue_scripts', 'wpseo_admin_bar_style');
        add_action('admin_init', function () {
            remove_action('edit_user_profile', ['WPSEO_Admin_User_Profile', 'user_profile'], 666);
            remove_action('show_user_profile', ['WPSEO_Admin_User_Profile', 'user_profile'], 666);
        }, 666);
        add_action('admin_init', function () {
            remove_action('show_user_profile', ['WPSEO_Admin_User_Profile', 'user_profile']);
            remove_action('edit_user_profile', ['WPSEO_Admin_User_Profile', 'user_profile']);
        }, 666);
        remove_action('edit_user_profile', [WPSEO_Admin_User_Profile::class, 'user_profile'], 666);
        remove_action('show_user_profile', [WPSEO_Admin_User_Profile::class, 'user_profile'], 666);
    }

    public static function capabilities($caps)
    {
        if (empty($caps['manage_options'])) {
            $caps['wpseo_bulk_edit'] = false;
        }

        return $caps;
    }
}
