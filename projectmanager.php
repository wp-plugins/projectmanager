<?php

class WP_ProjectManager
{
	/**
	* supported image types
	*
	* @var array
	*/
	var $supported_image_types = array( "jpg", "jpeg", "png", "gif" );
	
	
	/**
	 * ID of selected project
	 *
	 * @var int
	 */
	var $project_id;
	
	
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
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		global $wpdb;
			
		$wpdb->projectmanager_projects = $wpdb->prefix . 'projectmanager_projects';
		$wpdb->projectmanager_projectmeta = $wpdb->prefix . 'projectmanager_projectmeta';
		$wpdb->projectmanager_dataset = $wpdb->prefix . 'projectmanager_dataset';
		$wpdb->projectmanager_datasetmeta = $wpdb->prefix . 'projectmanager_datasetmeta';

		/*
		*  Set plugin url and path
		*/
		$this->plugin_url = WP_PLUGIN_URL.'/projectmanager';
		$this->plugin_path = WP_PLUGIN_DIR.'/projectmanager';
		
		//Save selected group. NULL if none is selected
		$this->setGroup();
	}
	function WP_Manager()
	{
		$this->__construct();
	}
	
	
	/**
	 * set project ID
	 *
	 * @param int $project_id
	 * @return void
	 */
	function setSettings( $project_id )
	{
		$this->project_id = $project_id;

		$options = get_option( 'projectmanager' );
		$this->per_page = $options[$project_id]['per_page'];

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
		$form_field_types = array( 1 => __('Text', 'projectmanager'), 2 => __('Textfield', 'projectmanager'), 3 => __('E-Mail', 'projectmanager'), 4 => __('Date', 'projectmanager'), 5 => __('URL', 'projectmanager') );
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
	 * @param string | false $file
	 * @return string
	 */
	function getImagePath( $file = false )
	{
		if ( $file )
			return WP_CONTENT_DIR.'/projects/'.$file;
		else
			return WP_CONTENT_DIR.'/projects';
	}
	
	
	/**
	 * returns url of image directory
	 *
	 * @param string | false $file
	 * @return string
	 */
	function getImageUrl( $file = false )
	{
		if ( $file )
			return WP_CONTENT_URL.'/projects/'.$file;
		else
			return WP_CONTENT_URL.'/projects';
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
	 * @return int | false
	 */
	function getGroup()
	{
		return $this->group;
	}
		
	
	/**
	 * gets group title
	 *
	 * @param int $grp_id
	 * @return string
	 */
	function getGroupTitle( $grp_id )
	{
		$group = get_category($grp_id);
		return $group->name;
	}
	
	
	/**
	 * check if group is selected
	 * 
	 * @param none
	 * @return boolean
	 */
	function isGroup()
	{
		if ( null != $this->getGroup() )
			return true;
		
		return false;
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
			$this->group = ( isset($_GET['grp_id']) && '' != $_GET['grp_id'] ) ? (int)$_GET['grp_id'] : null;
		
		return;
	}
		
		
	/**
	 * check if search was performed
	 *
	 * @param none
	 * @return boolean
	 */
	function isSearch()
	{
		if ( isset( $_POST['projectmanager_search'] ) )
			return true;
	
		return false;
	}
	
	
	/**
	 * returns search string
	 *
	 * @param none
	 * @return string
	 */
	function getSearchString()
	{
		if ( $this->isSearch() )
			return $_POST['projectmanager_search'];
		
		return '';
	}
	
	
	/**
	 * gets supported file types
	 *
	 * @param none
	 * @return array
	 */
	function getSupportedImageTypes()
	{
		return $this->supported_image_types;
	}
	
	
	/**
	 * checks if image type is supported
	 *
	 * @param string $filename
	 * @return boolean
	 */
	function imageTypeIsSupported( $filename )
	{
		if ( in_array($this->getImageType($filename), $this->supported_image_types) )
			return true;
		else
			return false;
	}
	
	
	/**
	 * gets image type
	 *
	 * @param string $filename
	 * @return string
	 */
	function getImageType( $filename )
	{
		$file_info = pathinfo($filename);
		return strtolower($file_info['extension']);
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
	 * @return int
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
	 * @return array
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
	 * gets form fields for project
	 *
	 * @param none
	 * @return array
	 */
	function getFormFields()
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
			$search .= ( $this->isGroup() )? " AND `grp_id` = {$this->group}" : '';
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
	
		if ( $this->isGroup() )
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
	 * @return string
	 */
	function getTableHeader()
	{
		$out = '';
		if ( $form_fields = $this->getFormFields() ) {
			foreach ( $form_fields AS $form_field ) {
				if ( 1 == $form_field->show_on_startpage )
				$out .= "\n\t<th scop='col'>".$form_field->label."</th>";
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
	 * @param boolean $show_all
	 * @return string
	 */
	function getDatasetMetaData( $dataset_id, $output = 'li', $show_all = false, $dataset_name = null )
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
				
				if ( 1 == $meta->type )
					$meta_value = "<span id='datafield".$meta->form_field_id."_".$dataset_id."'>".$meta_value."</span>";
				elseif ( 2 == $meta->type ) {
					if ( strlen($meta_value) > 150 && !$show_all )
						$meta_value = substr($meta_value, 0, 150)."...";
					$meta_value = nl2br($meta_value);
						
					$meta_value = "<span id='datafield".$meta->form_field_id."_".$dataset_id."'>".$meta_value."</span>";
				} elseif ( 3 == $meta->type )
					$meta_value = "<a href='mailto:".$meta_value."'><span id='datafield".$meta->form_field_id."_".$dataset_id."'>".$meta_value."</span></a>";
				elseif ( 4 == $meta->type )
					$meta_value = "<span id='datafield".$meta->form_field_id."_".$dataset_id."'>".mysql2date(get_option('date_format'), $meta_value )."</span>";
				elseif ( 5 == $meta->type )
					$meta_value = "<a href='http://".$meta_value."' target='_blank' title='".$meta_value."'><span id='datafield".$meta->form_field_id."_".$dataset_id."'>".$meta_value."</span></a>";
				
				if ( 1 == $meta->show_on_startpage || $show_all ) {
					if ( '' != $meta_value ) {
						if ( 'dl' == $output ) {
							$out .= "\n\t<dt class='projectmanager'>".$meta->label."</dt><dd>".$meta_value."</dd>";
						} else {
							$out .= "\n\t<".$output.">";
							$out .= $this->getThickbox( $dataset_id, $meta->form_field_id, $meta->type, $meta->value );
							$out .= "\n\t\t".$meta_value . $this->getThickboxLink($dataset_id, $meta->form_field_id, $meta->type, $meta->label." ".__('of','projectmanager')." ".$dataset_name);
							$out .= "\n\t</".$output.">";
						}
					} elseif ( 'td' == $output )
						$out .= "\n\t<".$output.">&#160;</".$output.">";
				}
			}
		}
		return $out;
	}
	function printDatasetMetaData( $dataset_id, $output = 'li', $show_all = false, $dataset_name = null )
	{
		echo $this->getDatasetMetaData( $dataset_id, $output, $show_all, $dataset_name );
	}
		 
		 
	/**
	 * get Thickbox Link for Ajax editing
	 *
	 * @param ing $dataset_id
	 * @param int $formfield_id
	 * @return string
	 */
	function getThickboxLink( $dataset_id, $formfield_id,  $formfield_type, $title )
	{
		$out = '';
		if ( is_admin() && current_user_can( 'manage_projects' ) ) {
			$dims = array('width' => '250', 'height' => '100');
			if ( 2 == $formfield_type )
				$dims = array('width' => '350', 'height' => '305');
						
			$out .= "&#160;<a class='thickbox' id='thickboxlink".$formfield_id."_".$dataset_id."' href='#TB_inline?height=".$dims['height']."&width=".$dims['width']."&inlineId=datafieldwrap".$formfield_id."_".$dataset_id."' title='".$title."'><img src='".$this->plugin_url."/images/edit.gif' border='0' alt='".__('Edit')."' /></a>";
		}
		return $out;
	}
	
	
	/**
	 * get Ajax Thickbox
	 *
	 * @param int $dataset_id
	 * @param int $formfield_id
	 * @param int $formfield_type
	 * @param string $value
	 * @return string
	 */
	function getThickbox( $dataset_id, $formfield_id, $formfield_type, $value )
	{
		$out = '';
		if ( is_admin() && current_user_can( 'manage_projects' ) ) {
			$dims = array('width' => '250px', 'height' => '80px');
			if ( 2 == $formfield_type )
				$dims = array('width' => '350px', 'height' => '250px');
			
			$out .= "\n\t\t<div id='datafieldwrap".$formfield_id."_".$dataset_id."' style='width:".$dims['width'].";height:".$dims['height'].";overfow:auto;display:none;'>";
			$out .= "\n\t\t<div id='datafieldbox".$formfield_id."_".$dataset_id."' class='projectmanager_thickbox'>";
			$out .= "\n\t\t\t<form>";
			if ( 1 == $formfield_type || 3 == $formfield_type || 5 == $formfield_type )
				$out .= "\n\t\t\t<input type='text' name='form_field_".$formfield_id."_".$dataset_id."' id='form_field_".$formfield_id."_".$dataset_id."' value='".$value."' size='30' />";
			elseif ( 2 == $formfield_type )
				$out .= "\n\t\t\t<textarea name='form_field_".$formfield_id."_".$dataset_id."' id='form_field_".$formfield_id."_".$dataset_id."' rows='10' cols='40'>".$value."</textarea>";
			elseif  ( 4 == $formfield_type ) {
				$out .= "\n\t\t\t<select size='1' name='form_field_".$formfield_id."_".$dataset_id."_day' id='form_field_".$formfield_id."_".$dataset_id."_day'>\n\t\t\t<option value=''>Tag</option>\n\t\t\t<option value=''>&#160;</option>";
				for ( $day = 1; $day <= 30; $day++ ) {
					$selected = ( $day == substr($value, 8, 2) ) ? ' selected="selected"' : '';
					$out .= "\n\t\t\t<option value='".str_pad($day, 2, 0, STR_PAD_LEFT)."'".$selected.">".$day."</option>";
				}
				$out .= "\n\t\t\t</select>";
				$out .= "\n\t\t\t<select size='1' name='form_field_".$formfield_id."_".$dataset_id."_month' id='form_field_".$formfield_id."_".$dataset_id."_month'>\n\t\t\t<option value=''>Monat</option>\n\t\t\t<option value=''>&#160;</option>";
				foreach ( $this->getMonths() AS $key => $month ) {
					$selected = ( $key == substr($value, 5, 2) ) ? ' selected="selected"' : '';
					$out .= "\n\t\t\t<option value='".str_pad($key, 2, 0, STR_PAD_LEFT)."'".$selected.">".$month."</option>";
				}
				$out .= "\n\t\t\t</select>";
				$out .= "\n\t\t\t<select size='1' name='form_field_".$formfield_id."_".$dataset_id."_year' id='form_field_".$formfield_id."_".$dataset_id."_year'>\n\t\t\t<option value=''>Jahr</option>\n\t\t\t<option value=''>&#160;</option>";
				for ( $year = date('Y')-50; $year <= date('Y')+10; $year++ ) {
					$selected = ( $year == substr($value, 0, 4) ) ? ' selected="selected"' : '';
					$out .= "\n\t\t\t<option value='".$year."'".$selected.">".$year."</option>";
				}
				$out .= "\n\t\t\t</select>";
			}
			$out .= "\n\t\t\t<div style='text-align:center; margin-top: 1em;'><input type='button' value='".__('Save')."' class='button-secondary' onclick='ProjectManager.ajaxSaveDataField(".$dataset_id.",".$formfield_id.",".$formfield_type.");return false;' />&#160;<input type='button' value='".__('Cancel')."' class='button' onclick='tb_remove();' /></div>";
			$out .= "\n\t\t\t</form>";
			$out .= "\n\t\t</div>";
			$out .= "\n\t\t</div>";
		}
		return $out;
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
				
		if ( $this->isGroup() )
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
	
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_projects} (title) VALUES ('%s')", $title ) );
		return __('Project added','projectmanager');
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
		return __('Project updated','projectmanager');
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
		$this->project_id = $project_id;

		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_dataset} (name, grp_id, project_id) VALUES ('%s', '%d', '%d')", $name, $group, $project_id ) );
		$dataset_id = $wpdb->insert_id;
			
		if ( $dataset_meta ) {
			foreach ( $dataset_meta AS $meta_id => $meta_value ) {
				if ( is_array($meta_value) ) {
					// form field value is a date
					if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) )
						$meta_value = $meta_value['year'].'-'.str_pad($meta_value['month'], 2, 0, STR_PAD_LEFT).'-'.str_pad($meta_value['day'], 2, 0, STR_PAD_LEFT);
				}
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ('%d', '%d', '%s')", $meta_id, $dataset_id, $meta_value ) );
			}
		}
			
		/*
		* Set Image if supplied
		*/
		if ( isset($_FILES['projectmanager_image']['name']) AND '' != $_FILES['projectmanager_image']['name'] )
			$tail = $this->uploadImage( $dataset_id, $_FILES['projectmanager_image']['name'], $_FILES['projectmanager_image']['size'], $_FILES['projectmanager_image']['tmp_name'] );
		
		return __( 'New dataset added to the database.', 'projectmanager' ).' '.$tail;
	}
		
		
	/**
	 * edit dataset
	 *
	 * @param int $project_id
	 * @param string $name
	 * @param int $group
	 * @param int $dataset_id
	 * @param array $dataset_meta
	 * @param boolean $del_image
	 * @param string $image_file
	 * @return string
	 */
	function editDataset( $project_id, $name, $group, $dataset_id, $dataset_meta = false, $del_image = false, $image_file = '', $overwrite_image = false )
	{
		global $wpdb;
		$this->project_id = $project_id;

		$tail = '';
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `name` = '%s', `grp_id` = '%d' WHERE `id` = '%d'", $name, $group, $dataset_id ) );
			
		if ( $dataset_meta ) {
			foreach ( $dataset_meta AS $meta_id => $meta_value ) {
				if ( is_array($meta_value) ) {
					// form field value is a date
					if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) )
						$meta_value = $meta_value['year'].'-'.str_pad($meta_value['month'], 2, 0, STR_PAD_LEFT).'-'.str_pad($meta_value['day'], 2, 0, STR_PAD_LEFT);
				}
				
				if ( 1 == $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = '".$dataset_id."' AND `form_id` = '".$meta_id."'" ) )
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '%s' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", $meta_value, $dataset_id, $meta_id ) );
				else
					$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '%s' )", $meta_id, $dataset_id, $meta_value ) );
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
			$tail = $this->uploadImage($dataset_id, $_FILES['projectmanager_image']['name'], $_FILES['projectmanager_image']['size'], $_FILES['projectmanager_image']['tmp_name'], $overwrite_image);
			
			
		return __('Dataset updated.', 'projectmanager').' '.$tail;
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
		@unlink( $this->getImagePath($image) );
		@unlink( $this->getImagePath('/thumb.'.$image) );
		@unlink( $this->getImagePath('/tiny.'.$image) );
	}
		
		
	/**
	 * check if datasets have details
	 * 
	 * @param int $project_id
	 * @return boolean
	 */
	function hasDetails( $project_id )
	{
		global $wpdb;
		$num_form_fields = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$project_id} AND `show_on_startpage` = 0" );
			
		if ( $num_form_fields > 0 )
			return true;
		
		return false;
	}
	
	
	/**
	 * set image path in database and upload image to server
	 *
	 * @param int  $dataset_id
	 * @param string $img_name
	 * @param int $img_size
	 * @param string $img_tmp_name
	 * @param string $uploaddir
	 * @param boolean $overwrite_image
	 * @return void | string
	 */
	function uploadImage( $dataset_id, $img_name, $img_size, $img_tmp_name, $overwrite_image = false )
	{
		global $wpdb;
		
		if ( $this->ImageTypeIsSupported($img_name) ) {
			$uploaddir = $this->getImagePath();
			$options = get_option('projectmanager');
				
			/*
			* Delete old images from server and clean database entry
			*/
			if ( $img_size > 0 ) {
				if ( $result = $wpdb->get_results( "SELECT `image` FROM {$wpdb->projectmanager_dataset} WHERE `id` = '".$dataset_id."'" ) ) {
					if ( $result[0]->image != basename($img_name) AND $result[0]->image != '' ) {
						$this->delImage($result[0]->image);
						$wpdb->query("UPDATE {$wpdb->projectmanager_dataset} SET `image` = '' WHERE `id` = {$dataset_id}");
					}
				}
			}
	
		
			/*
			* Upload Image to Server
			*/
			if ( $img_size > 0 ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", basename($img_name), $dataset_id ) );
	
				$uploadfile = $uploaddir.'/'.basename($img_name);
				if ( file_exists($uploadfile) && !$overwrite_image ) {
					return __('File exists and is not uploaded.','projectmanager');
				} else {
					if ( move_uploaded_file($img_tmp_name, $uploadfile) ) {
						$thumb = new Thumbnail($uploadfile);
						
						// Resize original file
						$normal_width = $options[$this->project_id]['medium_size']['width'];
						$normal_height = $options[$this->project_id]['medium_size']['height'];
						$thumb->resize( $normal_width, $normal_height );
						$thumb->save($uploadfile);
						
						// Create normal Thumbnail
						$thumb_width = $options[$this->project_id]['thumb_size']['width'];
						$thumb_height = $options[$this->project_id]['thumb_size']['height'];
						$thumb->resize( $thumb_width, $thumb_height );
						$thumb->save($uploaddir.'/thumb.'.basename($img_name));
									
						// Create tiny Thumbnail
						$thumb->resize(80,50);
						$thumb->save($uploaddir.'/tiny.'.basename($img_name));
					} else
						return __('An upload error occured. Please try again.','projectmanager');
				}
			}
		} else {
			return __('The file type is not supported. No Image was uploaded.','projectmanager');
		}
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
					
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projectmeta} SET `label` = '%s', `type` = '%d', `show_on_startpage` = '%d', `order` = '%d' WHERE `id` = '%d' LIMIT 1 ;", $form_label, $type, $show_on_startpage, $order, $form_id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `form_id` = '%d' WHERE `form_id` = '%d'", $form_id, $form_id ) );
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
					
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_projectmeta} (`label`, `type`, `show_on_startpage`, `order`, `project_id`) VALUES ( '%s', '%d', '%d', '%d', '%d');", $form_label, $type, $show_on_startpage, $order, $project_id ) );
				$form_id = mysql_insert_id();
					
				/*
				* Populate default values for every dataset
				*/
				if ( $datasets = $this->getDataset() ) {
					foreach ( $datasets AS $dataset ) {
						$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '' );", $form_id, $dataset->id ) );
					}
				}
			}
		}
			
		return __('Form Fields updated', 'projectmanager');
	}
		 
		
	/**
	 * replace shortcodes with respective HTML in posts or pages
	 *
	 * @param string $content
	 * @return string
	 */
	function insert( $content )
	{
		if ( stristr( $content, '[prjctmngr_search' )) {
			$search = "@\[prjctmngr_search_form\s*=\s*(\w+),(|right|center|left|)\]@i";
	
			if ( preg_match_all($search, $content , $matches) ) {
				if (is_array($matches)) {
					foreach($matches[1] AS $key => $v0) {
						$project_id = $v0;
						$search = $matches[0][$key];
						$replace = $this->getSearchForm( $project_id, $matches[2][$key] );
			
						$content = str_replace($search, $replace, $content);
					}
				}
			}
		}
		
		if ( stristr( $content, '[prjctmngr_group' )) {
			$search = "@\[prjctmngr_group_selection\s*=\s*(\w+),(|dropdown|list|),(|left|center|right|)\]@i";
		
			if ( preg_match_all($search, $content , $matches) ) {
				if (is_array($matches)) {
					foreach($matches[1] AS $key => $v0) {
						$project_id = $v0;
						$search = $matches[0][$key];
						$replace = $this->getGroupSelection( $project_id, $matches[2][$key], $matches[3][$key] );
				
						$content = str_replace($search, $replace, $content);
					}
				}
			}
		}
		
		if ( stristr( $content, '[dataset_list' )) {
			$search = "@\[dataset_list\s*=\s*(\w+),(|\d+),(|table|ul|ol|)\]@i";
		
			if ( preg_match_all($search, $content , $matches) ) {
				if (is_array($matches)) {
					foreach($matches[1] AS $key => $v0) {
						$project_id = $v0;
						$search = $matches[0][$key];
						$replace = apply_filters('projectmanager_dataset_list', '', $project_id, $matches[3][$key], $matches[2][$key]);
			
						$content = str_replace($search, $replace, $content);
					}
				}
			}
		}
		
		if ( stristr( $content, '[dataset_gallery' )) {
			$search = "@\[dataset_gallery\s*=\s*(\w+),(\d+),(|\d+)\]@i";
		
			if ( preg_match_all($search, $content , $matches) ) {
				if (is_array($matches)) {
					foreach($matches[1] AS $key => $v0) {
						$project_id = $v0;
						$search = $matches[0][$key];
						$replace =  apply_filters('projectmanager_dataset_gallery', '', $project_id, $matches[2][$key], $matches[3][$key]); 
			
						$content = str_replace($search, $replace, $content);
					}
				}
			}
		}

		$content = str_replace('<p></p>', '', $content);
		return $content;
	}
	

	/**
	 * create search formular
	 *
	 * @param string $style
	 */
	function getSearchForm( $project_id, $pos )
	{
		$this->project_id = $project_id;
		$search_string = ($this->isSearch()) ? $this->getSearchString() : '';
		$form_field_id = isset( $_POST['form_field'] ) ? $_POST['form_field'] : 0;
		
		if ( !isset($_GET['show'])) {
			$out = "</p>\n\n<div class='projectmanager_".$pos."'>\n<form class='projectmanager' action='' method='post'>";
			$out .= "\n\t<input type='text' name='projectmanager_search' value='".$search_string."' />";
			if ( $form_fields = $this->getFormFields() ) {
				$out .= "\n\t<select size='1' name='form_field'>";
				$out .= "\n\t\t<option value='0'>".__( 'Name', 'projectmanager' )."</option>";
				foreach ( $form_fields AS $form_field ) {
					$selected = ( $form_field_id == $form_field->id ) ? " selected='selected'" : "";
					$out .= "\n\t\t<option value='".$form_field->id."'".$selected.">".$form_field->label."</option>";
				}
				$out .= "\n\t</select>";
			} else
				$out .= "\n\t<input type='hidden' name='form_field' value='0' />";
				
			$out .= "\n\t<input type='submit' value='".__('Search', 'projectmanager')." &raquo;' class='button' />";
			$out .= "\n</form>\n</div>\n\n<p>";
		}

		return $out;
	}
	function printSearchForm( $project_id, $pos )
	{
		echo $this->getSearchForm( $project_id, $pos );
	}
		
	
	/**
	 * get group selection
	 *
	 * @param int $project_id
	 * @param string $type 'dropdown' | 'list'
	 */
	function getGroupSelection( $project_id, $type, $pos )
	{
		if ( 'dropdown' == $type )
			return $this->getGroupDropdown($project_id,$pos);
		elseif ( 'list' == $type )
			return $this->getGroupList($project_id,$pos);
	}
	
	
	/**
	 * get group dropdown
	 *
	 * @param int $project_id
	 * @return string
	 */
	function getGroupDropdown( $project_id, $pos )
	{
		global $wpdb, $wp_query;
		
		if ( is_admin() ) {
			$hidden = "\n<input type='hidden' name='page' value='".$_GET['page']."' />\n<input type='hidden' name='id' value='".$project_id."' />";
			$action = 'edit.php';
		} else {
			$page_obj = $wp_query->get_queried_object();
			$page_ID = $page_obj->ID;
		
			$hidden = "\n<input type='hidden' name='page_id' value='".$page_ID."' />";
			$action = get_permalink($page_ID);
		}
		
		$options = get_option( 'projectmanager' );
		
		$out = "</p>";
		if ( !isset($_GET['show'])) {
			$selected = isset($_GET['grp_id']) ? $_GET['grp_id'] : null;
			$out .= "\n\n<div class='projectmanager_".$pos."'>\n<form class='projectmanager' action='".$action."' method='get'>\n";
			$out .= wp_dropdown_categories(array('echo' => 0, 'hide_empty' => 0, 'name' => 'grp_id', 'orderby' => 'name', 'selected' => $selected, 'hierarchical' => true, 'child_of' => $options[$project_id]['category'], 'show_option_all' => __('Groups', 'projectmanager'), 'show_option_none' => '&#8212;&#8212;&#8212;&#8212;&#8212;&#8212;&#8212;&#8212;'));
			$out .= $hidden;
			$out .= "\n<input type='submit' value='".__( 'Go', 'projectmanager' )."' class='button' />";
			$out .= "\n</form>\n</div>\n\n";
		}
		$out .= "<p>";

		return $out;
	}
	
	/**
	 * get group list
	 *
	 * @param int $proeject_id
	 * @return string
	 */
	function getGroupList( $project_id, $pos )
	{
		global $wpdb;
		$options = get_option( 'projectmanager' );
		
		$out = '</p>';
		if ( !isset($_GET['show'])) {
			$out = "\n<div class='projectmanager_".$pos."'>\n\t<ul>";
			$out .= wp_list_categories(array('echo' => 0, 'title_li' => __('Groups', 'projectmanager'), 'child_of' => $options[$project_id]['category']));
			$out .= "\n\t</ul>\n</div>";
		}
		$out .= '<p>';
		
		return $out;
	}
	
	
	/**
	 * get dataset list
	 *
	 * @param int $project_id
	 * @param string $output
	 * @param ing $grp_id
	 * @return string
	 */
	function getDatasetList( $out = '', $project_id, $output = 'table', $grp_id = false )
	{
		$this->setSettings($project_id);
		if ( $grp_id ) $this->setGroup($grp_id);
	
		if ( isset( $_GET['show'] ) ) {
			$out .= apply_filters( 'projectmanager_single_view', $out, $project_id, $_GET['show'] );
		} else {
			if ( $this->isSearch() )
				$datasets = $this->getSearchResults($this->getSearchString(), $_POST['form_field']);
			else
				$datasets = $this->getDataset( null, 'name ASC', true, $project_id );
			
			$out .= "</p>";
			if ( $datasets ) {
				$num_datasets = ( $this->isSearch() ) ? count($datasets) : $this->getNumDatasets();
				$num_total_datasets = $this->getNumDatasets($project_id);
				$out .= "\n<div id='projectmanager_datasets_header'>";
				$out .= "\n\t<p>".sprintf(__('%d of %d Datasets', 'projectmanager'),$num_datasets, $num_total_datasets )."</p>";
				if ( $this->isSearch() )
					$out .= "<h3>".sprintf(__('Search: %d of %d', 'projectmanager'), $num_datasets, $this->getNumDatasets($project_id))."</h3>";
				elseif ( $this->isGroup() && !$grp_id )
					$out .= "<h3>".$this->getGroupTitle($this->getGroup())."</h3>";
				$out .= "\n</div>";
				
				if ( 'table' == $output ) {
					$out .= "\n<table class='projectmanager'>\n<tr>\n";
					$out .= "\t<th scope='col'>".__( 'Name', 'projectmanager' )."</th>";
					$out .= $this->getTableHeader();
					$out .= "\n</tr>";
				} else {
					$out .= "\n<".$output." class='projectmanager'>";
				}
				
				$dataset_output = ( 'table' == $output ) ? 'td' : 'li';
				foreach ( $datasets AS $dataset ) {
					$name = ($this->hasDetails($project_id)) ? '<a href="'.$this->pagination->createURL().'?grp_id='.$this->getGroup().'&amp;show='.$dataset->id.'">'.$dataset->name.'</a>' : $dataset->name;
					
					$class = ("alternate" == $class) ? '' : "alternate";
					
					if ( 'table' == $output )
						$out .= "\n<tr class='".$class."'><td>".$name."</td>";
					else
						$out .= "\n\t<li>".$name."</li>";
						
					$out .= $this->getDatasetMetaData( $dataset->id, $dataset_output );
					
					if ( 'table' == $output )
						$out .= "\n</tr>";
				}
				
				$out .= "\n</$output>\n";
			
				if ( !$this->isSearch() )
				$out .= $this->pagination->get();
			}
			$out .= "<p>";
		}
		
		return $out;
	}
	
	
	/**
	 * get dataset as gallery
	 *
	 * @param int $project_id
	 * @param int $num_cols
	 * @param int $grp_id
	 * @return string
	 */
	function getGallery( $out = '', $project_id, $num_cols, $grp_id = false )
	{
		$options = get_option( 'projectmanager' );
		
		$this->setSettings($project_id);
		if ( $grp_id ) $this->setGroup($grp_id);
					
		if ( isset( $_GET['show'] ) ) {
			$out .= apply_filters( 'projectmanager_single_view', $out, $project_id, $_GET['show'] );
		} else {
			if ( $this->isSearch() )
				$datasets = $this->getSearchResults($this->getSearchString(), $_POST['form_field']);
			else
				$datasets = $this->getDataset( null, 'name ASC', true, $project_id );
			
			$out .= "</p>";
			if ( $datasets ) {
				$out .= "\n\n<table class='projectmanager' summary='' title=''>\n<tr>";
				
				foreach ( $datasets AS $dataset ) {
					$i++;
					$before_name = '<a href="'.$this->pagination->createURL().'&amp;grp_id='.$this->getGroup().'&amp;show='.$dataset->id.'">';
					$after_name = '</a>';
					
					$out .= "\n\t<td style='padding: 5px;'>";
					if ($options[$project_id]['show_image'] == 1 && '' != $dataset->image)
						$out .= "\n\t\t".$before_name.'<img src="'.$this->getImageUrl('/thumb.'.$dataset->image).'" alt="'.$dataset->name.'" title="'.$dataset->name.'" />'.$after_name;
					
					$out .= "\n\t\t<p class='caption'>".$before_name.$dataset->name.$after_name."</p>";
					$out .= "\n\t</td>";
				
					if ( ( ( 0 == $i % $num_cols)) && ( $i < count($datasets) ) )
						$out .= "\n</tr>\n<tr>";
				}
				
				$out .= "\n</tr>\n</table>\n\n";
		
				$out .= $this->pagination->get();
			}
			$out .= "<p>";
		}
		
		return $out;
	}
	
	
	/**
	 * get details on dataset
	 *
	 * @param int $dataset_id
	 * @return string
	 */
	function getSingleView( $out, $project_id, $dataset_id )
	{
		$offset = $this->getDatasetOffset( $dataset_id ) + 1;
		$page = ceil($offset/$this->getPerPage());
		$options = get_option( 'projectmanager' );
	
		$out = "</p>";
		$out .= "\n<p class='return_to_overview'><a href='".$this->pagination->createURL()."'paging=".$page.">".__('Back to list', 'projectmanager')."</a></p>\n";
		
		if ( $dataset = $this->getDataset( $dataset_id ) ) {
			$out .= "<fieldset class='dataset'><legend>".__( 'Details of', 'projectmanager' )." ".$dataset[0]->name."</legend>\n";
			if ($options[$project_id]['show_image'] == 1 && '' != $dataset[0]->image)
				$out .= "\t<img src='".$this->getImageUrl($dataset[0]->image)."' title='".$dataset[0]->name."' alt='".$dataset[0]->name."' style='float: right;' />\n";
				
			$out .= "<dl>".$this->getDatasetMetaData( $dataset_id, 'dl', true )."\n</dl>\n";
			$out .= "</fieldset>\n";
		}
		
		$out .= "<p>";
		
		return $out;
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
		$this->setSettings($project_id);
		
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
				$name = ($this->hasDetails($project_id)) ? '<a href="'.get_permalink($options[$project_id]['page_id']).'&amp;show='.$dataset->id.'">'.$dataset->name.'</a>' : $dataset->name;
			
				echo "<li>".$name."</li>";
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
			$options[$project_id]['page_id'] = $_POST['page_id'][$project_id];
				
			update_option( 'projectmanager_widget', $options );
		}
			
		echo '<p style="text-align: left;"><label for="widget_title" class="projectmanager-widget">'.__('Title', 'projectmanager').'</label><input type="text" name="widget_title['.$project_id.']" id="widget_title" value="'.$options[$project_id]['title'].'" /></p>';
		echo '<p style="text-align: left;"><label for="limit" class="projectmanager-widget">'.__('Number', 'projectmanager').'</label><select style="margin-top: 0;" size="1" name="limit['.$project_id.']" id="limit">';
		for ( $i = 1; $i <= 10; $i++ ) {
			$selected = ( $options[$project_id]['limit'] == $i ) ? " selected='selected'" : '';
			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		echo '</select></p>';
		echo '<p style="text-align: left;"><label for="page_id['.$project_id.']" class="projectmanager-widget">'.__('Page').'</label>';
		wp_dropdown_pages(array('name' => 'page_id['.$project_id.']', 'selected' => $options[$project_id]['page_id']));
		echo '</p>';
			
		echo '<input type="hidden" name="projectmanager-submit" value="1" />';
	}
	
	
	/**
	 * add TinyMCE Button
	 *
	 * @param none
	 * @return void
	 */
	function addTinyMCEButton()
	{
		// Don't bother doing this stuff if the current user lacks permissions
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) return;
		
		// Check for LeagueManager capability
		if ( !current_user_can('manage_projects') ) return;
		
		// Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", array(&$this, 'addTinyMCEPlugin'));
			add_filter('mce_buttons', array(&$this, 'registerTinyMCEButton'));
		}
	}
	function addTinyMCEPlugin( $plugin_array )
	{
		$plugin_array['ProjectManager'] = $this->plugin_url.'/tinymce/editor_plugin.js';
		return $plugin_array;
	}
	function registerTinyMCEButton( $buttons )
	{
		array_push($buttons, "separator", "ProjectManager");
		return $buttons;
	}
	function changeTinyMCEVersion( $version )
	{
		return ++$version;
	}
	
	
	/**
	 * print breadcrumb navigation
	 *
	 * @param int $project_id
	 * @param string $page_title
	 * @param boolean $start
	 */
	function printBreadcrumb( $project_id, $page_title, $start=false )
	{
		echo '<p class="projectmanager_breadcrumb">';
		if ( !$this->single )
			echo '<a href="edit.php?page=projectmanager/page/index.php">'.__( 'Projectmanager', 'projectmanager' ).'</a> &raquo; ';
		
		if ( $page_title != $this->getProjectTitle( $project_id ) )
			echo '<a href="edit.php?page=projectmanager/page/show-project.php&amp;id='.$project_id.'">'.$this->getProjectTitle( $project_id ).'</a> &raquo; ';
		
		if ( !$start || ($start && !$this->single) ) echo $page_title;
		
		echo '</p>';
	}
	
	
	/**
	 * Add Code to Wordpress Header
	 *
	 * @param none
	 */
	function addHeaderCode($show_all=false)
	{
		global $wp_version;
		
		echo "\n\n<!-- WP-ProjectManager START -->\n";
		echo "<link rel='stylesheet' href='".$this->plugin_url."/style.css' type='text/css' />\n";
		
		if ( is_admin() AND ((isset( $_GET['page'] ) AND substr( $_GET['page'], 0, 14 ) == 'projectmanager') || $show_all )) {
			wp_register_script( 'projectmanager', $this->plugin_url.'/js/functions.js', array( ), PROJECTMANAGER_VERSION );
			wp_register_script( 'projectmanager_formfields', $this->plugin_url.'/js/formfields.js', array( 'projectmanager' ), PROJECTMANAGER_VERSION );
			wp_register_script ('projectmanager_ajax', $this->plugin_url.'/js/ajax.js', array( 'sack', 'thickbox', 'projectmanager' ), PROJECTMANAGER_VERSION );
		
			wp_print_scripts( 'projectmanager_formfields' );
			wp_print_scripts( 'projectmanager_ajax');
			
			echo '<link rel="stylesheet" href="'.get_option( 'siteurl' ).'/wp-includes/js/thickbox/thickbox.css" type="text/css" media="screen" />';
			
			echo "<script type='text/javascript'>\n";
			echo "var PRJCTMNGR_HTML_FORM_FIELD_TYPES = \"";
			foreach ($this->getFormFieldTypes() AS $form_type_id => $form_type)
				echo "<option value='".$form_type_id."'>".$form_type."</option>";
			echo "\";\n";
			
			?>
			//<![CDATA[
			ProjectManagerAjaxL10n = {
				blogUrl: "<?php bloginfo( 'wpurl' ); ?>", pluginPath: "<?php echo $this->plugin_path; ?>", pluginUrl: "<?php echo $this->plugin_url; ?>", requestUrl: "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php", imgUrl: "<?php echo $this->plugin_url; ?>/images", Edit: "<?php _e("Edit"); ?>", Post: "<?php _e("Post"); ?>", Save: "<?php _e("Save"); ?>", Cancel: "<?php _e("Cancel"); ?>", pleaseWait: "<?php _e("Please wait..."); ?>", Revisions: "<?php _e("Page Revisions"); ?>", Time: "<?php _e("Insert time"); ?>"
				   }
			//]]>
			<?php
			echo "</script>\n";
		}
		echo "<!-- WP-ProjectManager END -->\n\n";
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
		
		$options = array();
		$options['version'] = PROJECTMANAGER_VERSION;
		
		$old_options = get_option( 'projectmanager' );
		if ( version_compare($old_options['version'], PROJECTMANAGER_VERSION, '<') ) {
			require_once( $this->plugin_path . '/projectmanager-upgrade.php' );
			$options = $old_options;
			$options['version'] = PROJECTMANAGER_VERSION;
			update_option( 'projectmanager', $options );
		}
		
		$charset_collate = '';
		if ( $wpdb->supports_collation() ) {
			if ( ! empty($wpdb->charset) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate) )
				$charset_collate .= " COLLATE $wpdb->collate";
		}
		
		$create_projects_sql = "CREATE TABLE {$wpdb->projectmanager_projects} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`title` varchar( 50 ) NOT NULL default '',
						PRIMARY KEY ( `id` )) $charset_collate";
		maybe_create_table( $wpdb->projectmanager_projects, $create_projects_sql );
			
		$create_projectmeta_sql = "CREATE TABLE {$wpdb->projectmanager_projectmeta} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`type` int( 11 ) NOT NULL ,
						`label` varchar( 100 ) NOT NULL default '' ,
						`order` int( 10 ) NOT NULL ,
						`show_on_startpage` tinyint( 1 ) NOT NULL ,
						`project_id` int( 11 ) NOT NULL ,
						PRIMARY KEY ( `id` )) $charset_collate";
		maybe_create_table( $wpdb->projectmanager_projectmeta, $create_projectmeta_sql );
				
		$create_dataset_sql = "CREATE TABLE {$wpdb->projectmanager_dataset} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`name` varchar( 150 ) NOT NULL default '' ,
						`image` varchar( 50 ) NOT NULL default '' ,
						`grp_id` int( 11 ) NOT NULL ,
						`project_id` int( 11 ) NOT NULL ,
						PRIMARY KEY ( `id` )) $charset_collate";
		maybe_create_table( $wpdb->projectmanager_dataset, $create_dataset_sql );
			
		$create_datasetmeta_sql = "CREATE TABLE {$wpdb->projectmanager_datasetmeta} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`form_id` int( 11 ) NOT NULL ,
						`dataset_id` int( 11 ) NOT NULL ,
						`value` longtext NOT NULL default '' ,
						PRIMARY KEY ( `id` )) $charset_collate";
		maybe_create_table( $wpdb->projectmanager_datasetmeta, $create_datasetmeta_sql );


		/*
		* Set default options
		*/
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
		* Create directory for projects
		*/
		if ( !file_exists($this->getImagePath()) )
			mkdir( $this->getImagePath() );
	}
	
	
	/**
	 * adds admin menu
	 *
	 * @param none
	 */
	function addAdminMenu()
	{
		global $wpdb;
		
		$this->single = false;
		if ( $projects = $this->getProjects() ) {
			$options = get_option( 'projectmanager' );
			foreach( $projects AS $project ) {
				if ( 1 == $options[$project->id]['navi_link'] ) {
					$management_page = 'edit.php?page=projectmanager/page/show-project.php&id='.$project->id;
					add_management_page( $project->title, $project->title, 'manage_projects', $management_page );
				}
			}
			foreach ( $projects AS $project ) {
				if ( 1 == $options[$project->id]['navi_link'] && $this->getNumProjects() == 1) {
					$this->single = true;
					break;
				}
			}
		}
		
		if ( ! $this->single )
			add_management_page( __( 'Projects', 'projectmanager' ), __( 'Projects', 'projectmanager' ), 'manage_projects', basename( __FILE__, ".php" ).'/page/index.php' );
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
