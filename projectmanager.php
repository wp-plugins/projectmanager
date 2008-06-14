<?php

class WP_ProjectManager
{
	/**
	 *
	 */
	var $dir = array( 'templates' => 'wp-content/plugins/projectmanager/templates/', 'images' => 'uploads/projects/', 'thumbs' => 'uploads/projects/thumbs/' );

	
	/**
	 * ID of selected project
	 *
	 * @var int
	 */
	var $project_id;
	
	
	/**
	 * groups
	 *
	 * @var array() or false
	 */
	var $groups;
		
		
	/**
	 * current selected group
	 *
	 * @var int
	 */
	var $group;
		
		
	/**
	 * number of dataset per page
	 *
	 * @var int
	 */
	var $per_page;
		
		
	/**
	 * pagination object
	 *
	 * @var object
	 */
	var $pagination;
		
		
	/**
	 * Constructor of class
	 *
	 * @param
	 */
	function __construct( $project_id = false )
	{
		global $wpdb;
			
		$wpdb->projectmanager_projects = $wpdb->prefix . 'projectmanager_projects';
		$wpdb->projectmanager_projectmeta = $wpdb->prefix . 'projectmanager_projectmeta';
		$wpdb->projectmanager_dataset = $wpdb->prefix . 'projectmanager_dataset';
		$wpdb->projectmanager_datasetmeta = $wpdb->prefix . 'projectmanager_datasetmeta';

		/*
		*  Set plugin url and path
		*/
		$this->plugin_url = get_bloginfo('wpurl').'/'.PLUGINDIR.'/'.basename(__FILE__, ".php");
		$this->plugin_path = ABSPATH.PLUGINDIR.'/'.basename(__FILE__, ".php");
		
		//Save selected group. NULL if none is selected
		$this->setGroup();
	}
	function WP_Manager( $project_id = false )
	{
		$this->__construct( $project_id );
	}
	
	
	/**
	 * set project ID
	 *
	 * @param int $project_id
	 */
	function setSettings( $project_id )
	{
		$this->project_id = $project_id;

		$options = get_option( 'projectmanager' );

		$this->num_cols = $options[$project_id]['num_cols'];
		$this->num_rows = $options[$project_id]['num_rows'];
		$this->per_page = $this->num_cols * $this->num_rows;

		$this->pagination = new Pagination( $this->per_page, $this->getNumDatasets(), array('show') );
	}
	
	
	/**
	* returns supported form field types
	*
	* @param none
	* @return array
	*/
	function getFormFieldTypes()
	{
		$form_field_types = array( 1 => "Text", 2 => "Textfield", 3 => "E-Mail", 4 => "Date", 5 => "URL" );
		return $form_field_types;
	}
	
	/**
	 * returns array of months in appropriate language depending on Wordpress locale
	 *
	 * @param none
	 * @return array
	 */
	function getMonths()
	{
		$locale = get_locale();
		setlocale(LC_ALL, $locale);
		$months = array();
		for ( $month = 1; $month <= 12; $month++ )
			$months[$month] = htmlentities( strftime( "%B", mktime( 0,0,0, $month, date("m"), date("Y") ) ) );
		
		return $months;
	}
	
	
	/**
	 * returns image directory
	 *
	 * @param none
	 * @return string
	 */
	function getImageDir()
	{
		return $this->dir['images'];
	}
	
	
	/**
	 * returns thumbnail directory
	 *
	 * @param none
	 * @return string
	 */
	function getThumbsDir()
	{
		return $this->dir['thumbs'];
	}
		
		
	/**
	 * gets template directory
	 *
	 * @param none
	 * @return string
	 */
	function getTemplateDir()
	{
		return $this->dir['templates'];
	}
	
	
	/**
	 * gets project id
	 *
	 * @param none
	 * @return int
	 */
	function getProjectID()
	{
		return $this->project_id;
	}
	
	
	/**
	 * gets current group
	 * 
	 * @param none
	 * @return int or false
	 */
	function getCurrentGroup()
	{
		return $this->group;
	}
		
		
	/**
 	 * sets current group
	 *
	 * @param int $group_id
	 * @return void
	 */
	function setGroup( $grp_id = false )
	{
		if ( $grp_id )
			$this->group = $grp_id;
		else
			$this->group = ( isset($_GET['grp_id']) AND '' != $_GET['grp_id'] ) ? (int)$_GET['grp_id'] : false;
		
		return;
	}
		
		
	/**
	 * gets supported file types
	 *
	 * @param none
	 * @return array
	 */
	function getSupportedImageTypes()
	{
		require_once( 'image.php' );
		$image = new Image();
		return $image->getSupportedImageTypes();
	}
	
	
	/**
	 * gets object limit per page
	 *
	 * @param none
	 * @return int
	 */
	function getPerPage()
	{
		return $this->per_page;
	}
		
		
	/**
	 * sets number of objects per page
	 *
	 * @param int
	 * @return void
	 */
	function setPerPage($per_page)
	{
		$this->per_page = $per_page;
		$this->pagination->setPerPage( $per_page );
		
		return;
	}
		
		
	/**
	 * gets number of columns
	 *
	 * @param none
	 * @return int
	 */
	function getNumCols()
	{
		return $this->num_cols;
	}
		
		
	/**
	 * gets number of rows
	 *
	 * @param none
	 * @return int
	 */
	function getNumRows()
	{
		return $this->num_rows;
   	}
		
		
	/**
	 * gets groups
	 *
	 * @param int $grp_id (optional)
	 * @return array or string
	 */
	function getGroups( $grp_id = false )
	{
		$options = get_option( 'projectmanager' );
		if ( isset($options[$this->project_id]['groups']) && '' != $options[$this->project_id]['groups'] ) {
			$groups = explode( ',', $options[$this->project_id]['groups'] );
			$id = 0;
			foreach ( $groups AS $group ) {
				$id++;
				$group_list[$id] = $group;
			}
		} else {
			$group_list = false;
		}
		
		if ( !$grp_id )
			return $group_list;
		else
			return $group_list[$grp_id];
	}
		
		
	/**
	 * gets project title
	 *
	 * @param int $project_id
	 * @return string
	 */
	function getProjectTitle( $project_id )
	{
		$projects = $this->getProjects( $project_id );
		$project_title = $projects[0]->title;
		return $project_title;
	}
	
	
	/**
	 * get number of projects
	 *
	 * @param none
	 */
	function getNumProjects()
	{
		global $wpdb;
		$num_projects = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_projects}" );
				
