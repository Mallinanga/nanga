<?php
if ( ! function_exists('write_log')) {
    function write_log($log)
    {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}
if ( ! function_exists('grab_dump')) {
    function grab_dump($var)
    {
        ob_start();
        print_r($var);

        return ob_get_clean();
    }
}
if ( ! function_exists('nanga_get_current_url')) {
    function nanga_get_current_url()
    {
        global $wp;

        return site_url(add_query_arg([], $wp->request));
    }
}
if ( ! function_exists('nanga_check_user_role')) {
    function nanga_check_user_role($role, $user_id = null)
    {
        if (is_numeric($user_id)) {
            $user = get_userdata($user_id);
        } else {
            $user = wp_get_current_user();
        }
        if (empty($user)) {
            return false;
        }

        return in_array($role, (array)$user->roles);
    }
}
if ( ! function_exists('nanga_log')) {
    function nanga_log($log)
    {
        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }
}
if ( ! function_exists('inet_pton')) {
    function inet_pton($ip)
    {
        if (strpos($ip, '.') !== false) {
            $ip = pack('N', ip2long($ip));
        } elseif (strpos($ip, ':') !== false) {
            $ip  = explode(':', $ip);
            $res = str_pad('', (4 * (8 - count($ip))), '0000', STR_PAD_LEFT);
            foreach ($ip as $seg) {
                $res .= str_pad($seg, 4, '0', STR_PAD_LEFT);
            }
            $ip = pack('H' . strlen($res), $res);
        }

        return $ip;
    }
}
if ( ! function_exists('nanga_site_in_development')) {
    function nanga_site_in_development()
    {
        return defined('WP_ENV') && 'development' === WP_ENV;
    }
}
if ( ! function_exists('nanga_site_in_production')) {
    function nanga_site_in_production()
    {
        $development = apply_filters('nanga_development_domain', '.vgwebthings.com');
        if (0 !== strpos($_SERVER['HTTP_HOST'], $development)) {
            return true;
        }
        if (defined('WP_ENV') && 'production' === WP_ENV) {
            return true;
        }
        if ( ! defined('WP_ENV')) {
            return true;
        }

        return false;
    }
}
if ( ! function_exists('nanga_site_is_legacy')) {
    function nanga_site_is_legacy()
    {
        return defined('NANGA_LEGACY') || current_theme_supports('nanga-legacy');
    }
}
if ( ! function_exists('nanga_site_is_external')) {
    function nanga_site_is_external()
    {
        return defined('NANGA_EXTERNAL') && NANGA_EXTERNAL;
    }
}
if ( ! function_exists('nanga_user_is_superadmin')) {
    function nanga_user_is_superadmin()
    {
        return current_user_can('manage_options') && ! defined('NANGA_EXTERNAL');
    }
}
