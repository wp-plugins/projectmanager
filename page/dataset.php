<?php
if ( !current_user_can( 'manage_projects' ) ) : 
	echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
	
else :

$project_id = $_GET['project_id'];
$projectmanager->setSettings( $project_id );
$options = get_option( 'projectmanager' );

if ( isset($_GET['edit']) ) {
	$form_title = __('Edit Dataset','projectmanager');
	$dataset_id = $_GET['edit'];
	$dataset = $projectmanager->getDataset( $dataset_id );
	$grp_id = $dataset[0]->grp_id;
	$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );

	$name = $dataset[0]->name;
	$img_filename = $dataset[0]->image;
	$meta_data = array();
	foreach ( $dataset_meta AS $meta )
		$meta_data[$meta->form_field_id] = $meta->value;
}  else {
	$form_title = __('Add Dataset','projectmanager');
	$dataset_id = ''; $grp_id = '';
}
?>
<form name="post" id="post" action="edit.php?page=projectmanager/page/show-project.php&amp;id=<?php echo $project_id ?>" method="post" enctype="multipart/form-data">
	
<?php wp_nonce_field( 'projectmanager_edit-dataset' ) ?>
	
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_id, $form_title ) ?>
			
	<h2><?php echo $form_title ?></h2>
	
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="name"><?php _e( 'Name', 'projectmanager' ) ?></label></th>
		<td><input type="text" name="name" id="name" value="<?php echo $name ?>" size="45" /></td>
	</tr>
	<?php if ( 1 == $options[$project_id]['show_image'] ) : ?>
	<tr valign="top">
		<th scope="row"><label for="projectmanager_image"><?php _e( 'Image', 'projectmanager' ) ?></label></th>
		<td>
			<?php if ( '' != $img_filename ) : ?>
			<?php $img_url = WP_CONTENT_URL .'/'. $projectmanager->getImageDir(); ?>
			<img src="<?php echo $img_url.'tiny.'.$img_filename ?>" style="float: right; margin-right: 1em;" />
			<?php endif; ?>
			<input type="file" name="projectmanager_image" id="projectmanager_image" size="45"/><p><?php _e( 'Supported file types', 'projectmanager' ) ?>: <?php echo implode( ',',$projectmanager->getSupportedImageTypes() ); ?></p>
			<?php if ( '' != $img_filename ) : ?>
			<p style="float: left;"><label for="overwrite_image"><?php _e( 'Overwrite existing image', 'projectmanager' ) ?></label><input type="checkbox" id="overwrite_image" name="overwrite_image" value="1" style="margin-left: 1em;" /></p>
			<input type="hidden" name="image_file" value="<?php echo $img_filename ?>" />
			<p style="float: right;"><label for="del_old_image"><?php _e( 'Delete current image', 'projectmanager' ) ?></label><input type="checkbox" id="del_old_image" name="del_old_image" value="1" style="margin-left: 1em;" /></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php endif; ?>
	<?php if ( $form_fields = $projectmanager->getFormFields() ) : ?>
		<?php foreach ( $form_fields AS $form_field ) : ?>
		<tr valign="top">
			<th scope="row"><label for="form_field_<?php echo $form_field->id ?>"><?php echo $form_field->label ?></label></th>
			<td>
				<?php if ( 1 == $form_field->type || 3 == $form_field->type || 5 == $form_field->type ) : ?>
				<input type="text" name="form_field[<?php echo $form_field->id ?>]" id="form_field_<?php echo $form_field->id ?>" value="<?php echo $meta_data[$form_field->id] ?>" size="45" />
				<?php elseif ( 2 == $form_field->type ) : ?>
				<div style="width: 60%;">
					<textarea class="projectmanager_mceEditor" name="form_field[<?php echo $form_field->id ?>]" id="form_field_<?php echo $form_field->id ?>" cols="42" rows="5"><?php echo $meta_data[$form_field->id] ?></textarea>
				</div>
				<?php elseif ( 4 == $form_field->type ) : ?>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][day]">
					<option value="">Tag</option>
					<option value="">&#160;</option>
					<?php for ( $day = 1; $day <= 30; $day++ ) : ?>
						<?php $selected = ( $day == substr($meta_data[$form_field->id], 8, 2) ) ? ' selected="selected"' : ''; ?>
						<option value="<?php echo $day ?>"<?php echo $selected ?>><?php echo $day ?></option>
					<?php endfor; ?>
				</select>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][month]">
					<option value="">Monat</option>
					<option value="">&#160;</option>
					<?php foreach ( $projectmanager->getMonths() AS $key => $month ) : ?>
						<?php $selected = ( $key == substr($meta_data[$form_field->id], 5, 2) ) ? ' selected="selected"' : ''; ?>
						<option value="<?php echo $key ?>"<?php echo $selected ?>><?php echo $month ?></option>
					<?php endforeach; ?>
				</select>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][year]">
					<option value="">Jahr</option>
					<option value="">&#160;</option>
					<?php for ( $year = date('Y')-50; $year <= date('Y')+10; $year++ ) : ?>
						<?php $selected = ( $year == substr($meta_data[$form_field->id], 0, 4) ) ? ' selected="selected"' : ''; ?>
						<option value="<?php echo $year ?>"<?php echo $selected ?>><?php echo $year ?></option>
					<?php endfor; ?>
				</select>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	<!-- groups selection form -->
	<tr valign="top">
		<th scope="row"><label for="category"><?php echo __( 'Group', 'projectmanager' ) ?></label></th>
		<td><?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'grp_id', 'orderby' => 'name', 'selected' => $grp_id, 'hierarchical' => true, 'child_of' => $options[$project_id]['category'], 'show_option_none' => __('None'))); ?></td>
	</tr>
	</table>
	
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<input type="hidden" name="dataset_id" value="<?php echo $dataset_id ?>" />
	<input type="hidden" name="updateProjectManager" value="dataset" />
			
	<p class="submit"><input type="submit" name="addportrait" value="<?php echo $form_title ?> &raquo;" class="button" /></p>
</div>
</form>

<?php endif; ?>