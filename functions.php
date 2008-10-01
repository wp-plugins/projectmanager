<?php

/**
 * print search form
 *
 * @param string $style any valid CSS style
 * @return echo
 */
function projectmanager_search_form( $style ) {
	global $projectmanager;
	
	$projectmanager->printSearchForm( $style );
}

/**
 * checks if current project has any groups
 *
 * @param none
 * @return boolean
 */
function projectmanager_has_groups() {
	global $projectmanager;
	if ( $projectmanager->getGroups() )
		return true;
	else
		return false;
}

/**
 * prints option tags for groups selection form
 *
 * @param none
 * @return echo
 */
function projectmanager_groups_selections() {
	global $wpdb, $projectmanager;
	
	$project_id = $projectmanager->getProjectID();

	$output = '';
	foreach ( $projectmanager->getGroups() AS $grp_id => $title ) {
		$num_group = $wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_dataset} WHERE `grp_id` = {$grp_id} AND `project_id` = {$project_id}");
		if ( $grp_id == $projectmanager->getCurrentGroup() )
			$selected = ' selected="selected"';
		else
			$selected = '';

		$output .= '<option value="'.$grp_id.'"'.$selected.'>'.$title.' ('.$num_group.')</option>';
	}
	
	echo $output;
}

/**
 * checks if grp_id is the same as current selected group
 *
 * @param none
 * @return boolean
 */
function projectmanager_is_current_group() {
	global $grp_id;
	
	if ( $grp_id == $projectmanager->getCurrentGroup() )
		return true;
	else
		return false;
}

/**
 * checks if current page is startpage of project
 *
 * @param none
 * @return boolean
 */
function projectmanager_is_home() {
	if ( ( isset($_GET['grp_id']) AND '' != $_GET['grp_id'] ) || isset($_POST['projectmanager_search']) )
		return false;
	else
		return true;
}

/**
 * checks if search formular has been submitted
 *
 * @param none
 * @return boolean
 */
function projectmanager_is_search() {
	if ( isset($_POST['projectmanager_search']) )
		return true;
	
	return false;
}

/**
 * gets search results from database
 *
 * @param none
 * @return false | array
 */
function projectmanager_get_search() {
	global $projectmanager;
	
	if ( !isset( $_POST['projectmanager_search'] ) ) {
		return false;
	} else {
		$search_results =  $projectmanager->getSearchResults( $_POST['projectmanager_search'], $_POST['form_field']);
		return $search_results;
	}
}

/**
 * gets datasets from database
 *
 * @param none
 * @return array
 */
function projectmanager_get_datasets() {
	global $projectmanager, $DATASET_OFFSET;
	
	$project_id = $projectmanager->getProjectID();
		
	if ( projectmanager_is_single() ) {
		$DATASET_OFFSET = $projectmanager->getDatasetOffset( $_GET['show'] );
		$dataset_list = $projectmanager->getDataset( null, 'id ASC', false, $project_id );
		//$num_datasets = $projectmanager->getNumDatasets( $project_id );
		
		$dataset = $dataset_list;
	} else  {
		$dataset = $projectmanager->getDataset( null, 'name ASC', true, $project_id );
	}
	return $dataset;
}

/**
 * gets dataset meta values
 *
 * @param none
 * @return echo
 */
function projectmanager_dataset_meta( $output ) {
	global $projectmanager;
	$projectmanager->printDatasetMetaData( projectmanager_get_dataset_id(), $output );
}

/**
 * gets number of found datasets for search query
 *
 * @param none
 * @return int
 */
function projectmanager_get_num_search_found() {
	global $projectmanager;
	
	$search_results = projectmanager_get_search();
	$num_found = count($search_results);
	return $num_found;
}
/**
 * prints number of found datasets for search query
 *
 * @param none
 * @return echo
 */
function projectmanager_num_search_found() {
	echo projectmanager_get_num_search_found();
}

/**
 * gets total number of datasets for project
 *
 * @param none
 * @return int
 */
function projectmanager_get_num_total() {
	global $wpdb, $projectmanager;

	$project_id = $projectmanager->getProjectID();
	$num_total = $projectmanager->getNumDatasets( $project_id );
	return $num_total;
}
/**
 * prints total number of datasets for project
 *
 * @param none
 * @return echo
 */
