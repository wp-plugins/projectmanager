<?php
if ( !current_user_can( 'manage_projects' ) ) : 
     echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

if ( isset($_POST['updateProjectManager']) AND !isset($_POST['deleteit']) ) {
	if ( 'project' == $_POST['updateProjectManager'] && check_admin_referer('projectmanager_manage-projects') ) {
		if ( '' == $_POST['project_id'] )
			$return_message = $projectmanager->addProject( $_POST['project_title'] );
		else
			$return_message = $projectmanager->editProject( $_POST['project_title'], $_POST['project_id'] );
	}
	echo '<div id="message" class="updated fade"><p><strong>'.__( $return_message, 'projectmanager' ).'</strong></p></div>';
} elseif ( isset($_POST['deleteit']) AND isset($_POST['delete']) ) {
	if ( 'projects' == $_POST['item'] && check_admin_referer('projectmanager_delete-projects') ) {
		foreach ( $_POST['delete'] AS $project_id )
			$projectmanager->delProject( $project_id );
	}
}
?>
<div class="wrap" style="margin-bottom: 1em;">
	<h2><?php _e( 'Projectmanager', 'projectmanager' ) ?></h2>
	
	<form id="projects-filter" method="post" action="">
		
		<?php wp_nonce_field( 'projectmanager_delete-projects' ) ?>
		
		<input type="hidden" name="item" value="projects" />
		<div class="tablenav" style="margin-bottom: 0.1em;"><input type="submit" name="deleteit" value="<?php _e( 'Delete','projectmanager' ) ?>" class="button-secondary" /></div>
		
		<table class="widefat" summary="" title="<?php _e( 'Projectmanager', 'projectmanager' ) ?>">
		<thead>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" onclick="ProjectManager.checkAll(document.getElementById('projects-filter'));" /></th>
				<th scope="col" class="num">ID</th>
				<th scope="col"><?php _e( 'Project', 'projectmanager' ) ?></th>
				<th scope="col" class="num"><?php _e( 'Number of Datasets', 'projectmanager' ) ?></th>
				<th scope="col"><?php _e( 'Action', 'projectmanager' ) ?></th>
			</tr>
			<tbody id="the-list">
				<?php if ( $projects = $projectmanager->getProjects() ) : ?>
				<?php foreach ( $projects AS $project ) : ?>
				<?php $class = ( 'alternate' == $class ) ? '' : 'alternate'; ?>
				<tr class="<?php echo $class ?>">
					<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $project->id ?>" name="delete[<?php echo $project->id ?>]" /></th>
					<td class="num"><?php echo $project->id ?></td>
					<td><a href="edit.php?page=projectmanager/page/show-project.php&amp;id=<?php echo $project->id ?>"><?php echo $project->title ?></a></td>
					<td class="num"><?php echo $projectmanager->getNumDatasets( $project->id ) ?></td>
					<td><a href="edit.php?page=projectmanager/page/edit-project.php&amp;id=<?php echo $project->id ?>"><?php _e( 'Edit', 'projectmanager' ) ?></a></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</form>
</div>
<?php endif; ?>
