<?php
$project_id = $_GET['id'];
$projectmanager->setSettings( $project_id );
$options = get_option( 'projectmanager' );
if ( isset($_POST['updateProjectManager']) AND !isset($_POST['deleteit']) ) {
	if ( 'dataset' == $_POST['updateProjectManager'] ) {
		check_admin_referer( 'projectmanager_edit-dataset' );
		if ( '' == $_POST['dataset_id'] ) {
			$return_message = $projectmanager->addDataset( $_POST['project_id'], $_POST['name'], $_POST['grp_id'], $_POST['form_field'] );
		} else {
			$del_image = isset( $_POST['del_old_image'] ) ? true : false;
			$overwrite_image = isset( $_POST['overwrite_image'] ) ? true: false;
			$return_message = $projectmanager->editDataset( $_POST['project_id'], $_POST['name'], $_POST['grp_id'], $_POST['dataset_id'], $_POST['form_field'], $del_image, $_POST['image_file'], $overwrite_image );
		}
			
	}
	echo '<div id="message" class="updated fade"><p><strong>'.__( $return_message, 'projectmanager' ).'</strong></p></div>';
} elseif ( isset($_POST['deleteit']) AND isset($_POST['delete']) ) {
	if ( 'datasets' == $_POST['item'] ) {
		check_admin_referer('projectmanager_delete-datasets');
		foreach ( $_POST['delete'] AS $dataset_id )
			$projectmanager->delDataset( $dataset_id );
	}
}
$projectmanager->setPerPage(20);
$project_title = $projectmanager->getProjectTitle( $project_id );	
?>
<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_id, $project_title ) ?>
	
	<h2><?php echo $project_title ?></h2>
	
	<?php $projectmanager->printSearchForm( $project_id, 'right' ); ?>
	<p>
		<a href="edit.php?page=projectmanager/page/settings.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Settings', 'projectmanager' ) ?></a> &middot;
		<a href="edit.php?page=projectmanager/page/formfields.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Form Fields', 'projectmanager' ) ?></a> &middot;
		<!--<a href="edit.php?page=projectmanager/page/groups.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Groups', 'projectmanager' ) ?></a> &middot;-->
		<a href="edit.php?page=projectmanager/page/dataset.php&amp;project_id=<?php echo $project_id ?>"><?php _e( 'Add Dataset', 'projectmanager' ) ?></a>
	</p>
	
	<form id="dataset-filter" method="post" action="">
		
	<?php wp_nonce_field( 'projectmanager_delete-datasets' ) ?>
	<input type="hidden" name="item" value="datasets" />
	<div class="tablenav" style="margin-bottom: 0.1em;"><input type="submit" name="deleteit" value="<?php _e( 'Delete','projectmanager' ) ?>" class="button-secondary" /></div>			
	
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
		if ( isset($_POST['projectmanager_search']) AND '' != $_POST['projectmanager_search'] )
			$dataset_list = $projectmanager->getSearchResults( $_POST['projectmanager_search'], $_POST['form_field'] );
		else
			$dataset_list = $projectmanager->getDataset( false, 'name ASC', true );
		
		if ( $dataset_list ) :
			foreach ( $dataset_list AS $dataset ) :
				$class = ( 'alternate' == $class ) ? '' : 'alternate';
				if ( -1 != $dataset->grp_id ) {
					$cat = get_category( $dataset->grp_id );
					$group = $cat->name;
				} else
					$group = __( 'None', 'projectmanager' );
			?>
				<tr class="<?php echo $class ?>">
					<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $dataset->id ?>" name="delete[<?php echo $dataset->id ?>]" /></th>
					<td><a href="edit.php?page=projectmanager/page/dataset.php&amp;edit=<?php echo $dataset->id ?>&amp;project_id=<?php echo $project_id ?>"><?php echo $dataset->name ?></a></td>
					<?php if ( '' != $options[$project_id]['category'] ) : ?>
					<td><?php echo $group ?></td>
					<?php endif; ?>
					<?php $projectmanager->printDatasetMetaData( $dataset->id, 'td' ) ?>
				</tr>
			<?php endforeach ?>
		<?php endif ?>
		</tbody>
	</table>
	</form>
	<?php if ( !isset($_POST['projectmanager_search']) ) echo $projectmanager->pagination->get() ?>
</div>
