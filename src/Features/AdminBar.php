<?php

namespace Nanga\Features;

use Nanga\Settings;

class AdminBar
{

    public static function init()
    {
        add_action('admin_init', [self::class, 'actions']);
        add_action('admin_notices', [self::class, 'notices']);
        add_filter('show_admin_bar', [self::class, 'disable']);
        add_action('admin_bar_menu', [self::class, 'superadminNodes'], 100);
        add_action('admin_bar_menu', [self::class, 'adminNodes'], 100);
        add_action('admin_bar_menu', [self::class, 'editorNodes'], 100);
        add_action('admin_bar_menu', [self::class, 'nodes'], 100);
        add_action('admin_enqueue_scripts', [self::class, 'assets'], 100);
        add_action('wp_enqueue_scripts', [self::class, 'assets'], 1000);
        // add_action('wp_footer', [self::class, 'toggle'], 1000);
        add_theme_support('admin-bar', ['callback' => '__return_false']);

        //add_action('template_redirect', '_wp_admin_bar_init', 0);
        //add_action('admin_init', '_wp_admin_bar_init');
        //add_action('before_signup_header', '_wp_admin_bar_init');
        //add_action('activate_header', '_wp_admin_bar_init');
        add_action('template_redirect', function () {
            //remove_action('wp_before_admin_bar_render', 'wp_customize_support_script', 11);
            remove_action('admin_bar_menu', 'wp_admin_bar_customize_menu', 40);
        });
        /*
        add_action('wp_before_admin_bar_render', function () {
            self::adminbarNode('NANGA');
            self::adminbarNode('NANGA SUB1', 'http://www.vgwebthings.com', 'NANGA');
            self::adminbarNode('NANGA SUB2', 'http://www.vgwebthings.com', 'NANGA');
        });
        */
    }

    public static function actions()
    {
        global $pagenow;
        if ('index.php' !== $pagenow) {
            return;
        }
        if (isset($_GET['action']) && 'nanga-tools__flush-page-cache' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'])) {
        }
        if (isset($_GET['action']) && 'nanga-tools__flush-object-cache' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'])) {
            wp_cache_flush();
        }
        if (isset($_GET['action']) && 'nanga-tools__delete-transients' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'])) {
            global $wpdb;
            $sql = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "_transient_%"';
            $wpdb->query($sql);
        }
        if (isset($_GET['action']) && 'nanga-tools__flush-rewrites' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'])) {
            flush_rewrite_rules();
        }
    }

    public static function notices()
    {
        if (isset($_GET['action']) && 'nanga-tools__flush-page-cache' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Page Cache has been successfully flushed.</p></div>';
        }
        if (isset($_GET['action']) && 'nanga-tools__flush-object-cache' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Object Cache has been successfully flushed.</p></div>';
        }
        if (isset($_GET['action']) && 'nanga-tools__delete-transients' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Transients have been successfully deleted.</p></div>';
        }
        if (isset($_GET['action']) && 'nanga-tools__flush-rewrites' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Rewrite rules have been successfully flushed.</p></div>';
        }
    }

    public static function disable($userPreference)
    {
        if ( ! current_user_can('manage_options')) {
            return false;
        }

        return $userPreference;
        // remove_action('template_redirect', '_wp_admin_bar_init', 0);
        // remove_action('wp_footer', 'wp_admin_bar_render', 1000);
        // remove_action('wp_head', 'wp_admin_bar_header', 10);
    }

    public static function assets()
    {
        if (is_admin_bar_showing() || nanga_user_is_superadmin()) {
            wp_enqueue_style('nanga-admin-bar', NANGA_DIR_URL . 'assets/css/nanga-admin-bar.css', ['dashicons'], NANGA_VERSION);
        }
        if (is_admin_bar_showing() && nanga_user_is_superadmin()) {
            wp_enqueue_script('nanga-admin-bar', NANGA_DIR_URL . 'assets/js/nanga-admin-bar.js', ['jquery'], NANGA_VERSION, true);
        }
    }

    public static function superadminNodes($wp_admin_bar)
    {
        if ( ! nanga_user_is_superadmin()) {
            return;
        }
    }

