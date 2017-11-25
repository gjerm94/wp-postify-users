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
<form name="wppu_postify_form" id="wppu_postify_form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<fieldset>
		<legend>Press the button below to postify your users</legend>
		<?php wp_nonce_field('wppu_postify', 'postify'); ?>
		<input type="hidden" name="action" value="wppu_generate_posts">
	<input class="button-primary" type="submit" name="wppu_postify" value="Postify!"/>
	<input class="button-primary" type="submit" name="wppu_remove_posts" value="Remove user posts"/>
	</fieldset>
	

</form>

<?php 
	global $wpdb;
	
	$users = get_users();
	echo COUNT($users);

	foreach( $users as $user ) {
		$user_id = $user->id;
		$username = $user->user_login;
		//var_dump($user->data);
	}
	echo "<br />";
	
	$posts_table = $wpdb->posts;
	$query = "
			SELECT * FROM {$posts_table}
			WHERE post_type = 'WPPUser'
		";
	$wppusers = $wpdb->get_results($query);
	echo COUNT($wppusers);

	//$user = get_user_by('id',79);
	//echo $user->data;

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->