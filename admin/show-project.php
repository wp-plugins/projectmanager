<?php
if ( !current_user_can( 'view_projects' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';

else :
global $current_user;
$project_id = $projectmanager->getProjectID();
$project = $projectmanager->getCurrentProject();

if ( isset($_POST['updateProjectManager']) AND !isset($_POST['doaction']) ) {
	/*
	* Add or Edit Dataset
	*/
	if ( 'dataset' == $_POST['updateProjectManager'] ) {
		check_admin_referer( 'projectmanager_edit-dataset' );
		if ( '' == $_POST['dataset_id'] ) {
			$user_id = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : false;
			$this->addDataset( intval($_POST['project_id']), htmlspecialchars($_POST['name']), $_POST['post_category'], $_POST['form_field'], $user_id );
		} else {
			$dataset_owner = isset($_POST['owner']) ? intval($_POST['owner']) : false;
			$del_image = isset( $_POST['del_old_image'] ) ? true : false;
			$overwrite_image = ( isset($_POST['overwrite_image']) && 1 == $_POST['overwrite_image'] ) ? true: false;
			$this->editDataset( intval($_POST['project_id']), htmlspecialchars($_POST['name']), $_POST['post_category'], intval($_POST['dataset_id']), $_POST['form_field'], intval($_POST['user_id']), $del_image, $_POST['image_file'], $overwrite_image, $dataset_owner );
		}
	}
	$this->printMessage();
}  elseif ( isset($_POST['doaction']) && isset($_POST['action']) ) {
	check_admin_referer('projectmanager_dataset-bulk');
	if ( 'delete' == $_POST['action'] ) {
		global $current_user;
		if ( !current_user_can('delete_datasets') || ( !current_user_can('delete_other_datasets') && $dataset->user_id != $current_user->ID ) ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			$this->printMessage();
		} else {
			foreach ( $_POST['dataset'] AS $dataset_id ) {
				$this->delDataset( intval($dataset_id) );
			}
		}
	} elseif ( 'duplicate' == $_POST['action'] ) {
		global $current_user;
		if ( !current_user_can('edit_datasets') && !current_user_can( 'projectmanager_user') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			$this->printMessage();
		} else {
			foreach ( $_POST['dataset'] AS $dataset_id ) {
				$this->duplicateDataset( intval($dataset_id) );
			}
		}
  }
}
$orderby = array( '' => __('Order By', 'projectmanager'), 'name' => __('Name','projectmanager'), 'id' => __('ID','projectmanager') );
foreach ( $projectmanager->getFormFields() AS $form_field ) {
	$orderby['formfields_'.$form_field->id] = $form_field->label;
}
	
$order = array( '' => __('Order','projectmanager'), 'ASC' => __('Ascending','projectmanager'), 'DESC' => __('Descending','projectmanager') );

if ( $projectmanager->isSearch() )
	$datasets = $projectmanager->getSearchResults();
else
	$datasets = $projectmanager->getDatasets(array('limit' => true));

?>
<div class="wrap">
	<?php $this->printBreadcrumb( $projectmanager->getProjectTitle(), true ) ?>
	
	<h2><?php echo $projectmanager->getProjectTitle() ?> <?php if ($projectmanager->isCategory()) echo " &#8211; ".$projectmanager->getCatTitle() ?></h2>
	
	<ul class="subsubsub projectmanager-menu">
		<?php foreach ( $this->getMenu() AS $key => $item ) : ?>
		<?php if ( current_user_can($item['cap']) ) : ?>

		<?php if ( (isset($project->navi_link) && $project->navi_link != 1) || isset($_GET['subpage']) ) : ?>
		<li><a href="admin.php?page=projectmanager&amp;subpage=<?php echo $key ?>&amp;project_id=<?php echo $project_id ?>"><?php echo $item['title'] ?></a></li> |
		<?php else : ?>
		<li><a href="admin.php?page=<?php printf($item['page'], intval($project_id)) ?>"><?php echo $item['title'] ?></a></li>
		<?php endif; ?>
		<?php endif; ?>
		<?php endforeach; ?>
		<li><a href="edit-tags.php?taxonomy=category"><?php _e( 'Categories' ) ?></a></li>
	</ul>
	
	<p><form class='search-form alignright' action='' method='post'>
		<input type='text' class='search-input' name='search_string' value='<?php echo $projectmanager->getSearchString() ?>' />
		<?php if ( $form_fields = $projectmanager->getFormFields() ) : ?>
		<select size='1' name='search_option'>
			<option value='0' <?php selected(0, $projectmanager->getSearchOption()); ?>><?php _e( 'Name', 'projectmanager' ) ?></option>
			<?php foreach ( $form_fields AS $form_field ) : ?>

			<?php if ( $form_field->type != 'project' ) : ?>
			<option value='<?php echo $form_field->id ?>' <?php selected( $projectmanager->getSearchOption(), $form_field->id ) ?>><?php echo $form_field->label ?></option>
			<?php endif; ?>

			<?php endforeach; ?>
			<option value='-1' <?php selected( -1, $projectmanager->getSearchOption() ) ?>><?php _e( 'Categories', 'projectmanager' ) ?></option>
		</select>
		<?php else : ?>
		<input type='hidden' name='form_field' value='0' />
		<?php endif; ?>
		<input type='submit' value='<?php _e( 'Search', 'projectmanager' ) ?>' class='button-secondary action' />
	</form></p>
	
	<?php if ( $datasets ) : ?>
	
	<form id="dataset-filter" method="post" action="" name="form">
	<?php wp_nonce_field( 'projectmanager_dataset-bulk' ) ?>
	<div class="tablenav">
		<div class="alignleft actions">
			<!-- Bulk Actions -->
			<select name="action" size="1">
				<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
				<option value="delete"><?php _e('Delete')?></option>
				<option value="duplicate"><?php _e( 'Duplicate', 'projectmanager' ) ?></option>
			</select>
			<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
		</div>
		<div class="alignleft actions">
			<?php if ( -1 != $project->category ) : ?>
			<!-- Category Filter -->
			<?php wp_dropdown_categories(array('echo' => 1, 'hide_empty' => 0, 'hide_if_empty' => 1, 'name' => 'cat_id', 'orderby' => 'name', 'selected' => $projectmanager->getCatID(), 'hierarchical' => true, 'child_of' => $project->category, 'show_option_all' => __('View all categories'))); ?>
			<input type='hidden' name='page' value='<?php echo htmlspecialchars($_GET['page']) ?>' />
			<input type='hidden' name='project_id' value='<?php echo $project_id ?>' />
			<?php endif; ?>
			<select size='1' name='orderby'>
			<?php foreach ( $orderby AS $key => $value ) : ?>
				<?php $orderby_request = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : ''; ?>
				<option value='<?php echo $key ?>' <?php selected( $orderby_request, $key ) ?>><?php echo $value ?></option>
			<?php endforeach ?>
			</select>
			<select size='1' name='order'>
			<?php foreach ( $order AS $key => $value ) : ?>
				<?php $order_request = isset($_REQUEST['order']) ? $_REQUEST['order'] : ''; ?>
				<option value='<?php echo $key ?>' <?php selected ($order_request, $key) ?>><?php echo $value ?></option>
			<?php endforeach; ?>
			</select>
			<input type='submit' value='<?php _e( 'Apply' ) ?>' class='button' />
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

	<table class="widefat" id="datasets">
	<thead>
		<tr>
			<th scope="col" class="check-column"><input type="checkbox" onclick="ProjectManager.checkAll(document.getElementById('dataset-filter'));" /></th>
			<th scope="col"><?php _e( 'ID', 'leaguemanager' ) ?></th>
			<th scope="col" class="name"><?php _e( 'Name', 'projectmanager' ) ?></th>
			<?php if ( -1 != $project->category ) : ?>
			<th scope="col" class="categories"><?php _e( 'Categories', 'projectmanager' ) ?></th>
			<?php endif; ?>
			<?php $projectmanager->printTableHeader() ?>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope="col" class="check-column"><input type="checkbox" onclick="ProjectManager.checkAll(document.getElementById('dataset-filter'));" /></th>
			<th scope="col"><?php _e( 'ID', 'leaguemanager' ) ?></th>
			<th scope="col" class="name"><?php _e( 'Name', 'projectmanager' ) ?></th>
			<?php if ( -1 != $project->category ) : ?>
			<th scope="col" class="categories"><?php _e( 'Categories', 'projectmanager' ) ?></th>
			<?php endif; ?>
			<?php $projectmanager->printTableHeader() ?>
		</tr>
	</tfoot>
	<tbody id="the-list">
<?php
	foreach ( $datasets AS $dataset ) :
		$class = ( !isset($class) || 'alternate' == $class ) ? '' : 'alternate';
		if ( count($projectmanager->getSelectedCategoryIDs($dataset)) > 0 )
			$categories = $projectmanager->getSelectedCategoryTitles( $projectmanager->getSelectedCategoryIDs($dataset) );
		else
			$categories = __( 'None', 'projectmanager' );
				
		$dataset->name = htmlspecialchars(stripslashes($dataset->name), ENT_QUOTES);
?>
		<tr class="<?php echo $class ?>" id="dataset_<?php echo $dataset->id ?>">
			<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $dataset->id ?>" name="dataset[<?php echo $dataset->id ?>]" /></th>
			<td><?php echo $dataset->id ?></td>
			<td>
				<?php if ( ( current_user_can('edit_datasets') && $current_user->ID == $dataset->user_id ) || ( current_user_can('edit_other_datasets') ) ) : ?>
					<a href="admin.php?page=<?php if($_GET['page'] == 'projectmanager') echo 'projectmanager&subpage=dataset'; else echo 'project-dataset_'.$project_id ?>&amp;edit=<?php echo $dataset->id ?>&amp;project_id=<?php echo $project_id ?>"><?php echo $dataset->name ?></a>
				<?php else : ?>
					<?php echo $dataset->name ?>
				<?php endif; ?>
			</td>
			<?php if ( -1 != $project->category ) : ?>
			<td><?php echo $categories ?></td>
			<?php endif; ?>
			<?php $projectmanager->printDatasetMetaData( $dataset ) ?>
		</tr>
	<?php endforeach ?>
	</tbody>
	</table>
	</form>
		
	<?php else  : ?>
		<div class="error aligncenter" style="clear: both; margin-top: 3em; text-align: center;"><p><?php _e( 'Nothing found', 'projectmanager') ?></p></div>
	<?php endif ?>
	<div class="tablenav">
		<?php if ( $projectmanager->getPageLinks() ) echo "<div class='tablenav-pages'>$page_links_text</div>"; ?>
	</div>
</div>
<?php endif; ?>
