<?php

namespace Nanga\Features;

class UpdateTester
{

    public function __construct()
    {
        $this->content();
    }

    private function content()
    {
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        flush();
        $tests    = $this->tests();
        $failures = wp_list_filter($tests, ['severity' => 'fail']) || wp_list_filter($tests, ['severity' => 'warning']);
        if ($failures) {
            echo '<p>Your hosting company, support forum volunteers, or a friendly developer may be able to use this information to help you:</p>';
        }
        echo '<ul>';
        foreach ($tests as $item) {
            switch ($item->severity) {
                case 'warning':
                    $status = '<strong style="color:rgb(255,165,0);">WARNING!</strong>';
                    break;
                case 'pass':
                    $status = '<strong style="color:rgb(0,128,0);">PASS!</strong>';
                    break;
                case 'info':
                    $status = '<strong style="color:rgb(0,0,0);">INFO!</strong>';
                    break;
                case 'fail':
                    $status = '<strong style="color:rgb(255,0,0);">FAIL!</strong>';
                    break;
            }
            printf('<li>%s %s</li>', $status, $item->desc);
        }
        echo '</ul>';
        echo '<h2>';
        if ($failures) {
            if (apply_filters('send_core_update_notification_email', true)) {
                echo 'This site <strong>is not</strong> able to apply updates automatically. But we\'ll email ' . esc_html(get_site_option('admin_email')) . ' when there is a new security release.';
            } else {
                echo 'This site <strong>is not</strong> able to apply updates automatically.';
            }
        } else {
            echo 'This site <strong>is</strong> able to apply these updates automatically.';
        }
        echo '</h2>';
    }

    private function tests()
    {
        $tests = [];
        foreach (get_class_methods($this) as $method) {
            if ('test_' != substr($method, 0, 5)) {
                continue;
            }
            $result = call_user_func([$this, $method]);
            if (false === $result || null === $result) {
                continue;
            }
            $result = (object)$result;
            if (empty($result->severity)) {
                $result->severity = 'warning';
            }
            $tests[$method] = $result;
        }

        return $tests;
    }

    private function test_https_supported()
    {
        $support = wp_http_supports(['ssl']);

        return [
            'desc'     => $support ? 'Your WordPress install can communicate with WordPress.org securely.' : 'Your WordPress install cannot communicate with WordPress.org securely. Talk to your web host about OpenSSL support for PHP.',
            'severity' => $support ? 'pass' : 'fail',
        ];
    }

