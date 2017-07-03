<?php

namespace Nanga\Supports;

class Legacy
{

    public static function init()
    {
        self::filters();
        add_action('wp_enqueue_scripts', [self::class, 'assets']);
        add_action('after_setup_theme', [self::class, 'supports']);
        add_filter('body_class', [self::class, 'classesBody']);
        add_filter('post_class', [self::class, 'classesPost'], 10, 3);
        add_filter('get_image_tag_class', [self::class, 'classesImage'], 10, 4);
        add_filter('the_content', [self::class, 'removeParagraphFromImages']);
    }

    private static function filters()
    {
        remove_all_filters('comment_flood_filter');
        remove_filter('comment_text', 'make_clickable', 9);
        remove_filter('comments_open', '_close_comments_for_old_post', 10, 2);
        remove_filter('pings_open', '_close_comments_for_old_post', 10, 2);
        add_filter('comment_flood_filter', '__return_false', 10, 3);
        add_filter('enable_post_by_email_configuration', '__return_false', 100);
        add_filter('sanitize_user', 'strtolower');
        add_filter('widget_text', 'do_shortcode');
    }

    public static function assets()
    {
        wp_enqueue_style('nanga', NANGA_DIR_URL . 'assets/css/nanga-legacy.css', [], NANGA_VERSION, 'all');
        if ( ! is_admin()) {
            wp_deregister_script('jquery');
            wp_register_script('jquery', '//cdn.jsdelivr.net/jquery/2.1.3/jquery.min.js', [], null, false);
            wp_enqueue_script('jquery');
        }
        wp_enqueue_script('nanga', NANGA_DIR_URL . 'assets/js/nanga-legacy.js', ['jquery'], NANGA_VERSION, true);
        $legacy = [
            'ajax_url'     => admin_url('admin-ajax.php'),
            'current_user' => get_current_user_id(),
            'environment'  => (defined('WP_ENV')) ? WP_ENV : null,
            'locale'       => get_locale(),
            'nonce'        => wp_create_nonce(),
        ];
        wp_localize_script('nanga', 'nanga', $legacy);
    }

    public static function supports()
    {
        register_nav_menus([
            'primary' => 'Primary Menu',
            'footer'  => 'Footer Menu',
        ]);
    }

    public static function classesBody($classes)
    {
        global $wp_query;
        $badClasses = [];
        if (is_page()) {
            $pageId       = $wp_query->get_queried_object_id();
            $badClasses[] = 'page-id-' . $pageId;
            $ancestors    = get_ancestors(get_queried_object_id(), 'page');
            if ( ! empty ($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $badClasses[] = 'parent-pageid-' . $ancestor;
                }
            }
            $classes[] = str_replace('.php', '', basename(get_page_template()));
        }
        if (is_single()) {
            $postId       = $wp_query->get_queried_object_id();
            $badClasses[] = 'postid-' . $postId;
        }
        if (is_author()) {
            $authorId     = $wp_query->get_queried_object_id();
            $badClasses[] = 'author-' . $authorId;
        }
        if (is_category()) {
            $catId        = $wp_query->get_queried_object_id();
            $badClasses[] = 'category-' . $catId;
        }
        if (is_tax()) {
            $ancestors = get_ancestors(get_queried_object_id(), get_queried_object()->taxonomy);
            if ( ! empty($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $term      = get_term($ancestor, get_queried_object()->taxonomy);
                    $classes[] = esc_attr("parent-$term->taxonomy-$term->term_id");
                }
            }
        }
        if (is_single() || is_page() && ! is_front_page()) {
            $classes[] = 'slug-' . basename(get_permalink());
        }
        $badClasses[] = 'page-template-default';
        $badClasses[] = 'page-id-' . get_option('page_on_front');
        $classes      = array_diff($classes, $badClasses);

        return $classes;
    }

    public static function classesPost($classes, $class, $postId)
    {
        global $wp_query;
        $badClasses = [
            'page',
            'post',
            'post-' . $postId,
            'status-publish',
        ];
        $classes    = array_diff($classes, $badClasses);
        if (0 == $wp_query->current_post) {
            $classes[] = 'first-post';
        }

        return $classes;
    }

    public static function classesImage($classes, $imageId, $align, $size)
    {
        $classes = str_replace(' wp-image-' . $imageId, '', $classes);
        $classes = $classes . ' image-in-content';

        return $classes;
    }

    public static function removeParagraphFromImages($content)
    {
        return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
    }
}
