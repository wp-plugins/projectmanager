<?php

/**
 * display searchform manually
 *
 * @param int $project_id Project ID
 * @param array $args assoziative array of parameters, see default values (optional)
 * @return void
 */
function projectmanager_searchform( $project_id, $args = array() ) {
	global $pmShortcodes;
	$defaults = array( 'template' => 'extend' );
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);
	echo $pmShortcodes->displaySearchForm( array('project_id' => $project_id, 'template' => $template) );
}


/**
 * display project manually
 *
 * @param int $id Project ID
 * @param array $args assoziative array of parameters, see default values (optional)
 * @return void
 */
function project( $id, $args = array() ) {
	global $pmShortcodes;
	$defaults = array( 'template' => 'table', 'cat_id' => false, 'orderby' => false, 'order' => false, 'single' => true, 'selections' => true, 'results' => true, 'field_id' => false, 'field_value' => false );
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);
	echo $pmShortcodes->displayProject( array('id' => $id, 'template' => $template, 'cat_id' => $cat_id, 'orderby' => $orderby, 'single' => $single, 'selections' => $selections, 'results' => $results, 'field_id' => $field_id, 'field_vaLue' => $field_value) );
}


/**
 * display dataset manually
 *
 * @param int $id Dataset ID
 * @param array $args assoziative array of parameters, see default values (optional)
 * @return void
 */
function dataset( $id, $args = array() ) {
	global $pmShortcodes;
	$defaults = array( 'template' => '' );
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);
	echo $pmShortcodes->displayDataset( array('id' => $id, 'template' => $template, 'echo' => 1) );
}

?>
