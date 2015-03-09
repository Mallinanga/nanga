<?php

class Nanga {
    protected $loader;
    protected $nanga;
    protected $version;

    public function __construct() {
        $this->nanga   = 'nanga';
        $this->version = '1.1.3';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_shared_hooks();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_shortcodes();
        $this->plugin_control();
    }

    private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-shared.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-nanga-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-nanga-public.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-cache.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-shortcodes.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-cron.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-plugin-control.php';
        $this->loader = new Nanga_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new Nanga_i18n();
        $plugin_i18n->set_domain( $this->get_nanga() );
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    public function get_nanga() {
        return $this->nanga;
    }

    private function define_shared_hooks() {
        $plugin_shared = new Nanga_Shared( $this->get_nanga(), $this->get_version() );
        $this->loader->add_action( 'do_feed', $plugin_shared, 'disable_feeds', 1 );
        $this->loader->add_action( 'do_feed_atom', $plugin_shared, 'disable_feeds', 1 );
        $this->loader->add_action( 'do_feed_rdf', $plugin_shared, 'disable_feeds', 1 );
        $this->loader->add_action( 'do_feed_rss', $plugin_shared, 'disable_feeds', 1 );
        $this->loader->add_action( 'do_feed_rss2', $plugin_shared, 'disable_feeds', 1 );
        $this->loader->add_action( 'init', $plugin_shared, 'disable_core_functionality', 10 );
        $this->loader->add_action( 'init', $plugin_shared, 'disable_post_types' );
        $this->loader->add_action( 'init', $plugin_shared, 'disable_taxonomies' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'acf_load_point' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'acf_save_point' );
        $this->loader->add_action( 'shutdown', $plugin_shared, 'dump_queries', 999 );
        $this->loader->add_filter( 'request', $plugin_shared, 'empty_search' );
        $this->loader->add_filter( 'rewrite_rules_array', $plugin_shared, 'filter_rewrites' );
        $this->loader->add_filter( 'wp_mail_from', $plugin_shared, 'mail_from' );
        $this->loader->add_filter( 'wp_mail_from_name', $plugin_shared, 'mail_from_name' );
        /* Robots */
        $this->loader->add_filter( 'robots_txt', $plugin_shared, 'robots', 10, 2 );
        /* Disable Trackbacks and Pingbacks */
        $this->loader->add_action( 'xmlrpc_call', $plugin_shared, 'xmlrpc_call' );
        $this->loader->add_filter( 'bloginfo_url', $plugin_shared, 'bloginfo_url', 10, 2 );
        $this->loader->add_filter( 'wp_headers', $plugin_shared, 'wp_headers' );
        $this->loader->add_filter( 'xmlrpc_methods', $plugin_shared, 'xmlrpc_methods' );
        /* Theme */
        $this->loader->add_action( 'after_setup_theme', $plugin_shared, 'setup_theme' );
        /* Third-party */
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'features_easy_digital_downloads' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'features_gravity_forms' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'features_jetpack' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'features_json_api' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'features_woocommerce' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'features_wordpress_social_login' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'features_wpml' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'features_yoast_seo' );
    }

    public function get_version() {
        return $this->version;
    }

    private function define_admin_hooks() {
        $plugin_admin = new Nanga_Admin( $this->get_nanga(), $this->get_version() );
        $this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'admin_bar', 999 );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_footer_text', $plugin_admin, 'footer_left' );
        $this->loader->add_action( 'admin_head', $plugin_admin, 'debug' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'admin_color_scheme' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'disable_admin_notices' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'disable_pointers' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'disable_postboxes' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'layout_columns' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'all_options_page' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'disable_menus', 999 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'plugin_settings_menu' );
        $this->loader->add_action( 'after_setup_theme', $plugin_admin, 'add_editor_style' );
        $this->loader->add_action( 'login_enqueue_scripts', $plugin_admin, 'enqueue_login_styles' );
        $this->loader->add_action( 'login_init', $plugin_admin, 'dequeue_login_styles' );
        $this->loader->add_action( 'plugins_loaded', $plugin_admin, 'jigsaw' );
        $this->loader->add_action( 'plugins_loaded', $plugin_admin, 'settings_page' );
        $this->loader->add_action( 'tgmpa_register', $plugin_admin, 'required_plugins' );
        $this->loader->add_action( 'widgets_init', $plugin_admin, 'disable_widgets', 1 );
        $this->loader->add_action( 'wp_ajax_clear_debug_log', $plugin_admin, 'clear_debug_log' );
        $this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'disable_metaboxes' );
        $this->loader->add_filter( 'acf/settings/show_admin', $plugin_admin, 'acf_settings_show_admin' );
        $this->loader->add_filter( 'jpeg_quality', $plugin_admin, 'image_quality' );
        $this->loader->add_filter( 'login_errors', $plugin_admin, 'login_errors' );
        $this->loader->add_filter( 'login_headertitle', $plugin_admin, 'login_headertitle' );
        $this->loader->add_filter( 'login_headerurl', $plugin_admin, 'login_headerurl' );
        $this->loader->add_filter( 'mce_buttons', $plugin_admin, 'mce_buttons' );
        $this->loader->add_filter( 'page_row_actions', $plugin_admin, 'row_actions', 10, 2 );
        $this->loader->add_filter( 'post_row_actions', $plugin_admin, 'row_actions', 10, 2 );
        /* Default editor depending on user */
        $this->loader->add_filter( 'wp_default_editor', $plugin_admin, 'default_editor' );
        /* Force image attributes */
        $this->loader->add_action( 'admin_init', $plugin_admin, 'force_image_attributes' );
        /* Image license */
        //$this->loader->add_action( 'edit_attachment', $plugin_admin, 'image_license_save' );
        //$this->loader->add_filter( 'attachment_fields_to_edit', $plugin_admin, 'image_license_field', 10, 2 );
        /* Disable shake from login screens */
        $this->loader->add_action( 'login_head', $plugin_admin, 'disable_shake' );
        //$this->loader->add_filter( 'posts_fields', $plugin_admin, 'limit_post_fields', 0, 2 );
        $this->loader->add_filter( 'update_footer', $plugin_admin, 'footer_right', 999 );
        $this->loader->add_filter( 'upload_mimes', $plugin_admin, 'mime_types' );
        $this->loader->add_filter( 'wp_editor_set_quality', $plugin_admin, 'image_quality' );
        /* Columns */
        $this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'manage_posts_custom_column', 5, 2 );
        $this->loader->add_filter( 'manage_media_columns', $plugin_admin, 'columns_media' );
        $this->loader->add_filter( 'manage_pages_columns', $plugin_admin, 'columns_pages' );
        $this->loader->add_filter( 'manage_plugins_columns', $plugin_admin, 'columns_plugins' );
        $this->loader->add_filter( 'manage_posts_columns', $plugin_admin, 'columns_posts', 10, 2 );
        $this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'columns_users', 999 );
        /* Support Request Widget */
        $this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'support_request_widget' );
        /* Google Analytics Dashboard & Widget */
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'google_analytics_dashboard' );
        $this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'google_analytics_widget' );
        /* Display future publish date in lists */
        $this->loader->add_filter( 'post_date_column_time', $plugin_admin, 'post_date_column_time', 10, 2 );
        /* Show message in Featured Image */
        $this->loader->add_filter( 'admin_post_thumbnail_html', $plugin_admin, 'admin_post_thumbnail_html' );
        /* Customizer */
        $this->loader->add_action( 'customize_preview_init', $plugin_admin, 'customizer_scripts' );
        $this->loader->add_action( 'customize_register', $plugin_admin, 'customizer_register' );
        /* Plugin links */
        $this->loader->add_filter( 'plugin_action_links_' . $this->nanga . '/' . $this->nanga . '.php', $plugin_admin, 'plugin_action_links' );
        //$this->loader->add_action( 'login_enqueue_scripts', $plugin_admin, 'enqueue_password_hash' );
        //$this->loader->add_filter( 'locale', $plugin_admin, 'force_dashboard_locale', 10 );
        //$this->loader->add_filter( 'screen_options_show_screen', $plugin_admin, 'screen_options_show_screen' );
    }

    private function define_public_hooks() {
        $plugin_public = new Nanga_Public( $this->get_nanga(), $this->get_version() );
        $this->loader->add_action( 'template_redirect', $plugin_public, 'nice_search' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'asset_cachebusting', 100 );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_head', $plugin_public, 'analytics' );
        $this->loader->add_filter( 'the_password_form', $plugin_public, 'the_password_form' );
        /* Change locale on the fly */
        //@todo
        //$this->loader->add_filter( 'locale', $plugin_public, 'change_locale_on_the_fly' );
        /* Remove paragraphs from images in content */
        $this->loader->add_filter( 'the_content', $plugin_public, 'remove_paragraphs_from_images' );
        /* Random post */
        $this->loader->add_action( 'init', $plugin_public, 'random_post_rewrite' );
        $this->loader->add_action( 'template_redirect', $plugin_public, 'random_post_redirect', 666 );
        $this->loader->add_filter( 'query_vars', $plugin_public, 'random_post_query_var' );
        /* Body, Post and Attachment classes */
        $this->loader->add_filter( 'body_class', $plugin_public, 'body_class' );
        $this->loader->add_filter( 'get_image_tag_class', $plugin_public, 'attachment_class', 10, 4 );
        $this->loader->add_filter( 'post_class', $plugin_public, 'post_class', 10, 3 );
        /* Disable adminbar */
        $this->loader->add_action( 'after_setup_theme', $plugin_public, 'disable_adminbar' );
        /* Move all scripts to footer */
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'js_to_footer' );
        /* Comment Form */
        $this->loader->add_filter( 'comment_form_default_fields', $plugin_public, 'comment_form_default_fields' );
        $this->loader->add_filter( 'comment_form_defaults', $plugin_public, 'comment_form_defaults' );
        /* Relative URLs */
        //$this->loader->add_filter( 'script_loader_src', $plugin_public, 'relative_urls' );
        //$this->loader->add_filter( 'style_loader_src', $plugin_public, 'relative_urls' );
        /* Customizer Output */
        //$this->loader->add_action( 'wp_head', $plugin_public, 'customizer_output' );
        //$this->loader->add_filter( 'comment_id_fields', $plugin_public, 'remove_self_closing_tags' );
        //$this->loader->add_filter( 'get_avatar', $plugin_public, 'remove_self_closing_tags' );
        //$this->loader->add_filter( 'post_thumbnail_html', $plugin_public, 'remove_self_closing_tags' );
    }

    private function define_shortcodes() {
        $plugin_shortcodes = new Nanga_Shortcodes( $this->get_nanga(), $this->get_version() );
    }

    private function plugin_control() {
        //$plugin_control = new Nanga_Plugin_Control( array( 'debug-bar-timber/debug-bar-timber.php' ) );
        $plugin_control = new Nanga_Plugin_Control();
        if ( defined( 'WP_ENV' ) && 'development' === WP_ENV ) {
            $plugin_control->disable( 'google-analytics-for-wordpress/googleanalytics.php' );
            $plugin_control->disable( 'w3-total-cache/w3-total-cache.php' );
        }
        if ( defined( 'WP_ENV' ) && 'development' !== WP_ENV ) {
            $plugin_control->disable( 'debug-bar-timber/debug-bar-timber.php' );
            $plugin_control->disable( 'debug-bar/debug-bar.php' );
            $plugin_control->disable( 'underconstruction/underConstruction.php' );
        }
    }

    public function run() {
        $this->loader->run();
    }

    public function get_loader() {
        return $this->loader;
    }
}
