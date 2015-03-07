<?php
add_filter( 'got_rewrite', '__return_true', 999 );
add_action( 'wp_login_failed', function () {
    status_header( 403 );
} );
