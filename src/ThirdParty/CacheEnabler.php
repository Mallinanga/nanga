<?php

namespace Nanga\ThirdParty;

class CacheEnabler
{

    public static function init()
    {
        if ( ! class_exists('Cache_Enabler')) {
            return;
        }
        add_filter('user_can_clear_cache', function () {
            return current_user_can('edit_pages');
        });
    }
}
