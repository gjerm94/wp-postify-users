<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/gjerm94
 * @since      1.0.0
 *
 * @package    Wp_Postify_Users
 * @subpackage Wp_Postify_Users/admin/partials
 */
?>
<?php
if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}


?>

<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <form action="options.php" method="post">
        <?php
            settings_fields( $this->plugin_name );
            do_settings_sections( $this->plugin_name );
            submit_button();
        ?>
    </form>
</div>

<div class="wrap">
	<form name="wppu_postify_form" id="wppu_postify_form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<fieldset>
			<legend>Press the button below to postify your users</legend>
			<?php wp_nonce_field('wppu_postify', 'postify'); ?>
			<input type="hidden" name="action" value="wppu_generate_posts">
			<input class="button-primary" type="submit" name="wppu_postify" value="Postify!"/>
			<input class="button-primary" type="submit" name="wppu_remove_posts" value="Remove user posts"/>
		</fieldset>
	</form>
</div>	

</form>

<?php 

	echo get_option('wppu_post_type_name');


	global $wpdb;
	
	$users = get_users();
	echo COUNT($users);

	foreach( $users as $user ) {
		$user_id = $user->id;
		//$username = $user->user_login;
		//var_dump($user->data);
		//var_dump(get_user_meta( $user_id, 'Gårdsnavn' )); 
		//echo "<br />";
$helper = new Wp_Postify_Users_Helper($this->plugin_name, $this->version);
$user_post = $helper->get_post_by_user_id($user_id);
		if ( bp_has_profile() ) {
					while ( bp_profile_groups() ) : bp_the_profile_group();
						while ( bp_profile_fields() ) : bp_the_profile_field();
							global $field;
	      					$fieldname = bp_unserialize_profile_field( $field->name );
	      					$fieldvalue = (bp_get_profile_field_data('field='. $fieldname .'&user_id='. $user_id));
	      					$user_val[$fieldname] = $fieldvalue;
	      					
	      					$post_val = get_post_meta($user_post->ID, $fieldname);
	      					
	      					//var_dump($post_val);
	         			endwhile; //fields
					endwhile; //groups
					//var_dump($userval);
				}
				
	      					
	      					//var_dump($user_post);
	}



	echo "<br />";
	
	$args = array(
		'post_type' => 'WPPUser',
		'posts_per_page' => -1
	);
	$query = new WP_Query($args);

	if ( $query->have_posts() ) {
		$wppusers = $query->get_posts();
		echo COUNT($wppusers);
	}

	/**$posts_table = $wpdb->posts;
	$query = "
			SELECT * FROM {$posts_table}
			WHERE post_type = 'WPPUser'
		";
	$wppusers = $wpdb->get_results($query);*/

	

	/**$mycustomposts = get_posts( array( 'post_type' => 'WPPUser', 'posts_per_page' => -1 ) );
	foreach ( $mycustomposts as $post ) {
		
		var_dump(get_post_meta( $post->ID, '_user' ));
	}**/




?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
