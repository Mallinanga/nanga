<?php

namespace Nanga\Supports;

class DisableComments
{

    public static function init()
    {
        add_action('init', [self::class, 'forPosts']);
        add_filter('comments_rewrite_rules', '__return_empty_array');
        add_filter('rewrite_rules_array', [self::class, 'rewrites']);
        add_action('admin_menu', [self::class, 'menu']);
    }

    public static function forPosts()
    {
        remove_post_type_support('post', 'comments');
    }

    public static function rewrites($rules)
    {
        foreach ($rules as $rule => $rewrite) {
            if (preg_match('/.*cpage=/', $rewrite)) {
                unset($rules[$rule]);
            }
        }

        return $rules;
    }

    public static function menu()
    {
        remove_menu_page('edit-comments.php');
    }
}
