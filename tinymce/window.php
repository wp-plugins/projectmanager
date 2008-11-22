<?php

//$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$root = '/var/www/wordpress';

if (file_exists($root.'/wp-load.php')) {
	// WP 2.6
	require_once($root.'/wp-load.php');
} else {
	// Before 2.6
	if (!file_exists($root.'/wp-config.php'))  {
		echo "Could not find wp-config.php";	
		die;	
	}// stop when wp-config is not there
	require_once($root.'/wp-config.php');
}

require_once(ABSPATH.'/wp-admin/admin.php');

// check for rights
if(!current_user_can('edit_posts')) die;

global $wpdb;

$options = get_option('projectmanager');

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('Projectmanager', 'projectmanager') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo PROJECTMANAGER_URL ?>/tinymce/tinymce.js"></script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="ProjectManager" action="#">
	<div class="tabs">
		<ul>
			<li id="list_tab" class="current"><span><a href="javascript:mcTabs.displayTab('list_tab', 'list_panel');" onmouseover="return false;"><?php _e( 'Simple Output', 'projectmanager' ); ?></a></span></li>
			<li id="gallery_tab"><span><a href="javascript:mcTabs.displayTab('gallery_tab', 'gallery_panel');" onmouseover="return false;"><?php _e( 'Gallery', 'projectmanager' ); ?></a></span></li>
			<li id="groups_tab"><span><a href="javascript:mcTabs.displayTab('groups_tab', 'groups_panel');" onmouseover="return false;"><?php _e( 'Groups', 'projectmanager' ); ?></a></span></li>
			<li id="search_tab"><span><a href="javascript:mcTabs.displayTab('search_tab', 'search_panel');" onmouseover="return false;"><?php _e( 'Search Form', 'projectmanager' ); ?></a></span></li>
		</ul>
	</di>
	<div class="panel_wrapper">
		
	<!-- dataset list panel -->
	<div id="list_panel" class="panel current"><br />
	<table style="border: 0;" cellpadding="5" id="list_panel_table">
	<tr>
		<td><label for="list_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="list_projects" name="list_projects" style="width: 200px">
        	<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projectlist = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( is_array($projectlist) ) {
				foreach( $projectlist as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	<tr id='list_projects_groups_form'>
	<script language="javascript" type="text/javascript">
	function displayListGroupSelection() {
		var list_projects_id = document.getElementById('list_projects').value;
		<?php echo 'groupTitle = "'.__("Group", "projectmanager").'";'; ?>
		<?php echo 'groupSelection = "'.wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'list_projects_group', 'orderby' => 'name', 'hierarchical' => true, 'show_option_none' => __('None').'";'; ?>
		if ( list_projects_id != 0 ) {
			out = "\n\t<td><label for='list_projects_group'>" + groupTitle + "</label></td>";
			//out += "\n\t<td><?php //)); ?></td>";
			document.getElementById('list_projects_groups_form').innerHTML = out;
		}
	}
	</script>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label><?php _e( 'Show as', 'projectmanager' ) ?></label></td>
		<td>
		<input type="radio" name="list_showtype" id="list_showtype_table" value="table" checked="ckecked" /><label for="list_showtype_table"><?php _e( 'Table', 'projectmanager' ) ?></label><br />
		<input type="radio" name="showtype" id="showtype_ul" value="ul" /><label for="type_ul"><?php _e( 'Unsorted List', 'projectmanager' ) ?></label><br />
		<input type="radio" name="list_showtype" id="list_showtype_ol" value="ol" /><label for="list_showtype_ol"><?php _e( 'Sorted List', 'projectmanager' ) ?></label>
		</td>
	</tr>
	</table>
	</div>
	
	<!-- gallery panel -->
	<div id="gallery_panel" class="panel"><br />
	<table style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="gallery_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="gallery_projects" name="gallery_projects" style="width: 200px">
		<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projectlist = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( is_array($projectlist) ) {
				foreach( $projectlist as $project ) {
					echo $project->id;
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
				}
			}
		?>
        	</select>
		</td>
	</tr>
	<tr>
		<td><label for="gallery_projects_group"><?php _e("Group", 'projectmanager'); ?></label></td>
		<td><?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'gallery_projects_group', 'orderby' => 'name', 'hierarchical' => true, 'show_option_none' => __('None'))); ?></td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="num_cols"><?php _e( 'Columns', 'projectmanager' ) ?></label></td>
		<td><input type="text" name="num_cols" id="num_cols" value="" size="3" /></td>
	</tr>
	</table>
	</div>
	
	<!-- groups panel -->
	<div id="groups_panel" class="panel"><br />
	<table style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="groups_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="groups_projects" name="groups_projects" style="width: 200px">
		<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projectlist = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( is_array($projectlist) ) {
				foreach( $projectlist as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label><?php _e( 'Show as', 'projectmanager' ) ?></label></td>
		<td>
			<input type="radio" name="groups_showtype" id="groups_showtype_dropdown" value="dropdown" /><label for="groups_showtype_dropdown"><?php _e( 'Dropdown Menu', 'projectmaanger' ) ?></label><br />
			<input type="radio" name="groups_showtype" id="groups_showtype_list" value="list" /><label for="groups_showtype_list"><?php _e( 'List', 'projectmanager' ) ?></label>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="align_groups"><?php _e( 'Alignment', 'projectmanager' ) ?></label></td>
		<td>
			<select size="1" name="align_groups" id="align_groups">
				<option value="left"><?php _e( 'Left' ) ?></option>
				<option value="center"><?php _e( 'Center' ) ?></option>
				<option value="right"><?php _e( 'Right' ) ?></option>
			</select>
		</td>
	</tr>
	</table>
	</div>
	
	<!-- search panel -->
	<div id="search_panel" class="panel"><br />
	<table style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="search_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="search_projects" name="search_projects" style="width: 200px">
		<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projectlist = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( is_array($projectlist) ) {
				foreach( $projectlist as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="align_search"><?php _e( 'Alignment', 'projectmanager' ) ?></label></td>
		<td>
			<select size="1" name="align_search" id="align_search">
				<option value="left"><?php _e( 'Left' ) ?></option>
				<option value="center"><?php _e( 'Center' ) ?></option>
				<option value="right"><?php _e( 'Right' ) ?></option>
			</select>
		</td>
	</tr>
	</table>
	</div>
	
	</div>
	
	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'projectmanager'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'projectmanager'); ?>" onclick="insertProjectManagerLink();" />
		</div>
	</div>

</form>
<script language="javascript" type="text/javascript">
	addAttributes();
</script>
</body>
</html>