<?php

namespace Nanga\Supports;

class DisableFeeds
{

    public static function init()
    {
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
        add_filter('rewrite_rules_array', [self::class, 'rewrites']);
    }

    public static function rewrites($rules)
    {
        foreach ($rules as $rule => $rewrite) {
            if (preg_match('/.*(feed)/', $rule)) {
                unset($rules[$rule]);
            }
        }

        return $rules;
    }
}
