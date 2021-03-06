<?php
/**
 * projectmanager_upgrade() - update routine for older version
 * 
 * @return Success Message
 */
function projectmanager_upgrade() {
	global $wpdb, $projectmanager;
	
	$options = get_option( 'projectmanager' );
	$installed = $options['dbversion'];

	echo __('Upgrade database structure...', 'projectmanager');
	$wpdb->show_errors();
	
	if (version_compare($options['version'], '1.2.1', '<')) {
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
	
	if (version_compare($options['version'], '1.3', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_dataset} CHANGE `grp_id` `cat_ids` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  ");
	}
	
	if (version_compare($options['version'], '1.5', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_dataset} ADD `user_id` int( 11 ) NOT NULL default '1'" );
		$role = get_role('administrator');
		$role->remove_cap('manage_projectmanager');
	}
	
	if (version_compare($options['version'], '1.6.2', '<')) {
		/*
		* Copy Logos to new image directory and delete old one
		*/
		$dir_src = WP_CONTENT_DIR.'/projects';
		if ( file_exists($dir_src) ) {
			$dir_handle = opendir($dir_src);
			if ( wp_mkdir_p( $projectmanager->getFilePath() ) ) {
				while( $file = readdir($dir_handle) ) {
					if( $file!="." && $file!=".." ) {
						if ( copy ($dir_src."/".$file, $projectmanager->getFilePath()."/".$file) )
							unlink($dir_src."/".$file);
					}
				}
			}
			closedir($dir_handle);
			@rmdir($dir_src);
		}
		
	}
	
	if (version_compare($options['version'], '1.7', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} ADD `order_by` tinyint( 1 ) NOT NULL default '0' AFTER `order`" );
	}
	
	
	if (version_compare($installed, '1.8', '<')) {
		$role = get_role('administrator');
		$role->add_cap('project_user_profile');
		
		$role = get_role('editor');
		$role->add_cap('project_user_profile');
	}
	
	
	if (version_compare($installed, '1.9', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projects} CHANGE `title` `title` varchar( 255 ) NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_dataset} CHANGE `name` `name` varchar( 255 ) NOT NULL default '', CHANGE `image` `image` varchar( 50 ) NOT NULL default ''" );
	}
	
	if (version_compare($installed, '2.0', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} ADD `show_in_profile` tinyint( 1 ) NOT NULL default '0' AFTER `show_on_startpage`" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_dataset} ADD `order` int( 11 ) NOT NULL default '0'" );
	}
	
	if (version_compare($installed, '2.1', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} CHANGE `type` `type` varchar( 50 ) NOT NULL" );

		if ( $formfields = $wpdb->get_results("SELECT `type`, `id` FROM {$wpdb->projectmanager_projectmeta}") ) {
			foreach ( $formfields AS $formfield ) {
				if ( $formfield->type == 1 ) $type = 'text';
				elseif ( $formfield->type == 2 ) $type = 'textfield';
				elseif ( $formfield->type == 3 ) $type = 'email';
				elseif ( $formfield->type == 4 ) $type = 'date';
				elseif ( $formfield->type == 5 ) $type = 'uri';
				elseif ( $formfield->type == 6 ) $type = 'select';
				elseif ( $formfield->type == 7 ) $type = 'checkbox';
				elseif ( $formfield->type == 8 ) $type = 'radio';
		
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projectmeta} SET `type` = '%s' WHERE `id` = '%d'", $type, $formfield->id ) );
			}
		}
	}
	
	if (version_compare($installed, '2.2.1', '<')) {
		// Add default values for each database field	
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} CHANGE `type` `type` varchar( 50 ) NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} CHANGE `label` `label` varchar( 100 ) NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} CHANGE `show_on_startpage` `show_on_startpage` tinyint( 1 ) NOT NULL default '0'" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} CHANGE `show_in_profile` `show_in_profile` tinyint( 1 ) NOT NULL default '0'" );
		
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_dataset} CHANGE `name` `name` varchar( 255 ) NOT NULL default ''" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_dataset} CHANGE `cat_ids` `cat_ids` longtext NOT NULL default ''" );
	}
	

	if (version_compare($installed, '2.4.4', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projects} ADD `settings` LONGTEXT NOT NULL default ''" );
		foreach ( $projectmanager->getProjects() AS $project ) {
			$settings = $options['project_options'][$project->id];
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projects} SET `settings` = '%s' WHERE `id` = '%d'", maybe_serialize($settings), $project->id ) );
		}
		unset($options['project_options']);
	}


	if (version_compare($installed, '2.5', '<')) {
		$role = get_role('administrator');
		$role->remove_cap('projectmanager_admin');
		$role->remove_cap('manage_projects');
		$role->remove_cap('project_user_profile');
		$role = get_role('editor');
		$role->remove_cap('manage_projects');
		$role->remove_cap('project_user_profile');
	}
	
	if (version_compare($installed, '3.0', '<=')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} ADD `options` varchar ( 255 ) NOT NULL default ''" );
		foreach ( $wpdb->get_results( "SELECT `id` FROM {$wpdb->projectmanager_projectmeta}" ) AS $form_field) {
			$formfield_options = $options['form_field_options'][$form_field->id];
			if (is_array($formfield_options)) $formfield_options = implode(";", $formfield_options);
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projectmeta} SET `options` = '%s' WHERE `id` = '%d'", $formfield_options, $form_field->id ) );
		}
		unset($options['form_field_options']);
	}
	
	if (version_compare($installed, '3.1', '<=')) {
		$options['dashboard_widget']['num_items'] = 3;
		$options['dashboard_widget']['show_author'] = 1;
		$options['dashboard_widget']['show_date'] = 1;
		$options['dashboard_widget']['show_summary'] = 1;
	}


	if (version_compare($installed, '3.1.2', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} ADD `mandatory` tinyint( 1 ) NOT NULL default '0' AFTER `order_by`" );
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} ADD `unique` tinyint( 1 ) NOT NULL default '0' AFTER `mandatory`" );
		
		/*
		* create countries table and dump data
		*/
		include_once( ABSPATH.'/wp-admin/includes/upgrade.php' );
		$charset_collate = '';
		if ( $wpdb->supports_collation() ) {
			if ( ! empty($wpdb->charset) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate) )
				$charset_collate .= " COLLATE $wpdb->collate";
		}
		$create_countries_sql = "CREATE TABLE {$wpdb->projectmanager_countries} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT,
						`code` varchar( 3 ) NOT NULL default '',
						`name` varchar( 200 ) NOT NULL default '',
						PRIMARY KEY ( `id` )) $charset_collate";
		maybe_create_table( $wpdb->projectmanager_countries, $create_countries_sql );
		require_once(PROJECTMANAGER_PATH . "/CountriesSQL.php");
	}
	
	if (version_compare($installed, '3.1.3', '<')) {
		$wpdb->query( "ALTER TABLE {$wpdb->projectmanager_projectmeta} ADD `private` tinyint( 1 ) NOT NULL default '0' AFTER `unique`" );
	}
	
	if (version_compare($installed, '3.1.4', '<')) {
		$project_id_old = $projectmanager->getProjectID();
		foreach ($projects = $projectmanager->getProjects() AS $project) {
			$projectmanager->setProjectID($project->id);
			
			// create new project subdirectory
			wp_mkdir_p( $projectmanager->getFilePath() );
			
			// move default image
			if ($project->default_image != "" && file_exists($projectmanager->getFilePath($project->default_image, true))) {
				rename($projectmanager->getFilePath($project->default_image, true), $projectmanager->getFilePath($project->default_image));
				rename($projectmanager->getFilePath("thumb.".$project->default_image, true), $projectmanager->getFilePath("thumb.".$project->default_image));
				rename($projectmanager->getFilePath("tiny.".$project->default_image, true), $projectmanager->getFilePath("tiny.".$project->default_image));
			}
				
			foreach ($datasets = $projectmanager->getDatasets() AS $dataset) {
				// move main image
				if ($dataset->image != "" && file_exists($projectmanager->getFilePath($dataset->image, true))) {
					rename($projectmanager->getFilePath($dataset->image, true), $projectmanager->getFilePath($dataset->image));
					rename($projectmanager->getFilePath("thumb.".$dataset->image, true), $projectmanager->getFilePath("thumb.".$dataset->image));
					rename($projectmanager->getFilePath("tiny.".$dataset->image, true), $projectmanager->getFilePath("tiny.".$dataset->image));
				}
				
				$meta = $projectmanager->getDatasetMeta($dataset->id);
				if ( $meta ) {
					foreach ( $meta AS $m ) {
						// move media files to new directory
						if ('file' == $m->type || 'image' == $m->type || 'video' == $m->type) {
							if ($m->value != "" && file_exists($projectmanager->getFilePath($m->value, true))) {
								rename($projectmanager->getFilePath($m->value, true), $projectmanager->getFilePath($m->value));
								if ('image' == $m->type) {
									rename($projectmanager->getFilePath("thumb.".$m->value, true), $projectmanager->getFilePath("thumb.".$m->value));
									rename($projectmanager->getFilePath("tiny.".$m->value, true), $projectmanager->getFilePath("tiny.".$m->value));
								}
							}			
						}
					}
				}
			}
		}
		$projectmanager->setProjectID($project_id_old);
	}
	
	/* 
	* Fix a bug that would duplicate countries when activating/deactivating the plugin
	* Simply delete table and re-add countries
	*/	
	if (version_compare($installed, '3.1.5', '<')) {
		$wpdb->query( "DROP TABLE {$wpdb->projectmanager_countries}" );
		/*
		* create countries table and dump data
		*/
		include_once( ABSPATH.'/wp-admin/includes/upgrade.php' );
		$charset_collate = '';
		if ( $wpdb->supports_collation() ) {
			if ( ! empty($wpdb->charset) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate) )
				$charset_collate .= " COLLATE $wpdb->collate";
		}
		$create_countries_sql = "CREATE TABLE {$wpdb->projectmanager_countries} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT,
						`code` varchar( 3 ) NOT NULL default '',
						`name` varchar( 200 ) NOT NULL default '',
						PRIMARY KEY ( `id` )) $charset_collate";
		maybe_create_table( $wpdb->projectmanager_countries, $create_countries_sql );
		require_once(PROJECTMANAGER_PATH . "/CountriesSQL.php");
	}
	
	// Update dbversion
	$options['dbversion'] = PROJECTMANAGER_DBVERSION;
	$options['version'] = PROJECTMANAGER_VERSION;
	
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
	$filepath = admin_url() . 'admin.php?page=' . htmlspecialchars($_GET['page']);

	if (isset($_GET['upgrade']) && $_GET['upgrade'] == 'now') {
		projectmanager_do_upgrade($filepath);
		return;
	}
?>
	<div class="wrap">
		<h2><?php _e('Upgrade ProjectManager', 'projectmanager') ;?></h2>
		<p><?php _e('Your database for ProjectManager is out-of-date, and must be upgraded before you can continue.', 'projectmanager'); ?>
		<p><?php _e('The upgrade process may take a while, so please be patient.', 'projectmanager'); ?></p>
		<h3><a class="button" href="<?php echo $filepath;?>&amp;upgrade=now"><?php _e('Start upgrade now', 'projectmanager'); ?>...</a></h3>
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
	<p><?php _e('Upgrade successful', 'projectmanager') ;?></p>
	<h3><a class="button" href="<?php echo $filepath;?>"><?php _e('Continue', 'projectmanager'); ?>...</a></h3>
</div>
<?php
}
?>