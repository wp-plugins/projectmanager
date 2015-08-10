<?php
/**
Template page for the searchform

The following variables are usable:
	
	$form_fields: contains all formfields of the selected project
	$search: holds the search request
	$search_option: contains the selected search option
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<form class='search-form alignright' action='' method='post'>
<div>
	<input type='text' class='search-input' name='search_string_<?php echo $project_id ?>' value='<?php echo $search ?>' />
	
	<?php $projectmanager->printSearchFormHiddenFields() ?>
	
	<select size='1' name='search_option_<?php echo $project_id ?>'>
		<option value='0' <?php if ( 0 == $search_option ) echo " selected='selected'" ?>><?php _e( 'Name', 'projectmanager' ) ?></option>
		<?php foreach ( $form_fields AS $form_field ) : ?>
		<?php if ( $form_field->type != 'project' ) : ?>
		<option value='<?php echo $form_field->id ?>'<?php if ( $search_option == $form_field->id ) echo " selected='selected'" ?>><?php echo $form_field->label ?></option>
		<?php endif; ?>
		<?php endforeach; ?>
		<?php if ($project->category > 0) : ?>
		<option value='-1' <?php if ( -1 == $search_option ) echo " selected='selected'" ?>><?php _e( 'Categories', 'projectmanager' ) ?></option>
		<?php endif; ?>
	</select>
	
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<input type='submit' value='<?php _e('Search', 'projectmanager') ?> &raquo;' class='button' />
</div>
</form>
