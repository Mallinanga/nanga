<?php

namespace Nanga\Supports;

class Support
{

    public static function init()
    {
        add_action('admin_bar_menu', [self::class, 'nodes'], 100);
    }

    public static function nodes($wp_admin_bar)
    {
        if ( ! current_user_can('edit_pages')) {
            return;
        }
        if ( ! nanga_site_is_external()) {
            $wp_admin_bar->add_node([
                'href'   => 'mailto:info@vgwebthings.com?subject=' . __('Support Request', 'nanga'),
                'id'     => 'nanga-support',
                'parent' => 'top-secondary',
                'title'  => __('Get Support', 'nanga'),
            ]);
        }
    }
}
