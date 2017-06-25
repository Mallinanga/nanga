<?php

namespace Nanga\Features;

class Maintenance
{

    public static function init()
    {
        add_action('admin_init', [self::class, 'toggle'], 1);
        add_action('admin_notices', [self::class, 'notices']);
        if ( ! file_exists(ABSPATH . '.nanga-maintenance')) {
            return;
        }
        add_action('admin_init', [self::class, 'redirect'], 0);
        add_action('login_init', [self::class, 'redirect'], 0);
        add_action('template_redirect', [self::class, 'redirect'], 0);
    }

    public static function redirect()
    {
        if ( ! current_user_can('manage_options')) {
            header('HTTP/1.1 503 Service Unavailable', true, 503);
            require_once NANGA_DIR_PATH . 'views/maintenance.php';
            die();
        }
    }

    public static function toggle()
    {
        if (isset($_GET['action']) && 'nanga-enable-maintenance-mode' === $_GET['action'] && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'])) {
            self::enable();
        }
        if (isset($_GET['action']) && 'nanga-disable-maintenance-mode' === $_GET['action'] && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'])) {
            self::disable();
        }
    }

    public static function enable()
    {
        touch(ABSPATH . '.nanga-maintenance');
    }

    public static function disable()
    {
        unlink(ABSPATH . '.nanga-maintenance');
    }

    public static function notices()
    {
        if (file_exists(ABSPATH . '.nanga-maintenance')) {
            echo '<div class="notice notice-warning"><p>The site is in <strong>maintenance mode</strong>. Only administrators can access it. Once you are done you can disable it <a href="' . admin_url('options-general.php?page=nanga-settings&tab=maintenance') . '">here.</a></p></div>';
        }
        if (isset($_GET['action']) && 'nanga-disable-maintenance-mode' === $_GET['action'] && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Maintenance mode has been successfully disabled.</p></div>';
        }
    }
}
