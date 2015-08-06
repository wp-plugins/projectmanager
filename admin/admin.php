<?php
/**
* Admin class holding all adminstrative functions for the WordPress plugin ProjectManager
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright 	Copyright 2009
*/

class ProjectManagerAdminPanel extends ProjectManager
{
	
	/**
	 * error handling
	 *
	 * @param boolean
	 */
	var $error = false;


	/**
	 * load admin area
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		require_once( ABSPATH . 'wp-admin/includes/template.php' );
		add_action( 'admin_menu', array(&$this, 'menu') );
		
		//add_action('admin_print_scripts', array(&$this, 'loadScripts') );
		add_action('admin_print_styles', array(&$this, 'loadStyles') );
		add_action('wp_dashboard_setup', array( $this, 'registerDashboardWidget'));
		
		// cleanup backups that are older than 24 hours
		parent::cleanupOldFiles($this->getBackupPath(), 24);
	}
	function ProjectManagerAdminPanel()
	{
		$this->__construct();
	}
	

	/**
	 * check if there was an error
	 *
	 * @param none
	 * @return boolean
	 */
	function isError()
	{
		return $this->error;
	}
	
	
	/**
	 * retrieve path to project backups
	 *
	 * @param none
	 * @return string
	 */
	function getBackupPath( $file = false )
	{
		if ($file)
			return parent::getFilePath("backups/".$file, true);
		else
			return parent::getFilePath("backups", true);
	}
	
	
	/**
	 * get admin menu for subpage
	 *
	 * @param none
	 * @return array
	 */
	function getMenu()
	{
		$menu = array();
		$menu['settings'] = array( 'title' => __( 'Settings', 'projectmanager' ), 'cap' => 'edit_projects_settings', 'page' => 'project-settings_%d' );
		$menu['formfields'] = array( 'title' => __( 'Form Fields', 'projectmanager' ), 'cap' => 'edit_formfields', 'page' => 'project-formfields_%d' );
		$menu['dataset'] = array( 'title' => __( 'Add Dataset', 'projectmanager' ), 'cap' => 'edit_datasets', 'page' => 'project-dataset_%d' );
		$menu['import'] = array( 'title' => __( 'Import/Export', 'projectmanager' ), 'cap' => 'import_datasets', 'page' => 'project-import_%d' );

		return $menu;
	}