    private function test_constant_FILE_MODS()
    {
        if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
            return [
                'desc'     => 'The <code>DISALLOW_FILE_MODS</code> constant is defined as true',
                'severity' => 'fail',
            ];
        }
    }

    private function test_constant_AUTOMATIC_UPDATER_DISABLED()
    {
        if (defined('AUTOMATIC_UPDATER_DISABLED') && AUTOMATIC_UPDATER_DISABLED) {
            return [
                'desc'     => 'The <code>AUTOMATIC_UPDATER_DISABLED</code> constant is defined as true.',
                'severity' => 'fail',
            ];
        }
    }

    private function test_constant_WP_AUTO_UPDATE_CORE()
    {
        if (defined('WP_AUTO_UPDATE_CORE') && false === WP_AUTO_UPDATE_CORE) {
            return [
                'desc'     => 'The <code>WP_AUTO_UPDATE_CORE</code> constant is defined as false.',
                'severity' => 'fail',
            ];
        }
    }

    private function test_filters_automatic_updater_disabled()
    {
        if (apply_filters('automatic_updater_disabled', false)) {
            return [
                'desc'     => 'The <code>automatic_updater_disabled</code> filter returns <code>true</code>.',
                'severity' => 'fail',
            ];
        }
    }

    private function test_if_failed_update()
    {
        $failed = get_site_option('auto_core_update_failed');
        if ( ! $failed) {
            return false;
        }
        if ( ! empty($failed['critical'])) {
            $desc = 'A previous automatic background update ended with a critical failure, so updates are now disabled.';
            $desc .= ' ' . 'You would have received an email because of this';
            $desc .= ' ' . 'When you&#8217;ve been able to update using the &#8220;Update Now&#8221; button on Dashboard &rarr; Updates, we&#8217;ll clear this error for future update attempts.';
            $desc .= ' ' . 'The error code was <code>' . $failed['error_code'] . '</code>';

            return [
                'desc'     => $desc,
                'severity' => 'warning',
            ];
        }
        $desc = 'A previous automatic background update could not occur.';
        if (empty($failed['retry'])) {
            $desc .= ' ' . 'You would have received an email because of this';
        }
        $desc .= ' ' . 'We\'ll try again with the next release.';
        $desc .= ' ' . 'The error code was <code>' . $failed['error_code'] . '</code>.';

        return [
            'desc'     => $desc,
            'severity' => 'warning',
        ];
    }

    private function test_vcs_ABSPATH()
    {
        $result = $this->_test_is_vcs_checkout(ABSPATH);

        return $result;
    }

    private function _test_is_vcs_checkout($context)
    {
        $context_dirs = [ABSPATH];
        $vcs_dirs     = ['.svn', '.git', '.hg', '.bzr'];
        $check_dirs   = [];
        foreach ($context_dirs as $context_dir) {
            do {
                $check_dirs[] = $context_dir;
                if ($context_dir == dirname($context_dir)) {
                    break;
                }
            } while ($context_dir = dirname($context_dir));
        }
        $check_dirs = array_unique($check_dirs);
        foreach ($vcs_dirs as $vcs_dir) {
            foreach ($check_dirs as $check_dir) {
                if ($checkout = @is_dir(rtrim($check_dir, '\\/') . "/$vcs_dir")) {
                    break 2;
                }
            }
        }
        if ($checkout && ! apply_filters('automatic_updates_is_vcs_checkout', true, $context)) {
            return [
                'desc'     => 'The folder <code>' . $check_dir . '</code> was detected as being under version control (<code>' . $vcs_dir . '</code>), but the <code>automatic_updates_is_vcs_checkout</code> filter is allowing updates.',
                'severity' => 'info',
            ];
        }
        if ($checkout) {
            return [
                'desc'     => 'The folder <code>' . $check_dir . '</code> was detected as being under version control (<code>' . $vcs_dir . '</code>).',
                'severity' => 'fail',
            ];
        }

        return [
            'desc'     => 'No version control systems were detected.',
            'severity' => 'pass',
        ];
    }

    private function test_check_wp_filesystem_method()
    {
        $skin    = new \Automatic_Upgrader_Skin;
        $success = $skin->request_filesystem_credentials(false, ABSPATH);
        if ( ! $success) {
            $desc = 'Your installation of WordPress prompts for FTP credentials to perform updates.';
            $desc .= ' ' . '(Your site is performing updates over FTP due to file ownership. Talk to your hosting company.)';

            return [
                'desc'     => $desc,
                'severity' => 'fail',
            ];
        }

        return [
            'desc'     => 'Your installation of WordPress doesn\'t require FTP credentials to perform updates.',
            'severity' => 'pass',
        ];
    }

    private function test_all_files_writable()
    {
        global $wp_filesystem;
        include ABSPATH . WPINC . '/version.php';
        $skin    = new \Automatic_Upgrader_Skin;
        $success = $skin->request_filesystem_credentials(false, ABSPATH);
        if ( ! $success) {
            return false;
        }
        WP_Filesystem();
        if ('direct' != $wp_filesystem->method) {
            return false;
        }
        $checksums = get_core_checksums($wp_version, 'en_US');
        $dev       = (false !== strpos($wp_version, '-'));
        if ( ! $checksums && $dev) {
            $checksums = get_core_checksums((float)$wp_version - 0.1, 'en_US');
        }
        if ( ! $checksums && $dev) {
            return false;
        }
        if ( ! $checksums) {
            $desc = 'Couldn\'t retrieve a list of the checksums for WordPress ' . $wp_version;
            $desc .= ' ' . 'This could mean that connections are failing to WordPress.org.';

            return [
                'desc'     => $desc,
                'severity' => 'warning',
            ];
        }
        $unwritable_files = [];
        foreach (array_keys($checksums) as $file) {
            if ('wp-content' == substr($file, 0, 10)) {
                continue;
            }
            if ( ! file_exists(ABSPATH . '/' . $file)) {
                continue;
            }
            if ( ! is_writable(ABSPATH . '/' . $file)) {
                $unwritable_files[] = $file;
            }
        }
        if ($unwritable_files) {
            if (count($unwritable_files) > 20) {
                $unwritable_files   = array_slice($unwritable_files, 0, 20);
                $unwritable_files[] = '...';
            }

            return [
                'desc'     => 'Some files are not writable by WordPress:' . ' <ul><li>' . implode('</li><li>', $unwritable_files) . '</li></ul>',
                'severity' => 'fail',
            ];
        } else {
            return [
                'desc'     => 'All of your WordPress files are writable.',
                'severity' => 'pass',
            ];
        }
    }

    private function test_accepts_dev_updates()
    {
        include ABSPATH . WPINC . '/version.php';
        if (false === strpos($wp_version, '-')) {
            return false;
        }
        if (defined('WP_AUTO_UPDATE_CORE') && ('minor' === WP_AUTO_UPDATE_CORE || false === WP_AUTO_UPDATE_CORE)) {
            return [
                'desc'     => 'WordPress development updates are blocked by the <code>WP_AUTO_UPDATE_CORE</code> constant.',
                'severity' => 'fail',
            ];
        }
        if ( ! apply_filters('allow_dev_auto_core_updates', $wp_version)) {
            return [
                'desc'     => 'WordPress development updates are blocked by the <code>allow_dev_auto_core_updates</code> filter.',
                'severity' => 'fail',
            ];
        }

        return false;
    }

    private function test_accepts_minor_updates()
    {
        if (defined('WP_AUTO_UPDATE_CORE') && false === WP_AUTO_UPDATE_CORE) {
            return [
                'desc'     => 'WordPress security and maintenance releases are blocked by <code>define( \'WP_AUTO_UPDATE_CORE\', false );</code>.',
                'severity' => 'fail',
            ];
        }
        if ( ! apply_filters('allow_minor_auto_core_updates', true)) {
            return [
                'desc'     => 'WordPress security and maintenance releases are blocked by the <code>allow_minor_auto_core_updates</code> filter.',
                'severity' => 'fail',
            ];
        }

        return false;
    }
}
