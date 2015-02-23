<?php

class Nanga_Deactivator {
    public static function deactivate() {
        $playground = get_page_by_title( 'Playground' );
        wp_delete_post( $playground->ID, true );
        flush_rewrite_rules();
    }
}
