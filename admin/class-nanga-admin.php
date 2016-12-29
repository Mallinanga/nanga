<?php

class Nanga_Admin
{

    private $nanga;
    private $version;

    public function __construct($nanga, $version)
    {
        $this->nanga   = $nanga;
        $this->version = $version;
    }

    public function disable_shake()
    {
        remove_action('login_head', 'wp_shake_js', 12);
    }

    public function admin_post_thumbnail_html($html)
    {
        global $content_width;

        return $html .= __('<p>Click above to add an image to be displayed at the top of the content.<br><strong>The width of the image should be ' . $content_width . 'px</strong>!</p>', $this->nanga);
    }

    public function google_analytics_dashboard()
    {
        if (current_user_can('edit_pages') && current_theme_supports('nanga-analytics')) {
            add_dashboard_page('Google Analytics', 'Google Analytics', 'read', 'nanga-google-analytics-dashboard', [$this, 'google_analytics_dashboard_page']);
        }
    }

    public function google_analytics_dashboard_page()
    {
        include plugin_dir_path(dirname(__FILE__)) . 'admin/partials/nanga-google-analytics-dashboard.php';
    }

    public function google_analytics_widget()
    {
        if (current_user_can('edit_pages') && current_theme_supports('nanga-analytics')) {
            add_meta_box('google_analytics_widget', 'Google Analytics', [$this, 'google_analytics_widget_content'], 'dashboard', 'side', 'low');
        }
    }

    public function google_analytics_widget_content()
    {
        if (get_field('vg_google_analytics')) {
            include plugin_dir_path(dirname(__FILE__)) . 'admin/partials/nanga-google-analytics-widget.php';
        } else {
            printf('<p>' . __('Please insert a valid Google Analytics UA Code in %1$s.', $this->nanga) . '</p>', '<a href="' . admin_url('admin.php?page=general-settings') . '">settings</a>');
        }
    }

    public function plugin_settings_menu()
    {
        if (current_theme_supports('nanga-settings')) {
            add_menu_page(__('The Settings', $this->nanga), __('The Settings', $this->nanga), 'manage_options', 'nanga-settings.php', [$this, 'plugin_settings_page'], 'dashicons-shield', 666);
        }
    }

    public function plugin_settings_page()
    {
        include plugin_dir_path(dirname(__FILE__)) . 'admin/partials/nanga-settings-page.php';
    }

    public function limit_post_fields($fields, $query)
    {
        if ( ! is_admin() OR ! $query->is_main_query() OR (defined('DOING_AJAX') AND DOING_AJAX) OR (defined('DOING_CRON') AND DOING_CRON)) {
            return $fields;
        }
        $post_fields = $GLOBALS['wpdb']->posts;

        return implode(',', [
            "{$post_fields}.ID",
            "{$post_fields}.post_title",
            "{$post_fields}.post_date",
            "{$post_fields}.post_author",
            "{$post_fields}.post_name",
            "{$post_fields}.post_status",
            "{$post_fields}.comment_status",
            "{$post_fields}.ping_status",
            "{$post_fields}.post_password",
        ]);
    }

    public function post_date_column_time($h_time, $post)
    {
        if ('future' == $post->post_status) {
            $h_time .= '<br>' . get_post_time('g:i a', false, $post);
        }

        return $h_time;
    }

