<?php

namespace Nanga\Supports;

class DisableTrackbacks
{

    public static function init()
    {
        add_action('init', [self::class, 'forTypes']);
        add_filter('rewrite_rules_array', [self::class, 'rewrites']);
    }

    public static function forTypes()
    {
        remove_post_type_support('page', 'trackbacks');
        remove_post_type_support('post', 'trackbacks');
    }

    public static function rewrites($rules)
    {
        foreach ($rules as $rule => $rewrite) {
            if (preg_match('/.*tb=1/', $rewrite)) {
                unset($rules[$rule]);
            }
        }

        return $rules;
    }
}
