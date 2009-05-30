<?php
if ( !current_user_can( 'projectmanager_admin' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

if ( isset($_POST['saveSettings']) ) {
	check_admin_referer('projectmanager_manage-settings');
	$settings = array(
		"per_page" => $_POST['per_page'],
		"category" => $_POST['category'],
		"dataset_orderby" => $_POST['dataset_orderby'],
		"dataset_order" => $_POST['dataset_order'],
		"show_image" => isset( $_POST['show_image']) ? 1 : 0,
		"thumb_size" => array( "width" => $_POST['thumb_width'], "height" => $_POST['thumb_height'] ),
		"medium_size" => array( "width" => $_POST['medium_width'], "height" => $_POST['medium_height'] ),
		"chmod" => $_POST['chmod'],
		"navi_link" => isset( $_POST['navi_link'] ) ? 1 : 0,
		"profile_hook" => isset($_POST['profile_hook'] ) ? 1 : 0,
		"menu_icon" => $_POST['menu_icon'],
		"gallery_num_cols" => $_POST['gallery_num_cols']
	);

	$this->editProject( $_POST['project_title'], $_POST['project_id'] );
	$this->saveSettings( $settings, $_POST['project_id'] );

	$this->setMessage(__( 'Settings saved', 'projectmanager' ));
     	$this->printMessage();
}
$project = $projectmanager->getProject( $projectmanager->getProjectID());

if ( 1 == $project->show_image && !wp_mkdir_p( $projectmanager->getFilePath() ) )
	echo "<div class='error'><p>".sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $projectmanager->getFilePath() )."</p></div>";

?>

<div class="wrap">
	<?php $this->printBreadcrumb( __( 'Settings', 'projectmanager' ) ) ?>
	
	<form action="" method="post">
		<?php wp_nonce_field( 'projectmanager_manage-settings' ) ?>
		
		<h2><?php _e( 'Settings', 'projectmanager' ) ?></h2>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="project_title"><?php _e( 'Title', 'projectmanager' ) ?></label></td><td><input type="text" name="project_title" id="project_title" value="<?php echo $project->title ?>" size="30" style="margin-bottom: 1em;" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="per_page"><?php _e( 'Datasets per page', 'projectmanager' ) ?></label></th><td><input type="text" name="per_page" id="per_page" size="2" value="<?php echo $project->per_page ?>" /> <span class="setting-description"><?php _e( 'Use <strong>NaN</strong> for no limit', 'projectmanager' ) ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="category"><?php _e( 'Category', 'projectmanager' ) ?></label></th><td><?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'category', 'orderby' => 'name', 'selected' => $project->category, 'hierarchical' => true, 'show_option_none' => __('None'))); ?>&#160;<span class="setting-description"><?php _e( 'Child categories of this category are used for grouping of datasets', 'projectmanager' ) ?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="dataset_orderby"><?php _e( 'Sort Datasets by', 'projectmanager' ) ?></label></th>
			<td>
				<select size="1" name="dataset_orderby" id="dataset_orderby"><?php $this->datasetOrderbyOptions($project->dataset_orderby) ?></select>
				<select size="1" name="dataset_order" id="dataset_order"><?php $this->datasetOrderOptions($project->dataset_order) ?></select>
				&#160;<span class="setting-description"><?php _e('To order datasets manually there must be no limit on datasets.', 'projectmanager') ?></span>
			</td>
			
		</tr>
		<tr valign="top">
			<th scope="row"><label for="navi_link"><?php _e( 'Navi Link', 'projectmanager' ) ?></th><td><input type="checkbox" name="navi_link" id="navi_link" value="1"<?php if ( 1 == $project->navi_link  ) echo ' checked="checked"'; ?> />&#160;<span class="setting-description"><?php _e( 'Set this option to add a direct link in the navigation panel.', 'projectmanager' ) ?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="profile_hook"><?php _e( 'Hook into Profile', 'projectmanager' ) ?></th><td><input type="checkbox" name="profile_hook" id="profile_hook" value="1" <?php if ( 1 == $project->profile_hook ) echo 'checked="checked"' ?> />&#160;<span class="setting-description"><?php _e( 'Only one project can be hooked into the profile!', 'projectmanager' ) ?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="menu_icon"><?php _e( 'Menu Icon', 'projectmanager' ) ?></label></th>
			<td>
				<select size="1" name="menu_icon" id="menu_icon">
					<?php foreach ( $menu_icons = $projectmanager->readFolder( array( PROJECTMANAGER_PATH.'/admin/icons/menu', TEMPLATEPATH . "/projectmanager/icons")) AS $icon ) : ?>
					<option value="<?php echo $icon ?>" <?php if ( $icon == $project->menu_icon ) echo ' selected="selected"' ?>><?php echo $icon ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="gallery_num_cols"><?php _e( 'Number of Columns', 'projectmanager' ) ?></label></th><td><input type="text" name="gallery_num_cols" id="gallery_num_cols" value="<?php echo $project->gallery_num_cols ?>" size="2" />&#160;<span class="setting-description"><?php _e( 'Needed for multicolumn output, e.g. gallery', 'projectmanager') ?></span></td>
		</table>
		
		<h3><?php _e( 'Images', 'projectmanager' ) ?></h3>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="show_image"><?php _e( 'Show Image', 'projectmanager' ) ?></label></th><td><input type="checkbox" name="show_image" id="show_image"<?php if ( 1 == $project->show_image ) echo ' checked="checked"' ?> value="1"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="thumb_size"><?php _e( 'Thumbnail size', 'projectmanager' ) ?></label></th><td><label for="thumb_width"><?php _e( 'Width' ) ?>&#160;</label><input type="text" name="thumb_width" id="thumb_width" size="3" value="<?php echo $project->thumb_size['width'] ?>" />  <label for="thumb_height"><?php _e( 'Height' ) ?>&#160;</label><input type="text" name="thumb_height" id="thumb_height" size="3" value="<?php echo $project->thumb_size['height'] ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="medium_size"><?php _e( 'Medium size', 'projectmanager' ) ?></label></th><td><label for="medium_width"><?php _e( 'Max Width' ) ?>&#160;</label><input type="text" id="medium_width" name="medium_width" size="3" value="<?php echo $project->medium_size['width'] ?>" /> <label for="medium_height"><?php _e( 'Max Height' ) ?>&#160;</label> <input type="text" id="medium_height" name="medium_height" size="3" value="<?php echo $project->medium_size['height'] ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="chmod"><?php _e( 'File permissions', 'projectmanager' ) ?></label></th><td><input type="text" id="chmod" name="chmod" size="3" value="<?php echo $project->chmod ?>" />&#160;<span class="setting-description"><?php _e( "See <a href='http://de2.php.net/manual/en/function.chmod.php'>http://de2.php.net/manual/en/function.chmod.php</a> for more information", 'projectmanager' ) ?></span></td>
		</tr>
		</table>
		
		<input type="hidden" name="project_id" value="<?php echo $project->id ?>" />
		<p class="submit"><input type="submit" name="saveSettings" value="<?php _e( 'Save Settings', 'projectmanager' ) ?> &raquo;" class="button" /></p>
	</form> 
</div>

<?php endif; ?>
