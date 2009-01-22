<?php
     if ( !current_user_can( 'projectmanager_admin' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$project_id = $projectmanager->getProjectID();
     
if ( isset($_POST['import']) ) {
	$message = $projectmanager->importDatasets( $project_id, $_FILES['projectmanger_import'], $_POST['delimiter'], $_POST['cols'] );
     
     	if ( $projectmanager->error() )
     		$projectmanager->printErrorMessage();
     	else
		echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';
}
?>

<div class="wrap">
	<?php $projectmanager->printBreadcrumb( __('Import/Export', 'projectmanager') ) ?>
	<h2><?php _e( 'Import' ) ?></h2>
	
	<form action="" method="post" enctype="multipart/form-data">
	<?php wp_nonce_field( 'projectmanager_import-datasets' ) ?>
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="projectmanger_import"><?php _e('File','projectmanager') ?></label></th><td><input type="file" name="projectmanger_import" id="projectmanger_import" size="40"/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="delimiter"><?php _e('Delimiter','projectmanager') ?></label></th><td><input type="text" name="delimiter" id="delimiter" value=";" size="3" /><p><?php _e('For tab delimited files use TAB as delimiter', 'projectmanager') ?></td>
	</tr>
	</table>
	<h3><?php _e( 'Column Assignment', 'projectmanager' ) ?></h3>
	<p><?php _e('All FormFields need to be assigned, also if some contain no data', 'projectmanager') ?></p>
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php printf(__( 'Column %d', 'projectmanager'), 1 ) ?></th><td><?php _e( 'Name', 'projectmanager' ) ?></td>
	</tr>
	<?php for ( $i = 1; $i <= $projectmanager->getNumFormFields(); $i++ ) : ?>
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
	
	<p class="submit"><input type="submit" name="import" value="<?php _e('Import') ?> &raquo;" class="button" /></p>
	</form>
</div>

<div class="wrap">
	<h2><?php _e( 'Export') ?></h2>
	<form action="" method="post">
		<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
		<p class="submit"><input type="submit" name="projectmanager_export" value="<?php _e('Export') ?> &raquo;" class="button" /></p>
	</form>
</div>
<?php endif; ?>