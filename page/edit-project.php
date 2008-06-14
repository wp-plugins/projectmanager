<?php if ( !current_user_can( 'manage_projectmanager' ) ) : 
	echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :

$project_id = $_GET['id']; ?>
<form class="projectmanager" action="edit.php?page=projectmanager/page/index.php" method="post">
	<?php wp_nonce_field( 'projectmanager_manage-projects' ) ?>
	<div class="wrap">
	<?php $projectmanager->printBreadcrumb( $project_id, 'Edit Project' ); ?>
	<div class="narrow">
		<h2><?php _e( 'Edit Project', 'projectmanager' ) ?></h2>
		<label for="project_title"><?php _e( 'Title', 'projectmanager' ) ?>:</label><input type="text" name="project_title" id="project_title" value="<?php echo $projectmanager->getProjectTitle( $project_id ) ?>" size="30" style="margin-bottom: 1em;" /><br />
			
		<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
		<input type="hidden" name="updateProjectManager" value="project" />
			
		<p class="submit"><input type="submit" value="<?php _e( 'Edit Project', 'projectmanager' ) ?> &raquo;" class="button" /></p>
	</div>
	</div>
</form>
<?php endif; ?>