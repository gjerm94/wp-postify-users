<?php

/**
 * Define the core functionality
 *
 * Loads and defines the core functionality for this plugin
 * which will be used throughout the plugin lifecycle.
 *
 * @link       https://github.com/gjerm94
 * @since      1.0.0
 *
 * @package    Wp_Postify_Users
 * @subpackage Wp_Postify_Users/includes
 */

/**
 * Define the core functionality
 *
 * Loads and defines the core functionality for this plugin
 * that will be used throughout the plugin lifecycle.
 *
 * @since      1.0.0
 * @package    Wp_Postify_Users
 * @subpackage Wp_Postify_Users/includes
 * @author     gjerm94 <gjermundbakken94@gmail.com>
 */
class Wp_Postify_Users_Helper {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the "WPPUsers" custom post type.
	 *
	 * @since    1.0.0
	 */
	public function register_custom_post_type() {

		/**
		 * Post Type: BPPMembers.
		 */

		$labels = array(
			"name" => __( "WPPUsers", "" ),
			"singular_name" => __( "WPPUser", "" ),
		);

		$args = array(
			"label" => __( "WPPUsers", "" ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => false,
			"show_in_menu" => true,
			"exclude_from_search" => true,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"query_var" => true,
			"supports" => array( "title", "thumbnail", "excerpt", "custom-fields" ),
			"taxonomies" => array( "category" ),
		);

		register_post_type( "WPPUser", $args );
	}

	/**
	 * Inserts the posts for each registered user.
	 *
	 * @since    1.0.0
	 */
	public function register_user_posts() {
							
		//get all the registered users
		global $wpdb;
		$users = get_users();

		$posts_table = $wpdb->posts;
		$query = "
				SELECT * FROM {$posts_table}
				WHERE post_type = 'WPPUser'
			";
		$wppusers = $wpdb->get_results($query);
		
		$post_count = 0;
		
		foreach( $users as $user ) {
			
			//get all the needed field data from current member here
			$user_id = $user->id;
			$username = $user->user_login;			

			//make a new post with the 'BPPMember' type
			$member_post_arr = array(
				'post_title'   => $username,
				'post_status'  => 'publish',
				'post_type' => 'WPPUser'
				
			);

			$post_id = wp_insert_post( $member_post_arr );

			//store the user data in a hidden field for updating purposes
			add_post_meta($post_id, "_user", $user->data);

			if($post_id) {
				//post was successfully registered
				$post_count++;
			}
			//modify the postdata to match the users xprofile field values
			//Possible bug: Arrays/multivalue fields do not show up in backend
			/**if ( bp_has_profile() ) {
				while ( bp_profile_groups() ) : bp_the_profile_group();
					while ( bp_profile_fields() ) : bp_the_profile_field();
						global $field;
      					$fieldname = bp_unserialize_profile_field( $field->name );
      					$fieldvalue = (bp_get_profile_field_data('field='. $fieldname .'&user_id='. $id));
      					add_post_meta($post_id, $fieldname, $fieldvalue);
         			endwhile; //fields
					endwhile; //groups
		
			}*/
			
		}	

		$post_count_notice = $post_count . " posts generated.";
		return $post_count_notice;
	}

	/**
	 * Remove all registered user posts
	 * 
	 * @since 1.0.0
	 */
	public function remove_user_posts() {

		global $wpdb;

		$posts_table = $wpdb->posts;

		$query = "
		  DELETE FROM {$posts_table}
		  WHERE post_type = 'WPPUser' 
		";

		$post_count = $wpdb->query($query);
		$post_count_notice = $post_count . " posts deleted.";

		return $post_count_notice;

	}

	/**
	 * Check if user info changed since last time the post was inserted
	 *
	 * @since 1.0.0
	 */
	public function user_info_is_updated($user_id) {

	}
}