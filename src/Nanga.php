<?php

namespace Nanga;

use Nanga\Features\AdminBar;
use Nanga\Features\API;
use Nanga\Features\Comments;
use Nanga\Features\Cron;
use Nanga\Features\Customizer;
use Nanga\Features\Dashboard;
use Nanga\Features\Debug;
use Nanga\Features\Frontend;
use Nanga\Features\Heartbeat;
use Nanga\Features\Login;
use Nanga\Features\Mail;
use Nanga\Features\Maintenance;
use Nanga\Features\Media;
use Nanga\Features\Pages;
use Nanga\Features\Posts;
use Nanga\Features\Rewrites;
use Nanga\Features\Shortcodes;
use Nanga\Features\WordPress;
use Nanga\ThirdParty\ACF;
use Nanga\ThirdParty\Akismet;
use Nanga\ThirdParty\CacheEnabler;
use Nanga\ThirdParty\EasyDigitalDownloads;
use Nanga\ThirdParty\GravityForms;
use Nanga\ThirdParty\Jetpack;
use Nanga\ThirdParty\Optimus;
use Nanga\ThirdParty\Timber;
use Nanga\ThirdParty\UserRoleEditor;
use Nanga\ThirdParty\WooCommerce;
use Nanga\ThirdParty\WordPressSocialLogin;
use Nanga\ThirdParty\WPHelp;
use Nanga\ThirdParty\WPMigrateDB;
use Nanga\ThirdParty\WPML;
use Nanga\ThirdParty\WPRedis;
use Nanga\ThirdParty\Yoast;

class Nanga
{

    private function __construct()
    {
        if (defined('WP_CLI') && WP_CLI) {
            $this->commands();
        }
        if (nanga_site_in_development()) {
            Debug::init();
        }
        Maintenance::init();
        API::init();
        Rewrites::init();
        Cron::init();
        Mail::init();
        WordPress::actions();
        WordPress::filters();
        WordPress::init();
        AdminBar::init();
        // Frontend::init();
        Login::init();
        Dashboard::init();
        Heartbeat::init();
        Media::init();
        Pages::init();
        // Posts::init();
        Comments::init();
        // Shortcodes::init();
        Customizer::init();
        add_action('plugins_loaded', [$this, 'locale']);
        add_action('tgmpa_register', [$this, 'plugins']);
        add_action('plugins_loaded', [$this, 'thirdparty'], 11);
        add_action('after_setup_theme', [$this, 'supports']);
    }

    private function commands()
    {
        // \WP_CLI::add_command('nanga', '\Nanga\CLI\Nanga');
        // \WP_CLI::add_command('nanga-airplane-mode', '\Nanga\CLI\AirplaneMode');
    }

