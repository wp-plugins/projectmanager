<div class='wrap'>
	<h2><?php _e( 'Global Settings', 'projectmanager' ) ?></h2>
	<h3><?php _e( 'Color Scheme', 'projectmanager' ) ?></h3>

	<form action='' method='post' name='colors'>
	<?php wp_nonce_field( 'projetmanager_manage-global-league-options' ); ?>	
	<table class='form-table'>
	<tr valign='top'>
		<th scope='row'><label for='color_headers'><?php _e( 'Table Headers', 'projectmanager' ) ?></label></th><td><input type='text' name='color_headers' id='color_headers' value='<?php echo $options['colors']['headers'] ?>' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms["colors"].color_headers,"pick_color_headers"); return false;' name='pick_color_headers' id='pick_color_headers'>&#160;&#160;&#160;</a></td>
	</tr>
	<tr valign='top'>
		<th scope='row'><label for='color_rows'><?php _e( 'Table Rows', 'projectmanager' ) ?></label></th>
		<td>
			<p class='table_rows'><input type='text' name='color_rows_alt' id='color_rows_alt' value='<?php echo $options['colors']['rows'][0] ?>' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms["colors"].color_rows_alt,"pick_color_rows_alt"); return false;' name='pick_color_rows_alt' id='pick_color_rows_alt'>&#160;&#160;&#160;</a></p>
			<p class='table_rows'><input type='text' name='color_rows' id='color_rows' value='<?php echo $options['colors']['rows'][1] ?>' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms["colors"].color_rows,"pick_color_rows"); return false;' name='pick_color_rows' id='pick_color_rows'>&#160;&#160;&#160;</a></p>
		</td>
	</tr>
	</table>
	
	<h3><?php _e('Dashboard Widget Support News', 'projectmanager') ?></h3>
	<table class='form-table'>
	<tr valign='top'>
		<th scope='row'><label for='dashboard_num_items'><?php _e( 'Number of Support Threads', 'projectmanager' ) ?></label></th><td><input type='text' name='dashboard[num_items]' id='dashboard_num_items' value='<?php echo $options['dashboard_widget']['num_items'] ?>' size='2' /></td>
	</tr>
	<tr valign='top'>
		<th scope='row'><label for='dashboard_show_author'><?php _e( 'Show Author', 'projectmanager' ) ?></label></th><td><input type='checkbox' name='dashboard[show_author]' id='dashboard_show_author'<?php checked($options['dashboard_widget']['show_author'], 1) ?> /></td>
	</tr>
	<tr valign='top'>
		<th scope='row'><label for='dashboard_show_date'><?php _e( 'Show Date', 'projectmanager' ) ?></label></th><td><input type='checkbox' name='dashboard[show_date]' id='dashboard_show_date'<?php checked($options['dashboard_widget']['show_date'], 1) ?> /></td>
	</tr>
		<tr valign='top'>
		<th scope='row'><label for='dashboard_show_summary'><?php _e( 'Show Summary', 'projectmanager' ) ?></label></th><td><input type='checkbox' name='dashboard[show_summary]' id='dashboard_show_summary'<?php checked($options['dashboard_widget']['show_summary'], 1) ?> /></td>
	</tr>
	</table>
	
	<input type='hidden' name='page_options' value='color_headers,color_rows,color_rows_alt' />
	<p class='submit'><input type='submit' name='updateProjectManager' value='<?php _e( 'Save Preferences', 'projectmanager' ) ?> &raquo;' class='button-primary' /></p>
  </form>

</div>
	
<script language='javascript'>
	syncColor("pick_color_headers", "color_headers", document.getElementById("color_headers").value);
	syncColor("pick_color_rows", "color_rows", document.getElementById("color_rows").value);
	syncColor("pick_color_rows_alt", "color_rows_alt", document.getElementById("color_rows_alt").value);
</script>