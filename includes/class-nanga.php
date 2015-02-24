<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://github.com/Mallinanga
 * @since      1.0.0
 *
 * @package    Nanga
 * @subpackage Nanga/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Nanga
 * @subpackage Nanga/includes
 * @author     Panos Paganis <mallinanga@gmail.com>
 */
class Nanga {
    protected $loader;
    protected $nanga;
    protected $version;

    public function __construct() {
        $this->nanga   = 'nanga';
        $this->version = '1.0.0';
        $this->load_dependencies();
        $this->set_locale();
        $this->plugin_control();
        $this->define_shared_hooks();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_shortcodes();
    }

    /**
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-nanga-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-nanga-public.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-shared.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-cache.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-shortcodes.php';
        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nanga-plugin-control.php';
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

    private function plugin_control() {
        return;
    }

    private function define_shared_hooks() {
        $plugin_shared = new Nanga_Shared( $this->get_nanga(), $this->get_version() );
        $this->loader->add_action( 'init', $plugin_shared, 'disable_core_functionality', 10 );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'feautures_easy_digital_downloads' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'feautures_gravity_forms' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'feautures_jetpack' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'feautures_woocommerce' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'feautures_wordpress_social_login' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'feautures_wpml' );
        $this->loader->add_action( 'plugins_loaded', $plugin_shared, 'feautures_yoast_seo' );
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
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'all_options_page' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'disable_menus', 999 );
        $this->loader->add_action( 'after_setup_theme', $plugin_admin, 'add_editor_style' );
        $this->loader->add_action( 'login_enqueue_scripts', $plugin_admin, 'enqueue_login_styles' );
        $this->loader->add_action( 'login_init', $plugin_admin, 'dequeue_login_styles' );
        $this->loader->add_action( 'plugins_loaded', $plugin_admin, 'settings_page' );
        $this->loader->add_action( 'tgmpa_register', $plugin_admin, 'required_plugins' );
        $this->loader->add_action( 'wp_ajax_clear_debug_log', $plugin_admin, 'clear_debug_log' );
        $this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'disable_metaboxes' );
        $this->loader->add_filter( 'acf/settings/show_admin', $plugin_admin, 'acf_settings_show_admin' );
        $this->loader->add_filter( 'login_headertitle', $plugin_admin, 'login_headertitle' );
        $this->loader->add_filter( 'login_headerurl', $plugin_admin, 'login_headerurl' );
        $this->loader->add_filter( 'mce_buttons', $plugin_admin, 'mce_buttons' );
        $this->loader->add_filter( 'screen_options_show_screen', $plugin_admin, 'screen_options_show_screen' );
        $this->loader->add_filter( 'update_footer', $plugin_admin, 'footer_right', 999 );
        //$this->loader->add_filter( 'locale', $plugin_admin, 'set_locale', 10 );
        //$this->loader->add_action( 'init', $plugin_admin, 'disable_update_checks', 11 );
        //$this->loader->add_filter( 'plugin_action_links_nanga.php', $plugin_admin, 'plugin_action_links' );
        //$this->loader->add_filter( 'rewrite_rules_array', $plugin_admin, 'disable_rewrite_rules' );
    }

    private function define_public_hooks() {
        $plugin_public = new Nanga_Public( $this->get_nanga(), $this->get_version() );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'asset_cachebusting', 100 );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_head', $plugin_public, 'analytics' );
        $this->loader->add_filter( 'body_class', $plugin_public, 'body_class' );
        $this->loader->add_filter( 'the_password_form', $plugin_public, 'the_password_form' );
        //$this->loader->add_filter( 'comment_id_fields', $plugin_public, 'remove_self_closing_tags' );
        //$this->loader->add_filter( 'get_avatar', $plugin_public, 'remove_self_closing_tags' );
        //$this->loader->add_filter( 'post_thumbnail_html', $plugin_public, 'remove_self_closing_tags' );
    }

    private function define_shortcodes() {
        $plugin_shortcodes = new Nanga_Shortcodes( $this->get_nanga(), $this->get_version() );
    }

    public function run() {
        $this->loader->run();
    }

    public function get_loader() {
        return $this->loader;
    }
}
