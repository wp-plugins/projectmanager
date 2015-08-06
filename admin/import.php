<?php
if ( !current_user_can( 'import_datasets' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$project_id = $projectmanager->getProjectID();
$project = $projectmanager->getCurrentProject();

$media_filename = "Media-ProjectID_".$project->id.".zip";
$media_filename = $projectmanager->getFilePath($media_filename);

// clean up media zip file
if (file_exists($media_filename))
	@unlink($media_filename);

// Import data here. Data export is handled in /projectmanager.php
if ( isset($_POST['import_datasets']) ) {
	$this->importDatasets( $project_id, $_FILES['projectmanager_import'], $_POST['delimiter'], $_POST['cols'] );
	$this->printMessage();
} elseif ( isset($_POST['import_media']) ) {
	$this->importMedia();
	$this->printMessage();
}
?>

<div class="wrap">
	<?php $this->printBreadcrumb( __('Import/Export', 'projectmanager') ) ?>
	<h2><?php _e( 'Export Data', 'projectmanager' ) ?></h2>
	
	<?php if (file_exists($media_filename)) : ?>
	<!--<p><?php printf(__('Your media files are ready to <a href="%s">download</a>. (Last modified: %s)','projectmanager'), $projectmanager->getFileURL(basename($media_filename)), date ("F d Y H:i:s.", filemtime($media_filename))); ?></p>-->
	<?php endif; ?>
	
	<p><?php _e('You can export datasets in tab-delimited format or media files as zip archive', 'projectmanager') ?></p>
	
	<form action="" method="post">
		<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
		
		<p>
			<select size="1" name="export_type" id="export_type">
				<option value="data"><?php _e('Datasets', 'projectmanager') ?></option>
				<option value="media"><?php _e('Media Files', 'projectmanager') ?></option>
			</select>
		
			<input type="submit" name="projectmanager_export" value="<?php _e('Export Data', 'projectmanager') ?> &raquo;" class="button-primary" />
		</p>
	</form>
</div>

<div class="wrap">
	<h2><?php _e( 'Import Data', 'projectmanager' ) ?></h2>
	
	<h3><?php _e( 'Import Media', 'projectmanager' ) ?></h3>
	<p><?php _e( 'You can upload media files in zip format to the webserver', 'projectmanager' ) ?></p>
	<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'projectmanager_import-media' ) ?>
		<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
		<input type="file" name="projectmanager_media_zip" id="projectmanager_media_zip" size="40"/>
		<p class="submit"><input type="submit" name="import_media" value="<?php _e('Upload Media', 'projectmanager') ?> &raquo;" class="button-primary" /></p>
	</form>
	
	<h3><?php _e( 'Import Datasets', 'projectmanager' ) ?></h3>
	
	<form action="" method="post" enctype="multipart/form-data">
	<?php wp_nonce_field( 'projectmanager_import-datasets' ) ?>
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="projectmanager_import"><?php _e('File','projectmanager') ?></label></th><td><input type="file" name="projectmanager_import" id="projectmanager_import" size="40"/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="delimiter"><?php _e('Delimiter','projectmanager') ?></label></th><td><input type="text" name="delimiter" id="delimiter" value="TAB" size="3" /><p><?php _e('For tab delimited files use TAB as delimiter', 'projectmanager') ?></td>
	</tr>
	</table>
	<h3><?php _e( 'Column Assignment', 'projectmanager' ) ?></h3>
	<p><?php _e('All FormFields need to be assigned, also if some contain no data.', 'projectmanager') ?></p>
	<p><?php _e('Dates must have the format <strong>YYYY-MM-DD</strong>.', 'projectmanager') ?></p>
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php printf(__( 'Column %d', 'projectmanager'), 1 ) ?></th><td><?php _e( 'Name', 'projectmanager' ) ?></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php printf(__( 'Column %d', 'projectmanager'), 2 ) ?></th><td><?php _e( 'Image', 'projectmanager' ) ?></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php printf(__( 'Column %d', 'projectmanager'), 3 ) ?></th><td><?php _e( 'Categories', 'projectmanager' ) ?> - <?php _e('multiple categories separated by comma', 'projectmanager') ?></td>
	</tr>
	<?php for ( $i = 3; $i <= $projectmanager->getNumFormFields()+2; $i++ ) : ?>
	<tr valign="top">
		<th scope="row"><label for="col_<?php echo $i ?>"><?php printf(__( 'Column %d', 'projectmanager'), ($i+1)) ?></label></th>
		<td>
			<select size="1" name="cols[<?php echo $i ?>]" id="col_<?php echo $i ?>">
				<?php foreach ( $projectmanager->getFormFields() AS $form_field ) : ?>
				<option value="<?php echo $form_field->id ?>"><?php echo $form_field->label ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<?php endfor; ?>
	</table>
	
	<p class="submit"><input type="submit" name="import_datasets" value="<?php _e('Import Datasets', 'projectmanager') ?> &raquo;" class="button-primary" /></p>
	</form>
</div>
<?php endif; ?>