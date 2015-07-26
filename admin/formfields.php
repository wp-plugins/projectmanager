<?php
if ( !current_user_can( 'edit_formfields' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$project_id = $projectmanager->getProjectID();
$project = $projectmanager->getCurrentProject();
	
if ( isset($_POST['saveFormFields']) ) {
	check_admin_referer('projectmanager_manager-formfields');
	$new_formfields = isset($_POST['new_formfields']) ? $_POST['new_formfields'] : false;
	$this->setFormFields( intval($_POST['project_id']), $_POST['formfields'], $new_formfields );

	$this->printMessage();
}

if (isset($_POST['addFormField'])) {
	check_admin_referer('projectmanager_manager-formfields');
	$new_formfields = isset($_POST['new_formfields']) ? $_POST['new_formfields'] : array();
	$formfields = isset($_POST['formfields']) ? $_POST['formfields'] : array();
	$this->setFormFields( intval($_POST['project_id']), $formfields, $new_formfields );
	$this->addFormField(intval($_POST['project_id']));
}

if (isset($_POST['doaction']) && isset($_POST['action'])) {
	check_admin_referer('projectmanager_manager-formfields');
	if ( 'delete' == $_POST['action'] ) {
		global $current_user;
		if ( !current_user_can('edit_formfields') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			$this->printMessage();
		} else {
			foreach ( $_POST['del_formfield'] AS $f_id ) {
				$this->delFormField( intval($f_id) );
			}
			$this->setMessage( __("Formfields deleted", 'projectmanager'), false );
			$this->printMessage();
		}
	}
}
$options = get_option('projectmanager');
?>
<div class="wrap">
	<?php $this->printBreadcrumb( __('Form Fields','projectmanager') ) ?>
	
	<h2><?php _e( 'Form Fields', 'projectmanager' ) ?></h2>
	
	<form id="formfield-filter" method="post" action="">
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<?php wp_nonce_field( 'projectmanager_manager-formfields' ) ?>
	
	<div class="tablenav">
		<div class="alignleft actions">
			<!-- Bulk Actions -->
			<select name="action" size="1">
				<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
				<option value="delete"><?php _e('Delete')?></option>
			</select>
			<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
			<input type="submit" name="addFormField" value="<?php _e( 'Add Form Field', 'projectmanager' ) ?> &raquo;" class="button-secondary action" />
		</div>
		
	</div>
	<table class="widefat">
	<thead>
	<tr>
		<th scope="col" class="check-column"></th>
		<th scope="col"><?php _e( 'ID', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Label', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Type', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Options', 'projectmanager' ) ?>*</th>
		<th scope="col"><?php _e( 'Mandatory', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Unique', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Private', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Startpage', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Profile', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Order', 'projectmanager' ) ?></th>
		<th scope="sol"><?php _e( 'Order By', 'projectmanager' ) ?></th>
		<!--<th scope="col">&#160;</th>-->
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col" class="check-column"></th>
		<th scope="col"><?php _e( 'ID', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Label', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Type', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Options', 'projectmanager' ) ?>*</th>
		<th scope="col"><?php _e( 'Mandatory', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Unique', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Private', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Startpage', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Profile', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Order', 'projectmanager' ) ?></th>
		<th scope="sol"><?php _e( 'Order By', 'projectmanager' ) ?></th>
		<!--<th scope="col">&#160;</th>-->
	</tr>
	</tfoot>
	
	<tbody id="projectmanager_form_fields" class="form-table">
	<?php $form_fields = $projectmanager->getFormFields() ?>
	<?php if ( $form_fields ) : ?>
		<?php foreach( $form_fields AS $form_field ) : $class = ( !isset($class) || 'alternate' == $class ) ? '' : 'alternate'; ?>
		<tr id="form_id_<?php echo $form_field->id ?>" class="<?php echo $class ?>">
			<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $form_field->id ?>" name="del_formfield[<?php echo $form_field->id ?>]" /></th>
			<td><?php echo $form_field->id ?><input type="hidden" name="formfields[<?php echo $form_field->id ?>][id]" value="<?php echo $form_field->id ?>" /></td>
			<td><input type="text" name="formfields[<?php echo $form_field->id ?>][name]" value="<?php echo htmlspecialchars(stripslashes($form_field->label), ENT_QUOTES) ?>" /></td>
			<td id="form_field_options_box<?php echo $form_field->id ?>">
				<select id="form_type_<?php echo $form_field->id ?>" name="formfields[<?php echo $form_field->id ?>][type]" size="1" onChange="ProjectManager.toggleOptions(<?php echo $project_id ?>, <?php echo $form_field->id ?>, this.value, '<?php echo $form_field->options ?>' );">
				<?php foreach( $projectmanager->getFormFieldTypes() AS $form_type_id => $form_type ) : 
					$field_name = is_array($form_type) ? $form_type['name'] : $form_type;
				?>
					<option value="<?php echo $form_type_id ?>"<?php selected($form_type_id, $form_field->type); ?>><?php echo $field_name ?></option>
				<?php endforeach; ?>
				</select>
			</td>
			<td>
			<?php if ($form_field->type == 'project') : ?>
			<select size="1" name="formfields[<?php echo $form_field->id ?>][options]">
				<option value="0"><?php _e( 'Choose Project', 'projectmanager' ) ?></option>
				<?php foreach ( $projectmanager->getProjects() AS $p ) : ?>
				<?php if ( $p->id != $project_id ) : ?>
				<option value="<?php echo $p->id ?>"<?php selected($p->id, $form_field->options) ?>><?php echo $p->title ?></option>
				<?php endif; ?>
				<?php endforeach; ?>
			</select>
			<?php else : ?>
			<input type="text" name="formfields[<?php echo $form_field->id ?>][options]" value="<?php echo $form_field->options ?>" />
			<?php endif; ?>
			</td>
			<td><input type="checkbox" name="formfields[<?php echo $form_field->id ?>][mandatory]"<?php checked(1, $form_field->mandatory) ?> value="1" /></td>
			<td><input type="checkbox" name="formfields[<?php echo $form_field->id ?>][unique]"<?php checked(1, $form_field->unique) ?> value="1" /></td>
			<td><input type="checkbox" name="formfields[<?php echo $form_field->id ?>][private]"<?php checked(1, $form_field->private) ?> value="1" /></td>
			<td><input type="checkbox" name="formfields[<?php echo $form_field->id ?>][show_on_startpage]"<?php checked(1, $form_field->show_on_startpage) ?> value="1" /></td>
			<td><input type="checkbox" name="formfields[<?php echo $form_field->id ?>][show_in_profile]"<?php checked ( 1, $form_field->show_in_profile) ?> value="1" /></td>
			<td><input type="text" size="2" name="formfields[<?php echo $form_field->id ?>][order]" value="<?php echo $form_field->order ?>" /></td>
			<td><input type="checkbox" name="formfields[<?php echo $form_field->id ?>][orderby]" value="1"<?php checked ( 1, $form_field->order_by ) ?> /></td>
			<!--<td style="text-align: center; width: 12px; vertical-align: middle;"><a class="image_link" href="#" onclick='return ProjectManager.removeFormField("form_id_<?php echo $form_field->id ?>");'><img src="../wp-content/plugins/projectmanager/admin/icons/trash.gif" alt="<?php _e( 'Delete', 'projectmanager' ) ?>" title="<?php _e( 'Delete formfield', 'projectmanager' ) ?>" /></a></td>-->
		</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
	</table>
	<!--<p class="submit" style="float: right; margin: 1em 0 0 0; padding: 0;"><input type="submit" name="addFormField" value="<?php _e( 'Add Form Field', 'projectmanager' ) ?> &raquo;" class="button-secondary action" /></p>-->
	<p style="margin: 0.5em 0 0 0; padding: 0;">*<?php _e('The Options field is used to store meta-data of formfields. Separate values by ; to save options for selectable fields (i.e. dropdown, checkbox or radio list).', 'projectmanager') ?></p>
	<p style="margin: 0.5em 0 0 0; padding: 0;">*<?php _e('The Options field can be also used to set a maximum number of characters for given formfield in the format `max:XX`.', 'projectmanager') ?></p>
	<p style="margin: 0.5em 0 0 0; padding: 0;">*<?php _e('The Options field can be also used to set a maximum number of characters for textfields and TinyMCE editors after which the data will be cut `limit:XX`.', 'projectmanager') ?></p>
	<!--<p><a href='#' onclick='return ProjectManager.addFormField(<?php echo $project->id ?>);'><?php _e( 'Add new formfield', 'projectmanager' ) ?></a></p>-->
	<p class="submit"><input type="submit" name="saveFormFields" value="<?php _e( 'Save Form Fields', 'projectmanager' ) ?> &raquo;" class="button-primary" /></p>
	</form>
</div> 

<?php endif; ?>