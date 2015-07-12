<?php

class Nanga_Deactivator {
    public static function deactivate() {
        if ( wp_next_scheduled( 'nanga_maybe_purge_transients' ) ) {
            wp_clear_scheduled_hook( 'nanga_maybe_purge_transients' );
        }
        delete_option( 'nanga_maintenance_mode' );
        flush_rewrite_rules();
    }
}
