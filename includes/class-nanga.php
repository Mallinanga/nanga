<?php

class Nanga
{

    protected $loader;
    protected $nanga;
    protected $version;

    public function __construct()
    {
        $this->nanga   = 'nanga';
        $this->version = '1.2.1';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_cron();
        $this->define_public_hooks();
        $this->define_shared_hooks();
        $this->define_shortcodes();
        $this->define_third_party();
        //$this->define_updates();
    }

    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nanga-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nanga-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nanga-shared.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-nanga-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-nanga-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nanga-cache.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nanga-shortcodes.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nanga-cron.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nanga-updates.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nanga-third-party.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cpt/extended-cpts.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cpt/extended-taxos.php';
        $this->loader = new Nanga_Loader();
    }

    private function set_locale()
    {
        $plugin_i18n = new Nanga_i18n();
        $plugin_i18n->set_domain($this->get_nanga());
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks()
    {
        $pluginAdmin = new Nanga_Admin($this->get_nanga(), $this->get_version());
        $this->loader->add_action('admin_bar_menu', $pluginAdmin, 'admin_bar', 999);
        $this->loader->add_action('admin_enqueue_scripts', $pluginAdmin, 'enqueue_scripts');
        $this->loader->add_action('admin_enqueue_scripts', $pluginAdmin, 'enqueue_styles');
        $this->loader->add_action('admin_footer_text', $pluginAdmin, 'footer_left');
        //$this->loader->add_action('admin_head', $pluginAdmin, 'debug');
        $this->loader->add_action('admin_init', $pluginAdmin, 'admin_color_scheme');
        $this->loader->add_action('admin_init', $pluginAdmin, 'disable_admin_notices');
        $this->loader->add_action('admin_init', $pluginAdmin, 'disable_pointers');
        $this->loader->add_action('admin_init', $pluginAdmin, 'disable_postboxes');
        $this->loader->add_action('admin_init', $pluginAdmin, 'force_image_attributes');
        $this->loader->add_action('admin_init', $pluginAdmin, 'layout_columns');
        $this->loader->add_action('admin_menu', $pluginAdmin, 'all_options_page');
        $this->loader->add_action('admin_menu', $pluginAdmin, 'disable_menus', 999);
        $this->loader->add_action('admin_menu', $pluginAdmin, 'google_analytics_dashboard');
        $this->loader->add_action('admin_menu', $pluginAdmin, 'plugin_settings_menu');
        $this->loader->add_action('after_setup_theme', $pluginAdmin, 'add_editor_style');
        $this->loader->add_action('customize_preview_init', $pluginAdmin, 'customizer_scripts');
        $this->loader->add_action('customize_register', $pluginAdmin, 'customizer_register');
        $this->loader->add_action('login_enqueue_scripts', $pluginAdmin, 'enqueue_login_styles');
        $this->loader->add_action('login_head', $pluginAdmin, 'disable_shake');
        $this->loader->add_action('login_init', $pluginAdmin, 'dequeue_login_styles');
        $this->loader->add_action('manage_pages_custom_column', $pluginAdmin, 'featured_image_column', 5, 2);
        $this->loader->add_action('manage_posts_custom_column', $pluginAdmin, 'featured_image_column', 5, 2);
        $this->loader->add_action('plugins_loaded', $pluginAdmin, 'jigsaw');
        //$this->loader->add_action('plugins_loaded', $pluginAdmin, 'settings_page');
        $this->loader->add_action('tgmpa_register', $pluginAdmin, 'required_plugins');
        $this->loader->add_action('widgets_init', $pluginAdmin, 'disable_widgets', 1);
        $this->loader->add_action('wp_ajax_clear_debug_log', $pluginAdmin, 'clear_debug_log');
        $this->loader->add_action('wp_dashboard_setup', $pluginAdmin, 'disable_metaboxes');
        $this->loader->add_action('wp_dashboard_setup', $pluginAdmin, 'google_analytics_widget');
        $this->loader->add_action('wp_dashboard_setup', $pluginAdmin, 'support_request_widget');
        $this->loader->add_filter('acf/settings/show_admin', $pluginAdmin, 'acf_settings_show_admin');
        $this->loader->add_filter('jpeg_quality', $pluginAdmin, 'image_quality');
        $this->loader->add_filter('login_headertitle', $pluginAdmin, 'login_headertitle');
        $this->loader->add_filter('login_headerurl', $pluginAdmin, 'login_headerurl');
        $this->loader->add_filter('manage_media_columns', $pluginAdmin, 'columns_media');
        $this->loader->add_filter('manage_pages_columns', $pluginAdmin, 'columns_pages');
        $this->loader->add_filter('manage_plugins_columns', $pluginAdmin, 'columns_plugins');
        $this->loader->add_filter('manage_posts_columns', $pluginAdmin, 'columns_posts', 10, 2);
        $this->loader->add_filter('manage_users_columns', $pluginAdmin, 'columns_users', 999);
        $this->loader->add_filter('mce_buttons', $pluginAdmin, 'mce_buttons');
        $this->loader->add_filter('page_row_actions', $pluginAdmin, 'row_actions', 10, 2);
        $this->loader->add_filter('plugin_action_links_' . $this->nanga . '/' . $this->nanga . '.php', $pluginAdmin, 'plugin_action_links');
        $this->loader->add_filter('post_date_column_time', $pluginAdmin, 'post_date_column_time', 10, 2);
        $this->loader->add_filter('post_row_actions', $pluginAdmin, 'row_actions', 10, 2);
        $this->loader->add_filter('update_footer', $pluginAdmin, 'footer_right', 999);
        $this->loader->add_filter('upload_mimes', $pluginAdmin, 'mime_types');
        $this->loader->add_filter('user_contactmethods', $pluginAdmin, 'user_contact', 666);
        $this->loader->add_filter('wp_default_editor', $pluginAdmin, 'default_editor');
        $this->loader->add_filter('wp_editor_set_quality', $pluginAdmin, 'image_quality');
        //$this->loader->add_action( 'edit_attachment', $pluginAdmin, 'image_license_save' );
        //$this->loader->add_filter( 'admin_post_thumbnail_html', $pluginAdmin, 'admin_post_thumbnail_html' );
        //$this->loader->add_filter( 'attachment_fields_to_edit', $pluginAdmin, 'image_license_field', 10, 2 );
        //$this->loader->add_filter( 'locale', $pluginAdmin, 'force_dashboard_locale', 10 );
        //$this->loader->add_filter( 'posts_fields', $pluginAdmin, 'limit_post_fields', 0, 2 );
        //$this->loader->add_filter( 'screen_options_show_screen', $pluginAdmin, 'screen_options_show_screen' );
    }

    private function define_cron()
    {
        $plugin_cron = new Nanga_Cron($this->get_nanga(), $this->get_version());
        //$this->loader->add_action( 'nanga_monthly_schedule', $plugin_cron, 'maybe_purge_transients' );
        $this->loader->add_filter('cron_schedules', $plugin_cron, 'intervals');
    }

    private function define_public_hooks()
    {
        $pluginPublic = new Nanga_Public($this->get_nanga(), $this->get_version());
        //$this->loader->add_action('after_setup_theme', $pluginPublic, 'disable_adminbar');
        //$this->loader->add_action( 'init', $pluginPublic, 'random_post_rewrite' );
        //$this->loader->add_action('template_redirect', $pluginPublic, 'maintenance_mode');
        //$this->loader->add_action( 'template_redirect', $pluginPublic, 'nice_search' );
        //$this->loader->add_action( 'template_redirect', $pluginPublic, 'random_post_redirect', 666 );
        $this->loader->add_action('wp_enqueue_scripts', $pluginPublic, 'asset_cachebusting', 100);
        $this->loader->add_action('wp_enqueue_scripts', $pluginPublic, 'cookiesAssets');
        $this->loader->add_action('wp_enqueue_scripts', $pluginPublic, 'enqueue_scripts');
        $this->loader->add_action('wp_enqueue_scripts', $pluginPublic, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $pluginPublic, 'js_to_footer');
        $this->loader->add_action('wp_head', $pluginPublic, 'analytics');
        $this->loader->add_action('wp_footer', $pluginPublic, 'cookies');
        $this->loader->add_filter('body_class', $pluginPublic, 'body_class');
        $this->loader->add_filter('comment_form_default_fields', $pluginPublic, 'comment_form_default_fields');
        $this->loader->add_filter('comment_form_defaults', $pluginPublic, 'comment_form_defaults');
        $this->loader->add_filter('comment_id_fields', $pluginPublic, 'remove_self_closing_tags');
        $this->loader->add_filter('get_avatar', $pluginPublic, 'remove_self_closing_tags');
        $this->loader->add_filter('get_image_tag_class', $pluginPublic, 'attachment_class', 10, 4);
        $this->loader->add_filter('locale', $pluginPublic, 'change_locale_on_the_fly');
        $this->loader->add_filter('post_class', $pluginPublic, 'post_class', 10, 3);
        $this->loader->add_filter('post_thumbnail_html', $pluginPublic, 'remove_self_closing_tags');
        //$this->loader->add_filter('query_vars', $pluginPublic, 'random_post_query_var');
        $this->loader->add_filter('the_content', $pluginPublic, 'remove_paragraphs_from_images');
        $this->loader->add_filter('the_password_form', $pluginPublic, 'the_password_form');
        //$this->loader->add_action( 'wp_head', $pluginPublic, 'customizer_output' );
        //$this->loader->add_filter( 'bloginfo_url', $pluginPublic, 'relative_urls' );
        //$this->loader->add_filter( 'script_loader_src', $pluginPublic, 'relative_urls' );
        //$this->loader->add_filter( 'style_loader_src', $pluginPublic, 'relative_urls' );
        //$this->loader->add_filter( 'the_permalink', $pluginPublic, 'relative_urls' );
    }

    private function define_shared_hooks()
    {
        $pluginShared = new Nanga_Shared($this->get_nanga(), $this->get_version());
        $this->loader->add_action('after_setup_theme', $pluginShared, 'setup_theme');
        $this->loader->add_action('do_feed', $pluginShared, 'disable_feeds', 1);
        $this->loader->add_action('do_feed_atom', $pluginShared, 'disable_feeds', 1);
        $this->loader->add_action('do_feed_rdf', $pluginShared, 'disable_feeds', 1);
        $this->loader->add_action('do_feed_rss', $pluginShared, 'disable_feeds', 1);
        $this->loader->add_action('do_feed_rss2', $pluginShared, 'disable_feeds', 1);
        $this->loader->add_action('init', $pluginShared, 'disable_core_functionality', 10);
        $this->loader->add_action('init', $pluginShared, 'disable_post_types', 1);
        $this->loader->add_action('init', $pluginShared, 'disable_taxonomies', 1);
        //$this->loader->add_action('plugins_loaded', $pluginShared, 'acf_load_point');
        //$this->loader->add_action('plugins_loaded', $pluginShared, 'acf_save_point');
        //$this->loader->add_action('shutdown', $pluginShared, 'dump_queries', 999);
        $this->loader->add_action('xmlrpc_call', $pluginShared, 'xmlrpc_call');
        $this->loader->add_filter('bloginfo_url', $pluginShared, 'bloginfo_url', 10, 2);
        $this->loader->add_filter('heartbeat_settings', $pluginShared, 'heartbeat');
        $this->loader->add_filter('request', $pluginShared, 'empty_search');
        $this->loader->add_filter('rewrite_rules_array', $pluginShared, 'filter_rewrites');
        $this->loader->add_filter('robots_txt', $pluginShared, 'robots', 10, 2);
        $this->loader->add_filter('wp_headers', $pluginShared, 'wp_headers');
        //$this->loader->add_filter( 'wp_mail_from', $pluginShared, 'mail_from' );
        $this->loader->add_filter('wp_mail_from_name', $pluginShared, 'mail_from_name');
        $this->loader->add_filter('xmlrpc_methods', $pluginShared, 'xmlrpc_methods');
    }

    private function define_shortcodes()
    {
        $plugin_shortcodes = new Nanga_Shortcodes($this->get_nanga(), $this->get_version());
    }

    private function define_third_party()
    {
        $plugin_third_party = new NangaThirdParty($this->get_nanga(), $this->get_version());
        $this->loader->add_action('plugins_loaded', $plugin_third_party, 'features_akismet');
        $this->loader->add_action('plugins_loaded', $plugin_third_party, 'features_easy_digital_downloads');
        $this->loader->add_action('plugins_loaded', $plugin_third_party, 'features_gravity_forms');
        $this->loader->add_action('plugins_loaded', $plugin_third_party, 'features_jetpack');
        //$this->loader->add_action( 'plugins_loaded', $plugin_third_party, 'features_json_api' );
        $this->loader->add_action('plugins_loaded', $plugin_third_party, 'features_timber');
        $this->loader->add_action('plugins_loaded', $plugin_third_party, 'features_woocommerce');
        $this->loader->add_action('plugins_loaded', $plugin_third_party, 'features_wordpress_social_login');
        $this->loader->add_action('plugins_loaded', $plugin_third_party, 'features_wpml');
        $this->loader->add_action('plugins_loaded', $plugin_third_party, 'features_yoast_seo');
    }

    public function get_nanga()
    {
        return $this->nanga;
    }

    public function get_version()
    {
        return $this->version;
    }

    public function run()
    {
        $this->loader->run();
    }

    public function get_loader()
    {
        return $this->loader;
    }

    private function define_updates()
    {
        $plugin_updates = new Nanga_Updates($this->get_nanga(), $this->get_version());
        $this->loader->add_filter('plugins_api', $plugin_updates, 'inject_info', 10, 3);
        $this->loader->add_filter('pre_set_site_transient_update_plugins', $plugin_updates, 'inject_update');
        $this->loader->add_filter('upgrader_post_install', $plugin_updates, 'post_install', 10, 3);
    }
}
