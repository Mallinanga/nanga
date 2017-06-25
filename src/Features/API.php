<?php

namespace Nanga\Features;

class API
{

    public static function init()
    {
        add_action('rest_api_init', [self::class, 'routes']);
    }

    public static function routes()
    {
        register_rest_route('nanga/v1', '/diagnostics/', [
            'methods'  => 'GET',
            'callback' => self::diagnostics(),
        ]);
    }

    public static function diagnostics($data)
    {
        return $data;
    }
}
