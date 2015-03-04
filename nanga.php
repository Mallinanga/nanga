<?php
/**
 * @wordpress-plugin
 * Plugin Name:       VG web things
 * Plugin URI:        https://github.com/Mallinanga/nanga
 * GitHub Plugin URI: https://github.com/Mallinanga/nanga
 * Description:       Functions that don't belong to the theme.
 * Version:           1.0.9
 * Author:            Panos Paganis
 * Author URI:        https://github.com/Mallinanga
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nanga
 * Domain Path:       /languages
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
    require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );
}
function activate_nanga() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-nanga-activator.php';
    Nanga_Activator::activate();
}

function deactivate_nanga() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-nanga-deactivator.php';
    Nanga_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_nanga' );
register_deactivation_hook( __FILE__, 'deactivate_nanga' );
require plugin_dir_path( __FILE__ ) . 'includes/nanga-helpers.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-nanga.php';
if ( file_exists( plugin_dir_path( __FILE__ ) . 'includes/nanga-limbo.php' ) ) {
    require plugin_dir_path( __FILE__ ) . 'includes/nanga-limbo.php';
}
if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/zamoose/themehookalliance/tha-theme-hooks.php' ) ) {
    require plugin_dir_path( __FILE__ ) . 'vendor/zamoose/themehookalliance/tha-theme-hooks.php';
}
function run_nanga() {
    $plugin = new Nanga();
    $plugin->run();
}

run_nanga();