function projectmanager_num_total() {
	echo projectmanager_get_num_total();
}

/**
 * gets number of groups for project
 *
 * @param none
 * @return int
 */
function projectmanager_get_num_groups() {
	global $projectmanager;
	
	$num_groups = ( $projectmanager->getGroups() ) ? count($projectmanager->getGroups()) : 0;
	return $num_groups;
}
/**
 * prints number of groups for project
 *
 * @param none
 * @return int
 */
function projectmanager_num_groups() {
	echo projectmanager_get_num_groups();
}

/**
 * gets group title of current group
 *
 * @param none
 * @return string
 */
function projectmanager_get_group_title() {
	global $projectmanager;
	
	return $projectmanager->getGroups( $_GET['grp_id'] );
}
/**
 * prints group title of current group
 *
 * @param none
 * @return string
 */
function projectmanager_group_title() {
	echo projectmanager_get_group_title();
}

/**
 * prints project title
 *
 * @param none
 * @return echo
 */
function projectmanager_project_title() {
	global $projectmanager;
	
	echo $projectmanager->getProjectTitle(  $projectmanager->getProjectID() );
}

/**
 * prints table header with dataset meta labels
 *
 * @param none
 * @return echo
 */
function projectmanager_table_header() {
	global $projectmanager;
	$projectmanager->printTableHeader();
}

/**
 * checks if there are any datasets for current project
 *
 * @param none
 * @return boolean
 */
function projectmanager_has_dataset() {
	global $projectmanager, $PROJECTMANAGER_DATASET;
	
	if ( $PROJECTMANAGER_DATASET = projectmanager_get_search() )
		return true;
	elseif ( $PROJECTMANAGER_DATASET = projectmanager_get_datasets() )
		return true;
	else
		return false;	
}

/**
 * gets dataset as table
 *
 * @param boolean $single (optional) if true, link to single dataset. if false, no link is printed
 * @return string
 */
function projectmanager_get_datasets_table( $single = true ) {
	global $projectmanager, $PROJECTMANAGER_DATASET;
	
	$out = '';
	if ( $PROJECTMANAGER_DATASET ) {
		foreach ( $PROJECTMANAGER_DATASET AS $dataset ) {
			$name = ( $single ) ? '<a href="'.$projectmanager->pagination->createURL().'?grp_id='.$projectmanager->getCurrentGroup().'&amp;show='.$dataset->id.'">'.$dataset->name.'</a>' : $dataset->name;
			$class = ("alternate" == $class) ? '' : "alternate";
			$out .= '<tr class="'.$class.'"><td>'.$name.'</td>';
			$out .= $projectmanager->getDatasetMetaData( $dataset->id, 'td' );
			$out .= '</tr>';
		}
	}
	
	return $out;
}
/**
 * prints dataset as table. see projectmanager_get_datasets_table() for usage
 *
 * @param boolean $string
 * @return echo
 */
function projectmanager_datasets_table( $single = true ) {
	echo projectmanager_get_datasets_table( $single );
}

/**
 * checks if current page is the single dataset page
 *
 * @param none
 * @return boolean
 */
function projectmanager_is_single() {
	if ( isset( $_GET['show'] ) )
		return true;
	
	return false;
}

/**
 * prints pagination
 *
 * @param none
 * @return echo
 */
function projectmanager_pagination() {
	global $projectmanager;
	echo $projectmanager->pagination->get();
}

/**
 * prints dataset overview as gallery table
 *
 * @param none
 * @return echo
 */
