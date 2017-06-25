<?php

namespace Nanga\Features;

class Cron
{

    public static function init()
    {
        add_filter('cron_schedules', [self::class, 'intervals']);
        // add_action('nanga_monthly_schedule', [self::class, 'transients']);
    }

    public static function intervals($schedules)
    {
        $schedules['weekly']  = [
            'interval' => 604800,
            'display'  => 'Once Weekly',
        ];
        $schedules['monthly'] = [
            'interval' => 2635200,
            'display'  => 'Once Monthly',
        ];

        return $schedules;
    }

    public static function transients()
    {
        global $wpdb;
        $sql = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "_transient_%"';
        $wpdb->query($sql);
    }

    public static function _transients($older_than = '1 week', $safemode = true)
    {
        global $wpdb;
        $older_than_time = strtotime('-' . $older_than);
        if ($older_than_time > time() || $older_than_time < 1) {
            return false;
        }
        //$transients =    $wpdb->get_col( $wpdb->prepare( "SELECT REPLACE(option_name, '_transient_timeout_', '') AS transient_name FROM $wpdb->options WHERE option_name LIKE '\_transient\_timeout\__%%' AND option_value < %s", $older_than_time ) );
        $site_transients = $wpdb->get_col($wpdb->prepare("SELECT REPLACE(option_name, '_site_transient_timeout_', '') AS transient_name FROM $wpdb->options WHERE option_name LIKE '_site_transient_timeout__%%' AND option_value < %s", $older_than_time));
        $transients      = $wpdb->get_col($wpdb->prepare("SELECT REPLACE(option_name, '_transient_timeout_', '') AS transient_name FROM $wpdb->options WHERE option_name LIKE '_transient_timeout__%%' AND option_value < %s", $older_than_time));
        if ($safemode) {
            foreach ($site_transients as $site_transient) {
                delete_site_transient($site_transient);
            }
            foreach ($transients as $transient) {
                delete_transient($transient);
            }
        }

        return $transients;
    }
}
