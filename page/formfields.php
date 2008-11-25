<?php
if ( !current_user_can( 'manage_projects' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$project_id = $projectmanager->getProjectID();
if ( isset($_POST['saveFormFields']) ) {
	check_admin_referer('projectmanager_manage-formfields');
	$message = $projectmanager->setFormFields( $_POST['project_id'], $_POST['form_name'], $_POST['form_type'], $_POST['show_on_startpage'], $_POST['form_order'], $_POST['new_form_name'], $_POST['new_form_type'], $_POST['new_show_on_startpage'], $_POST['new_form_order'] );
     
	echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';
}
?>
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( __('Form Fields','projectmanager') ) ?>
	
	<h2><?php _e( 'Form Fields', 'projectmanager' ) ?></h2>
	
	<form method="post" action="edit.php?page=projectmanager/page/formfields.php&amp;project_id=<?php echo $project_id ?>">
		
	<?php wp_nonce_field( 'projectmanager_manage-formfields' ) ?>
	<table id="projectmanager_form_fields_table" class="form-table">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Label', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Type', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Show on startpage', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Order', 'projectmanager' ) ?></th>
		<th scope="col">&#160;</th>
	</tr>
	</thead>
	<tbody id="projectmanager_form_fields">
	<?php $form_fields = $projectmanager->getFormFields() ?>
	<?php if ( $form_fields ) : ?>
		<?php foreach( $form_fields AS $form_field ) : ?>
		<tr id="form_id_<?php echo $form_field->id ?>">
			<td><input type="text" name="form_name[<?php echo $form_field->id ?>]" value="<?php echo $form_field->label ?>" /></td>
			<td>
				<select name="form_type[<?php echo $form_field->id ?>]" size="1">
				<?php foreach( $projectmanager->getFormFieldTypes() AS $form_type_id => $form_type ) : 
				$selected = ( $form_type_id == $form_field->type ) ? "selected='selected'" : '';
				?>
				<option value="<?php echo $form_type_id ?>"<?php echo $selected ?>><?php echo $form_type ?></option>
				<?php endforeach; ?>
				</select>
			</td>
			<td><input type="checkbox" name="show_on_startpage[<?php echo $form_field->id ?>]"<?php echo ( 1 == $form_field->show_on_startpage ) ? ' checked="checked"' : '' ?> value="1" /></td>
			<td><input type="text" size="2" name="form_order[<?php echo $form_field->id ?>]" value="<?php echo $form_field->order ?>" /></td>
			<td style="text-align: center; width: 12px; vertical-align: middle;"><a class="image_link" href="#" onclick='return ProjectManager.removeFormField("form_id_<?php echo $form_field->id ?>", <?php echo $form_field->id ?>);'><img src="../wp-content/plugins/projectmanager/images/trash.gif" alt="<?php _e( 'Delete', 'projectmanager' ) ?>" title="<?php _e( 'Delete formfield', 'projectmanager' ) ?>" /></a>
		</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
	</table>
	<p><a href='#' onclick='return ProjectManager.addFormField();'><?php _e( 'Add new formfield', 'projectmanager' ) ?></a></p>
	
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	
	<p class="submit"><input type="submit" name="saveFormFields" value="<?php _e( 'Save Form Fields', 'projectmanager' ) ?> &raquo;" class="button" /></p>
	</form>
</div> 

<?php endif; ?>