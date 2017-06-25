<?php

namespace Nanga\Supports;

class DisableEmoji
{

    public static function init()
    {
        add_filter('emoji_svg_url', '__return_false');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('init', 'smilies_init', 5);
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('the_content', 'convert_smilies');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('the_excerpt', 'convert_smilies');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }
}
