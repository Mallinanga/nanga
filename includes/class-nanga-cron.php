<?php

class Nanga_Cron {
    private $nanga;
    private $version;

    public function __construct( $nanga, $version ) {
        $this->nanga   = $nanga;
        $this->version = $version;
    }

    public function intervals( $schedules ) {
        $schedules['weekly']  = array(
            'interval' => 604800,
            'display'  => __( 'Once Weekly', $this->nanga ),
        );
        $schedules['monthly'] = array(
            'interval' => 2635200,
            'display'  => __( 'Once Monthly', $this->nanga ),
        );

        return $schedules;
    }

    public function maybe_purge_transients( $older_than = '15 minutes', $safemode = true ) {
        global $wpdb;
        $older_than_time = strtotime( '-' . $older_than );
        if ( $older_than_time > time() || $older_than_time < 1 ) {
            return false;
        }
        //$transients =    $wpdb->get_col( $wpdb->prepare( "SELECT REPLACE(option_name, '_transient_timeout_', '') AS transient_name FROM $wpdb->options WHERE option_name LIKE '\_transient\_timeout\__%%' AND option_value < %s", $older_than_time ) );
        $site_transients = $wpdb->get_col( $wpdb->prepare( "SELECT REPLACE(option_name, '_site_transient_timeout_', '') AS transient_name FROM $wpdb->options WHERE option_name LIKE '_site_transient_timeout__%%' AND option_value < %s", $older_than_time ) );
        $transients      = $wpdb->get_col( $wpdb->prepare( "SELECT REPLACE(option_name, '_transient_timeout_', '') AS transient_name FROM $wpdb->options WHERE option_name LIKE '_transient_timeout__%%' AND option_value < %s", $older_than_time ) );
        if ( $safemode ) {
            foreach ( $site_transients as $site_transient ) {
                write_log( $site_transient );
                //delete_site_transient( $site_transient );
            }
            foreach ( $transients as $transient ) {
                write_log( $transient );
                //delete_transient( $transient );
            }
        }

        return $transients;
    }
}
