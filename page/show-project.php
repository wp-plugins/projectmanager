<?php
if ( !current_user_can( 'manage_projects' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';

else :

$project_id = $projectmanager->getProjectID();
$options = get_option( 'projectmanager' );
if ( isset($_POST['updateProjectManager']) AND !isset($_POST['deleteit']) ) {
	if ( 'dataset' == $_POST['updateProjectManager'] ) {
		check_admin_referer( 'projectmanager_edit-dataset' );
		if ( '' == $_POST['dataset_id'] ) {
			$message = $projectmanager->addDataset( $_POST['project_id'], $_POST['name'], $_POST['post_category'], $_POST['form_field'] );
		} else {
			$del_image = isset( $_POST['del_old_image'] ) ? true : false;
			$overwrite_image = isset( $_POST['overwrite_image'] ) ? true: false;
			$message = $projectmanager->editDataset( $_POST['project_id'], $_POST['name'], $_POST['post_category'], $_POST['dataset_id'], $_POST['form_field'], $del_image, $_POST['image_file'], $overwrite_image );
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
$project_title = $projectmanager->getProjectTitle( );
	
if ( $projectmanager->isSearch() )
	$datasets = $projectmanager->getSearchResults();
else
	$datasets = $projectmanager->getDatasets( true );

$num_datasets = ( $projectmanager->isSearch() ) ? count($datasets) : $projectmanager->getNumDatasets($project_id);
?>
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_title, true ) ?>
	
	<h2><?php echo $project_title ?></h2>
	
	<div id="projectmanager_navbar">
	<?php $projectmanager->printSearchForm( $project_id, 'right' ); ?>
	<?php $projectmanager->printCategoryDropdown( $project_id, 'right' ) ?>
	<p style="clear: both;">
		<a href="edit.php?page=projectmanager/page/settings.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Settings', 'projectmanager' ) ?></a> &middot;
		<a href="edit.php?page=projectmanager/page/formfields.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Form Fields', 'projectmanager' ) ?></a> &middot;
		<a href="edit.php?page=projectmanager/page/dataset.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Add Dataset', 'projectmanager' ) ?></a>
	</p>
	</div>
	
	<?php if ( $datasets ) : ?>
	<form id="dataset-filter" method="post" action="">
		
	<?php wp_nonce_field( 'projectmanager_delete-datasets' ) ?>
	<input type="hidden" name="item" value="datasets" />
	<div class="tablenav" style="margin-bottom: 0.1em;">
		<p class="num_datasets"><?php printf(__('%d of %d Datasets', 'projectmanager'),$num_datasets, $projectmanager->getNumDatasets($project_id, true) ) ?></p>
		<input type="submit" name="deleteit" value="<?php _e( 'Delete','projectmanager' ) ?>" class="button-secondary" />
	</div>
	
	<table class="widefat">
		<thead>
		<tr>
			<th scope="col" class="check-column"><input type="checkbox" onclick="ProjectManager.checkAll(document.getElementById('dataset-filter'));" /></th>
			<th scope="col"><?php _e( 'Name', 'projectmanager' ) ?></th>
			<?php if ( -1 != $options[$project_id]['category'] ) : ?>
			<th scope="col"><?php _e( 'Categories', 'projectmanager' ) ?></th>
			<?php endif; ?>
			<?php $projectmanager->printTableHeader() ?>
		</tr>
		</thead>
		<tbody id="the-list">
		<?php
		foreach ( $datasets AS $dataset ) :
			$class = ( 'alternate' == $class ) ? '' : 'alternate';
			if ( count($projectmanager->getSelectedCategoryIDs($dataset)) > 0 )
				$categories = $projectmanager->getSelectedCategoryTitles( $projectmanager->getSelectedCategoryIDs($dataset) );
			else
				$categories = __( 'None', 'projectmanager' );
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
				<?php if ( -1 != $options[$project_id]['category'] ) : ?>
				<td>
					<!-- Popup Window for Ajax group editing -->
					<div id="groupchoosewrap<?php echo $dataset->id; ?>" style="width:250px;height:80px;overflow:auto;display:none;">
						<div id="groupchoose<?php echo $dataset->id; ?>" class='projectmanager_thickbox'>
							<form>
								<ul class="categorychecklist" id="categorychecklist<?php echo $dataset->id ?>">
								<?php $projectmanager->categoryChecklist( $options[$project_id]['category'], $projectmanager->getSelectedCategoryIDs(&$dataset) ) ?>
								</ul>
								<div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.ajaxSaveCategories(<?php echo $dataset->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div>
							</form>
						</div>
					</div>
					<span id="dataset_category_text<?php echo $dataset->id ?>"><?php echo $categories ?></span>&#160;<a class="thickbox" id="thickboxlink_category<?php echo $dataset->id ?>" href="#TB_inline?height=100&amp;width=250&amp;inlineId=groupchoosewrap<?php echo $dataset->id ?>" title="<?php printf(__('Categories of %s','projectmanager'),$dataset->name) ?>"><img src="<?php echo PROJECTMANAGER_URL ?>/images/edit.gif" border="0" alt="<?php _e('Edit') ?>" /></a>
				</td>
				<?php endif; ?>
				<?php $projectmanager->printDatasetMetaData( $dataset, 'td', false ) ?>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
	</form>
	<?php else : ?>
		<div class="error" style="margin-top: 3em;"><p><?php _e( 'Nothing found', 'projectmanager') ?></p></div>
	<?php endif ?>
	
	<?php if ( !$projectmanager->isSearch() ) echo $projectmanager->pagination->get() ?>
</div>
<?php endif; ?>
