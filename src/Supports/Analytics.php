<?php

namespace Nanga\Supports;

class Analytics
{

    public static function init()
    {
        add_action('admin_bar_menu', [self::class, 'nodes'], 110);
        add_action('wp_dashboard_setup', [self::class, 'metabox']);
    }

    public static function nodes($wp_admin_bar)
    {
        if ( ! current_user_can('edit_pages')) {
            return;
        }
        $wp_admin_bar->add_menu([
            'id'     => 'nanga-analytics',
            'parent' => 'top-secondary',
            'title'  => __('Analytics', 'nanga'),
        ]);
        $links = [
            'Google Analytics'      => 'https://www.google.com/analytics/',
            'Google Search Console' => 'https://www.google.com/webmasters/tools/dashboard?siteUrl=' . site_url(),
        ];
        $i     = 1;
        foreach ($links as $label => $url) {
            $wp_admin_bar->add_node([
                'href'   => $url,
                'id'     => 'nanga-analytics__' . $i++,
                'meta'   => ['target' => '_blank'],
                'parent' => 'nanga-analytics',
                'title'  => $label,
            ]);
        }
    }

    public static function metabox()
    {
        if ( ! current_user_can('edit_pages')) {
            return;
        }
        add_meta_box('nanga-google-analytics', 'Google Analytics', [self::class, 'widget'], 'dashboard', 'side', 'high');
    }

    public static function widget()
    {
        include NANGA_DIR_PATH . 'views/google-analytics-widget.php';
        echo '<p><strong>' . __('Please insert a valid Google Analytics UA.', 'nanga') . '</strong></p>';
    }
}
