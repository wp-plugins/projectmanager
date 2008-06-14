<?php
if ( !current_user_can( 'manage_projects' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$project_id = $_GET['project_id'];
$projectmanager->setSettings( $project_id );
if ( isset($_POST['updateProjectManager']) ) {
	if ( 'settings' == $_POST['updateProjectManager'] && check_admin_referer('projectmanager_manage-settings') ) {
		$options = get_option( 'projectmanager' );
		$options[$_POST['project_id']]['num_cols'] = $_POST['thumbcols'];
		$options[$_POST['project_id']]['num_rows'] = $_POST['thumbrows'];
		$options[$_POST['project_id']]['show_image'] = isset($_POST['show_image']) ? 1 : 0;
		$options[$_POST['project_id']]['template'] = $_POST['tpl'];
		$options[$_POST['project_id']]['groups'] = $_POST['groups'];
		$options[$_POST['project_id']]['use_widget'] = isset( $_POST['use_widget'] ) ? 1 : 0;
		$options[$_POST['project_id']]['thumb_size'] = array("width" => $_POST['thumb_width'], "height" => $_POST['thumb_height']);
						
		update_option( 'projectmanager', $options );
		$return_message = 'Settings saved';
	} elseif ( 'form_fields' == $_POST['updateProjectManager'] && check_admin_referer('projectmanager_manage-formfields') ) {
		$return_message = $projectmanager->setFormFields( $_POST['project_id'], $_POST['form_name'], $_POST['form_type'], $_POST['show_on_startpage'], $_POST['form_order'], $_POST['new_form_name'], $_POST['new_form_type'], $_POST['new_show_on_startpage'], $_POST['new_form_order'] );
	}
	echo '<div id="message" class="updated fade"><p><strong>'.__( $return_message, 'projectmanager' ).'</strong></p></div>';
}

$options = get_option( 'projectmanager' );
?>
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_id, 'Settings' ) ?>
	
	<h2><?php _e( 'Settings', 'projectmanager' ) ?></h2>
	<form class="projectmanager" action="edit.php?page=projectmanager/page/settings.php&amp;project_id=<?php echo $project_id ?>" method="post">
		<?php wp_nonce_field( 'projectmanager_manage-settings' ) ?>
		
		<label for="thumbcols"><?php _e( 'Number of cols', 'projectmanager' ) ?>:</label><input type="text" name="thumbcols" id="thumbcols" size="2" value="<?php echo $options[$project_id]['num_cols'] ?>" /><br />
		<label for="thumbrows"><?php _e( 'Number of rows', 'projectmanager' ) ?>:</label><input type="text" name="thumbrows" id="thumbrows" size="2" value="<?php echo $options[$project_id]['num_rows'] ?>" /><br />
		<label for="groups"><?php _e( 'Groups', 'projectmanager' ) ?>:</label><input type="text" size="50" name="groups" id="groups" value="<?php echo $options[$project_id]['groups'] ?>" /><br />
		
		<label for="template"><?php _e( 'Template', 'projectmanager' ) ?>:</label>
		<select size="1" name="tpl">
		<?php if ( $handle = opendir('../'.$projectmanager->getTemplateDir()) ) : ?>
			<?php while ( false !== ( $file = readdir($handle) ) ) : ?>
				<?php if ( $file == $options[$project_id]['template'] )
					$selected = ' selected="selected"';
				      else
				      	$selected = '';
					
					$tpl_data = implode( '', file('../'.$projectmanager->getTemplateDir().$file ));
					preg_match( '|Template Name:(.*)$|mi', $tpl_data, $tpl_name);
				?>
				<?php if ( $file != '.' && $file != '..' ) : ?>
				<option value="<?php echo $file ?>"<?php echo $selected ?>><?php echo $tpl_name[1] ?></option>
				<?php endif ?>
			<?php endwhile ?>
		<?php endif ?>
		</select><br />
		
		<?php if ( 1 == $options[$project_id]['use_widget']  ) $selected = ' checked="checked"'; else $selected = ''; ?>
		<label for="use_widget"><?php _e( 'Use Widget', 'projectmanager' ) ?>:</label>
		<input type="checkbox" name="use_widget" id="use_widget"<?php echo $selected ?> value="1"><br />
			
		<?php if ( 1 == $options[$project_id]['show_image'] ) $selected = ' checked="checked"'; else $selected = ''; ?>
		<label for="show_image"><?php _e( 'Show Image', 'projectmanager' ) ?>:</label>
		<input type="checkbox" name="show_image" id="show_image"<?php echo $selected ?> value="1"><br />
		<label for="thumb_size"><?php _e( 'Thumbnail size', 'projectmanager' ) ?>:</label><input type="text" name="thumb_width" size="3" value="<?php echo $options[$project_id]['thumb_size']['width'] ?>" /> x <input type="text" name="thumb_height" size="3" value="<?php echo $options[$project_id]['thumb_size']['height'] ?>" /><br />
		
		<input type="hidden" name="updateProjectManager" value="settings" />
		<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
		
		<p class="submit"><input type="submit" name="updateSettings" value="<?php _e( 'Save Settings', 'projectmanager' ) ?> &raquo;" class="button" /></p>
	</form> 
</div>

<div class="wrap">
	<h2><?php _e( 'Form Fields', 'projectmanager' ) ?></h2>
	
	<form class="projectmanager" method="post" action="edit.php?page=projectmanager/page/settings.php&amp;project_id=<?php echo $project_id ?>">
		
	<?php wp_nonce_field( 'projectmanager_manage-formfields' ) ?>
	<table id="projectmanager_form_fields_table">
		<thead>
		<tr>
			<th><?php _e( 'Label', 'projectmanager' ) ?></th>
			<th><?php _e( 'Type', 'projectmanager' ) ?></th>
			<th><?php _e( 'Show on startpage', 'projectmanager' ) ?></th>
			<th><?php _e( 'Order', 'projectmanager' ) ?></th>
			<th>&#160;</th>
		</tr>
		</thead>
		<tbody id="projectmanager_form_fields">
		<?php $form_fields = $projectmanager->getProjectMeta() ?>
		<?php if ( $form_fields ) : ?>
			<?php foreach( $form_fields AS $form_field ) : ?>
				<tr id="form_id_<?php echo $form_field->id ?>">
					<td><input type="text" name="form_name[<?php echo $form_field->id ?>]" value="<?php echo $form_field->label ?>" /></td>
					<td>
						<select name="form_type[<?php echo $form_field->id ?>]" size="1">
							<?php foreach( $projectmanager->getFormFieldTypes() AS $form_type_id => $form_type ) : 
								$selected = '';
								if ( $form_type_id == $form_field->type )
									$selected = "selected='selected'";
							?>
							<option value="<?php echo $form_type_id ?>"<?php echo $selected ?>><?php _e( $form_type, 'projectmanager' ) ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<?php $selected = ( 1 == $form_field->show_on_startpage ) ? ' checked="checked"' : ''; ?>
					<td><input type="checkbox" name="show_on_startpage[<?php echo $form_field->id ?>]"<?php echo $selected ?> value="1" /></td>
					<td><input type="text" size="2" name="form_order[<?php echo $form_field->id ?>]" value="<?php echo $form_field->order ?>" /></td>
					<td style="text-align: center; width: 12px; vertical-align: middle;"><a class="image_link" href="#" onclick='return ProjectManager.removeFormField("form_id_<?php echo $form_field->id ?>", <?php echo $form_field->id ?>);'><img src="../wp-content/plugins/projectmanager/images/trash.gif" alt="<?php _e( 'Delete', 'projectmanager' ) ?>" title="<?php _e( 'Delete formfield', 'projectmanager' ) ?>" /></a>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
	<p><a href='#' onclick='return ProjectManager.addFormField();'><?php _e( 'Add new formfield', 'projectmanager' ) ?></a></p>
	
	<input type="hidden" name="updateProjectManager" value="form_fields" />
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	
	<p class="submit"><input type="submit" name="updateSettings" value="<?php _e( 'Save Form Fields', 'projectmanager' ) ?> &raquo;" class="button" /></p>
	</form>
</div>
<?php endif; ?>