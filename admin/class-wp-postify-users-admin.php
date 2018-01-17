<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/gjerm94
 * @since      1.0.0
 *
 * @package    Wp_Postify_Users
 * @subpackage Wp_Postify_Users/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Postify_Users
 * @subpackage Wp_Postify_Users/admin
 * @author     gjerm94 <gjermundbakken94@gmail.com>
 */
class Wp_Postify_Users_Admin {

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
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_name = 'wppu';

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Postify_Users_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Postify_Users_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-postify-users-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Postify_Users_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Postify_Users_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-postify-users-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the settings for the plugin
	 *
	 * @since    1.0.0
	 */
	public function register_setting() {
		// Add a General section
		add_settings_section(
			$this->option_name . '_general',
			__( 'General', 'wp-postify-users' ),
			array( $this, $this->option_name . '_general_cb' ),
			$this->plugin_name
		);

		/**
		 * Register the post type name settings field (text input)
		 */
		add_settings_field(
			$this->option_name . '_post_type_name',
			__( 'Name of the custom post type: ', 'wp-postify-users' ),
			array( $this, $this->option_name . '_post_type_name_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_post_type_name' )
		);

		/**
		 * Register the include custom fields settings field (checkbox)
		 */ 
		add_settings_field(
			$this->option_name . '_include_custom_fields',
			__( 'Include user custom fields in the posts (if available) ', 'wp-postify-users' ),
			array( $this, $this->option_name . '_include_custom_fields_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_include_custom_fields' )
		);
		
		/**
		 * Register the post-content settings field (select)
		 */ 
		add_settings_field(
			$this->option_name . '_post_content',
			__( 'Use this field value as post-contents: ', 'wp-postify-users' ),
			array( $this, $this->option_name . '_post_content_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_post_content' )
		);

		register_setting( $this->plugin_name, $this->option_name . '_post_type_name', $args );
		register_setting( $this->plugin_name, $this->option_name . '_include_custom_fields', $args );
		register_setting( $this->plugin_name, $this->option_name . '_post_content', $args );
	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function wppu_general_cb() {
		echo '<p>' . __( 'Please change the settings accordingly.', 'wp-postify-users' ) . '</p>';
	}

	/**
	 * Render the posttype name input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function wppu_post_type_name_cb() {
		$post_type_name = get_option('wppu_post_type_name');
		echo '<input type="text" name="' . $this->option_name . '_post_type_name' . '" id="' . $this->option_name . '_post_type_name' . '" value="' . $post_type_name . '"> ';
	}

	/**
	 * Render the include custom fields input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function wppu_include_custom_fields_cb() {
		$include_custom_fields = get_option('wppu_include_custom_fields');
		echo "<input type='checkbox' name='" . $this->option_name . '_include_custom_fields' . "' id='" . $this->option_name . "_include_custom_fields'" . checked($include_custom_fields, "on", false) . "'> ";
	}

	/**
	 * Render the post-content field input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function wppu_post_content_cb() {
		//Fetch the xprofile field names to populate the select
		//Should probably find a way to see if xprofile exists in the future
		$profile_groups = BP_XProfile_Group::get( array( 'fetch_fields' => true	) );

		echo "<select name='" . $this->option_name . '_post_content' . "' id='" . $this->option_name . '_post_content' . "'>";
		if ( !empty( $profile_groups ) ) {
			foreach ( $profile_groups as $profile_group ) {
				if ( !empty( $profile_group->fields ) ) {				
					foreach ( $profile_group->fields as $field ) {
						echo "<option id='" . $field->id . "' value='" . $field->name . "'>" . $field->name . "</option>";
					}
				}
			}
		}
		echo "</select>";
	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since    1.0.0
	 */
	public function add_options_page()
	{
		add_options_page("WP Postify Users", "Postify Users", 'manage_options', "wp-postify-users", array($this, 'display_options_page'));
	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/wp-postify-users-admin-display.php';
	}

	/**
	 * Handles the post event for the tools page form
	 *
	 * @since    1.0.0
	 */
	public function admin_form_posted() {
		
		if ( !current_user_can( 'manage_options' ) )  {
    		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		if(!empty($_POST['postify'])) { 
			
			$helper = new Wp_Postify_Users_Helper($this->plugin_name, $this->version);
			
			
			if (!empty($_POST['wppu_postify'])) {
				$insert_post_notice = $helper->register_user_posts();
			} elseif(!empty($_POST['wppu_remove_posts'])) {
				$insert_post_notice = $helper->remove_all_user_posts();	
			}

			// server response
			// TODO: Need better ways to test this
			if($insert_post_notice) {
				$admin_notice = "success";
			}

			$this->custom_redirect( $admin_notice, $_POST, $insert_post_notice );
			exit;
		}

	}

	/**
	 * Redirect
	 * 
	 * @since    1.0.0
	 */
	public function custom_redirect( $admin_notice, $response, $insert_post_notice ) {
		wp_redirect( esc_url_raw( add_query_arg( array(
									'wppu_admin_add_notice' => $admin_notice,
									'wppu_response' => $response,
									'wppu_post_count_notice' => $insert_post_notice
									),
							admin_url('admin.php?page='. $this->plugin_name ) 
					) ) );
	}

	/**
	 * Print Admin Notices
	 * 
	 * @since    1.0.0
	 */
	public function print_plugin_admin_notices() {              
		  if ( isset( $_REQUEST['wppu_admin_add_notice'] ) ) {
			if( $_REQUEST['wppu_admin_add_notice'] === "success") {
				$html =	'<div class="notice notice-success is-dismissible"> 
							<p><strong>The request was successful. </strong></p><br>';
				$html .= '<pre>' . htmlspecialchars( print_r( $_REQUEST['wppu_post_count_notice'], true) ) . '</pre></div>';
				echo $html;
			}
			
			// handle other types of form notices
		  }
		  else {
			  return;
		  }
	}

}
