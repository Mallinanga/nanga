<?php

namespace Nanga\Supports;

class DisableREST
{

    public static function init()
    {
        // remove_action('template_redirect', 'rest_output_link_header', 11, 0);
        remove_action('init', 'rest_api_init', 10);
        remove_action('wp_head', 'rest_output_link_wp_head', 10, 0);
    }
}
