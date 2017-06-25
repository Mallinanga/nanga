<?php

namespace Nanga\Features;

class Debug
{

    public static function init()
    {
        // add_action('admin_bar_menu', [self::class, 'nodes'], 1000);
        // add_action('admin_footer', [self::class, 'debug'], 1000);
        // add_action('wp_footer', [self::class, 'debug'], 1000);
        // add_action('admin_enqueue_scripts', [self::class, 'assets'], 1000);
        // add_action('wp_enqueue_scripts', [self::class, 'assets'], 1000);
        // add_action('nanga_debug_footer', [self::class, 'moduleCache']);
        // add_action('nanga_debug_footer', [self::class, 'moduleFilters']);
        // add_action('nanga_debug_footer', [self::class, 'moduleGlobals']);
        // add_action('nanga_debug_footer', [self::class, 'moduleOptions']);
        // add_action('nanga_debug_footer', [self::class, 'moduleQueries']);
        // add_action('nanga_debug_footer', [self::class, 'moduleRequest']);
        // add_action('wp_ajax_clear_debug_log', [self::class, 'clearLog']);
    }

    public static function nodes($wp_admin_bar)
    {
        if ( ! nanga_user_is_superadmin()) {
            return;
        }
        $wp_admin_bar->add_menu([
            'id'     => 'nanga-debug',
            'parent' => 'top-secondary',
            'title'  => 'Debug',
        ]);
        $wp_admin_bar->add_node([
            'href'   => '#nanga-object-cache-stats',
            'id'     => 'nanga-object-cache-stats-link',
            'meta'   => ['class' => 'nanga-debug-link'],
            'parent' => 'nanga-debug',
            'title'  => 'Object Cache Stats',
        ]);
        $wp_admin_bar->add_node([
            'href'   => '#nanga-debug-log',
            'id'     => 'nanga-debug-log-link',
            'meta'   => ['class' => 'nanga-debug-link'],
            'parent' => 'nanga-debug',
            'title'  => 'Debug Log',
        ]);
    }

    public static function assets()
    {
        wp_enqueue_style('nanga-debug', NANGA_DIR_URL . 'assets/css/nanga-debug.css', ['dashicons'], NANGA_VERSION);
        wp_enqueue_script('nanga-debug', NANGA_DIR_URL . 'assets/js/nanga-debug.js', ['jquery'], NANGA_VERSION, true);
    }

    public static function debug()
    {
        if (is_admin_bar_showing()) {
            ?>
            <div id="nanga-debug-footer">
                <div class="nd-toolbar">
                    <a href="#!">Clear Log</a>
                    <a href="#!">Download Log</a>
                </div>
                <div class="nd-inner">
                    <?php do_action('nanga_debug_footer'); ?>
                    <div class="nanga-debug-module"><textarea id="nanga-debug-log" readonly rows="20">lorem</textarea></div>
                </div>
            </div>
            <?php
        }
    }

    public static function moduleCache()
    {
        echo '<div class="nanga-debug-module">';
        $GLOBALS['wp_object_cache']->stats();
        echo '</div>';
    }

    public static function moduleFilters()
    {
        echo '<div class="nanga-debug-module">';
        add_action('init', function () {
            echo '<pre>';
            print_r($GLOBALS['wp_filter']['init']);
            echo '</pre>';
        }, 1000);
        echo '</div>';
    }

    public static function moduleGlobals()
    {
        echo '<div class="nanga-debug-module">';
        echo '<pre>';
        print_r(apache_request_headers());
        print_r(apache_response_headers());
        print_r($GLOBALS);
        print_r($_GET);
        print_r($_POST);
        print_r($_REQUEST);
        echo '</pre>';
        echo '</div>';
    }

    public static function moduleOptions()
    {
        echo '<div class="nanga-debug-module">';
        global $wpdb;
        $results = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE autoload='yes'");
        $options = [];
        foreach ($results as $result) {
            $options[] = $result->option_name;
        }
        sort($options);
        echo '<pre>';
        print_r($options);
        echo '</pre>';
        echo '</div>';
    }

    public static function moduleQueries()
    {
        global $wpdb;
        echo '<div class="nanga-debug-module">';
        echo '<pre>';
        print_r($wpdb->queries);
        echo '</pre>';
        echo '</div>';
    }

    public static function moduleRequest()
    {
        add_action('parse_request', function ($wp) {
            echo '<div class="nanga-debug-module">';
            echo '<pre>';
            print_r($wp);
            echo '</pre>';
            echo '</div>';
        });
    }

    public static function clearLog()
    {
        $log = fopen(WP_CONTENT_DIR . '/debug.log', 'w');
        fclose($log);
        wp_die();
    }
}
