<?php

/**
 * Plugin Name:       MagniPOS
 * Plugin URI:        https://www.magnipos.com/
 * Description:       MagniPos is a Point of Sale System(POS) for WooCommerce
 * Version:           1.3.1
 * Author:            MagniGenie
 * Author URI:        https://www.magnigenie.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       magni-pos
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MAGNI_POS_VERSION', '1.3.1' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-magni-pos-activator.php
 */
function activate_magni_pos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-magni-pos-activator.php';
	Magni_Pos_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-magni-pos-deactivator.php
 */
function deactivate_magni_pos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-magni-pos-deactivator.php';
	Magni_Pos_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_magni_pos' );
register_deactivation_hook( __FILE__, 'deactivate_magni_pos' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-magni-pos.php';


// Helper constants.
define( 'MAGNI_JWT_AUTH_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'MAGNI_JWT_AUTH_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'MAGNI_JWT_AUTH_PLUGIN_VERSION', '2.1.0' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_magni_pos() {

	$plugin = new Magni_Pos();
	$plugin->run();

}
run_magni_pos();