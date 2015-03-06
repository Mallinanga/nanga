<?php

class Nanga_Deactivator {
    public static function deactivate() {
        wp_clear_scheduled_hook( 'nanga_task_1' );
        wp_clear_scheduled_hook( 'nanga_task_2' );
        wp_clear_scheduled_hook( 'nanga_task_3' );
        delete_option( 'nanga_maintenance_mode' );
        //$playground = get_page_by_title( 'Playground' );
        //wp_delete_post( $playground->ID, true );
        flush_rewrite_rules();
    }
}
