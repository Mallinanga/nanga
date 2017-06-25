<?php

namespace Nanga\Features;

class Frontend
{

    public static function init()
    {
        add_action('wp_enqueue_scripts', [self::class, 'assets']);
    }

    public static function assets()
    {
    }
}
