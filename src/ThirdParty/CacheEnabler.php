<?php

namespace Nanga\ThirdParty;

class CacheEnabler
{

    public static function init()
    {
        if ( ! class_exists('Cache_Enabler')) {
            return;
        }
        // add_action('admin_bar_menu', [self::class, 'node'], 99);
        add_filter('user_can_clear_cache', [self::class, 'cap']);
    }

    public static function cap()
    {
        return current_user_can('edit_pages');
    }

    public static function node($wp_admin_bar)
    {
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(add_query_arg('action', 'nanga-tools__flush-page-cache', admin_url('index.php'))),
            'id'     => 'nanga-tools__flush-page-cache',
            'parent' => 'nanga-tools',
            'title'  => 'Flush Page Cache',
        ]);
    }
}
