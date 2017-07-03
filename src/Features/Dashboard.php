<?php

namespace Nanga\Features;

class Dashboard
{

    public static function init()
    {
        // add_action('admin_init', [self::class, 'layout']);
        // add_action('admin_init', [self::class, 'postboxes']);
        // add_filter('get_user_option_screen_layout_attachment', [self::class, '_one']);
        // add_filter('get_user_option_screen_layout_dashboard', [self::class, '_one']);
        add_filter('get_user_option_screen_layout', [self::class, '_two']);
        add_action('admin_enqueue_scripts', [self::class, 'assets'], 100);
        add_action('admin_footer_text', '__return_empty_string');
        add_action('admin_head', [self::class, 'help']);
        add_action('admin_head', [self::class, 'opacity'], 100);
        add_action('admin_init', [self::class, 'footer']);
        add_action('admin_init', [self::class, 'scheme']);
        add_action('admin_init', [self::class, 'notices']);
        add_action('admin_menu', [self::class, 'menu'], 999);
        add_action('after_plugin_row_nanga/nanga.php', [self::class, 'warning'], 10, 3);
        add_action('personal_options', [self::class, 'profile']);
        add_action('wp_dashboard_setup', [self::class, 'metaboxes']);
        add_filter('get_user_option_admin_color', [self::class, 'colors']);
        add_filter('manage_users_columns', [self::class, 'columnsUsers'], 100);
        add_filter('plugin_action_links_nanga/nanga.php', [self::class, 'pluginLinks']);
        add_filter('update_footer', '__return_empty_string', 999);
        add_filter('wp_default_editor', [self::class, 'editor']);
        remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
        remove_action('admin_init', 'default_password_nag_handler', 10);
        remove_action('admin_init', 'register_admin_color_schemes', 1);
        remove_action('admin_menu', '_add_post_type_submenus');
        remove_action('welcome_panel', 'wp_welcome_panel');
    }

    public static function footer()
    {
        remove_filter('update_footer', 'core_update_footer');
    }

    public static function notices()
    {
        remove_action('admin_notices', 'default_password_nag', 10);
        remove_action('admin_notices', 'maintenance_nag', 10);
        remove_action('admin_notices', 'update_nag', 3);
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
        wp_enqueue_script('nanga-admin', NANGA_DIR_URL . 'assets/js/nanga-admin.js', ['jquery'], NANGA_VERSION, true);
        wp_localize_script('nanga-admin', 'nanga', [
            'current_user' => get_current_user_id(),
            'environment'  => (defined('WP_ENV')) ? WP_ENV : null,
            'locale'       => get_locale(),
        ]);
    }

    public static function menu()
    {
        remove_submenu_page('edit.php', 'post-new.php');
        remove_submenu_page('edit.php?post_type=page', 'post-new.php?post_type=page');
        remove_submenu_page('index.php', 'update-core.php');
        remove_submenu_page('plugins.php', 'plugin-install.php');
        remove_submenu_page('upload.php', 'media-new.php');
        remove_submenu_page('users.php', 'user-new.php');
        if ( ! current_user_can('manage_options')) {
            remove_menu_page('tools.php');
            remove_submenu_page('themes.php', 'themes.php');
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

    public static function columnsUsers($columns)
    {
        unset($columns['posts']);
        unset($columns['role']);
        unset($columns['ure_roles']);

        return $columns;
    }

    public static function profile()
    {
        ?>
        <script>
            (function ($) {
                $(function () {
                    var h2 = $('#your-profile').find('h2');
                    h2.each(function () {
                        if ($(this).text() === 'About Yourself' || $(this).text() === 'Λίγα λόγια για εσάς') {
                            $(this).next().remove();
                            $(this).remove();
                        }
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

    public static function pluginLinks($links)
    {
        return array_merge(['advanced_settings' => '<a href="' . admin_url('options-general.php?page=nanga-settings') . '">' . __('Settings', 'nanga') . '</a>'], $links);
    }

    public static function warning($file, $data, $status)
    {
        if (version_compare($data['Version'], '2.0.0', '<')) {
            echo '</tr><tr class="plugin-update-tr active"><td colspan="5" class="plugin-update" style="box-shadow:none;"><div class="update-message notice inline notice-error notice-alt" style="margin-top:15px;"><p>Versions above <em>2.0.0</em> include major changes. Please make sure you understand all the implications before upgrading this plugin.</p></div></td>';
        }
    }

    public static function editor()
    {
        if (nanga_user_is_superadmin()) {
            return 'html';
        }

        return 'tinymce';
    }

    public static function _one()
    {
        return 1;
    }

    public static function _two()
    {
        return 2;
    }
}
