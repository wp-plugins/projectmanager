<?php
/**
Template page for tablenav. Loaded by do_action('projectmanager_tablenav')

The following variables are usable:

	$orderby: contains array of possible order options
	$order: contains array of possible directions of ordering (ascending, descending)
	$category: controls category dropdown, either false or contains category
	$selected_cat: currently selected category
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>		
<div class='projectmanager_tablenav'>
<form action='<?php the_permalink() ?>' method='get'>
	<input type='hidden' name='page_id' value='<?php the_ID() ?>' />
	<?php if ( $category ) : ?>
	<?php wp_dropdown_categories(array('echo' => 1, 'hide_empty' => 0, 'name' => 'cat_id', 'orderby' => 'name', 'selected' => $selected_cat, 'hierarchical' => true, 'child_of' => $category, 'show_option_all' => __('View all categories'))); ?>
	<?php endif; ?>
	<select size='1' name='orderby'>
		<?php foreach ( $orderby AS $key => $value ) : ?>
		<option value='<?php echo $key ?>' <?php if ($_GET['orderby'] == $key) echo ' selected="selected"' ?>><?php echo $value ?></option>
		<?php endforeach; ?>
	</select>
	<select size='1' name='order'>
		<?php foreach ( $order AS $key => $value ) : ?>
		<option value='<?php echo $key ?>' <?php if ($_GET['order'] == $key) echo ' selected="selected"' ?>><?php echo $value ?></option>
		<?php endforeach; ?>
	</select>
	
	<input type='submit' value='<?php _e( 'Apply' ) ?>' class='button' />
</form>
</div>