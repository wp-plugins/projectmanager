<?php
$old_options = get_option( 'projectmanager' );

/*
* Upgrade to Version 2.4
*/
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
	/**
	 * Copy Logos to new image directory and delete old one
	 */
	$dir_src = WP_CONTENT_DIR.'/projects';
	$dir_handle = opendir($dir_src);
	if ( wp_mkdir_p( $this->getImagePath() ) ) {
		while( $file = readdir($dir_handle) ) {
			if( $file!="." && $file!=".." ) {
				if ( copy ($dir_src."/".$file, $this->getImagePath()."/".$file) )
					unlink($dir_src."/".$file);
			}
		}
		
		
	}
	@rmdir($dir_src);
	closedir($dir_handle);
	
}

?>
