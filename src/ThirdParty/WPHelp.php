<?php

namespace Nanga\ThirdParty;

class WPHelp
{

    public static function init()
    {
        if ( ! class_exists('CWS_WP_Help_Plugin')) {
            return;
        }
        add_filter('cws_wp_help_option_defaults', [self::class, 'options']);
        add_filter('cws_wp_help_edit_documents_cap', [self::class, 'access']);
    }

    public static function options()
    {
        return [
            'h2'            => __('Help', 'nanga'),
            'h3'            => __('Index', 'nanga'),
            'menu_location' => 'bottom',
        ];
    }

    public static function access()
    {
        return 'manage_options';
    }
}
