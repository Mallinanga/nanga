<?php

class Nanga_Deactivator {
    public static function deactivate() {
        delete_option( 'nanga_cleared_widgets' );
        delete_option( 'nanga_maintenance_mode' );
        $playground = get_page_by_title( 'Playground' );
        wp_delete_post( $playground->ID, true );
        flush_rewrite_rules();
    }
}
