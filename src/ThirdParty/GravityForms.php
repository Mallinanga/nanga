<?php

namespace Nanga\ThirdParty;

class GravityForms
{

    public static function init()
    {
        if ( ! class_exists('GFForms')) {
            return;
        }
        add_action('admin_menu', [self::class, 'menu'], 100);
        add_filter('gform_disable_view_counter', '__return_true');
        remove_action('widgets_init', 'gf_register_widget');
        add_action('init', [self::class, 'updates'], 100);
    }

    public static function menu()
    {
        remove_submenu_page('gf_edit_forms', 'gf_addons');
        remove_submenu_page('gf_edit_forms', 'gf_help');
        remove_submenu_page('gf_entries', 'gf_addons');
        remove_submenu_page('gf_entries', 'gf_help');
    }

    public static function updates()
    {
        remove_action('after_plugin_row_gravityforms/gravityforms.php', ['GFAutoUpgrade', 'rg_plugin_row']);
        remove_action('after_plugin_row_gravityforms/gravityforms.php', ['GFForms', 'plugin_row'], 10);
        remove_action('install_plugins_pre_plugin-information', ['GFForms', 'display_changelog'], 9);
        remove_filter('auto_update_plugin', ['GFForms', 'maybe_auto_update'], 10, 2);
        remove_filter('plugins_api', ['GFForms', 'get_addon_info'], 100, 3);
        remove_filter('site_transient_update_plugins', ['GFForms', 'check_update']);
        remove_filter('transient_update_plugins', ['GFForms', 'check_update']);
    }
}
