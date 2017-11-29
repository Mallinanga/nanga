<?php

namespace Nanga\Features;

class Media
{

    public static function init()
    {
        // add_filter('rewrite_rules_array', [self::class, 'rewrites']);
        add_action('init', [self::class, 'supports'], 1);
        add_filter('manage_media_columns', [self::class, 'columns'], 100);
        // add_action('admin_init', [self::class, 'metaboxes']);
        add_filter('jpeg_quality', [self::class, 'quality']);
        add_filter('wp_editor_set_quality', [self::class, 'quality']);
        add_filter('use_default_gallery_style', '__return_false');
    }

    public static function rewrites($rules)
    {
        foreach ($rules as $rule => $rewrite) {
            if (preg_match('/.*attachment=/', $rewrite)) {
                unset($rules[$rule]);
            }
        }

        return $rules;
    }

    public static function supports()
    {
        remove_post_type_support('attachment', 'comments');
    }

    public static function columns($columns)
    {
        unset($columns['author']);
        // unset($columns['parent']);

        return $columns;
    }

    public static function metaboxes()
    {
        remove_meta_box('authordiv', 'attachment', 'normal');
    }

    public static function quality()
    {
        return 100;
    }
}