    public static function activate()
    {
        if ( ! wp_next_scheduled('nanga_hourly_schedule')) {
            wp_schedule_event(time(), 'hourly', 'nanga_hourly_schedule');
        }
        if ( ! wp_next_scheduled('nanga_twicedaily_schedule')) {
            wp_schedule_event(time(), 'twicedaily', 'nanga_twicedaily_schedule');
        }
        if ( ! wp_next_scheduled('nanga_daily_schedule')) {
            wp_schedule_event(time(), 'daily', 'nanga_daily_schedule');
        }
        if ( ! wp_next_scheduled('nanga_weekly_schedule')) {
            wp_schedule_event(time(), 'weekly', 'nanga_weekly_schedule');
        }
        if ( ! wp_next_scheduled('nanga_monthly_schedule')) {
            wp_schedule_event(time(), 'monthly', 'nanga_monthly_schedule');
        }
        if (get_option('nanga_plugin_activated')) {
            return;
        }
        $wpseo                                    = get_option('wpseo');
        $wpseo_rss                                = get_option('wpseo_rss');
        $wpseo_social                             = get_option('wpseo_social');
        $wpseo_titles                             = get_option('wpseo_titles');
        $wpseo['company_or_person']               = 'company';
        $wpseo['yandexverify']                    = false;
        $wpseo_rss['rssafter']                    = '';
        $wpseo_rss['rssbefore']                   = '';
        $wpseo_social['twitter']                  = false;
        $wpseo_titles['disable-author']           = true;
        $wpseo_titles['disable-date']             = true;
        $wpseo_titles['hideeditbox-attachment']   = true;
        $wpseo_titles['hideeditbox-tax-category'] = true;
        $wpseo_titles['hideeditbox-tax-post_tag'] = true;
        $wpseo_titles['noindex-archive-wpseo']    = true;
        $wpseo_titles['noindex-attachment']       = true;
        $wpseo_titles['noindex-author-wpseo']     = true;
        $wpseo_titles['noindex-subpages-wpseo']   = true;
        $wpseo_titles['noindex-tax-category']     = true;
        $wpseo_titles['noindex-tax-post_tag']     = true;
        delete_option('sidebars_widgets');
        update_option('avatar_default', 'blank');
        update_option('blog_public', 0);
        update_option('blogdescription', '');
        update_option('cache-enabler', [
            'expires'     => 0,
            'new_post'    => 0,
            'new_comment' => 0,
            'compress'    => 1,
            'webp'        => 1,
            'excl_ids'    => '',
            'minify_html' => 2,
        ]);
        update_option('comment_max_links', 1);
        update_option('comment_whitelist', 0);
        update_option('comments_notify', 0);
        update_option('date_format', 'd/m/Y');
        update_option('default_comment_status', 'closed');
        update_option('default_ping_status', 'closed');
        update_option('default_pingback_flag', 0);
        update_option('gform_enable_noconflict', 0);
        update_option('gzipcompression', 1);
        update_option('image_default_align', 'none');
        update_option('image_default_link_type', 'none');
        update_option('imsanity_bmp_to_jpg', 1);
        update_option('imsanity_max_height', 1200);
        update_option('imsanity_max_height_library', 1200);
        update_option('imsanity_max_height_other', 0);
        update_option('imsanity_max_width', 1200);
        update_option('imsanity_max_width_library', 1200);
        update_option('imsanity_max_width_other', 0);
        update_option('imsanity_quality', 100);
        update_option('large_crop', 1);
        update_option('large_size_h', 0);
        update_option('large_size_w', 0);
        update_option('mailserver_login', '');
        update_option('mailserver_pass', '');
        update_option('mailserver_port', 0);
        update_option('mailserver_url', '');
        update_option('medium_crop', 1);
        update_option('medium_large_size_h', 0);
        update_option('medium_large_size_w', 0);
        update_option('medium_size_h', 150);
        update_option('medium_size_h', 150);
        update_option('medium_size_w', 150);
        update_option('medium_size_w', 150);
        update_option('moderation_notify', 0);
        update_option('posts_per_page', 5);
        update_option('posts_per_rss', 1);
        update_option('require_name_email', 0);
        update_option('rg_gforms_currency', 'EUR');
        update_option('rg_gforms_disable_css', 1);
        update_option('rg_gforms_enable_akismet', 1);
        update_option('rg_gforms_enable_html5', 1);
        update_option('rss_use_excerpt', 1);
        update_option('show_avatars', 0);
        update_option('show_on_front', 'page');
        update_option('thread_comments', 0);
        update_option('thumbnail_size_h', 150);
        update_option('thumbnail_size_w', 150);
        update_option('time_format', 'H:i');
        update_option('timezone_string', 'Europe/Athens');
        update_option('use_smilies', 0);
        if (defined('OPTIMUS_KEY')) {
            update_site_option('optimus_key', OPTIMUS_KEY);
        }
        /*
        if (defined('WP_HELP_REMOTE')) {
            update_option('cws_wp_help', [
                'slurp_url' => WP_HELP_REMOTE,
            ]);
        }
        */
        update_option('nanga_plugin_activated', true);
        flush_rewrite_rules();
    }

