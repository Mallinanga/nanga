<?php

namespace Nanga\Supports;

class DisableSearch
{

    public static function init()
    {
        add_filter('search_rewrite_rules', '__return_empty_array');
    }
}
