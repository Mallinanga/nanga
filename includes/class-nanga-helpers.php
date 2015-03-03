<?php

class Nanga_Helpers {
    private $nanga;
    private $version;

    public function __construct( $nanga, $version ) {
        $this->nanga   = $nanga;
        $this->version = $version;
    }

    /**
     * This will generate a line of CSS for use in header output. If the setting
     * ($mod_name) has no defined value, the CSS will not be output.
     *
     * @uses get_theme_mod()
     *
     * @param string $selector CSS selector
     * @param string $style The name of the CSS *property* to modify
     * @param string $mod_name The name of the 'theme_mod' option to fetch
     * @param string $prefix Optional. Anything that needs to be output before the CSS property
     * @param string $postfix Optional. Anything that needs to be output after the CSS property
     * @param bool $echo Optional. Whether to print directly to the page (default: true).
     *
     * @return string Returns a single line of CSS with selectors and a property.
     */
    public static function generate_css( $selector, $style, $mod_name, $prefix = '', $postfix = '', $echo = true ) {
        $return = '';
        $mod    = get_theme_mod( $mod_name );
        if ( ! empty( $mod ) ) {
            $return = sprintf( '%s { %s:%s; }',
                $selector,
                $style,
                $prefix . $mod . $postfix
            );
            if ( $echo ) {
                echo $return;
            }
        }

        return $return;
    }

    public static function nanga_estimated_reading_time() {
        $post_object = get_post();
        $words       = str_word_count( strip_tags( $post_object->post_content ) );
        $minutes     = floor( $words / 120 );
        $seconds     = floor( $words % 120 / ( 120 / 60 ) );
        if ( 1 <= $minutes ) {
            $estimated_time = $minutes . ' minute' . ( $minutes == 1 ? '' : 's' ) . ', ' . $seconds . ' second' . ( $seconds == 1 ? '' : 's' );
        } else {
            $estimated_time = $seconds . ' second' . ( $seconds == 1 ? '' : 's' );
        }

        return $estimated_time;
    }

    public static function nanga_related_posts() {
        global $post;
        $tags = wp_get_post_tags( $post->ID );
        if ( $tags ) {
            $first_tag = $tags[0]->term_id;
            $my_query  = new WP_Query( array(
                'tag__in'          => array( $first_tag ),
                'post__not_in'     => array( $post->ID ),
                'showposts'        => 5,
                'caller_get_posts' => 1
            ) );
            if ( $my_query->have_posts() ) {
                echo '<h3>Related Posts</h3>';
                while ( $my_query->have_posts() ) {
                    $my_query->the_post();
                    ?>
                    <p>
                        <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                    </p>
                <?php
                }
                wp_reset_postdata();
            }
        }
    }

    public static function nanga_check_user_role( $role, $user_id = null ) {
        if ( is_numeric( $user_id ) ) {
            $user = get_userdata( $user_id );
        } else {
            $user = wp_get_current_user();
        }
        if ( empty( $user ) ) {
            return false;
        }

        return in_array( $role, (array) $user->roles );
    }

    public static function nanga_get_current_url() {
        global $wp;

        return home_url( add_query_arg( array(), $wp->request ) );
    }

    /**
     * Usage: cache_fragment_output( 'unique-key', 3600, function () { functions_that_do_stuff_live(); these_should_echo(); });
     *
     * @param $key
     * @param $ttl
     * @param $function
     */
    public function nanga_cache_fragment_output( $key, $ttl, $function ) {
        $group  = 'fragment-cache';
        $output = wp_cache_get( $key, $group );
        if ( empty( $output ) ) {
            ob_start();
            call_user_func( $function );
            $output = ob_get_clean();
            wp_cache_add( $key, $output, $group, $ttl );
        }
        echo $output;
    }
}
