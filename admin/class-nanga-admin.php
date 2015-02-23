<?php

class Nanga_Admin {
    private $nanga;
    private $version;

    public function __construct( $nanga, $version ) {
        $this->nanga   = $nanga;
        $this->version = $version;
    }

    public function login_headerurl() {
        return get_site_url();
    }

    public function login_headertitle() {
        return get_option( 'blogname' );
    }

    public function screen_options_show_screen() {
        return current_user_can( 'manage_options' );
    }

    public function acf_settings_show_admin( $show ) {
        return current_user_can( 'manage_options' );
    }

    public function dequeue_login_styles() {
        wp_deregister_style( 'open-sans' );
        wp_register_style( 'open-sans', false );
        wp_deregister_style( 'dashicons' );
        wp_register_style( 'dashicons', false );
        wp_deregister_style( 'buttons' );
        wp_register_style( 'buttons', false );
        //wp_deregister_style( 'login' );
        //wp_register_style( 'login', false );
    }

    public function enqueue_login_styles() {
        wp_register_style( $this->nanga . '-login', plugin_dir_url( __FILE__ ) . 'css/nanga-login.css', array(), $this->version, 'all' );
        wp_print_styles( $this->nanga . '-login' );
        //wp_register_style( $this->nanga . '-login-style', get_template_directory_uri() . '/assets/css/app.css', array(), $this->version, 'all' );
        //wp_print_styles( $this->nanga . '-login-style' );
    }

    public function enqueue_styles() {
        wp_deregister_style( 'open-sans' );
        wp_register_style( 'open-sans', false );
        wp_enqueue_style( $this->nanga, plugin_dir_url( __FILE__ ) . 'css/nanga-admin.css', array(), $this->version, 'all' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->nanga, plugin_dir_url( __FILE__ ) . 'js/nanga-admin.js', array( 'jquery' ), $this->version, true );
    }

    public function plugin_action_links( $links ) {
        $links[] = '<a href="' . get_admin_url( null, 'admin.php?page=advanced-settings' ) . '">' . __( 'Settings', 'nanga' ) . '</a>';

        return $links;
    }

    public function disable_update_checks() {
    }

    public function disable_rewrite_rules( $rules ) {
        write_log( print_r( $rules ) );

        return $rules;
    }

    public function disable_admin_notices() {
        remove_action( 'admin_notices', 'update_nag', 3 );
    }

    public function disable_menus() {
        remove_menu_page( 'separator1' );
        remove_menu_page( 'separator2' );
        remove_menu_page( 'separator-last' );
        if ( ! current_user_can( 'manage_options' ) ) {
            remove_menu_page( 'tools.php' );
            remove_menu_page( 'profile.php' );
        }
        if ( ! post_type_supports( 'post', 'comments' ) ) {
            remove_menu_page( 'edit-comments.php' );
        }
    }

