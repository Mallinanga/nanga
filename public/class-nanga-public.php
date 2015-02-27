<?php

class Nanga_Public {
    private $nanga;
    private $version;

    public function __construct( $nanga, $version ) {
        $this->nanga   = $nanga;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style( $this->nanga, plugin_dir_url( __FILE__ ) . 'css/nanga-public.css', array(), $this->version, 'all' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->nanga, plugin_dir_url( __FILE__ ) . 'js/nanga-public.js', array( 'jquery' ), $this->version, true );
    }

    public function asset_cachebusting() {
        global $wp_styles, $wp_scripts;
        $wp_dir         = str_replace( home_url(), '', site_url() );
        $site_root_path = str_replace( $wp_dir, '', ABSPATH );
        foreach (
            array(
                'wp_styles',
                'wp_scripts'
            ) as $resource
        ) {
            foreach ( (array) $$resource->queue as $name ) {
                if ( empty( $$resource->registered[ $name ] ) ) {
                    continue;
                }
                $src = $$resource->registered[ $name ]->src;
                if ( 0 === strpos( $src, '/' ) ) {
                    $src = site_url( $src );
                }
                if ( false === strpos( $src, home_url() ) ) {
                    continue;
                }
                $file = str_replace( home_url( '/' ), $site_root_path, $src );
                if ( ! file_exists( $file ) ) {
                    continue;
                }
                $mtime = filectime( $file );
                //$$resource->registered[ $name ]->ver = $$resource->registered[ $name ]->ver . '-' . $mtime;
                $$resource->registered[ $name ]->ver = $mtime;
            }
        }
    }

    public function the_password_form() {
        $output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form pure-form pure-form-stacked" method="post">';
        $output .= '<input name="post_password" type="password" class="pure-input-1">';
        $output .= '<input type="submit" class="pure-button pure-input-1" value="' . __( 'View', 'nanga' ) . '">';
        $output .= '</form>';

        return $output;
    }

    public function remove_self_closing_tags( $input ) {
        return str_replace( ' />', '>', $input );
    }

    public function analytics() {
        $google_analytics_ua = get_field( 'vg_google_analytics', 'options' );
        if ( ! empty( $google_analytics_ua ) ) {
            echo '<script type="text/javascript">var _gaq = _gaq || []; _gaq.push([\'_setAccount\', \'' . $google_analytics_ua . '\']); _gaq.push([\'_trackPageview\']); (function () { var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true; ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\'; var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s); })();</script>';
        }
        if ( get_field( 'vg_google_analytics_events', 'options', true ) ) {
            echo '<script type="text/javascript">(function (tos) { window.setInterval(function () { tos = (function (t) { return t[0] == 50 ? (parseInt(t[1]) + 1) + \':00\' : (t[1] || \'0\') + \':\' + (parseInt(t[0]) + 10); })(tos.split(\':\').reverse()); window.pageTracker ? pageTracker._trackEvent(\'Time\', \'Log\', tos) : _gaq.push([\'_trackEvent\', \'Time\', \'Log\', tos]); }, 10000); })(\'00\');</script>';
        }
    }

    public function body_class( $classes ) {
        global $wp_query;
        $no_classes = '';
        if ( is_page() ) {
            $page_id    = $wp_query->get_queried_object_id();
            $no_classes = 'page-id-' . $page_id;
        }
        if ( is_single() ) {
            $post_id    = $wp_query->get_queried_object_id();
            $no_classes = 'postid-' . $post_id;
        }
        if ( is_author() ) {
            $author_id  = $wp_query->get_queried_object_id();
            $no_classes = 'author-' . $author_id;
        }
        if ( is_category() ) {
            $cat_id     = $wp_query->get_queried_object_id();
            $no_classes = 'category-' . $cat_id;
        }
        if ( is_tax() ) {
            $ancestors = get_ancestors( get_queried_object_id(), get_queried_object()->taxonomy );
            if ( ! empty( $ancestors ) ) {
                foreach ( $ancestors as $ancestor ) {
                    $term      = get_term( $ancestor, get_queried_object()->taxonomy );
                    $classes[] = esc_attr( "parent-$term->taxonomy-$term->term_id" );
                }
            }
        }
        if ( is_single() || is_page() && ! is_front_page() ) {
            $classes[] = basename( get_permalink() );
        }
        $home_id_class  = 'page-id-' . get_option( 'page_on_front' );
        $remove_classes = array(
            'page-template-default',
            $home_id_class,
            $no_classes
        );
        $classes        = array_diff( $classes, $remove_classes );

        return $classes;
    }

    public function nice_search() {
        global $wp_rewrite;
        if ( ! isset( $wp_rewrite ) || ! is_object( $wp_rewrite ) || ! $wp_rewrite->using_permalinks() ) {
            return;
        }
        $search_base = $wp_rewrite->search_base;
        if ( is_search() && ! is_admin() && false === strpos( $_SERVER['REQUEST_URI'], "/{$search_base}/" ) ) {
            wp_redirect( home_url( "/{$search_base}/" . urlencode( get_query_var( 's' ) ) ) );
            exit();
        }
    }

    /**
     * @todo
     */
    public function change_locale_on_the_fly( $locale ) {
        if ( isset( $_GET['language'] ) && 'el' == $_GET['language'] ) {
            return 'el';
        } else {
            return $locale;
        }
    }
}
