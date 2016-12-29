<?php

class NangaThirdParty
{

    private $nanga;
    private $version;

    public function __construct($nanga, $version)
    {
        $this->nanga   = $nanga;
        $this->version = $version;
    }

    public function features_akismet()
    {
        if (class_exists('Akismet')) {
            add_filter('akismet_debug_log', '__return_false');
        }
    }

    public function features_help()
    {
        if (class_exists('CWS_WP_Help_Plugin')) {
            //add_filter( 'cws_wp_help_option_defaults', array( 'menu_location' => 'below-dashboard', ) );
        }
    }

    public function features_wordpress_social_login()
    {
        if (function_exists('wsl_add_stylesheets')) {
            remove_action('wp_enqueue_scripts', 'wsl_add_stylesheets');
            remove_action('login_enqueue_scripts', 'wsl_add_stylesheets');
        }
    }

    public function features_gravity_forms()
    {
        if (class_exists('GFForms')) {
            //add_filter('gform_confirmation_anchor', '__return_false');
            //add_filter('gform_enable_shortcode_notification_message', '__return_false');
            add_filter('gform_disable_view_counter', '__return_true');
            remove_action('widgets_init', 'gf_register_widget');
        }
    }

    public function features_woocommerce()
    {
        if (class_exists('WooCommerce')) {
            remove_action('wp_head', 'wc_generator_tag');
            add_action('init', function () {
                remove_post_type_support('product', 'custom-fields');
            });
            add_action('admin_menu', function () {
                remove_menu_page('separator-woocommerce');
                if ( ! current_user_can('manage_woocommerce')) {
                    remove_menu_page('woocommerce');
                }
            }, 999);
            //add_filter( 'woocommerce_enqueue_styles', '__return_false' );
            //remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
            //add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
            /*
            if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
                function woocommerce_template_loop_product_thumbnail() {
                    echo woocommerce_get_product_thumbnail();
                }
            }
            if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {
                function woocommerce_get_product_thumbnail(
                    $size = 'shop_catalog',
                    $placeholder_width = 0,
                    $placeholder_height = 0
                ) {
                    global $post, $woocommerce;
                    if ( ! $placeholder_width ) {
                        $placeholder_width = $woocommerce->get_image_size( 'shop_catalog_image_width' );
                    }
                    if ( ! $placeholder_height ) {
                        $placeholder_height = $woocommerce->get_image_size( 'shop_catalog_image_height' );
                    }
                    $output = '<div class="imagewrapper">';
                    if ( has_post_thumbnail() ) {
                        $output .= get_the_post_thumbnail( $post->ID, $size );
                    } else {
                        $output .= '<img src="' . woocommerce_placeholder_img_src() . '" alt="Placeholder" width="' . $placeholder_width . '" height="' . $placeholder_height . '" />';
                    }
                    $output .= '</div>';

                    return $output;
                }
            }
            */
        }
    }

    public function features_easy_digital_downloads()
    {
        if (class_exists('Easy Digital Downloads')) {
            add_filter('edd_api_log_requests', '__return_false');
            remove_action('plugins_loaded', ['EDD_Heartbeat', 'init']);
            remove_action('wp_head', 'edd_version_in_header');
            if ( ! defined('EDD_SLUG')) {
                define('EDD_SLUG', 'services');
            }
        }
    }

    public function features_wpml()
    {
        if (class_exists('SitePress')) {
            global $sitepress;
            remove_action('wp_head', [
                $sitepress,
                'meta_generator_tag',
            ]);
            add_action('wp_before_admin_bar_render', function () {
                global $wp_admin_bar;
                $wp_admin_bar->remove_menu('WPML_ALS');
            });
            add_action('admin_init', function () {
                global $sitepress;
                remove_action('show_user_profile', [
                    $sitepress,
                    'show_user_options',
                ]);
            });
        }
    }

    public function features_yoast_seo()
    {
        if (class_exists('WPSEO_Frontend')) {
            add_filter('wpseo_use_page_analysis', '__return_false');
            remove_action('admin_notices', [Yoast_Notification_Center::get(), 'display_notifications']);
            remove_action('all_admin_notices', [Yoast_Notification_Center::get(), 'display_notifications']);
            //add_filter( 'wpseo_bulk_edit_roles', function ( $roles ) { return array( 'administrator' ); }, 999999 );
        }
    }

    public function features_jetpack()
    {
        if (function_exists('jetpack_photon_url')) {
            add_filter('jetpack_photon_url', 'jetpack_photon_url', 10, 3);
        }
        if (class_exists('Jetpack')) {
            if (defined('WP_ENV') && 'production' != WP_ENV) {
                add_filter('jetpack_development_mode', '__return_true');
            }
            add_action('after_setup_theme', function () {
                add_theme_support('infinite-scroll', [
                    'container' => 'content',
                    'footer'    => false,
                ]);
            });
            add_action('wp_enqueue_scripts', function () {
                //wp_dequeue_script( 'devicepx' );
            }, 20);
            add_filter('jetpack_disable_twitter_cards', '__return_true', 99);
            add_filter('jetpack_enable_open_graph', '__return_false', 99);
            add_filter('jetpack_get_default_modules', '__return_empty_array');
            add_filter('jetpack_implode_frontend_css', '__return_false');
            add_filter('wpl_is_enabled_sitewide', '__return_false');
            add_filter('infinite_scroll_js_settings', function ($settings) {
                write_log($settings);

                return $settings;
            });
        }
    }

    public function features_json_api()
    {
        if (class_exists('WP_JSON_Server')) {
            remove_action('wp_head', 'json_output_link_wp_head', 10, 0);
            add_filter('json_url_prefix', function () {
                return 'api/v1';
            });
            add_filter('json_serve_request', function () {
                header('Access-Control-Allow-Origin: *');
            });
            add_filter('json_query_var-posts_per_page', function ($posts_per_page) {
                if (10 < intval($posts_per_page)) {
                    $posts_per_page = 10;
                }

                return $posts_per_page;
            });
            add_filter('json_query_vars', function ($valid_vars) {
                $valid_vars[] = 'offset';

                return $valid_vars;
            });
        }
    }

    public function features_timber()
    {
        if (class_exists('Timber')) {
            add_filter('timber/cache/location', function () {
                return WP_CONTENT_DIR . '/cache/timber';
            });
        }
    }
}
