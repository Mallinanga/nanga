<?php

namespace Nanga;

use Nanga\Features\UpdateTester;
use Nanga\Supports\Pagespeed;

class Settings
{

    protected static $test;
    protected static $tabs;

    private function __construct()
    {
        self::options();
        self::tabs();
        add_action('admin_init', [$this, 'settings']);
        add_action('admin_menu', [$this, 'settingsPage']);
        add_action('admin_notices', [$this, 'notices'], 1);
    }

    public static function options()
    {
        if ( ! empty(self::$test)) {
            return self::$test;
        }
        self::$test = get_option('nanga');

        return self::$test;
    }

    public static function tabs()
    {
        if ( ! empty(self::$tabs)) {
            return self::$tabs;
        }
        self::$tabs = apply_filters('nanga_settings_tabs', [
            'general'     => [
                'icon'  => 'dashicons-admin-settings',
                'show'  => true,
                'slug'  => 'general',
                'title' => 'General',
            ],
            'appearance'  => [
                'icon'  => 'dashicons-admin-appearance',
                'show'  => true,
                'slug'  => 'appearance',
                'title' => 'Appearance',
            ],
            'maintenance' => [
                'icon'  => 'dashicons-hidden',
                'show'  => true,
                'slug'  => 'maintenance',
                'title' => 'Under Construction',
            ],
            'updates'     => [
                'icon'  => 'dashicons-update',
                'show'  => true,
                'slug'  => 'updates',
                'title' => 'Updates',
            ],
            'cronjobs'    => [
                'icon'  => 'dashicons-backup',
                'show'  => false,
                'slug'  => 'cronjobs',
                'title' => 'Cronjobs',
            ],
            'extend'      => [
                'icon'  => 'dashicons-admin-plugins',
                'show'  => true,
                'slug'  => 'extend',
                'title' => 'Extend',
            ],
        ]);

        return self::$tabs;
    }

    public static function instance()
    {
        static $instance = false;
        if ($instance === false) {
            $instance = new static();
        }

        return $instance;
    }

    public function settings()
    {
        register_setting('nanga_settings_general', 'nanga');
        register_setting('nanga_settings_appearance', 'nanga');
        add_settings_section('nanga_settings_general', 'General Settings', [$this, 'sectionGeneralSettings'], 'nanga_settings_general');
        add_settings_field('nanga_settings_field_ua', 'Google Analytics UA', [$this, 'fieldUA'], 'nanga_settings_general', 'nanga_settings_general', ['label_for' => 'ua', 'class' => 'row--ua']);
        // add_settings_field('nanga_settings_field_marketing', 'Marketing Scripts', [$this, 'fieldMarketing'], 'nanga_settings_general', 'nanga_settings_general', ['label_for' => 'marketing', 'class' => 'row--marketing']);
        // add_settings_section('nanga_settings_test', 'Test Settings', null, 'nanga_settings_general');
    }

    public function sectionGeneralSettings($args)
    {
        ?>
        <?php
    }

    public function fieldUA($args)
    {
        ?>
        <input type="text" id="<?php echo esc_attr($args['label_for']); ?>" name="nanga[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo self::$test[$args['label_for']]; ?>">
        <p class="description">Not fully working yet.</p>
        <?php
    }

    public function fieldMarketing($args)
    {
        ?>
        <textarea id="<?php echo esc_attr($args['label_for']); ?>" name="nanga[<?php echo esc_attr($args['label_for']); ?>]" rows="10" class="regular-text"></textarea>
        <?php
    }

    public function settingsPage()
    {
        add_options_page('Settings for ' . get_site_url(), 'VG web things', 'manage_options', 'nanga-settings', [$this, 'settingsPageRender']);
    }

