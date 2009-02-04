<?php
/**
 * projectmanager_upgrade() - update routine for older version
 * 
 * @return Success Message
 */
function projectmanager_upgrade() {
	global $wpdb, $projectmanager
	
	$options = get_option( 'leaguemanager' );
	$installed = isset($options['dbversion']) ? $options['dbversion'] : '2.6';

	if (version_compare($old_options['version'], '1.2.1', '<')) {
		$charset_collate = '';
		if ( $wpdb->supports_collation() ) {
			if ( ! empty($wpdb->charset) )
				$charset_collate = "CONVERT TO CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate) )
				$charset_collate .= " COLLATE $wpdb->collate";
		}
		
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projects} $charset_collate" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} $charset_collate" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_dataset} $charset_collate" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_datasetmeta} $charset_collate" );
	}
	
	if (version_compare($old_options['version'], '1.3', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_dataset} CHANGE `grp_id` `cat_ids` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  ");
	}
	
	if (version_compare($old_options['version'], '1.5', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_dataset} ADD `user_id` int( 11 ) NOT NULL default '1'" );
		$role = get_role('administrator');
		$role->remove_cap('manage_projectmanager');
	}
	
	if (version_compare($old_options['version'], '1.6.2', '<')) {
		/*
		* Copy Logos to new image directory and delete old one
		*/
		$dir_src = WP_CONTENT_DIR.'/projects';
		$dir_handle = opendir($dir_src);
		if ( wp_mkdir_p( $projectmanager->getImagePath() ) ) {
			while( $file = readdir($dir_handle) ) {
				if( $file!="." && $file!=".." ) {
					if ( copy ($dir_src."/".$file, $projectmanager->getImagePath()."/".$file) )
						unlink($dir_src."/".$file);
				}
			}
			
			
		}
		@rmdir($dir_src);
		closedir($dir_handle);
		
	}
	
	if (version_compare($old_options['version'], '1.7', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} ADD `order_by` tinyint( 1 ) NOT NULL default '0' AFTER `order`" );
	}
	
	
	/*
	* Update version and dbversion
	*/
	$options['dbversion'] = PROJECTMANAGER_DBVERSION;
	
	update_option('projectmanager', $options);
	echo __('finished', 'projectmanager') . "<br />\n";
	$wpdb->hide_errors();
	return;
}


/**
* projectmanager_upgrade_page() - This page showsup , when the database version doesn't fit to the script PROJECTMANAGER_DBVERSION constant.
* 
* @return Upgrade Message
*/
function projectmanager_upgrade_page()  {	
	$filepath    = admin_url() . 'admin.php?page=' . $_GET['page'];

	if ($_GET['upgrade'] == 'now') {
		projectmanager_do_upgrade($filepath);
		return;
	}
?>
	<div class="wrap">
		<h2><?php _e('Upgrade ProjectManager', 'projectmanager') ;?></h2>
		<p><?php _e('Your database for LeagueManager is out-of-date, and must be upgraded before you can continue.', 'projectmanager'); ?>
		<p><?php _e('The upgrade process may take a while, so please be patient.', 'projectmanager'); ?></p>
		<h3><a href="<?php echo $filepath;?>&amp;upgrade=now"><?php _e('Start upgrade now', 'projectmanager'); ?>...</a></h3>
	</div>
	<?php
}


/**
 * projectmanager_do_upgrade() - Proceed the upgrade routine
 * 
 * @param mixed $filepath
 * @return void
 */
function projectmanager_do_upgrade($filepath) {
	global $wpdb;
?>
<div class="wrap">
	<h2><?php _e('Upgrade ProjectManager', 'projectmanager') ;?></h2>
	<p><?php projectmanager_upgrade();?></p>
	<p><?php _e('Upgrade sucessfull', 'projectmanager') ;?></p>
	<h3><a href="<?php echo $filepath;?>"><?php _e('Continue', 'projectmanager'); ?>...</a></h3>
</div>
<?php
}
?>