function projectmanager_gallery() {
	global $projectmanager, $PROJECTMANAGER_DATASET;
	
	$options = get_option( 'projectmanager' );
	$out = '';
	
	$project_id = $projectmanager->getProjectID();
	$i = 0;
	foreach( $PROJECTMANAGER_DATASET AS $dataset ) {
		$i++;
		$out .= '<td style="padding: 5px;">';
		if ($options[$project_id]['show_image'] == 1)
			$out .= '<a href="'.$projectmanager->pagination->createURL().'?grp_id='.$projectmanager->getCurrentGroup().'&amp;show='.$dataset->id.'"><img src="'.get_bloginfo('wpurl').'/wp-content/'.$projectmanager->getThumbsDir().$dataset->image.'" alt="'.$dataset->name.'" title="'.$dataset->name.'" /></a>';
			
		$out .= '<p class="caption" style="margin: 2px 0px 5px 0px;"><a href="?grp_id='.$projectmanager->getCurrentGroup().'&amp;show='.$dataset->id.'">'.$dataset->name.'</a></p>';
		$out .= '</td>';
		
		if ( ( ( 0 == $i % $projectmanager->getNumCols())) && ( $i < count($PROJECTMANAGER_DATASET) ) )
			$out .= '</tr><tr>';
	}
		
	echo $out;
}

/**
 * generates return link from single view back to overview
 *
 * @param none
 * @return echo
 */
function projectmanager_list_return_link() {
	global $projectmanager, $DATASET_OFFSET;
	$x = $DATASET_OFFSET+1;
	
	$list_page = ceil($x/$projectmanager->getPerPage());
		echo '<a href="'.$projectmanager->pagination->createURL().'paging='.$list_page.'">'.__('Back to thumbnail list', 'projectmanager').'</a>';
}

/**
 * checks if current dataset has an image to display. Only usable in single view!
 *
 * @param none
 * @return boolean
 */
function projectmanager_dataset_has_image() {
	global $projectmanager, $PROJECTMANAGER_DATASET, $DATASET_OFFSET;;
	
	$project_id = $projectmanager->getProjectID();
	$options = get_option( 'projectmanager' );
	if ( 1 == $options[$project_id]['show_image'] && '' != $PROJECTMANAGER_DATASET[$DATASET_OFFSET]->image )
		return true;
	else
		return false;
}

/**
 * gets name of current dataset. Only usable in single view!
 *
 * @param none
 * @return string
 */
function projectmanager_get_dataset_name() {
	global $PROJECTMANAGER_DATASET, $DATASET_OFFSET;
	return htmlspecialchars($PROJECTMANAGER_DATASET[$DATASET_OFFSET]->name);
}
/**
 * prints name of current database. Only usable in single view!
 *
 * @param none
 * @return string
 */
function projectmanager_dataset_name() {
	echo projectmanager_get_dataset_name();
}

/**
 * gets id of current dataset. Only usable in single view!
 *
 * @param none
 * @return int
 */
function projectmanager_get_dataset_id() {
	global $PROJECTMANAGER_DATASET, $DATASET_OFFSET;
	return $PROJECTMANAGER_DATASET[$DATASET_OFFSET]->id;
}
/**
 * pritns id of current dataset. Only usable in single view!
 *
 * @param none
 * @return echo
 */
function projectmanger_dataset_id() {
	echo projectmanager_get_dataset_id();
}

/**
 * gets image of current dataset. Only usable in single view!
 *
 * @param none
 * @return string
 */
function projectmanager_get_dataset_image() {
	global $projectmanager, $PROJECTMANAGER_DATASET, $DATASET_OFFSET;
	
	$image = get_bloginfo('wpurl').'/wp-content/'.$projectmanager->getImageDir().$PROJECTMANAGER_DATASET[$DATASET_OFFSET]->image;
	return $image;
}
/**
 * prints image of current dataset. Only usable in single view!
 *
 * @param none
 * @return echo
 */
function projectmanager_dataset_image() {
	echo projectmanager_get_dataset_image();
}


/**
 * print current URL
 *
 * @param none
 * @return echo
 */
function projectmanager_groups_form() {
	global $projectmanager, $wp_query;
	
	$page_obj = $wp_query->get_queried_object();
		echo get_permalink($page_obj->ID);
	echo '<form class="projectmanager" action="" method="get" onchange="this.submit()" style="float: left; margin-bottom: 2em;">
		<select size="1" name="grp_id">
			<option value="">Groups</option>
			<option value="">-------------</option>';
			projectmanager_groups_selections();
	echo  '</select>
		<input type="submit" value="Go" />
	</form>';
}