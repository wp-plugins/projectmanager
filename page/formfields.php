<?php
if ( !current_user_can( 'projectmanager_admin' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$project_id = $projectmanager->getProjectID();
if ( isset($_POST['saveFormFields']) ) {
	check_admin_referer('projectmanager_manage-formfields');
	$projectmanager->setFormFields( $_POST['project_id'], $_POST['form_name'], $_POST['form_type'], $_POST['show_on_startpage'], $_POST['form_order'], $_POST['order_by'], $_POST['new_form_name'], $_POST['new_form_type'], $_POST['new_show_on_startpage'], $_POST['new_form_order'], $_POST['new_order_by'] );
     
	$projectmanager->printMessage();
}
$options = get_option('projectmanager');
?>
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( __('Form Fields','projectmanager') ) ?>
	
	<h2><?php _e( 'Form Fields', 'projectmanager' ) ?></h2>
	
	<form method="post" action="edit.php?page=projectmanager/page/formfields.php&amp;project_id=<?php echo $project_id ?>">
		
	<?php wp_nonce_field( 'projectmanager_manage-formfields' ) ?>
	<table class="widefat">
	<thead>
	<tr>
		<th scope="col"><?php _e( 'Label', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Type', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Show on startpage', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Order', 'projectmanager' ) ?></th>
		<th scope="sol"><?php _e( 'Order By', 'projectmanager' ) ?></th>
		<th scope="col">&#160;</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"><?php _e( 'Label', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Type', 'projectmanager' ) ?></th>
		<th scope="col"><?php _e( 'Show on startpage', 'projectmanager' ) ?></th>
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
			<td><input type="text" name="form_name[<?php echo $form_field->id ?>]" value="<?php echo $form_field->label ?>" /></td>
			<td id="form_field_options_box<?php echo $form_field->id ?>">
				<?php $form_field_options = is_array($options['form_field_options'][$form_field->id]) ? implode(', ', $options['form_field_options'][$form_field->id]) : ''; ?>
				<select id="form_type_<?php echo $form_field->id ?>" name="form_type[<?php echo $form_field->id ?>]" size="1" onChange="ProjectManager.toggleOptions(<?php echo $form_field->id ?>, this.value, '<?php _e('Save') ?>', '<?php _e('Cancel') ?>', '<?php _e('Options','projectmanager') ?>', '<?php echo $form_field_options ?>' );">
				<?php foreach( $projectmanager->getFormFieldTypes() AS $form_type_id => $form_type ) : 
					$selected = ( $form_type_id == $form_field->type ) ? "selected='selected'" : '';
				?>
					<option value="<?php echo $form_type_id ?>"<?php echo $selected ?>><?php echo $form_type ?></option>
				<?php endforeach; ?>
				</select>
				
				<?php if ( $form_field->type == 6 || $form_field->type == 7 || $form_field->type == 8 ) : ?>
				<!-- Thickbox Container and Link for Form Field Options -->
				<div id="form_field_options_container<?php echo $form_field->id ?>" style="display: inline;">
					<div id="form_field_options_div<?php echo $form_field->id ?>" style="width: 150px; height: 80px; overflow: auto; display: none;"><div class="projectmanager_thickbox">
						<form><textarea cols="40" rows="10" id="form_field_options<?php echo $form_field->id ?>"><?php if ($options['form_field_options'][$form_field->id] != '' ) echo implode("\n", $options['form_field_options'][$form_field->id]) ?></textarea><div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.ajaxSaveFormFieldOptions(<?php echo $form_field->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div></form>
					</div></div>
					<span>&#160;<a href='#TB_inline?width=150&heigth=100&inlineId=form_field_options_div<?php echo $form_field->id ?>' style="display: inline;" id="options_link<?php echo $form_field->id ?>" class="thickbox" title="<?php _e('Options','projectmanager') ?>"><?php _e('Options','projectmanager') ?></a></span>
				</div>
				<?php endif; ?>
			</td>
			<td><input type="checkbox" name="show_on_startpage[<?php echo $form_field->id ?>]"<?php echo ( 1 == $form_field->show_on_startpage ) ? ' checked="checked"' : '' ?> value="1" /></td>
			<td><input type="text" size="2" name="form_order[<?php echo $form_field->id ?>]" value="<?php echo $form_field->order ?>" /></td>
			<td><input type="checkbox" name="order_by[<?php echo $form_field->id ?>]" value="1"<?php echo ( 1 == $form_field->order_by ) ? ' checked="checked"' : '' ?> /></td>
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