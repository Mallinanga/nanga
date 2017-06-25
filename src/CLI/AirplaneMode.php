<?php

namespace Nanga\CLI;

use WP_CLI_Command;
use Nanga\Features\AirplaneMode as AirplaneModeFeature;

/**
 * Disable all external HTTP requests.
 */
class AirplaneMode extends WP_CLI_Command
{

    /**
     * Enables airplane mode.
     *
     * @when       after_wp_load
     * @subcommand on
     * @alias      enable
     */
    function enable()
    {
        AirplaneModeFeature::getInstance()->enable();
        \WP_CLI::success('Airplane mode was enabled');
    }

    /**
     * Disables airplane mode.
     *
     * @when       after_wp_load
     * @subcommand off
     * @alias      disable
     */
    function disable()
    {
        AirplaneModeFeature::getInstance()->disable();
        \WP_CLI::success('Airplane mode was disabled');
    }

    /**
     * Provides the status of airplane mode.
     *
     * @when after_wp_load
     */
    function status()
    {
        $on = 'on' === get_site_option('nanga-airplane-mode');
        \WP_CLI::success($on ? 'Airplane mode is enabled' : 'Airplane mode is disabled');
    }

    /**
     * Purge the transients set from airplane mode.
     *
     * @when after_wp_load
     */
    function clean()
    {
        AirplaneModeFeature::getInstance()->purge_transients(true);
        \WP_CLI::success('Transients have been cleared');
    }
}
