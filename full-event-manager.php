<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * this starts the plugin.
 *
 * @link:             https://jonnas1982.github.io/Full-Event-Manager/
 * @since             0.0.1
 * @package           full_event_manager
 *
 * @wordpress-plugin
 * Plugin Name:       Full Event Manager
 * Plugin URI:        https://jonnas1982.github.io/Full-Event-Manager/
 * Description:       Description
 * Version:           0.0.1
 * Author:            jonnas1982
 * Author URI:        https://jonnas1982.github.io
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       full-event-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-full-event-manager-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-full-event-manager-deactivator.php';

/** This action is documented in includes/class-full-event-manager-activator.php */
register_activation_hook( __FILE__, array( 'full-event-manager_Activator', 'activate' ) );

/** This action is documented in includes/class-full-event-manager-deactivator.php */
register_activation_hook( __FILE__, array( 'full-event-manager_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-full-event-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_FullEventManager() {

	$plugin = new FullEventManager();
	$plugin->run();

}
run_FullEventManager();
