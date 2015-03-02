<?php

class Nanga_Shared {
    private $nanga;
    private $version;

    public function __construct( $nanga, $version ) {
        $this->nanga   = $nanga;
        $this->version = $version;
        $this->run_cleanup();
        $this->run_search_visibility();
        $this->run_updates();
    }

    private function run_cleanup() {
        add_filter( 'comment_flood_filter', '__return_false', 10, 3 );
        add_filter( 'enable_post_by_email_configuration', '__return_false', 100 );
        add_filter( 'sanitize_user', 'strtolower' );
        add_filter( 'use_default_gallery_style', '__return_false' );
        add_filter( 'widget_text', 'do_shortcode' );
        remove_action( 'login_head', 'wp_shake_js', 12 );
        remove_action( 'set_comment_cookies', 'wp_set_comment_cookies' );
        remove_action( 'welcome_panel', 'wp_welcome_panel' );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
        remove_action( 'wp_head', 'feed_links', 2 );
        remove_action( 'wp_head', 'feed_links_extra' );
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'index_rel_link' );
        remove_action( 'wp_head', 'parent_post_rel_link', 10 );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'start_post_rel_link', 10 );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
        remove_all_filters( 'comment_flood_filter' );
        remove_filter( 'comment_text', 'capital_P_dangit', 31 );
        remove_filter( 'the_content', 'capital_P_dangit', 11 );
        remove_filter( 'the_title', 'capital_P_dangit', 11 );
        show_admin_bar( false );
    }

    private function run_search_visibility() {
        if ( ! defined( 'WP_ENV' ) ) {
            return;
        }
        if ( 'production' === WP_ENV ) {
            if ( ! get_option( 'blog_public' ) ) {
                update_option( 'blog_public', 1 );
            }
        } else {
            if ( get_option( 'blog_public' ) ) {
                update_option( 'blog_public', 0 );
            }
        }
    }

    private function run_updates() {
        //add_filter( 'auto_core_update_send_email', '__return_false' );
        add_filter( 'automatic_updates_send_debug_email', '__return_true' );
        if ( defined( 'WP_ENV' ) && 'development' === WP_ENV ) {
            add_filter( 'automatic_updates_is_vcs_checkout', '__return_false', 1 );
        }
        if ( defined( 'NG_PLAYGROUND' ) && NG_PLAYGROUND ) {
            add_filter( 'auto_update_plugin', function ( $update, $item ) {
                $disallowed_plugins = array( 'nanga' );
                if ( in_array( $item->slug, $disallowed_plugins ) ) {
                    return false;
                }

                return true;
            }, 20, 2 );
        } else {
            add_filter( 'auto_update_plugin', function ( $update, $item ) {
                $allowed_plugins = array( 'advanced-custom-fields-pro', 'github-updater', 'jigsaw', 'timber-library' );
                if ( in_array( $item->slug, $allowed_plugins ) ) {
                    return true;
                }

                return false;
            }, 20, 2 );
        }
        /*
         * @todo
        add_filter( 'auto_update_theme', function ( $update, $item ) {
            $allowed_themes = array( 'vg-base' );
            if ( in_array( $item->slug, $allowed_themes ) ) {
            }

            return false;
        }, 20, 2 );
        */
    }

    public function disable_core_functionality() {
        // title editor author thumbnail excerpt trackbacks custom-fields comments revisions page-attributes
        remove_post_type_support( 'attachment', 'comments' );
        remove_post_type_support( 'page', 'comments' );
        remove_post_type_support( 'page', 'custom-fields' );
        remove_post_type_support( 'page', 'revisions' );
        remove_post_type_support( 'page', 'thumbnail' );
        remove_post_type_support( 'post', 'custom-fields' );
        remove_post_type_support( 'post', 'excerpt' );
        remove_post_type_support( 'post', 'post-formats' );
        remove_post_type_support( 'post', 'revisions' );
        remove_post_type_support( 'post', 'trackbacks' );
    }

    public function disable_feeds() {
        wp_die( 'Nothing to see here...' );
    }

    public function disable_taxonomies() {
        global $wp_taxonomies;
        if ( current_theme_supports( 'disable-categories' ) ) {
            unset( $wp_taxonomies['category'] );
        }
        if ( current_theme_supports( 'disable-tags' ) ) {
            //register_taxonomy( 'post_tag', array() );
            unset( $wp_taxonomies['post_tag'] );
        }
        unset( $wp_taxonomies['link_category'] );
        unset( $wp_taxonomies['post_format'] );
    }

    public function disable_post_types() {
        if ( current_theme_supports( 'disable-posts' ) ) {
            //global $wp_post_types;
            //unset( $wp_post_types['post'] );
        }
    }

    public function wp_headers( $headers ) {
        if ( isset( $headers['X-Pingback'] ) ) {
            unset( $headers['X-Pingback'] );
        }

        return $headers;
    }

    public function xmlrpc_methods( $methods ) {
        unset( $methods['pingback.ping'] );

        return $methods;
    }

    public function bloginfo_url( $output, $show ) {
        if ( 'pingback_url' === $show ) {
            $output = '';
        }

        return $output;
    }

    public function xmlrpc_call( $action ) {
        if ( 'pingback.ping' === $action ) {
            wp_die( 'Pingbacks are not supported', 'Not Allowed!', array( 'response' => 403 ) );
        }
    }

    public function filter_rewrites( $rules ) {
        foreach ( $rules as $rule => $rewrite ) {
            if ( preg_match( '/trackback\/\?\$$/i', $rule ) ) {
                unset( $rules[ $rule ] );
            }
            if ( preg_match( '/.*(feed)/', $rule ) ) {
                unset( $rules[ $rule ] );
            }
        }
        if ( current_theme_supports( 'disable-categories' ) ) {
            if ( get_option( 'category_base' ) ) {
                $category_base = get_option( 'category_base' );
            } else {
                $category_base = 'category';
            }
            foreach ( $rules as $rule => $rewrite ) {
                if ( preg_match( '/(' . $category_base . ')/', $rule ) ) {
                    unset( $rules[ $rule ] );
                }
            }
        }
        if ( current_theme_supports( 'disable-tags' ) ) {
            if ( get_option( 'tag_base' ) ) {
                $tag_base = get_option( 'tag_base' );
            } else {
                $tag_base = 'tag';
            }
            foreach ( $rules as $rule => $rewrite ) {
                if ( preg_match( '/(' . $tag_base . ')/', $rule ) ) {
                    unset( $rules[ $rule ] );
                }
            }
        }

        return $rules;
    }

    public function setup_theme() {
        if ( ! isset( $content_width ) ) {
            if ( function_exists( 'get_field' ) ) {
                if ( get_field( 'vg_content_width', 'options' ) ) {
                    $content_width = get_field( 'vg_content_width', 'options' );
                } else {
                    $content_width = 800;
                }
            } else {
                $content_width = 800;
            }
        }
        add_theme_support( 'menus' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'html5', array(
            'caption',
            'comment-form',
            'comment-list',
            'gallery',
            'search-form',
        ) );
        register_nav_menus( array(
            'primary' => __( 'Primary Menu', 'vg' ),
            'footer'  => __( 'Footer Menu', 'vg' ),
        ) );
    }

    public function acf_load_point() {
        add_filter( 'acf/settings/load_json', function ( $paths ) {
            unset( $paths[0] );
            $paths[] = dirname( __FILE__ ) . '/acf';

            return $paths;
        } );
    }

    public function acf_save_point() {
        if ( defined( 'NG_PLAYGROUND' ) && NG_PLAYGROUND ) {
            add_filter( 'acf/settings/save_json', function ( $path ) {
                $path = dirname( __FILE__ ) . '/acf';

                return $path;
            } );
        }
    }

    public function features_wordpress_social_login() {
        if ( function_exists( 'wsl_add_stylesheets' ) ) {
            remove_action( 'wp_enqueue_scripts', 'wsl_add_stylesheets' );
            remove_action( 'login_enqueue_scripts', 'wsl_add_stylesheets' );
        }
    }

    public function features_gravity_forms() {
        if ( class_exists( 'GFForms' ) ) {
            add_filter( 'gform_confirmation_anchor', create_function( '', 'return false;' ) );
            add_filter( 'gform_enable_shortcode_notification_message', '__return_false' );
            add_filter( 'gform_init_scripts_footer', '__return_true' );
            remove_action( 'install_plugins_pre_plugin-information', array( 'GFLogging', 'display_changelog' ) );
            remove_filter( 'site_transient_update_plugins', array( 'GFForms', 'check_update', ) );
            remove_filter( 'site_transient_update_plugins', array( 'GFLogging', 'check_update', ) );
            remove_filter( 'site_transient_update_plugins', array( 'RGForms', 'check_update', ) );
            remove_filter( 'transient_update_plugins', array( 'GFForms', 'check_update', ) );
            remove_filter( 'transient_update_plugins', array( 'GFLogging', 'check_update', ) );
            remove_filter( 'transient_update_plugins', array( 'RGForms', 'check_update', ) );
            //add_filter( 'gform_cdata_open', 'wrap_gform_cdata_open' );
            function wrap_gform_cdata_open( $content = '' ) {
                $content = 'document.addEventListener( "DOMContentLoaded", function() { ';

                return $content;
            }

            //add_filter( 'gform_cdata_close', 'wrap_gform_cdata_close' );
            function wrap_gform_cdata_close( $content = '' ) {
                $content = ' }, false );';

                return $content;
            }

            //add_action( 'admin_head', 'custom_gform_admin_menu_icon' );
            function custom_gform_admin_menu_icon() {
                echo "<style>#adminmenu #toplevel_page_gf_entries div.wp-menu-image:before, #adminmenu #toplevel_page_gf_edit_forms div.wp-menu-image:before{content:'\175';}#adminmenu #toplevel_page_gf_entries div.wp-menu-image img, #adminmenu #toplevel_page_gf_edit_forms div.wp-menu-image img{display:none;}</style>";
            }
        }
    }

    public function features_woocommerce() {
        if ( class_exists( 'WooCommerce' ) ) {
            add_filter( 'woocommerce_enqueue_styles', '__return_false' );
            add_action( 'get_header', 'nanga_remove_woocommerce_generator_tag' );
            function nanga_remove_woocommerce_generator_tag() {
                remove_action( 'wp_head', 'wc_generator_tag' );
            }

            remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
            add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
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
        }
    }

    public function features_easy_digital_downloads() {
        if ( class_exists( 'Easy Digital Downloads' ) ) {
            add_filter( 'edd_api_log_requests', '__return_false' );
            remove_action( 'plugins_loaded', array( 'EDD_Heartbeat', 'init' ) );
            remove_action( 'wp_head', 'edd_version_in_header' );
            if ( ! defined( 'EDD_SLUG' ) ) {
                define( 'EDD_SLUG', 'products' );
            }
        }
    }

    public function features_wpml() {
        if ( class_exists( 'SitePress' ) ) {
            global $sitepress;
            remove_action( 'wp_head', array(
                $sitepress,
                'meta_generator_tag'
            ) );
            add_action( 'wp_before_admin_bar_render', function () {
                global $wp_admin_bar;
                $wp_admin_bar->remove_menu( 'WPML_ALS' );
            } );
            add_action( 'admin_init', function () {
                global $sitepress;
                remove_action( 'show_user_profile', array(
                    $sitepress,
                    'show_user_options'
                ) );
            } );
        }
    }

    public function features_yoast_seo() {
        if ( class_exists( 'WPSEO_Frontend' ) ) {
            add_filter( 'wpseo_use_page_analysis', '__return_false' );
            add_action( 'admin_init', function () {
                global $wpseo_admin;
                remove_action( 'show_user_profile', array(
                    $wpseo_admin,
                    'user_profile'
                ) );
                remove_action( 'edit_user_profile', array(
                    $wpseo_admin,
                    'user_profile'
                ) );
            } );
        }
    }

    public function features_jetpack() {
        if ( function_exists( 'jetpack_photon_url' ) ) {
            add_filter( 'jetpack_photon_url', 'jetpack_photon_url', 10, 3 );
        }
        if ( class_exists( 'Jetpack' ) ) {
            add_filter( 'jetpack_development_mode', '__return_true' );
            add_filter( 'jetpack_get_default_modules', '__return_empty_array' );
            add_action( 'after_setup_theme', function () {
                add_theme_support( 'infinite-scroll', array(
                    'container' => 'main',
                    'footer'    => 'page'
                ) );
            } );
            add_action( 'wp_enqueue_scripts', function () {
                wp_dequeue_style( 'jetpack_related-posts' );
                wp_dequeue_style( 'jetpack_likes' );
                wp_dequeue_script( 'devicepx' );
            }, 20 );
            function nanga_post_tweet_count( $post_id ) {
                if ( ! ( $count = get_transient( 'wds_post_tweet_count' . $post_id ) ) ) {
                    $response = wp_remote_retrieve_body( wp_remote_get( 'https://cdn.api.twitter.com/1/urls/count.json?url=' . urlencode( get_permalink( $post_id ) ) ) );
                    if ( is_wp_error( $response ) ) {
                        return 'error';
                    }
                    $json  = json_decode( $response );
                    $count = absint( $json->count );
                    set_transient( 'wds_post_tweet_count' . $post_id, absint( $count ), 30 * MINUTE_IN_SECONDS );
                }

                return absint( $count );
            }

            function nanga_post_like_count( $post_id ) {
                if ( ! ( $count = get_transient( 'wds_post_like_count' . $post_id ) ) ) {
                    $fql = 'SELECT url, ';
                    //$fql .= "share_count, ";
                    //$fql .= "like_count, ";
                    //$fql .= "comment_count, ";
                    $fql .= 'total_count ';
                    $fql .= "FROM link_stat WHERE url = '" . get_permalink( $post_id ) . "'";
                    $response = wp_remote_retrieve_body( wp_remote_get( 'https://api.facebook.com/method/fql.query?format=json&query=' . urlencode( $fql ) ) );
                    if ( is_wp_error( $response ) ) {
                        return 'error';
                    }
                    $json  = json_decode( $response );
                    $count = absint( $json[0]->total_count );
                    set_transient( 'wds_post_like_count' . $post_id, absint( $count ), 30 * 60 );
                }

                return absint( $count );
            }

            function nanga_post_pageview_count( $post_id ) {
                if ( ! ( $count = get_transient( 'wds_post_pageview_count' . $post_id ) ) ) {
                    if ( function_exists( 'stats_get_csv' ) ) {
                        $response = stats_get_csv( 'postviews', 'post_id=' . $post_id . '&period=month&limit=1' );
                        $count    = absint( $response[0]['views'] );
                    } else {
                        return 'error';
                    }
                    set_transient( 'wds_post_pageview_count' . $post_id, absint( $count ), 30 * 60 );
                }

                return absint( $count );
            }

            function nanga_post_comment_count( $post_id ) {
                if ( ! ( $count = get_transient( 'wds_post_comment_count' . $post_id ) ) ) {
                    if ( comments_open() ) {
                        $count = absint( get_comments_number( $post_id ) );
                    } else {
                        return 'error';
                    }
                    set_transient( 'wds_post_comment_count' . $post_id, absint( $count ), 30 * 60 );
                }

                return absint( $count );
            }
        }
    }

    public function features_json_api() {
        if ( class_exists( 'WP_JSON_Server' ) ) {
            remove_action( 'wp_head', 'json_output_link_wp_head', 10, 0 );
            add_filter( 'json_url_prefix', function () {
                return 'api/v1';
            } );
            add_filter( 'json_serve_request', function () {
                header( 'Access-Control-Allow-Origin: *' );
            } );
            add_filter( 'json_query_var-posts_per_page', function ( $posts_per_page ) {
                if ( 10 < intval( $posts_per_page ) ) {
                    $posts_per_page = 10;
                }

                return $posts_per_page;
            } );
            add_filter( 'json_query_vars', function ( $valid_vars ) {
                $valid_vars[] = 'offset';

                return $valid_vars;
            } );
        }
    }

    public function mail_from() {
        $from = 'info@' . $_SERVER['SERVER_NAME'];

        return apply_filters( 'vg_mail_from', $from );
    }

    public function mail_from_name() {
        $from_name = get_bloginfo();

        return apply_filters( 'vg_mail_from_name', $from_name );
    }

    public function dump_queries() {
        if ( isset( $_GET['nanga_dump'] ) && current_user_can( 'manage_options' ) ) {
            global $wpdb;
            echo '<pre>';
            print_r( $wpdb->queries );
            echo '</pre>';
        }
    }

    /**
     * @todo
     */
    public function empty_search( $query_vars ) {
        if ( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
            $query_vars['s'] = 'Please do not do empty searches...';
        }

        return $query_vars;
    }

    private function run_plugin_control() {
        /*
         * @todo
         */
        /*
        $plugin_control = new Nanga_Plugin_Control( array( 'core-control.php' ) );
        $plugin_control = new Nanga_Plugin_Control();
        if ( defined( 'WP_ENV' ) && 'development' === WP_ENV ) {
            $plugin_control->disable( 'core-control.php' );
            $plugin_control->disable( 'underConstruction.php' );
        }
        if ( defined( 'WP_ENV' ) && 'development' !== WP_ENV ) {
            $plugin_control->disable( 'core-control.php' );
        }
        */
    }
}
