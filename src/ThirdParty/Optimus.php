<?php

namespace Nanga\ThirdParty;

class Optimus
{

    public static function init()
    {
        if ( ! is_admin() || current_user_can('manage_options')) {
            return;
        }
        // remove_action('all_admin_notices', ['Optimus_HQ', 'optimus_hq_notice']);
        remove_action('manage_media_custom_column', ['Optimus_Media', 'manage_column'], 10, 2);
        remove_filter('manage_media_columns', ['Optimus_Media', 'manage_columns']);
    }
}
