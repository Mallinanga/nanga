<?php

class Nanga_Plugin_Control {
    static $instance;
    private $disabled = array();

    public function __construct( Array $disables = null ) {
        if ( is_array( $disables ) ) {
            foreach ( $disables as $disable ) {
                $this->disable( $disable );
            }
        }
        add_filter( 'option_active_plugins', array( $this, 'do_disabling' ) );
        self::$instance = $this;
    }

    public function disable( $file ) {
        $this->disabled[] = $file;
    }

    public function do_disabling( $plugins ) {
        if ( count( $this->disabled ) ) {
            foreach ( (array) $this->disabled as $plugin ) {
                if ( 'option_active_plugins' == current_filter() ) {
                    $key = array_search( $plugin, $plugins );
                } else {
                    $key = ! empty( $plugins[ $plugin ] ) ? $plugin : false;
                }
                if ( false !== $key ) {
                    unset( $plugins[ $key ] );
                }
            }
        }

        return $plugins;
    }
}
