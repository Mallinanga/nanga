<?php

namespace Nanga\Features;

class Login
{

    public static function init()
    {
        add_action('login_head', [self::class, 'shake']);
        add_filter('login_headertitle', [self::class, 'title']);
        add_filter('login_headerurl', [self::class, 'link']);
    }

    public static function shake()
    {
        remove_action('login_head', 'wp_shake_js', 12);
    }

    public static function title()
    {
        return get_option('blogname');
    }

    public static function link()
    {
        return site_url();
    }
}