		return $num_projects;
	}
	
	
	/**
	 * gets projects from database
	 *
	 * @param int $project_id (optional)
	 * @return array
	 */
	function getProjects( $project_id = false )
	{
		global $wpdb;
		
		$search = ( $project_id ) ? " WHERE `id` = {$project_id}" : '';
		$sql = "SELECT `title`, `id` FROM {$wpdb->projectmanager_projects} $search ORDER BY `id` ASC";
		return $wpdb->get_results( $sql );
	}
	
	
	/**
	 * gets all widgedized projects
	 *
	 * @param none
	 */
	function getWidgetProjects()
	{
		global $wpdb;
		$all_projects = $this->getProjects();
		$options = get_option( 'projectmanager' );
		
		$widget_projects = array();
		foreach ( $all_projects AS $project ) {
			if ( 1 == $options[$project->id]['use_widget'] )
				$widget_projects[] = $project;
		}
		return $widget_projects;
	}
	
	
	/**
	* gets project meta
	*
	* @param none
	* @return array
	*/
	function getProjectMeta()
	{
		global $wpdb;
	
		$sql = "SELECT `label`, `type`, `order`, `show_on_startpage`, `id` FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$this->project_id} ORDER BY `order` ASC;";
		return $wpdb->get_results( $sql );
	}
	
	
	/**
	 * gets number of datasets for specific project
	 *
	 * @param int $project_id (optional)
	 * @return int
	 */
	function getNumDatasets( $project_id = false )
	{
		global $wpdb;
		
		if ( $project_id ) {
			$search = " WHERE `project_id` = {$project_id}";
		} else {
			$search = " WHERE `project_id` = {$this->project_id}";
			$search .= ( null != $this->group )? " AND `grp_id` = {$this->group}" : '';
		}
					
		$num_dataset = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_dataset} $search" );
	
		return $num_dataset;
	}
		
		
	/**
	 * gets dataset
	 *
	 * @param int $dataset_id
	 * @param string $order 
	 * @param bool $limit (optional)
	 * @param int $project_id (optional)
	 * @return array
	 */
	function getDataset( $dataset_id = false, $order = 'name ASC', $limit = false, $project_id = false )
	{
		global $wpdb;
		
		if ( $limit ) {
			$page = $this->pagination->getPage();
			// offset for MySQL query
			$offset = ( $page - 1 ) * $this->per_page;
		}
		$project_id = ( $project_id ) ? $project_id : $this->project_id;

		$sql = "SELECT `id`, `name`, `image`, `grp_id` FROM {$wpdb->projectmanager_dataset}";
	
		if ( $this->group )
			$sql .= " WHERE `project_id` = {$project_id} AND `grp_id` = {$this->group}";
		elseif ( $dataset_id )
			$sql .= " WHERE `id` = {$dataset_id}";
		else
			$sql .= " WHERE `project_id` = {$project_id}";
			
		$sql .= " ORDER BY $order";
		$sql .= ( $limit ) ? " LIMIT ".$offset.",".$this->per_page.";" : ";";
			
		$dataset = $wpdb->get_results( $sql );
		return $dataset;
	}
		
		
	/**
	 * gets meta data for dataset
	 *
	 * @param int $dataset_id
	 * @return array
	 */
	function getDatasetMeta( $dataset_id )
	{
	 	global $wpdb;
		$sql = "SELECT form.id AS form_field_id, form.label AS label, data.value AS value, form.type AS type, form.show_on_startpage AS show_on_startpage FROM {$wpdb->projectmanager_datasetmeta} AS data LEFT JOIN {$wpdb->projectmanager_projectmeta} AS form ON form.id = data.form_id WHERE data.dataset_id = {$dataset_id} ORDER BY form.order ASC";
		return $wpdb->get_results( $sql );
	}
		
	
	/**
	 * gets form field labels as table header
	 *
	 * @param none
	 *
	 */
	function getTableHeader()
	{
		$out = '';
		if ( $form_fields = $this->getProjectMeta() ) {
			foreach ( $form_fields AS $form_field ) {
				if ( 1 == $form_field->show_on_startpage )
					$out .= '<th>'.$form_field->label.'</th>';
			}
		}
		return $out;
	}
	function printTableHeader()
	{
		echo $this->getTableHeader( );
	}
	
		 
	/**
	 * gets dataset meta data. Output types are list items or table columns
	 *
	 * @param int $dataset_id
	 * @param string $output td | li | dl (default 'li')
	 *
	 */
	function getDatasetMetaData( $dataset_id, $output = 'li' )
	{
		$out = '';
		if ( $dataset_meta = $this->getDatasetMeta( $dataset_id ) ) {
			foreach ( $dataset_meta AS $meta ) {
				/*
				* Check some special field types
				*
				* 1: One line Text
				* 2: Multiple lines Text
				* 3: E-Mail
				* 4: Date
				* 5: External URL
				*/
				$meta_value = htmlspecialchars( $meta->value );
					
				if ( 2 == $meta->type )
					$meta_value = nl2br( $meta_value );
				elseif ( 3 == $meta->type )
					$meta_value = "<a href='mailto:".$meta_value."'>".$meta_value."</a>";
				elseif ( 4 == $meta->type )
					$meta_value = ( '' == $meta_value ) ? '' : date( "j.n.Y", mktime( 0, 0, 0, substr($meta_value,5,2), substr($meta_value,8,2), substr($meta_value,0,4) ) );
				elseif ( 5 == $meta->type )
					$meta_value = "<a href='http://".$meta_value."' target='_blank' title='".$meta_value."'>".$meta_value."</a>";
				
				if ( 1 == $meta->show_on_startpage ) {
					if ( '' != $meta_value ) {
						if ( 'dl' == $output )
							$out .= '<dt class="projectmanager">'.$meta->label.'</dt><dd>'.$meta_value.'</dd>';
						else
							$out .= '<'.$output.'>'.$meta_value.'</'.$output.'>';
					} elseif ( 'td' == $output )
						$out .= '<'.$output.'>&#160;</'.$output.'>';
				}
			}
		}
		return $out;
	}
	function printDatasetMetaData( $dataset_id, $output = 'li' )
	{
		echo $this->getDatasetMetaData( $dataset_id, $output );
	}
		 
		 
	/**
	 * gets offset of dataset
	 *
	 * @param int $dataset_id
	 * @return int
	 */
	function getDatasetOffset( $dataset_id )
	{
		global $wpdb;
			
		$search = "WHERE `id` < '".$dataset_id."' AND project_id = {$this->project_id}";
				
		if ( $this->group )
			$search .= " AND `grp_id` = '".$this->group."'";
		
		$offset = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_dataset} $search" );

		return $offset;
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
	
		$wpdb->query( "INSERT INTO {$wpdb->projectmanager_projects} (title) VALUES ('".$this->slashes($title)."')" );
		return 'Project added';
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
		$wpdb->query( "UPDATE {$wpdb->projectmanager_projects} SET `title` = '".$this->slashes($title)."' WHERE `id` = {$project_id}" );
		return 'Project updated';
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
		
		foreach ( $this->getDataset() AS $dataset )
			$this->delDataset( $dataset->id );
		
		$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$project_id}" );
		$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projects} WHERE `id` = {$project_id}" );
	}
	
	
	/**
	 * add new dataset
	 *
	 * @param int $project_id
	 * @param string $name
	 * @param int $group
	 * @param array $dataset_meta
	 * @return string
	 */
	function addDataset( $project_id, $name, $group, $dataset_meta = false )
	{
		
		global $wpdb;
			
		$sql = "INSERT INTO {$wpdb->projectmanager_dataset}
				(name, grp_id, project_id)
			VALUES
				('".$this->slashes($name)."',
				'".$group."',
				'".$project_id."')";
		$wpdb->query( $sql );
			
		$dataset_id = $wpdb->insert_id;
			
		if ( $dataset_meta ) {
			foreach ( $dataset_meta AS $meta_id => $meta_value ) {
				if ( is_array($meta_value) ) {
					// form field value is a date
					if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) )
						$meta_value = $meta_value['year'].'-'.str_pad($meta_value['month'], 2, 0, STR_PAD_LEFT).'-'.str_pad($meta_value['day'], 2, 0, STR_PAD_LEFT);
				}
				$sql = "INSERT INTO {$wpdb->projectmanager_datasetmeta}
						(form_id, dataset_id, value)
					VALUES ('".$meta_id."',
						'".$dataset_id."',
						'".$this->slashes($meta_value)."')";
				$wpdb->query( $sql );
			}
		}
			
		/*
		* Set Image if supplied
		*/
		if ( isset($_FILES['projectmanager_image']['name']) AND '' != $_FILES['projectmanager_image']['name'] )
			$tail = $this->setImage( $dataset_id, $_FILES['projectmanager_image']['name'], $_FILES['projectmanager_image']['size'], $_FILES['projectmanager_image']['tmp_name'], $this->getImageDir() );
		
		return __( 'New dataset added to the database', 'projectmanager' ).$tail;
	}
		
		
	/**
	 * edit dataset
	 *
	 * @param string $name
	 * @param int $group
	 * @param int $dataset_id
	 * @param array $dataset_meta
	 * @param boolean $del_image
	 * @param string $image_file
	 * @return string
	 */
	function editDataset( $name, $group, $dataset_id, $dataset_meta = false, $del_image = false, $image_file = '' )
	{
		global $wpdb;
			
		$tail = '';
		$wpdb->query( "UPDATE {$wpdb->projectmanager_dataset} SET `name` = '".$this->slashes($name)."', `grp_id` = '".$group."' WHERE `id` = {$dataset_id}" );
			
		if ( $dataset_meta ) {
			foreach ( $dataset_meta AS $meta_id => $meta_value ) {
				if ( is_array($meta_value) ) {
					// form field value is a date
					if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) )
						$meta_value = $meta_value['year'].'-'.str_pad($meta_value['month'], 2, 0, STR_PAD_LEFT).'-'.str_pad($meta_value['day'], 2, 0, STR_PAD_LEFT);
				}
				
				$wpdb->query( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '".$this->slashes($meta_value)."' WHERE `dataset_id` = '".$dataset_id."' AND `form_id` = {$meta_id}");
			}
		}
			
			
		// Delete Image if options is checked
		if ($del_image) {
			$wpdb->query("UPDATE {$wpdb->projectmanager_dataset} SET `image` = '' WHERE `id` = {$dataset_id}");
			$this->delImage( $image_file );
		}
			
		/*
		* Set Image if supplied
		*/
		if ( isset($_FILES['projectmanager_image']['name']) AND '' != $_FILES['projectmanager_image']['name'] )
			$tail = $this->setImage($dataset_id, $_FILES['projectmanager_image']['name'], $_FILES['projectmanager_image']['size'], $_FILES['projectmanager_image']['tmp_name'], $this->getImageDir());
			
			
		return __('Dataset updated', 'projectmanager').' '.$tail;
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
		$wpdb->query("DELETE FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = {$dataset_id}");
		$wpdb->query("DELETE FROM {$wpdb->projectmanager_dataset} WHERE `id` = {$dataset_id}");
		
			
		return;
	}
		
		
	/**
	 * delete image along with thumbnail from server
	 *
	 * @param string $image
	 * @return void
	 *
	 */
	function delImage( $image )
	{
		@unlink( ABSPATH.'wp-content/'.$this->getImageDir().$image );
		@unlink( ABSPATH.'wp-content/'.$this->getThumbsDir().$image );
	}
		
		
	/**
	 * set image path in database and upload image to server
	 *
	 * @param int  $dataset_id
	 * @param string $img_name
	 * @param int $img_size
	 * @param string $img_tmp_name
	 * @param string $uploaddir
	 */
	function setImage( $dataset_id, $img_name, $img_size, $img_tmp_name, $uploaddir )
	{
		global $wpdb;
		
		require_once( 'image.php' );
		$image = new Image();
		if ( $image->ImageTypeIsSupported($img_name) ) {
			$uploaddir = ABSPATH.'wp-content/'.$uploaddir;
				
			/*
			* Delete old images from server and clean database entry
			*/
			if ( $img_size > 0 ) {
				$result = $wpdb->get_results( "SELECT `image` FROM {$wpdb->projectmanager_dataset} WHERE `id` = '".$dataset_id."'" );		
				if ( $result ) {
					if ( $result[0]->image != basename($img_name) AND $result[0]->image != '' ) {
						@unlink(ABSPATH.'wp-content/'.$this->getImageDir().$result[0]->image);
						@unlink(ABSPATH.'wp-content/'.$this->getThumbsDir().$result[0]->image);
					}
					$wpdb->query("UPDATE {$wpdb->projectmanager_dataset} SET `image` = '' WHERE `id` = {$dataset_id}");
				}
			}
	
		
			/*
			* Upload Image to Server
			*/
			if ( $img_size > 0 ) {
				$wpdb->query( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '".$this->slashes( basename($img_name) )."' WHERE id = '".$dataset_id."'");
	
				$uploadfile = $uploaddir.basename($img_name);
				if ( file_exists($uploadfile) ) {
					return __('File exists and is not uploaded.','projectmanager');
				} else {
					if ( move_uploaded_file($img_tmp_name, $uploadfile) )
						return true;
					else
						return __('An upload error occured. Please try again.','projectmanager');
				}
			}
		} else {
			return __('The file type is not supported. No Image was uploaded.','projectmanager');
		}
	}
		
		
	/**
	 * adds slashes if magic_quotes_gpc is off
	 *
	 * @param string $str
	 * @return string
	 */
	function slashes( $str ) {
		if (get_magic_quotes_gpc() == 0)
			$str = addslashes($str);
		return $str;
	}
		

	/**
	 * print portraits in post or page
	 *
	 * @param string $content
	 *
	 */
	function printProject( $content )
	{
		$pattern = "/\[print_projects id\s*=\s*(\w+)(?:\s|\])|grp\s*=\s*(\w+)(?:\s|\])/i";
		 
		if ( preg_match_all($pattern, $content , $matches) ) {
			$search = '';
			if ( is_array( $matches[0] ) )
				foreach ( $matches[0] AS $search_string )
					$search .= $search_string;
			else
				$search .= $matches[0];
			
			$project_id = $matches[1][0];
			$grp_id = ( isset($matches[2][1]) ) ? $matches[2][1] : false;
			
			$this->setGroup( $grp_id );
			$content = str_replace($search, $this->getTemplate( $project_id ), $content);
		}
		
		return $content;
	}
		 
		 
	 /**
	  * get template for frontend output
	  *
	  * @param int $project_id
	  */
	function getTemplate( $project_id )
	{	
		global $wpdb;
		
		$this->setSettings( $project_id );
		$options = get_option( 'projectmanager' );
		$template = $this->plugin_path.'/templates/'.$options[$project_id]['template'];
		
		echo '</p>';
		if ( file_exists($template) )
			include $template;
		else
			echo '<p>'.__( 'Could not find template', 'projectmanager' ).'</p>';
		echo '<p>';
	}
 

	/**
	 * Set Form Fields
	 *
	 * @param int $project_id
	 * @param array $form_name
	 * @param array $form_type
	 * @param array $form_order
	 * @param array $new_form_name
	 * @param array $new_form_type
	 * @param array $new_form_order
	 *
	 * @return string
	 */
	function setFormFields( $project_id, $form_name, $form_type, $form_show_on_startpage, $form_order, $new_form_name, $new_form_type, $new_form_show_on_startpage, $new_form_order )
	{
		global $wpdb;
			
			
		if ( null != $form_name ) {
			foreach ( $wpdb->get_results( "SELECT `id` FROM {$wpdb->projectmanager_projectmeta}" ) AS $form_field) {
				if ( !array_key_exists( $form_field->id, $form_name ) ) {
					$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `id` = {$form_field->id}" );
					$wpdb->query( "DELETE FROM {$wpdb->projectmanager_datasetmeta} wHERE `form_id` = {$form_field->id}" );
				}
			}
				
			foreach ( $form_name AS $form_id => $form_label ) {
				$type = $form_type[$form_id];
				$order = $form_order[$form_id];
				$show_on_startpage = (isset($form_show_on_startpage[$form_id])) ? 1 : 0;
					
				$wpdb->query("UPDATE {$wpdb->projectmanager_projectmeta} SET `label` = '$form_label', `type` = '$type', `show_on_startpage` = '$show_on_startpage', `order` = '$order' WHERE `id` = '".$form_id."' LIMIT 1 ;");
				$wpdb->query("UPDATE {$wpdb->projectmanager_datasetmeta} SET `form_id` = '".$form_id."' WHERE `form_id` = {$form_id}");
			}
		}
			
		if ( null != $new_form_name ) {
			foreach ($new_form_name AS $form_id => $form_label) {
				$type = $new_form_type[$form_id];
				$show_on_startpage = (isset($new_form_show_on_startpage[$form_id])) ? 1 : 0;
					
				$max_order_sql = "SELECT MAX(`order`) AS `order` FROM {$wpdb->projectmanager_projectmeta};";
				if ($new_form_order[$form_id] != '') {
					$order = $new_form_order[$form_id];
				} else {
					$max_order_sql = $wpdb->get_results($max_order_sql, ARRAY_A);
					$order = $max_order_sql[0]['order'] +1;
				}
					
				$wpdb->query( "INSERT INTO {$wpdb->projectmanager_projectmeta} (`label`, `type`, `show_on_startpage`, `order`, `project_id`) VALUES ( '".$this->slashes($form_label)."', '".$type."', '".$show_on_startpage."', '".$order."', '".$project_id."');" );
				$form_id = mysql_insert_id();
					
				/*
				* Populate default values for every dataset
				*/
				if ( $datasets = $this->getDataset() ) {
					foreach ( $datasets AS $dataset ) {
						$wpdb->query( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '".$form_id."', '".$dataset->id."', '' );" );
					}
				}
			}
		}
			
		return __('Form Fields updated', 'projectmanager');
	}
		 
		
	/**
	 * create search formular
	 *
	 * @param string $style
	 */
	function getSearchForm( $style )
	{
		$search_string = isset( $_POST['projectmanager_search'] ) ? $_POST['projectmanager_search'] : '';
		$form_field_id = isset( $_POST['form_field'] ) ? $_POST['form_field'] : 0;
			
		$out = "<form class='projectmanager' style='".$style."' action='' method='post'>
			<input type='text' name='projectmanager_search' id='search' value='".$search_string."' />";
		if ( $form_fields = $this->getProjectMeta() ) {
			$out .= "<select size='1' style='margin: 1em 1em 0em 1em;' name='form_field'>";
			$out .= "<option value='0'>".__( 'Name', 'projectmanager' )."</option>";
			foreach ( $form_fields AS $form_field ) {
				$selected = ( $form_field_id == $form_field->id ) ? " selected='selected'" : "";
				$out .= "<option value='".$form_field->id."'".$selected.">".$form_field->label."</option>";
			}
			$out .= "</select>";
		}
		$out .= "<input type='submit' value='".__('Search', 'projectmanager')." &raquo;' class='button' />
			</form>";

		return $out;
	}
	function printSearchForm( $style, $action = '' )
	{
		echo $this->getSearchForm( $style, $action );
	}
		
	
	/**
	 * gets search results
	 *
	 * @param string $search
	 * @param int $meta_id
	 */
	function getSearchResults( $search, $meta_id )
	{
		global $wpdb;
		
		if ( 0 == $meta_id ) {
			$dataset_list = $wpdb->get_results( "SELECT `id`, `name`, `grp_id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$this->project_id} AND `name` REGEXP CONVERT( _utf8 '".$search."' USING latin1 ) ORDER BY `name` ASC" );
		} else {
			$sql = "SELECT  t1.dataset_id AS id,
					t2.name,
					t2.grp_id
				FROM {$wpdb->projectmanager_datasetmeta} AS t1, {$wpdb->projectmanager_dataset} AS t2
				WHERE t1.value REGEXP CONVERT( _utf8 '".$search."' USING latin1 )
					AND t1.form_id = '".$meta_id."'
					AND t1.dataset_id = t2.id
				ORDER BY t1.dataset_id ASC";
			$dataset_list = $wpdb->get_results( $sql );
		}
		return $dataset_list;
	}
	
	
	/**
	 * Widget
	 *
	 * @param array $args
	 */
	function widget( $args )
	{
		global $wpdb;
		
		$options = get_option( 'projectmanager_widget' );
		$widget_id = $args['widget_id'];
		$project_id = $options[$widget_id];
		
		$defaults = array(
			'before_widget' => '<li id="projectmanager" class="widget '.get_class($this).'_'.__FUNCTION__.'">',
			'after_widget' => '</li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
			'widget_title' => $options[$project_id]['title'],
			'limit' => $options[$project_id]['limit'],
		);
		$args = array_merge( $defaults, $args );
		extract( $args );
		
			
		$datasets = $wpdb->get_results( "SELECT `id`, `name` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$project_id} ORDER BY `id` DESC LIMIT 0,".$limit." " ); 
			
		echo $before_widget . $before_title . $widget_title . $after_title;
		echo "<ul id='projectmanager_widget'>";
		if ( $datasets ) {
			foreach ( $datasets AS $dataset ) {
				echo "<li>".$dataset->name."</li>";
			}
		}
		echo "</ul>";
		echo $after_widget;
	}
		 
		 
	/**
	 * Widget Control
	 *
	 * @param none
	 */
	function widgetControl( $args )
	{
		extract( $args );
		$options = get_option( 'projectmanager_widget' );
		
		if ($_POST['projectmanager-submit']) {
			$options[$widget_id] = $project_id;
			$options[$project_id]['title'] = $_POST['widget_title'][$project_id];
			$options[$project_id]['limit'] = $_POST['limit'][$project_id];
				
			update_option( 'projectmanager_widget', $options );
		}
			
		echo '<p style="text-align: left;"><label for="widget_title" class="projectmanager-widget">'.__('Title', 'projectmanager').'</label><input type="text" name="widget_title['.$project_id.']" id="widget_title" value="'.$options[$project_id]['title'].'" /></p>';
		echo '<p style="text-align: left;"><label for="limit" class="projectmanager-widget">'.__('Number', 'projectmanager').'</label><select style="margin-top: 0;" size="1" name="limit['.$project_id.']" id="limit">';
		for ( $i = 1; $i <= 10; $i++ ) {
			$selected = ( $options[$project_id]['limit'] == $i ) ? " selected='selected'" : '';
			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		echo '</select></p>';
			
		echo '<input type="hidden" name="projectmanager-submit" value="1" />';
	}
	
	
	/**
	 * Add Global Options Page
	 *
	 * @param none
	 */
	function addOptionsPage()
	{
		if ( isset($_POST['updateProjectManager']) AND !isset($_POST['deleteit']) ) {
			if ( 'project' == $_POST['updateProjectManager'] ) {
				if ( '' == $_POST['project_id'] )
					$return_message = $this->addProject( $_POST['project_title'] );
			}
			
			echo '<div id="message" class="updated fade"><p><strong>'.__( $return_message, 'projectmanager' ).'</strong></p></div>';
		}
	?>
		<div class="wrap">
			<h2><?php _e( 'Projectmanager', 'projectmanager' ) ?></h2>
			<!-- Add New Project -->
			<form class="projectmanager" action="" method="post">
				<h3><?php _e( 'Add Project', 'projectmanager' ) ?></h3>
				<label for="project_title"><?php _e( 'Title', 'projectmanager' ) ?>:</label><input type="text" name="project_title" id="project_title" value="" size="30" style="margin-bottom: 1em;" /><br />
						
				<input type="hidden" name="project_id" value="" />
				<input type="hidden" name="updateProjectManager" value="project" />
				
				<p class="submit"><input type="submit" value="<?php _e( 'Add Project', 'projectmanager' ) ?> &raquo;" class="button" /></p>
			</form>
		</div>
		<div class="wrap">
			<!-- Plugin Uninstallation -->
			<h3 style='clear: both; padding-top: 1em;'><?php _e( 'Uninstall ProjectManager', 'projectmanager' ) ?></h3>
			<form method="get" action="index.php">
				<input type="hidden" name="projectmanager" value="uninstall" />
					
				<p><input type="checkbox" name="delete_plugin" value="1" id="delete_plugin" /> <label for="delete_plugin"><?php _e( 'Yes I want to uninstall ProjectManager Plugin. All Data will be deleted!', 'projectmanager' ) ?></label> <input type="submit" value="<?php _e( 'Uninstall ProjectManager', 'projectmanager' ) ?> &raquo;" class="button" /></p>
			</form>
		</div>
	<?php
	}
	
	
	/**
	 * print breadcrumb navigation
	 *
	 * @param int $project_id
	 * @param string $page_title
	 */
	function printBreadcrumb( $project_id, $page_title )
	{
		echo '<p class="projectmanager_breadcrumb">';
		if ( 1 != $this->getNumProjects() )
			echo '<a href="edit.php?page=projectmanager/page/index.php">'.__( 'Projectmanager', 'projectmanager' ).'</a> &raquo; ';
		
		if ( $page_title != $this->getProjectTitle( $project_id ) )
			echo '<a href="edit.php?page=projectmanager/page/show-project.php&amp;id='.$project_id.'">'.$this->getProjectTitle( $project_id ).'</a> &raquo; ';
		
		_e( $page_title, 'projectmanager' );
		
		echo '</p>';
	}
	
	
	/**
	 * Add Code to Wordpress Header
	 *
	 * @param none
	 */
	function addHeaderCode()
	{
		global $wp_version;
		
		echo "\n\n<!-- WP-ProjectManager START -->\n";
		echo "<link rel='stylesheet' href='".$this->plugin_url."/style.css' type='text/css' />\n";
		
		if ( is_admin() AND isset( $_GET['page'] ) AND substr( $_GET['page'], 0, 14 ) == 'projectmanager' ) {
			wp_register_script( 'projectmanager', $this->plugin_url.'/projectmanager.js', array( 'tiny_mce' ), '1.0' );
			wp_print_scripts( 'projectmanager' );
			
			// Load TinyMCE for WP 2.3.x
			if ( strpos($wp_version, '2.3') === 0 ) {
				wp_register_script( 'projectmanager_tinymce', $this->plugin_url.'/tiny_mce_config.php', false, '20070528' );
				wp_print_scripts( 'projectmanager_tinymce' );
			}
			echo "<script type='text/javascript'>\n";
			echo "var PRJCTMNGR_HTML_FORM_FIELD_TYPES = \"";
			foreach ($this->getFormFieldTypes() AS $form_type_id => $form_type)
				echo "<option value='".$form_type_id."'>".__( $form_type, 'projectmanager' )."</option>";
			echo "\";\n";
			echo "</script>\n";
		}
		echo "<!-- WP-ProjectManager END -->\n\n";
	}
		
	
	/**
	 * customize TinyMCE Settings. WP 2.5.x
	 *
	 * @param array $initArray
	 * @return array
	 */
	function tinyMceInit( $initArray )
	{
		//print_r($initArray);
		
		$initArray['mode'] = 'textareas';
		$initArray['editor_selector'] = 'projectmanager_mceEditor';
		unset($initArray['onpageload'], $initArray['skin']);
		return $initArray;
	}
	
	
	/**
	 * Filter functions to customize TinyMCE Buttons
	 *
	 * @param none
	 * @return array
	 */
	function tinyMceButtons()
	{
	$mce_buttons = array( 'bold', 'italic', 'underline', '|', 'bullist', 'numlist', 'blockquote', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|', 'link', 'unlink', 'image', 'pastetext', 'pasteword', 'wp_adv' );
		return $mce_buttons;
	}
	function tinyMceButtons2()
	{
		$mce_buttons = array( 'formatselect', 'cut', 'copy', '|', 'outdent', 'indent', '|', 'undo', 'redo', '|', 'cleanup', 'help', 'code', 'forecolor', 'backcolor' );
		return $mce_buttons;
	}
	
	
	/**
	 * Initialize Widget
	 *
	 * @param none
	 */
	function initWidget()
	{
		$options = get_option('projectmanager');
			
		if (!function_exists('register_sidebar_widget')) {
			return;
		}
			
		// Register Widgets
		foreach ( $this->getWidgetProjects() AS $project ) {
			$name = $project->title;
			register_sidebar_widget( $name, array(&$this, 'widget') );
			register_widget_control( $name, array(&$this, 'widgetControl'), 250, 100, array( 'project_id' => $project->id, 'widget_id' => sanitize_title($name) ) );
		}
	}
		 
		 
	/**
	 * Initialize Plugin
	 *
	 * @param none
	 */
	function init()
	{
		global $wpdb;
		include_once( ABSPATH.'/wp-admin/includes/upgrade.php' );
			
		$create_projects_sql = "CREATE TABLE {$wpdb->projectmanager_projects} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`title` varchar( 50 ) NOT NULL default '',
						PRIMARY KEY ( `id` ))";
		maybe_create_table( $wpdb->projectmanager_projects, $create_projects_sql );
			
		$create_projectmeta_sql = "CREATE TABLE {$wpdb->projectmanager_projectmeta} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`type` int( 11 ) NOT NULL ,
						`label` varchar( 100 ) NOT NULL default '' ,
						`order` int( 10 ) NOT NULL ,
						`show_on_startpage` tinyint( 1 ) NOT NULL ,
						`project_id` int( 11 ) NOT NULL ,
						PRIMARY KEY ( `id` ))";
		maybe_create_table( $wpdb->projectmanager_projectmeta, $create_projectmeta_sql );
			
		$create_dataset_sql = "CREATE TABLE {$wpdb->projectmanager_dataset} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`name` varchar( 150 ) NOT NULL default '' ,
						`image` varchar( 50 ) NOT NULL default '' ,
						`grp_id` int( 11 ) NOT NULL ,
						`project_id` int( 11 ) NOT NULL ,
						PRIMARY KEY ( `id` ))";
		maybe_create_table( $wpdb->projectmanager_dataset, $create_dataset_sql );
			
		$create_datasetmeta_sql = "CREATE TABLE {$wpdb->projectmanager_datasetmeta} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`form_id` int( 11 ) NOT NULL ,
						`dataset_id` int( 11 ) NOT NULL ,
						`value` longtext NOT NULL default '' ,
						PRIMARY KEY ( `id` ))";
		maybe_create_table( $wpdb->projectmanager_datasetmeta, $create_datasetmeta_sql );


		/*
		* Set default options
		*/
		$options = array();
		$options['version'] = PROJECTMANAGER_VERSION;
		add_option( 'projectmanager', $options, 'ProjectManager Options', 'yes' );

		/*
		* Add Capabilities
		*/
		$role = get_role('administrator');
		$role->add_cap('manage_projectmanager');
		$role->add_cap('manage_projects');
		
		$role = get_role('editor');
		$role->add_cap('manage_projects');
		
		/*
		* Add widget options
		*/
		if ( function_exists('register_sidebar_widget') )
			add_option( 'projectmanager_widget', array(), 'ProjectManager Widget Options', 'yes' );
		
		/*
		* Create directories for projects
		*/
		if ( !file_exists(ABSPATH.'wp-content/uploads') )
			mkdir( ABSPATH.'wp-content/uploads' );
		if ( !file_exists(ABSPATH.'wp-content/'.$this->getImageDir()) )
			mkdir( ABSPATH.'wp-content/'.$this->getImageDir() );
		if ( !file_exists(ABSPATH.'wp-content/'.$this->getThumbsDir()) )
			mkdir( ABSPATH.'wp-content/'.$this->getThumbsDir() );
	}
	
	
	/**
	 * adds admin menu
	 *
	 * @param none
	 */
	function addAdminMenu()
	{
		global $wpdb;
		
		
		if ( 1 == $this->getNumProjects() ) {
			$project = $wpdb->get_results( "SELECT `id` FROM {$wpdb->projectmanager_projects} ORDER BY `id` ASC LIMIT 0,1" );
			$management_page = 'edit.php?page=projectmanager/page/show-project.php&id='.$project[0]->id;
		} else
			$management_page = basename( __FILE__, ".php" ).'/page/index.php';
		
		add_management_page( __( 'Projects', 'projectmanager' ), __( 'Projects', 'projectmanager' ), 'manage_projects', $management_page );
		add_options_page( __( 'Projectmanager', 'projectmanager' ), __( 'Projectmanager', 'projectmanager' ), 'manage_projectmanager', basename(__FILE__), array(&$this, 'addOptionsPage') );
	}


	/**
	 * uninstalls ProjectManager
	 *
	 * @param none
	 * @return boolean
	 */
	function uninstall()
	{
		global $wpdb;

		$wpdb->query( "DROP TABLE {$wpdb->projectmanager_projects}" );
		$wpdb->query( "DROP TABLE {$wpdb->projectmanager_projectmeta}" );
		$wpdb->query( "DROP TABLE {$wpdb->projectmanager_dataset}" );
		$wpdb->query(" DROP TABLE {$wpdb->projectmanager_datasetmeta}" );

		delete_option( 'projectmanager' );
		delete_option( 'projectmanager_widget' );

		$plugin = basename(__FILE__, ".php") .'/plugin-hook.php';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( function_exists( "deactivate_plugins" ) )
			deactivate_plugins( $plugin );
		else {
			$current = get_option('active_plugins');
			array_splice($current, array_search( $plugin, $current), 1 ); // Array-fu!
			update_option('active_plugins', $current);
			do_action('deactivate_' . trim( $plugin ));
		}
	}
}
?>
