<?php
/**
 * @link              https://github.com/Mallinanga
 * @since             1.0.0
 * @package           Nanga
 *
 * @wordpress-plugin
 * Plugin Name:       VG web things
 * Plugin URI:        https://github.com/VGwebthings/vg-plugin
 * GitHub Plugin URI: https://github.com/VGwebthings/vg-plugin
 * Description:       Functions that don't belong to the theme.
 * Version:           1.0.0
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
require plugin_dir_path( __FILE__ ) . 'includes/class-nanga.php';
require plugin_dir_path( __FILE__ ) . 'includes/nanga-general.php';
require plugin_dir_path( __FILE__ ) . 'includes/nanga-extras.php';
function run_nanga() {
    $plugin = new Nanga();
    $plugin->run();
}

run_nanga();
