<?php

namespace Nanga\ThirdParty;

class Akismet
{

    public static function init()
    {
        if ( ! class_exists('Akismet')) {
            return;
        }
        add_filter('akismet_debug_log', '__return_false');
    }
}
