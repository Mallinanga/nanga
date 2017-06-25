<?php

namespace Nanga\Supports;

class DisableREST
{

    public static function init()
    {
        remove_action('init', 'rest_api_init', 10);
        remove_action('wp_head', 'rest_output_link_wp_head');
    }
}
