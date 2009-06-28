<?php
if ( !current_user_can( 'edit_formfields' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$project_id = $projectmanager->getProjectID();
$project = $projectmanager->getCurrentProject();

if ( isset($_POST['saveFormFields']) ) {
	check_admin_referer('projectmanager_manage-formfields');
	$this->setFormFields( $_POST['project_id'], $_POST['formfields'], $_POST['new_formfields'] );

	$this->printMessage();
}
$options = get_option('projectmanager');
?>
<div class="wrap">
	<?php $this->printBreadcrumb( __('Form Fields','projectmanager') ) ?>
	
	<h2><?php _e( 'Form Fields', 'projectmanager' ) ?></h2>
	
	<form method="post" action="">
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<?php wp_nonce_field( 'projectmanager_manage-formfields' ) ?>
	
	<table class="widefat">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'ID', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Label', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Type', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Startpage', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Profile', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Order', 'projectmanager' ) ?></th>
		<th scope="sol"><?php _e( 'Order By', 'projectmanager' ) ?></th>
		<th scope="col">&#160;</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'ID', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Label', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Type', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Startpage', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Profile', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Order', 'projectmanager' ) ?></th>
		<th scope="sol"><?php _e( 'Order By', 'projectmanager' ) ?></th>
		<th scope="col">&#160;</th>
	</tr>
	</tfoot>
	
	<tbody id="projectmanager_form_fields" class="form-table">
	<?php $form_fields = $projectmanager->getFormFields() ?>
	<?php if ( $form_fields ) : ?>
		<?php foreach( $form_fields AS $form_field ) : $class = ( 'alternate' == $class ) ? '' : 'alternate'; ?>
		<tr id="form_id_<?php echo $form_field->id ?>" class="<?php echo $class ?>">
			<td><?php echo $form_field->id ?></td>
			<td><input type="text" name="formfields[<?php echo $form_field->id ?>][name]" value="<?php echo htmlspecialchars(stripslashes($form_field->label), ENT_QUOTES) ?>" /></td>
			<td id="form_field_options_box<?php echo $form_field->id ?>">
				<?php $form_field->options = is_array($options['form_field_options'][$form_field->id]) ? implode('|', $options['form_field_options'][$form_field->id]) : ''; ?>
				<select id="form_type_<?php echo $form_field->id ?>" name="formfields[<?php echo $form_field->id ?>][type]" size="1" onChange="ProjectManager.toggleOptions(<?php echo $project_id ?>, <?php echo $form_field->id ?>, this.value, '<?php echo $form_field->options ?>' );">
				<?php foreach( $projectmanager->getFormFieldTypes() AS $form_type_id => $form_type ) : 
					$selected = ( $form_type_id == $form_field->type ) ? " selected='selected'" : '';
					$field_name = is_array($form_type) ? $form_type['name'] : $form_type;
				?>
					<option value="<?php echo $form_type_id ?>"<?php echo $selected ?>><?php echo $field_name ?></option>
				<?php endforeach; ?>
				</select>

				<span id="loading_formfield_options_<?php echo $form_field->id ?>"></span>
				
				<?php if ( $form_field->type == 'project' ) : ?>
				<div id="form_field_options_container<?php echo $form_field->id ?>" style="display: inline;">
					<div id="form_field_options_div<?php echo $form_field->id ?>" style="width: 300px; height: 100px; overflow: auto; display: none;"><div class="projectmanager_thickbox">
						<select size="1" id="form_field_project_<?php echo $form_field->id ?>">
							<option value="0"><?php _e( 'Choose Project', 'projectmanager' ) ?></option>
							<?php foreach ( $projectmanager->getProjects() AS $p ) : ?>
							<?php if ( $p->id != $project_id ) : ?>
							<option value="<?php echo $p->id ?>"<?php selected($p->id, $options['form_field_options'][$form_field->id]) ?>><?php echo $p->title ?></option>
							<?php endif; ?>
							<?php endforeach; ?>
						</select>
						<div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.saveProjectLink(<?php echo $form_field->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div>
					</div></div>
					<span>&#160;<a href='#TB_inline&width=300&height=100&inlineId=form_field_options_div<?php echo $form_field->id ?>' style="display: inline;" id="options_link<?php echo $form_field->id ?>" class="thickbox" title="<?php _e('Choose Project to Link','projectmanager') ?>"><img src="<?php echo $this->getIconURL("databases.png") ?>" alt="<?php _e('Set','projectmanager') ?>" /></a></span>
				</div>
				<?php endif; ?>

				<!-- Thickbox Container and Link for Form Field Options -->
				<?php if ( $form_field->type == 'select' || $form_field->type == 'checkbox' || $form_field->type == 'radio' ) : ?>
				<div id="form_field_options_container<?php echo $form_field->id ?>" style="display: inline;" >
					<div id="form_field_options_div<?php echo $form_field->id ?>" style="width: 450px; height: 350px; overflow: auto; display: none;">
					<?php foreach ( $options['form_field_options'][$form_field->id] AS $item ) : ?>
					<input type="text" id="form_field_options<?php echo $form_field->id ?>" value="<?php echo $item ?>" size="30" /><br />
					<?php endforeach; ?>
					
					<div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.ajaxSaveFormFieldOptions(<?php echo $form_field->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div>
						
						<p><?php _e( "Separate Options by <strong>|</strong>", 'projectmanager' ) ?></p>
					</div>
					<span>&#160;<a href='#TB_inline&width=450&height=350&inlineId=form_field_options_div<?php echo $form_field->id ?>' style="display: inline;" id="options_link<?php echo $form_field->id ?>" class="thickbox" title="<?php _e('Options','projectmanager') ?>"><img src="<?php echo $this->getIconURL("application_list.png") ?>" alt="<?php _e('Set','projectmanager') ?>" /></a></span>
				</div>
				<?php endif; ?>
			</td>
			<td><input type="checkbox" name="formfields[<?php echo $form_field->id ?>][show_on_startpage]"<?php checked(1, $form_field->show_on_startpage) ?> value="1" /></td>
			<td><input type="checkbox" name="formfields[<?php echo $form_field->id ?>][show_in_profile]"<?php checked ( 1, $form_field->show_in_profile) ?> value="1" /></td>
			<td><input type="text" size="2" name="formfields[<?php echo $form_field->id ?>][order]" value="<?php echo $form_field->order ?>" /></td>
			<td><input type="checkbox" name="formfields[<?php echo $form_field->id ?>][orderby]" value="1"<?php checked ( 1, $form_field->order_by ) ?> /></td>
			<td style="text-align: center; width: 12px; vertical-align: middle;"><a class="image_link" href="#" onclick='return ProjectManager.removeFormField("form_id_<?php echo $form_field->id ?>");'><img src="../wp-content/plugins/projectmanager/admin/icons/trash.gif" alt="<?php _e( 'Delete', 'projectmanager' ) ?>" title="<?php _e( 'Delete formfield', 'projectmanager' ) ?>" /></a>
		</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
	</table>
	<p><a href='#' onclick='return ProjectManager.addFormField();'><?php _e( 'Add new formfield', 'projectmanager' ) ?></a></p>
	<p class="submit"><input type="submit" name="saveFormFields" value="<?php _e( 'Save Form Fields', 'projectmanager' ) ?> &raquo;" class="button" /></p>
	</form>
</div> 

<?php endif; ?>
