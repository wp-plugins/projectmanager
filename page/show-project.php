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
			$dataset_owner = isset($_POST['owner']) ? $_POST['owner'] : false;
			$del_image = isset( $_POST['del_old_image'] ) ? true : false;
			$overwrite_image = isset( $_POST['overwrite_image'] ) ? true: false;
			$message = $projectmanager->editDataset( $_POST['project_id'], $_POST['name'], $_POST['post_category'], $_POST['dataset_id'], $_POST['form_field'], $_POST['user_id'], $del_image, $_POST['image_file'], $overwrite_image, $dataset_owner );
		}
	}
	if ( $message ) echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';
}  elseif ( isset($_POST['doaction']) && isset($_POST['action']) ) {
	check_admin_referer('projectmanager_dataset-bulk');
	if ( 'delete' == $_POST['action'] ) {
		foreach ( $_POST['dataset'] AS $dataset_id )
			$projectmanager->delDataset( $dataset_id );
	}
} elseif ( isset($_POST['cat_id']) ) {
	$projectmanager->setCatID($_POST['cat_id']);
}

$project_title = $projectmanager->getProjectTitle();
	
if ( $projectmanager->isSearch() )
	$datasets = $projectmanager->getSearchResults();
else
	$datasets = $projectmanager->getDatasets( true );
