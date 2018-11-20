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
		 * Post Type: WPPUsers.
		 */

		$post_type = get_option('wppu_post_type_name');

		if ( !$post_type ) {
			$post_type = 'WPPUsers'; 
		}

		$labels = array(
			"name" => __( $post_type, "" ),
			"singular_name" => __( $post_type, "" ),
		);

		$args = array(
			"label" => __( $post_type, "" ),
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
			"supports" => array( "title", "thumbnail", "excerpt", "custom-fields", "editor" ),
			"taxonomies" => array( "category" )
		);

		register_post_type( $post_type, $args );
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

		$post_count = 0;
		
		//loop through each user
		foreach( $users as $user ) {
			
			//get all the needed field data from current member here
			$user_id = $user->id;

			//create a post if the current user has no associated post OR user has updated their data
			if ( $this->user_info_is_updated($user_id)) {
				
				//remove old post if updated user data
				//TODO: It would probably be better to update the post instead of deleting the old one
				if ( $this->user_info_is_updated($user_id) && $this->get_post_by_user_id($user_id) ) {
					//TODO: send post id here, not user id
					$this->remove_user_post($user_id);
				}
				$username = $user->user_login;

				//get the name of the field in the post content option			
				$post_content = get_option('wppu_post_content');

				//get value of post content field
				$post_content = bp_get_profile_field_data('field=' . $post_content . '&user_id=' . $user_id);

				//make a new post with the 'BPPMember' type
				$member_post_arr = array(
					'post_title'   => $username,
					'post_content' => $post_content,
					'post_status'  => 'publish',
					'post_type' => 'WPPUser'
				);

				$post_id = wp_insert_post( $member_post_arr );

				//store the user data in a hidden field for updating purposes				
				add_post_meta($post_id, "_user_id", $user_id);

				if($post_id) {
					//post was successfully registered
					$post_count++;
				}

				//modify the postdata to match the users xprofile field values
				if ( bp_has_profile() ) {
					while ( bp_profile_groups() ) : bp_the_profile_group();
						while ( bp_profile_fields() ) : bp_the_profile_field();
							global $field;
	      					$field_name = bp_unserialize_profile_field( $field->name );
	      					$field_value = bp_get_profile_field_data( 'field='. $field_name .'&user_id='. $user_id );
							add_post_meta($post_id, $field_name, $field_value);
	         			endwhile; //fields
					endwhile; //groups
				}

				//set thumbnail
				//TODO: create a way to check if user avatar has changed
				$avatar_url = get_avatar_url($user_id);
				if ( $avatar_url ) {
					$this->generate_thumbnail($avatar_url, $post_id, $username);
				}

				
			}
		}	

		$post_count_notice = $post_count . " posts generated.";
		return $post_count_notice;
	}

	/**
	 * Remove all registered user posts
	 * 
	 * @since 1.0.0
	 */
	public function remove_all_user_posts() {
		
		$user_posts = get_posts( array( 'post_type' => 'WPPUser', 'posts_per_page' => -1 ) );
		
		$post_count = 0;
		
		foreach( $user_posts as $post ) {
     		$this->remove_user_post( $post->ID );
			
    		$post_count++;	
   		}
		
		$post_count_notice = $post_count . " posts deleted.";

		return $post_count_notice;

	}

	public function remove_user_post( $post_id ) {
		$media = get_children( array(
			        'post_parent' => $post_id,
			        'post_type'   => 'attachment'
			    ) );

			    if( ! empty( $media ) ) {
			        foreach( $media as $file ) {
			        	wp_delete_attachment( $file->ID );
			    	}
			    }

			    
     		// delete post
    		wp_delete_post( $post_id, true);
	}

	/**
	 * Check if user data changed/exists since last inserted post
	 *
	 * @since 1.0.0
	 */
	public function user_info_is_updated($user_id) {
		
		//check for changes in the user data
		$user = get_user_by('id',$user_id);
		$user_data = $user->data;

		$user_post = $this->get_post_by_user_id($user_id);
		
		if ( ! $user_post ) {
			return true;
		}

		$user_post_data = get_post_meta($user_post->ID, "_user");

		if ( $user_data != $user_post_data[0] ) {
			return true;
		}
		
		//check for changes in extended profile fields
		/**if ( bp_has_profile() ) {
			while ( bp_profile_groups() ) : bp_the_profile_group();
				//loop through each field
				while ( bp_profile_fields() ) : bp_the_profile_field();
					global $field;
  					$fieldname = bp_unserialize_profile_field( $field->name );
  					$fieldvalue = (bp_get_profile_field_data('field='. $fieldname .'&user_id='. $user_id));
  					  					
  					$post_val = get_post_meta($user_post->ID, $fieldname);
  					
  					if ( $fieldvalue !== $post_val[0]) {
  						//user has changed field value 
  						return true;
  					} 
     			endwhile; //fields
			endwhile; //groups
		}**/

		return false;
	}

	/**
	 * Gets a post with a users ID
	 * 
	 * @since 1.0.0
	 */
	public function get_post_by_user_id($user_id) {
		$args = array(
			'post_type' => 'WPPUser',
		   	'meta_query' => array(
		       array(
		           'key' => '_user_id',
		           'value' => $user_id,
		           'compare' => '=',
		       )
		   )
		);
		
		$query = new WP_Query($args);
		if ( $query->have_posts() ) {
			
			while ( $query->have_posts() ) {
				
				$query->the_post();
				$user_post_id = get_post_meta(get_the_ID(), "_user_id", true);
				
				if ($user_post_id == $user_id) {
					return $query->post;
				}
			}
		} 
		return false;
	}

	/**
	 * Generates a thumbnail for a post using the users avatar
	 */
	public function generate_thumbnail($image_url, $post_id, $username) {
		$upload_dir = wp_upload_dir();
	    $image_data = file_get_contents($image_url);
	    $filename = basename($image_url);
	    
	    if( !file_exists ( $filename ) ) { 
			if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
		    else                                    $file = $upload_dir['basedir'] . '/' . $filename;
		   
		    
		    file_put_contents($file, $image_data);

		    $wp_filetype = wp_check_filetype($filename, null );
		    $attachment = array(
		        'post_mime_type' => $wp_filetype['type'],
		        'post_title' => sanitize_file_name($username),
		        'post_content' => '',
		        'post_status' => 'inherit'
		    );
		    
		    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		    require_once(ABSPATH . 'wp-admin/includes/image.php');
		    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		    $res1 = wp_update_attachment_metadata( $attach_id, $attach_data );
		    $res2 = set_post_thumbnail( $post_id, $attach_id );
		}
	}
}