    public function settingsPageRender()
    {
        $currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        $tabs       = self::tabs();
        ?>
        <div id="nanga-settings" class="wrap">
            <hr class="wp-header-end">
            <h1 class="wp-heading-inline--"><?php echo esc_html(get_admin_page_title()); ?></h1>
            <h2 class="nav-tab-wrapper wp-clearfix">
                <?php
                foreach ($tabs as $tab) {
                    if ( ! $tab['show']) {
                        continue;
                    }
                    ?>
                    <a href="?page=nanga-settings&tab=<?php echo $tab['slug']; ?>" class="nav-tab <?php echo 'nav-tab-' . $tab['slug']; ?> <?php echo $currentTab == $tab['slug'] ? 'nav-tab-active' : ''; ?>">
                        <span class="dashicons <?php echo $tab['icon']; ?>"></span>
                        <span><?php echo $tab['title']; ?></span>
                    </a>
                    <?php
                }
                ?>
                <a href="?page=nanga-settings&tab=debug" class="nav-tab nav-tab-debug <?php echo $currentTab == 'debug' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-sos"></span>
                    <span class="">Debug</span>
                </a>
            </h2>
            <div class="nanga-settings__content">
                <?php
                if ($currentTab == 'general') {
                    echo '<form method="post" action="options.php">';
                    settings_fields('nanga_settings_general');
                    do_settings_sections('nanga_settings_general');
                    submit_button();
                    echo '<form>';
                }
                if ($currentTab == 'appearance') {
                    // echo '<form method="post" action="options.php">';
                    // settings_fields('nanga_settings_appearance');
                    // do_settings_sections('nanga_settings_appearance');
                    $this->pageAppearance();
                    // submit_button();
                    // echo '<form>';
                }
                if ($currentTab == 'maintenance') {
                    $this->pageMaintenance();
                }
                if ($currentTab == 'updates') {
                    $this->pageUpdates();
                }
                if ($currentTab == 'cronjobs') {
                    $this->pageCronjobs();
                }
                if ($currentTab == 'pagespeed') {
                    $this->pagePagespeed();
                }
                if ($currentTab == 'extend') {
                    $this->pageExtend();
                }
                if ($currentTab == 'debug') {
                    $this->pageDebug();
                }
                do_action('nanga_settings_tab_content_' . $currentTab);
                ?>
            </div>
        </div>
        <?php
    }

    private function pageAppearance()
    {
        echo '<h2>Favicon & Login Logo</h2> ';
        echo '<p>The image will be used as a browser and app icon for your site as well as for the login screen. The image must be square, and <strong>at least 512 pixels wide and tall</strong>.</p>';
        echo '<p class="description">In case there is a filename named <code>logo.svg</code> or <code>logo.png</code> under <code>assets/img/</code> it will be used as the login screen logo.</p>';
        echo '<p>Choose image from the <a href="' . admin_url('/customize.php?autofocus[section]=title_tagline') . '">Customizer</a>.</p>';
    }

    private function pageMaintenance()
    {
        echo '<h2>Maintenance Mode</h2>';
        if (file_exists(ABSPATH . '.nanga-maintenance')) {
            echo '<a href="' . wp_nonce_url(admin_url('options-general.php?page=nanga-settings&tab=maintenance&action=nanga-disable-maintenance-mode')) . '" class="button">Disable Maintenance Mode</a>';
        } else {
            echo '<a href="' . wp_nonce_url(admin_url('options-general.php?page=nanga-settings&tab=maintenance&action=nanga-enable-maintenance-mode')) . '" class="button">Enable Maintenance Mode</a>';
        }
        // echo '<h2>Under Construction Mode</h2>';
        // echo '<p>Configure Under Construction mode on your site\'s <a href="' . admin_url('options-reading.php') . '">visibility settings</a>.</p>';
    }

    private function pageUpdates()
    {
        echo '<h2>Background Updates Tester</h2> ';
        echo '<p>WordPress automatic background updates require a number of conditions to be met.</p>';
        if ( ! isset($_GET['tester'])) {
            echo '<a href="' . admin_url('options-general.php?page=nanga-settings&tab=updates&tester=1') . '" class="button">Evaluate Updates</a>';

            return;
        }
        new UpdateTester();
    }

    private function pageCronjobs()
    {
    }

    private function pagePagespeed()
    {
        // Pagespeed::vg_pagespeed_widget();
    }

    private function pageExtend()
    {
        echo '<h2>Install Additional Plugins</h2> ';
        echo '<p>Choose additional plugins to install <a href="' . admin_url('/themes.php?page=tgmpa-install-plugins') . '">here</a>.</p>';
    }

