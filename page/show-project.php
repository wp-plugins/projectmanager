<?php
if ( !current_user_can( 'manage_projects' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';

else :
if ( !isset( $_GET['paged'] ) )
	$_GET['paged'] = 1;
	
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
} elseif ( isset($_GET['_wp_http_referer']) && ! empty($_GET['_wp_http_referer']) ) {
	//wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	//exit;
} elseif ( isset($_POST['deleteit']) AND isset($_POST['delete']) ) {
	if ( 'datasets' == $_POST['item'] ) {
		check_admin_referer('projectmanager_delete-datasets');
		foreach ( $_POST['delete'] AS $dataset_id )
			echo $dateset_id;
			//$projectmanager->delDataset( $dataset_id );
	}
}
$project_title = $projectmanager->getProjectTitle( );
	
if ( $projectmanager->isSearch() )
	$datasets = $projectmanager->getSearchResults();
else
	$datasets = $projectmanager->getDatasets( true );

$num_datasets = ( $projectmanager->isSearch() ) ? count($datasets) : $projectmanager->getNumDatasets($project_id);
$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => $projectmanager->getNumPages(),
	'current' => $_GET['paged']
));
?>
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_title, true ) ?>
	
	<h2><?php echo $project_title ?></h2>
	
	<div id="projectmanager_navbar">
	<?php $projectmanager->printSearchForm( $project_id, 'right' ); ?>
	<ul class="subsubsub">
		<li><a href="edit.php?page=projectmanager/page/settings.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Settings', 'projectmanager' ) ?></a></li> |
		<li><a href="edit.php?page=projectmanager/page/formfields.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Form Fields', 'projectmanager' ) ?></a></li> |
		<li><a href="edit.php?page=projectmanager/page/dataset.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Add Dataset', 'projectmanager' ) ?></a></li>
	</ul>
	</div>
	
	<?php if ( $datasets ) : ?>
	
	<form id="dataset-filter" method="get" action="edit.php" name="form">
		
	<?php wp_nonce_field( 'projectmanager_delete-datasets' ) ?>
	<input type="hidden" name="item" value="datasets" />
	<div class="tablenav" style="margin-bottom: 0.1em;">
		<div class="alignleft actions">
			<select name="action" size="1">
			<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
			<option value="delete"><?php _e('Delete')?></option>
			</select>
			<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
			<!--<form action='edit.php' method='get'>-->
				<?php wp_dropdown_categories(array('echo' => 1, 'hide_empty' => 0, 'name' => 'cat_id', 'orderby' => 'name', 'selected' => $projectmanager->getCatID(), 'hierarchical' => true, 'child_of' => $options[$project_id]['category'], 'show_option_all' => __('View all categories'))); ?>
				<input type='hidden' name='page' value='<?php echo $_GET['page'] ?>' />
				<input type='hidden' name='project_id' value='<?php echo $project_id ?>' />
				<input type='submit' value='<?php _e( 'Filter', 'projectmanager' ) ?>' class='button' />
			</form>
		</div>
		
		<?php if ( $page_links && !$projectmanager->isSearch() ) : ?>
		<div class="tablenav-pages">
			<?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
			number_format_i18n( ( $_GET['paged'] - 1 ) * $projectmanager->getPerPage() + 1 ),
			number_format_i18n( min( $_GET['paged'] * $projectmanager->getPerPage(), $num_datasets ) ),
			number_format_i18n( $num_datasets ),
			$page_links
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
					<?php if ( $dataset->user_id == $current_user->ID || current_user_can( 'projectmanager_admin') ) : ?>
					<!-- Popup Window for Ajax name editing -->
					<div id="datasetnamewrap<?php echo $dataset->id; ?>" style="width:250px;height:80px;overflow:auto;display:none;">
						<div id="datasetnamebox<?php echo $dataset->id; ?>" class='projectmanager_thickbox'>
							<form><input type='text' name='dataset_name<?php echo $dataset_id ?>' id='dataset_name<?php echo $dataset->id ?>' value='<?php echo $dataset->name ?>' size='30' />
							<div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.ajaxSaveDatasetName(<?php echo $dataset->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div></form>
						</div>
					</div>
					<a href="edit.php?page=projectmanager/page/dataset.php&amp;edit=<?php echo $dataset->id ?>&amp;project_id=<?php echo $project_id ?>"><span id="dataset_name_text<?php echo $dataset->id ?>"><?php echo $dataset->name ?></span></a>&#160;<a class="thickbox" id="thickboxlink_name<?php echo $dataset->id ?>" href="#TB_inline?height=100&amp;width=250&amp;inlineId=datasetnamewrap<?php echo $dataset->id ?>" title="<?php _e('Name','projectmanager') ?>"><img src="<?php echo PROJECTMANAGER_URL ?>/images/edit.gif" border="0" alt="<?php _e('Edit') ?>" /></a>
					<?php else : ?>
						<span><?php echo $dataset->name ?></span>
					<?php endif; ?>
				</td>
				<?php if ( -1 != $options[$project_id]['category'] ) : ?>
				<td>
					<?php if ( $dataset->user_id == $current_user->ID || current_user_can( 'projectmanager_admin') ) : ?>
					<!-- Popup Window for Ajax group editing -->
					<div id="groupchoosewrap<?php echo $dataset->id; ?>" style="width:250px;height:80px;overflow:auto;display:none;">
						<div id="groupchoose<?php echo $dataset->id; ?>" class='projectmanager_thickbox'>
							<form>
								<ul class="categorychecklist" id="categorychecklist<?php echo $dataset->id ?>">
								<?php $projectmanager->categoryChecklist( $options[$project_id]['category'], $projectmanager->getSelectedCategoryIDs($dataset) ) ?>
								</ul>
								<div style="text-align:center; margin-top: 1em;"><input type="button" value="<?php _e('Save') ?>" class="button-secondary" onclick="ProjectManager.ajaxSaveCategories(<?php echo $dataset->id; ?>);return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div>
							</form>
						</div>
					</div>
					<span id="dataset_category_text<?php echo $dataset->id ?>"><?php echo $categories ?></span>&#160;<a class="thickbox" id="thickboxlink_category<?php echo $dataset->id ?>" href="#TB_inline?height=100&amp;width=250&amp;inlineId=groupchoosewrap<?php echo $dataset->id ?>" title="<?php printf(__('Categories of %s','projectmanager'),$dataset->name) ?>"><img src="<?php echo PROJECTMANAGER_URL ?>/images/edit.gif" border="0" alt="<?php _e('Edit') ?>" /></a>
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
		<?php if ( $page_links && !$projectmanager->isSearch() ) echo "<div class='tablenav-pages'>$page_links_text</div>"; ?>
	</div>
</div>
<?php endif; ?>
