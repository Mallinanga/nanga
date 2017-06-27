<?php

namespace Nanga\Features;

use WP_REST_Response;
use WP_REST_Server;

class API
{

    public static function init()
    {
        add_action('rest_api_init', [self::class, 'routes']);
    }

    public static function routes()
    {
        register_rest_route('nanga/v1', '/diagnostics/', [
            'methods'  => WP_REST_Server::READABLE,
            'callback' => [self::class, 'diagnostics'],
        ]);
    }

    public static function diagnostics()
    {
        $response = [
            'nanga' => (bool)get_option('nanga_plugin_activated'),
        ];

        return new WP_REST_Response($response);
    }
}
