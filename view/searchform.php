<?php
/**
Template page for the searchform

The following variables are usable:
	
	$form_fields: contains all formfields of the selected project
	$search_string: holds the search request
	$search_option: contains the selected search option
	$align: contains the alignment of the formular, can be either 'alignleft', 'alignright', 'aligncenter'
	$display: controls if the search options are displayed ('extend') or not ('compact')
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<form class='search-form <?php echo $align ?>' action='' method='post'>
	<input type='text' class='search-input' name='search_string' value='<?php echo $search_string ?>' />
	<?php if ( $display == 'extend' && $form_fields ) : ?>
		<select size='1' name='search_option'>
			<option value='0' <?php if ( 0 == $search_option ) echo " selected='selected'" ?>><?php _e( 'Name', 'projectmanager' ) ?></option>
			<?php foreach ( $form_fields AS $form_field ) : ?>
			<option value='<?php echo $form_field->id ?>'<?php if ( $search_option == $form_field->id ) echo " selected='selected'" ?>><?php echo $form_field->label ?></option>
			<?php endforeach; ?>
			<option value='-1' <?php if ( -1 == $search_option ) echo " selected='selected'" ?>><?php _e( 'Categories', 'projectmanager' ) ?></option>
		</select>
	<?php else : ?>
	<input type='hidden' name='form_field' value='0' />
	<input type='submit' value='<?php _e('Search', 'projectmanager') ?> &raquo;' class='button' />
</form>