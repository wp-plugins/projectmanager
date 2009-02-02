<?php
if ( !current_user_can( 'manage_projects' ) ) : 
	echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :
$options = get_option( 'projectmanager' );
$project_id = $projectmanager->getProjectID();
	
$error = false;
if ( isset($_GET['edit']) ) {
	$form_title = __('Edit Dataset','projectmanager');
	$dataset_id = $_GET['edit'];
	$dataset = $projectmanager->getDataset( $dataset_id );
	
	if ( $dataset->user_id == $current_user->ID || current_user_can( 'projectmanager_admin') ) {
		$cat_ids = $projectmanager->getSelectedCategoryIDs($dataset);
		$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );
	
		$name = $dataset->name;
		$img_filename = $dataset->image;
		$meta_data = array();
		foreach ( $dataset_meta AS $meta )
			$meta_data[$meta->form_field_id] = $meta->value;
	} else {
		$error = true;
		$error_msg = __( "You don't have the permission to edit this dataset", 'projectmanager' );
	}
}  else {
	// Throw Error if double entry of same user_id is detected and user is no admin
	$num_datasets = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_dataset} WHERE `user_id` = {$current_user->ID}" );
	if ( !current_user_can( 'projectmanager_admin') && $num_datasets != 0 ) {
		$error = true;
		$error_msg = __( 'An Entry of your user ID has been detected', 'projectmanager' );
	}
	
	$form_title = __('Add Dataset','projectmanager');
	$dataset_id = ''; $cat_ids = array(); $img_filename = ''; $name = ''; $meta_data = array();
}
$is_profile_page = false;
$options = $options['project_options'][$project_id];

if ( !$error ) {

// Try to create image directory
if ( 1 == $options['show_image'] && !wp_mkdir_p( $projectmanager->getImagePath() ) )
	echo "<div class='error'><p>".sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $projectmanager->getImagePath() )."</p></div>";
?>
<form name="post" id="post" action="edit.php?page=projectmanager/page/show-project.php&amp;project_id=<?php echo $project_id ?>" method="post" enctype="multipart/form-data">
	
<?php wp_nonce_field( 'projectmanager_edit-dataset' ) ?>
	
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $form_title ) ?>
			
	<h2><?php echo $form_title ?></h2>
	
	<?php include( 'dataset-form.php' ) ?>
	
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<input type="hidden" name="dataset_id" value="<?php echo $dataset_id ?>" />
	<input type="hidden" name="user_id" value="<?php echo $dataset->user_id ?>" />
	<input type="hidden" name="updateProjectManager" value="dataset" />
			
	<p class="submit"><input type="submit" name="addportrait" value="<?php echo $form_title ?> &raquo;" class="button" /></p>
</div>
</form>

<?php } else {
      echo '<div class="error"><p style="text-align: center;">'.$error_msg.'</p></div>';
}

endif; ?>