?>
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_title, true ) ?>
	
	<h2><?php echo $project_title ?> <?php if ($projectmanager->isCategory()) echo " &#8211; ".$projectmanager->getCatTitle() ?></h2>
	
	<form class='search-form' action='' method='post' style="float: right; margin-left: 1em;">
		<input type='text' class='search-input' name='search_string' value='<?php $projectmanager->getSearchString() ?>' />
		<?php if ( $form_fields = $projectmanager->getFormFields() ) : ?>
		<select size='1' name='search_option'>
			<?php $selected[0] = ( 0 == $projectmanager->getSearchOption() ) ? " selected='selected'" : ""; ?>
			<option value='0' <?php echo $selected[0] ?>><?php _e( 'Name', 'projectmanager' ) ?></option>
			<?php foreach ( $form_fields AS $form_field ) : $selected = ( $search_option == $form_field->id ) ? " selected='selected'" : ""; ?>
			<option value='<?php echo $form_field->id ?>' <?php echo $selected ?>><?php echo $form_field->label ?></option>
			<?php endforeach; ?>
			<?php $selected[1] = ( -1 == $search_option ) ? " selected='selected'" : ""; ?>
			<option value='-1' <?php echo $selected[1] ?>><?php _e( 'Categories', 'projectmanager' ) ?></option>
		</select>
		<?php else : ?>
		<input type='hidden' name='form_field' value='0' />
		<?php endif; ?>
		<input type='submit' value='<? _e( 'Search', 'projectmanager' ) ?>' class='button-secondary action' />
	</form>
	
	<ul class="subsubsub">
		<li><a href="edit.php?page=projectmanager/page/settings.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Settings', 'projectmanager' ) ?></a></li> |
		<li><a href="edit.php?page=projectmanager/page/formfields.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Form Fields', 'projectmanager' ) ?></a></li> |
		<li><a href="edit.php?page=projectmanager/page/dataset.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Add Dataset', 'projectmanager' ) ?></a></li> |
		<li><a href="categories.php"><?php _e( 'Categories' ) ?></a></li> |
		<?php if ( current_user_can( 'projectmanager_admin' ) ) : ?>
		<li><a href="admin.php?page=projectmanager/page/import.php&amp;project_id=<?php echo $project_id ?>"><?php _e('Import') ?></a></li>
		<?php endif; ?>
	</ul>
	
	<?php if ( $datasets ) : ?>
	
	<form id="dataset-filter" method="post" action="" name="form">
	<?php wp_nonce_field( 'projectmanager_dataset-bulk' ) ?>
	<div class="tablenav" style="margin-bottom: 0.1em;">
		<div class="alignleft actions">
			<!-- Bulk Actions -->
			<select name="action" size="1">
				<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
				<option value="delete"><?php _e('Delete')?></option>
			</select>
			<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
			
			<!-- Category Filter -->
			<?php wp_dropdown_categories(array('echo' => 1, 'hide_empty' => 0, 'name' => 'cat_id', 'orderby' => 'name', 'selected' => $projectmanager->getCatID(), 'hierarchical' => true, 'child_of' => $options[$project_id]['category'], 'show_option_all' => __('View all categories'))); ?>
			<input type='hidden' name='page' value='<?php echo $_GET['page'] ?>' />
			<input type='hidden' name='project_id' value='<?php echo $project_id ?>' />
			<input type='submit' value='<?php _e( 'Filter' ) ?>' class='button' />
		</div>
		
		<?php if ( $projectmanager->getPageLinks() ) : ?>
		<div class="tablenav-pages">
			<?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'projectmanager' ) . '</span>%s',
			number_format_i18n( ( $projectmanager->getCurrentPage() - 1 ) * $projectmanager->getPerPage() + 1 ),
			number_format_i18n( min( $projectmanager->getCurrentPage() * $projectmanager->getPerPage(),  $projectmanager->getNumDatasets($project_id) ) ),
			number_format_i18n(  $projectmanager->getNumDatasets($project_id) ),
			$projectmanager->getPageLinks()
			); echo $page_links_text; ?>
		</div>
		<?php endif; ?>
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
		<tfoot>
		<tr>
			<th scope="col" class="check-column"><input type="checkbox" onclick="ProjectManager.checkAll(document.getElementById('dataset-filter'));" /></th>
			<th scope="col"><?php _e( 'Name', 'projectmanager' ) ?></th>
			<?php if ( -1 != $options[$project_id]['category'] ) : ?>
			<th scope="col"><?php _e( 'Categories', 'projectmanager' ) ?></th>
			<?php endif; ?>
			<?php $projectmanager->printTableHeader() ?>
		</tr>
		</tfoot>
		
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
				<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $dataset->id ?>" name="dataset[<?php echo $dataset->id ?>]" /></th>
				<td>
					<?php if ( $dataset->user_id == $current_user->ID || current_user_can( 'projectmanager_admin') ) : ?>
					<!-- Popup Window for Ajax name editing -->
					<div id="datasetnamewrap<?php echo $dataset->id; ?>" style="overflow:auto;display:none;">
						<div id="datasetnamebox<?php echo $dataset->id; ?>" class='projectmanager_thickbox'>
							<form><input type='text' name='dataset_name<?php echo $dataset_id ?>' id='dataset_name<?php echo $dataset->id ?>' value='<?php echo $dataset->name ?>' size='30' />
							<div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.ajaxSaveDatasetName(<?php echo $dataset->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div></form>
						</div>
					</div>
					<a href="edit.php?page=projectmanager/page/dataset.php&amp;edit=<?php echo $dataset->id ?>&amp;project_id=<?php echo $project_id ?>"><span id="dataset_name_text<?php echo $dataset->id ?>"><?php echo $dataset->name ?></span></a>&#160;<a class="thickbox" id="thickboxlink_name<?php echo $dataset->id ?>" href="#TB_inline&amp;height=100&amp;width=300&amp;inlineId=datasetnamewrap<?php echo $dataset->id ?>" title="<?php _e('Name','projectmanager') ?>"><img src="<?php echo PROJECTMANAGER_URL ?>/images/edit.gif" border="0" alt="<?php _e('Edit') ?>" /></a>
					<?php else : ?>
						<span><?php echo $dataset->name ?></span>
					<?php endif; ?>
				</td>
				<?php if ( -1 != $options[$project_id]['category'] ) : ?>
				<td>
					<?php if ( $dataset->user_id == $current_user->ID || current_user_can( 'projectmanager_admin') ) : ?>
					<!-- Popup Window for Ajax group editing -->
					<div id="groupchoosewrap<?php echo $dataset->id; ?>" style="overflow:auto;display:none;">
						<div id="groupchoose<?php echo $dataset->id; ?>" class='projectmanager_thickbox'>
							<form>
								<ul class="categorychecklist" id="categorychecklist<?php echo $dataset->id ?>">
								<?php $projectmanager->categoryChecklist( $options[$project_id]['category'], $projectmanager->getSelectedCategoryIDs($dataset) ) ?>
								</ul>
								<div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.ajaxSaveCategories(<?php echo $dataset->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div>
							</form>
						</div>
					</div>
					<span id="dataset_category_text<?php echo $dataset->id ?>"><?php echo $categories ?></span>&#160;<a class="thickbox" id="thickboxlink_category<?php echo $dataset->id ?>" href="#TB_inline&amp;height=300&amp;width=300&amp;inlineId=groupchoosewrap<?php echo $dataset->id ?>" title="<?php printf(__('Categories of %s','projectmanager'),$dataset->name) ?>"><img src="<?php echo PROJECTMANAGER_URL ?>/images/edit.gif" border="0" alt="<?php _e('Edit') ?>" /></a>
					<?php else : ?>
						<span><?php echo $categories ?></span>
					<?php endif; ?>
				</td>
				<?php endif; ?>
				<?php $projectmanager->printDatasetMetaData( $dataset, 'td', false ) ?>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
	</form>
	<?php elseif ( $projectmanager->getNumDatasets($project_id,true) > 0 )  : ?>
		<div class="error" style="margin-top: 3em;"><p><?php _e( 'Nothing found', 'projectmanager') ?></p></div>
	<?php endif ?>
	<div class="tablenav">
		<?php if ( $projectmanager->getPageLinks() ) echo "<div class='tablenav-pages'>$page_links_text</div>"; ?>
	</div>
</div>
<?php endif; ?>
