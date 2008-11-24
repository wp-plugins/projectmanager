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
?>
