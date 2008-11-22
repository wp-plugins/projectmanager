<?php

/**
 * SACK response function for saving group
 *
 * @since 1.2
 */
function projectmanager_save_group() {
	global $wpdb;
	
	$dataset_id = intval($_POST['dataset_id']);
	$group = intval($_POST['group']);
	
	if ( $group != -1 ) {
		$cat = get_category($group);
		$cat_name = $cat->name;
	} else
		$cat_name = __('None', 'projectmanager');
	
	$wpdb->query( $wpdb->prepare ( "UPDATE {$wpdb->projectmanager_dataset} SET `grp_id` = %d WHERE `id` = %d", $group, $dataset_id ) );

	die( "ProjectManager.reInit;jQuery('span#prjctmngr_group" . $dataset_id . "').fadeOut('fast', function() {
		jQuery('a#thickboxlink" . $dataset_id . "').show();
		jQuery('span#prjctmngr_group" . $dataset_id . "').html('" . addslashes_gpc( $cat_name ) . "').fadeIn('fast');
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
	
	if ( 2 == $_POST['formfield_type'] ) {
		$new_value_old = $new_value;
		$new_value = str_replace('\\n', "\r", $new_value);
	}
	
	if ( 1 == $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = '".$dataset_id."' AND `form_id` = '".$meta_id."'" ) )
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '%s' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", $new_value, $dataset_id, $meta_id ) );
	else
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '%s' )", $meta_id, $dataset_id, $new_value ) );

	$new_value = addslashes_gpc( $new_value );
	
	if ( 2 == $_POST['formfield_type'] ) {
		$new_value = $new_value_old;
		$new_value = str_replace('\n', "", $new_value);
		if (strlen($new_value) > 150 )
			$new_value = substr($new_value, 0, 150)."...";
	}
			
	if ( 4 == $_POST['formfield_type'] )
		$new_value = mysql2date(get_option('date_format'), $_POST['new_value']);
	
	die( "ProjectManager.reInit;jQuery('span#datafield" . $meta_id . "_" . $dataset_id . "').fadeOut('fast', function() {
		jQuery('a#thickboxlink" . $meta_id . "_" . $dataset_id . "').show();
		jQuery('span#datafield" . $meta_id . "_" . $dataset_id . "').html('" . $new_value . "').fadeIn('fast');
	});");
}

?>