    private function pageDebug()
    {
        global $wpdb;
        $table_prefix = $wpdb->base_prefix;
        echo '<textarea autocomplete="off" readonly rows="45" style="font-family:monospace;width:100%;">';
        echo 'Environment: ';
        echo esc_html((defined('WP_ENV')) ? WP_ENV : 'Undefined');
        echo "\r\n";
        echo 'Debug Mode: ';
        echo esc_html((defined('WP_DEBUG') && WP_DEBUG) ? 'Yes' : 'No');
        echo "\r\n";
        echo 'Compatibility Mode: ';
        if (defined('NANGA_LEGACY')) {
            echo 'Yes';
        } else {
            echo 'No';
        }
        echo "\r\n\r\n";
        echo 'site_url(): ';
        echo esc_html(site_url());
        echo "\r\n";
        echo 'home_url(): ';
        echo esc_html(home_url());
        echo "\r\n";
        echo 'Database Name: ';
        echo esc_html($wpdb->dbname);
        echo "\r\n";
        echo 'Table Prefix: ';
        echo esc_html($table_prefix);
        echo "\r\n";
        echo 'WordPress: ';
        echo bloginfo('version');
        if (is_multisite()) {
            $multisite_type = defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL ? 'Sub-domain' : 'Sub-directory';
            echo ' Multisite (' . $multisite_type . ')';
            echo "\r\n";
            if (defined('DOMAIN_CURRENT_SITE')) {
                echo 'Domain Current Site: ';
                echo DOMAIN_CURRENT_SITE;
                echo "\r\n";
            }
            if (defined('PATH_CURRENT_SITE')) {
                echo 'Path Current Site: ';
                echo PATH_CURRENT_SITE;
                echo "\r\n";
            }
            if (defined('SITE_ID_CURRENT_SITE')) {
                echo 'Site ID Current Site: ';
                echo SITE_ID_CURRENT_SITE;
                echo "\r\n";
            }
            if (defined('BLOG_ID_CURRENT_SITE')) {
                echo 'Blog ID Current Site: ';
                echo BLOG_ID_CURRENT_SITE;
            }
        }
        echo "\r\n";
        echo 'Web Server: ';
        echo esc_html(! empty($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '');
        echo "\r\n";
        echo 'PHP: ';
        if (function_exists('phpversion')) {
            echo esc_html(phpversion());
        }
        echo "\r\n";
        echo 'MySQL: ';
        echo esc_html(empty($wpdb->use_mysqli) ? mysql_get_server_info() : mysqli_get_server_info($wpdb->dbh));
        echo "\r\n";
        echo 'ext/mysqli: ';
        echo empty($wpdb->use_mysqli) ? 'No' : 'Yes';
        echo "\r\n";
        echo 'WP Memory Limit: ';
        echo esc_html(WP_MEMORY_LIMIT);
        echo "\r\n";
        echo 'Blocked External HTTP Requests: ';
        if ( ! defined('WP_HTTP_BLOCK_EXTERNAL') || ! WP_HTTP_BLOCK_EXTERNAL) {
            echo 'None';
            echo "\r\n";
        } else {
            $accessible_hosts = (defined('WP_ACCESSIBLE_HOSTS')) ? WP_ACCESSIBLE_HOSTS : '';
            if (empty($accessible_hosts)) {
                echo 'ALL';
                echo "\r\n";
            } else {
                echo "Partially\r\n";
                echo "Accessible Hosts:\r\n";
                foreach (explode(',', $accessible_hosts) as $host) {
                    echo '  ' . esc_html($host) . "\r\n";
                }
            }
        }
        echo 'WP Locale: ';
        echo esc_html(get_locale());
        echo "\r\n";
        echo 'DB Charset: ';
        echo esc_html(DB_CHARSET);
        echo "\r\n";
        if (function_exists('ini_get') && $suhosin_limit = ini_get('suhosin.post.max_value_length')) {
            echo 'Suhosin Post Max Value Length: ';
            echo esc_html(is_numeric($suhosin_limit) ? size_format($suhosin_limit) : $suhosin_limit);
            echo "\r\n";
        }
        if (function_exists('ini_get') && $suhosin_limit = ini_get('suhosin.request.max_value_length')) {
            echo 'Suhosin Request Max Value Length: ';
            echo esc_html(is_numeric($suhosin_limit) ? size_format($suhosin_limit) : $suhosin_limit);
            echo "\r\n";
        }
        echo 'WP Max Upload Size: ';
        echo esc_html(size_format(wp_max_upload_size()));
        echo "\r\n";
        //echo 'PHP Post Max Size: ';
        //echo esc_html(size_format($this->get_post_max_size()));
        //echo "\r\n";
        echo 'PHP Time Limit: ';
        if (function_exists('ini_get')) {
            echo esc_html(ini_get('max_execution_time'));
        }
        echo "\r\n";
        echo 'PHP Error Log: ';
        if (function_exists('ini_get')) {
            echo esc_html(ini_get('error_log'));
        }
        echo "\r\n";
        echo 'fsockopen: ';
        if (function_exists('fsockopen')) {
            echo 'Enabled';
        } else {
            echo 'Disabled';
        }
        echo "\r\n";
        echo 'OpenSSL: ';
        echo esc_html(OPENSSL_VERSION_TEXT);
        echo "\r\n";
        echo 'cURL: ';
        if (function_exists('curl_init')) {
            echo 'Enabled';
        } else {
            echo 'Disabled';
        }
        do_action('nanga_diagnostics');
        echo "\r\n\r\n";
        $theme = wp_get_theme();
        echo "Theme Name: " . esc_html($theme->Name) . "\r\n";
        echo "Theme Version: " . esc_html($theme->Version) . "\r\n";
        echo "Theme Folder: " . esc_html(basename($theme->get_stylesheet_directory())) . "\r\n";
        if ($theme->get('Template')) {
            echo "Parent Theme Folder: " . esc_html($theme->get('Template')) . "\r\n";
        }
        if ( ! file_exists($theme->get_stylesheet_directory())) {
            echo "WARNING: Theme Folder Not Found\r\n";
        }
        echo "\r\n";
        echo "Active Plugins:\r\n";
        $activePlugins = (array)get_option('active_plugins', []);
        if (is_multisite()) {
            $networkActivePlugins = wp_get_active_network_plugins();
            $activePlugins        = array_map([$this, 'remove_wp_plugin_dir'], $networkActivePlugins);
        }
        foreach ($activePlugins as $plugin) {
            $pluginData = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
            if (empty($pluginData['Name'])) {
                return;
            }
            printf("%s%s (v%s) by %s\r\n", $pluginData['Name'], '', $pluginData['Version'], $pluginData['AuthorName']);
        }
        $muPlugins = wp_get_mu_plugins();
        if ($muPlugins) {
            echo "\r\n";
            echo "Must-use Plugins:\r\n";
            foreach ($muPlugins as $muPlugin) {
                $pluginData = get_plugin_data($muPlugin);
                if (empty($pluginData['Name'])) {
                    return;
                }
                printf("%s%s (v%s) by %s\r\n", $pluginData['Name'], '', $pluginData['Version'], $pluginData['AuthorName']);
            }
            echo "\r\n";
        }
        echo '</textarea>';
    }

    public function notices()
    {
        if ( ! current_user_can('manage_options')) {
            return;
        }
        if (nanga_site_in_development() && 0 != get_option('blog_public')) {
            echo '<div class="notice notice-error"><p>The site is in <strong>development mode</strong> but search engines can index it. Please make sure <a href="' . admin_url('options-reading.php') . '">search engine visibility</a> is setup correctly.</p></div>';
        }
        if (nanga_site_in_production() && ! get_option('blog_public')) {
            echo '<div class="notice notice-error"><p>The site is in <strong>production mode</strong>. Please make sure <a href="' . admin_url('options-reading.php') . '">search engine visibility</a> is setup correctly.</p></div>';
        }
        if (nanga_site_in_production() && isset(self::$test['ua']) && empty(self::$test['ua'])) {
            echo '<div class="notice notice-warning is-dismissible"><p>The site is missing a Google Analytics UA. Please enter one in <a href="' . admin_url('options-general.php?page=nanga-settings&tab=general') . '">general settings</a>.</p></div>';
        }
    }

    private function pagewelcome()
    {
    }

    private function __clone()
    {
    }

    private function __sleep()
    {
    }

    private function __wakeup()
    {
    }
}
