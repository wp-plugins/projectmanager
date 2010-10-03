<?php
if ( !current_user_can( 'view_projects' ) ) : 
     echo '<div class="error"><p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p></div>';
else :
?>
<div class="wrap">
	<h2 id="top"><?php _e( 'Projectmanager Documentation', 'projectmanager' ) ?></h2>
	
	<h3><?php _e( 'Content', 'projectmanager') ?></h3>
	<ul>
	 <li><a href="#shortcodes"><?php _e( 'Shortcodes', 'projectmanager' ) ?></a></li>
	 <li><a href="#templates"><?php _e( 'Templates', 'projectmanager' ) ?></a></li>
	 <li><a href="#access"><?php _e( 'Access Control', 'projectmanager' ) ?></a></li>
	 <li><a href="#customization"><?php _e( 'Customization', 'projectmanager' ) ?></a></li>
	</ul>
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
	<h3 id="shortcodes"><?php _e( 'Shortcodes', 'projectmanager' ) ?></h3>

	<!-- Shortcode to display all datasets from one project -->
 	<p><?php _e( 'The main shortcode is to display datasets of a project. The following shows a minimal example. A complete list of arguments is described in the table below.', 'projectmanager' ) ?></p>
	<blockquote><p>[project id=ID template=X]</p></blockquote>

	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Parameter', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Possible Values', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Default', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Optional', 'projectmanager' ) ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="" valign="top">
			<td>id</td>
			<td><?php _e( 'ID of Project', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'No', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>template</td>
			<td><?php _e( 'Template file used to display', 'projectmanager' ) ?></td>
			<td><?php _e( '<em>table</em>, <em>gallery</em> or template file without extension', 'projectmanager' ) ?></td>
			<td>table</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="" valign="top">
			<td>cat_id</td>
			<td><?php _e( 'Set this attribute to only display datasets of given category ID', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>orderby</td>
			<td><?php _e( 'order datasets by given field', 'projectmanager' ) ?></td>
			<td><?php _e( '<em>name</em>, <em>id</em>, <em>formfields-ID</em> (replace ID with respective ID or <em>rand</em>. <em>rand</em> must be used together with the results attribute to limit the number of datastes.', 'projectmanager' ) ?></td>
			<td>name</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="" valign="top">
			<td>order</td>
			<td><?php _e( 'Ordering of datasets', 'projectmanager' ) ?></td>
			<td><em>ASC</em>, <em>DESC</em></td>
			<td>ASC</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>single</td>
			<td><?php _e( 'Toggle link to single dataset', 'projectmanager' ) ?></td>
			<td><?php _e( '<em>true</em>, <em>false</em>', 'projectmanager' ) ?></td>
			<td>true</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="" valign="top">
			<td>selections</td>
			<td><?php _e( 'Toggle display of selection forms for categories and dataset ordering', 'projectmanager' ) ?></td>
			<td><em>true</em>, <em>false</em></td>
			<td>true</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>results</td>
			<td><?php _e( 'Limit number of datasets', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="" valign="top">
			<td>field_id</td>
			<td><?php _e( 'Filter datasets for formfield ID. Must be used together with <em>field_value</em>.', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>field_value</td>
			<td><?php _e( 'Filter datasets for formfield value. Must be used together with <em>field_id</em>.', 'projectmanager' ) ?></td>
			<td><em>string</em></td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>

	<!-- Shortcode to display individual dataset -->
	<p><?php _e( 'A single dataset can be displayed with the following code', 'projectmanager' ) ?></p>
	<blockquote><p>[dataset id=X]</p></blockquote>
	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Parameter', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Possible Values', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Default', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Optional', 'projectmanager' ) ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="" valign="top">
			<td>id</td>
			<td><?php _e( 'ID of Project', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'No', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>template</td>
			<td><?php _e( 'Template to use', 'projectmanager' ) ?></td>
			<td>file name without extension</td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>

	<!-- Shortcode to show search form -->
	<p><?php _e( 'A simple search form can be displayed with the following code', 'projectmanager' ) ?></p>
	<blockquote><p>[project_search project_id=ID template=X]</p></blockquote>
	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Parameter', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Possible Values', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Default', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Optional', 'projectmanager' ) ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="" valign="top">
			<td>project_id</td>
			<td><?php _e( 'ID of Project', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'No', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>template</td>
			<td><?php _e( 'Template to use', 'projectmanager' ) ?></td>
			<td><em>compact</em>, <em>extend</em></td>
			<td>extend</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>

	<!-- Shortcode to include dataset input form -->
	<p><?php _e( 'It is possible to include the dataset input form with the following code', 'projectmanager' ) ?></p>
	<blockquote><p>[dataset_form project_id=ID]</p></blockquote>
	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Parameter', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Possible Values', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Default', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Optional', 'projectmanager' ) ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="" valign="top">
			<td>project_id</td>
			<td><?php _e( 'ID of Project', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'No', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>

	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  	<h3 id="templates"><?php _e( 'Templates', 'projectmanager' ) ?></h3>
    <p><?php _e( 'Templates are special files that are used to display plugin data in the website frontend. They reside in the following directory', 'projectmanager' ) ?></p>
	<blockquote><p>WP_PLUGIN_DIR/projectmanager/view/</p></blockquote>
	<p><?php _e( 'The following table lists all available default templates', 'projectmanager' ) ?></p>
	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Template', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
		</tr>
	</thead>
		<tbody>
		<tr class="" valign="top">
			<td>table.php</td>
			<td><?php _e( 'Tabular display of datasets', 'projectmanager' ) ?></td>

		</tr>
		<tr class="alternate" valign="top">
			<td>gallery.php</td>
			<td><?php _e( 'Show datasets as photo gallery', 'projectmanager' ) ?></td>

		</tr>
		<tr class="" valign="top">
			<td>table-image.php</td>
			<td><?php _e( 'Tabular display of datasets with small image', 'projectmanager' ) ?></td>

		</tr>
		<tr class="alternate" valign="top">
			<td>selections.php</td>
			<td><?php _e( 'Display dropdown selections for categories and dataset sorting', 'projectmanager' ) ?></td>

		</tr>
		<tr class="" valign="top">
			<td>dataset-form.php</td>
			<td><?php _e( 'Dataset input formular', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>search-extend.php</td>
			<td><?php _e( 'Full Search formular', 'projectmanager' ) ?></td>

		</tr>
		<tr class="" valign="top">
			<td>search-compact.php</td>
			<td><?php _e( 'Compact Search formular', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>
	
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
 	<h3 id="access"><?php _e( 'Access Control', 'projectmanager' ) ?></h3>

	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  	<h3 id="customization"><?php _e( 'Customization', 'projectmanager' ) ?></h3>

</div>
<?php endif; ?>
