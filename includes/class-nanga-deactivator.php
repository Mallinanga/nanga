<?php

class Nanga_Deactivator {
    public static function deactivate() {
        if ( wp_next_scheduled( 'nanga_hourly_schedule' ) ) {
            wp_clear_scheduled_hook( 'nanga_hourly_schedule' );
        }
        if ( wp_next_scheduled( 'nanga_twicedaily_schedule' ) ) {
            wp_clear_scheduled_hook( 'nanga_twicedaily_schedule' );
        }
        if ( wp_next_scheduled( 'nanga_daily_schedule' ) ) {
            wp_clear_scheduled_hook( 'nanga_daily_schedule' );
        }
        if ( wp_next_scheduled( 'nanga_weekly_schedule' ) ) {
            wp_clear_scheduled_hook( 'nanga_weekly_schedule' );
        }
        if ( wp_next_scheduled( 'nanga_monthly_schedule' ) ) {
            wp_clear_scheduled_hook( 'nanga_monthly_schedule' );
        }
        delete_option( 'nanga_maintenance_mode' );
        delete_transient( 'nanga_cached_version' );
        flush_rewrite_rules();
    }
}
