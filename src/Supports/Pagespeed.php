<?php

namespace Nanga\Supports;

class Pagespeed
{

    public static function init()
    {
        if ( ! nanga_user_is_superadmin()) {
            return;
        }
        add_filter('nanga_settings_tabs', [self::class, 'tab']);
        add_action('admin_enqueue_scripts', [self::class, 'assets'], 101);
        add_action('wp_dashboard_setup', function () {
            add_meta_box('nanga-pagespeed', 'Pagespeed', [self::class, 'vg_pagespeed_widget'], 'dashboard', 'side', 'low');
        });
        add_action('wp_ajax_vg_pagespeed_fetch_score', [self::class, 'vg_pagespeed_fetch_score']);
    }

    public static function tab($tabs)
    {
        $tabs['pagespeed'] = [
            'icon'  => 'dashicons-performance',
            'show'  => true,
            'slug'  => 'pagespeed',
            'title' => 'Pagespeed',
        ];

        return $tabs;
    }

    public static function assets($screen)
    {
        if ('index.php' === $screen || 'settings_page_nanga-settings' === $screen) {
            wp_enqueue_style('nanga-pagespeed', NANGA_DIR_URL . 'assets/css/nanga-pagespeed.css', [], NANGA_VERSION);
            wp_enqueue_script('nanga-pagespeed', NANGA_DIR_URL . 'assets/js/nanga-pagespeed.js', ['jquery'], NANGA_VERSION, true);
            wp_localize_script('nanga-pagespeed', 'nangaPagespeed', [
                'key'    => (defined('GOOGLE_API_KEY')) ? GOOGLE_API_KEY : null,
                'locale' => get_locale(),
            ]);
        }
    }

    public static function vg_pagespeed_widget()
    {
        $urls = self::vg_pagespeed();
        ?>
        <div id="nanga-pagespeed">
            <table>
                <?php /*
                <thead>
                <tr>
                    <td></td>
                    <td class="nanga-pagespeed__actions"></td>
                    <td class="nanga-pagespeed__score">Desktop</td>
                    <td class="nanga-pagespeed__score">Mobile</td>
                </tr>
                </thead>
                */ ?>
                <tbody>
                <?php
                $i = 1;
                foreach ($urls as $url) {
                    echo '<tr>';
                    echo '<td class="nanga-pagespeed__url"><a href="https://developers.google.com/speed/pagespeed/insights/?url=' . $url . '&tab=desktop" target="_blank" style="">' . $url . $url . $url . '</a></td>';
                    echo '<td class="nanga-pagespeed__actions" data-report="desktop-' . $i . '">Recalculate</td>';
                    echo '<td class="nanga-pagespeed__score"><div class="score dashicons dashicons-image-rotate" data-url="' . $url . '"></div></td>';
                    echo '<td class="nanga-pagespeed__score"><div class="score dashicons dashicons-image-rotate" data-url="' . $url . '"></div></td>';
                    echo '</tr>';
                    echo '<tr class="nanga-pagespeed__report nanga-pagespeed__report--desktop-' . $i . '"><td colspan="4">Desktop Report</td></tr>';
                    echo '<tr class="nanga-pagespeed__report nanga-pagespeed__report--mobile-' . $i . '"><td colspan="4">Mobile Report</td></tr>';
                    $i++;
                }
                ?>
                </tbody>
            </table>
            <p></p>
            <a href="<?php echo wp_nonce_url(admin_url('index.php?action=nanga-fetch-all-pagespeed-scores')); ?>" class="button button-primary--">Fetch all scores</a>
        </div>
        <?php
    }

    public static function vg_pagespeed()
    {
        $urls  = [];
        $types = get_post_types([
            //'_builtin' => true,
            'public' => true,
            //'publicly_queryable' => true,
        ]);
        unset($types['attachment']);
        $customTypes = get_post_types([
            '_builtin'           => false,
            //'public'           => true,
            'publicly_queryable' => true,
        ]);
        foreach ($customTypes as $customType) {
            //$urls[] = get_post_type_archive_link($customType);
        }
        $posts = new \WP_Query([
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'post_type'              => $types,
            'posts_per_page'         => 500,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);
        if ( ! empty($posts->posts)) {
            foreach ($posts->posts as $id) {
                $urls[] = get_permalink($id);
            }
        }
        $taxonomies = get_taxonomies([
            'public' => true,
        ]);
        unset($taxonomies['nav_menu']);
        $terms = new \WP_Term_Query([
            'fields'                 => 'ids',
            'get'                    => 'all',
            'hide_empty'             => false,
            'taxonomy'               => $taxonomies,
            'update_term_meta_cache' => false,
        ]);
        if ( ! empty($terms->terms)) {
            foreach ($terms->terms as $term) {
                $urls[] = get_term_link($term);
            }
        }
        //array_values(array_filter($urls));
        sort($urls);
        array_unique($urls);

        return $urls;
    }

    public static function vg_pagespeed_fetch_score()
    {
        $url     = $_REQUEST['url'];
        $request = wp_remote_get('https://www.googleapis.com/pagespeedonline/v2/runPagespeed?key=' . GOOGLE_API_KEY . '&url=' . $url . '&locale=' . get_locale());
        //$request = new \WP_Error();
        if ( ! is_wp_error($request)) {
            $body      = wp_remote_retrieve_body($request);
            $pagespeed = json_decode($body);
            wp_send_json_success($pagespeed->ruleGroups->SPEED->score);
        }
        wp_send_json_error('Error');
    }
}
