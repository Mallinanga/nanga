<?php

namespace Nanga\ThirdParty;

class WPMigrateDB
{

    public static function init()
    {
        if ( ! class_exists('WPMDB')) {
            return;
        }
        add_filter('wpmdb_preserved_options', [self::class, 'keep']);
    }

    public static function keep($options)
    {
        $options[] = 'acf_pro_license';

        return $options;
    }
}
