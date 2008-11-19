<?php
if ( !current_user_can( 'manage_projects' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$project_id = $_GET['project_id'];
$projectmanager->setSettings( $project_id );
if ( isset($_POST['saveSettings']) ) {
	check_admin_referer('projectmanager_manage-settings');
	$options = get_option( 'projectmanager' );
	$options[$_POST['project_id']]['per_page'] = $_POST['per_page'];
	$options[$_POST['project_id']]['category'] = $_POST['category'];
	$options[$_POST['project_id']]['show_image'] = isset($_POST['show_image']) ? 1 : 0;
	$options[$_POST['project_id']]['groups'] = $_POST['groups'];
	$options[$_POST['project_id']]['use_widget'] = isset( $_POST['use_widget'] ) ? 1 : 0;
	$options[$_POST['project_id']]['thumb_size'] = array("width" => $_POST['thumb_width'], "height" => $_POST['thumb_height']);
		
	$projectmanager->editProject( $_POST['project_title'], $_POST['project_id'] );
	update_option( 'projectmanager', $options );
	$return_message = 'Settings saved';
	
	echo '<div id="message" class="updated fade"><p><strong>'.__( $return_message, 'projectmanager' ).'</strong></p></div>';
}

$options = get_option( 'projectmanager' );
?>

<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_id, 'Settings' ) ?>
	
	<form action="edit.php?page=projectmanager/page/settings.php&amp;project_id=<?php echo $project_id ?>" method="post">
		<?php wp_nonce_field( 'projectmanager_manage-settings' ) ?>
		
		<h2><?php _e( 'Settings', 'projectmanager' ) ?></h2>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="project_title"><?php _e( 'Title', 'projectmanager' ) ?></label></td><td><input type="text" name="project_title" id="project_title" value="<?php echo $projectmanager->getProjectTitle( $project_id ) ?>" size="30" style="margin-bottom: 1em;" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="per_page"><?php _e( 'Datasets per page', 'projectmanager' ) ?></label></th><td><input type="text" name="per_page" id="per_page" size="2" value="<?php echo $options[$project_id]['per_page'] ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="category"><?php _e( 'Category', 'projectmanager' ) ?></label><td><?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'category', 'orderby' => 'name', 'selected' => $options[$project_id]['category'], 'hierarchical' => true, 'show_option_none' => __('None'))); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php if ( 1 == $options[$project_id]['use_widget']  ) $selected = ' checked="checked"'; else $selected = ''; ?><label for="use_widget"><?php _e( 'Use Widget', 'projectmanager' ) ?></label></th><td><input type="checkbox" name="use_widget" id="use_widget"<?php echo $selected ?> value="1"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php if ( 1 == $options[$project_id]['show_image'] ) $selected = ' checked="checked"'; else $selected = ''; ?><label for="show_image"><?php _e( 'Show Image', 'projectmanager' ) ?></label></th><td><input type="checkbox" name="show_image" id="show_image"<?php echo $selected ?> value="1"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="thumb_size"><?php _e( 'Thumbnail size', 'projectmanager' ) ?></label></th><td><input type="text" name="thumb_width" size="3" value="<?php echo $options[$project_id]['thumb_size']['width'] ?>" /> x <input type="text" name="thumb_height" size="3" value="<?php echo $options[$project_id]['thumb_size']['height'] ?>" /></td>
		</tr>
		</table>
		
		<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
		<p class="submit"><input type="submit" name="saveSettings" value="<?php _e( 'Save Settings', 'projectmanager' ) ?> &raquo;" class="button" /></p>
	</form> 
</div>

<?php endif; ?>