    public static function deactivate()
    {
        if (wp_next_scheduled('nanga_hourly_schedule')) {
            wp_clear_scheduled_hook('nanga_hourly_schedule');
        }
        if (wp_next_scheduled('nanga_twicedaily_schedule')) {
            wp_clear_scheduled_hook('nanga_twicedaily_schedule');
        }
        if (wp_next_scheduled('nanga_daily_schedule')) {
            wp_clear_scheduled_hook('nanga_daily_schedule');
        }
        if (wp_next_scheduled('nanga_weekly_schedule')) {
            wp_clear_scheduled_hook('nanga_weekly_schedule');
        }
        if (wp_next_scheduled('nanga_monthly_schedule')) {
            wp_clear_scheduled_hook('nanga_monthly_schedule');
        }
        update_option('blog_public', 1);
        flush_rewrite_rules();
    }

    public static function uninstall()
    {
        delete_option('rsa_options');
        update_option('blog_public', 1);
        flush_rewrite_rules();
    }

    public static function instance()
    {
        static $instance = false;
        if ($instance === false) {
            $instance = new static();
        }

        return $instance;
    }

    public function locale()
    {
        load_plugin_textdomain('nanga', false, '/nanga/languages/');
    }

    public function plugins()
    {
        $plugins = [];
        $sanity  = current_theme_supports('nanga-image-sanity');
        if ('vg-twig' == get_option('template')) {
            $plugins[] = [
                'name'             => 'Timber',
                'slug'             => 'timber-library',
                'required'         => true,
                'force_activation' => true,
            ];
        }
        $plugins[] = [
            'name'             => 'Image Sanity',
            'slug'             => 'imsanity',
            'required'         => $sanity,
            'force_activation' => $sanity,
        ];
        $plugins[] = [
            'name'             => 'Optimus',
            'slug'             => 'optimus',
            'required'         => true,
            'force_activation' => false,
        ];
        $plugins[] = [
            'name'             => 'Cache Enabler',
            'slug'             => 'cache-enabler',
            'required'         => false,
            'force_activation' => false,
        ];
        $plugins[] = [
            'name'             => 'WP Migrate DB',
            'slug'             => 'wp-migrate-db',
            'required'         => false,
            'force_activation' => false,
        ];
        if ( ! nanga_site_is_external()) {
            $plugins[] = [
                'name'             => 'VG web things Contact Form',
                'slug'             => 'nanga-contact',
                'source'           => 'https://github.com/Mallinanga/nanga-contact/archive/master.zip',
                'required'         => false,
                'force_activation' => false,
            ];
            $plugins[] = [
                'name'             => 'VG web things Newsletter Form',
                'slug'             => 'nanga-newsletter',
                'source'           => 'https://github.com/Mallinanga/nanga-newsletter/archive/master.zip',
                'required'         => false,
                'force_activation' => false,
            ];
            $plugins[] = [
                'name'             => 'VG web things Notifications',
                'slug'             => 'nanga-notifications',
                'source'           => 'https://github.com/Mallinanga/nanga-notifications/archive/master.zip',
                'required'         => false,
                'force_activation' => false,
            ];
            $plugins[] = [
                'name'             => 'Advanced Custom Fields Pro',
                'slug'             => 'advanced-custom-fields-pro',
                'source'           => 'https://s3-eu-west-1.amazonaws.com/www.vgwebthings.com/advanced-custom-fields-pro.zip',
                'required'         => true,
                'force_activation' => false,
                'external_url'     => 'http://www.advancedcustomfields.com/pro/',
            ];
            $plugins[] = [
                'name'             => 'WP Help',
                'slug'             => 'wp-help',
                'required'         => false,
                'force_activation' => false,
            ];
            $plugins[] = [
                'name'             => 'Gravity Forms',
                'slug'             => 'gravityforms',
                'source'           => 'https://github.com/wp-premium/gravityforms/archive/master.zip',
                'required'         => false,
                'force_activation' => false,
            ];
            $plugins[] = [
                'name'             => 'WP All Import',
                'slug'             => 'wp-all-import-pro',
                'source'           => 'https://s3-eu-west-1.amazonaws.com/www.vgwebthings.com/wp-all-import-pro.zip',
                'required'         => false,
                'force_activation' => false,
                'external_url'     => 'http://www.wpallimport.com/',
            ];
            $plugins[] = [
                'name'             => 'WP All Export',
                'slug'             => 'wp-all-export-pro',
                'source'           => 'https://s3-eu-west-1.amazonaws.com/www.vgwebthings.com/wp-all-export-pro.zip',
                'required'         => false,
                'force_activation' => false,
                'external_url'     => 'http://www.wpallimport.com/export/',
            ];
            $plugins[] = [
                'name'             => 'WPML',
                'slug'             => 'sitepress-multilingual-cms',
                'source'           => 'https://s3-eu-west-1.amazonaws.com/www.vgwebthings.com/sitepress-multilingual-cms.3.7.0.zip',
                'required'         => false,
                'force_activation' => false,
            ];
            /*
            $plugins[] = [
                'name'             => 'WP Sync DB',
                'slug'             => 'wp-sync-db',
                'source'           => 'https://s3-eu-west-1.amazonaws.com/www.vgwebthings.com/wp-sync-db.zip',
                'required'         => false,
                'force_activation' => false,
            ];
            $plugins[] = [
                'name'             => 'WP Sync DB Media Files',
                'slug'             => 'wp-sync-db-media-files',
                'source'           => 'https://s3-eu-west-1.amazonaws.com/www.vgwebthings.com/wp-sync-db-media-files.zip',
                'required'         => false,
                'force_activation' => false,
            ];
            $plugins[] = [
                'name'             => 'WP Sync DB CLI',
                'slug'             => 'wp-sync-db-cli',
                'source'           => 'https://s3-eu-west-1.amazonaws.com/www.vgwebthings.com/wp-sync-db-cli.zip',
                'required'         => false,
                'force_activation' => false,
            ];
            */
        }
        if (nanga_site_in_production() && ! nanga_site_is_external()) {
            $plugins[] = [
                'name'             => 'WP Redis',
                'slug'             => 'wp-redis',
                'required'         => false,
                'force_activation' => false,
            ];
            $plugins[] = [
                'name'             => 'Akismet',
                'slug'             => 'akismet',
                'required'         => true,
                'force_activation' => false,
            ];
            $plugins[] = [
                'name'             => 'Jetpack',
                'slug'             => 'jetpack',
                'required'         => true,
                'force_activation' => false,
            ];
        }
        $plugins = apply_filters('nanga_plugins', $plugins);
        $config  = [
            'capability'   => 'manage_options',
            'dismissable'  => true,
            'has_notices'  => nanga_user_is_superadmin(),
            'is_automatic' => true,
            'menu'         => 'nanga-extend',
            'parent_slug'  => 'options-general.php',
            'strings'      => [
                'page_title' => __('Recommended & Required Plugins', 'nanga'),
                'menu_title' => __('VG Extend', 'nanga'),
            ],
        ];
        tgmpa($plugins, $config);
    }

