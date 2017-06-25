<?php

namespace Nanga\Supports;

class DisablePosts
{

    public static function init()
    {
        // add_action('init', [self::class, 'disable'], 1);
        add_filter('post_rewrite_rules', '__return_empty_array');
        add_action('admin_menu', [self::class, 'menu']);
        add_filter('customize_nav_menu_available_item_types', [self::class, 'customizer']);
        // TODO exclude posts from search
    }

    public static function disable()
    {
        global $wp_post_types;
        unset($wp_post_types['post']);
    }

    public static function menu()
    {
        remove_menu_page('edit.php');
    }

    public static function customizer($types)
    {
        foreach ($types as $key => $type) {
            if ('post' === $type['object']) {
                unset($types[$key]);
            }
        }

        return $types;
    }
}