	/**
	 * adds menu to the admin interface
	 *
	 * @param none
	 */
	function menu()
	{
		$options = get_option('projectmanager');
		if( !isset($options['dbversion']) || $options['dbversion'] != PROJECTMANAGER_DBVERSION )
			$update = true;
		else
			$update = false;

		if ( !$update && $projects = parent::getProjects() ) {
			foreach( $projects AS $project ) {
				if ( isset($project->navi_link) && 1 == $project->navi_link ) {
					$icon = $project->menu_icon;
					if ( function_exists('add_object_page') )
						$page = add_object_page( $project->title, $project->title, 'view_projects', 'project_' . $project->id, array(&$this, 'display'), $this->getIconURL($icon) );
					else
						$page = add_menu_page( $project->title, $project->title, 'view_projects', 'project_' . $project->id, array(&$this, 'display'), $this->getIconURL($icon) );

					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					
					$page = add_submenu_page('project_' . $project->id, __($project->title, 'projectmanager'), __('Overview','projectmanager'),'view_projects', 'project_' . $project->id, array(&$this, 'display'));
					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					$page = add_submenu_page('project_' . $project->id, __( 'Add Dataset', 'projectmanager' ), __( 'Add Dataset', 'projectmanager' ), 'edit_datasets', 'project-dataset_' . $project->id, array(&$this, 'display'));
					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					$page = add_submenu_page('project_' . $project->id, __( 'Form Fields', 'projectmanager' ), __( 'Form Fields', 'projectmanager' ), 'edit_formfields', 'project-formfields_' . $project->id, array(&$this, 'display'));
					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					$page = add_submenu_page('project_' . $project->id, __( 'Settings', 'projectmanager' ), __( 'Settings', 'projectmanager' ), 'edit_projects_settings', 'project-settings_' . $project->id, array(&$this, 'display'));
					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					add_submenu_page('project_' . $project->id, __('Categories'), __('Categories'), 'manage_projects', 'edit-tags.php?taxonomy=category');
					add_submenu_page('project_' . $project->id, __('Import/Export', 'projectmanager'), __('Import/Export', 'projectmanager'), 'import_datasets', 'project-import_' . $project->id, array(&$this, 'display'));			
				}
			}
			
		}
		
		
		// Add global Projects Menu
		$page = add_menu_page(__('Projects', 'projectmanager'), __('Projects', 'projectmanager'), 'view_projects', PROJECTMANAGER_PATH,array(&$this, 'display'), PROJECTMANAGER_URL.'/admin/icons/menu/databases.png');
		add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
		
		add_submenu_page(PROJECTMANAGER_PATH, __('Projects', 'projectmanager'), __('Overview','projectmanager'),'view_projects', PROJECTMANAGER_PATH, array(&$this, 'display'));
		$page = add_submenu_page(PROJECTMANAGER_PATH, __( 'Settings'), __('Settings'), 'projectmanager_settings', 'projectmanager-settings', array( &$this, 'display') );
		add_action("admin_print_scripts-$page", array(&$this, 'loadColorpicker') );
		add_submenu_page(PROJECTMANAGER_PATH, __( 'Documentation', 'projectmanager'), __('Documentation', 'projectmanager'), 'view_projects', 'projectmanager-documentation', array( &$this, 'display') );
				
		$plugin = 'projectmanager/projectmanager.php';
		add_filter( 'plugin_action_links_' . $plugin, array( &$this, 'pluginActions' ) );
	}
	
	
	/**
	* Register ProjectManager Dashboard Widget
	*
	* @param  none
	* @return void
	*/
	public static function registerDashboardWidget()
	{
		wp_add_dashboard_widget(
			'projectmanager_dashboard',
			__('ProjectManager Latest Support News', 'projectmanager'),
			array(
				'ProjectManagerAdminPanel',
				'latestSupportNews'
			)
		);
	}
	/**
	 * Get latest news from ProjectManager Support on WordPress.org
	 *
	 * @param  none
	 * @return string
	 */
	public static function latestSupportNews()
	{
		$options = get_option('projectmanager');
		echo '<div class="rss-widget">';

		wp_widget_rss_output(array(
			'url' => 'http://wordpress.org/support/rss/plugin/projectmanager',
			'show_author' => $options['dashboard_widget']['show_author'],
			'show_date' => $options['dashboard_widget']['show_date'],
			'show_summary' => $options['dashboard_widget']['show_summary'],
			'items' => $options['dashboard_widget']['num_items']
		));

		echo '</div>';
	}
	
	
	/**
	 * show admin menu
	 *
	 * @param none
	 */
	function display()
	{
		global $projectmanager;
		
		$options = get_option('projectmanager');

		// Update Plugin Version
		if ( $options['version'] != PROJECTMANAGER_VERSION ) {
			$options['version'] = PROJECTMANAGER_VERSION;
			update_option('projectmanager', $options);
		}

		if( !isset($options['dbversion']) || $options['dbversion'] != PROJECTMANAGER_DBVERSION ) {
			include_once ( dirname (__FILE__) . '/upgrade.php' );
			projectmanager_upgrade_page();
			return;
		}

		switch ($_GET['page']) {
			case 'projectmanager-settings':
				$this->displayOptionsPage();
				break;
			case 'projectmanager-documentation':
			  include_once( dirname(__FILE__) . '/documentation.php' );
			  break;
			case 'projectmanager':
				$page = isset($_GET['subpage']) ? $_GET['subpage'] : '';
				switch($page) {
					case 'show-project':
						include_once( dirname(__FILE__) . '/show-project.php' );
						break;
					case 'settings':
						include_once( dirname(__FILE__) . '/settings.php' );
						break;
					case 'dataset':
						include_once( dirname(__FILE__) . '/dataset.php' );
						break;
					case 'formfields':
						include_once( dirname(__FILE__) . '/formfields.php' );
						break;
					case 'import':
						include_once( dirname(__FILE__) . '/import.php' );
						break;
					default:
						include_once( dirname(__FILE__) . '/index.php' );
						break;
				}
				break;
			
			default:
				$page = explode("_", $_GET['page']);
				$projectmanager->init($page[1]);
							
				switch ($page[0]) {
					case 'project':
						include_once( dirname(__FILE__) . '/show-project.php' );
						break;
					case 'project-settings':
						include_once( dirname(__FILE__) . '/settings.php' );
						break;
					case 'project-dataset':
						include_once( dirname(__FILE__) . '/dataset.php' );
						break;
					case 'project-formfields':
						include_once( dirname(__FILE__) . '/formfields.php' );
						break;
					case 'project-import':
						include_once( dirname(__FILE__) . '/import.php' );
						break;
						
				}
		}
	}
	
	
	/**
	 * display link to settings page in plugin table
	 *
	 * @param array $links array of action links
	 * @return void
	 */
	function pluginActions( $links )
	{
		$settings_link = '<a href="admin.php?page=projectmanager-settings">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
	
		return $links;
	}
	
	
	/**
	 * load scripts
	 *
	 * @param none
	 * @return void
	 */
	function loadScripts()
	{
		wp_register_script( 'projectmanager', PROJECTMANAGER_URL.'/admin/js/functions.js', array( 'sack', 'scriptaculous', 'prototype' ), PROJECTMANAGER_VERSION );
		wp_enqueue_script( 'projectmanager' );
	}
	function loadColorpicker()
	{
		wp_register_script ('projectmanager_colorpicker', PROJECTMANAGER_URL.'/admin/js/colorpicker.js', array( 'colorpicker' ), PROJECTMANAGER_VERSION );
		wp_enqueue_script('projectmanager_colorpicker');
	}

	
	/**
	 * load styles
	 *
	 * @param none
	 * @return void
	 */
	function loadStyles()
	{
		wp_enqueue_style('thickbox');
		wp_enqueue_style('projectmanager', PROJECTMANAGER_URL . "/style.css", false, '1.0', 'screen');
		wp_enqueue_style('projectmanager_admin', PROJECTMANAGER_URL . "/admin/style.css", false, '1.0', 'screen');
	}
	
	
	/**
	 * set message by calling parent function
	 *
	 * @param string $message
	 * @param boolean $error (optional)
	 * @return void
	 */
	function setMessage( $message, $error = false )
	{
		parent::setMessage( $message, $error );
	}
	
	
	/**
	 * print message calls parent
	 *
	 * @param none
	 * @return string
	 */
	function printMessage()
	{
		parent::printMessage();
	}
	
	
	/**
	 * display global settings page (e.g. color scheme options)
	 *
	 * @param none
	 * @return void
	 */
	function displayOptionsPage($include=false)
	{
		$options = get_option('projectmanager');
		
		if ( current_user_can( 'projectmanager_settings' ) ) {
			if ( isset($_POST['updateProjectManager']) ) {
				check_admin_referer('projetmanager_manage-global-league-options');
				$options['colors']['headers'] = htmlspecialchars($_POST['color_headers']);
				$options['colors']['rows'] = array( htmlspecialchars($_POST['color_rows_alt']), htmlspecialchars($_POST['color_rows']) );
				$options['dashboard_widget']['num_items'] = intval($_POST['dashboard']['num_items']);
				$options['dashboard_widget']['show_author'] = isset($_POST['dashboard']['show_author']) ? 1 : 0;
				$options['dashboard_widget']['show_date'] = isset($_POST['dashboard']['show_date']) ? 1 : 0;
				$options['dashboard_widget']['show_summary'] = isset($_POST['dashboard']['show_summary']) ? 1 : 0;
				
				update_option( 'projectmanager', $options );
				$this->setMessage(__( 'Settings saved', 'leaguemanager' ));
				$this->printMessage();
			}
			require_once (dirname (__FILE__) . '/settings-global.php');
		} else {
			echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
		}
	}
	
	
	/**
	 *  get icon URl
	 *  
	 *  First check if custom directory 'projectmanager/icons' exists in template directory
	 *  If not load default dir.
	 *  
	 *  @param none
	 *  @return directory
	 */
	function getIconURL( $icon, $dir = 'menu' )
	{
		if ( file_exists(TEMPLATEPATH . "/projectmanager/icons/".$icon))
			return get_template_directory_uri() . "/projectmanager/icons/".$icon;
		elseif ( file_exists(PROJECTMANAGER_PATH.'/admin/icons/'.$dir.'/'.$icon) )
			return PROJECTMANAGER_URL.'/admin/icons/'.$dir.'/'.$icon;
		else
			return PROJECTMANAGER_URL.'/admin/icons/'.$dir.'/databases.png';
	}
	
	
	/**
	 * check if there is only a single project
	 *
	 * @param none
	 * @return boolean
	 */
	function isSingle()
	{
		$this->single = false;
		$projects = parent::getProjects();
		foreach ( $projects AS $project ) {
			if ( 1 == $project->navi_link && parent::getNumProjects() == 1) {
				$this->single = true;
				break;
			}
		}
		return $this->single;
	}
	
	
	/**
	 * gets order of datasets
	 *
	 * @param string $input serialized string with order
	 * @param string $listname ID of list to sort
	 * @return sorted array of parameters
	 */
 	function getOrder( $input, $listname = 'the-list' )
	{
		parse_str( $input, $input_array );
		$input_array = $input_array[$listname];
		$order_array = array();
		for ( $i = 0; $i < count($input_array); $i++ ) {
			if ( $input_array[$i] != '' )
				$order_array[$i+1] = $input_array[$i];
		}
		return $order_array;	
	}
	
	
	/**
	 * gets checklist for groups. Adopted from wp-admin/includes/template.php
	 *
	 * @param int $child_of parent category
	 * @param array $selected cats array of selected category IDs
	 */
	function categoryChecklist( $child_of, $selected_cats )
	{
		$walker = new Walker_Category_Checklist();
		$child_of = (int) $child_of;
		
		$args = array();
		$args['selected_cats'] = $selected_cats;
		$args['popular_cats'] = array();
		$categories = get_categories( "child_of=$child_of&hierarchical=0&hide_empty=0" );
		
		$checked_categories = array();
		for ( $i = 0; isset($categories[$i]); $i++ ) {
			if ( in_array($categories[$i]->term_id, $args['selected_cats']) ) {
				$checked_categories[] = $categories[$i];
				unset($categories[$i]);
			}
		}

		// Put checked cats on top
		echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
		// Then the rest of them
		echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
	}
	



