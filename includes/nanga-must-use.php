<?php
add_filter( 'got_rewrite', '__return_true', 999 );
add_action( 'wp_login_failed', function () {
    status_header( 403 );
} );
add_filter( 'xmlrpc_methods', function ( $methods ) {
    return false;
} );
/*
add_action( 'admin_init', function () {
    if ( ! is_plugin_active( 'nanga/nanga.php' ) ) {
        wp_remote_post(
            'https://api.pushover.net/1/messages.json',
            array(
                'method'  => 'POST',
                'timeout' => 15,
                'body'    => array(
                    'user'     => PUSHOVER_USER,
                    'token'    => PUSHOVER_TOKEN,
                    'device'   => PUSHOVER_DEVICE,
                    'priority' => '1',
                    'sound'    => 'bike',
                    'title'    => 'Plugin Deactivated',
                    'message'  => get_site_url(),
                ),
            )
        );
    }
} );
*/
