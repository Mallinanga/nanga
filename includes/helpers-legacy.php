<?php
if ( ! function_exists('nanga_post_tweet_count')) {
    function nanga_post_tweet_count($post_id)
    {
        if ( ! ($count = get_transient('nanga_post_tweet_count_' . $post_id))) {
            $response = wp_remote_retrieve_body(wp_remote_get('https://cdn.api.twitter.com/1/urls/count.json?url=' . urlencode(get_permalink($post_id))));
            if (is_wp_error($response)) {
                return '0';
            }
            $json  = json_decode($response);
            $count = absint($json->count);
            set_transient('nanga_post_tweet_count_' . $post_id, absint($count), 30 * MINUTE_IN_SECONDS);
        }

        return absint($count);
    }
}
if ( ! function_exists('nanga_post_like_count')) {
    function nanga_post_like_count($post_id)
    {
        if ( ! ($count = get_transient('nanga_post_like_count_' . $post_id))) {
            $fql = 'SELECT url, ';
            //$fql .= 'share_count, like_count, comment_count';
            $fql      .= 'total_count ';
            $fql      .= "FROM link_stat WHERE url = '" . get_permalink($post_id) . "'";
            $response = wp_remote_retrieve_body(wp_remote_get('https://api.facebook.com/method/fql.query?format=json&query=' . urlencode($fql)));
            if (is_wp_error($response)) {
                return '0';
            }
            $json  = json_decode($response);
            $count = absint($json[0]->total_count);
            set_transient('nanga_post_like_count_' . $post_id, absint($count), 30 * MINUTE_IN_SECONDS);
        }

        return absint($count);
    }
}
if ( ! function_exists('nanga_post_pageview_count')) {
    function nanga_post_pageview_count($post_id)
    {
        if ( ! ($count = get_transient('nanga_post_pageview_count_' . $post_id))) {
            if (function_exists('stats_get_csv')) {
                $response = stats_get_csv('postviews', 'post_id=' . $post_id . '&period=month&limit=1');
                $count    = absint($response[0]['views']);
            } else {
                return '0';
            }
            set_transient('nanga_post_pageview_count_' . $post_id, absint($count), 30 * MINUTE_IN_SECONDS);
        }

        return absint($count);
    }
}
if ( ! function_exists('nanga_post_comment_count')) {
    function nanga_post_comment_count($post_id)
    {
        if ( ! ($count = get_transient('nanga_post_comment_count_' . $post_id))) {
            if (comments_open()) {
                $count = absint(get_comments_number($post_id));
            } else {
                return '0';
            }
            set_transient('nanga_post_comment_count_' . $post_id, absint($count), 30 * MINUTE_IN_SECONDS);
        }

        return absint($count);
    }
}
if ( ! function_exists('wp_is_mobile_phone')) {
    function wp_is_mobile_phone()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',
                $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
                substr($useragent, 0, 4))
        ) {
            return true;
        }

        return false;
    }
}
if ( ! function_exists('nanga_posted_on')) {
    function nanga_posted_on()
    {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
        }
        $time_string = sprintf($time_string, esc_attr(get_the_date('c')), esc_html(get_the_date()), esc_attr(get_the_modified_date('c')), esc_html(get_the_modified_date()));
        printf('<span class="posted-on">%1$s</span><span class="byline">%2$s</span>', sprintf('<a href="%1$s" rel="bookmark">%2$s</a>', esc_url(get_permalink()), $time_string), sprintf('<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_html(get_the_author())));
    }
}
if ( ! function_exists('nanga_pagination')) {
    function nanga_pagination($query)
    {
        if ($query == null) {
            global $wp_query;
            $query = $wp_query;
        }
        $big = 999999999;
        echo paginate_links([
            'base'    => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format'  => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total'   => $query->max_num_pages,
        ]);
    }
}
if ( ! function_exists('nanga_estimated_reading_time')) {
    function nanga_estimated_reading_time()
    {
        $post_object = get_post();
        $words       = str_word_count(strip_tags($post_object->post_content));
        $minutes     = floor($words / 120);
        $seconds     = floor($words % 120 / (120 / 60));
        if (1 <= $minutes) {
            $estimated_time = $minutes . ' minute' . ($minutes == 1 ? '' : 's') . ', ' . $seconds . ' second' . ($seconds == 1 ? '' : 's');
        } else {
            $estimated_time = $seconds . ' second' . ($seconds == 1 ? '' : 's');
        }

        return $estimated_time;
    }
}
if ( ! function_exists('nanga_related_posts')) {
    function nanga_related_posts()
    {
        global $post;
        $tags = wp_get_post_tags($post->ID);
        if ($tags) {
            $first_tag = $tags[0]->term_id;
            $my_query  = new WP_Query([
                'tag__in'          => [$first_tag],
                'post__not_in'     => [$post->ID],
                'showposts'        => 5,
                'caller_get_posts' => 1,
            ]);
            if ($my_query->have_posts()) {
                echo '<h3>Related Posts</h3>';
                while ($my_query->have_posts()) {
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
}
if ( ! function_exists('nanga_cache_fragment_output')) {
    /**
     * Usage: cache_fragment_output( 'unique-key', 3600, function () { functions_that_do_stuff_live(); these_should_echo(); });
     *
     * @param $key
     * @param $ttl
     * @param $function
     */
    function nanga_cache_fragment_output($key, $ttl, $function)
    {
        $group  = 'nanga-cache';
        $output = wp_cache_get($key, $group);
        if (empty($output)) {
            ob_start();
            call_user_func($function);
            $output = ob_get_clean();
            wp_cache_add($key, $output, $group, $ttl);
        }
        echo $output;
    }
}
