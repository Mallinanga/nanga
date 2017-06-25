<?php

namespace Nanga\Supports;

class DisableAuthors
{

    public static function init()
    {
        add_action('init', [self::class, 'forPages']);
        add_filter('author_rewrite_rules', '__return_empty_array');
    }

    public static function forPages()
    {
        remove_post_type_support('page', 'author');
    }
}
