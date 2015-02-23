<?php
add_filter( 'got_rewrite', '__return_true', 999 );
add_filter( 'wp_headers', function ( $headers ) {
    unset( $headers['X-Pingback'] );
} );
add_action( 'wp_login_failed', function () {
    status_header( 403 );
} );

class Nanga_Kill_Email {
    static $instance;

    public function __construct() {
        self::$instance = $this;
        add_action( 'phpmailer_init', array(
            $this,
            'maybe_kill_email',
        ) );
    }

    public function maybe_kill_email( $phpmailer ) {
        if ( 'New WordPress Site' === $phpmailer->Subject ) {
            $phpmailer->ClearAllRecipients();
        }
    }
}

new Nanga_Kill_Email;

class Nanga_Plugin_Control {
    static $instance;
    private $disabled = array();

    public function __construct( Array $disables = null ) {
        if ( is_array( $disables ) ) {
            foreach ( $disables as $disable ) {
                $this->disable( $disable );
            }
        }
        add_filter( 'option_active_plugins', array(
            $this,
            'do_disabling'
        ) );
        self::$instance = $this;
    }

    public function disable( $file ) {
        $this->disabled[] = $file;
    }

    public function do_disabling( $plugins ) {
        if ( count( $this->disabled ) ) {
            foreach ( (array) $this->disabled as $plugin ) {
                $key = array_search( $plugin, $plugins );
                if ( false !== $key ) {
                    unset( $plugins[ $key ] );
                }
            }
        }

        return $plugins;
    }
}

//$plugin_control = new Nanga_Plugin_Control( array( 'core-control.php' ) );
$plugin_control = new Nanga_Plugin_Control();
if ( defined( 'WP_ENV' ) && 'development' === WP_ENV ) {
    $plugin_control->disable( 'core-control.php' );
    $plugin_control->disable( 'underConstruction.php' );
}
if ( defined( 'WP_ENV' ) && 'development' !== WP_ENV ) {
    $plugin_control->disable( 'core-control.php' );
}
