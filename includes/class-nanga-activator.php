<?php

class Nanga_Activator {
    public static function activate() {
        /*
        $playground_title    = 'Playground';
        $playground_content  = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sit amet nunc id ligula posuere adipiscing. Ut non ligula vitae ante faucibus eleifend. Vivamus sed fermentum lacus, et porta nibh. Donec consectetur ultrices turpis, sit amet aliquet erat aliquet eu. Quisque molestie sed lacus tincidunt lobortis. Aliquam fringilla, dui ut luctus luctus, nibh libero lacinia diam, et tempus sem erat id orci. Nunc egestas purus nibh, sed rutrum magna accumsan a. In in fermentum lectus, nec feugiat velit. Suspendisse lobortis malesuada justo, vitae consequat augue placerat et. Aliquam congue porta tristique. Vivamus pulvinar ipsum dui, ut interdum massa dictum ac. Vestibulum tincidunt tincidunt lorem at egestas. Phasellus consectetur lobortis facilisis.';
        $playground_template = false;
        $playground_check    = get_page_by_title( $playground_title );
        $playground_page     = array(
            'post_type'    => 'page',
            'post_title'   => $playground_title,
            'post_content' => $playground_content,
            'post_status'  => 'publish',
            'post_author'  => 1,
        );
        if ( ! isset( $playground_check->ID ) ) {
            $playground_id = wp_insert_post( $playground_page );
            if ( ! empty( $playground_template ) ) {
                update_post_meta( $playground_id, '_wp_page_template', $playground_template );
            }
        }
        */
        /*
        add_action( 'after_setup_theme', function () {
            if ( ! get_option( 'nanga_cleared_widgets' ) ) {
                update_option( 'sidebars_widgets', array() );
                update_option( 'nanga_cleared_widgets', true );
            }
        } );
        */
        if ( ! wp_next_scheduled( 'nanga_hourly_schedule' ) ) {
            wp_schedule_event( time(), 'hourly', 'nanga_hourly_schedule' );
        }
        if ( ! wp_next_scheduled( 'nanga_twicedaily_schedule' ) ) {
            wp_schedule_event( time(), 'twicedaily', 'nanga_twicedaily_schedule' );
        }
        if ( ! wp_next_scheduled( 'nanga_daily_schedule' ) ) {
            wp_schedule_event( time(), 'daily', 'nanga_daily_schedule' );
        }
        if ( ! wp_next_scheduled( 'nanga_weekly_schedule' ) ) {
            wp_schedule_event( time(), 'weekly', 'nanga_weekly_schedule' );
        }
        if ( ! wp_next_scheduled( 'nanga_monthly_schedule' ) ) {
            wp_schedule_event( time(), 'monthly', 'nanga_monthly_schedule' );
        }
        if ( ! get_option( 'nanga_plugin_activated' ) ) {
            update_option( 'avatar_default', 'blank' );
            update_option( 'blog_public', 0 );
            update_option( 'blogdescription', '' );
            update_option( 'comment_max_links', 1 );
            update_option( 'comments_notify', 0 );
            update_option( 'date_format', 'd/m/Y' );
            update_option( 'default_comment_status', 'closed' );
            update_option( 'default_ping_status', 'closed' );
            update_option( 'gform_enable_noconflict', 0 );
            update_option( 'gzipcompression', 1 );
            update_option( 'image_default_link_type', 'none' );
            update_option( 'imsanity_bmp_to_jpg', 1 );
            update_option( 'imsanity_max_height', 1200 );
            update_option( 'imsanity_max_height_library', 1200 );
            update_option( 'imsanity_max_height_other', 0 );
            update_option( 'imsanity_max_width', 1200 );
            update_option( 'imsanity_max_width_library', 1200 );
            update_option( 'imsanity_max_width_other', 0 );
            update_option( 'imsanity_quality', 90 );
            update_option( 'large_crop', 1 );
            update_option( 'large_size_h', 0 );
            update_option( 'mailserver_login', '' );
            update_option( 'mailserver_pass', '' );
            update_option( 'mailserver_port', 0 );
            update_option( 'mailserver_url', '' );
            update_option( 'medium_crop', 1 );
            update_option( 'medium_size_h', 150 );
            update_option( 'medium_size_h', 150 );
            update_option( 'medium_size_w', 150 );
            update_option( 'medium_size_w', 150 );
            update_option( 'moderation_notify', 0 );
            update_option( 'posts_per_page', 5 );
            update_option( 'posts_per_rss', 1 );
            update_option( 'rg_gforms_currency', 'EUR' );
            update_option( 'rg_gforms_disable_css', 1 );
            update_option( 'rg_gforms_enable_html5', 1 );
            update_option( 'rss_use_excerpt', 1 );
            update_option( 'show_avatars', 0 );
            update_option( 'show_on_front', 'page' );
            update_option( 'thread_comments', 0 );
            update_option( 'thumbnail_size_h', 100 );
            update_option( 'thumbnail_size_w', 100 );
            update_option( 'time_format', 'H:i' );
            update_option( 'timezone_string', 'Europe/Athens' );
            update_option( 'use_smilies', 0 );
        }
        update_option( 'nanga_plugin_activated', true );
        flush_rewrite_rules();
    }
}
