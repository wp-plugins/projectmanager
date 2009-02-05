<?php
if ( !current_user_can( 'projectmanager_admin' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$options = get_option( 'projectmanager' );
$project_id = $projectmanager->getProjectID();
$projectmanager->getProject($project_id);

if ( isset($_POST['saveSettings']) ) {
	check_admin_referer('projectmanager_manage-settings');
	$options = get_option( 'projectmanager' );
	$project_id = $_POST['project_id'];
	$options['project_options'][$project_id]['per_page'] = $_POST['per_page'];
	$options['project_options'][$project_id]['category'] = $_POST['category'];
	$options['project_options'][$project_id]['dataset_orderby'] = $_POST['dataset_orderby'];
	$options['project_options'][$project_id]['show_image'] = isset( $_POST['show_image']) ? 1 : 0;
	$options['project_options'][$project_id]['use_widget'] = isset( $_POST['use_widget'] ) ? 1 : 0;
	$options['project_options'][$project_id]['thumb_size'] = array( "width" => $_POST['thumb_width'], "height" => $_POST['thumb_height'] );
     	$options['project_options'][$project_id]['medium_size'] = array( "width" => $_POST['medium_width'], "height" => $_POST['medium_height'] );
     	$options['project_options'][$project_id]['navi_link'] = isset( $_POST['navi_link'] ) ? 1 : 0;
     	$options['project_options'][$project_id]['profile_hook'] = isset($_POST['profile_hook'] ) ? 1 : 0;
	$options['project_options'][$project_id]['menu_icon'] = $_POST['menu_icon'];
		
	$this->editProject( $_POST['project_title'], $_POST['project_id'] );
	update_option( 'projectmanager', $options );
	
	$this->setMessage(__( 'Settings saved', 'projectmanager' ));
     	$this->printMessage();
}
$settings = $options['project_options'][$project_id];

if ( 1 == $settings['show_image'] && !wp_mkdir_p( $projectmanager->getImagePath() ) )
	echo "<div class='error'><p>".sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $projectmanager->getImagePath() )."</p></div>";

?>

<div class="wrap">
	<?php $this->printBreadcrumb( __( 'Settings', 'projectmanager' ) ) ?>
	
	<form action="" method="post">
		<?php wp_nonce_field( 'projectmanager_manage-settings' ) ?>
		
		<h2><?php _e( 'Settings', 'projectmanager' ) ?></h2>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="project_title"><?php _e( 'Title', 'projectmanager' ) ?></label></td><td><input type="text" name="project_title" id="project_title" value="<?php echo $projectmanager->getProjectTitle( ) ?>" size="30" style="margin-bottom: 1em;" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="per_page"><?php _e( 'Datasets per page', 'projectmanager' ) ?></label></th><td><input type="text" name="per_page" id="per_page" size="2" value="<?php echo $settings['per_page'] ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="category"><?php _e( 'Category', 'projectmanager' ) ?></label></th><td><?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'category', 'orderby' => 'name', 'selected' => $settings['category'], 'hierarchical' => true, 'show_option_none' => __('None'))); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="dataset_orderby"><?php _e( 'Sort Datasets by', 'projectmanager' ) ?></label></th><td><select size="1" name="dataset_orderby" id="dataset_orderby"><?php $this->datasetOrderOptions($settings['dataset_orderby']) ?></select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="use_widget"><?php _e( 'Use Widget', 'projectmanager' ) ?></label></th><td><input type="checkbox" name="use_widget" id="use_widget"<?php if ( 1 == $settings['use_widget']  ) echo ' checked="checked"'; ?> value="1" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="navi_link"><?php _e( 'Navi Link', 'projectmanager' ) ?></th><td><input type="checkbox" name="navi_link" id="navi_link" value="1"<?php if ( 1 == $settings['navi_link']  ) echo ' checked="checked"'; ?> /><br /><?php _e( 'Set this option to add a direct link in the navigation panel. <br/> If there is only one project in the database, the link to the index page will be disabled.', 'projectmanager' ) ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="profile_hook"><?php _e( 'Hook into Profile', 'projectmanager' ) ?></th><td><input type="checkbox" name="profile_hook" id="profile_hook" value="1" <?php if ( 1 == $settings['profile_hook'] ) echo 'checked="checked"' ?> /><br /><?php _e( 'Only one project can be hooked into the profile!', 'projectmanager' ) ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="menu_icon"><?php _e( 'Menu Icon', 'projectmanager' ) ?></label></th>
			<td>
				<select size="1" name="menu_icon" id="menu_icon">
					<?php foreach ( $menu_icons = $projectmanager->readFolder(PROJECTMANAGER_PATH.'/admin/icons/menu') AS $icon ) : ?>
					<option value="<?php echo $icon ?>" <?php if ( $icon == $settings['menu_icon'] ) echo ' selected="selected"' ?>><?php echo $icon ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		</table>
		
		<h3><?php _e( 'Images', 'projectmanager' ) ?></h3>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="show_image"><?php _e( 'Show Image', 'projectmanager' ) ?></label></th><td><input type="checkbox" name="show_image" id="show_image"<?php if ( 1 == $settings['show_image'] ) echo ' checked="checked"' ?> value="1"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="thumb_size"><?php _e( 'Thumbnail size', 'projectmanager' ) ?></label></th><td><label for="thumb_width"><?php _e( 'Width' ) ?>&#160;</label><input type="text" name="thumb_width" id="thumb_width" size="3" value="<?php echo $settings['thumb_size']['width'] ?>" />  <label for="thumb_height"><?php _e( 'Height' ) ?>&#160;</label><input type="text" name="thumb_height" id="thumb_height" size="3" value="<?php echo $settings['thumb_size']['height'] ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="medium_size"><?php _e( 'Medium size', 'projectmanager' ) ?></label></th><td><label for="medium_width"><?php _e( 'Max Width' ) ?>&#160;</label><input type="text" id="medium_width" name="medium_width" size="3" value="<?php echo $settings['medium_size']['width'] ?>" /> <label for="medium_height"><?php _e( 'Max Height' ) ?>&#160;</label> <input type="text" id="medium_height" name="medium_height" size="3" value="<?php echo $settings['medium_size']['height'] ?>" /></td>
		</tr>
		</table>
		
		<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
		<p class="submit"><input type="submit" name="saveSettings" value="<?php _e( 'Save Settings', 'projectmanager' ) ?> &raquo;" class="button" /></p>
	</form> 
</div>
<?php if ( $this->isSingle() ) $this->displayOptionsPage(true); ?>

<?php endif; ?>