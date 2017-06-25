<?php

namespace Nanga\Features;

class Pages
{

    public static function init()
    {
        add_filter('manage_pages_columns', [self::class, 'columns'], 100);
    }

    public static function columns($columns)
    {
        unset($columns['date']);

        return $columns;
    }
}
