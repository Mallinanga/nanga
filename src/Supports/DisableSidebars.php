<?php

namespace Nanga\Supports;

class DisableSidebars
{

    public static function init()
    {
        // remove_theme_support('widgets');
        remove_action('init', 'wp_widgets_init', 1);
    }
}
