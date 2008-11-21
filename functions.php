<?php

/**
 * SACK response function for saving group
 *
 * @since 1.2
 */
function projectmanager_save_group() {
	global $wpdb, $projectmanager;
	
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
}

?>