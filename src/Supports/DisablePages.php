<?php

namespace Nanga\Supports;

class DisablePages
{

    public static function init()
    {
        add_filter('page_rewrite_rules', '__return_empty_array');
        add_action('admin_menu', [self::class, 'menu']);
        add_filter('customize_nav_menu_available_item_types', [self::class, 'customizer']);
        // TODO exclude pages from search
    }

    public static function menu()
    {
        remove_menu_page('edit.php?post_type=page');
    }

    public static function customizer($types)
    {
        foreach ($types as $key => $type) {
            if ('page' === $type['object']) {
                unset($types[$key]);
            }
        }

        return $types;
    }
}
