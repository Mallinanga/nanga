<?php

namespace Nanga\Supports;

class DisableEmbeds
{

    public static function init()
    {
        remove_action('rest_api_init', 'wp_oembed_register_route');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        add_filter('rewrite_rules_array', [self::class, 'rewrites']);
    }

    public static function rewrites($rules)
    {
        foreach ($rules as $rule => $rewrite) {
            if (preg_match('/.*embed=/', $rewrite)) {
                unset($rules[$rule]);
            }
        }

        return $rules;
    }
}
