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
	 * ID of current project
	 *
	 * @var int
	 */
	var $project_id;
	
	
	/**
	 * selected category
	 *
	 * @var int
	 */
	var $cat_id;
		
		
	/**
	 * number of dataset per page
	 *
	 * @var int
	 */
	var $per_page;
		
	
	/**
	 * form fields
	 *
	 * @param array
	 */
	var $form_fields = false;
	
	
	/**
	 * error handling
	 *
	 * @param boolean
	 */
	var $error = false;
	
	
	/**
	 * error message
	 *
	 * @param string
	 */
	var $message = '';
	
	
	/**
	 * __construct() - Initialize project settings
	 *
	 * @param int $project_id ID of selected project. false if none is selected
	 * @return void
	 */
	function __construct( $project_id )
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
		$this->setCatID();

		if ( $project_id )
			$this->initialize($project_id);
		
		return;
	}
	/**
	 * WP_ProjectManager() - Wrapper function to sustain downward compatibility to PHP 4.
	 *
	 * Wrapper function which calls constructor of class
	 *
	 * @param int $project_id
	 * @return none
	 */
	function WP_ProjectManager( $project_id )
	{
		$this->__construct( $project_id );
	}
	
	
	/**
	 * init() - Initialize project settings
	 *
	 * @param int $project_id
	 * @return void
	 */
	function initialize( $project_id )
	{
		$options = get_option( 'projectmanager' );
		$this->project_id = $project_id;
		$this->per_page = isset($options[$this->project_id]['per_page']) ? $options[$this->project_id]['per_page'] : 20;

		$this->num_items = $this->getNumDatasets($this->project_id);
		$this->num_max_pages = ( 0 == $this->per_page || $this->isSearch() ) ? 1 : ceil( $this->num_items/$this->per_page );
	}
	
	
	/**
	 * getCurrentPage() - retrieve current page
	 *
	 * @param none
	 * @return int
	 */
	function getCurrentPage()
	{
		global $wp;
		if (isset($wp->query_vars['paged']))
			$this->current_page = max(1, intval($wp->query_vars['paged']));
		elseif (isset($_GET['paged']))
			$this->current_page = (int)$_GET['paged'];
		else
			$this->current_page = 1;

		return $this->current_page;
	}
	
	
	/**
	 * getNumPages() - retrieve number of pages
	 *
	 * @param none
	 * @return int
	 */
	function getNumPages()
	{
		return $this->num_max_pages;
	}

	
	/**
	 * getPerPage() - gets object limit per page
	 *
	 * @param none
	 * @return int
	 */
	function getPerPage()
	{
		return $this->per_page;
	}
		
		
	/**
	 * setPerPage() - sets number of objects per page
	 *
	 * @param int
	 * @return void
	 */
	function setPerPage( $per_page )
	{
		$this->per_page = $per_page;
		$this->pagination->setPerPage( $per_page );
		
		return;
	}
	
	
	/**
	 * getPageLinks() - display pagination
	 *
	 * @param none
	 * @return string
	 */
	function getPageLinks()
	{
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => '&#9668;',
			'next_text' => '&#9658;',
			'total' => $this->getNumPages(),
			'current' => $this->getCurrentPage()
		));
		return $page_links;
	}
	

	/**
	 * getFormFieldTypes() - returns array of form field types
	 *
	 * @param none
	 * @return array
	 */
	function getFormFieldTypes()
	{
		$form_field_types = array( 1 => __('Text', 'projectmanager'), 2 => __('Textfield', 'projectmanager'), 3 => __('E-Mail', 'projectmanager'), 4 => __('Date', 'projectmanager'), 5 => __('URL', 'projectmanager'), 6 => __('Selection', 'projectmanager'), 7 => __( 'Checkbox List', 'projectmanager'), 8 => __( 'Radio List', 'projectmanager') );
		return $form_field_types;
	}
	
	
	/**
	 * getMonths() - returns array of months in appropriate language depending on Wordpress locale
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
	 * error() - check if an error occured
	 *
	 * @param none
	 * @return boolean
	 */
	function error()
	{
		return $this->error;
	}
	
	
	/**
	 * getErrorMessage() - return error message
	 *
	 * @param none
	 */
	function getErrorMessage()
	{
		if ($this->error)
			return $this->message;
	}
	
	
	/**
	 * printErrorMessage() - print formatted error message
	 *
	 * @param none
	 */
	function printErrorMessage()
	{
		echo "\n<div class='error'><p>".$this->getErrorMessage()."</p></div>";
	}
	
	
	/**
	 * printMessage() - print formatted success or error message
	 *
	 * @param none
	 */
	function printMessage()
	{
		if ( $this->error )
			echo "\n<div class='error'><p>".$this->message."</p></div>";
		else
			echo "\n<div id='message' class='updated fade'><p><strong>".$this->message."</strong></p></div>";
	}
	
	
	/**
	 * getImagePath() - returns image directory
	 *
	 * @param string | false $file
	 * @return string
	 */
	function getImagePath( $file = false )
	{
		if ( $file )
			return WP_CONTENT_DIR.'/uploads/projects/'.$file;
		else
			return WP_CONTENT_DIR.'/uploads/projects';
	}
	
	
	/**
	 * getImageUrl() - returns url of image directory
	 *
	 * @param string | false $file image file
	 * @return string
	 */
	function getImageUrl( $file = false )
	{
		if ( $file )
			return WP_CONTENT_URL.'/uploads/projects/'.$file;
		else
			return WP_CONTENT_URL.'/uploads/projects';
	}
	
	
	/**
	 * getProjectID() - gets project ID
	 *
	 * @param none
	 * @return int
	 */
	function getProjectID()
	{
		return $this->project_id;
	}
	
	
	/**
 	 * setCategory() - sets current category
	 *
	 * @param int $cat_id
	 * @return void
	 */
	function setCatID( $cat_id = false )
	{
		if ( $cat_id )
			$this->cat_id = $cat_id;
		elseif ( isset($_GET['cat_id']) )
			$this->cat_id = (int)$_GET['cat_id'];
		elseif ( isset($_POST['cat_id']) )
			$this->cat_id = (int)$_POST['cat_id'];
		else
			$this->cat_id = null;
			
		return;
	}
	
	
	/**
	 * getCat() - gets current category
	 * 
	 * @param none
	 * @return int
	 */
	function getCatID()
	{
		return $this->cat_id;
	}
		
	
	/**
	 * getCatTitle() - gets group title
	 *
	 * @param int $cat_id
	 * @return string
	 */
	function getCatTitle( $cat_id = false )
	{
		if ( !$cat_id ) $cat_id = $this->getCatID();
		$c = get_category($cat_id);
		return $c->name;
	}
	
	
	/**
	 * isCategory() check if category is selected
	 * 
	 * @param none
	 * @return boolean
	 */
	function isCategory()
	{
		if ( null != $this->getCatID() )
			return true;
		
		return false;
	}
	

	/**
	 * isSearch() - check if search was performed
	 *
	 * @param none
	 * @return boolean
	 */
	function isSearch()
	{
		if ( isset( $_POST['search_string'] ) )
			return true;
	
		return false;
	}
	
	
	/**
	 * getSearchString() - returns search string
	 *
	 * @param none
	 * @return string
	 */
	function getSearchString()
	{
		if ( $this->isSearch() )
			return $_POST['search_string'];

		return '';
	}
	
	
	/**
	 * getSearchOption() - gets form field ID of search request
	 *
	 * @param none
	 * @return int
	 */
	function getSearchOption()
	{
		if ( $this->isSearch() )
			return $_POST['search_option'];
		
		return 0;
	}
	
	
	/**
	 * getSupportedImageTypes() - gets supported file types
	 *
	 * @param none
	 * @return array
	 */
	function getSupportedImageTypes()
	{
		return $this->supported_image_types;
	}
	
	
	/**
	 * imageTypeisSupported() - checks if image type is supported
	 *
	 * @param string $filename image file
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
	 * getImageType() - gets image type of supplied image
	 *
	 * @param string $filename image file
	 * @return string
	 */
	function getImageType( $filename )
	{
		$file_info = pathinfo($filename);
		return strtolower($file_info['extension']);
	}
	
	
	/**
	 * getProjectTitle() - gets project title
	 *
	 * @param none
	 * @return string
	 */
	function getProjectTitle( )
	{
		$project = $this->getProject( $this->project_id );
		return $project->title;
	}
	
	
	/**
	 * getNumProjects() - get number of projects
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
	 * getProjects() - gets all projects from database
	 *
	 * @param int $project_id (optional)
	 * @return array
	 */
	function getProjects()
	{
		global $wpdb;
		
		return $wpdb->get_results( "SELECT `title`, `id` FROM {$wpdb->projectmanager_projects} ORDER BY `id` ASC" );
	}
	
	
	/**
	 * getProject() - gets one project
	 *
	 * @param int $project_id
	 * @return array
	 */
	function getProject( $project_id )
	{
		global $wpdb;
		$projects = $wpdb->get_results( "SELECT `title`, `id` FROM {$wpdb->projectmanager_projects} WHERE `id` = {$project_id} ORDER BY `id` ASC" );
		return $projects[0];
	}
	
	
	/**
	 * getWidgetProjects() - gets all widgedized projects
	 *
	 * @param none
	 * @return array
	 */
	function getWidgetProjects()
	{
		global $wpdb;
		$projects = $this->getProjects();
		$options = get_option( 'projectmanager' );
		
		$widget_projects = array();
		foreach ( $projects AS $project ) {
			if ( 1 == $options[$project->id]['use_widget'] )
				$widget_projects[] = $project;
		}
		return $widget_projects;
	}
	
	
	/**
	 * getFormFields() - gets form fields for project
	 *
	 * @param none
	 * @return array
	 */
	function getFormFields()
	{
		global $wpdb;
	
		$sql = "SELECT `label`, `type`, `order`, `order_by`, `show_on_startpage`, `id` FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$this->project_id} ORDER BY `order` ASC;";
		return $wpdb->get_results( $sql );
	}
	
	
	/**
	* getNumFormFields() - gets number of form fields for a specific project
	*
	* @param none
	* @return int
	*/
	function getNumFormFields( )
	{
		global $wpdb;
	
		$num_form_fields = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$this->project_id}" );
		return $num_form_fields;
	}
	
	
	/**
	 * categoryChecklist() - gets checklist for groups. Adopted from wp-admin/includes/template.php
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
	 * getSelectedCategoryIDs() - get selected categories for dataset
	 *
	 * @param object $dataset
	 * @return array
	 */
	function getSelectedCategoryIDs( $dataset )
	{
		$cat_ids = maybe_unserialize($dataset->cat_ids);
		if ( !is_array($cat_ids) )
			$cat_ids = array($cat_ids);
		return $cat_ids;
	}
	
	
	/**
	 * getSelectedCategoryTitles() - get selected categories string
	 *
	 * @param array $cat_ids
	 * @return string
	 */
	function getSelectedCategoryTitles( $cat_ids )
	{
		if ( !is_array($cat_ids) ) $cat_ids = array();
		
		$categories = array();
		foreach ( $cat_ids AS $cat_id )
			$categories[] = $this->getCatTitle($cat_id);

		return implode(", ", $categories);
	}
	
	
	/**
	 * getCategorySearchString() - gets datasets in a given group
	 *
	 * @param none
	 * @return array
	 */
	function getCategorySearchString( )
	{
		global $wpdb;
		
		$datasets = $wpdb->get_results( "SELECT `id`, `cat_ids` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$this->project_id} ORDER BY `name` ASC" );
								
		if ( !is_array($datasets) ) $datasets = array($datasets);
		
		
		$selected_datasets = array();
		foreach ( $datasets AS $dataset )
			if ( in_array($this->getCatID(), $this->getSelectedCategoryIDs($dataset)) )
				$selected_datasets[] = '`id` = '.$dataset->id;
		
		$sql = ' AND ('.implode(' OR ', $selected_datasets).')';
		return $sql;
	}
	
	
	/**
	 * get possible sorting options for datasets
	 *
	 * @param string $selected
	 * @return string
	 */
	function datasetOrderOptions( $selected )
	{
		$options = array( 'id' => __('ID', 'projectmanager'), 'name' => __('Name','projectmanager'), 'formfields' => __('Formfields', 'projectmanager') );
		
		foreach ( $options AS $option => $title ) {
			$select = ( $selected == $option ) ? ' selected="selected"' : '';
			echo '<option value="'.$option.'"'.$select.'>'.$title.'</option>';
		}
	}
	
	
	/**
	 * getNumDatasets() - gets number of datasets for specific project
	 *
	 * @param int $project_id
	 * @return int
	 */
	function getNumDatasets( $project_id, $all = false )
	{
		global $wpdb;

		$sql = "SELECT COUNT(ID) FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$project_id}";
		if ( $all )
			return $wpdb->get_var( $sql );
		elseif ( $this->isSearch() )
			return count($this->datasets);
		else {
			if ( $this->isCategory() )
				$sql .= $this->getCategorySearchString();

			return $wpdb->get_var( $sql );
		}
	}
		
	
	/**
	 * getDatasets() - gets all datasets for a project
	 *
	 * @param int $dataset_id
	 * @param int $project_id
	 * @param bool $limit
	 * @param string $order
	 * @return array
	 */
	 function getDatasets( $limit = false )
	{
		global $wpdb;
		$options = get_option('projectmanager');
		
		// Set ordering
		if ( !isset($options[$this->project_id]['dataset_orderby']) || $options[$this->project_id]['dataset_orderby'] == '' )
			$orderby = 'name ASC';
		elseif ( $options[$this->project_id]['dataset_orderby'] != 'formfields' )
			$orderby = $options[$this->project_id]['dataset_orderby'].' ASC';
		else
			$orderby = 'name ASC';
			
		if ( $limit ) $offset = ( $this->getCurrentPage() - 1 ) * $this->per_page;

		$sql = "SELECT `id`, `name`, `image`, `cat_ids`, `user_id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$this->project_id}";
		
		if ( $this->isCategory() )
			$sql .= $this->getCategorySearchString();
		
		$sql .=  " ORDER BY $orderby";
		$sql .= ( $limit ) ? " LIMIT ".$offset.",".$this->per_page.";" : ";";
		
		$datasets = $wpdb->get_results($sql);
		
		if ( $options[$this->project_id]['dataset_orderby'] == 'formfields' )
			$datasets = $this->orderDatasetsByFormFields($datasets);
			
		return $datasets;
	}
	
	
	/**
	 * getDataset() - gets single dataset
	 *
	 * @param int $dataset_id
	 * @return array
	 */
	function getDataset( $dataset_id )
	{
		global $wpdb;
		$dataset = $wpdb->get_results( "SELECT `id`, `name`, `image`, `cat_ids`, `user_id` FROM {$wpdb->projectmanager_dataset} WHERE `id` = {$dataset_id}" );
		return $dataset[0];
	}
		
		
	/**
	 * orderDatasetsByFormFields() - order datasets by chosen form fields
	 *
	 * @param array $datasets
	 * @return array
	 */
	function orderDatasetsByFormFields( $datasets )
	{
		global $wpdb;
	
		/*
		* Generate array of parameters to sort datasets by
		*/
		$to_sort = array();
		foreach ( $this->getFormFields( ) AS $form_field )
			if ( 1 == $form_field->order_by )
				array_push( $to_sort, $form_field->id );
		
		/*
		* Only process datasets if there is anything to do
		*/
		if ( $to_sort ) {
			/*
			* Generate array of dataset data to sort and indexed array of unsorted datasets
			*/
			$i = 0;
			$datasets_new = array();
			$dataset_meta = array();
			foreach ( $datasets AS $dataset ) {
				foreach ( $this->getDatasetMeta( $dataset->id ) AS $meta ) {
					$meta_value = $meta->value;
					$dataset_meta[$i][$meta->form_field_id] = $meta_value;
				}
				$dataset_meta[$i]['dataset_id'] = $dataset->id;
				
				$i++;
				
				$datasets_new[$dataset->id] = $dataset;
			}
				
			/*
			*  Generate order arrays
			*/
			$order = array();
			foreach ( $dataset_meta AS $key => $row ) {
				$i=0;
				foreach ( $to_sort AS $form_field_id ) {
					$order[$i][$key] = $row[$form_field_id];
					$i++;
				}
			}
			
			/*
			* Create array of arguments for array_multisort
			*/
			$func_args = array();
			foreach ( $order AS $key => $order_array ) {
				array_push( $func_args, $order_array );
				array_push( $func_args, SORT_ASC );
			}
			
			/*
			* sort datasets with array_multisort
			*/
			$eval = 'array_multisort(';
			for ( $i = 0; $i < count($func_args); $i++ )
				$eval .= "\$func_args[$i],";
			
			$eval .= "\$dataset_meta);";
			eval($eval);
				
			/*
			* Create sorted array of datasets
			*/
			$datasets_ordered = array();
			$x = 0;
			foreach ( $dataset_meta AS $key => $row ) {
				$datasets_ordered[$x] = $datasets_new[$row['dataset_id']];
				$x++;
			}
				
			return $datasets_ordered;
		}
		
		// simply return unsorted datasets
		return $datasets;
	}
	
	
	/**
	 * getDatasetPage() - determine page dataset is on
	 *
	 * @param int $dataset_id
	 * @return int
	 */
	function getDatasetPage( $dataset_id )
	{
		$datasets = $this->getDatasets();
		$offsets = array();
		foreach ( $datasets AS $o => $d ) {
			$offsets[$d->id] = $o;
		}
		$number = $offsets[$dataset_id] +1;
		return ceil($number/$this->getPerPage());
	}
	
	
	/**
	 * getDatasetMeta() - gets meta data for dataset
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
	 * getTableHeader() - gets form field labels as table header
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
	 * getDatasetMetaData() - gets dataset meta data. Output types are list items or table columns
	 *
	 * @param array $dataset
	 * @param string $output td | li | dl (default 'li')
	 * @param boolean $show_all
	 * @return string
	 */
	function getDatasetMetaData( $dataset, $output = 'li', $show_all = false )
	{
		$out = '';
		if ( $dataset_meta = $this->getDatasetMeta( $dataset->id ) ) {
			foreach ( $dataset_meta AS $meta ) {
				/*
				* Check some special field types
				*
				* 1: One line Text
				* 2: Multiple lines Text
				* 3: E-Mail
				* 4: Date
				* 5: External URL
				* 6: Dropdown
				* 7: Checkbox List
				* 8: Radio List
				*/
				if (is_string($meta->value)) $meta_value = htmlspecialchars( $meta->value );
				
				if ( 1 == $meta->type || 6 == $meta->type || 7 == $meta->type || 8 == $meta->type )
					$meta_value = "<span id='datafield".$meta->form_field_id."_".$dataset->id."'>".$meta_value."</span>";
				elseif ( 2 == $meta->type ) {
					if ( strlen($meta_value) > 150 && !$show_all )
						$meta_value = substr($meta_value, 0, 150)."...";
					$meta_value = nl2br($meta_value);
						
					$meta_value = "<span id='datafield".$meta->form_field_id."_".$dataset->id."'>".$meta_value."</span>";
				} elseif ( 3 == $meta->type )
					$meta_value = "<a href='mailto:".$meta_value."'><span id='datafield".$meta->form_field_id."_".$dataset->id."'>".$meta_value."</span></a>";
				elseif ( 4 == $meta->type )
					$meta_value = "<span id='datafield".$meta->form_field_id."_".$dataset->id."'>".mysql2date(get_option('date_format'), $meta_value )."</span>";
				elseif ( 5 == $meta->type )
					$meta_value = "<a href='http://".$meta_value."' target='_blank' title='".$meta_value."'><span id='datafield".$meta->form_field_id."_".$dataset->id."'>".$meta_value."</span></a>";
					
				if ( 1 == $meta->show_on_startpage || $show_all ) {
					if ( '' != $meta_value ) {
						if ( 'dl' == $output ) {
							$out .= "\n\t<dt>".$meta->label."</dt><dd>".$meta_value."</dd>";
						} elseif ( 'li' == $output ) {
							$out .= "\n\t<".$output."><span class='dataset_label'>".$meta->label."</span>:&#160;".$meta_value."</".$output.">";
						} else {
							$out .= "\n\t<".$output.">";
							$out .= $this->getThickbox( $dataset->id, $meta->form_field_id, $meta->type, maybe_unserialize($meta->value), $dataset->user_id );
							$out .= "\n\t\t".$meta_value . $this->getThickboxLink($dataset->id, $meta->form_field_id, $meta->type, $meta->label." ".__('of','projectmanager')." ".$dataset->name, $dataset->user_id);
							$out .= "\n\t</".$output.">";
						}
					} elseif ( 'td' == $output )
						$out .= "\n\t<".$output.">&#160;</".$output.">";
				}
			}
		}
		return $out;
	}
	function printDatasetMetaData( $dataset, $output = 'li', $show_all = false )
	{
		echo $this->getDatasetMetaData( $dataset, $output, $show_all );
	}
		 
		 
	/**
	 * getThickboxLink() - get Thickbox Link for Ajax editing
	 *
	 * @param ing $dataset_id
	 * @param int $formfield_id
	 * @param int $formfield_type
	 * @param string $title
	 * @param int $dataset_owner
	 * @return string
	 */
	function getThickboxLink( $dataset_id, $formfield_id,  $formfield_type, $title, $dataset_owner )
	{
		global $current_user;
		
		$out = '';
		if ( is_admin() && current_user_can( 'manage_projects' ) && ($dataset_owner == $current_user->ID || current_user_can( 'projectmanager_admin')) ) {
			$dims = array('width' => '300', 'height' => '100');
			if ( 2 == $formfield_type )
				$dims = array('width' => '400', 'height' => '400');
			if ( 7 == $formfield_type || 8 == $formfield_type )
				$dims = array('width' => '300', 'height' => '300');
						
			$out .= "&#160;<a class='thickbox' id='thickboxlink".$formfield_id."_".$dataset_id."' href='#TB_inline&height=".$dims['height']."&width=".$dims['width']."&inlineId=datafieldwrap".$formfield_id."_".$dataset_id."' title='".$title."'><img src='".$this->plugin_url."/images/edit.gif' border='0' alt='".__('Edit')."' /></a>";
		}
		return $out;
	}
	
	
	/**
	 * getThickbox() - get Ajax Thickbox
	 *
	 * @param int $dataset_id
	 * @param int $formfield_id
	 * @param int $formfield_type
	 * @param string $value
	 * @param int $dataset_owner
	 * @return string
	 */
	function getThickbox( $dataset_id, $formfield_id, $formfield_type, $value, $dataset_owner )
	{
		global $current_user;
		
		$out = '';
		if ( is_admin() && current_user_can( 'manage_projects' ) && ($dataset_owner == $current_user->ID || current_user_can( 'projectmanager_admin')) ) {
			
			$out .= "\n\t\t<div id='datafieldwrap".$formfield_id."_".$dataset_id."' style='overfow:auto;display:none;'>";
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
			elseif ( 6 == $formfield_type )
				$out .= $this->printFormFieldDropDown($formfield_id, $value, $dataset_id, "form_field_".$formfield_id."_".$dataset_id, false);
			elseif ( 7 == $formfield_type )
				$out .= $this->printFormFieldCheckboxList($formfield_id, $value, 0, "form_field_".$formfield_id."_".$dataset_id, false);
			elseif ( 8 == $formfield_type )
				$out .= $this->printFormFieldRadioList($formfield_id, $value, 0, "form_field_".$formfield_id."_".$dataset_id, false);
	
			$out .= "\n\t\t\t<div style='text-align:center; margin-top: 1em;'><input type='button' value='".__('Save')."' class='button-secondary' onclick='ProjectManager.ajaxSaveDataField(".$dataset_id.",".$formfield_id.",".$formfield_type.");return false;' />&#160;<input type='button' value='".__('Cancel')."' class='button' onclick='tb_remove();' /></div>";
			$out .= "\n\t\t\t</form>";
			$out .= "\n\t\t</div>";
			$out .= "\n\t\t</div>";
		}
		return $out;
	}
	

	/**
	 * addProject() - add new project
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
	 * editProject() - edit project
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
	 * delProject() - delete project
	 *
	 * @param int  $project_id
	 * @return void
	 */
	function delProject( $project_id )
	{
		global $wpdb;
		
		foreach ( $this->getDatasets() AS $dataset )
			$this->delDataset( $dataset->id );
		
		$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$project_id}" );
		$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projects} WHERE `id` = {$project_id}" );
	}

	
	/**
	 * importDatasets() - import datasets from CSV file
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
			$new_file =  $this->getImagePath().'/'.basename($file['name']);
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
						if ( $name != '' ) {
							$this->addDataset($project_id, $name, array(), $meta);
							$i++;
						}
					}
					fclose($handle);
					
					return sprintf(__( '%d Datasets successfully imported', 'projectmanager' ), $i);
				} else {
					$this->error = true;
					$this->message = __('The file is not readable', 'projectmanager');
				}
			} else {
				$this->error = true;
				$this->message = sprintf( __('The uploaded file could not be moved to %s.' ), $this->getImagePath() );
			}
			@unlink($new_file); // remove file from server after import is done
		} else {
			$this->error = true;
			$this->message = __('The uploaded file seems to be empty', 'projectmanager');
		}
	}
	
	
	/**
	 * exportDatasets() - export datasets to CSV
	 *
	 * @param int $project_id
	 * @return file
	 */
	function exportDatasets( $project_id )
	{
		$this->project_id = $project_id;
		$filename = $this->getProjectTitle()."_".date("Y-m-d").".csv";
		/*
		* Generate Header
		*/
		$contents = "Name\tCategories";
		foreach ( $this->getFormFields() AS $form_field )
			$contents .= "\t".$form_field->label;
		
		foreach ( $this->getDatasets() AS $dataset ) {
			$contents .= "\n".$dataset->name."\t".$this->getSelectedCategoryTitles(maybe_unserialize($dataset->cat_ids));

			foreach ( $this->getDatasetMeta( $dataset->id ) AS $meta ) {
				$contents .= "\t".$meta->value;
			}
		}
		
		header('Content-Type: text/csv');
    		header('Content-Disposition: inline; filename="'.$filename.'"');
		echo $contents;
		exit();
	}
	
	
	/**
	 * addDataset() - add new dataset
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

		$num_datasets = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_dataset} WHERE `user_id` = {$current_user->ID}" );
		
		if ( current_user_can( 'projectmanager_admin') || $num_datasets == 0 ) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_dataset} (name, cat_ids, project_id, user_id) VALUES ('%s', '%s', '%d', '%d')", $name, maybe_serialize($cat_ids), $project_id, $current_user->ID ) );
			$dataset_id = $wpdb->insert_id;
				
			if ( $dataset_meta ) {
				foreach ( $dataset_meta AS $meta_id => $meta_value ) {
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
			
			// Check for unsbumitted form data, e.g. checkbox list
			if ($form_fields = $this->getFormFields()) {
				foreach ( $form_fields AS $form_field ) {
					if ( !array_key_exists($form_field->id, $dataset_meta) ) {
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", $dataset_id, $form_field->id ) );
					}
				}
			}
		
			if ( isset($_FILES['projectmanager_image']) && $_FILES['projectmanager_image']['name'] != ''  )
				$this->uploadImage($team_id, $_FILES['projectmanager_image']);
				
			if ( $this->error ) $this->printErrorMessage();
			
			return __( 'New dataset added to the database.', 'projectmanager' ).' '.$tail;
		} else {
			$this->error = true;
			$this->message = __( 'An Entry of your user ID has been detected', 'projectmanager' );
			$this->printErrorMessage();
			return false;
		}
	}
		
		
	/**
	 * editDataset() - edit dataset
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

		if ( $user_id == $current_user->ID || current_user_can( 'projectmanager_admin') ) {
			$tail = '';
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `name` = '%s', `cat_ids` = '%s' WHERE `id` = '%d'", $name, maybe_serialize($cat_ids), $dataset_id ) );
			
			// Change Dataset owner if supplied
			if ( $owner )
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `user_id` = '%d' WHERE `id` = '%d'", $owner, $dataset_id ) );
			
			
			if ( $dataset_meta ) {
				foreach ( $dataset_meta AS $meta_id => $meta_value ) {
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
			if ($form_fields = $this->getFormFields()) {
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
			
			if ( $this->error ) $this->printErrorMessage();
				
			return __('Dataset updated.', 'projectmanager');
		} else {
			$this->error = true;
			$this->message = __( "You don't have the permission to edit this dataset", "projectmanager" );
			$this->printErrorMessage();
			return false;
		}
	}
		
		
	/**
	 * delDataset() - delete dataset
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
	 * display Form Field options as dropdown
	 *
	 * @param int $form_id
	 * @param ing $selected
	 * @param boolean $echo default true
	 * @return string
	 */
	 function printFormFieldDropDown( $form_id, $selected, $dataset_id, $name, $echo = true )
	{
		$options = get_option('projectmanager');
		
		$out = '';
		if ( count($options['form_field_options'][$form_id]) > 1 ) {
			$out .= "<select size='1' name='".$name."' id='form_field_".$form_id."_".$dataset_id."'>";
			foreach ( $options['form_field_options'][$form_id] AS $option_name ) {
				if ( $option_name == $selected )
					$out .= "<option value='".$option_name."' selected='selected'>".$option_name."</option>";
				else
					$out .= "<option value='".$option_name."'>".$option_name."</option>"; 
			}
			$out .= "</select>";
		}
		
		if ( $echo )
			echo $out;
		else
			return $out;
	}
	
	
	/**
	 * display Form Field options as checkbox list
	 *
	 * @param int $form_id
	 * @param array $selected
	 * @param boolean $echo default true
	 * @return string
	 */
	function printFormFieldCheckboxList( $form_id, $selected=array(), $dataset_id, $name, $echo = true )
	{
		$options = get_option('projectmanager');
		
		$selected = explode(',', $selected);
		$out = '';
		if ( count($options['form_field_options'][$form_id]) > 1 ) {
			$out .= "<ul class='checkboxlist'>";
			foreach ( $options['form_field_options'][$form_id] AS $id => $option_name ) {
				if ( count($selected) > 0 && in_array($option_name, $selected) )
					$out .= "<li><input type='checkbox' name='".$name."' checked='checked' value='".$option_name."' id='checkbox_".$form_id."_".$id."'><label for='checkbox_".$form_id."_".$id."'> ".$option_name."</label></li>";
				else
					$out .= "<li><input type='checkbox' name='".$name."' value='".$option_name."' id='checkbox_".$form_id."_".$id."'><label for='checkbox_".$form_id."_".$id."'> ".$option_name."</label></li>";
			}
			$out .= "</ul>";
		}
		
		if ( $echo )
			echo $out;
		else
			return $out;
	}
	
	/**
	* display Form Field options as radio list
	*
	* @param int $form_id
	* @param int $selected
	* @param boolean $echo default true
	* @return string
	*/
	function printFormFieldRadioList( $form_id, $selected, $dataset_id, $name, $echo = true )
	{
		$options = get_option('projectmanager');
		
		$out = '';
		if ( count($options['form_field_options'][$form_id]) > 1 ) {
			$out .= "<ul class='radiolist'>";
			foreach ( $options['form_field_options'][$form_id] AS $id => $option_name ) {
				if ( $option_name == $selected )
					$out .= "<li><input type='radio' name='".$name."' value='".$option_name."' checked='checked'  id='radio_".$form_id."_".$id."'><label for='radio_".$form_id."_".$id."'> ".$option_name."</label></li>";
				else
					$out .= "<li><input type='radio' name='".$name."' value='".$option_name."' id='radio_".$form_id."_".$id."'><label for='radio_".$form_id."_".$id."'> ".$option_name."</label></li>";
			}
			$out .= "</ul>";
		}
		
		if ( $echo )
			echo $out;
		else
			return $out;
	}

	
	/**
	 * delImage() - delete image along with thumbnails from server
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
	 * hasDetails() - check if datasets have details
	 * 
	 * @param int $project_id
	 * @return boolean
	 */
	function hasDetails()
	{
		global $wpdb;
		$num_form_fields = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$this->project_id} AND `show_on_startpage` = 0" );
			
		if ( $num_form_fields > 0 )
			return true;
		
		return false;
	}
	
	
	/**
	 * uploadImage() - set image path in database and upload image to server
	 *
	 * @param int  $dataset_id
	 * @param array $file
	 * @param boolean $overwrite_image
	 * @return void | string
	 */
	function uploadImage( $dataset_id, $file, $overwrite = false )
	{
		global $wpdb;
		
		$this->error = false;
		if ( $this->ImageTypeIsSupported($file['name']) ) {
			if ( $file['size'] > 0 ) {
				$options = get_option('projectmanager');
				$new_file =  $this->getImagePath().'/'.basename($file['name']);
				if ( file_exists($new_file) && !$overwrite ) {
					$this->error = true;
					$this->message = __('File exists and is not uploaded. Set the overwrite option if you want to replace it.','projectmanager');
				} else {
					if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
						if ( $dataset = $this->getDataset($dataset_id) )
							if ( $dataset->image != '' ) $this->delImage($dataset->image);

						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", basename($file['name']), $dataset_id ) );
			
						// Resize original file and create thumbnails
						$dims = array( 'width' => $options[$this->project_id]['medium_size']['width'], 'height' => $options[$this->project_id]['medium_size']['height'] );
						$this->createThumbnail( $new_file, $dims, $new_file );

						$dims = array( 'width' => $thumb_width = $options[$this->project_id]['thumb_size']['width'], 'height' => $options[$this->project_id]['thumb_size']['height'] );
						$this->createThumbnail( $new_file, $dims, $this->getImagePath().'/thumb.'.basename($file['name']) );

						$dims = array( 'width' => 80, 'height' => 50 );
						$this->createThumbnail( $new_file, $dims, $this->getImagePath().'/tiny.'.basename($file['name']) );
					} else {
						$this->error = true;
						$this->message = sprintf( __('The uploaded file could not be moved to %s.' ), $this->getImagePath() );
					}
				}
			}
		} else {
			$this->error = true;
			$this->message = __('The file type is not supported.','projectmanager');
		}
	}
	
	
	/**
	 * create Thumbnail of Image
	 *
	 * @param string $image
	 * @param array $dims
	 * @param string $new_image
	 */
	function createThumbnail( $image, $dims, $new_image )
	{
		$thumb = new Thumbnail($image);
		$thumb->resize( $dims['width'], $dims['heigth'] );
		$thumb->save($new_image);
	}
	
	
	/**
	 * setFormFields() - save Form Fields
	 *
	 * @param int $project_id
	 * @param array $form_name
	 * @param array $form_type
	 * @param array $form_order
	 * @param array $form_order_by
	 * @param array $new_form_name
	 * @param array $new_form_type
	 * @param array $new_form_order
	 * @param array $new_form_order_by
	 *
	 * @return string
	 */
	function setFormFields( $project_id, $form_name, $form_type, $form_show_on_startpage, $form_order, $form_order_by, $new_form_name, $new_form_type, $new_form_show_on_startpage, $new_form_order, $new_form_order_by )
	{
		global $wpdb;
		
		$options = get_option('projectmanager');
		if ( null != $form_name ) {
			foreach ( $wpdb->get_results( "SELECT `id`, `project_id` FROM {$wpdb->projectmanager_projectmeta}" ) AS $form_field) {
				if ( !array_key_exists( $form_field->id, $form_name ) ) {
					unset($options['form_field_options'][$form_field->id]);
					
					$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `id` = {$form_field->id} AND `project_id` = {$project_id}"  );
					if ( $project_id == $form_field->project_id )
						$wpdb->query( "DELETE FROM {$wpdb->projectmanager_datasetmeta} wHERE `form_id` = {$form_field->id}" );
				}
			}
				
			foreach ( $form_name AS $form_id => $form_label ) {
				$type = $form_type[$form_id];
				$order = $form_order[$form_id];
				$order_by = isset($form_order_by[$form_id]) ? 1 : 0;
				$show_on_startpage = isset($form_show_on_startpage[$form_id]) ? 1 : 0;
					
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projectmeta} SET `label` = '%s', `type` = '%d', `show_on_startpage` = '%d', `order` = '%d', `order_by` = '%d' WHERE `id` = '%d' LIMIT 1 ;", $form_label, $type, $show_on_startpage, $order, $order_by, $form_id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `form_id` = '%d' WHERE `form_id` = '%d'", $form_id, $form_id ) );
			}
		}
			
		if ( null != $new_form_name ) {
			foreach ($new_form_name AS $tmp_form_id => $form_label) {
				$type = $new_form_type[$tmp_form_id];
				$order_by = isset($new_form_order_by[$tmp_form_id]) ? 1 : 0;
				$show_on_startpage = (isset($new_form_show_on_startpage[$tmp_form_id])) ? 1 : 0;
					
				$max_order_sql = "SELECT MAX(`order`) AS `order` FROM {$wpdb->projectmanager_projectmeta};";
				if ($new_form_order[$tmp_form_id] != '') {
					$order = $new_form_order[$tmp_form_id];
				} else {
					$max_order_sql = $wpdb->get_results($max_order_sql, ARRAY_A);
					$order = $max_order_sql[0]['order'] +1;
				}
				
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_projectmeta} (`label`, `type`, `show_on_startpage`, `order`, `order_by`, `project_id`) VALUES ( '%s', '%d', '%d', '%d', '%d', '%d');", $form_label, $type, $show_on_startpage, $order, $order_by, $project_id ) );
				$form_id = mysql_insert_id();
					
				// Redirect form field options to correct $form_id if present
				if ( isset($options['form_field_options'][$tmp_form_id]) ) {
					$options['form_field_options'][$form_id] = $options['form_field_options'][$tmp_form_id];
					unset($options['form_field_options'][$tmp_form_id]);
				}
				
				/*
				* Populate default values for every dataset
				*/
				if ( $datasets = $this->getDatasets() ) {
					foreach ( $datasets AS $dataset ) {
						$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '' );", $form_id, $dataset->id ) );
					}
				}
			}
		}
		
		update_option('projectmanager', $options);
		return __('Form Fields updated', 'projectmanager');
	}
		 
		
	/**
	 * insert() - replace shortcodes with respective HTML in posts or pages
	 *
	 * @param string $content
	 * @return string
	 */
	function insert( $content )
	{
		if ( stristr( $content, '[prjctmngr_search' )) {
			$search = "@\[prjctmngr_search_form\s*=\s*(\w+),(|right|center|left|),(|extend|compact|)\]@i";
	
			if ( preg_match_all($search, $content , $matches) ) {
				if (is_array($matches)) {
					foreach($matches[1] AS $key => $v0) {
						$project_id = $v0;
						$search = $matches[0][$key];
						$replace = $this->getSearchForm( $project_id, $matches[2][$key], $matches[3][$key] );
			
						$content = str_replace($search, $replace, $content);
					}
				}
			}
		}
		
		if ( stristr( $content, '[prjctmngr_category' )) {
			$search = "@\[prjctmngr_category_selection\s*=\s*(\w+),(|dropdown|list|),(|left|center|right|)\]@i";
		
			if ( preg_match_all($search, $content , $matches) ) {
				if (is_array($matches)) {
					foreach($matches[1] AS $key => $v0) {
						$project_id = $v0;
						$search = $matches[0][$key];
						$replace = $this->getCategorySelection( $project_id, $matches[2][$key], $matches[3][$key] );
				
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
						$replace = apply_filters('projectmanager_dataset_list', $replace, $project_id, $matches[3][$key], $matches[2][$key]);
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
						$replace =  apply_filters('projectmanager_dataset_gallery', $replace, $project_id, $matches[2][$key], $matches[3][$key]); 
			
						$content = str_replace($search, $replace, $content);
					}
				}
			}
		}

		$content = str_replace('<p></p>', '', $content);
		return $content;
	}
	

	/**
	 * getSearchForm() - create search formular
	 *
	 * @param string $style
	 */
	function getSearchForm( $project_id, $pos = '', $display )
	{
		$this->project_id = $project_id;
		$search_option = $this->getSearchOption();
		
		$class = ( $pos != '' ) ? 'align'.$pos : '';

		if ( !isset($_GET['show'])) {
			$out = "</p>\n\n<div class='".$class." projectmanager'>\n<form class='search-form' action='' method='post'>";
			$out .= "\n\t<input type='text' class='search-input' name='search_string' value='".$this->getSearchString()."' />";
			if ( $display == 'extend' ) {
				if ( $form_fields = $this->getFormFields()) {
					$out .= "\n\t<select size='1' name='search_option'>";
					$selected[0] = ( 0 == $search_option ) ? " selected='selected'" : "";
					$out .= "\n\t\t<option value='0'".$selected[0].">".__( 'Name', 'projectmanager' )."</option>";
					foreach ( $form_fields AS $form_field ) {
						$selected = ( $search_option == $form_field->id ) ? " selected='selected'" : "";
						$out .= "\n\t\t<option value='".$form_field->id."'".$selected.">".$form_field->label."</option>";
					}
					$selected[1] = ( -1 == $search_option ) ? " selected='selected'" : "";
					$out .= "\n\t<option value='-1'".$selected[1].">".__( 'Categories', 'projectmanager' )."</option>";
					$out .= "\n\t</select>";
				}
			} else
				$out .= "\n\t<input type='hidden' name='form_field' value='0' />";
				
			$out .= "\n\t<input type='submit' value='".__('Search', 'projectmanager')." &raquo;' class='button' />";
			$out .= "\n</form>\n</div>\n\n<p>";
		}

		return $out;
	}
	function printSearchForm( $project_id, $pos = '' )
	{
		echo $this->getSearchForm( $project_id, $pos );
	}
		
	
	/**
	 * getCategorySelection() - get group selection
	 *
	 * @param int $project_id
	 * @param string $type 'dropdown' | 'list'
	 */
	function getCategorySelection( $project_id, $type, $pos )
	{
		if ( 'dropdown' == $type )
			return $this->getCategoryDropdown($project_id,$pos);
		elseif ( 'list' == $type )
			return $this->getCategoryList($project_id,$pos);
	}
	
	
	/**
	 * getCategoryDropdown() - get group dropdown
	 *
	 * @param int $project_id
	 * @return string
	 */
	function getCategoryDropdown( $project_id, $pos = '' )
	{
		global $wpdb, $wp_query;
		
		$this->project_id = $project_id;
		$options = get_option( 'projectmanager' );
		if ( is_admin() ) {
			$hidden = "\n<input type='hidden' name='page' value='".$_GET['page']."' />\n<input type='hidden' name='project_id' value='".$this->project_id."' />";
			$action = 'edit.php';
		} else {
			$page_obj = $wp_query->get_queried_object();
			$page_ID = $page_obj->ID;
		
			$hidden = "\n<input type='hidden' name='page_id' value='".$page_ID."' />";
			$action = get_permalink($page_ID);
		}

		$class = ($pos != '') ? 'align'.$pos : '';
		
		$out = "</p>";
		if ( !isset($_GET['show']) && -1 != $options[$this->project_id]['category'] ) {
			$out .= "\n\n<div class='".$class." projectmanager'>\n<form action='".$action."' method='get'>\n";
			$out .= wp_dropdown_categories(array('echo' => 0, 'hide_empty' => 0, 'name' => 'cat_id', 'orderby' => 'name', 'selected' => $this->getCatID(), 'hierarchical' => true, 'child_of' => $options[$this->project_id]['category'], 'show_option_all' => __('View all categories')));
			$out .= $hidden;
			$out .= "\n<input type='submit' value='".__( 'Filter', 'projectmanager' )."' class='button' />";
			$out .= "\n</form>\n</div>\n\n";
		}
		$out .= "<p>";

		return $out;
	}
	function printCategoryDropdown( $project_id, $pos = '' )
	{
		echo $this->getCategoryDropdown( $project_id, $pos );
	}
	
	
	/**
	 * getCategoryList() - get group list
	 *
	 * @param int $proeject_id
	 * @return string
	 */
	function getCategoryList( $project_id, $pos )
	{
		global $wpdb;
		$this->project_id = $project_id;
		$options = get_option( 'projectmanager' );
		
		$out = '</p>';
		if ( !isset($_GET['show'])) {
			$out = "\n<div class='align".$pos."'>\n\t<ul>";
			$out .= wp_list_categories(array('echo' => 0, 'title_li' => __('Categories', 'projectmanager'), 'child_of' => $options[$this->project_id]['category']));
			$out .= "\n\t</ul>\n</div>";
		}
		$out .= '<p>';
		
		return $out;
	}
	
	
	/**
	 * getDatasetList() - get dataset list
	 *
	 * Function to display the datasets of a given project in a page or post as list.
	 * The function is called via the filter `projectmanager_dataset_list` and can be modified or overwritten by
	 *
	 * remove_filter('projectmanager_dataset_list', array(&$projectmanager, 'getDatasetList'));
	 * add_filter('projectmanager_dataset_list', 'my_function', 10, 4);
	 *
	 * function my_function( $out = '', $project_id, $output = 'table', $cat_id = false ) {
	 *	// Do some stuff
	 * }
	 *
	 * @param int $project_id
	 * @param string $output
	 * @param ing $cat_id
	 * @return string
	 */
	function getDatasetList( $out, $project_id, $output = 'table', $cat_id = false )
	{
		$this->initialize($project_id);
		if ( $cat_id ) $this->setCatID($cat_id);
	
		if ( isset( $_GET['show'] ) ) {
			$out = apply_filters( 'projectmanager_single_view', $out, $this->project_id, $_GET['show'] );
		} else {
			if ( $this->isSearch() )
				$datasets = $this->getSearchResults();
			else
				$datasets = $this->getDatasets( true  );
			
			$out = "</p>";
			if ( $datasets ) {
				$num_total_datasets = $this->getNumDatasets($this->project_id, true);
				$out .= "\n<div id='projectmanager_datasets_header'>";
				$out .= ( !$this->isSearch() ) ? "\n\t<p>".sprintf(__('%d of %d Datasets', 'projectmanager'), $this->getNumDatasets($this->project_id), $num_total_datasets )."</p>" : '';
				if ( $this->isSearch() )
					$out .= "<h3>".sprintf(__('Search: %d of %d', 'projectmanager'),  $this->getNumDatasets($this->project_id), $num_total_datasets)."</h3>";
				elseif ( $this->isCategory() )
					$out .= "<h3>".$this->getCatTitle($this->getCatID())."</h3>";
				$out .= "\n</div>";
				
				if ( 'table' == $output ) {
					$out .= "\n<table class='projectmanager'>\n<tr>\n";
					$out .= "\t<th scope='col'>".__( 'Name', 'projectmanager' )."</th>";
					$out .= $this->getTableHeader();
					$out .= "\n</tr>";
				} else {
					$out .= "\n<".$output." class='projectmanager'>";
				}
				
				foreach ( $datasets AS $dataset ) {
					$url = get_permalink();
					$url = add_query_arg('show', $dataset->id, $url);
					$url = ($this->isCategory()) ? add_query_arg('cat_id', $this->getCatID(), $url) : $url;
					$name = ($this->hasDetails()) ? '<a href="'.$url.'">'.$dataset->name.'</a>' : $dataset->name;
					
					$class = ("alternate" == $class) ? '' : "alternate";
					
					if ( 'table' == $output )
						$out .= "\n<tr class='".$class."'><td>".$name."</td>".$this->getDatasetMetaData( $dataset, 'td' )."</tr>";
					else
						$out .= "\n\t<li>".$name."<ul>".$this->getDatasetMetaData( $dataset, 'li' )."</ul></li>";
				}
				
				$out .= "\n</$output>\n";
				
				if ( !$this->isSearch() ) $out .= "<p class='page-numbers'>".$this->getPageLinks()."</p>";
			} else {
				$out .= "<p class='error'>".__( 'Nothing found', 'projectmanager')."</p>";
			}
			$out .= "<p>";
		}
		
		return $out;
	}
	
	
	/**
	 * getGallery() - get dataset as gallery
	 *
	 * Function to display the datasets of a given project in a page or post as gallery.
	 * The function is called via the filter `projectmanager_dataset_gallery` and can be modified or overwritten by
	 *
	 * remove_filter('projectmanager_dataset_gallery', array(&$projectmanager, 'getDatasetList'));
	 * add_filter('projectmanager_dataset_gallery', 'my_function', 10, 4);
	 *
	 * function my_function( $out = '', $project_id, $num_cols, $cat_id = false ) {
	 *	// Do some stuff
	 * }
	 *
	 * @param int $project_id
	 * @param int $num_cols
	 * @param int $cat_id
	 * @return string
	 */
	function getGallery( $out, $project_id, $num_cols, $cat_id = false )
	{
		$options = get_option( 'projectmanager' );
		$this->initialize($project_id);

		if ( $cat_id ) $this->setCatID($cat_id);

		if ( isset( $_GET['show'] ) ) {
			$out = apply_filters( 'projectmanager_single_view', $out, $this->project_id, $_GET['show'] );
		} else {
			if ( $this->isSearch() )
				$datasets = $this->getSearchResults();
			else
				$datasets = $this->getDatasets( true );
			
			$out = "</p>";
			if ( $datasets ) {
				$out .= "\n\n<div class='dataset_gallery'>\n<div class='gallery-row'>";
				
				foreach ( $datasets AS $dataset ) {
					$i++;
					$url = get_permalink();
					$url = add_query_arg('show', $dataset->id, $url);
					$url = ($this->isCategory()) ? add_query_arg('cat_id', $this->getCatID(), $url) : $url;
								
					$before_name = '<a href="'.$url.'">';
					$after_name = '</a>';
					
					$width = floor(100/$num_cols);
					$out .= "\n\t<div class='gallery-item' style='width: ".$width."%;'>";
					if ($options[$this->project_id]['show_image'] == 1 && '' != $dataset->image)
						$out .= "\n\t\t".$before_name.'<img src="'.$this->getImageUrl('/thumb.'.$dataset->image).'" alt="'.$dataset->name.'" title="'.$dataset->name.'" />'.$after_name;
					
					$out .= "\n\t\t<p class='caption'>".$before_name.$dataset->name.$after_name."</p>";
					$out .= "\n\t</div>";
				
					if ( ( ( 0 == $i % $num_cols)) && ( $i < count($datasets) ) )
						$out .= "\n</div>\n<div class='gallery-row'>";
				}
				
				$out .= "\n</div>\n</div><br style='clear: both;' />\n\n";
		
				if ( !$this->isSearch() ) $out .= "<p class='page-numbers'>".$this->getPageLinks()."</p>";
			} else {
				$out .= "<p class='error'>".__( 'Nothing found', 'projectmanager')."</p>";
			}
			$out .= "<p>";
		}
		
		return $out;
	}
	
	
	/**
	 * getSingleView () - get details on dataset
	 *
 	 * Function to display the single view of a dataset
	 * The function is called via the filter `projectmanager_dataset_list` and can be modified or overwritten by
	 *
	 * remove_filter('projectmanager_single_view', array(&$projectmanager, 'getSingleView'));
	 * add_filter('projectmanager_single_view', 'my_function', 10, 4);
	 *
	 * function my_function( $out = '', $project_id, $output = 'table', $cat_id = false ) {
	 *	// Do some stuff
	 * }
	 *
	 * @param int $dataset_id
	 * @return string
	 */
	function getSingleView( $out, $project_id, $dataset_id )
	{
		$options = get_option( 'projectmanager' );
		$url = get_permalink();
		$url = add_query_arg('paged', $this->getDatasetPage($dataset_id), $url);
		$url = ($this->isCategory()) ? add_query_arg('cat_id', $this->getCatID(), $url) : $url;
					
		$out = "</p>";
		$out .= "\n<p><a href='".$url."'>".__('Back to list', 'projectmanager')."</a></p>\n";
		
		if ( $dataset = $this->getDataset( $dataset_id ) ) {
			$out .= "<fieldset class='dataset'><legend>".__( 'Details of', 'projectmanager' )." ".$dataset->name."</legend>\n";
			if ($options[$this->project_id]['show_image'] == 1 && '' != $dataset->image)
				$out .= "\t<div class='image'><img src='".$this->getImageUrl($dataset->image)."' title='".$dataset->name."' alt='".$dataset->name."' /></div>\n";
				
			$out .= "<dl>".$this->getDatasetMetaData( $dataset, 'dl', true )."\n</dl>\n";
			$out .= "</fieldset>\n";
		}
		
		$out .= "<p>";
		
		return $out;
	}
	
	
	/**
	 * getSearchResults() - gets search results
	 *
	 * @param none
	 * @return array
	 */
	function getSearchResults( )
	{
		global $wpdb;
		
		$search = $this->getSearchString();
		$option = $this->getSearchOption();
			
		if ( 0 == $option ) {
			$datasets = $wpdb->get_results( "SELECT `id`, `name`, `cat_ids` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$this->project_id} AND `name` REGEXP CONVERT( _utf8 '".$search."' USING latin1 ) ORDER BY `name` ASC" );
		} elseif ( -1 == $option ) {
			$categories = explode(",", $search);
			$cat_ids = array();
			foreach ( $categories AS $category ) {
				$c = $wpdb->get_results( $wpdb->prepare ( "SELECT `term_id` FROM $wpdb->terms WHERE `name` = '%s'", trim($category) ) );
				$cat_ids[] = $c[0]->term_id;;
			}
			$sql = "SELECT `id`, `name`, `image`, `cat_ids` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$this->project_id}";
				
			foreach ( $cat_ids AS $cat_id ) {
				$this->setCatID($cat_id);
				$sql .= $this->getCategorySearchString();
			}
			$this->cat_id = null;
			
			$sql .=  " ORDER BY `name` ASC;";

			$datasets = $wpdb->get_results($sql);
		} else {
			$sql = "SELECT  t1.dataset_id AS id,
					t2.name,
					t2.cat_ids
				FROM {$wpdb->projectmanager_datasetmeta} AS t1, {$wpdb->projectmanager_dataset} AS t2
				WHERE t1.value REGEXP CONVERT( _utf8 '".$search."' USING latin1 )
					AND t1.form_id = '".$option."'
					AND t1.dataset_id = t2.id
				ORDER BY t1.dataset_id ASC";
			$datasets = $wpdb->get_results( $sql );
		}
		
		$this->datasets = $datasets;
		return $datasets;
	}
	
	
	/**
	 * widget() - display widget
	 *
	 * @param array $args
	 */
	function widget( $args )
	{
		global $wpdb;
		
		$options = get_option( 'projectmanager_widget' );
		$widget_id = $args['widget_id'];
		$project_id = $options[$widget_id]['project_id'];
		$this->initialize($project_id);
		
		$defaults = array(
			'before_widget' => '<li id="projectmanager" class="widget '.get_class($this).'_'.__FUNCTION__.'">',
			'after_widget' => '</li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
			'widget_title' => $options[$widget_id]['title'],
			'limit' => $options[$widget_id]['limit'],
			'slideshow' => ( 1 == $options[$widget_id]['slideshow']['show'] ) ? true : false,
		);
		$args = array_merge( $defaults, $args );
		extract( $args );
		
		
		$limit = ( 0 != $limit ) ? "LIMIT 0,".$limit : '';
		$datasets = $wpdb->get_results( "SELECT `id`, `name`, `image` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$project_id} ORDER BY `id` DESC ".$limit." " ); 

		echo $before_widget . $before_title . $widget_title . $after_title;
		if ( $slideshow )
			echo '<div id="projectmanager_slideshow_'.$project_id.'" class="projectmanager_slideshow">';
		else
			echo "<ul class='projectmanager_widget'>";
				
		if ( $datasets ) {
			$url = get_permalink($options[$widget_id]['page_id']);
			foreach ( $datasets AS $dataset ) {
				$url = add_query_arg('show', $dataset->id, $url);
				$name = ($this->hasDetails()) ? '<a href="'.$url.'"><img src="'.$this->getImageUrl($dataset->image).'" alt="'.$dataset->name.'" title="'.$dataset->name.'" /></a>' : '<img src="'.$this->getImageUrl($dataset->image).'" alt="'.$dataset->name.'" title="'.$dataset->name.'" />';
				
				if ( $slideshow ) {
					if ( $dataset->image != '' )
						echo $name;
				} else
					echo "<li>".$name."</li>";
			}
		}
		if ( $slideshow )
			echo "</div>";
		else
			echo "</ul>";
		echo $after_widget;
	}
		 
		 
	/**
	 * widgetControl() - Widget Control
	 *
	 * @param none
	 */
	function widgetControl( $args )
	{
		extract( $args );
		$options = get_option( 'projectmanager_widget' );
		
		if ($_POST['projectmanager-submit']) {
			$options[$widget_id]['project_id'] = $project_id;
			$options[$widget_id]['title'] = $_POST['widget_title'][$project_id];
			$options[$widget_id]['limit'] = $_POST['limit'][$project_id];
			$options[$widget_id]['page_id'] = $_POST['page_id'][$project_id];
			$options[$widget_id]['slideshow'] = array('show' => $_POST['projectmanager_slideshow'][$project_id], 'width' => $_POST['projectmanager_slideshow_width'][$project_id], 'height' => $_POST['projectmanager_slideshow_height'][$project_id], 'time' => $_POST['projectmanager_slideshow_time'][$project_id], 'fade' => $_POST['projectmanager_slideshow_fade'][$project_id], 'random' => $_POST['projectmanager_slideshow_order'][$project_id]);
				
			update_option( 'projectmanager_widget', $options );
		}
		
		echo '<div class="projectmanager_widget_control">';
		echo '<p><label for="widget_title">'.__('Title', 'projectmanager').'</label><input class="widefat" type="text" name="widget_title['.$project_id.']" id="widget_title" value="'.$options[$widget_id]['title'].'" /></p>';
		echo '<p><label for="limit">'.__('Display', 'projectmanager').'</label>&#160;<select style="margin-top: 0;" size="1" name="limit['.$project_id.']" id="limit">';
		$selected['show_all'] = ( $options[$widget_id]['limit'] == 0 ) ? " selected='selected'" : '';
		echo '<option value="0"'.$selected['show_all'].'>'.__('All','projectmanager').'</option>';
		for ( $i = 1; $i <= 10; $i++ ) {
		        $selected = ( $options[$widget_id]['limit'] == $i ) ? " selected='selected'" : '';
			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		echo '</select></p>';
		echo '<p><label for="page_id['.$project_id.']">'.__('Page','projectmanager').'</label>&#160;'.wp_dropdown_pages(array('name' => 'page_id['.$project_id.']', 'selected' => $options[$widget_id]['page_id'], 'echo' => 0)).'</p>';
		echo '<fieldset class="slideshow_control"><legend>'.__('Slideshow','projectmanager').'</legend>';
		$checked = ($options[$widget_id]['slideshow']['show'] == 1) ? ' checked="checked"' : '';
		echo '<p><input type="checkbox" name="projectmanager_slideshow['.$project_id.']" id="projectmanager_slideshow" value="1"'.$checked.' style="margin-left: 0.5em;" />&#160;<label for="projectmanager_slideshow" class="right">'.__( 'Use Slideshow', 'projectmanager' ).'</label></p>';
		echo '<p><label for="projectmanager_slideshow_width">'.__( 'Width', 'projectmanager' ).'</label><input type="text" size="3" name="projectmanager_slideshow_width['.$project_id.']" id="projectmanager_slideshow_width" value="'.$options[$widget_id]['slideshow']['width'].'" class="widefat" style="display: inline; clear: none; width: auto;" /> px</p>';
		echo '<p><label for="projectmanager_slideshow_height">'.__( 'Height', 'projectmanager' ).'</label><input type="text" size="3" name="projectmanager_slideshow_height['.$project_id.']" id="projectmanager_slideshow_height" value="'.$options[$widget_id]['slideshow']['height'].'" class="widefat" style="display: inline; clear: none; width: auto;" /> px</p>';
		echo '<p><label for="projectmanager_slideshow_time">'.__( 'Time', 'projectmanager' ).'</label><input type="text" name="projectmanager_slideshow_time['.$project_id.']" id="projectmanager_slideshow_time" size="1" value="'.$options[$widget_id]['slideshow']['time'].'" class="widefat" style="display: inline; clear: none; width: auto;" /> '.__( 'seconds','projectmanager').'</p>';
		echo '<p><label for="projectmanager_slideshow_fade">'.__( 'Fade Effect', 'projectmanager' ).'</label>'.$this->getSlideshowFadeEffects($options[$widget_id]['slideshow']['fade'], $project_id).'</p>';
		echo '<p><label for="projectmanager_slideshow_order">'.__('Order','projectmanager').'</label>'.$this->getSlideshowOrder($options[$widget_id]['slideshow']['random'], $project_id).'</p>';
		echo '</fieldset>';
	
		echo '<input type="hidden" name="projectmanager-submit" value="1" />';
		echo '</div>';
	}
	
	
	/**
	* fadeEffects() - dropdown list
	*
	* @param string $selected
	* @param int $project_id
	* @return string
	*/
	function getSlideshowFadeEffects( $selected, $project_id )
	{
		$effects = array(__('Fade','projectmanager') => 'fade', __('Zoom Fade','projectmanager') => 'zoomFade', __('Scroll Up','projectmanager') => 'scrollUp', __('Scroll Left','projectmanager') => 'scrollLeft', __('Scroll Right','projectmanager') => 'scrollRight', __('Scroll Down','projectmanager') => 'scrollDown', __( 'Zoom','projectmanager') => 'zoom', __('Grow X','projectmanager') => 'growX', __('Grow Y','projectmanager') => 'growY', __('Zoom BR','projectmanager') => 'zoomBR', __('Zoom TL','projectmanager') => 'zoomTL', __('Random','projectmanager') => 'random');
		
		$out = '<select size="1" name="projectmanager_slideshow_fade['.$project_id.']" id="projectmanager_slideshow_fade">';
		foreach ( $effects AS $name => $effect ) {
			$checked =  ( $selected == $effect ) ? " selected='selected'" : '';
			$out .= '<option value="'.$effect.'"'.$checked.'>'.$name.'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
	
	/**
	 * order() - dropdown list of Order possibilites
	 *
	 * @param string $selected
	 * @param int $project_id
	 * @return string
	 */
	function getSlideshowOrder( $selected, $project_id )
	{
		$order = array(__('Ordered','projectmanager') => 'false', __('Random','projectmanager') => 'true');
		$out = '<select size="1" name="projectmanager_slideshow_order['.$project_id.']" id="projectmanager_slideshow_order">';
		foreach ( $order AS $name => $value ) {
			$checked =  ( $selected == $value ) ? " selected='selected'" : '';
			$out .= '<option value="'.$value.'"'.$checked.'>'.$name.'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
	
	/**
	 * addTinyMCEButton() - add TinyMCE Button
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
	 * printBreadcrumb() - print breadcrumb navigation
	 *
	 * @param int $project_id
	 * @param string $page_title
	 * @param boolean $start
	 */
	function printBreadcrumb( $page_title, $start=false )
	{
		$options = get_option('projectmanager');
		if ( 1 != $options[$this->project_id]['navi_link'] ) {
			echo '<p class="projectmanager_breadcrumb">';
			if ( !$this->single )
				echo '<a href="edit.php?page=projectmanager/page/index.php">'.__( 'Projectmanager', 'projectmanager' ).'</a> &raquo; ';
			
			if ( $page_title != $this->getProjectTitle() )
				echo '<a href="edit.php?page=projectmanager/page/show-project.php&amp;project_id='.$this->project_id.'">'.$this->getProjectTitle().'</a> &raquo; ';
			
			if ( !$start || ($start && !$this->single) ) echo $page_title;
			
			echo '</p>';
		}
	}
	
	
	/**
	 * addHeaderCode() - Add Code to Wordpress Header
	 *
	 * @param none
	 */
	function addHeaderCode($show_all=false)
	{
		$options = get_option('projectmanager');
		$options['widget'] = get_option( 'projectmanager_widget' );
		
		echo "\n\n<!-- WP-ProjectManager START -->\n";
		echo "<link rel='stylesheet' href='".$this->plugin_url."/style.css' type='text/css' />\n";

		if ( !is_admin() ) {
			// Table styles
			echo "\n<style type='text/css'>";
			echo "\n\ttable.projectmanager th { background-color: ".$options['colors']['headers']." }";
			echo "\n\ttable.projectmanager tr { background-color: ".$options['colors']['rows'][1]." }";
			echo "\n\ttable.projectmanager tr.alternate { background-color: ".$options['colors']['rows'][0]." }";
			echo "\n\tfieldset.dataset { border-color: ".$options['colors']['headers']." }";
			echo "\n</style>";
			wp_register_script( 'jquery_slideshow', $this->plugin_url.'/js/jquery.aslideshow.js', array('jquery'), '0.5.3' );
			wp_print_scripts( 'jquery_slideshow' );
		
			foreach ( $options['widget'] AS $widget_id => $opts ) {
				if ($opts['slideshow']['show'] == 1) {
			?>
			<script type='text/javascript'>
			//<![CDATA[
				   jQuery(document).ready(function(){
				   jQuery('#projectmanager_slideshow_<?php echo $opts['project_id'] ?>').slideshow({
				   width: <?php echo $opts['slideshow']['width'] ?>,
				   height:<?php echo $opts['slideshow']['height']; ?>,
				   time: <?php echo $opts['slideshow']['time']*1000; ?>,
				   title:false,
				   panel:false,
				   loop:true,
				   play:true,
				   playframe: false,
				   effect: '<?php echo $opts['slideshow']['fade'] ?>',
				   random: <?php echo $opts['slideshow']['random'] ?>,
				   });
				   });
			   //]]>
			</script>
			<?php
			}}
		}
	
		if ( is_admin() AND ((isset( $_GET['page'] ) AND substr( $_GET['page'], 0, 14 ) == 'projectmanager') || $show_all )) {
			wp_register_script( 'projectmanager', $this->plugin_url.'/js/functions.js', array( 'colorpicker', 'sack' ), PROJECTMANAGER_VERSION );
			wp_register_script( 'projectmanager_formfields', $this->plugin_url.'/js/formfields.js', array( 'projectmanager', 'thickbox' ), PROJECTMANAGER_VERSION );
			wp_register_script ('projectmanager_ajax', $this->plugin_url.'/js/ajax.js', array( 'projectmanager' ), PROJECTMANAGER_VERSION );
		
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
				blogUrl: "<?php bloginfo( 'wpurl' ); ?>", pluginPath: "<?php echo $this->plugin_path; ?>", pluginUrl: "<?php echo $this->plugin_url; ?>", requestUrl: "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php", imgUrl: "<?php echo $this->plugin_url; ?>/images", Edit: "<?php _e("Edit"); ?>", Post: "<?php _e("Post"); ?>", Save: "<?php _e("Save"); ?>", Cancel: "<?php _e("Cancel"); ?>", pleaseWait: "<?php _e("Please wait..."); ?>", Revisions: "<?php _e("Page Revisions"); ?>", Time: "<?php _e("Insert time"); ?>", Options: "<?php _e("Options", "projectmanager") ?>", Delete: "<?php _e('Delete', 'projectmanager') ?>"
				   }
			//]]>
			<?php
			echo "</script>\n";
		}
		echo "<!-- WP-ProjectManager END -->\n\n";
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
				echo '<div id="message" class="updated fade"><p><strong>'.__( 'Settings saved', 'leaguemanager' ).'</strong></p></div>';
			}
			
			
			echo "\n<form action='' method='post' name='colors'>";
			wp_nonce_field( 'projetmanager_manage-global-league-options' );
			echo "\n<div class='wrap'>";
			echo "\n\t<h2>".__( 'Global Settings', 'projectmanager' )."</h2>";
			echo "\n\t<h3>".__( 'Color Scheme', 'projectmanager' )."</h3>";
			echo "\n\t<table class='form-table'>";
			echo "\n\t<tr valign='top'>";
			echo "\n\t\t<th scope='row'><label for='color_headers'>".__( 'Table Headers', 'projectmanager' )."</label></th><td><input type='text' name='color_headers' id='color_headers' value='".$options['colors']['headers']."' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms[\"colors\"].color_headers,\"pick_color_headers\"); return false;' name='pick_color_headers' id='pick_color_headers'>&#160;&#160;&#160;</a></td>";
			echo "\n\t</tr>";
			echo "\n\t<tr valign='top'>";
			echo "\n\t<th scope='row'><label for='color_rows'>".__( 'Table Rows', 'projectmanager' )."</label></th>";
			echo "\n\t\t<td>";
			echo "\n\t\t\t<p class='table_rows'><input type='text' name='color_rows_alt' id='color_rows_alt' value='".$options['colors']['rows'][0]."' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms[\"colors\"].color_rows_alt,\"pick_color_rows_alt\"); return false;' name='pick_color_rows_alt' id='pick_color_rows_alt'>&#160;&#160;&#160;</a></p>";
			echo "\n\t\t\t<p class='table_rows'><input type='text' name='color_rows' id='color_rows' value='".$options['colors']['rows'][1]."' size='10' /><a href='#' class='colorpicker' onClick='cp.select(document.forms[\"colors\"].color_rows,\"pick_color_rows\"); return false;' name='pick_color_rows' id='pick_color_rows'>&#160;&#160;&#160;</a></p>";
			echo "\n\t\t</td>";
			echo "\n\t</tr>";
			echo "\n\t</table>";
			echo "\n<input type='hidden' name='page_options' value='color_headers,color_rows,color_rows_alt' />";
			echo "\n<p class='submit'><input type='submit' name='updateProjectManager' value='".__( 'Save Preferences', 'projectmanager' )." &raquo;' class='button' /></p>";
			echo "\n</form>";
		
			echo "<script language='javascript'>
				syncColor(\"pick_color_headers\", \"color_headers\", document.getElementById(\"color_headers\").value);
				syncColor(\"pick_color_rows\", \"color_rows\", document.getElementById(\"color_rows\").value);
				syncColor(\"pick_color_rows_alt\", \"color_rows_alt\", document.getElementById(\"color_rows_alt\").value);
			</script>";
	
		//	echo "<p>".sprintf(__( "To add and manage projects, go to the <a href='%s'>Management Page</a>", 'projectmanager' ), get_option( 'siteurl' ).'/wp-admin/edit.php?page=projectmanager/page/index.php')."</p>";
	
		} elseif(!$include) {
			echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
		}
	}
	
	
	/**
	 * initWidget() - Initialize Widget
	 *
	 * @param none
	 */
	function activateWidget()
	{
		$options = get_option('projectmanager');
			
		if (!function_exists('register_sidebar_widget')) {
			return;
		}
			
		// Register Widgets
		foreach ( $this->getWidgetProjects() AS $project ) {
			$widget_ops = array('classname' => 'widget_projectmanager', 'description' => $project->title );
			wp_register_sidebar_widget( sanitize_title($project->title), $project->title, array(&$this, 'widget'), $widget_ops );
			wp_register_widget_control( sanitize_title($project->title), $project->title, array(&$this, 'widgetControl'), array('width' => 250, 'height' => 100), array( 'project_id' => $project->id, 'widget_id' => sanitize_title($project->title) ) );
		}
	}
		 
		 
	/**
	 * init() - Initialize Plugin
	 *
	 * @param none
	 */
	function activate()
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
						`order_by` tinyint( 1 ) NOT NULL default '0',
						`show_on_startpage` tinyint( 1 ) NOT NULL ,
						`project_id` int( 11 ) NOT NULL ,
						PRIMARY KEY ( `id` )) $charset_collate";
		maybe_create_table( $wpdb->projectmanager_projectmeta, $create_projectmeta_sql );
				
		$create_dataset_sql = "CREATE TABLE {$wpdb->projectmanager_dataset} (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`name` varchar( 150 ) NOT NULL default '' ,
						`image` varchar( 50 ) NOT NULL default '' ,
						`cat_ids` longtext NOT NULL ,
						`project_id` int( 11 ) NOT NULL ,
						`user_id` int( 11 ) NOT NULL default '1',
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
		$role->add_cap('projectmanager_admin');
		$role->add_cap('manage_projects');
		
		$role = get_role('editor');
		$role->add_cap('manage_projects');
		
		/*
		* Add widget options
		*/
		if ( function_exists('register_sidebar_widget') )
			add_option( 'projectmanager_widget', array(), 'ProjectManager Widget Options', 'yes' );
	}
	
	
	/**
	 * addAdminMenu() - adds admin menu
	 *
	 * @param none
	 */
	function addAdminMenu()
	{
		global $wpdb;
		
		if ( $projects = $this->getProjects() ) {
			$options = get_option( 'projectmanager' );
			foreach( $projects AS $project ) {
				if ( 1 == $options[$project->id]['navi_link'] ) {
					$page = 'admin.php?page=projectmanager/page/show-project.php&project_id='.$project->id;
					add_menu_page( $project->title, $project->title, 'manage_projects', $page, '', $this->plugin_url.'/images/menu.png' );
					add_submenu_page($page, __('Overview', 'projectmanager'), __('Overview','projectmanager'),'manage_projects', $page,'');
					add_submenu_page($page, __( 'Add Dataset', 'projectmanager' ), __( 'Add Dataset', 'projectmanager' ), 'manage_projects', 'admin.php?page=projectmanager/page/dataset.php&project_id='.$project->id);
					add_submenu_page($page, __( 'Form Fields', 'projectmanager' ), __( 'Form Fields', 'projectmanager' ), 'manage_projects', 'admin.php?page=projectmanager/page/formfields.php&project_id='.$project->id);
					add_submenu_page($page, __( 'Settings', 'projectmanager' ), __( 'Settings', 'projectmanager' ), 'manage_projects', 'admin.php?page=projectmanager/page/settings.php&project_id='.$project->id);
					add_submenu_page($page, __('Categories'), __('Categories'), 'manage_projects', 'categories.php');
					add_submenu_page($page, __('Import/Export', 'projectmanager'), __('Import/Export', 'projectmanager'), 'projectmanager_admin', 'admin.php?page=projectmanager/page/import.php&project_id='.$project->id);
				}
			}
		}
		
		if ( ! $this->isSingle() ) {
			$page = basename(__FILE__,".php").'/page/index.php';
			add_menu_page(__('Projects', 'projectmanager'), __('Projects', 'projectmanager'), 'manage_projects', $page,'', $this->plugin_url.'/images/menu.png');
			add_submenu_page($page, __('Overview', 'projectmanager'), __('Overview','projectmanager'),'manage_projects', $page,'');
			add_submenu_page($page, __( 'Settings'), __('Settings'), 'manage_projects', 'projectmanager', array( &$this, 'displayOptionsPage') );
		}
		
		$plugin = 'projectmanager/plugin-hook.php';
		add_filter( 'plugin_action_links_' . $plugin, array( &$this, 'pluginActions' ) );
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
		$projects = $this->getProjects();
		foreach ( $projects AS $project ) {
			if ( 1 == $options[$project->id]['navi_link'] && $this->getNumProjects() == 1) {
				$this->single = true;
				break;
			}
		}
		return $this->single;
	}
	
	
	/**
	 * pluginActions() - display link to settings page in plugin table
	 *
	 * @param array $links array of action links
	 * @return void
	 */
	function pluginActions( $links )
	{
		$settings_link = '<a href="admin.php?page=projectmanager">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
	
		return $links;
	}
	
	
	/**
	 * uninstall() - uninstalls ProjectManager
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
		$wpdb->query( "DROP TABLE {$wpdb->projectmanager_datasetmeta}" );

		delete_option( 'projectmanager' );
		delete_option( 'projectmanager_widget' );
	}
}
?>