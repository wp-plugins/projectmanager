<?php
if ( !current_user_can( 'manage_projects' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';

else :

$project_id = $_GET['id'];
$projectmanager->setSettings( $project_id );
$options = get_option( 'projectmanager' );
if ( isset($_POST['updateProjectManager']) AND !isset($_POST['deleteit']) ) {
	if ( 'dataset' == $_POST['updateProjectManager'] ) {
		check_admin_referer( 'projectmanager_edit-dataset' );
		if ( '' == $_POST['dataset_id'] ) {
			$message = $projectmanager->addDataset( $_POST['project_id'], $_POST['name'], $_POST['grp_id'], $_POST['form_field'] );
		} else {
			$del_image = isset( $_POST['del_old_image'] ) ? true : false;
			$overwrite_image = isset( $_POST['overwrite_image'] ) ? true: false;
			$message = $projectmanager->editDataset( $_POST['project_id'], $_POST['name'], $_POST['grp_id'], $_POST['dataset_id'], $_POST['form_field'], $del_image, $_POST['image_file'], $overwrite_image );
		}
			
	}
	echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';
} elseif ( isset($_POST['deleteit']) AND isset($_POST['delete']) ) {
	if ( 'datasets' == $_POST['item'] ) {
		check_admin_referer('projectmanager_delete-datasets');
		foreach ( $_POST['delete'] AS $dataset_id )
			$projectmanager->delDataset( $dataset_id );
	}
}
$project_title = $projectmanager->getProjectTitle( $project_id );	
?>
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_id, $project_title, true ) ?>
	
	<h2><?php echo $project_title ?></h2>
	
	<div id="projectmanager_navbar">
	<?php $projectmanager->printSearchForm( $project_id, 'right' ); ?>
	<?php echo $projectmanager->getGroupDropdown( $project_id, 'right' ) ?>
	<p>
		<a href="edit.php?page=projectmanager/page/settings.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Settings', 'projectmanager' ) ?></a> &middot;
		<a href="edit.php?page=projectmanager/page/formfields.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Form Fields', 'projectmanager' ) ?></a> &middot;
		<a href="edit.php?page=projectmanager/page/dataset.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Add Dataset', 'projectmanager' ) ?></a>
	</p>
	</div>
	
	<form id="dataset-filter" method="post" action="">
		
	<?php wp_nonce_field( 'projectmanager_delete-datasets' ) ?>
	<input type="hidden" name="item" value="datasets" />
	<div class="tablenav" style="margin-bottom: 0.1em;">
		<p class="num_datasets"><?php printf(__ngettext('%d Dataset', '%d Datasets', $projectmanager->getNumDatasets(), 'projectmanager'),$projectmanager->getNumDatasets()) ?></p>
		<input type="submit" name="deleteit" value="<?php _e( 'Delete','projectmanager' ) ?>" class="button-secondary" />
	</div>
	
	<table class="widefat">
		<thead>
		<tr>
			<th scope="col" class="check-column"><input type="checkbox" onclick="ProjectManager.checkAll(document.getElementById('dataset-filter'));" /></th>
			<th scope="col"><?php _e( 'Name', 'projectmanager' ) ?></th>
			<?php if ( '' != $options[$project_id]['category'] ) : ?>
			<th scope="col"><?php _e( 'Group', 'projectmanager' ) ?></th>
			<?php endif; ?>
			<?php $projectmanager->printTableHeader() ?>
		</tr>
		</thead>
		<tbody id="the-list">
		<?php
		if ( $projectmanager->isSearch() )
			$dataset_list = $projectmanager->getSearchResults( $projectmanager->getSearchString(), $_POST['form_field'] );
		else
			$dataset_list = $projectmanager->getDataset( false, 'name ASC', true );
		
		if ( $dataset_list ) :
			foreach ( $dataset_list AS $dataset ) :
				$class = ( 'alternate' == $class ) ? '' : 'alternate';
				if ( -1 != $dataset->grp_id )
					$group = $projectmanager->getGroupTitle( $dataset->grp_id );
				else
					$group = __( 'None', 'projectmanager' );
			?>
				<tr class="<?php echo $class ?>">
					<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $dataset->id ?>" name="delete[<?php echo $dataset->id ?>]" /></th>
					<td>
						<!-- Popup Window for Ajax name editing -->
						<div id="datasetnamewrap<?php echo $dataset->id; ?>" style="width:250px;height:80px;overflow:auto;display:none;">
							<div id="datasetnamebox<?php echo $dataset->id; ?>" class='projectmanager_thickbox'>
								<form><input type='text' name='dataset_name<?php echo $dataset_id ?>' id='dataset_name<?php echo $dataset->id ?>' value='<?php echo $dataset->name ?>' size='30' />
								<div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.ajaxSaveDatasetName(<?php echo $dataset->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div></form>
							</div>
						</div>
						<a href="edit.php?page=projectmanager/page/dataset.php&amp;edit=<?php echo $dataset->id ?>&amp;project_id=<?php echo $project_id ?>"><span id="dataset_name_text<?php echo $dataset->id ?>"><?php echo $dataset->name ?></span></a>&#160;<a class="thickbox" id="thickboxlink_name<?php echo $dataset->id ?>" href="#TB_inline?height=100&amp;width=250&amp;inlineId=datasetnamewrap<?php echo $dataset->id ?>" title="<?php _e('Name','projectmanager') ?>"><img src="<?php echo PROJECTMANAGER_URL ?>/images/edit.gif" border="0" alt="<?php _e('Edit') ?>" /></a>
					</td>
					<?php if ( '' != $options[$project_id]['category'] ) : ?>
					<td>
						<!-- Popup Window for Ajax group editing -->
						<div id="groupchoosewrap<?php echo $dataset->id; ?>" style="width:250px;height:80px;overflow:auto;display:none;">
							<div id="groupchoose<?php echo $dataset->id; ?>" class='projectmanager_thickbox'>
								<form><?php wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'grp_id'.$dataset->id, 'orderby' => 'name', 'selected' => $dataset->grp_id, 'hierarchical' => true, 'child_of' => $options[$project_id]['category'], 'show_option_none' => __('None'))); ?>
								<div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.ajaxSaveGroup(<?php echo $dataset->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div></form>
							</div>
						</div>
						<span id="dataset_group_text<?php echo $dataset->id ?>"><?php echo $group ?></span>&#160;<a class="thickbox" id="thickboxlink_group<?php echo $dataset->id ?>" href="#TB_inline?height=100&amp;width=250&amp;inlineId=groupchoosewrap<?php echo $dataset->id ?>" title="<?php printf(__('Group of %s','projectmanager'),$dataset->name) ?>"><img src="<?php echo PROJECTMANAGER_URL ?>/images/edit.gif" border="0" alt="<?php _e('Edit') ?>" /></a>
					</td>
					<?php endif; ?>
					<?php $projectmanager->printDatasetMetaData( $dataset->id, 'td', false, $dataset->name ) ?>
				</tr>
			<?php endforeach ?>
		<?php endif ?>
		</tbody>
	</table>
	</form>
	<?php if ( !$projectmanager->isSearch() ) echo $projectmanager->pagination->get() ?>
</div>
<?php endif; ?>