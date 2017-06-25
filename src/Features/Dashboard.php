<?php

namespace Nanga\Features;

class Dashboard
{

    public static function init()
    {
        remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
        add_action('admin_init', [self::class, 'scheme']);
        add_filter('get_user_option_admin_color', [self::class, 'colors']);
        // add_action('admin_init', [self::class, 'postboxes']);
        add_action('admin_head', [self::class, 'help']);
        add_action('admin_head', [self::class, 'opacity'], 100);
        add_action('admin_enqueue_scripts', [self::class, 'assets'], 100);
        add_action('admin_menu', [self::class, 'menu'], 999);
        remove_action('welcome_panel', 'wp_welcome_panel');
        add_action('wp_dashboard_setup', [self::class, 'metaboxes']);
        // add_action('admin_init', [self::class, 'layout']);
        // add_filter('get_user_option_screen_layout_dashboard', [self::class, 'one']);
        // add_filter('get_user_option_screen_layout_attachment', [self::class, 'one']);
        add_filter('manage_users_columns', [self::class, 'columnsUsers'], 100);
        add_filter('plugin_action_links_nanga/nanga.php', [self::class, 'pluginLinks']);
        add_filter('wp_default_editor', [self::class, 'editor']);
        add_action('admin_footer_text', '__return_empty_string');
        add_filter('update_footer', '__return_empty_string', 999);
    }

    public static function scheme()
    {
        wp_admin_css_color('nanga', 'VG web things', NANGA_DIR_URL . 'assets/css/nanga-admin-colors.css', ['#000000', '#0098ed', '#e1e1e1', '#ffffff'], ['base' => '#000', 'focus' => '#fff', 'current' => '#fff']);
    }

    public static function colors($colors)
    {
        return 'nanga';
    }

    public static function postboxes()
    {
        $postTypes = get_post_types(['_builtin' => false, 'public' => true]);
        foreach ($postTypes as $postType) {
            remove_meta_box('sharing_meta', $postType, 'advanced');
        }
    }

    public static function help()
    {
        $screen = get_current_screen();
        if ($screen) {
            $screen->remove_help_tabs();
        }
    }

    public static function opacity()
    {
        echo '<style>body{opacity:0;transition:opacity .25s;}</style>';
    }

    public static function assets($screen)
    {
        wp_enqueue_style('nanga-admin', NANGA_DIR_URL . 'assets/css/nanga-admin.css', [], NANGA_VERSION, 'all');
        wp_enqueue_script('nanga', NANGA_DIR_URL . 'assets/js/nanga-admin.js', ['jquery'], NANGA_VERSION, true);
        wp_localize_script('nanga', 'nanga', [
            'current_user' => get_current_user_id(),
            'environment'  => (defined('WP_ENV')) ? WP_ENV : null,
            'locale'       => get_locale(),
        ]);
    }

    public static function menu()
    {
        remove_submenu_page('edit.php', 'post-new.php');
        remove_submenu_page('edit.php?post_type=page', 'post-new.php?post_type=page');
        remove_submenu_page('index.php', 'wp-ses/ses-stats.php');
        remove_submenu_page('plugins.php', 'plugin-install.php');
        remove_submenu_page('upload.php', 'media-new.php');
        remove_submenu_page('users.php', 'user-new.php');
        if ( ! nanga_user_is_superadmin()) {
            remove_menu_page('tools.php');
        }
    }

    public static function metaboxes()
    {
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
        $metaboxes = apply_filters('nanga_dashboard_metaboxes', [
            'dashboard_activity',
            'dashboard_browser_nag',
            'dashboard_incoming_links',
            'dashboard_plugins',
            'dashboard_recent_comments',
            'dashboard_right_now',
            'icl_dashboard_widget',
            'jetpack_summary_widget',
            'rg_forms_dashboard',
            'woocommerce_dashboard_recent_orders',
            'woocommerce_dashboard_recent_reviews',
            'woocommerce_dashboard_right_now',
            'woocommerce_dashboard_sales',
            'woocommerce_dashboard_status',
            'wp_cube',
            'wpseo-dashboard-overview',
        ]);
        foreach ($metaboxes as $metabox) {
            remove_meta_box($metabox, 'dashboard', 'normal');
        }
    }

    public static function one()
    {
        return 1;
    }

    public static function columnsUsers($columns)
    {
        unset($columns['posts']);
        unset($columns['role']);
        unset($columns['ure_roles']);

        return $columns;
    }

    public static function pluginLinks($links)
    {
        return array_merge(['advanced_settings' => '<a href="' . admin_url('options-general.php?page=nanga-settings') . '">' . __('Settings', 'nanga') . '</a>'], $links);
    }

    public static function editor()
    {
        if (nanga_user_is_superadmin()) {
            return 'html';
        }

        return 'tinymce';
    }
}