    public function thirdparty()
    {
        /*
        if ( ! function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugins = get_option('active_plugins');
        foreach ($plugins as $file) {
            $plugin = get_plugin_data(WP_PLUGIN_DIR . '/' . $file);
            //$class  = str_replace(' ', '', ucwords($plugin['Name'], ' '));
            $class  = str_replace(' ', '', $plugin['Name']);
            if ( ! class_exists($class)) {
                continue;
            }
        }
        */
        ACF::init();
        Akismet::init();
        CacheEnabler::init();
        EasyDigitalDownloads::init();
        GravityForms::init();
        Jetpack::init();
        Timber::init();
        Optimus::init();
        UserRoleEditor::init();
        WooCommerce::init();
        WordPressSocialLogin::init();
        WPHelp::init();
        WPMigrateDB::init();
        WPML::init();
        WPRedis::init();
        Yoast::init();
    }

    public function supports()
    {
        global $_wp_theme_features;
        $supports = $_wp_theme_features;
        foreach ($supports as $support => $enabled) {
            if (0 !== strpos($support, 'nanga-') || ! $enabled) {
                continue;
            }
            $support = str_replace('Nanga', '', str_replace('-', '', ucwords($support, '-')));
            $class   = __NAMESPACE__ . '\\Supports\\' . $support;
            if ( ! class_exists($class)) {
                continue;
            }
            $class::init();
        }
    }

    private function __clone()
    {
    }

    private function __sleep()
    {
    }

    private function __wakeup()
    {
    }
}
