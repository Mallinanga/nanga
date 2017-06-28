<?php

namespace Nanga\Supports;

class DisableCategories
{

    public static function init()
    {
        add_action('init', [self::class, 'taxonomy'], 1);
        // add_filter('rewrite_rules_array', [self::class, 'rewrites']);
        add_filter('customize_nav_menu_available_item_types', [self::class, 'customizer']);
    }

    public static function taxonomy()
    {
        // global $wp_taxonomies;
        // register_taxonomy('category', []);
        // unset($wp_taxonomies['category']);
        unregister_taxonomy_for_object_type('category', 'post');
    }

    public static function rewrites($rules)
    {
        // TODO this removes even rewrite rules from custom post types that contain the word category
        $base   = get_option('category_base');
        $needle = ($base) ? $base : 'category';
        foreach ($rules as $rule => $rewrite) {
            if (preg_match('/(' . $needle . ')/', $rule)) {
                unset($rules[$rule]);
            }
        }

        return $rules;
    }

    public static function customizer($types)
    {
        foreach ($types as $key => $type) {
            if ('category' === $type['object']) {
                unset($types[$key]);
            }
        }

        return $types;
    }
}