	/**
	 * get possible sorting options for datasets
	 *
	 * @param string $selected
	 * @return string
	 */
	function datasetOrderbyOptions( $selected )
	{
		$options = array( 'id' => __('ID', 'projectmanager'), 'name' => __('Name','projectmanager'), 'formfields' => __('Formfields', 'projectmanager') );
		
		foreach ( $options AS $option => $title ) {
			$select = ( $selected == $option ) ? ' selected="selected"' : '';
			echo '<option value="'.$option.'"'.$select.'>'.$title.'</option>';
		}
	}
	
	
	/**
	 * get possible order options
	 *
	 * @param string $selected
	 * @return string
	 */
	function datasetOrderOptions( $selected )
	{
		$options = array( 'ASC' => __('Ascending','projectmanager'), 'DESC' => __('Descending','projectmanager') );
		
		foreach ( $options AS $option => $title ) {
			$select = ( $selected == $option ) ? ' selected="selected"' : '';
			echo '<option value="'.$option.'"'.$select.'>'.$title.'</option>';
		}
	}
	
	
	/**
	 * add new project
	 *
	 * @param string $title
	 * @return string
	 */
	function addProject( $title )
	{
		global $wpdb;
	
		if ( !current_user_can('edit_projects') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_projects} (title) VALUES ('%s')", htmlspecialchars($title) ) );
		$project_id = $wpdb->insert_id;
		
		parent::setProjectID($project_id);
		// create media directory for project
		wp_mkdir_p( parent::getFilePath() );
		
		$this->setMessage( __('Project added','projectmanager') );
		
		do_action('projectmanager_add_project', $project_id);
	}
	
	
	/**
	 * edit project
	 *
	 * @param string $title
	 * @param int $project_id
	 * @return string
	 */
	function editProject( $title, $project_id )
	{
		global $wpdb;

		if ( !current_user_can('edit_projects') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}
		
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projects} SET `title` = '%s' WHERE `id` = '%d'", htmlspecialchars($title), intval($project_id) ) );
		$this->setMessage( __('Project updated','projectmanager') );
		
