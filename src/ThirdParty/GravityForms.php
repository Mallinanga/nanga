<?php

namespace Nanga\ThirdParty;

class GravityForms
{

    public static function init()
    {
        if ( ! class_exists('GFForms')) {
            return;
        }
        remove_action('widgets_init', 'gf_register_widget');
        add_action('admin_menu', [self::class, 'menu'], 100);
        add_filter('gform_disable_view_counter', '__return_true');
    }

    public static function menu()
    {
        remove_submenu_page('gf_edit_forms', 'gf_addons');
        remove_submenu_page('gf_edit_forms', 'gf_help');
        remove_submenu_page('gf_entries', 'gf_addons');
        remove_submenu_page('gf_entries', 'gf_help');
    }
}
