<?php
/**
 * @wordpress-plugin
 * Author URI: https://github.com/Mallinanga/
 * Author:      Panos Paganis
 * Description: Functions that don't belong to the theme.
 * Domain Path: /languages
 * Plugin Name: VG web things
 * Text Domain: nanga
 * Version:     2.0.2
 */

defined('WPINC') || die;

define('NANGA_VERSION', '2.0.2');
define('NANGA_DIR_PATH', plugin_dir_path(__FILE__));
define('NANGA_DIR_URL', plugin_dir_url(__FILE__));

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once NANGA_DIR_PATH . 'includes/extended-cpts.php';
require_once NANGA_DIR_PATH . 'includes/extended-taxos.php';
require_once NANGA_DIR_PATH . 'includes/helpers.php';
if (defined('NANGA_LEGACY') || current_theme_supports('nanga-legacy')) {
    require_once NANGA_DIR_PATH . 'includes/helpers-legacy.php';
}

register_activation_hook(__FILE__, ['\Nanga\Nanga', 'activate']);
register_deactivation_hook(__FILE__, ['\Nanga\Nanga', 'deactivate']);
register_uninstall_hook(__FILE__, ['\Nanga\Nanga', 'uninstall']);

$settings = \Nanga\Settings::instance();
$nanga    = \Nanga\Nanga::instance();