    public function set_locale( $locale ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return 'en_US';
        } else {
            return $locale;
        }
    }

    public function admin_color_scheme() {
        //remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
        wp_admin_css_color( $this->nanga, 'VG web things', plugin_dir_url( __FILE__ ) . 'css/nanga-admin-colors.css', array( '#000000', '#0098ed', '#e1e1e1', '#ffffff' ) );
        add_filter( 'get_user_option_admin_color', function ( $result ) {
            return 'nanga';
        } );
    }

    public function settings_page() {
        if ( function_exists( 'acf_add_options_page' ) ) {
            acf_add_options_page( array(
                'page_title' => 'General Settings',
                'menu_title' => 'Settings',
                'menu_slug'  => 'general-settings',
                'redirect'   => false,
                'position'   => false,
                'icon_url'   => 'dashicons-forms'
            ) );
            acf_add_options_sub_page( array(
                'page_title'  => 'Advanced Settings',
                'menu_title'  => 'Advanced Settings',
                'menu_slug'   => 'advanced-settings',
                'parent_slug' => 'general-settings',
                'capability'  => 'manage_options'
            ) );
        }
    }

    public function admin_bar( $wp_toolbar ) {
        $links = array(
            'Google Analytics'  => 'http://www.google.com/analytics/',
            'Webmaster Tools'   => 'https://www.google.com/webmasters/tools/dashboard?hl=en&siteUrl=' . get_site_url(),
            'Twitter Reactions' => 'http://search.twitter.com/search?q=' . get_site_url(),
        );
        $wp_toolbar->add_menu( array(
            'href'  => false,
            'id'    => 'nanga-links',
            'title' => 'Administration Tools',
        ) );
        $count = 1;
        foreach ( $links as $label => $url ) {
            $wp_toolbar->add_node( array(
                'href'   => $url,
                'id'     => 'nanga-link_' . $count ++,
                'meta'   => array( 'target' => '_blank' ),
                'parent' => 'nanga-links',
                'title'  => $label,
            ) );
        }
        $wp_toolbar->add_node( array(
            'href'  => 'http://www.vgwebthings.com/',
            'id'    => 'get-help',
            'meta'  => array( 'target' => '_blank', ),
            'title' => 'Get Support',
        ) );
        $wp_toolbar->add_node( array(
            'href'   => 'mailto:info@vgwebthings.com?subject=Support%20Request',
            'id'     => 'get-support',
            'parent' => 'get-help',
            'title'  => 'Email Support',
        ) );
        //$my_account = $wp_toolbar->get_node( 'my-account' );
        $wp_toolbar->add_node( array(
            'id'    => 'my-account',
            'title' => 'My Account',
            //'href'  => get_site_url(),
            //'meta'  => array( 'target' => '_blank' ),
        ) );
        $wp_toolbar->add_node( array(
            'id'    => 'site-name',
            'title' => get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' ),
            'meta'  => array( 'target' => '_blank' )
        ) );
        $wp_toolbar->remove_node( 'about' );
        $wp_toolbar->remove_node( 'appearance' );
        $wp_toolbar->remove_node( 'comments' );
        $wp_toolbar->remove_node( 'customize' );
        $wp_toolbar->remove_node( 'dashboard' );
        $wp_toolbar->remove_node( 'documentation' );
        $wp_toolbar->remove_node( 'edit' );
        $wp_toolbar->remove_node( 'edit-profile' );
        $wp_toolbar->remove_node( 'feedback' );
        $wp_toolbar->remove_node( 'menus' );
        $wp_toolbar->remove_node( 'new-content' );
        $wp_toolbar->remove_node( 'new-link' );
        $wp_toolbar->remove_node( 'new-media' );
        $wp_toolbar->remove_node( 'new-page' );
        $wp_toolbar->remove_node( 'new-post' );
        $wp_toolbar->remove_node( 'new-user' );
        $wp_toolbar->remove_node( 'search' );
        $wp_toolbar->remove_node( 'support-forums' );
        $wp_toolbar->remove_node( 'themes' );
        $wp_toolbar->remove_node( 'updates' );
        $wp_toolbar->remove_node( 'user-info' );
        $wp_toolbar->remove_node( 'view' );
        $wp_toolbar->remove_node( 'view-site' );
        $wp_toolbar->remove_node( 'wp-logo' );
        $wp_toolbar->remove_node( 'wp-logo-external' );
        $wp_toolbar->remove_node( 'wporg' );
        $wp_toolbar->remove_node( 'wpseo-menu' );
    }

    public function disable_metaboxes() {
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'icl_dashboard_widget', 'dashboard', 'normal' );
        remove_meta_box( 'mandrill_widget', 'dashboard', 'normal' );
        remove_meta_box( 'woocommerce_dashboard_recent_orders', 'dashboard', 'normal' );
        remove_meta_box( 'woocommerce_dashboard_recent_reviews', 'dashboard', 'normal' );
        remove_meta_box( 'woocommerce_dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'woocommerce_dashboard_sales', 'dashboard', 'normal' );
        remove_meta_box( 'wp_cube', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_browser_nag', 'dashboard', 'normal' );
        remove_meta_box( 'rg_forms_dashboard', 'dashboard', 'normal' );
    }

    public function footer_left() {
        return 'Developed by <a href="' . get_the_author_meta( 'user_url', 1 ) . '" target="_blank">' . get_the_author_meta( 'display_name', 1 ) . '</a>';
    }

    public function footer_right( $wp_version ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return '';
        } else {
            $wp_version        = str_ireplace( 'version', '', $wp_version );
            $vg_version        = wp_get_theme( 'vg' )->get( 'Version' );
            $vg_twig_version   = wp_get_theme( 'vg-twig-child' )->get( 'Version' );
            $vg_plugin_version = ' | <strong>Plugin</strong> N/A';
            if ( ! $vg_version ) {
                $vg_version = ' | <strong>VG</strong> N/A';
            } else {
                $vg_version = ' | <strong>VG</strong> ' . $vg_version;
            }
            if ( ! $vg_twig_version ) {
                $vg_twig_version = ' | <strong>Twig</strong> N/A';
            } else {
                $vg_twig_version = ' | <strong>Twig</strong> ' . $vg_twig_version;
            }
            if ( is_plugin_active( 'nanga/nanga.php' ) ) {
                $version           = get_plugin_data( WP_PLUGIN_DIR . '/nanga/nanga.php', false, false );
                $vg_plugin_version = ' | <strong>Plugin</strong> ' . $version['Version'];
            }

            return '<strong>WP</strong> ' . $wp_version . $vg_version . $vg_twig_version . $vg_plugin_version;
        }
    }

    public function add_editor_style() {
        add_editor_style( plugin_dir_url( __FILE__ ) . 'css/nanga-editor-style.css' );
    }

    public function mce_buttons( $mce_buttons ) {
        $pos = array_search( 'wp_more', $mce_buttons, true );
        if ( false !== $pos ) {
            $tmp_buttons   = array_slice( $mce_buttons, 0, $pos + 1 );
            $tmp_buttons[] = 'wp_page';
            $mce_buttons   = array_merge( $tmp_buttons, array_slice( $mce_buttons, $pos + 1 ) );
        }

        return $mce_buttons;
    }

    public function all_options_page() {
        add_options_page( 'Debug Settings', 'Debug Settings', 'administrator', 'options.php' );
    }

    public function debug() {
        $screen = get_current_screen();
        $screen->set_help_sidebar( '' );
        //add_filter( 'get_user_option_screen_layout_' . $screen->id, function () { return 1; } );
        //add_filter( 'screen_layout_columns', function ( $aaa ) { write_log( $aaa ); } );
        //if ( 'dashboard' == $screen->id ) { $screen->set_columns(); }
        if ( ! current_user_can( 'manage_options' ) ) {
            $screen->remove_help_tabs();
        }
        if ( current_user_can( 'manage_options' ) ) {
            $screen->remove_help_tabs();
            if ( 'dashboard' == $screen->id ) {
                if ( file_exists( WP_CONTENT_DIR . '/debug.log' ) ) {
                    wp_enqueue_script( $this->nanga . '-debug-log', plugin_dir_url( __FILE__ ) . 'js/nanga-debug-log.js' );
                    $screen->add_help_tab( array(
                        'id'      => 'debug_log',
                        'title'   => 'Debug Log',
                        'content' => '<pre id="debug-log" style="margin:10px 0 0 0;overflow-x:hidden;max-height:640px;line-height:1;font-size:10px;">' . file_get_contents( WP_CONTENT_DIR . '/debug.log' ) . '</pre><p><a href="#" id="clear-debug-log" style="text-decoration:none;">Clear Log</a></p>',
                    ) );
                }
                global $wp_admin_bar;
                $screen->add_help_tab( array(
                    'id'      => 'adminbar_info',
                    'title'   => 'Adminbar Info',
                    'content' => '<pre>' . grab_dump( $wp_admin_bar ) . '</pre>',
                ) );
                global $menu, $submenu;
                $screen->add_help_tab( array(
                    'id'      => 'menu_info',
                    'title'   => 'Menu Info',
                    'content' => '<pre>' . grab_dump( $submenu ) . '</pre>' . '<pre>' . grab_dump( $menu ) . '</pre>',
                ) );
                global $wp_meta_boxes;
                $screen->add_help_tab( array(
                    'id'      => 'metaboxes_info',
                    'title'   => 'Metaboxes Info',
                    'content' => '<pre>' . grab_dump( $wp_meta_boxes ) . '</pre>',
                ) );
            }
            $screen->add_help_tab( array(
                'id'      => 'screen_info',
                'title'   => 'Screen Info',
                'content' => '<pre>' . grab_dump( $screen ) . '</pre>',
            ) );
            global $wpdb;
            $screen->add_help_tab( array(
                'id'      => 'queries_info',
                'title'   => 'Queries Info',
                'content' => '<pre>' . grab_dump( $wpdb->queries ) . '</pre>',
            ) );
        }
    }

    public function clear_debug_log() {
        $handle = fopen( WP_CONTENT_DIR . '/debug.log', 'w' );
        fclose( $handle );
        die();
    }

    public function required_plugins() {
        $plugins = array(
            array(
                'name'             => 'Advanced Custom Fields Pro',
                'slug'             => 'advanced-custom-fields-pro',
                'source'           => 'https://s3-eu-west-1.amazonaws.com/www.vgwebthings.com/advanced-custom-fields-pro.zip',
                'required'         => true,
                'force_activation' => true,
                'external_url'     => 'http://www.advancedcustomfields.com/pro'
            ),
            array(
                'name'             => 'GitHub Updater',
                'slug'             => 'github-updater',
                'source'           => 'https://github.com/afragen/github-updater/archive/develop.zip',
                'required'         => true,
                'force_activation' => false,
                'external_url'     => 'https://github.com/afragen/github-updater'
            ),
            array(
                'name'     => 'Timber',
                'slug'     => 'timber-library',
                'required' => false,
            ),
            array(
                'name'             => 'Image Sanity',
                'slug'             => 'imsanity',
                'required'         => true,
                'force_activation' => true,
            ),
            array(
                'name'     => 'Jigsaw',
                'slug'     => 'jigsaw',
                'required' => false,
            ),
            array(
                'name'     => 'Under Construction',
                'slug'     => 'underconstruction',
                'required' => false,
            ),
            array(
                'name'     => 'Relevanssi',
                'slug'     => 'relevanssi',
                'required' => false,
            ),
        );
        $config  = array(
            'default_path' => '',
            'menu'         => 'nanga-install-plugins',
            'has_notices'  => true,
            'dismissable'  => false,
            'dismiss_msg'  => false,
            'is_automatic' => true,
            'message'      => false,
            'strings'      => array(
                'page_title'                      => 'Install Required Plugins',
                'menu_title'                      => 'Install Plugins',
                'installing'                      => 'Installing Plugin: %s',
                'oops'                            => 'Something went wrong with the plugin API.',
                'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ),
                'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ),
                'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ),
                'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ),
                'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ),
                'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ),
                'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ),
                'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ),
                'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
                'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
                'return'                          => 'Return to Required Plugins Installer',
                'plugin_activated'                => 'Plugin activated successfully.',
                'complete'                        => 'All plugins installed and activated successfully. %s',
                'nag_type'                        => 'updated'
            )
        );
        tgmpa( $plugins, $config );
    }
}
