<?php

namespace Nanga\Supports;

class DisableDates
{

    public static function init()
    {
        add_filter('date_rewrite_rules', '__return_empty_array');
    }
}
