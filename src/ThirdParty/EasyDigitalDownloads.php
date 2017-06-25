<?php

namespace Nanga\ThirdParty;

class EasyDigitalDownloads
{

    public static function init()
    {
        if ( ! class_exists('Easy Digital Downloads')) {
            return;
        }
        add_filter('edd_api_log_requests', '__return_false');
        remove_action('plugins_loaded', ['EDD_Heartbeat', 'init']);
        remove_action('wp_head', 'edd_version_in_header');
    }
}
