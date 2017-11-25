<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/gjerm94
 * @since      1.0.0
 *
 * @package    Wp_Postify_Users
 * @subpackage Wp_Postify_Users/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_Postify_Users
 * @subpackage Wp_Postify_Users/includes
 * @author     gjerm94 <gjermundbakken94@gmail.com>
 */
class Wp_Postify_Users_Deactivator {

	/**
	 * Delete postified users.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;

		$posts_table = $wpdb->posts;

		$query = "
		  DELETE FROM {$posts_table}
		  WHERE post_type = 'member' 
		";

		$wpdb->query($query);
	}

}
