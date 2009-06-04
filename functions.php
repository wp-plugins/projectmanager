<?php

/**
 * SACK response to delete file
 * 
 * @since 1.4
 */
function projectmanager_ajax_delete_file() {
	$file = $_POST['file'];
	@unlink($file);
	die();
}


/**
 * SACK response for saving form field options
 *
 * @since 1.3
 */
function projectmanager_save_form_field_options() {
	$options = get_option('projectmanager');
	
	$form_id = $_POST['form_id'];
	$form_options = explode("\n", $_POST['options']);
	
	$options['form_field_options'][$form_id] = $form_options;
	update_option('projectmanager', $options);
	
	die("ProjectManager.reInit();");
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
	
	/*if (get_magic_quotes_gpc())
		$new_name = stripslashes_deep($new_name);*/
		
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
	global $wpdb, $projectmanager, $projectmanager_loader;
	
	$dataset_id = intval($_POST['dataset_id']);
	$formfield_type = $_POST['formfield_type'];
	$meta_id = intval($_POST['formfield_id']);
	$new_value = $_POST['new_value'];

	// Textarea
	if ( 'textfield' == $formfield_type )
		$new_value = str_replace('\n', "\n", $new_value);
	// Checkbox List
	if ( 'checkbox' == $formfield_type )
		$new_value = substr($new_value,0,-1);

	$count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = '".$dataset_id."' AND `form_id` = '".$meta_id."'" );
	if ( !empty($count) )
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '%s' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", $new_value, $dataset_id, $meta_id ) );
	else
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '%s' )", $meta_id, $dataset_id, $new_value ) );
	
	// Textarea
	if ( 'textfield' == $formfield_type ) {
		$new_value = str_replace("\n", "", $new_value);
		if (strlen($new_value) > 150 )
			$new_value = substr($new_value, 0, 150)."...";
	}
			
	// Some special output formats
	if ( 'date' == $formfield_type )
		$new_value = mysql2date(get_option('date_format'), $_POST['new_value']);
	elseif ( 'image' == $formfield_type )
		$new_value = '<img class="projectmanager_image" src="'.$new_value.'" alt="'.__("Image", "projectmanager").'" />';
	elseif ( 'uri' == $formfield_type )
		$new_value = '<a class="projectmanager_url" href="http://'.$projectmanager->extractURL($new_value, 'url').'" target="_blank" title="'.$projectmanager->extractURL($new_value, 'title').'">'.$projectmanager->extractURL($new_value, 'title').'</a>';
	elseif ( 'email' == $formfield_type )
		$new_value = '<a href="mailto:'.$projectmanager->extractURL($new_value, 'url').'" class="projectmanager_email">'.$projectmanager->extractURL($new_value, 'title').'</a>';	
	elseif ( 'numeric' == $formfield_type ) {
		if ( class_exists('NumberFormatter') ) {
			$fmt = new NumberFormatter( get_locale(), NumberFormatter::DECIMAL );
			$meta_value = $fmt->format($meta_value);
		} else {
			$meta_value = apply_filters( 'projectmanager_numeric', $meta_value );
		}
	} elseif ( 'currency' == $formfield_type ) {
		if ( class_exists('NumberFormatter') ) {
			$fmt = new NumberFormatter( get_locale(), NumberFormatter::CURRENCY );
			$meta_value = $fmt->format($meta_value);
		} else {
			$meta_value = money_format('%i', $meta_value);
			$meta_value = apply_filters( 'projectmanager_currency', $meta_value );
		}
	}

	die( "ProjectManager.reInit();jQuery('span#datafield" . $meta_id . "_" . $dataset_id . "').fadeOut('fast', function() {
		jQuery('a#thickboxlink" . $meta_id . "_" . $dataset_id . "').show();
		jQuery('span#datafield" . $meta_id . "_" . $dataset_id . "').html('" . $new_value . "').fadeIn('fast');
	});");
}


/**
 * SACK response to manually set order of datasets
 *
 * @since 2.0
 */
function projectmanager_save_dataset_order() {
	global $wpdb, $projectmanager_loader;
	$order = $_POST['order'];
	$order = $projectmanager_loader->adminPanel->getOrder($order);
	foreach ( $order AS $order => $dataset_id ) {
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `order` = '%d' WHERE `id` = '%d'", $order, $dataset_id ) );
	}
}



/**
 * SACK response to insert user data from database
 *
 * @since 2.5
 */
function projectmanager_insert_wp_user() {
	$user_id = (int)$_POST['wp_user_id'];
	$user = new WP_User($user_id);
	$user = $user->data;

	die("
		document.getElementById('name').value = '".$user->display_name."';
		document.getElementById('user_id').value = '".$user_id."';
	");
}
?>