    public function jigsaw()
    {
        if (class_exists('Jigsaw')) {
            if (get_option('nanga_maintenance_mode')) {
                Jigsaw::show_notice('<strong>' . __('Site is in maintenance mode!', $this->nanga) . '</strong>', 'error');
            }
            Jigsaw::remove_column('page', 'author');
            Jigsaw::remove_column('post', 'categories');
            Jigsaw::remove_column('post', 'tags');
            if (current_user_can('manage_options')) {
                Jigsaw::add_toolbar_item('Clear Object Cache', function () {
                    wp_cache_flush();
                    Jigsaw::show_notice('Object Cache has been <strong>flushed</strong>', 'updated');
                }, 'nanga-tools');
                if (class_exists('TimberLoader')) {
                    Jigsaw::add_toolbar_item('Clear Timber Cache', function () {
                        TimberCommand::clear_cache_timber();
                        Jigsaw::show_notice('Timber Cache has been <strong>flushed</strong>', 'updated');
                    }, 'nanga-tools');
                    Jigsaw::add_toolbar_item('Clear Twig Cache', function () {
                        TimberCommand::clear_cache_twig();
                        Jigsaw::show_notice('Twig Cache has been <strong>flushed</strong>', 'updated');
                    }, 'nanga-tools');
                }
                Jigsaw::add_toolbar_item('Clear Update Cache', function () {
                    if (function_exists('wp_clean_update_cache')) {
                        wp_clean_update_cache();
                    }
                    Jigsaw::show_notice('Update Cache has been <strong>flushed</strong>', 'updated');
                }, 'nanga-tools');
                Jigsaw::add_toolbar_item('Force Updates', function () {
                    wp_maybe_auto_update();
                }, 'nanga-tools');
                Jigsaw::add_toolbar_item('Flush Rewrite Rules', function () {
                    flush_rewrite_rules();
                    Jigsaw::show_notice('Rewrite Rules have been <strong>flushed</strong>', 'updated');
                }, 'nanga-tools');
                Jigsaw::add_toolbar_item('Toggle Maintenance Mode', function () {
                    if (get_option('nanga_maintenance_mode')) {
                        update_option('nanga_maintenance_mode', 0);
                        Jigsaw::show_notice(__('Site is now live!', $this->nanga), 'updated');
                    } else {
                        update_option('nanga_maintenance_mode', 1);
                        Jigsaw::show_notice('<strong>' . __('Site is in maintenance mode!', $this->nanga) . '</strong>', 'error');
                    }
                }, 'nanga-tools');
            }
        }
    }

    public function image_quality()
    {
        return 100;
    }

    public function login_headerurl()
    {
        return home_url();
    }

    public function login_headertitle()
    {
        return get_option('blogname');
    }

    public function screen_options_show_screen()
    {
        return current_user_can('manage_options');
    }

    public function acf_settings_show_admin($show)
    {
        return current_user_can('manage_options');
    }

    public function dequeue_login_styles()
    {
        if (current_theme_supports('nanga-white-label-login')) {
            //wp_deregister_style( 'open-sans' );
            //wp_register_style( 'open-sans', false );
            //wp_deregister_style( 'dashicons' );
            //wp_register_style( 'dashicons', false );
            wp_deregister_style('buttons');
            wp_register_style('buttons', false);
        }
    }

    public function enqueue_login_styles()
    {
        if (current_theme_supports('nanga-white-label-login')) {
            wp_register_style($this->nanga . '-login', plugin_dir_url(__FILE__) . 'css/nanga-login.css', [], $this->version, 'all');
            wp_print_styles($this->nanga . '-login');
        }
        //@todo
        /*
        $site_logo = get_theme_mod( 'site_logo' );
        $handle    = @fopen( 'http://local.playground.paganis.net/media/2015/03/favicon.pn', 'r' );
        if ( ! $handle ) {
            write_log( 'No' );
            remove_theme_mod( 'site-logo' );
        }
        if ( $site_logo ) {
            if ( fopen( $site_logo, 'r' ) ) {
                echo '<style>.login h1{display:block;}.login h1 a{background-image:none,url(' . $site_logo . ');width:100%;height:200px;background-size:contain;}</style>';
            } else {
                remove_theme_mod( 'site-logo' );
            }
        }
        */
    }

