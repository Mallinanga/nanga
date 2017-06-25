<?php

namespace Nanga\Supports;

class DisablePostFormats
{

    public static function init()
    {
        add_action('init', [self::class, 'taxonomy'], 1);
        add_filter('rewrite_rules_array', [self::class, 'rewrites']);
    }

    public static function taxonomy()
    {
        // global $wp_taxonomies;
        // unset($wp_taxonomies['link_category']);
        // unset($wp_taxonomies['post_format']);
        remove_post_type_support('post', 'post-formats');
    }

    public static function rewrites($rules)
    {
        foreach ($rules as $rule => $rewrite) {
            if (preg_match('/.*post_format=/', $rewrite)) {
                unset($rules[$rule]);
            }
        }

        return $rules;
    }
}
