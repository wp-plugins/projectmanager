<?php
if ( !current_user_can( 'manage_projects' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$options = get_option( 'projectmanager' );
$project_id = $projectmanager->getProjectID();
if ( isset($_POST['saveSettings']) ) {
	check_admin_referer('projectmanager_manage-settings');
	$options = get_option( 'projectmanager' );
	$options[$_POST['project_id']]['per_page'] = $_POST['per_page'];
	$options[$_POST['project_id']]['category'] = $_POST['category'];
	$options[$_POST['project_id']]['show_image'] = isset( $_POST['show_image']) ? 1 : 0;
	$options[$_POST['project_id']]['use_widget'] = isset( $_POST['use_widget'] ) ? 1 : 0;
	$options[$_POST['project_id']]['thumb_size'] = array( "width" => $_POST['thumb_width'], "height" => $_POST['thumb_height'] );
     	$options[$_POST['project_id']]['medium_size'] = array( "width" => $_POST['medium_width'], "height" => $_POST['medium_height'] );
     	$options[$_POST['project_id']]['navi_link'] = isset( $_POST['navi_link'] ) ? 1 : 0;
		
	$projectmanager->editProject( $_POST['project_title'], $_POST['project_id'] );
	update_option( 'projectmanager', $options );
	
	echo '<div id="message" class="updated fade"><p><strong>'.__( 'Settings saved', 'projectmanager' ).'</strong></p></div>';
}
?>

<div class="wrap">
	<?php $projectmanager->printBreadcrumb( __( 'Settings', 'projectmanager' ) ) ?>
	
	<form action="edit.php?page=projectmanager/page/settings.php&amp;project_id=<?php echo $project_id ?>" method="post">
		<?php wp_nonce_field( 'projectmanager_manage-settings' ) ?>
		
		<h2><?php _e( 'Settings', 'projectmanager' ) ?></h2>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="project_title"><?php _e( 'Title', 'projectmanager' ) ?></label></td><td><input type="text" name="project_title" id="project_title" value="<?php echo $projectmanager->getProjectTitle( ) ?>" size="30" style="margin-bottom: 1em;" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="per_page"><?php _e( 'Datasets per page', 'projectmanager' ) ?></label></th><td><input type="text" name="per_page" id="per_page" size="2" value="<?php echo $options[$project_id]['per_page'] ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="category"><?php _e( 'Category', 'projectmanager' ) ?></label><td><?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'category', 'orderby' => 'name', 'selected' => $options[$project_id]['category'], 'hierarchical' => true, 'show_option_none' => __('None'))); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="use_widget"><?php _e( 'Use Widget', 'projectmanager' ) ?></label></th><td><input type="checkbox" name="use_widget" id="use_widget"<?php if ( 1 == $options[$project_id]['use_widget']  ) echo ' checked="checked"'; ?> value="1" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="navi_link"><?php _e( 'Navi Link', 'projectmanager' ) ?></th><td><input type="checkbox" name="navi_link" id="navi_link" value="1"<?php if ( 1 == $options[$project_id]['navi_link']  ) echo ' checked="checked"'; ?> /><br /><?php _e( 'Set this option to add a direct link in the navigation panel. If there is only one project in the database, the link to the index page will be disabled.', 'projectmanager' ) ?></td>
		</tr>
		</table>
		
		<h3><?php _e( 'Images', 'projectmanager' ) ?></h3>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="show_image"><?php _e( 'Show Image', 'projectmanager' ) ?></label></th><td><input type="checkbox" name="show_image" id="show_image"<?php if ( 1 == $options[$project_id]['show_image'] ) echo ' checked="checked"' ?> value="1"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="thumb_size"><?php _e( 'Thumbnail size', 'projectmanager' ) ?></label></th><td><label for="thumb_width"><?php _e( 'Width' ) ?>&#160;</label><input type="text" name="thumb_width" id="thumb_width" size="3" value="<?php echo $options[$project_id]['thumb_size']['width'] ?>" />  <label for="thumb_height"><?php _e( 'Height' ) ?>&#160;</label><input type="text" name="thumb_height" id="thumb_height" size="3" value="<?php echo $options[$project_id]['thumb_size']['height'] ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="medium_size"><?php _e( 'Medium size', 'projectmanager' ) ?></label></th><td><label for="medium_width"><?php _e( 'Max Width' ) ?>&#160;</label><input type="text" id="medium_width" name="medium_width" size="3" value="<?php echo $options[$project_id]['medium_size']['width'] ?>" /> <label for="medium_height"><?php _e( 'Max Height' ) ?>&#160;</label> <input type="text" id="medium_height" name="medium_height" size="3" value="<?php echo $options[$project_id]['medium_size']['height'] ?>" /></td>
		</tr>
		</table>
		
		<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
		<p class="submit"><input type="submit" name="saveSettings" value="<?php _e( 'Save Settings', 'projectmanager' ) ?> &raquo;" class="button" /></p>
	</form> 
</div>

<?php endif; ?>