    public function enqueue_styles($hook)
    {
        //wp_deregister_style( 'open-sans' );
        //wp_register_style( 'open-sans', false );
        wp_enqueue_style($this->nanga, plugin_dir_url(__FILE__) . 'css/nanga-admin.css', [], $this->version, 'all');
        if ('toplevel_page_nanga-settings' === $hook) {
            wp_enqueue_style($this->nanga . '-settings', plugin_dir_url(__FILE__) . 'css/nanga-settings.css', [], $this->version, 'all');
        }
    }

    public function enqueue_scripts($hook)
    {
        //echo '<script> WebFontConfig = {classes: false, google: {families: [\'Fira+Sans:300,400,500,700,300italic,400italic,500italic,700italic:latin,greek\']}}; (function () { var s; var wf = document.createElement(\'script\'); wf.src = (\'https:\' == document.location.protocol ? \'https\' : \'http\') + \'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js\'; wf.type = \'text/javascript\'; wf.async = \'true\'; s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(wf, s); })(); </script>';
        wp_enqueue_script($this->nanga, plugin_dir_url(__FILE__) . 'js/nanga-admin.js', ['jquery'], $this->version, true);
        wp_localize_script($this->nanga, $this->nanga, [
            'locale'       => get_locale(),
            'current_user' => get_current_user_id(),
            'environment'  => WP_ENV,
            //@todo
            //'gaid'         => get_field( 'vg_google_analytics_id' ),
        ]);
        if ('toplevel_page_nanga-settings' === $hook) {
        }
        if ('index.php' === $hook && current_theme_supports('nanga-support-request')) {
            wp_enqueue_script($this->nanga . '-support-request', plugin_dir_url(__FILE__) . 'js/nanga-support-form.js', ['jquery'], $this->version, true);
            wp_localize_script($this->nanga . '-support-request', $this->nanga . '_support_request', [
                'msg_success' => __('Thank you! Your request has been sent. We will get back at you as soon as possible.', $this->nanga),
                'msg_error'   => __('Oops! Something went wrong and we couldn\'t send your message.', $this->nanga),
            ]);
        }
    }

    public function plugin_action_links($links)
    {
        return array_merge(['advanced_settings' => '<a href="' . admin_url('admin.php?page=general-settings') . '">' . __('Settings', $this->nanga) . '</a>'], $links);
    }

    public function disable_admin_notices()
    {
        remove_action('admin_notices', 'update_nag', 3);
    }

    public function disable_pointers()
    {
        remove_action('admin_enqueue_scripts', [
            'WP_Internal_Pointers',
            'enqueue_scripts',
        ]);
    }

    public function disable_menus()
    {
        remove_menu_page('separator-last');
        remove_menu_page('separator1');
        remove_menu_page('separator2');
        remove_submenu_page('edit.php', 'post-new.php');
        remove_submenu_page('edit.php?post_type=page', 'post-new.php?post_type=page');
        remove_submenu_page('index.php', 'wp-ses/ses-stats.php');
        remove_submenu_page('plugins.php', 'plugin-install.php');
        remove_submenu_page('upload.php', 'media-new.php');
        remove_submenu_page('users.php', 'user-new.php');
        if (current_theme_supports('nanga-disable-posts')) {
            remove_menu_page('edit.php');
        }
        if ( ! current_user_can('manage_options')) {
            remove_menu_page('tools.php');
            remove_menu_page('profile.php');
        }
        if ( ! post_type_supports('post', 'comments')) {
            remove_menu_page('edit-comments.php');
        }
    }

    public function disable_widgets()
    {
        //unregister_widget( 'WP_Nav_Menu_Widget' );
        //unregister_widget( 'WP_Widget_Categories' );
        //unregister_widget( 'WP_Widget_Calendar' );
        unregister_widget('WP_Widget_Links');
        unregister_widget('WP_Widget_Meta');
        unregister_widget('WP_Widget_Pages');
        unregister_widget('WP_Widget_Recent_Comments');
        unregister_widget('WP_Widget_Recent_Posts');
        unregister_widget('WP_Widget_RSS');
        unregister_widget('WP_Widget_Search');
        unregister_widget('WP_Widget_Tag_Cloud');
    }

