<?php
if ( !current_user_can( 'manage_projects' ) ) : 
	echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
	
else :

$project_id = $_GET['project_id'];
$projectmanager->setSettings( $project_id );
$options = get_option( 'projectmanager' );

/*
* Create Thumbnails
*/
if ( 1 == $options[$project_id]['show_image'] ) {
	require_once( ABSPATH . PLUGINDIR . '/projectmanager/image.php' );
	$image = new Image();
	$image->thumbnails( $projectmanager->getImageDir(), $projectmanager->getThumbsDir(), $options[$project_id]['thumb_size']['width'], $options[$project_id]['thumb_size']['height']);
}

if ( isset($_GET['edit']) ) {
	$form_title = 'Edit Dataset';
	$dataset_id = $_GET['edit'];
	$dataset = $projectmanager->getDataset( $dataset_id );
	$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );

	$name = $dataset[0]->name;
	$img_filename = $dataset[0]->image;
	$meta_data = array();
	foreach ( $dataset_meta AS $meta )
		$meta_data[$meta->form_field_id] = $meta->value;
}  else {
	$form_title = 'Add Dataset';
	$dataset_id = '';
}
?>
<form name="post" id="post" class="projectmanager" action="edit.php?page=projectmanager/page/show-project.php&amp;id=<?php echo $project_id ?>" method="post" enctype="multipart/form-data">
	
<?php wp_nonce_field( 'projectmanager_edit-dataset' ) ?>
	
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_id, $form_title ) ?>
			
	<h2><?php echo __( $form_title, 'projectmanager' ) ?></h2>
	
	<?php if ( 1 == $options[$project_id]['show_image'] && '' != $img_filename ) : ?>
	<div style="float: right; clear: both;">
		<p><strong><?php echo __( 'Current Image', 'projectmanager' ) ?></strong></p>
		<p><img src="<?php echo get_option('siteurl') ?>/wp-content/<?php echo $projectmanager->getThumbsDir().$img_filename ?>"/></p>
		<input type="hidden" name="image_file" value="<?php echo $img_filename ?>" />
		<label for="del_image" style="width: auto;"><?php _e( 'Delete Image','projectmanager' ) ?></label><input type="checkbox" name="del_image" id="del_image" value="1" />
	</div>
	<?php endif; ?>
	
	<label for="name"><?php _e( 'Name', 'projectmanager' ) ?>:</label><input type="text" name="name" id="name" value="<?php echo $name ?>" size="45" /><br />
		
	<?php if ( 1 == $options[$project_id]['show_image'] ) : ?>
	<label for="projectmanager_image"><?php _e( 'Image', 'projectmanager' ) ?>:</label><input type="file" name="projectmanager_image" id="projectmanager_image" size="45"/><br />
	<p class="info"><?php _e( 'Supported file types', 'projectmanager' ) ?>: <?php echo implode( ',',$projectmanager->getSupportedImageTypes() ); ?></p>
	<?php endif; ?>
				
	<?php if ( $form_fields = $projectmanager->getProjectMeta() ) : ?>
		<?php foreach ( $form_fields AS $form_field ) : ?>
			<label for="form_field_<?php echo $form_field->id ?>"><?php echo $form_field->label ?></label>
			<?php if ( 1 == $form_field->type || 3 == $form_field->type || 5 == $form_field->type ) : ?>
				<input type="text" name="form_field[<?php echo $form_field->id ?>]" id="form_field_<?php echo $form_field->id ?>" value="<?php echo $meta_data[$form_field->id] ?>" size="45" /><br />
			<?php elseif ( 2 == $form_field->type ) : ?>
				<div id="poststuff" style="margin:1em auto 0.5em 0; width: 60%;">
					<!--<fieldset id="<?php echo user_can_richedit() ? 'postdiv' : 'postdiv'; ?>">-->
						<?php //the_editor($form_field_data[$form_field->id], 'form_field['.$form_field->id.']') ?>
						<div><textarea class="projectmanager_mceEditor" name="form_field[<?php echo $form_field->id ?>]" id="form_field_<?php echo $form_field->id ?>" cols="42" rows="5"><?php echo $meta_data[$form_field->id] ?></textarea></div><br />
					<!--</fieldset>-->
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
				</select><br />
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
			
	<!-- groups selection form -->
	<?php if( $projectmanager->getGroups() ) : ?>
	<label for="category"><?php echo __( 'Group', 'projectmanager' ) ?>:</label>
	<select size="1" name="grp_id" id="category">
		<option value=""><?php echo __( 'Select group', 'projectmanager' ) ?></option>
		<option value="">-------------------</option>
		<option value="">&#160;</option>
		<?php foreach ( $projectmanager->getGroups() AS $key => $val ) : ?>
			<?php if ( isset($dataset[0]->grp_id) AND $dataset[0]->grp_id == $key ) : ?>
				<option value='<?php echo $key ?>' selected='selected'>
			<?php else : ?>
				<option value='<?php echo $key ?>'>
			<?php endif; ?>
			<?php echo $val; ?>
			</option>
		<?php endforeach; ?>
	</select>
	<?php endif; ?>
	
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<input type="hidden" name="dataset_id" value="<?php echo $dataset_id ?>" />
	<input type="hidden" name="updateProjectManager" value="dataset" />
			
	<p class="submit"><input type="submit" name="addportrait" value="<?php echo __( $form_title, 'projectmanager' ) ?> &raquo;" class="button" /></p>
</div>
</form>

<?php endif; ?>