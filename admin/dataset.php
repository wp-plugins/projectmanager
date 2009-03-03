<?php
if ( !current_user_can( 'manage_projects' ) && !current_user_can( 'projectmanager_admin' ) ) : 
	echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :
$options = get_option( 'projectmanager' );
$project_id = $projectmanager->getProjectID();
	
if ( isset($_GET['edit']) ) {
	$form_title = __('Edit Dataset','projectmanager');
	$dataset_id = $_GET['edit'];
	$dataset = $projectmanager->getDataset( $dataset_id );
	
	$cat_ids = $projectmanager->getSelectedCategoryIDs($dataset);
	$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );
	
	$name = $dataset->name;
	$img_filename = $dataset->image;
	$meta_data = array();
	foreach ( $dataset_meta AS $meta )
		$meta_data[$meta->form_field_id] = stripslashes_deep($meta->value);
}  else {
	$form_title = __('Add Dataset','projectmanager');
	$dataset_id = ''; $cat_ids = array(); $img_filename = ''; $name = ''; $meta_data = array();
}
$is_profile_page = false;
$options = $options['project_options'][$project_id];
$page = ($_GET['page'] == 'projectmanager') ? 'projectmanager&subpage=show-project&project_id='.$project_id : 'project_'.$project_id;

// Try to create image directory
if ( 1 == $options['show_image'] && !wp_mkdir_p( $projectmanager->getImagePath() ) )
	echo "<div class='error'><p>".sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $projectmanager->getImagePath() )."</p></div>";
?>
<form name="post" id="post" action="admin.php?page=<?php echo $page ?>" method="post" enctype="multipart/form-data">
	
<?php wp_nonce_field( 'projectmanager_edit-dataset' ) ?>
	
<div class="wrap">
	<?php $this->printBreadcrumb( $form_title ) ?>

	<h2><?php echo $form_title ?></h2>
	
	<?php include( 'dataset-form.php' ) ?>
	
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<input type="hidden" name="dataset_id" value="<?php echo $dataset_id ?>" />
	<input type="hidden" name="user_id" value="<?php echo $dataset->user_id ?>" />
	<input type="hidden" name="updateProjectManager" value="dataset" />
			
	<p class="submit"><input type="submit" name="addportrait" value="<?php echo $form_title ?> &raquo;" class="button" /></p>
</div>
</form>

<?php endif; ?>