<?php

/**
 * SACK response for saving form field options
 *
 * @since 1.3
 */
function projectmanager_save_form_field_options() {
	$options = get_option('projectmanager');
	
	$form_id = $_POST['form_id'];
	$form_options = $_POST['options'];
	
	$options['form_field_options'][$form_id] = $form_options;
	update_option('projectmanager');
}


/**
 * SACK response for showing category selection in TinyMCE button
 *
 * @since 1.2
 */
function projectmanager_show_category_selection() {
	$options = get_option('projectmanager');
	$el_id = $_POST['el_id'];
	$project_id = intval($_POST['project_id']);
	
	if ( -1 != $options[$project_id]['category'] ) {	
		$cat_selection = wp_dropdown_categories( array( "hide_empty" => 0, "name" => $el_id, "orderby" => "name", "hierarchical" => true, "child_of" => $options[$project_id]['category'], "show_option_none" => __("None") ));
	
		$cat_selection = str_replace("&nbsp;", "&#160;", $cat_selection);
		$cat_selection =  str_replace("\n", '', $cat_selection);
		$cat_selection = addslashes_gpc($cat_selection);
		
		die( "function displayCategorySelection() {
			var projectId = ".$project_id.";
			catTitle = '".__("Category", "projectmanager")."';
			catSelection = '".$cat_selection."';
			if ( projectId != 0 ) {
				out = \"<td><label for='".$el_id."'>\" + catTitle + \"</label></td>\";
				out += \"<td>\" + catSelection + \"</td>\";
				document.getElementById('".$el_id."').innerHTML = out;
			}
		}
		displayCategorySelection();
		");
	}
}


/**
 * SACK response function for saving dataset name
 *
 * @since 1.2
 */
function projectmanager_save_name() {
	global $wpdb;
	
	$dataset_id = intval($_POST['dataset_id']);
	$new_name = $_POST['new_name'];
	
	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `name` = '%s' WHERE `id` = '%d'", $new_name, $dataset_id ) );

	die( "ProjectManager.reInit();jQuery('span#dataset_name_text" . $dataset_id . "').fadeOut('fast', function() {
		jQuery('a#thickboxlink_name" . $dataset_id . "').show();
		jQuery('span#dataset_name_text" . $dataset_id . "').html('" . addslashes_gpc( $new_name ) . "').fadeIn('fast');
	});");
}


/**
 * SACK response function for saving group
 *
 * @since 1.2
 */
function projectmanager_save_categories() {
	global $wpdb, $projectmanager;
	
	$dataset_id = intval($_POST['dataset_id']);
	$new_cats = explode(",",substr($_POST['cat_ids'],0,-1));
	
	if ( count($new_cats) > 0 ) {
		$cat_name = $projectmanager->getSelectedCategoryTitles($new_cats);
	} else
		$cat_name = __('None', 'projectmanager');
	
	$wpdb->query( $wpdb->prepare ( "UPDATE {$wpdb->projectmanager_dataset} SET `cat_ids` = %s WHERE `id` = %d", maybe_serialize($new_cats), $dataset_id ) );

	die( "ProjectManager.reInit();jQuery('span#dataset_category_text" . $dataset_id . "').fadeOut('fast', function() {
		jQuery('a#thickboxlink_category" . $dataset_id . "').show();
		jQuery('span#dataset_category_text" . $dataset_id . "').html('" . $cat_name . "').fadeIn('fast');
	});");
}


/**
 * SACK response function to save any dynamic form field
 *
 * @since 1.2
 */
function projectmanager_save_form_field_data() {
	global $wpdb;
	
	$dataset_id = intval($_POST['dataset_id']);
	$meta_id = intval($_POST['formfield_id']);
	$new_value = $_POST['new_value'];
	
	if ( 2 == $_POST['formfield_type'] )
		$new_value = str_replace('\n', "\n", $new_value);
		
	$new_value = addslashes_gpc( $new_value );
	if ( 1 == $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = '".$dataset_id."' AND `form_id` = '".$meta_id."'" ) )
		$wpdb->query( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '".$new_value."' WHERE `dataset_id` = {$dataset_id} AND `form_id` = {$meta_id}" );
	else
		$wpdb->query( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '".$meta_id."', '".$dataset_id."', '".$new_value."' )" );
	
	
	if ( 2 == $_POST['formfield_type'] ) {
		$new_value = str_replace("\n", "", $new_value);
		if (strlen($new_value) > 150 )
			$new_value = substr($new_value, 0, 150)."...";
	}
			
	if ( 4 == $_POST['formfield_type'] )
		$new_value = mysql2date(get_option('date_format'), $_POST['new_value']);
	
	die( "ProjectManager.reInit();jQuery('span#datafield" . $meta_id . "_" . $dataset_id . "').fadeOut('fast', function() {
		jQuery('a#thickboxlink" . $meta_id . "_" . $dataset_id . "').show();
		jQuery('span#datafield" . $meta_id . "_" . $dataset_id . "').html('" . $new_value . "').fadeIn('fast');
	});");
}

?>