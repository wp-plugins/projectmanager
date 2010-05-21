<?php
if ( !current_user_can( 'view_projects' ) ) : 
     echo '<div class="error"><p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p></div>';
else :
?>
<div class="wrap">
	<h2 id="top"><?php _e( 'Projectmanager Documentation', 'projectmanager' ) ?></h2>
	
	<h3><?php _e( 'Content', 'projectmanager') ?></h3>
	<ul>
	 <li><a href="#introduction"><?php _e( 'Introduction', 'projectmanager' ) ?></a></li>
	 <li><a href="#setup"><?php _e( 'Setup Manager', 'projectmanager' ) ?></a></li>
	 <li><a href="#shortcodes"><?php _e( 'Shortcodes', 'projectmanager' ) ?></a></li>
	 <li><a href="#templates"><?php _e( 'Templates', 'projectmanager' ) ?></a></li>
	 <li><a href="#access"><?php _e( 'Access Control', 'projectmanager' ) ?></a></li>
	 <li><a href="#customization"><?php _e( 'Customization', 'projectmanager' ) ?></a></li>
	</ul>
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  <h3 id="introduction"><?php _e( 'Introduction', 'projectmanager' ) ?></h3>
  <p>ProjectManager is a flexible data management tool.
  
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  <h3 id="setup"><?php _e( 'Setup Manager', 'projectmanager' ) ?></h3>
  
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  <h3 id="shortcodes"><?php _e( 'Shortcodes', 'projectmanager' ) ?></h3>
  
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  <h3 id="templates"><?php _e( 'Templates', 'projectmanager' ) ?></h3>
  
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  <h3 id="access"><?php _e( 'Access Control', 'projectmanager' ) ?></h3>

	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  <h3 id="customization"><?php _e( 'Customization', 'projectmanager' ) ?></h3>

</div>
<?php endif; ?>