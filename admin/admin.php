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
	 * load admin area
	 *
	 * @param none
	 * @return void
	 */
	function __construct($project_id)
	{
		require_once( ABSPATH . 'wp-admin/includes/template.php' );
		add_action( 'admin_menu', array(&$this, 'menu') );
		
		add_action('admin_print_scripts', array(&$this, 'loadScripts') );
		add_action('admin_print_styles', array(&$this, 'loadStyles') );
		
		$this->project_id = $project_id;
	}
	function LeagueManagerAdmin()
	{
		$this->__construct();
	}
	
	
	/**
	 * adds menu to the admin interface
	 *
	 * @param none
	 */
	function menu()
	{
		if ( $projects = parent::getProjects() ) {
			$options = get_option( 'projectmanager' );
			foreach( $projects AS $project ) {
				if ( 1 == $options['project_options'][$project->id]['navi_link'] ) {
					$icon = $options['project_options'][$project->id]['menu_icon'];
					if ( function_exists('add_object_page') )
						add_object_page( $project->title, $project->title, 'manage_projects', 'project_' . $project->id, array(&$this, 'display'), $this->getIconURL($icon) );
					else
						add_menu_page( $project->title, $project->title, 'manage_projects', 'project_' . $project->id, array(&$this, 'display'), $this->getIconURL($icon) );

					add_submenu_page('project_' . $project->id, __($project->title, 'projectmanager'), __('Overview','projectmanager'),'manage_projects', 'project_' . $project->id, array(&$this, 'display'));
					add_submenu_page('project_' . $project->id, __( 'Add Dataset', 'projectmanager' ), __( 'Add Dataset', 'projectmanager' ), 'manage_projects', 'project-dataset_' . $project->id, array(&$this, 'display'));
					add_submenu_page('project_' . $project->id, __( 'Form Fields', 'projectmanager' ), __( 'Form Fields', 'projectmanager' ), 'manage_projects', 'project-formfields_' . $project->id, array(&$this, 'display'));
					add_submenu_page('project_' . $project->id, __( 'Settings', 'projectmanager' ), __( 'Settings', 'projectmanager' ), 'manage_projects', 'project-settings_' . $project->id, array(&$this, 'display'));
					add_submenu_page('project_' . $project->id, __('Categories'), __('Categories'), 'manage_projects', 'categories.php');
					add_submenu_page('project_' . $project->id, __('Import/Export', 'projectmanager'), __('Import/Export', 'projectmanager'), 'manage_projects', 'project-import_' . $project->id, array(&$this, 'display'));
				}
			}
		}
		
		// Add global Projects Menu
		if ( function_exists('add_object_page') )
			add_object_page( __('Projects','projectmanager'), __('Projects', 'projectmanager'), 'manage_projects', PROJECTMANAGER_PATH, array(&$this, 'display'), PROJECTMANAGER_URL.'/admin/icons/menu/databases.png');
		else
			add_menu_page(__('Projects', 'projectmanager'), __('Projects', 'projectmanager'), 'manage_projects', PROJECTMANAGER_PATH,array(&$this, 'display'), PROJECTMANAGER_URL.'/admin/icons/menu/databases.png');

		add_submenu_page(PROJECTMANAGER_PATH, __('Projects', 'projectmanager'), __('Overview','projectmanager'),'manage_projects', PROJECTMANAGER_PATH,array(&$this, 'display'));
		add_submenu_page(PROJECTMANAGER_PATH, __( 'Settings'), __('Settings'), 'manage_projects', 'projectmanager-settings', array( &$this, 'display') );
		
		$plugin = 'projectmanager/projectmanager.php';
		add_filter( 'plugin_action_links_' . $plugin, array( &$this, 'pluginActions' ) );
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
		if( !isset($options['dbversion']) || $options['dbversion'] != PROJECTMANAGER_DBVERSION ) {
			include_once ( dirname (__FILE__) . '/upgrade.php' );
			projectmanager_upgrade_page();
			return;
		}

		switch ($_GET['page']) {
			case 'projectmanager-settings':
				$this->displayOptionsPage();
				break;
			case 'projectmanager':
				switch($_GET['subpage']) {
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
				$projectmanager->initialize($page[1]);
							
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
		wp_register_script( 'projectmanager', PROJECTMANAGER_URL.'/admin/js/functions.js', array( 'colorpicker', 'sack', 'scriptaculous', 'prototype' ), PROJECTMANAGER_VERSION );
		wp_register_script( 'projectmanager_formfields', PROJECTMANAGER_URL.'/admin/js/formfields.js', array( 'projectmanager', 'thickbox' ), PROJECTMANAGER_VERSION );
		wp_register_script ('projectmanager_ajax', PROJECTMANAGER_URL.'/admin/js/ajax.js', array( 'projectmanager' ), PROJECTMANAGER_VERSION );
		
		wp_enqueue_script( 'projectmanager_formfields' );
		wp_enqueue_script( 'projectmanager_ajax');
			
		echo "<script type='text/javascript'>\n";
		echo "var PRJCTMNGR_HTML_FORM_FIELD_TYPES = \"";
		foreach (parent::getFormFieldTypes() AS $form_type_id => $form_type) {
			$field_name = is_array($form_type) ? $form_type['name'] : $form_type;
			echo "<option value='".$form_type_id."'>".$field_name."</option>";
		}
		echo "\";\n";
			
		?>
		//<![CDATA[
		ProjectManagerAjaxL10n = {
			blogUrl: "<?php bloginfo( 'wpurl' ); ?>", pluginPath: "<?php echo PROJECTMANAGER_PATH; ?>", pluginUrl: "<?php echo PROJECTMANAGER_URL; ?>", requestUrl: "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php", imgUrl: "<?php echo PROJECTMANAGER_URL; ?>/images", Edit: "<?php _e("Edit"); ?>", Post: "<?php _e("Post"); ?>", Save: "<?php _e("Save"); ?>", Cancel: "<?php _e("Cancel"); ?>", pleaseWait: "<?php _e("Please wait..."); ?>", Revisions: "<?php _e("Page Revisions"); ?>", Time: "<?php _e("Insert time"); ?>", Options: "<?php _e("Options", "projectmanager") ?>", Delete: "<?php _e('Delete', 'projectmanager') ?>", delFile: "<?php _e('Delete File', 'projectmanager')?>"
			   }
		//]]>
		<?php
		echo "</script>\n";
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
		
		if ( current_user_can( 'projectmanager_admin' ) ) {
			if ( isset($_POST['updateProjectManager']) ) {
				check_admin_referer('projetmanager_manage-global-league-options');
				$options['colors']['headers'] = $_POST['color_headers'];
				$options['colors']['rows'] = array( $_POST['color_rows_alt'], $_POST['color_rows'] );
				
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
	function getIconURL( $icon )
	{
		if ( file_exists(TEMPLATEPATH . "/projectmanager/icons/".$icon))
			return get_template_directory_uri() . "/projectmanager/icons/".$icon;
		elseif ( file_exists(PROJECTMANAGER_URL.'/admin/icons/menu/'.$icon) )
			return PROJECTMANAGER_URL.'/admin/icons/menu/'.$icon;
		else
			return PROJECTMANAGER_URL.'/admin/icons/menu/databases.png';
	}
	
	
	/**
	 * check if there is only a single project
	 *
	 * @param none
	 * @return boolean
	 */
	function isSingle()
	{
		$options = get_option( 'projectmanager' );
		$this->single = false;
		$projects = parent::getProjects();
		foreach ( $projects AS $project ) {
			if ( 1 == $options['project_options'][$project->id]['navi_link'] && parent::getNumProjects() == 1) {
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
		$walker = new Walker_Category_Checklist;
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
		$options = array( 'order' => __('Manual', 'projectmanager'), 'id' => __('ID', 'projectmanager'), 'name' => __('Name','projectmanager'), 'formfields' => __('Formfields', 'projectmanager') );
		
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
	
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_projects} (title) VALUES ('%s')", $title ) );
		$this->setMessage( __('Project added','projectmanager') );
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
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projects} SET `title` = '%s' WHERE `id` = '%d'", $title, $project_id ) );
		$this->setMessage( __('Project updated','projectmanager') );
	}
	
	
	/**
	 * delete project
	 *
	 * @param int  $project_id
	 * @return void
	 */
	function delProject( $project_id )
	{
		global $wpdb;
		
		foreach ( parent::getDatasets() AS $dataset )
			$this->delDataset( $dataset->id );
		
		$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$project_id}" );
		$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projects} WHERE `id` = {$project_id}" );
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
		if ( $file['size'] > 0 ) {
			/*
			* Upload CSV file to image directory, temporarily
			*/
			$new_file =  parent::getFilePath().'/'.basename($file['name']);
			if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
				$handle = @fopen($new_file, "r");
				if ($handle) {
					if ( "TAB" == $delimiter ) $delimiter = "\t"; // correct tabular delimiter
					
					$i = 0; // initialize dataset counter
					while (!feof($handle)) {
						$buffer = fgets($handle, 4096);
						$line = explode($delimiter, $buffer);
						$name = $line[0];
						// assign column values to form fields
						foreach ( $cols AS $col => $form_field_id ) {
							$meta[$form_field_id] = $line[$col];
						}
						
						if ( $line && !empty($name) ) {
							$this->addDataset($project_id, $name, array(), $meta);
							$i++;
						}
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
	 * export datasets to CSV
	 *
	 * @param int $project_id
	 * @return file
	 */
	function exportDatasets( $project_id )
	{
		global $projectmanager;
		
		$this->project_id = $project_id;
		$projectmanager->initialize($project_id);
		$project = $projectmanager->getProject();
			
		$filename = $project->title."_".date("Y-m-d").".csv";
		/*
		* Generate Header
		*/
		$contents = __('Name','projectmanager')."\t".__('Categories','projectmanager');
		foreach ( $projectmanager->getFormFields() AS $form_field )
			$contents .= "\t".$form_field->label;
		
		foreach ( $projectmanager->getDatasets() AS $dataset ) {
			$contents .= "\n".$dataset->name."\t".$projectmanager->getSelectedCategoryTitles(maybe_unserialize($dataset->cat_ids));

			foreach ( $projectmanager->getDatasetMeta( $dataset->id ) AS $meta ) {
				// Remove line breaks
				$meta->value = str_replace("\r\n", "", stripslashes($meta->value));
				$contents .= "\t".$meta->value;
			}
		}
		
		header('Content-Type: text/csv');
    		header('Content-Disposition: inline; filename="'.$filename.'"');
		echo $contents;
		exit();
	}
	
	
	/**
	 * add new dataset
	 *
	 * @param int $project_id
	 * @param string $name
	 * @param array $cat_ids
	 * @param array $dataset_meta
	 * @return string
	 */
	function addDataset( $project_id, $name, $cat_ids, $dataset_meta = false )
	{
		global $wpdb, $current_user;
		$this->project_id = $project_id;

		if ( current_user_can( 'manage_projects') ) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_dataset} (name, cat_ids, project_id, user_id) VALUES ('%s', '%s', '%d', '%d')", $name, maybe_serialize($cat_ids), $project_id, $current_user->ID ) );
			$dataset_id = $wpdb->insert_id;
				
			if ( $dataset_meta ) {
				foreach ( $dataset_meta AS $meta_id => $meta_value ) {
					$formfield = parent::getFormFields($meta_id);
					
					// Manage file upload
					if ( 'fileupload' == $formfield->type ) {
						$file = array('name' => $_FILES['form_field']['name'][$meta_id], 'tmp_name' => $_FILES['form_field']['tmp_name'][$meta_id], 'size' => $_FILES['form_field']['size'][$meta_id], 'type' => $_FILES['form_field']['type'][$meta_id]);
						if ( !empty($file['name']) )
							$this->uploadFile($file);
							
						$meta_value = basename($file['name']);
					}
					
					if ( is_array($meta_value) ) {
						// form field value is a date
						if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) )
							$meta_value = $meta_value['year'].'-'.str_pad($meta_value['month'], 2, 0, STR_PAD_LEFT).'-'.str_pad($meta_value['day'], 2, 0, STR_PAD_LEFT);
						else
							$meta_value = implode(",", $meta_value);
					}

					$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ('%d', '%d', '%s')", $meta_id, $dataset_id, $meta_value ) );
				}
			}
			
			// Check for unsubmitted form data, e.g. checkbox list
			if ($form_fields = parent::getFormFields()) {
				foreach ( $form_fields AS $form_field ) {
					if ( !array_key_exists($form_field->id, $dataset_meta) ) {
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", $dataset_id, $form_field->id ) );
					}
				}
			}
		
			if ( isset($_FILES['projectmanager_image']) && $_FILES['projectmanager_image']['name'] != ''  )
				$this->uploadImage($dataset_id, $_FILES['projectmanager_image']);
				
			$this->setMessage( __( 'New dataset added to the database.', 'projectmanager' ) );
		} else {
			$this->setmessage( __( "You don't have the permission to add datasets", "projectmanager" ), true );
		}
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
	function editDataset( $project_id, $name, $cat_ids, $dataset_id, $dataset_meta = false, $user_id, $del_image = false, $image_file = '', $overwrite_image = false, $owner = false )
	{
		global $wpdb, $current_user;
		$this->project_id = $project_id;

		if ( current_user_can( 'manage_projects') ) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `name` = '%s', `cat_ids` = '%s' WHERE `id` = '%d'", $name, maybe_serialize($cat_ids), $dataset_id ) );
			
			// Change Dataset owner if supplied
			if ( $owner )
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `user_id` = '%d' WHERE `id` = '%d'", $owner, $dataset_id ) );
			
			if ( $dataset_meta ) {
				foreach ( $dataset_meta AS $meta_id => $meta_value ) {
					$formfield = parent::getFormFields($meta_id);
					
					// Manage file upload
					if ( 'fileupload' == $formfield->type ) {
						$file = array('name' => $_FILES['form_field']['name'][$meta_id], 'tmp_name' => $_FILES['form_field']['tmp_name'][$meta_id], 'size' => $_FILES['form_field']['size'][$meta_id], 'type' => $_FILES['form_field']['type'][$meta_id], 'current' => $meta_value['current']);
						$delete = (1 == $meta_value['del']) ? true : false;
						$meta_value = $this->editFile($file, $meta_value['overwrite'], $delete);
					}
					
					
					if ( is_array($meta_value) ) {
						// form field value is a date
						if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) )
							$meta_value = $meta_value['year'].'-'.str_pad($meta_value['month'], 2, 0, STR_PAD_LEFT).'-'.str_pad($meta_value['day'], 2, 0, STR_PAD_LEFT);
						else
							$meta_value = implode(",", $meta_value);
					}
					
					if ( 1 == $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = '".$dataset_id."' AND `form_id` = '".$meta_id."'" ) )
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '%s' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", $meta_value, $dataset_id, $meta_id ) );
					else
						$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '%s' )", $meta_id, $dataset_id, $meta_value ) );
				}
			}
			
			// Check for unsbumitted form data, e.g. checkbox lis
			if ($form_fields = parent::getFormFields()) {
				foreach ( $form_fields AS $form_field ) {
					if ( !array_key_exists($form_field->id, $dataset_meta) ) {
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", $dataset_id, $form_field->id ) );
					}
				}
			}
			
			// Delete Image if option is checked
			if ($del_image) {
				$wpdb->query("UPDATE {$wpdb->projectmanager_dataset} SET `image` = '' WHERE `id` = {$dataset_id}");
				$this->delImage( $image_file );
			}
				
			if ( isset($_FILES['projectmanager_image']) && $_FILES['projectmanager_image']['name'] != '' )
				$this->uploadImage($dataset_id, $_FILES['projectmanager_image'], $overwrite_image);
			
			$this->setmessage( __('Dataset updated.', 'projectmanager') );
		} else {
			$this->setmessage( __( "You don't have the permission to edit this dataset", "projectmanager" ), true );
		}
	}
		
		
	/**
	 * delete dataset
	 *
	 * @param int $dataset_id
	 * @return void;
	 */
	function delDataset( $dataset_id )
	{
		global $wpdb;
			
		if ( $dataset = $wpdb->get_results( "SELECT `image` FROM {$wpdb->projectmanager_dataset} WHERE `id` = {$dataset_id}" ) )
			$img = $dataset[0]->image;
			
		$this->delImage( $img );
		foreach ( parent::getDatasetMeta($dataset_id) AS $dataset_meta ) {
			if ( 'fileupload' == $dataset_meta->type )
				@unlink(parent::getFilePath($dataset_meta->value));
		}
		$wpdb->query("DELETE FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = {$dataset_id}");
		$wpdb->query("DELETE FROM {$wpdb->projectmanager_dataset} WHERE `id` = {$dataset_id}");
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
		
		$new_file = parent::getFilePath().'/'.basename($file['name']);
		$image = new ProjectManagerImage($new_file);
		if ( $image->supported($file['name']) ) {
			if ( $file['size'] > 0 ) {
				$options = get_option('projectmanager');
				$options = $options['project_options'][$this->project_id];
				
				if ( file_exists($new_file) && !$overwrite ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", basename($file['name']), $dataset_id ) );
					$this->setMessage( __('File exists and is not uploaded. Set the overwrite option if you want to replace it.','projectmanager'), true );
				} else {
					if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
						if ( $dataset = parent::getDataset($dataset_id) )
							if ( $dataset->image != '' ) $this->delImage($dataset->image);

						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", basename($file['name']), $dataset_id ) );
			
						// Resize original file and create thumbnails
						$dims = array( 'width' => $options['medium_size']['width'], 'height' => $options['medium_size']['height'] );
						$image->createThumbnail( $dims, $new_file, $options['chmod'] );
						
						$dims = array( 'width' => $options['thumb_size']['width'], 'height' => $options['thumb_size']['height'] );
						$image->createThumbnail( $dims, parent::getFilePath().'/thumb.'.basename($file['name']), $options['chmod'] );
						
						$dims = array( 'width' => 80, 'height' => 50 );
						$image->createThumbnail( $dims, parent::getFilePath().'/tiny.'.basename($file['name']), $options['chmod'] );
					} else {
						$this->setMessage( sprintf( __('The uploaded file could not be moved to %s.' ), parent::getFilePath() ), true );
					}
				}
			}
		} else {
			$this->setMessage( __('The file type is not supported.','projectmanager'), true );
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
			}
		}
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
	 * save Form Fields
	 *
	 * @param int $project_id
	 * @param array $form_name
	 * @param array $form_type
	 * @param array $form_show_on_startpage
	 * @param array $form_show_in_profile
	 * @param array $form_order
	 * @param array $form_order_by
	 * @param array $new_form_name
	 * @param array $new_form_type
	 * @param array $new_show_in_profile
	 * @param array $new_form_order
	 * @param array $new_form_order_by
	 *
	 * @return string
	 */
	function setFormFields( $project_id, $form_name, $form_type, $form_show_on_startpage, $form_show_in_profile, $form_order, $form_order_by, $new_form_name, $new_form_type, $new_form_show_on_startpage, $new_form_show_in_profile, $new_form_order, $new_form_order_by )
	{
		global $wpdb;
		
		$options = get_option('projectmanager');
		if ( null != $form_name ) {
			foreach ( $wpdb->get_results( "SELECT `id`, `project_id` FROM {$wpdb->projectmanager_projectmeta}" ) AS $form_field) {
				if ( !array_key_exists( $form_field->id, $form_name ) ) {
					$del = (bool) $wpdb->query( "DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `id` = {$form_field->id} AND `project_id` = {$project_id}"  );
					if ( $del ) unset($options['form_field_options'][$form_field->id]);
					if ( $project_id == $form_field->project_id )
						$wpdb->query( "DELETE FROM {$wpdb->projectmanager_datasetmeta} wHERE `form_id` = {$form_field->id}" );
				}
			}
				
			foreach ( $form_name AS $form_id => $form_label ) {
				$type = $form_type[$form_id];
				$order = $form_order[$form_id];
				$order_by = isset($form_order_by[$form_id]) ? 1 : 0;
				$show_on_startpage = isset($form_show_on_startpage[$form_id]) ? 1 : 0;
				$show_in_profile = isset($form_show_in_profile[$form_id]) ? 1 : 0;
					
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projectmeta} SET `label` = '%s', `type` = '%s', `show_on_startpage` = '%d', `show_in_profile` = '%d', `order` = '%d', `order_by` = '%d' WHERE `id` = '%d' LIMIT 1 ;", $form_label, $type, $show_on_startpage, $show_in_profile, $order, $order_by, $form_id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `form_id` = '%d' WHERE `form_id` = '%d'", $form_id, $form_id ) );
			}
		}
			
		if ( null != $new_form_name ) {
			foreach ($new_form_name AS $tmp_form_id => $form_label) {
				$type = $new_form_type[$tmp_form_id];
				$order_by = isset($new_form_order_by[$tmp_form_id]) ? 1 : 0;
				$show_on_startpage = isset($new_form_show_on_startpage[$tmp_form_id]) ? 1 : 0;
				$show_in_profile = isset($new_form_show_in_profile[$tmp_form_id]) ? 1 : 0;
				
				$max_order_sql = "SELECT MAX(`order`) AS `order` FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$project_id};";
				if ($new_form_order[$tmp_form_id] != '') {
					$order = $new_form_order[$tmp_form_id];
				} else {
					$max_order_sql = $wpdb->get_results($max_order_sql, ARRAY_A);
					$order = $max_order_sql[0]['order'] +1;
				}
				
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_projectmeta} (`label`, `type`, `show_on_startpage`, `show_in_profile`, `order`, `order_by`, `project_id`) VALUES ( '%s', '%s', '%d', '%d', '%d', '%d', '%d');", $form_label, $type, $show_on_startpage, $show_in_profile, $order, $order_by, $project_id ) );
				$form_id = mysql_insert_id();
					
				// Redirect form field options to correct $form_id if present
				if ( isset($options['form_field_options'][$tmp_form_id]) ) {
					$options['form_field_options'][$form_id] = $options['form_field_options'][$tmp_form_id];
					unset($options['form_field_options'][$tmp_form_id]);
				}
				
				/*
				* Populate default values for every dataset
				*/
				if ( $datasets = $wpdb->get_results( "SELECT `id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$project_id}" ) ) {
					foreach ( $datasets AS $dataset ) {
						$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '' );", $form_id, $dataset->id ) );
					}
				}
			}
		}
		
		update_option('projectmanager', $options);
		$this->setMessage( __('Form Fields updated', 'projectmanager') );
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
		$options = get_option('projectmanager');
		if ( 1 != $options['project_options'][$projectmanager->getProjectID()]['navi_link'] ) {
			echo '<p class="projectmanager_breadcrumb">';
			if ( !$this->single )
				echo '<a href="admin.php?page=projectmanager">'.__( 'Projectmanager', 'projectmanager' ).'</a> &raquo; ';
			
			if ( $page_title != $projectmanager->getProjectTitle() )
				echo '<a href="admin.php?page=projectmanager&subpage=show-project&amp;project_id='.$projectmanager->getProjectID().'">'.$projectmanager->getProjectTitle().'</a> &raquo; ';
			
			if ( !$start || ($start && !$this->single) ) echo $page_title;
			
			echo '</p>';
		}
	}
	
	
	/**
	 * hook dataset input fields into profile
	 *
	 * @param none
	 */
	function profileHook()
	{
		global $current_user, $wpdb, $projectmanager;
		
		if ( current_user_can('project_user_profile') ) {
			$options = get_option('projectmanager');
			$options = $options['project_options'];
			
			$this->project_id = 0;
			foreach ( $options AS $project_id => $settings ) {
				if ( 1 == $settings['profile_hook'] ) {
					$this->project_id = $project_id;
					break;
				}
			}
			$projectmanager->initialize($this->project_id);
			$options = $options[$this->project_id];
			if ( $this->project_id != 0 ) {
				$projectmanager->getProject();
				
				$is_profile_page = true;
				$dataset = $wpdb->get_results( "SELECT `id`, `name`, `image`, `cat_ids`, `user_id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$this->project_id} AND `user_id` = '".$current_user->ID."' LIMIT 0,1" );
				$dataset = $dataset[0];
				
				if ( $dataset ) {
					$dataset_id = $dataset->id;
					$cat_ids = $projectmanager->getSelectedCategoryIDs($dataset);
					$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );
		
					$img_filename = $dataset->image;
					$meta_data = array();
					foreach ( $dataset_meta AS $meta )
						$meta_data[$meta->form_field_id] = htmlspecialchars(stripslashes_deep($meta->value),ENT_QUOTES);
				} else {
					$dataset_id = ''; $cat_ids = array(); $img_filename = ''; $meta_data = array();
				}
				
				echo '<h3>'.$projectmanager->getProjectTitle().'</h3>';
				echo '<input type="hidden" name="project_id" value="'.$this->project_id.'" /><input type="hidden" name="dataset_id" value="'.$dataset_id.'" /><input type="hidden" name="dataset_user_id" value="'.$current_user->ID.'" />';
				
				include( dirname(__FILE__). '/dataset-form.php' );
			}
		}
	}
	
	
	/**
	 * update Profile settings
	 *
	 * @param none
	 * @return none
	 */
	function updateProfile()
	{
		$user_id = $_POST['dataset_user_id'];
		check_admin_referer('update-user_' . $user_id);
		if ( '' == $_POST['dataset_id'] ) {
			$this->addDataset( $_POST['project_id'], $_POST['display_name'], $_POST['post_category'], $_POST['form_field'] );
		} else {
			$del_image = isset( $_POST['del_old_image'] ) ? true : false;
			$overwrite_image = ( isset($_POST['overwrite_image']) && 1 == $_POST['overwrite_image'] ) ? true: false;
			$this->editDataset( $_POST['project_id'], $_POST['display_name'], $_POST['post_category'], $_POST['dataset_id'], $_POST['form_field'], $user_id, $del_image, $_POST['image_file'], $overwrite_image );
		}
	}
}
