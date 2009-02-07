<table class="form-table">
	<?php if (!$is_profile_page) : ?>
	<tr valign="top">
		<th scope="row"><label for="name"><?php _e( 'Name', 'projectmanager' ) ?></label></th>
		<td><input type="text" name="name" id="name" value="<?php echo $name ?>" size="45" /></td>
	</tr>
	<?php endif; ?>
	<?php if ( 1 == $options['show_image'] ) : ?>
	<tr valign="top">
		<th scope="row"><label for="projectmanager_image"><?php _e( 'Image', 'projectmanager' ) ?></label></th>
		<td>
			<?php if ( '' != $img_filename ) : ?>
				<img src="<?php echo $projectmanager->getImageUrl('tiny.'.$img_filename)?>" class="alignright" />
			<?php endif; ?>
			<input type="file" name="projectmanager_image" id="projectmanager_image" size="45"/><p><?php _e( 'Supported file types', 'projectmanager' ) ?>: <?php echo implode( ',',$projectmanager->getSupportedImageTypes() ); ?></p>
			<?php if ( '' != $img_filename ) : ?>
				<p class="alignleft"><label for="overwrite_image"><?php _e( 'Overwrite existing image', 'projectmanager' ) ?></label><input type="checkbox" id="overwrite_image" name="overwrite_image" value="1" style="margin-left: 1em;" /></p>
				<input type="hidden" name="image_file" value="<?php echo $img_filename ?>" />
				<p class="alignright"><label for="del_old_image"><?php _e( 'Delete current image', 'projectmanager' ) ?></label><input type="checkbox" id="del_old_image" name="del_old_image" value="1" style="margin-left: 1em;" /></p>
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
					<textarea class="projectmanager_mceEditor" name="form_field[<?php echo $form_field->id ?>]" id="form_field_<?php echo $form_field->id ?>" cols="70" rows="8"><?php echo $meta_data[$form_field->id] ?></textarea>
				</div>
				<?php elseif ( 4 == $form_field->type ) : ?>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][day]">
					<option value="">Tag</option>
					<option value="">&#160;</option>
					<?php for ( $day = 1; $day <= 30; $day++ ) : ?>
						<option value="<?php echo $day ?>"<?php if ( $day == substr($meta_data[$form_field->id], 8, 2) ) echo ' selected="selected"'; ?>><?php echo $day ?></option>
					<?php endfor; ?>
				</select>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][month]">
					<option value="">Monat</option>
					<option value="">&#160;</option>
					<?php foreach ( $projectmanager->getMonths() AS $key => $month ) : ?>
						<option value="<?php echo $key ?>"<?php if ( $key == substr($meta_data[$form_field->id], 5, 2) ) echo ' selected="selected"'; ?>><?php echo $month ?></option>
					<?php endforeach; ?>
				</select>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][year]">
					<option value="0000">Jahr</option>
					<option value="0000">&#160;</option>
					<?php for ( $year = date('Y')-50; $year <= date('Y')+10; $year++ ) : ?>
						<option value="<?php echo $year ?>"<?php if ( $year == substr($meta_data[$form_field->id], 0, 4) ) echo ' selected="selected"' ?>><?php echo $year ?></option>
					<?php endfor; ?>
				</select>
				<?php elseif ( 6 == $form_field->type ) : $projectmanager->printFormFieldDropDown($form_field->id, $meta_data[$form_field->id], $dataset_id, "form_field[".$form_field->id."]"); ?>
				<?php elseif ( 7 == $form_field->type ) : $projectmanager->printFormFieldCheckboxList($form_field->id, $meta_data[$form_field->id], $dataset_id, "form_field[".$form_field->id."][]"); ?>
				<?php elseif ( 8 == $form_field->type ) : $projectmanager->printFormFieldRadioList($form_field->id, $meta_data[$form_field->id], $dataset_id, "form_field[".$form_field->id."]"); ?>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if ( -1 != $options['category'] && current_user_can('projectmanager_admin') ) : ?>
	<!-- category selection form -->
	<tr valign="top">
		<th scope="row"><label for="post_category"><?php _e( 'Categories', 'projectmanager' ) ?></label></th>
		<td>
			<div id="projectmanager-category-adder">
			<ul class="categorychecklist">
				<?php $this->categoryChecklist( $options['category'], $cat_ids ) ?>
			</ul>
			</div>
		</td>
	</tr>
	<?php endif; ?>
	<?php if ( isset($_GET['edit']) && current_user_can('projectmanager_admin') && !$is_profile_page ) : ?>
	<tr valign="top">
		<th scope="row"><label for="owner"><?php _e( 'Owner', 'projectmanager' ) ?></label></th>
		<td><?php wp_dropdown_users( array('selected' => $dataset->user_id, 'name' => 'owner') ) ?></td>
	</tr>
	<?php endif; ?>
</table>