		do_action('projectmanager_edit_project', intval($project_id));
	}
	
	
	/**
	 * delete project
	 *
	 * @param int  $project_id
	 * @return void
	 */
	function delProject( $project_id )
	{
		global $wpdb, $projectmanager;
		
		if ( !current_user_can('delete_projects') ) 
			return;

		$project_id = intval($project_id);
		$projectmanager->init($project_id);
		foreach ( $projectmanager->getDatasets() AS $dataset )
			$this->delDataset( $dataset->id );
		
		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = '%d'", $project_id) );
		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->projectmanager_projects} WHERE `id` = '%d'", $project_id) );
		
		do_action('projectmanager_del_project', $project_id);
	}

	
	/**
	 * save Project Settings
	 *
	 * @param array $settings
	 * @param int $project_id
	 * @return void
	 */
	function saveSettings( $settings, $project_id )
	{
		global $wpdb, $projectmanager;

		if ( !current_user_can('edit_projects_settings') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		$project_id = intval($project_id);
		$project = $projectmanager->getProject($project_id);
		$settings['default_image'] = $project->default_image;
		
		if (isset($settings['del_default_image']) && $settings['del_default_image'] == 1) {
			$this->delImage($project->default_image);
			$settings['default_image'] = "";
			unset($settings['del_default_image']);
		}
		
		if ( isset($_FILES['project_default_image']) && $_FILES['project_default_image']['name'] != '' && file_exists($_FILES['project_default_image']['tmp_name']) ) {
			require_once (PROJECTMANAGER_PATH . '/lib/image.php');
			$file = $_FILES['project_default_image'];
		
			$new_file = parent::getFilePath().'/'.basename($file['name']);
			$image = new ProjectManagerImage($new_file);
			if ( $image->supported($file['name']) ) {
				if ( $file['size'] > 0 ) {
					if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
						// delete old image if present
						if ( $project->default_image != "" && $project->default_image != basename($file['name'])) $this->delImage($project->default_image);
						
						if (file_exists($new_file)) {
							// Resize original file and create thumbnails
							$dims = array( 'width' => $project->medium_size['width'], 'height' => $project->medium_size['height'] );
							$image->createThumbnail( $dims, $new_file, $project->chmod );

							$dims = array( 'width' => $project->thumb_size['width'], 'height' => $project->thumb_size['height'] );
							$image->createThumbnail( $dims, parent::getFilePath().'/thumb.'.basename($file['name']), $project->chmod );
									
							$dims = array( 'width' => $project->tiny_size['width'], 'height' => $project->tiny_size['height'] );
							$image->createThumbnail( $dims, parent::getFilePath().'/tiny.'.basename($file['name']), $project->chmod );
						}
						// set image filename in settings
						$settings['default_image'] = basename($file['name']);
					} else {		
						$this->setMessage( sprintf( __('The uploaded file could not be moved to %s.' ), parent::getFilePath() ), true );
					}
				}
			} else {
				$this->setMessage( __('The file type is not supported.','projectmanager'), true );
			}
		}
		
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projects} SET `settings` = '%s' WHERE `id` = '%d'", maybe_serialize($settings), $project_id ) );
		$this->setMessage(__('Settings saved', 'projectmanager'));
		
		do_action('projectmanager_save_settings', $project_id);
	}


	/**
	 * import datasets from CSV file
	 *
	 * @param int $project_id
	 * @param array $file CSV file
	 * @param string $delimiter
	 * @param array $cols column assignments
	 * @return string
	 */
	function importDatasets( $project_id, $file, $delimiter, $cols )
	{
		global $wpdb;
		
		if ( !current_user_can('import_datasets') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		if ($delimiter == ",") {
			$this->setMessage(__('Dataset columns must not be separated by comma as multiple categories use this delimiter', 'projectmanager'), true);
			return false;
		}
		
		$project_id = intval($project_id);

		if ( $file['size'] > 0 ) {
			/*
			* Upload CSV file to image directory, temporarily
			*/
			$new_file =  parent::getFilePath().'/'.basename($file['name']);
			if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
				$handle = @fopen($new_file, "r");
				if ($handle) {
					if ( "TAB" == $delimiter ) $delimiter = "\t"; // correct tabular delimiter
					
					$i = 0; $l=0; // initialize dataset & line counter
					while (!feof($handle)) {
						  $buffer = fgets($handle, 4096);
						  $line = explode($delimiter, $buffer);
						  
						  if ( $l > 0 && $line ) {
							$name = $line[0];
							$image = $line[1];
							$categories = empty($line[2]) ? '' : explode(",", $line[2]);
							/*
    						 * get Category IDs from titles
    						 */						
							$cat_ids = array();
    						if ( !empty($categories) ) {
								foreach ( $categories AS $category ) {
									$cat_ids[] = get_cat_ID($category);
								}
                			}
                
    						// assign column values to form fields
							$meta = array();
    						foreach ( $cols AS $col => $form_field_id ) {
    							$meta[$form_field_id] = $line[$col];
    						}
    		
    						if ( $line && !empty($name) ) {
    							$dataset_id = $this->addDataset($project_id, $name, $cat_ids, $meta, $user_id = false, $is_admin = true, $import = true);
								$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", $image, $dataset_id ) );
    							$i++;
    						}
  					  }
  					  $l++;
					}
					fclose($handle);
					
					$this->setMessage(sprintf(__( '%d Datasets successfully imported', 'projectmanager' ), $i));
				} else {
					$this->setMessage( __('The file is not readable', 'projectmanager'), true );
				}
			} else {
				$this->setMessage(sprintf( __('The uploaded file could not be moved to %s.' ), parent::getFilePath()) );
			}
			@unlink($new_file); // remove file from server after import is done
		} else {
			$this->setMessage( __('The uploaded file seems to be empty', 'projectmanager'), true );
		}
	}
	
	
	/**
	 * import media to webserver
	 *
	 * @param none
	 */
	function importMedia()
	{
		global $projectmanager;
		
		if (!isset($_FILES['projectmanager_media_zip']) || empty($_FILES['projectmanager_media_zip']['name'])) {
			$this->setMessage(__('You have to select a media file in zip format', 'projectmanager'), true);
			return false;
		} else {
			$media_file = $_FILES['projectmanager_media_zip'];
			$file = $this->uploadFile($media_file, true );
		
			if (file_exists($file)) {
				if ($projectmanager->unzipFiles($file)) {
					$this->setMessage(__('Media file have been successfully imported', 'projectmanager'));
					// remove zip file
					@unlink($file);
				} else {
					$this->setMessage(__('Media zip file could not be unpacked','projectmanager'), true);
				}
			}
		}
	}
	
	
	/**
	 * check if dataset with given user ID exists
	 *
	 * @param int $project_id
	 * @param int $user_id
	 * @return boolean
	 */
	function datasetExists( $project_id, $user_id )
	{
		global $wpdb;

		$project_id = intval($project_id);
		$user_id = intval($user_id);
		$count= $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$project_id} AND `user_id` = '".$user_id."'" );

		if ( $count > 0 )
			return true;
		
		return false;
	}


	/**
	 * export datasets to CSV
	 *
	 * @param int $project_id
	 * @return file
	 */
	function exportData( $project_id, $type = "data" )
	{
		global $projectmanager;
		
		wp_mkdir_p( $this->getBackupPath() );
		
		//if ( !current_user_can('import_datasets') ) {
		//	$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
		//	return;
		//}
 
		$project_id = intval($project_id);
		$this->project_id = $project_id;
		$projectmanager->init($project_id);
		$project = $projectmanager->getProject();
		
		// Initialize array with media files
		$media = array();
		$media_filename = $project->title."_Media_".date("Y-m-d").".zip";
		
		$filename = $project->title."_".date("Y-m-d").".csv";
		/*
		* Generate Header
		*/
		$contents = __('Name','projectmanager')."\t".__('Image','projectmanager')."\t".__('Categories','projectmanager');
		foreach ( $projectmanager->getFormFields() AS $form_field )
			$contents .= "\t".$form_field->label;
		
		foreach ( $projectmanager->getDatasets() AS $dataset ) {
			// add main image to media array
			if ($dataset->image != "") {
				$media[] = $projectmanager->getFilePath($dataset->image);
				$media[] = $projectmanager->getFilePath("thumb.".$dataset->image);
				$media[] = $projectmanager->getFilePath("tiny.".$dataset->image);
			}
			
			$contents .= "\n".$dataset->name."\t".$dataset->image."\t".$projectmanager->getSelectedCategoryTitles(maybe_unserialize($dataset->cat_ids));

			foreach ( $projectmanager->getDatasetMeta( $dataset->id ) AS $meta ) {
				// Add media files to array
				if (($meta->type == "file" || $meta->type == "video") && $meta->value != "") {
					$media[] = $projectmanager->getFilePath($meta->value);
				}
				if ($meta->type == "image" && $meta->value != "") {
					$media[] = $projectmanager->getFilePath($meta->value);
					$media[] = $projectmanager->getFilePath("thumb.".$meta->value);
					$media[] = $projectmanager->getFilePath("tiny.".$meta->value);
				}
				
				// Remove line breaks
				$meta->value = str_replace("\r\n", "", stripslashes($meta->value));
				$contents .= "\t".strip_tags($meta->value);
			}
		}
		
		if ($type == "media") {
			// create zip Archive of media files
			$ret = $projectmanager->createZip($media, $this->getBackupPath($media_filename));
			
			if ($ret) {
				header("Content-Description: File Transfer");
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=\"".$media_filename."\"");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize($this->getBackupPath($media_filename)));
				ob_end_flush();
				@readfile($this->getBackupPath($media_filename));
				exit();
			} else {
				$this->setMessage(__('No media files found to export', 'projectmanager'), true);
				$this->printMessage();
			}
		}
		if ($type == "data") {
			header('Content-Type: text/csv');
			header('Content-Disposition: inline; filename="'.$filename.'"');
			echo $contents;
			exit();
		}
	}
	
	
	/**
	 * make sure that meta value is unique
	 *
	 * @param int $project_id
	 * @param array $formfield
	 * @param string $value
	 * @return boolean
	 */
	function datasetMetaValueIsUnique($project_id, $formfield_id, $value, $dataset_id = false) {
		global $wpdb;
		
		$data = $wpdb->get_results( $wpdb->prepare("SELECT dataset.id AS dataset_id, data.value AS value FROM {$wpdb->projectmanager_dataset} AS dataset LEFT JOIN {$wpdb->projectmanager_datasetmeta} AS data ON dataset.id = data.dataset_id WHERE dataset.project_id = '%d' AND data.form_id = '%d'", $project_id, $formfield_id) );
		foreach ($data AS $d) {
			if ($value == $d->value && $d->dataset_id != $dataset_id)
				return false;
		}
		
		return true;
	}	
	
	
	/**
	 * add new dataset
	 *
	 * @param int $project_id
	 * @param string $name
	 * @param array $cat_ids
	 * @param array $dataset_meta
	 * @param false|int $user_id
	 * @param boolean $is_admin
	 * @param boolean $import
	 * @return string
	 */
	function addDataset( $project_id, $name, $cat_ids, $dataset_meta = false, $user_id = false, $is_admin = true, $import = false )
	{
		global $wpdb, $current_user, $projectmanager;
		require_once (PROJECTMANAGER_PATH . '/lib/image.php');

		if ( $user_id && $this->datasetExists($project_id, $user_id) ) {
			$this->setMessage( __( 'You cannot add two datasets with same User ID.', 'projectmanager' ), true );
			return false;
		}

		$project_id = intval($project_id);
		$projectmanager->init($project_id);
		$this->project_id = $project_id;
		$project = $this->project = $projectmanager->getProject($project_id);
		if ( !$user_id ) $user_id = $current_user->ID;
		$user_id = intval($user_id);

		if ($is_admin) {
			// Negative check on capability: user can't edit datasets
			if ( !current_user_can('edit_datasets') && !current_user_can('projectmanager_user') && !current_user_can('import_datasets') ) {
				$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
				return;
			}

			// user has only cap 'projectmanager_user' but not 'edit_other_datasets' and 'edit_datasets'
			if ( current_user_can('projectmanager_user') && !current_user_can('edit_other_datasets') && !current_user_can('edit_datasets') && !current_user_can('import_datasets') ) {
				// and dataset with this user ID already exists
				if ( $this->datasetExists($project_id, $user_id) ) {
					$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
					return;
				}
			}
		}
		
		$this->error = false;
		// Make sure that a dataset name was provided
		if ($name == "") {
			$this->setMessage( __("You have to provide a name", 'projectmanager'), true );
			$this->printMessage();
			$this->error = true;
		}
		if (!$is_admin && strlen($name) > 50) {
			$this->setMessage( __("Your name must not exceed 50 characters", 'projectmanager'), true );
			$this->printMessage();
			$this->error = true;
		}
		
		if ($project->image_mandatory == 1 && !$import) {
			if (!isset($_FILES['projectmanager_image']) || (isset($_FILES['projectmanager_image']) && $_FILES['projectmanager_image']['name'] == '')) {
				$this->setMessage( __("You have to provide an image to upload", 'projectmanager'), true );
				$this->printMessage();
				$this->error = true;
			}
		}
		
		// Check each formfield for mandatory and unique values
		foreach (parent::getFormFields(false, true) AS $formfield) {
			$formfield_options = explode(";", $formfield->options);
			
			// check if there is a maximum input length given
			$match = preg_grep("/max:/", $formfield_options);
			if (count($match) == 1) {
				$max = explode(":", $match[0]);
				$max = $max[1];
			} else {
				$max = 0;
			}
			
			// make sure that mandatory fields are not empty
			if ($formfield->mandatory == 1) {
				if( !isset($dataset_meta[$formfield->id]) || (isset($dataset_meta[$formfield->id]) && $dataset_meta[$formfield->id] == "") ) {
					$this->setMessage( sprintf(__("Mandatory field %s is empty", 'projectmanager'), $formfield->label), true );
					$this->printMessage();
					$this->error = true;
				}
			}
			
			// make sure unique fields have no match in database
			if ($formfield->unique == 1) {
				if (!$this->datasetMetaValueIsUnique($project_id, $formfield->id, $dataset_meta[$formfield->id])) {
					$this->setMessage(sprintf(__("Provided %s `%s` is not a valid e-mail address", 'projectmanager'), $formfield->label, $dataset_meta[$formfield->id]), true);
					$this->printMessage();
					$this->error = true;
				}
			}
			
			// check email validity
			if ($formfield->type == "email") {
				if (!filter_var($dataset_meta[$formfield->id], FILTER_VALIDATE_EMAIL)) {
					$this->setMessage(sprintf(__("Provided %s `%s` is not a valid e-mail address", 'projectmanager'), $formfield->label, $dataset_meta[$formfield->id]), true);
					$this->printMessage();
					$this->error = true;
				}
			}
			
			// check that provided input is not longer than $max
			if ($max > 0 && strlen($dataset_meta[$formfield->id]) > $max) {
				$this->setMessage(sprintf(__("Provided %s is longer than the allowed length of %s characters", 'projectmanager'), $formfield->label, $max),true);
				$this->printMessage();
				$this->error = true;
			}
		}
		$this->setMessage("");
		$this->setMessage("", true);
		
		// stop if an error occured
		if ($this->error) return;
		
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_dataset} (name, cat_ids, project_id, user_id) VALUES ('%s', '%s', '%d', '%d')", $name, maybe_serialize($cat_ids), $project_id, $user_id ) );
		$dataset_id = $wpdb->insert_id;
				
		if ( $dataset_meta ) {
			foreach ( $dataset_meta AS $meta_id => $meta_value ) {
				$meta_id = intval($meta_id);
				$formfield = parent::getFormFields($meta_id);
				
				// Manage file upload - Not available in dataset import
				if ( !$import && ('file' == $formfield->type || 'image' == $formfield->type || 'video' == $formfield->type )) {
					$file = array('name' => $_FILES['form_field']['name'][$meta_id], 'tmp_name' => $_FILES['form_field']['tmp_name'][$meta_id], 'size' => $_FILES['form_field']['size'][$meta_id], 'type' => $_FILES['form_field']['type'][$meta_id]);
					if ( !empty($file['name']) )
						$this->uploadFile($file);
					
					$meta_value = basename($file['name']);
					
					
					// Create Thumbails for Image
					if ( 'image' == $formfield->type && !empty($meta_value) ) {
						$new_file = parent::getFilePath().'/'.$meta_value;
						$image = new ProjectManagerImage($new_file);
						// Resize original file and create thumbnails
						$dims = array( 'width' => $project->medium_size['width'], 'height' => $project->medium_size['height'] );
						$image->createThumbnail( $dims, $new_file, $project->chmod );

						$dims = array( 'width' => $project->thumb_size['width'], 'height' => $project->thumb_size['height'] );
						$image->createThumbnail( $dims, parent::getFilePath().'/thumb.'.$meta_value, $project->chmod );
						
						$dims = array( 'width' => $project->tiny_size['width'], 'height' => $project->tiny_size['height'] );
						$image->createThumbnail( $dims, parent::getFilePath().'/tiny.'.$meta_value, $project->chmod );
					}		
				} elseif ( 'numeric' == $formfield->type || 'currency' == $formfield->type ) {
					$meta_value += 0; // convert value to numeric type
				}
				
				if ( is_array($meta_value) ) {
					// form field value is a date
					if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) ) {
						$meta_value = sprintf("%s-%s-%s", $meta_value['year'], $meta_value['month'], $meta_value['day']);
					} elseif ( array_key_exists('hour', $meta_value) && array_key_exists('minute', $meta_value) ) {
						$meta_value = sprintf("%s:%s", $meta_value['hour'], $meta_value['minute']);
					}
				}

				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ('%d', '%d', '%s')", $meta_id, $dataset_id, maybe_serialize($meta_value) ) );
			}
			
			// Check for unsubmitted form data, e.g. checkbox list
			if ($form_fields = parent::getFormFields()) {
				foreach ( $form_fields AS $form_field ) {
					if ( !array_key_exists($form_field->id, $dataset_meta) ) {
						$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ('%d', '%d', '')", $dataset_id, $form_field->id ) );
					}
				}
			}
		} else {
			// Populate empty meta value for new registered user
			foreach ( $projectmanager->getFormFields() AS $formfield ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ('%d', '%d', '')", $formfield->id, $dataset_id ) );
			}
		}


		if ( isset($_FILES['projectmanager_image']) && $_FILES['projectmanager_image']['name'] != ''  )
			$this->uploadImage($dataset_id, $_FILES['projectmanager_image']);
				
		$this->setMessage( __( 'New dataset added to the database.', 'projectmanager' ) );
		
		do_action('projectmanager_add_dataset', $dataset_id);
		
		return $dataset_id;
	}
		
		
	/**
	 * edit dataset
	 *
	 * @param int $project_id
	 * @param string $name
	 * @param array $cat_ids
	 * @param int $dataset_id
	 * @param array $dataset_meta
	 * @param int $user_id
	 * @param boolean $del_image
	 * @param string $image_file
	 * @param int|false $owner
	 * @return string
	 */
	function editDataset( $project_id, $name, $cat_ids, $dataset_id, $dataset_meta = false, $user_id, $del_image = false, $image_file = '', $overwrite_image = false, $owner = false, $is_admin = true )
	{
		global $wpdb, $current_user, $projectmanager;
		
		require_once (PROJECTMANAGER_PATH . '/lib/image.php');
		
		$this->project_id = intval($project_id);
		$project = $this->project = $projectmanager->getProject($this->project_id);
		$dataset = $projectmanager->getDataset($dataset_id);

		// Check if user has either cap 'edit_datasets' or 'projectmanager_user'
		if ( !current_user_can('edit_datasets') && !current_user_can('projectmanager_user') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		// check if user has cap 'edit_other_datasets'
		if ( !current_user_can('edit_other_datasets') ) {
			if ( $dataset->user_id != $current_user->ID ) {
				$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
				return;
			}
		}

		$project_id = intval($project_id);
		$dataset_id = intval($dataset_id);
		$user_id = intval($user_id);
		
		$this->error = false;
		// Make sure that a dataset name was provided
		if ($name == "") {
			$this->setMessage( __("You have to provide a name", 'projectmanager'), true );
			$this->printMessage();
			$this->error = true;
		}
		if (!$is_admin && strlen($name) > 50) {
			$this->setMessage( __("Your name must not  exceed 50 characters", 'projectmanager'), true );
			$this->printMessage();
			$this->error = true;
		}
		
		// Check each formfield for mandatory and unique values
		foreach (parent::getFormFields() AS $formfield) {
			$formfield_options = explode(";", $formfield->options);
			
			// check if there is a maximum input length given
			$match = preg_grep("/max:/", $formfield_options);
			if (count($match) == 1) {
				$max = explode(":", $match[0]);
				$max = $max[1];
			} else {
				$max = 0;
			}
			
			// make sure that mandatory fields are not empty
			if ($formfield->mandatory == 1) {
				if( !isset($dataset_meta[$formfield->id]) || (isset($dataset_meta[$formfield->id]) && $dataset_meta[$formfield->id] == "") ) {
					$this->setMessage( __(sprintf("Mandatory field %s is empty", $formfield->label), 'projectmanager'), true );
					$this->printMessage();
					$this->error = true;
				}
			}
			
			// make sure unique fields have no match in database
			if ($formfield->unique == 1) {
				if (!$this->datasetMetaValueIsUnique($project_id, $formfield->id, $dataset_meta[$formfield->id], $dataset_id)) {
					$this->setMessage(__(sprintf("Provided %s `%s` is already present in the database", $formfield->label, $dataset_meta[$formfield->id]), 'projectmanager'), true);
					$this->printMessage();
					$this->error = true;				
				}
			}

			// check email validity
			if ($formfield->type == "email") {
				if (!filter_var($dataset_meta[$formfield->id], FILTER_VALIDATE_EMAIL)) {
					$this->setMessage(__(sprintf("Provided %s `%s` is not a valid e-mail address", $formfield->label, $dataset_meta[$formfield->id]), "projectmanager"), true);
					$this->printMessage();
					$this->error = true;
				}
			}
						
			// check that provided input is not longer than $max
			if ($max > 0 && strlen($dataset_meta[$formfield->id]) > $max) {
				$this->setMessage(__(sprintf("Provided %s is longer than the allowed length of %s characters", $formfield->label, $max), "projectmanager"), true);
				$this->printMessage();
				$this->error = true;
			}
		}
		$this->setMessage("");
		$this->setMessage("", true);
		
		// stop if an error occured
		if ($this->error) return;
		
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `name` = '%s', `cat_ids` = '%s' WHERE `id` = '%d'", $name, maybe_serialize($cat_ids), $dataset_id ) );
			
		// Change Dataset owner if supplied
		if ( $owner )
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `user_id` = '%d' WHERE `id` = '%d'", $owner, $dataset_id ) );
			
		if ( $dataset_meta ) {
			foreach ( $dataset_meta AS $meta_id => $meta_value ) {
				$meta_id = intval($meta_id);
				$formfield = parent::getFormFields($meta_id);
					
				// Manage file upload
				if ( 'file' == $formfield->type || 'image' == $formfield->type || 'video' == $formfield->type ) {
					$file = array('name' => $_FILES['form_field']['name'][$meta_id], 'tmp_name' => $_FILES['form_field']['tmp_name'][$meta_id], 'size' => $_FILES['form_field']['size'][$meta_id], 'type' => $_FILES['form_field']['type'][$meta_id], 'current' => $meta_value['current']);
					$delete = (isset($meta_value['del']) && 1 == $meta_value['del']) ? true : false;
					$overwrite = isset($meta_value['overwrite']) ? true : false;
					$meta_value = $this->editFile($file, $overwrite, $delete);
					
					// Create Thumbnails for Image
					if ( 'image' == $formfield->type && !empty($meta_value) ) {
						$new_file = parent::getFilePath().'/'.$meta_value;
						$image = new ProjectManagerImage($new_file);
						// Resize original file and create thumbnails
						$dims = array( 'width' => $project->medium_size['width'], 'height' => $project->medium_size['height'] );
						$image->createThumbnail( $dims, $new_file, $project->chmod );

						$dims = array( 'width' => $project->thumb_size['width'], 'height' => $project->thumb_size['height'] );
						$image->createThumbnail( $dims, parent::getFilePath().'/thumb.'.$meta_value, $project->chmod );
						
						$dims = array( 'width' => $project->tiny_size['width'], 'height' => $project->tiny_size['height'] );
						$image->createThumbnail( $dims, parent::getFilePath().'/tiny.'.$meta_value, $project->chmod );
					}		
				} elseif ( 'numeric' == $formfield->type || 'currency' == $formfield->type ) {
					$meta_value += 0; // convert value to numeric type
				}
					
					
				if ( is_array($meta_value) ) {
					// form field value is a date
					if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) ) {
						$meta_value = sprintf("%s-%s-%s", $meta_value['year'], $meta_value['month'], $meta_value['day']);
					} elseif ( array_key_exists('hour', $meta_value) && array_key_exists('minute', $meta_value) ) {
						$meta_value = sprintf("%s:%s", $meta_value['hour'], $meta_value['minute']);
					}
				}
					
				if ( 1 == $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = '".$dataset_id."' AND `form_id` = '".$meta_id."'" ) )
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '%s' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", maybe_serialize($meta_value), $dataset_id, $meta_id ) );
				else
					$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '%s' )", $meta_id, $dataset_id, maybe_serialize($meta_value) ) );
			}
		
			// Check for unsbumitted form data, e.g. checkbox lis
			if ($form_fields = parent::getFormFields()) {
				foreach ( $form_fields AS $form_field ) {
					if ( !array_key_exists($form_field->id, $dataset_meta) ) {
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", $dataset_id, $form_field->id ) );
					}
				}
			}
		}
			
			
		// Delete Image if option is checked
		if ($del_image) {
			$wpdb->query($wpdb->prepare("UPDATE {$wpdb->projectmanager_dataset} SET `image` = '' WHERE `id` ='%d'", $dataset_id) );
			$this->delImage( $image_file );
		}
			
		if ( isset($_FILES['projectmanager_image']) ) {
			if ( is_array($_FILES['projectmanager_image']['name']) ) {
				$file = array(
					'name' => $_FILES['projectmanager_image']['name'][$dataset_id],
					'tmp_name' => $_FILES['projectmanager_image']['tmp_name'][$dataset_id],
					'size' => $_FILES['projectmanager_image']['size'][$dataset_id],
					'type' => $_FILES['projectmanager_image']['type'][$dataset_id],
					);
			} else {
				$file = $_FILES['projectmanager_image'];
			}

			if ( !empty($file['name']) ) 
				$this->uploadImage($dataset_id, $file, $overwrite_image);
		}
			
		$this->setMessage( __('Dataset updated.', 'projectmanager') );
		
		do_action('projectmanager_edit_dataset', $dataset_id);
	}
		
	
  /**
   * duplicate dataset
   * 
   * @param int $dataset_id
   * @return boolean
   */
  function duplicateDataset( $dataset_id )
  {
    global $projectmanager, $wpdb;
	$dataset_id = intval($dataset_id);
    $dataset = $projectmanager->getDataset( $dataset_id );
    $meta = $projectmanager->getDatasetMeta( $dataset_id );
    
    $meta_data = array();
    foreach ( $meta AS $m ) {
      $meta_data[$m->form_field_id] = $m->value;
    }
    
    $this->addDataset($dataset->project_id, $dataset->name, maybe_unserialize($dataset->cat_ids), $meta_data);
    $id = $wpdb->insert_id;
    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", $dataset->image, $id ) );
    
    return true;
  }
  
                	
	/**
	 * delete dataset
	 *
	 * @param int $dataset_id
	 * @return void;
	 */
	function delDataset( $dataset_id )
	{
		global $wpdb, $current_user, $projectmanager;
			
		$dataset_id = intval($dataset_id);
		$dataset = $projectmanager->getDataset($dataset_id); 

		if ( !current_user_can('delete_datasets') || ( !current_user_can('delete_other_datasets') && $dataset->user_id != $current_user->ID ) ) 
			return;
		
			
		// Delete files
		$this->delImage( $dataset->image );
		foreach ( parent::getDatasetMeta($dataset_id) AS $dataset_meta ) {
			if ( 'file' == $dataset_meta->type || 'video' == $dataset_meta->type) {
				@unlink(parent::getFilePath($dataset_meta->value));
			} elseif ( 'image' == $dataset_meta->type ) {
				@unlink(parent::getFilePath($dataset_meta->value));
				@unlink(parent::getFilePath("thumb.".$dataset_meta->value));
				@unlink(parent::getFilePath("tiny.".$dataset_meta->value));
			}
		}
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = '%d'", $dataset_id) );
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->projectmanager_dataset} WHERE `id` ='%d'", $dataset_id) );
		
		do_action('projectmanager_del_dataset', $dataset_id);
	}
	
	
	/**
	 * delete image along with thumbnails from server
	 *
	 * @param string $image
	 * @return void
	 *
	 */
	function delImage( $image )
	{
		@unlink( parent::getFilePath($image) );
		@unlink( parent::getFilePath('/thumb.'.$image) );
		@unlink( parent::getFilePath('/tiny.'.$image) );
	}
	
	
	/**
	 * set image path in database and upload image to server
	 *
	 * @param int $dataset_id
	 * @param array $file
	 * @param boolean $overwrite_image
	 * @return void | string
	 */
	function uploadImage( $dataset_id, $file, $overwrite = false )
	{
		global $wpdb;
		
		require_once (PROJECTMANAGER_PATH . '/lib/image.php');

		$project = $this->project;
		$dataset_id = intval($dataset_id);
		
		$new_file = parent::getFilePath().'/'.basename($file['name']);
		$image = new ProjectManagerImage($new_file);
		if ( $image->supported($file['name']) ) {
			if ( $file['size'] > 0 ) {
				if ( file_exists($new_file) && !$overwrite ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", basename($file['name']), $dataset_id ) );
					$this->setMessage( __('File exists and is not uploaded. Set the overwrite option if you want to replace it.','projectmanager'), true );
					$this->error = true;
				} else {
					if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
						if ( $dataset = parent::getDataset($dataset_id) )
							if ( $dataset->image != '' && $dataset->image != basename($file['name']) ) $this->delImage($dataset->image);

						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", basename($file['name']), $dataset_id ) );
			
						if (file_exists($new_file)) {
							// Resize original file and create thumbnails
							$dims = array( 'width' => $project->medium_size['width'], 'height' => $project->medium_size['height'] );
							$image->createThumbnail( $dims, $new_file, $project->chmod );

							$dims = array( 'width' => $project->thumb_size['width'], 'height' => $project->thumb_size['height'] );
							$image->createThumbnail( $dims, parent::getFilePath().'/thumb.'.basename($file['name']), $project->chmod );
							
							$dims = array( 'width' => $project->tiny_size['width'], 'height' => $project->tiny_size['height'] );
							$image->createThumbnail( $dims, parent::getFilePath().'/tiny.'.basename($file['name']), $project->chmod );
						}
					} else {
						$this->setMessage( sprintf( __('The uploaded file could not be moved to %s.' ), parent::getFilePath() ), true );
						$this->error = true;
					}
				}
			}
		} else {
			$this->setMessage( __('The file type is not supported.','projectmanager'), true );
			$this->error = true;
		}
	}
	
	
	/**
	 * Upload file to webserver
	 * 
	 */
	function uploadFile( $file, $overwrite = false )
	{
		$new_file = parent::getFilePath().'/'.basename($file['name']);
		if ( file_exists($new_file) && !$overwrite ) {
			$this->setMessage( __('File exists and is not uploaded. Set the overwrite option if you want to replace it.','projectmanager'), true );
		} else {
			if ( !move_uploaded_file($file['tmp_name'], $new_file) ) {
				$this->setMessage( sprintf( __('The uploaded file could not be moved to %s.' ), parent::getFilePath() ), true );
			} else {
				return $new_file;
			}
		}
		
		return false;
	}
	
	
	/**
	 * Set File for editing datasets
	 * 
	 * @param array $file
	 * @param boolean $overwrite
	 * @param boolean $del_file
	 * @return string
	 */
	function editFile( $file, $overwrite, $del )
	{
		if ( $del )
			@unlink(parent::getFilePath(basename($file['current'])));
						
		if ( !empty($file['name']) ) {
			$overwrite = isset($overwrite) ? true : false;
			$this->uploadFile($file, $overwrite);
		}
		if ( $del )
			$meta_value = '';
		else
			$meta_value = !empty($file['name']) ? basename($file['name']) : $file['current'];
			
		return $meta_value;
	}
	
	
	/**
	 * add new Form Field
	 *
	 * @param int $project_id
	 * @param array $formfield
	 * @param 
	 */
	function addFormField( $project_id, $formfield = false )
	{
		global $wpdb;
		
		if ( !current_user_can('edit_formfields') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}
		
		$project_id = intval($project_id);
		
		if (!$formfield) {
			$formfield = array();
			$formfield['name'] = '';
			$formfield['type'] = 'text'; 
			$formfield['options'] = '';
		}		
		$order_by = isset($formfield['orderby']) ? 1 : 0;
		$mandatory = isset($formfield['mandatory']) ? 1 : 0;
		$unique = isset($formfield['unique']) ? 1 : 0;
		$private = isset($formfield['private']) ? 1 : 0;
		$show_on_startpage = isset($formfield['show_on_startpage']) ? 1 : 0;
		$show_in_profile = isset($formfield['show_in_profile']) ? 1 : 0;

		
		// get maximum order number
		$max_order_sql = $wpdb->prepare("SELECT MAX(`order`) AS `order` FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = '%d'", $project_id);
		if (isset($formfield['order']) && $formfield['order'] != '') {
			$order = $formfield['order'];
		} else {
			$max_order_sql = $wpdb->get_results($max_order_sql, ARRAY_A);
			$order = $max_order_sql[0]['order'] +1;
		}
				
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_projectmeta} (`label`, `type`, `show_on_startpage`, `show_in_profile`, `order`, `order_by`, `mandatory`, `unique`, `private`, `options`, `project_id`) VALUES ( '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%d');", $formfield['name'], $formfield['type'], $show_on_startpage, $show_in_profile, $order, $order_by, $mandatory, $unique, $private, $formfield['options'], $project_id ) );
		$formfield_id = $wpdb->insert_id;
				
		/*
		* Populate default values for every dataset
		*/
		if ( $datasets = $wpdb->get_results( $wpdb->prepare("SELECT `id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = '%d'", $project_id) ) ) {
			foreach ( $datasets AS $dataset ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '' );", $formfield_id, $dataset->id ) );
			}
		}
	}
	
	
	/**
	 * edit Form Field
	 *
	 * @param array $formfield
	 */
	function editFormField( $formfield )
	{
		global $wpdb;
		
		if ( !current_user_can('edit_formfields') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		// make sure $formfield_id is numeric
		$formfield_id = intval($formfield['id']);
		
		$order_by = isset($formfield['orderby']) ? 1 : 0;
		$mandatory = isset($formfield['mandatory']) ? 1 : 0;
		$unique = isset($formfield['unique']) ? 1 : 0;
		$private = isset($formfield['private']) ? 1 : 0;
		$show_on_startpage = isset($formfield['show_on_startpage']) ? 1 : 0;
		$show_in_profile = isset($formfield['show_in_profile']) ? 1 : 0;
					
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projectmeta} SET `label` = '%s', `type` = '%s', `show_on_startpage` = '%d', `show_in_profile` = '%d', `order` = '%d', `order_by` = '%d', `mandatory` = '%d', `unique` = '%d', `private` = '%d', `options` = '%s' WHERE `id` = '%d' LIMIT 1 ;", $formfield['name'], $formfield['type'], $show_on_startpage, $show_in_profile, $formfield['order'], $order_by, $mandatory, $unique, $private, $formfield['options'], $formfield_id ) );
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `form_id` = '%d' WHERE `form_id` = '%d'", $formfield_id, $formfield_id ) );
	}
	
	
	/**
	 * delete Form Field
	 *
	 * @param int $formfield_id
	 */
	function delFormField( $formfield_id ) 
	{
		global $wpdb;
		
		if ( !current_user_can('edit_formfields') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}
		
		// make sure $formfield_id is numeric
		$formfield_id = intval($formfield_id);
		
		// delete formfield metadata from options
		$options = get_option('projectmanager');
		unset($options['form_field_options'][$formfield_id]);
		update_option('projectmanager', $options);
		
		// delete formfield and formfield data
		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `id` = '%d'", $formfield_id) );
		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->projectmanager_datasetmeta} WHERE `form_id` = '%d'", $formfield_id) );
		
		//$this->setMessage( __('Formfield deleted', 'projectmanager') );
	}
	
	
	/**
	 * save Form Fields
	 *
	 * @param int $project_id
	 * @param array $formfields
	 * @param array $new_formfields
	 *
	 * @return string
	 */
	function setFormFields( $project_id, $formfields, $new_formfields )
	{
		global $wpdb;
		
		if ( !current_user_can('edit_formfields') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		$project_id = intval($project_id);

		if ( !empty($formfields) ) {
			foreach ( $wpdb->get_results( "SELECT `id`, `project_id` FROM {$wpdb->projectmanager_projectmeta}" ) AS $form_field) {
				if ( !array_key_exists( $form_field->id, $formfields ) ) {
					if ( $project_id == $form_field->project_id )
						$this->delFormField($form_field->id);
				}
			}
				
			foreach ( $formfields AS $id => $formfield ) {
				$formfield['id'] = $id;
				$this->editFormField( $formfield );
			}
		} else {
			$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = '%d'", $project_id)  );
		}
			
		if ( !empty($new_formfields) ) {
			foreach ($new_formfields AS $tmp_id => $formfield) {
				$this->addFormField( $project_id, $formfield);
			}
		}
		
		$this->setMessage( __('Form Fields updated', 'projectmanager') );
		
		do_action('projectmanager_save_formfields', $project_id);
	}
	
	
	/**
	 * print breadcrumb navigation
	 *
	 * @param int $project_id
	 * @param string $page_title
	 * @param boolean $start
	 */
	function printBreadcrumb( $page_title, $start=false )
	{
		global $projectmanager;
		$project = $projectmanager->getProject($projectmanager->getProjectID());

		//if ( 1 != $project->navi_link ) {
			echo '<p class="projectmanager_breadcrumb">';
			if ( !isset($this->single) || !$this->single )
				echo '<a href="admin.php?page=projectmanager">'.__( 'Projectmanager', 'projectmanager' ).'</a> &raquo; ';
			
			if ( $page_title != $project->title )
				echo '<a href="admin.php?page=projectmanager&subpage=show-project&amp;project_id='.$project->id.'">'.$project->title.'</a> &raquo; ';
			
			if ( !$start || ($start && (!isset($this->single) || !$this->single)) ) echo $page_title;
			
			echo '</p>';
		//}
	}
	
	
	/**
	 * hook dataset input fields into profile
	 *
	 * @param none
	 */
	function profileHook()
	{
		global $current_user, $wpdb, $projectmanager;
		
		if ( !current_user_can('projectmanager_user') )
			return;

		$options = get_option('projectmanager');

		$projects = array();
		foreach ( $projectmanager->getProjects() AS $project ) {
			if ( isset($project->profile_hook) && 1 == $project->profile_hook ) 
				$projects[] = $project->id;
		}

		if ( !empty($projects) ) {
			foreach ( $projects AS $project_id ) {
				$this->project_id = $project_id;
				$projectmanager->init($this->project_id);
				$project = $projectmanager->getProject();
			
				$is_profile_page = true;
				$dataset = $wpdb->get_results( $wpdb->prepare("SELECT `id`, `name`, `image`, `cat_ids`, `user_id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = '%d' AND `user_id` = '%d' LIMIT 0,1", $this->project_id, intval($current_user->ID)) );
				$dataset = $dataset[0];
					
				if ( $dataset ) {
					$dataset_id = $dataset->id;
					$cat_ids = $projectmanager->getSelectedCategoryIDs($dataset);
					$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );
		
					$img_filename = $dataset->image;
					$meta_data = array();
					foreach ( $dataset_meta AS $meta ) {
						if ( is_string($meta->value) )
							$meta_data[$meta->form_field_id] = htmlspecialchars(stripslashes_deep($meta->value), ENT_QUOTES);
						else
							$meta_data[$meta->form_field_id] = stripslashes_deep($meta->value);
					}
					
					echo '<h3>'.$projectmanager->getProjectTitle().'</h3>';
					echo '<input type="hidden" name="project_id['.$dataset_id.']" value="'.$project_id.'" /><input type="hidden" name="dataset_id[]" value="'.$dataset_id.'" /><input type="hidden" name="dataset_user_id" value="'.$current_user->ID.'" />';
				
					$projectmanager->loadTinyMCE();
					include( dirname(__FILE__). '/dataset-form.php' );
				}
			}
		}
	}
	
	
	/**
	 * update Profile settings
	 *
	 * @param none
	 * @return none
	 */
	function updateProfile($user_id)
	{
		//$user_id = intval($_POST['dataset_user_id']);

		foreach ( (array)$_POST['dataset_id'] AS $id ) {
			$id = intval($id);
			$del_image = isset( $_POST['del_old_image'][$id] ) ? true : false;
			$overwrite_image = ( isset($_POST['overwrite_image'][$id]) && 1 == $_POST['overwrite_image'][$id] ) ? true: false;
			$this->editDataset( $_POST['project_id'][$id], $_POST['display_name'], $_POST['post_category'][$id], $id, $_POST['form_field'][$id], $user_id, $del_image, $_POST['image_file'][$id], $overwrite_image );
		}
	}
}
?>