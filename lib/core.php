<?php

class ProjectManager
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
	 * current project object
	 *
	 * @var object
	 */
	var $project;
	
	
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
	 * order of datasets
	 *
	 * @var string
	 */
	var $order = 'ASC';
	
	
	/**
	 * datafield datasets are ordered by
	 *
	 * @var string
	 */
	var $orderby = 'name';
	
	
	/**
	 * __construct() - Initialize project settings
	 *
	 * @param int $project_id ID of selected project. false if none is selected
	 * @return void
	 */
	function __construct( $project_id )
	{
		global $wpdb;
			
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
		$options = $options['project_options'][$project_id];
		
		$this->project_id = $project_id;
		$this->per_page = isset($options['per_page']) ? $options['per_page'] : 20;

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
			'current' => $this->getCurrentPage(),
			'add_args' => $this->query_args
		));
		return $page_links;
	}
	

	/**
	 * setDatasetOrder() - get dataset order
	 *
	 * @param none
	 * @return boolean
	 */
	function setDatasetOrder( )
	{
		$options = get_option('projectmanager');
		$options = $options['project_options'][$this->project_id];

		$formfield_id = false;
		if ( isset($_POST['orderby']) && isset($_POST['order']) && !isset($_POST['doaction']) ) {
			$orderby = explode('_', $_POST['orderby']);
			$this->orderby = ( $_POST['orderby'] != '' ) ? $_POST['orderby'] : 'name';
			$formfield_id = $orderby[1];
			$this->order = ( $_POST['order'] != '' ) ? $_POST['order'] : 'ASC';

			$this->query_args['order'] = $this->order;
			$this->query_args['orderby'] = $this->orderby;
		} elseif ( isset($_GET['orderby']) && isset($_GET['order']) ) {
			$orderby = explode('_', $_GET['orderby']);
			$this->orderby = ( $_GET['orderby'] != '' ) ? $_GET['orderby'] : 'name';
			$formfield_id = $orderby[1];
			$this->order = ( $_GET['order'] != '' ) ? $_GET['order'] : 'ASC';
		} elseif ( isset($options['dataset_orderby']) && $options['dataset_orderby'] != 'formfields' && $options['dataset_orderby'] != '' ) {
			$this->orderby = $options['dataset_orderby'];
			$this->order = 'ASC';
		} else {
			$this->orderby = 'name';
			$this->order = 'ASC';
		}
		return $formfield_id;
	}
	

	/**
	 * getDatasetOrder() - get SQL order
	 *
	 * @param none
	 * @return string
	 */
	function getDatasetOrder()
	{
		return $this->orderby." ".$this->order;
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
	 * setMessage() - set a message
	 *
	 * @param string $message
	 * @param boolean $error
	 * @return none
	 */
	function setMessage( $message, $error = false )
	{
		$type = 'success';
		if ( $error ) {
			$this->error = true;
			$type = 'error';
		}
		$this->message[$type] = $message;
	}
	
	
	/**
	 * printMessage() - print formatted success or error message
	 *
	 * @param none
	 */
	function printMessage()
	{
		if ( $this->error )
			echo "<div class='error'><p>".$this->message['error']."</p></div>";
		else
			echo "<div id='message' class='updated fade'><p><strong>".$this->message['success']."</strong></p></div>";
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
			return WP_CONTENT_DIR.'/uploads/projects/'.$file;
		else
			return WP_CONTENT_DIR.'/uploads/projects';
	}
	
	
	/**
	 * returns url of image directory
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
	 * gets project ID
	 *
	 * @param none
	 * @return int
	 */
	function getProjectID()
	{
		return $this->project_id;
	}
	
	
	/**
 	 * setCatID() - sets current category
	 *
	 * @param int $cat_id
	 * @return void
	 */
	function setCatID( $cat_id = false )
	{
		if ( $cat_id )
			$this->cat_id = $cat_id;
		elseif ( isset($_POST['cat_id']) ) {
			$this->cat_id = (int)$_POST['cat_id'];
			$this->query_args['cat_id'] = $this->cat_id;
		} elseif ( isset($_GET['cat_id']) )
			$this->cat_id = (int)$_GET['cat_id'];
		 else
			$this->cat_id = null;
			
		return;
	}
	
	
	/**
	 * getCatID() - gets current category
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
	 * getProjectTitle() - gets project title
	 *
	 * @param none
	 * @return string
	 */
	function getProjectTitle( )
	{
		return $this->project->title;
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
	function getProject( $project_id = false )
	{
		global $wpdb;

		if ( !$project_id ) $project_id = $this->project_id;
		$projects = $wpdb->get_results( "SELECT `title`, `id` FROM {$wpdb->projectmanager_projects} WHERE `id` = {$project_id} ORDER BY `id` ASC" );
		$this->project = $projects[0];
		return $projects[0];
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
		$formfield_id = $this->setDatasetOrder();

		$sql_order = ( $this->orderby != 'name' && $this->orderby != 'id' ) ? 'name '.$this->order : $this->getDatasetOrder();
		
		if ( $limit ) $offset = ( $this->getCurrentPage() - 1 ) * $this->per_page;

		$sql = "SELECT `id`, `name`, `image`, `cat_ids`, `user_id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$this->project_id}";
		
		if ( $this->isCategory() )
			$sql .= $this->getCategorySearchString();
		
		$sql .=  " ORDER BY ".$sql_order;
		$sql .= ( $limit ) ? " LIMIT ".$offset.",".$this->per_page.";" : ";";
		
		$datasets = $wpdb->get_results($sql);
		
		if ( $options['project_options'][$this->project_id]['dataset_orderby'] == 'formfields' || $formfield_id )
			$datasets = $this->orderDatasetsByFormFields($datasets, $formfield_id);
		
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
	 * get datasets by date
	 *
	 * @param string $year
	 * @param string $month
	 * @param string $day
	 * @return array of datasets
	 */
	function getDatasetsByDate()
	{
	}
	
	
	/**
	 * orderDatasetsByFormFields() - order datasets by chosen form fields
	 *
	 * @param array $datasets
	 * @param int|false $form_field_id
	 * @return array
	 */
	function orderDatasetsByFormFields( $datasets, $form_field_id = false )
	{
		global $wpdb;
	
		/*
		* Generate array of parameters to sort datasets by
		*/
		$to_sort = array();
		if ( !$form_field_id ) {
			foreach ( $this->getFormFields( ) AS $form_field )
				if ( 1 == $form_field->order_by )
					$to_sort[] = $form_field->id;
		} else {
			$to_sort[] = $form_field_id;
		}
		
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
				$sort = ( $this->order = 'DESC' ) ? SORT_DESC : SORT_ASC;
				array_push( $func_args, $order_array );
				array_push( $func_args, $sort );
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
		$number = $offsets[$dataset_id] + 1;
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
				$meta_value = (is_string($meta->value)) ? htmlspecialchars( $meta->value ) : $meta->value;
				
				if ( 1 == $meta->type || 6 == $meta->type || 7 == $meta->type || 8 == $meta->type )
					$meta_value = "<span id='datafield".$meta->form_field_id."_".$dataset->id."'>".$meta_value."</span>";
				elseif ( 2 == $meta->type ) {
					if ( strlen($meta_value) > 150 && !$show_all )
						$meta_value = substr($meta_value, 0, 150)."...";
					$meta_value = nl2br($meta_value);
						
					$meta_value = "<span id='datafield".$meta->form_field_id."_".$dataset->id."'>".$meta_value."</span>";
				} elseif ( 3 == $meta->type && $meta_value != '')
					$meta_value = "<a href='mailto:".$meta_value."' class='projectmanager_email'><span id='datafield".$meta->form_field_id."_".$dataset->id."'>".$meta_value."</span></a>";
				elseif ( 4 == $meta->type )
					$meta_value = "<span id='datafield".$meta->form_field_id."_".$dataset->id."'>".mysql2date(get_option('date_format'), $meta_value )."</span>";
				elseif ( 5 == $meta->type && $meta_value != '')
					$meta_value = "<a class='projectmanager_url' href='http://".$meta_value."' target='_blank' title='".$meta_value."'><span id='datafield".$meta->form_field_id."_".$dataset->id."'>".$meta_value."</span></a>";
					
				if ( 1 == $meta->show_on_startpage || $show_all ) {
					if ( $meta->value != '' ) {
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
					} elseif ( 'td' == $output ) {
						$out .= "\n\t<".$output.">&#160;</".$output.">";
					}
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
		$this->message['success'] = __('Project added','projectmanager');
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
		$this->message['success'] = __('Project updated','projectmanager');
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
					
					$this->message['success'] = sprintf(__( '%d Datasets successfully imported', 'projectmanager' ), $i);
				} else {
					$this->error = true;
					$this->message['error'] = __('The file is not readable', 'projectmanager');
				}
			} else {
				$this->error = true;
				$this->message['error'] = sprintf( __('The uploaded file could not be moved to %s.' ), $this->getImagePath() );
			}
			@unlink($new_file); // remove file from server after import is done
		} else {
			$this->error = true;
			$this->message['error'] = __('The uploaded file seems to be empty', 'projectmanager');
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
				
			$this->message['success'] = __( 'New dataset added to the database.', 'projectmanager' ).' '.$tail;
		} else {
			$this->error = true;
			$this->message['error'] = __( 'An Entry of your user ID has been detected', 'projectmanager' );
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
			
			$this->message['success'] = __('Dataset updated.', 'projectmanager');
		} else {
			$this->error = true;
			$this->message['error'] = __( "You don't have the permission to edit this dataset", "projectmanager" );
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
				$options = $options['project_options'][$this->project_id];
				
				$new_file =  $this->getImagePath().'/'.basename($file['name']);
				if ( file_exists($new_file) && !$overwrite ) {
					$this->error = true;
					$this->message['error'] = __('File exists and is not uploaded. Set the overwrite option if you want to replace it.','projectmanager');
				} else {
					if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
						if ( $dataset = $this->getDataset($dataset_id) )
							if ( $dataset->image != '' ) $this->delImage($dataset->image);

						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", basename($file['name']), $dataset_id ) );
			
						// Resize original file and create thumbnails
						$dims = array( 'width' => $options['medium_size']['width'], 'height' => $options['medium_size']['height'] );
						$this->createThumbnail( $new_file, $dims, $new_file );

						$dims = array( 'width' => $options['thumb_size']['width'], 'height' => $options['thumb_size']['height'] );
						$this->createThumbnail( $new_file, $dims, $this->getImagePath().'/thumb.'.basename($file['name']) );

						$dims = array( 'width' => 80, 'height' => 50 );
						$this->createThumbnail( $new_file, $dims, $this->getImagePath().'/tiny.'.basename($file['name']) );
					} else {
						$this->error = true;
						$this->message['error'] = sprintf( __('The uploaded file could not be moved to %s.' ), $this->getImagePath() );
					}
				}
			}
		} else {
			$this->error = true;
			$this->message['error'] = __('The file type is not supported.','projectmanager');
		}
	}
	
	

	
	
	/**
	 * profileHook() - hook dataset input fields into profile
	 *
	 * @param none
	 */
	function profileHook()
	{
		global $current_user, $wpdb, $projectmanager;
		
		if ( current_user_can('manage_projects') ) {
			$options = get_option('projectmanager');
			$options = $options['project_options'];
			
			$this->project_id = 0;
			foreach ( $options AS $project_id => $settings ) {
				if ( 1 == $settings['profile_hook'] ) {
					$this->project_id = $project_id;
					break;
				}
			}
			
			if ( $this->project_id != 0 ) {
				$this->getProject();
				
				$is_profile_page = true;
				$dataset = $wpdb->get_results( "SELECT `id`, `name`, `image`, `cat_ids`, `user_id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$this->project_id} AND `user_id` = '".$current_user->ID."' LIMIT 0,1" );
				$dataset = $dataset[0];
				
				if ( $dataset ) {
					$dataset_id = $dataset->id;
					$cat_ids = $this->getSelectedCategoryIDs($dataset);
					$dataset_meta = $this->getDatasetMeta( $dataset_id );
		
					$img_filename = $dataset->image;
					$meta_data = array();
					foreach ( $dataset_meta AS $meta )
						$meta_data[$meta->form_field_id] = $meta->value;
				} else {
					$dataset_id = ''; $cat_ids = array(); $img_filename = ''; $meta_data = array();
				}
				
				echo '<h3>'.$this->getProjectTitle().'</h3>';
				echo '<input type="hidden" name="project_id" value="'.$this->project_id.'" /><input type="hidden" name="dataset_id" value="'.$dataset_id.'" /><input type="hidden" name="dataset_user_id" value="'.$current_user->ID.'" />';
				
				include( 'page/dataset-form.php' );
			}
		}
	}
	
	
	/**
	 * updateProfile() - update Profile settings
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
			$overwrite_image = isset( $_POST['overwrite_image'] ) ? true: false;
			$this->editDataset( $_POST['project_id'], $_POST['display_name'], $_POST['post_category'], $_POST['dataset_id'], $_POST['form_field'], $user_id, $del_image, $_POST['image_file'], $overwrite_image );
		}
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
		$this->message['success'] = __('Form Fields updated', 'projectmanager');
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
	 * printBreadcrumb() - print breadcrumb navigation
	 *
	 * @param int $project_id
	 * @param string $page_title
	 * @param boolean $start
	 */
	function printBreadcrumb( $page_title, $start=false )
	{
		$options = get_option('projectmanager');
		if ( 1 != $options['project_options'][$this->project_id]['navi_link'] ) {
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
		
		echo "\n\n<!-- WP-ProjectManager START -->\n";
		echo "<link rel='stylesheet' href='".$this->plugin_url."/style.css' type='text/css' />\n";

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
				$this->setMessage(__( 'Settings saved', 'leaguemanager' ));
				$this->printMessage();
			}
			include( 'settings-global.php' );	
		} elseif(!$include) {
			echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
		}
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
				if ( 1 == $options['project_options'][$project->id]['navi_link'] ) {
					$page = 'admin.php?page=projectmanager/page/show-project.php&project_id='.$project->id;
					add_menu_page( $project->title, $project->title, 'manage_projects', $page, '', $this->plugin_url.'/admin/icons/databases.png' );
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
			add_menu_page(__('Projects', 'projectmanager'), __('Projects', 'projectmanager'), 'manage_projects', $page,'', $this->plugin_url.'/admin/icons/databases.png');
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
			if ( 1 == $options['project_options'][$project->id]['navi_link'] && $this->getNumProjects() == 1) {
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
	
	

}
?>