    public static function adminNodes($wp_admin_bar)
    {
        if ( ! current_user_can('manage_options')) {
            return;
        }
        $wp_admin_bar->add_menu([
            'id'    => 'nanga-settings',
            'title' => __('Settings', 'nanga'),
        ]);
        $tabs = Settings::tabs();
        foreach ($tabs as $tab) {
            if ( ! $tab['show']) {
                continue;
            }
            $wp_admin_bar->add_node([
                'href'   => admin_url('options-general.php?page=nanga-settings&tab=' . $tab['slug']),
                'id'     => 'nanga-settings__' . $tab['slug'],
                'parent' => 'nanga-settings',
                'title'  => $tab['title'],
            ]);
        }
        $wp_admin_bar->add_menu([
            'href'   => false,
            'id'     => 'nanga-tools',
            'parent' => 'top-secondary',
            'title'  => __('Tools', 'nanga'),
        ]);
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(add_query_arg('action', 'nanga-tools__flush-object-cache', admin_url('index.php'))),
            'id'     => 'nanga-tools__flush-object-cache',
            'parent' => 'nanga-tools',
            'title'  => 'Flush Object Cache',
        ]);
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(add_query_arg('action', 'nanga-tools__delete-transients', admin_url('index.php'))),
            'id'     => 'nanga-tools__delete-transients',
            'parent' => 'nanga-tools',
            'title'  => 'Delete Transients',
        ]);
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(add_query_arg('action', 'nanga-tools__flush-rewrites', admin_url('index.php'))),
            'id'     => 'nanga-tools__flush-rewrites',
            'parent' => 'nanga-tools',
            'title'  => 'Flush Rewrite Rules',
        ]);
    }

    public static function editorNodes($wp_admin_bar)
    {
        if ( ! current_user_can('edit_pages')) {
            return;
        }
    }

    public static function nodes($wp_admin_bar)
    {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
        remove_action('admin_bar_menu', 'wp_admin_bar_edit_menu', 80);
        remove_action('admin_bar_menu', 'wp_admin_bar_new_content_menu', 70);
        remove_action('admin_bar_menu', 'wp_admin_bar_search_menu', 4);
        remove_action('admin_bar_menu', 'wp_admin_bar_updates_menu', 50);
        remove_action('admin_bar_menu', 'wp_admin_bar_wp_menu', 10);
        $wp_admin_bar->remove_node('about');
        $wp_admin_bar->remove_node('appearance');
        $wp_admin_bar->remove_node('archive');
        $wp_admin_bar->remove_node('comments');
        $wp_admin_bar->remove_node('customize');
        $wp_admin_bar->remove_node('dashboard');
        $wp_admin_bar->remove_node('documentation');
        $wp_admin_bar->remove_node('edit');
        $wp_admin_bar->remove_node('feedback');
        $wp_admin_bar->remove_node('menus');
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('search');
        $wp_admin_bar->remove_node('support-forums');
        $wp_admin_bar->remove_node('themes');
        $wp_admin_bar->remove_node('updates');
        $wp_admin_bar->remove_node('view');
        $wp_admin_bar->remove_node('view-site');
        $wp_admin_bar->remove_node('view-store');
        $wp_admin_bar->remove_node('wp-logo');
        $wp_admin_bar->remove_node('wporg');
        $wp_admin_bar->remove_node('wpseo-menu');
        $wp_admin_bar->add_node([
            'id'    => 'my-account',
            'title' => false,
        ]);
    }

    public static function toggle()
    {
        if ( ! nanga_user_is_superadmin()) {
            return;
        }
        echo '<a href="' . admin_url('index.php') . '" id="nanga-bar-toggle" class="dashicons dashicons-wordpress-alt"></a>';
    }

    private static function add($name, $href = '', $parent = '', $custom_meta = [])
    {
        global $wp_admin_bar;
        if ( ! is_super_admin() || ! is_object($wp_admin_bar) || ! function_exists('is_admin_bar_showing') || ! is_admin_bar_showing()) {
            return;
        }
        $id     = sanitize_key(basename(__FILE__, '.php') . '-' . $name);
        $parent = sanitize_key(basename(__FILE__, '.php') . '-' . $parent);
        $meta   = strpos($href, site_url()) !== false ? [] : ['target' => '_blank'];
        $meta   = array_merge($meta, $custom_meta);
        $wp_admin_bar->add_node([
            'href'   => $href,
            'id'     => $id,
            'meta'   => $meta,
            'parent' => $parent,
            'title'  => $name,
        ]);
    }
}
