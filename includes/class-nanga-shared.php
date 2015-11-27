<?php

class Nanga_Shared {
    private $nanga;
    private $version;

    public function __construct( $nanga, $version ) {
        $this->nanga   = $nanga;
        $this->version = $version;
        $this->run_cleanup();
        $this->run_search_visibility();
        //$this->run_updates();
    }

    private function run_cleanup() {
        add_filter( 'comment_flood_filter', '__return_false', 10, 3 );
        add_filter( 'enable_post_by_email_configuration', '__return_false', 100 );
        add_filter( 'sanitize_user', 'strtolower' );
        add_filter( 'the_generator', '__return_false' );
        add_filter( 'use_default_gallery_style', '__return_false' );
        add_filter( 'widget_text', 'do_shortcode' );
        remove_action( 'init', 'smilies_init', 5 );
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
        remove_filter( 'comment_text', 'make_clickable', 9 );
        remove_filter( 'comments_open', '_close_comments_for_old_post', 10, 2 );
        remove_filter( 'pings_open', '_close_comments_for_old_post', 10, 2 );
        remove_filter( 'template_redirect', 'redirect_canonical' );
        remove_filter( 'template_redirect', 'wp_old_slug_redirect' );
        remove_filter( 'template_redirect', 'wp_redirect_admin_locations', 1000 );
        remove_filter( 'template_redirect', 'wp_shortlink_header', 11 );
        remove_filter( 'the_content', 'capital_P_dangit', 11 );
        remove_filter( 'the_content', 'convert_smilies' );
        remove_filter( 'the_content', 'wptexturize' );
        remove_filter( 'the_excerpt', 'convert_smilies' );
        remove_filter( 'the_excerpt', 'wptexturize' );
        remove_filter( 'the_title', 'capital_P_dangit', 11 );
        remove_filter( 'the_title', 'wptexturize' );
        remove_filter( 'wp_title', 'capital_P_dangit', 11 );
        remove_filter( 'wp_title', 'wptexturize' );
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        add_filter( 'tiny_mce_plugins', function ( $plugins ) {
            if ( is_array( $plugins ) ) {
                return array_diff( $plugins, array( 'wpemoji' ) );
            } else {
                return array();
            }
        } );
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
        add_filter( 'auto_core_update_email', function ( $email ) {
            $email['headers'] = 'From: Blinky <blinky@vgwebthings.com>' . "\r\n";
            $email['to']      = 'infrastructure@vgwebthings.com';

            return $email;
        }, 1 );
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
                $allowed_plugins = array(
                    'acf-gallery',
                    'acf-options-page',
                    'acf-repeater',
                    'advanced-custom-fields',
                    'advanced-custom-fields-pro',
                    'akismet',
                    'codepress-admin-columns',
                    'floating-social-bar',
                    'imsanity',
                    'jigsaw',
                    'json-rest-api',
                    'nanga',
                    'nanga-deploy',
                    'nextgen-gallery',
                    'post-types-order',
                    'posts-to-posts',
                    'relevanssi',
                    'simple-lightbox',
                    'sitepress-multilingual-cms',
                    'timber-library',
                    'user-role-editor',
                    'woocommerce-multilingual',
                    'wordpress-seo',
                    'wp-thumb',
                    'wpml-media',
                    'wpml-string-translation',
                    'wpml-translation-management',
                );
                if ( in_array( $item->slug, $allowed_plugins ) ) {
                    return true;
                }

                return false;
            }, 20, 2 );
        }
        add_filter( 'auto_update_theme', function ( $update, $item ) {
            return false;
        }, 20, 2 );
    }

    public function disable_core_functionality() {
        remove_post_type_support( 'attachment', 'comments' );
        remove_post_type_support( 'page', 'comments' );
        remove_post_type_support( 'page', 'custom-fields' );
        remove_post_type_support( 'page', 'revisions' );
        remove_post_type_support( 'page', 'trackbacks' );
        remove_post_type_support( 'post', 'custom-fields' );
        remove_post_type_support( 'post', 'excerpt' );
        remove_post_type_support( 'post', 'post-formats' );
        remove_post_type_support( 'post', 'revisions' );
        remove_post_type_support( 'post', 'trackbacks' );
        if ( current_theme_supports( 'nanga-disable-comments' ) ) {
            remove_post_type_support( 'post', 'comments' );
        }
    }

    public function disable_feeds() {
        wp_die( 'Nothing to see here...' );
    }

    public function disable_taxonomies() {
        global $wp_taxonomies;
        if ( current_theme_supports( 'nanga-disable-categories' ) ) {
            register_taxonomy( 'category', array() );
            unset( $wp_taxonomies['category'] );
        }
        if ( current_theme_supports( 'nanga-disable-tags' ) ) {
            register_taxonomy( 'post_tag', array() );
            unset( $wp_taxonomies['post_tag'] );
        }
        unset( $wp_taxonomies['link_category'] );
        unset( $wp_taxonomies['post_format'] );
    }

    public function disable_post_types() {
        if ( current_theme_supports( 'nanga-disable-posts' ) ) {
            global $wp_post_types;
            unset( $wp_post_types['post'] );
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
        if ( current_theme_supports( 'nanga-disable-categories' ) ) {
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
        if ( current_theme_supports( 'nanga-disable-tags' ) ) {
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
                $vg_content_width = get_field( 'vg_content_width', 'options' );
                if ( $vg_content_width ) {
                    $content_width = $vg_content_width;
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
            'primary' => __( 'Primary Menu', $this->nanga ),
            'footer'  => __( 'Footer Menu', $this->nanga ),
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

    public function mail_from() {
        $from = 'info@' . preg_replace( '/^www\./', '', $_SERVER['SERVER_NAME'] );

        return apply_filters( $this->nanga . '_mail_from', $from );
    }

    public function mail_from_name() {
        $from_name = get_bloginfo();

        return apply_filters( $this->nanga . '_mail_from_name', $from_name );
    }

    public function dump_queries() {
        if ( isset( $_GET['nanga_dump'] ) && current_user_can( 'manage_options' ) ) {
            global $wpdb;
            echo '<pre>';
            print_r( $wpdb->queries );
            echo '</pre>';
        }
    }

    public function empty_search( $query_vars ) {
        if ( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
            $query_vars['s'] = 'empty';
        }

        return $query_vars;
    }

    public function robots( $output, $public ) {
        $output .= "Disallow: /console/\n";
        $output .= "Disallow: /vendor/\n";
        $output .= "Disallow: /wp-includes/\n";

        return $output;
    }

    public function heartbeat( $settings ) {
        $settings['interval'] = 15;

        return $settings;
    }
}