    public function force_dashboard_locale($locale)
    {
        if (is_admin() && ! defined('DOING_AJAX')) {
            return 'en_US';
        } else {
            return $locale;
        }
    }

    public function admin_color_scheme()
    {
        wp_admin_css_color($this->nanga, 'VG web things', plugin_dir_url(__FILE__) . 'css/nanga-admin-colors.css', ['#000000', '#0098ed', '#e1e1e1', '#ffffff']);
        remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
        add_filter('get_user_option_admin_color', function ($result) {
            return 'nanga';
        });
    }

    public function settings_page()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page([
                'page_title' => __('General Settings', $this->nanga),
                'menu_title' => __('Settings', $this->nanga),
                'menu_slug'  => 'general-settings',
                'redirect'   => false,
                'position'   => false,
                'icon_url'   => 'dashicons-forms',
            ]);
            acf_add_options_sub_page([
                'page_title'  => __('Advanced Settings', $this->nanga),
                'menu_title'  => __('Advanced Settings', $this->nanga),
                'menu_slug'   => 'advanced-settings',
                'parent_slug' => 'general-settings',
                'capability'  => 'manage_options',
            ]);
        }
    }

    public function admin_bar($wp_toolbar)
    {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
        remove_action('admin_bar_menu', 'wp_admin_bar_edit_menu', 80);
        remove_action('admin_bar_menu', 'wp_admin_bar_my_account_item', 7);
        remove_action('admin_bar_menu', 'wp_admin_bar_my_account_menu', 0);
        remove_action('admin_bar_menu', 'wp_admin_bar_new_content_menu', 70);
        remove_action('admin_bar_menu', 'wp_admin_bar_search_menu', 0);
        remove_action('admin_bar_menu', 'wp_admin_bar_search_menu', 4);
        remove_action('admin_bar_menu', 'wp_admin_bar_site_menu', 30);
        remove_action('admin_bar_menu', 'wp_admin_bar_updates_menu', 40);
        remove_action('admin_bar_menu', 'wp_admin_bar_wp_menu', 10);
        if (is_admin()) {
            $wp_toolbar->add_node([
                'id'     => 'nanga-logout',
                'parent' => 'top-secondary',
                'title'  => __('Logout', $this->nanga),
                'href'   => wp_logout_url(),
            ]);
            $wp_toolbar->add_node([
                'id'     => 'get-help',
                'parent' => 'top-secondary',
                'title'  => __('Get Support', $this->nanga),
            ]);
            $wp_toolbar->add_node([
                'href'   => 'mailto:info@vgwebthings.com?subject=' . __('Support Request', $this->nanga),
                'id'     => 'get-support',
                'parent' => 'get-help',
                'title'  => __('Email Support', $this->nanga),
            ]);
            if (current_theme_supports('nanga-support-request')) {
                $wp_toolbar->add_node([
                    'href'   => get_admin_url(),
                    'id'     => 'get-support-request',
                    'parent' => 'get-help',
                    'title'  => __('Create Support Request', $this->nanga),
                ]);
            }
            $links = [
                'Google Analytics'  => 'https://www.google.com/analytics/',
                'Webmaster Tools'   => 'https://www.google.com/webmasters/tools/dashboard?hl=en&siteUrl=' . home_url(),
                'Twitter Reactions' => 'https://twitter.com/search?q=' . $_SERVER['SERVER_NAME'],
            ];
            if (current_theme_supports('nanga-analytics')) {
                $analytics_page = admin_url('index.php?page=nanga-google-analytics-dashboard');
            } else {
                $analytics_page = false;
            }
            $wp_toolbar->add_menu([
                'href'   => $analytics_page,
                'id'     => 'nanga-links',
                'parent' => 'top-secondary',
                'title'  => __('Analytics', $this->nanga),
            ]);
            $count = 1;
            foreach ($links as $label => $url) {
                $wp_toolbar->add_node([
                    'href'   => $url,
                    'id'     => 'nanga-link_' . $count++,
                    'meta'   => ['target' => '_blank'],
                    'parent' => 'nanga-links',
                    'title'  => $label,
                ]);
            }
            if (current_user_can('manage_options')) {
                $wp_toolbar->add_menu([
                    'href'   => false,
                    'id'     => 'nanga-tools',
                    'parent' => 'top-secondary',
                    'title'  => __('Tools', $this->nanga),
                ]);
            }
            $wp_toolbar->add_node([
                'id'    => 'nanga-visit-site',
                'href'  => home_url(),
                'title' => __('View Public Side of the Site', $this->nanga),
                'meta'  => ['target' => '_blank'],
            ]);
        }
        $wp_toolbar->remove_node('about');
        $wp_toolbar->remove_node('appearance');
        $wp_toolbar->remove_node('comments');
        $wp_toolbar->remove_node('customize');
        $wp_toolbar->remove_node('dashboard');
        $wp_toolbar->remove_node('documentation');
        $wp_toolbar->remove_node('edit');
        $wp_toolbar->remove_node('edit-profile');
        $wp_toolbar->remove_node('feedback');
        $wp_toolbar->remove_node('logout');
        $wp_toolbar->remove_node('menus');
        $wp_toolbar->remove_node('my-account');
        $wp_toolbar->remove_node('new-content');
        $wp_toolbar->remove_node('new-link');
        $wp_toolbar->remove_node('new-media');
        $wp_toolbar->remove_node('new-page');
        $wp_toolbar->remove_node('new-post');
        $wp_toolbar->remove_node('new-user');
        $wp_toolbar->remove_node('search');
        $wp_toolbar->remove_node('site-name');
        $wp_toolbar->remove_node('support-forums');
        $wp_toolbar->remove_node('themes');
        $wp_toolbar->remove_node('updates');
        $wp_toolbar->remove_node('user-actions');
        $wp_toolbar->remove_node('user-info');
        $wp_toolbar->remove_node('view');
        $wp_toolbar->remove_node('view-site');
        $wp_toolbar->remove_node('wp-logo');
        $wp_toolbar->remove_node('wp-logo-external');
        $wp_toolbar->remove_node('wporg');
        $wp_toolbar->remove_node('wpseo-menu');
    }

    public function disable_metaboxes()
    {
        remove_meta_box('dashboard_activity', 'dashboard', 'normal');
        remove_meta_box('dashboard_browser_nag', 'dashboard', 'normal');
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
        remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
        remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
        remove_meta_box('icl_dashboard_widget', 'dashboard', 'normal');
        remove_meta_box('rg_forms_dashboard', 'dashboard', 'normal');
        remove_meta_box('woocommerce_dashboard_recent_orders', 'dashboard', 'normal');
        remove_meta_box('woocommerce_dashboard_recent_reviews', 'dashboard', 'normal');
        remove_meta_box('woocommerce_dashboard_right_now', 'dashboard', 'normal');
        remove_meta_box('woocommerce_dashboard_sales', 'dashboard', 'normal');
        remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal');
        remove_meta_box('wp_cube', 'dashboard', 'normal');
        remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');
    }

    public function disable_postboxes()
    {
        remove_meta_box('authordiv', 'attachment', 'normal');
        $post_types = get_post_types(['_builtin' => false, 'public' => true]);
        foreach ($post_types as $post_type) {
            remove_meta_box('sharing_meta', $post_type, 'advanced');
        }
    }

    public function footer_left()
    {
        return 'Developed by <a href="' . get_the_author_meta('user_url', 1) . '" target="_blank">' . get_the_author_meta('display_name', 1) . '</a>';
    }

    public function footer_right($wp_version)
    {
        if ( ! current_user_can('manage_options')) {
            return '';
        } else {
            $wp_version        = str_ireplace('version', '', $wp_version);
            $vg_twig_version   = wp_get_theme('vg-twig')->get('Version');
            $vg_plugin_version = ' | <strong>Plugin</strong> N/A';
            $environment       = defined('WP_ENV') ? ' | <strong>Environment</strong> ' . ucfirst(WP_ENV) : ' | <strong>Env</strong> N/A';
            if ( ! $vg_twig_version) {
                $vg_twig_version = ' | <strong>Theme</strong> N/A';
            } else {
                $vg_twig_version = ' | <strong>Theme</strong> ' . $vg_twig_version;
            }
            if (is_plugin_active('nanga/nanga.php')) {
                $version           = get_plugin_data(WP_PLUGIN_DIR . '/nanga/nanga.php', false, false);
                $vg_plugin_version = ' | <strong>Plugin</strong> ' . $version['Version'];
            }

            return '<strong>WP</strong> ' . $wp_version . $vg_twig_version . $vg_plugin_version . $environment;
        }
    }

    public function add_editor_style()
    {
        add_editor_style(plugin_dir_url(__FILE__) . 'css/nanga-editor-style.css');
    }

    public function mce_buttons($mce_buttons)
    {
        $pos = array_search('wp_more', $mce_buttons, true);
        if (false !== $pos) {
            $tmp_buttons   = array_slice($mce_buttons, 0, $pos + 1);
            $tmp_buttons[] = 'wp_page';
            $mce_buttons   = array_merge($tmp_buttons, array_slice($mce_buttons, $pos + 1));
        }

        return $mce_buttons;
    }

    public function all_options_page()
    {
        if (current_theme_supports('nanga-settings')) {
            add_submenu_page('options-general.php', 'WP Options', 'WP Options', 'manage_options', 'options.php');
        }
    }

    public function columns_users($columns)
    {
        unset($columns['posts']);
        unset($columns['role']);
        unset($columns['ure_roles']);

        return $columns;
    }

    public function columns_media($columns)
    {
        unset($columns['author']);
        unset($columns['icon']);
        unset($columns['parent']);

        return $columns;
    }

    public function columns_plugins($columns)
    {
        return $columns;
    }

    public function columns_posts($columns, $post_type)
    {
        //$post_types = get_post_types( array( 'public' => true ), 'names' );
        //if ( in_array( $post_type, $post_types, true ) ) { unset( $columns['cb'] ); }
        if (post_type_supports($post_type, 'thumbnail') && 'product' != $post_type) {
            $columns = $columns + ['icon' => __('Featured', $this->nanga)];
        }

        return $columns;
    }

    public function featured_image_column($column_name, $post_id)
    {
        switch ($column_name) {
            case 'icon':
                if (function_exists('the_post_thumbnail') && '' != get_the_post_thumbnail()) {
                    echo the_post_thumbnail('thumbnail', ['width' => '60', 'height' => 60, 'style' => 'width:60px;height:60px;']);
                } else {
                    echo '<div style="width:60px;height:60px;background:#e1e1e1;color:#0098ed;text-align:center;"><span class="dashicons dashicons-images-alt2" style="font-size:40px;width:60px;height:60px;line-height:60px;"></span></div>';
                }
                break;
        }
    }

    public function columns_pages($columns)
    {
        if (post_type_supports('page', 'thumbnail')) {
            $columns = $columns + ['icon' => __('Featured', $this->nanga)];
        }

        return $columns;
    }

    public function layout_columns()
    {
        add_filter('get_user_option_screen_layout_dashboard', function () {
            return 1;
        });
        add_filter('get_user_option_screen_layout_attachment', function () {
            return 1;
        });
        //add_filter( 'screen_layout_columns', function ( $empty_columns, $screen_id, $screen ) { write_log( $screen ); }, 10, 3 );
    }

    public function mime_types($existing_mimes)
    {
        $existing_mimes['mp4'] = 'video/mp4';
        $existing_mimes['ogg'] = 'video/ogg';
        $existing_mimes['ogv'] = 'video/ogv';
        unset($existing_mimes['bmp']);
        unset($existing_mimes['gif']);

        return $existing_mimes;
    }

    public function customizer_register($wp_customize)
    {
        $wp_customize->add_section('vg_customizer_section', [
            'title'    => __('VG Settings', $this->nanga),
            'priority' => 666,
        ]);
        $wp_customize->add_setting('site_logo', [
            'transport' => 'postMessage',
        ]);
        $wp_customize->add_setting('site_color', [
            'default'   => '#0098ED',
            'transport' => 'postMessage',
        ]);
        $wp_customize->add_setting('site_secondary_color', [
            'default'   => '#E1E1E1',
            'transport' => 'postMessage',
        ]);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'site_logo', [
            'label'    => __('Site Logo', $this->nanga),
            'section'  => 'vg_customizer_section',
            'settings' => 'site_logo',
        ]));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_color', [
            'label'    => __('Site Main Color', $this->nanga),
            'section'  => 'vg_customizer_section',
            'settings' => 'site_color',
        ]));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_secondary_color', [
            'label'    => __('Site Secondary Color', $this->nanga),
            'section'  => 'vg_customizer_section',
            'settings' => 'site_secondary_color',
        ]));
    }

    public function customizer_scripts()
    {
        wp_enqueue_script($this->nanga . '-customizer', plugin_dir_url(__FILE__) . 'js/nanga-customizer.js', ['customize-preview'], null, true);
    }

    public function debug()
    {
        $screen = get_current_screen();
        $screen->set_help_sidebar('');
        $screen->remove_help_tabs();
        if (current_user_can('manage_options')) {
            if ('dashboard' == $screen->id) {
                if (file_exists(WP_CONTENT_DIR . '/debug.log')) {
                    wp_enqueue_script($this->nanga . '-debug-log', plugin_dir_url(__FILE__) . 'js/nanga-debug-log.js');
                    $screen->add_help_tab([
                        'id'    => 'debug_log',
                        'title' => 'Debug Log',
                        //'content' => '<pre id="debug-log" style="margin:10px 0 0 0;overflow-x:hidden;max-height:640px;line-height:1;font-size:10px;">' . file_get_contents( WP_CONTENT_DIR . '/debug.log' ) . '</pre><p><a href="#" id="clear-debug-log" style="text-decoration:none;">Clear Log</a></p>',
                    ]);
                }
                global $menu, $submenu;
                $screen->add_help_tab([
                    'id'      => 'debug_menu',
                    'title'   => 'Debug Menu',
                    'content' => '<pre>' . grab_dump($submenu) . '</pre>' . '<pre>' . grab_dump($menu) . '</pre>',
                ]);
                global $wp_meta_boxes;
                $screen->add_help_tab([
                    'id'      => 'debug_metaboxes',
                    'title'   => 'Debug Metaboxes',
                    'content' => '<pre>' . grab_dump($wp_meta_boxes) . '</pre>',
                ]);
            }
            $screen->add_help_tab([
                'id'      => 'debug_screen',
                'title'   => 'Debug Screen',
                'content' => '<pre>' . grab_dump($screen) . '</pre>',
            ]);
            if (defined('SAVEQUERIES')) {
                global $wpdb;
                $screen->add_help_tab([
                    'id'      => 'debug_queries',
                    'title'   => 'Debug Queries',
                    'content' => '<pre>' . grab_dump($wpdb->queries) . '</pre>',
                ]);
            }
        }
    }

    public function clear_debug_log()
    {
        $handle = fopen(WP_CONTENT_DIR . '/debug.log', 'w');
        fclose($handle);
        die();
    }

    public function row_actions($actions, $post)
    {
        unset($actions['inline hide-if-no-js']);

        return $actions;
    }

    public function default_editor()
    {
        if (current_user_can('manage_options')) {
            return 'html';
        }

        return 'tinymce';
    }

    public function image_license_field($form_fields, $post)
    {
        $field_value            = get_post_meta($post->ID, 'license', true);
        $form_fields['license'] = [
            'value' => $field_value ? $field_value : '',
            'label' => __('Photo License', $this->nanga),
        ];

        return $form_fields;
    }

    public function image_license_save($attachment_id)
    {
        $license = $_REQUEST['attachments'][$attachment_id]['license'];
        if (isset($license)) {
            update_post_meta($attachment_id, 'license', $license);
        }
    }

    public function force_image_attributes()
    {
        $image_link_type = get_option('image_default_link_type');
        if ('none' !== $image_link_type) {
            update_option('image_default_link_type', 'none');
        }
        $image_align = get_option('image_default_align');
        if ('none' !== $image_align) {
            update_option('image_default_align', 'none');
        }
    }

    public function support_request_widget()
    {
        if (current_theme_supports('nanga-support-request')) {
            add_meta_box('support_request_widget', __('Create a Support Request', $this->nanga), [$this, 'support_request_form'], 'dashboard', 'normal', 'low');
        }
    }

    public function support_request_form()
    {
        echo '<div class="support-request-container vg-container"> <div class="support-request-container__messages"></div> <form id="support-request-form" accept-charset="UTF-8" action="https://formkeep.com/f/36041913c4c7" method="POST"><input type="hidden" name="utf8" value="✓"> <p><input type="email" name="email" placeholder="' . __('Your email', $this->nanga) . '" value="' . get_the_author_meta('user_email',
                get_current_user_id()) . '" class="widefat" required></p> <p><input type="text" name="name" placeholder="' . __('Your name', $this->nanga) . '" value="' . get_the_author_meta('display_name', get_current_user_id()) . '" class="widefat" required></p> <p><textarea name="message" placeholder="' . __('Your message',
                $this->nanga) . '" rows="10" class="widefat" required></textarea></p> <input type="hidden" name="site" value="' . home_url() . '"> <input type="submit" id="support-request-form__submit" class="button button-primary" value="' . __('Send Support Request', $this->nanga) . '"> </form> </div>';
    }

    public function user_contact($user_contact)
    {
        unset($user_contact['facebook']);
        unset($user_contact['googleplus']);
        unset($user_contact['twitter']);

        return $user_contact;
    }

    public function required_plugins()
    {
        $plugins = [
            [
                'name'             => 'Advanced Custom Fields Pro',
                'slug'             => 'advanced-custom-fields-pro',
                'source'           => 'https://s3-eu-west-1.amazonaws.com/www.vgwebthings.com/advanced-custom-fields-pro.zip',
                'required'         => true,
                'force_activation' => true,
                'external_url'     => 'http://www.advancedcustomfields.com/pro',
            ],
            /*
            [
                'name'             => 'Jigsaw',
                'slug'             => 'jigsaw',
                'required'         => false,
                'force_activation' => false,
            ],
            */
        ];
        if ('vg-twig' == get_option('template')) {
            $plugins[] = [
                'name'             => 'Timber',
                'slug'             => 'timber-library',
                'required'         => true,
                'force_activation' => true,
            ];
        }
        if (current_theme_supports('nanga-sanity')) {
            $plugins[] = [
                'name'             => 'Image Sanity',
                'slug'             => 'imsanity',
                'required'         => true,
                'force_activation' => true,
            ];
        }
        if (current_theme_supports('nanga-deploy')) {
            $plugins[] = [
                'name'             => 'VG web things Deployer',
                'slug'             => 'nanga-deploy',
                'source'           => 'https://github.com/Mallinanga/nanga-deploy/archive/master.zip',
                'required'         => false,
                'force_activation' => false,
            ];
        }
        $config = [
            'capability'   => 'manage_options',
            'dismissable'  => true,
            'has_notices'  => current_user_can('manage_options'),
            'is_automatic' => true,
        ];
        tgmpa($plugins, $config);
    }
}
