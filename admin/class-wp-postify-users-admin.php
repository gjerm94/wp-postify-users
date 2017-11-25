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
	 * Adds a management page under the Tools submenu
	 *
	 * @since    1.0.0
	 */
	public function add_tools_page()
	{
		add_management_page("WP Postify Users", "Postify Users", 'manage_options', "wp-postify-users", array($this, 'display_tools_page'));
	}

	/**
	 * Render the tools page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_tools_page()
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
				$insert_post_notice = $helper->remove_user_posts();	
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
