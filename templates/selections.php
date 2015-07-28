<?php
/**
Template page for selections. Loaded by do_action('projectmanager_selections')

The following variables are usable:

	$orderby: contains array of possible order options
	$order: contains array of possible directions of ordering (ascending, descending)
	$category: controls category dropdown, either false or contains category
	$selected_cat: currently selected category
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<?php if ( $category || $orderby || $order ) : ?>

<div class='projectmanager_selections'>
<form action='<?php the_permalink() ?>' method='get'>
<div>
	<input type='hidden' name='page_id' value='<?php the_ID() ?>' />
	<input type='hidden' name='project_id' value='<?php echo $project_id ?>' />
	<?php $projectmanager->printSelectionFormHiddenFields() ?>
	
	<?php if ( $category ) : ?>
	<?php wp_dropdown_categories(array('echo' => 1, 'hide_empty' => 0, 'hide_if_empty' => 1, 'name' => 'cat_id_'.$project_id, 'orderby' => 'name', 'selected' => $selected['category'], 'hierarchical' => true, 'child_of' => $category, 'show_option_all' => __('View all categories'))); ?>
	<?php endif; ?>
	<?php if ( $orderby ) : ?>
	<select size='1' name='orderby_<?php echo $project_id ?>'>
		<?php foreach ( $orderby AS $key => $value ) : ?>
		<option value='<?php echo $key ?>' <?php if ($selected['orderby'] == $key) echo ' selected="selected"' ?>><?php echo $value ?></option>
		<?php endforeach; ?>
	</select>
	<?php endif; ?>
	<?php if ( $order ) : ?>
	<select size='1' name='order_<?php echo $project_id ?>'>
		<?php foreach ( $order AS $key => $value ) : ?>
		<option value='<?php echo $key ?>' <?php if ($selected['order'] == $key) echo ' selected="selected"' ?>><?php echo $value ?></option>
		<?php endforeach; ?>
	</select>
	<?php endif; ?>
	
	<?php if ($projectmanager->hasCountryFormField()) : ?>
	<?php foreach ($projectmanager->getCountryFormFields() AS $field) : ?>
	<?php 
		if ($key = array_values(preg_grep("/country_".$field->id."/", array_keys($_GET)))) $selected_country = htmlspecialchars($_GET[$key[0]]);
		else $selected_country = "";
	?>
			
	<select size="1" style="width: 200px;" name="country_<?php echo $field->id ?>">
		<option value=""><?php printf(__('Filter by %s', 'projectmanager'), $field->label) ?></option>
		<?php foreach ($projectmanager->getCountries() AS $country) : ?>
		<option value="<?php echo $country->code ?>" <?php selected($country->code, $selected_country ); ?>><?php echo $country->name ?></option>
		<?php endforeach; ?>
	</select>
	<?php endforeach; ?>
	<?php endif; ?>
	
	<input type='submit' value='<?php _e( 'Apply' ) ?>' class='button' />
</div>
</form>
</div>

<?php endif; ?>