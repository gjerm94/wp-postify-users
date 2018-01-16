<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/gjerm94
 * @since             1.0.0
 * @package           Wp_Postify_Users
 *
 * @wordpress-plugin
 * Plugin Name:       WP Postify Users
 * Plugin URI:        https://github.com/gjerm94/wp-postify-users
 * Description:       A plugin for WordPress that generates posts for each registered user on your site.
 * Version:           1.0.0
 * Author:            gjerm94
 * Author URI:        https://github.com/gjerm94
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-postify-users
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-postify-users-activator.php
 */
function activate_wp_postify_users() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-postify-users-activator.php';
	Wp_Postify_Users_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-postify-users-deactivator.php
 */
function deactivate_wp_postify_users() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-postify-users-deactivator.php';
	Wp_Postify_Users_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_postify_users' );
register_deactivation_hook( __FILE__, 'deactivate_wp_postify_users' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-postify-users.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_postify_users() {

	$plugin = new Wp_Postify_Users();
	$plugin->run();

}
run_wp_postify_users();
