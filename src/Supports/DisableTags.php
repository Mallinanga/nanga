<?php

namespace Nanga\Supports;

class DisableTags
{

    public static function init()
    {
        add_action('init', [self::class, 'taxonomy'], 1);
        add_filter('rewrite_rules_array', [self::class, 'rewrites']);
        add_filter('customize_nav_menu_available_item_types', [self::class, 'customizer']);
    }

    public static function taxonomy()
    {
        // global $wp_taxonomies;
        // register_taxonomy('post_tag', []);
        // unset($wp_taxonomies['post_tag']);
        unregister_taxonomy_for_object_type('post_tag', 'post');
    }

    public static function rewrites($rules)
    {
        $base   = get_option('tag_base');
        $needle = ($base) ? $base : 'tag';
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
            if ('post_tag' === $type['object']) {
                unset($types[$key]);
            }
        }

        return $types;
    }
}
