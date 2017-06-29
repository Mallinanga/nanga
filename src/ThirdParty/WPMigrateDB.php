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
        add_filter('wpmdb_upload_info', [self::class, 'location']);
    }

    public static function keep($options)
    {
        $options[] = 'acf_pro_license';

        return $options;
    }

    public static function location()
    {
        return [
            'path' => ABSPATH . '_files',
            'url'  => site_url() . '/_files',
        ];
    }
}
