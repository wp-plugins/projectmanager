<?php

$root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));

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

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('Projectmanager', 'projectmanager') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<?php wp_register_script( 'projectmanager', PROJECTMANAGER_URL.'/admin/js/functions.js', array( 'colorpicker', 'sack' ), PROJECTMANAGER_VERSION ); wp_register_script ('projectmanager_ajax', PROJECTMANAGER_URL.'/admin/js/ajax.js', array( 'projectmanager' ), PROJECTMANAGER_VERSION ); wp_print_scripts( 'projectmanager_ajax'); ?>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo PROJECTMANAGER_URL ?>/admin/tinymce/tinymce.js"></script>
	<script type="text/javascript">
	//<![CDATA[
	ProjectManagerAjaxL10n = {
		blogUrl: "<?php bloginfo( 'wpurl' ); ?>",
		//pluginPath: "<?php echo PROJECTMANAGER_PATH; ?>",
		pluginUrl: "<?php echo PROJECTMANAGER_URL; ?>",
		requestUrl: "<?php  bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
		imgUrl: "<?php echo PROJECTMANAGER_URL; ?>/images",
		Edit: "<?php _e("Edit"); ?>",
		Post: "<?php _e("Post"); ?>",
		Save: "<?php _e("Save"); ?>",
		Cancel: "<?php _e("Cancel"); ?>",
		pleaseWait: "<?php _e("Please wait..."); ?>",
		Revisions: "<?php _e("Page Revisions"); ?>",
		Time: "<?php _e("Insert time"); ?>",
		Options: "<?php _e("Options", "projectmanager") ?>",
		Delete: "<?php _e('Delete', 'projectmanager') ?>"
	}
	//]]>
	</script>
	<base target="_self" />
	
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="ProjectManagerTinyMCE" action="#">
	<div class="tabs">
		<ul>
			<li id="project_tab" class="current"><span><a href="javascript:mcTabs.displayTab('project_tab', 'project_panel');" onmouseover="return false;"><?php _e( 'Project', 'projectmanager' ); ?></a></span></li>
			<li id="dataset_tab"><span><a href="javascript:mcTabs.displayTab('dataset_tab', 'dataset_panel');" onmouseover="return false;"><?php _e( 'Dataset', 'projectmanager' ); ?></a></span></li>
			<li id="search_tab"><span><a href="javascript:mcTabs.displayTab('search_tab', 'search_panel');" onmouseover="return false;"><?php _e('Search Form','projectmanager') ?></a></span></li>
			<li id="datasetform_tab"><span><a href="javascript:mcTabs.displayTab('datasetform_tab', 'datasetform_panel');" onmouseover="return false;"><?php _e('Dataset Form','projectmanager') ?></a></span></li>
			<li id="num_datasets_tab"><span><a href="javascript:mcTabs.displayTab('num_datasets_tab', 'num_datasets_panel');" onmouseover="return false;"><?php _e('Number of datasets','projectmanager') ?></a></span></li>
			<li id="testimonials_tab"><span><a href="javascript:mcTabs.displayTab('testimonials_tab', 'testimonials_panel');" onmouseover="return false;"><?php _e('Testimonials','projectmanager') ?></a></span></li>
		</ul>
	</div>
	<div class="panel_wrapper" style="height: 190px;">
		
	<!-- project panel -->
	<div id="project_panel" class="panel current">
	<table style="border: 0;">
	<tr>
		<td><label for="projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="projects" name="projects" style="width: 200px">
        	<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        </select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="template"><?php _e( 'Template', 'projectmanager' ) ?></label></td>
		<td>
		<?php $templates = array('gallery' => __('Gallery', 'projectmanager'), 'table' => __('Table', 'projectmanager'), 'table-image' => __('Table with Image', 'projectmanager')) ?>
		<select size="1" name="project_template" id="project_template">
		<?php foreach ($templates AS $value => $template_name) : ?>
		<option value="<?php echo $value ?>"><?php echo $template_name ?></option>
		<?php endforeach; ?>
		</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="cat_id"><?php _e( 'Category', 'projectmanager' ) ?></label></td>
		<td><?php wp_dropdown_categories(array( 'hide_empty' => 0, 'name' => 'cat_id', 'orderby' => 'name', 'hierarchical' => true, 'show_option_all' => __('Display all Datasets', 'projectmanager'))); ?></td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="orderby"><?php _e( 'Order By', 'projectmanager' ) ?></label></td>
		<td>
			<select size="1" name="orderby" id="orderby">
				<option value=""><?php _e( 'Default', 'projectmanager') ?></option>
				<option value="name"><?php _e('Name', 'projectmanager') ?></option>
				<option value="id"><?php _e('ID', 'projectmanager') ?></option>
				<option value="formfields"><?php _e( 'Formfields', 'projectmanager') ?></option>
			</select>
			<input type="text" size="3" name="formfield_id" id="formfield_id" />
			<select size="1" name="order" id="order">
				<option value=""><?php _e( 'Default', 'projectmanager') ?></option>
				<option value="asc"><?php _e('Ascending', 'projectmanager') ?></option>
				<option value="desc"><?php _e('Descending', 'projectmanager') ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="selections"><?php _e('Show Selections', 'projectmanager') ?></label></td>
		<td><input type="checkbox" name="selections" value="true" id="selections" checked="checked" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="limit"><?php _e('Limit', 'projectmanager') ?></label></td>
		<td>
			<select size="1" name="limit" id="limit">
				<option value=""><?php _e('Show all datasets', 'projectmanager') ?></option>
				<?php for($i = 1; $i <= 20; $i++) : ?>
				<option value="<?php echo $i ?>"><?php echo $i ?></option>
				<?php endfor; ?>
			</select>
		</td>
	</tr>
	</table>
	</div>
	
	<!-- dataset panel -->
	<div id="dataset_panel" class="panel">
	<table style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="datasets"><?php _e("Dataset", 'projectmanager'); ?></label></td>
		<td>
		<select id="datasets" name="datasets" style="width: 200px">
		<option value="0"><?php _e("", 'projectmanager'); ?></option>
		<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if ($projects) {
				foreach ($projects AS $project) {
					echo '<optgroup label="'.$project->title.'">';
					$datasets = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = '%d' ORDER BY id ASC", $project->id) );
					if( ($datasets) ) {
						foreach( $datasets as $dataset )
							echo '<option value="'.$dataset->id.'" >'.$dataset->name.'</option>'."\n";
					}
					echo '</optgroup>';
				}
			}
		?>
        	</select>
		</td>
	</tr>
	</table>
	</div>
	
	<!-- search panel -->
	<div id="search_panel" class="panel">
	<table style="border: 0;">
	<tr>
		<td><label for="search_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="search_projects" name="search_projects" style="width: 200px">
		<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label><?php _e( 'Display', 'projectmanager' ) ?></label></td>
		<td>
			<input type="radio" name="search_display" id="search_display_extend" value="extend" checked="ckecked" /><label for="search_display_extended"><?php _e( 'Extended Version', 'projectmanager' ) ?></label><br />
			<input type="radio" name="search_display" id="search_display_compact" value="compact" /><label for="search-display_compact"><?php _e( 'Compact Version', 'projectmanager' ) ?></label><br />
		</td>
	</tr>
	</table>
	</div>
	
	<!-- dataset form panel -->
	<div id="datasetform_panel" class="panel">
	<table style="border: 0;">
	<tr>
		<td><label for="datasetform_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="datasetform_projects" name="datasetform_projects" style="width: 200px">
		<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        </select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="datasetform_captcha"><?php _e('Use Captcha', 'projectmanager') ?></label></td>
		<td>
			<input type="checkbox" name="datasetform_captcha" value="true" id="datasetform_captcha" checked="checked" />
			<label for="datasetform_captcha_timeout"><?php _e('Timeout', 'projectmanager') ?></label>
			<input type="text" name="datasetform_captcha_timeout" id="datasetform_captcha_timeout" size="3" value="30" /> <span><?php _e('minutes', 'projectmanager') ?></span>
		</td>
	</tr>
	<tr>
		<td><label for="datasetform_submit_message"><?php _e("Submit Message", 'projectmanager'); ?></label></td>
		<td><input type="text" name="datasetform_submit_message" id="datasetform_submit_message" style="width: 200px" /> (<?php _e('Optional', 'projectmanager') ?>)</td>
	</tr>
	<tr>
		<td><label for="datasetform_submit_title"><?php _e("Button Title", 'projectmanager'); ?></label></td>
		<td><input type="text" name="datasetform_submit_title" id="datasetform_submit_title" style="width: 200px" /> (<?php _e('Optional', 'projectmanager') ?>)</td>
	</tr>
	<tr>
		<td><label for="datasetform_templates"><?php _e("Template", 'projectmanager'); ?></label></td>
		<td><input type="text" name="datasetform_template" id="datasetform_template" style="width: 200px" /></td>
	</tr>
	</table>
	</div>
	
	<!-- num_dataset form panel -->
	<div id="num_datasets_panel" class="panel">
	<table style="border: 0;">
	<tr>
		<td><label for="num_datasets_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="num_datasets_projects" name="num_datasets_projects" style="width: 200px">
		<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        </select>
		</td>
	</tr>
	<tr>
		<td><label for="num_datasets_text"><?php _e("Optional Text", 'projectmanager'); ?></label></td>
		<td><input type="text" size="40" name="num_datasets_text" id="num_datasets_text" /></td>
	</tr>
	</table>
	</div>
	
	<!-- testimonials panel -->
	<div id="testimonials_panel" class="panel">
	<table style="border: 0;">
	<tr>
		<td><label for="testimonials_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select size="1" id="testimonials_projects" name="testimonials_projects" style="width: 200px">
		<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        </select>
		</td>
	</tr>
	<tr>
		<td><label for="testimonials_template"><?php _e('Template', 'projectmanager'); ?></label></td>
		<td>
		<?php $templates = array('intro' => __('Intro', 'projectmanager'), '' => __('List', 'projectmanager')) ?>
		<select size="1" name="testimonials_template" id="testimonials_template">
		<?php foreach ($templates AS $value => $template_name) : ?>
		<option value="<?php echo $value ?>"><?php echo $template_name ?></option>
		<?php endforeach; ?>
		</select>
		</td>
	</tr>
	<tr>
		<td><label for="testimonials_number"><?php _e("Number of datasets", 'projectmanager'); ?></label></td>
		<td>
			<input type="text" size="5" name="testimonials_number" id="testimonials_number" />
			<label for="testimonials_ncol"><?php _e("Number of Columns", 'projectmanager'); ?></label>
			<input type="text" size="5" name="testimonials_ncol" id="testimonials_ncol" />
		</td>
	</tr>
	<tr>
		<td><label for="testimonials_comment_id"><?php _e("Fromfields", 'projectmanager'); ?></label></td>
		<td>
			<select size="1" id="testimonials_comment_id" name="testimonials_comment_id" style="width: 100px">
			<option value=""><?php _e("Comment", 'projectmanager'); ?></option>
			<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project ) {
					echo "<optgroup label='".$project->title."'>";
					$formfields = $wpdb->get_results($wpdb->prepare("SELECT `label`, `id` FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = '%d' ORDER BY `order` ASC", $project->id) );
					if ($formfields) {
						foreach ($formfields AS $formfield)
							echo '<option value="'.$formfield->id.'" >'.$formfield->label.'</option>'."\n";
					}
					echo "</optgroup>";
				}
			}
			?>
			</select>
			<select size="1" id="testimonials_country_id" name="testimonials_country_id" style="width: 100px">
			<option value=""><?php _e("Country", 'projectmanager'); ?></option>
			<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project ) {
					echo "<optgroup label='".$project->title."'>";
					$formfields = $wpdb->get_results($wpdb->prepare("SELECT `label`, `id` FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = '%d' ORDER BY `order` ASC", $project->id) );
					if ($formfields) {
						foreach ($formfields AS $formfield)
							echo '<option value="'.$formfield->id.'" >'.$formfield->label.'</option>'."\n";
					}
					echo "</optgroup>";
				}
			}
			?>
			</select>
			<select size="1" id="testimonials_city_id" name="testimonials_city_id" style="width: 100px">
			<option value=""><?php _e("City", 'projectmanager'); ?></option>
			<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project ) {
					echo "<optgroup label='".$project->title."'>";
					$formfields = $wpdb->get_results($wpdb->prepare("SELECT `label`, `id` FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = '%d' ORDER BY `order` ASC", $project->id) );
					if ($formfields) {
						foreach ($formfields AS $formfield)
							echo '<option value="'.$formfield->id.'" >'.$formfield->label.'</option>'."\n";
					}
					echo "</optgroup>";
				}
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="testimonials_sign_page_id"><?php _e("Signing Page ID", 'projectmanager'); ?></label></td>
		<td>
			<select size="1" id="testimonials_sign_page_id" name="testimonials_sign_page_id" style="width: 100px;">
				<option value=""></option>
			<?php if ($pages = get_pages()) : ?>
			<?php foreach ($pages AS $page) : ?>
				<option value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
			</select>
			<span><?php _e('or', 'projectmanager') ?></span>
			<span><input type="text" size="20" name="testimonials_sign_page_id_text" id="testimonials_sign_page_id_text" placeholder="<?php _e('Anker on same page', 'projectmanager') ?>" /></span>
			<span>(<?php _e('Optional', 'projectmanager') ?>)</span>
			<!--<input type="text" size="10" placeholder="<?php _e('Optional', 'projectmanager') ?>" name="testimonials_sign_page_id" id="testimonials_sign_page_id" />-->
		</td>
	</tr>
	<tr>
		<td><label for="testimonials_list_page_id"><?php _e("Supporter Page ID", 'projectmanager'); ?></label></td>
		<td>
			<select size="1" id="testimonials_list_page_id" name="testimonials_list_page_id" style="width: 100px;">
				<option value=""></option>
			<?php if ($pages = get_pages()) : ?>
			<?php foreach ($pages AS $page) : ?>
				<option value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
			</select>
			<span>(<?php _e('Optional', 'projectmanager') ?>)</span>
			<!--<input type="text" size="10" placeholder="<?php _e('Optional', 'projectmanager') ?>" name="testimonials_list_page_id" id="testimonials_list_page_id" />-->
		</td>
	</tr>
	<tr>
		<td><label for="testimonials_selections"><?php _e('Show Selections', 'projectmanager') ?></label></td>
		<td><input type="checkbox" name="testimonials_selections" value="true" id="testimonials_selections" checked="checked" /></td>
	</tr>
	</table>
	</div>
	
	</div>
	
	<br style="clear: both;" />
	<div class="mceActionPanel" style="margin-top: 0.5em;">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'projectmanager'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'projectmanager'); ?>" onclick="ProjectManagerInsertLink();" />
		</div>
	</div>
</form>
</body>
</html>
