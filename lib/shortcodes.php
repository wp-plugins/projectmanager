<?php
/**
* Shortcodes class for the WordPress plugin ProjectManager
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright 	Copyright 2009
*/

class ProjectManagerShortcodes extends ProjectManager
{
	/**
	 * initialize shortcodes
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		$this->addShortcodes();
	}
	function LeagueManagerShortcodes()
	{
		$this->__construct();
	}
	
	
	/**
	 * Adds shortcodes
	 *
	 * @param none
	 * @return void
	 */
	function addShortcodes()
	{
		add_shortcode( 'dataset', array(&$this, 'single') );
		add_shortcode( 'dataset_list', array(&$this, 'list') );
		add_shortcode( 'dataset_gallery', array(&$this, 'gallery') );
		add_shortcode( 'projectmanager_search_form', array(&$this, 'getSearchForm') );
		
		add_action( 'dataset_single', array(&$this, 'single') );
		add_action( 'projectmanager_tablenav', array(&$this, 'tablenav') );
		
		add_filter( 'the_content', array(&$this, 'convert') );
	}
	
	
	/**
	 * Load template for user display. First the current theme directory is checked for a template
	 * before defaulting to the plugin
	 *
	 * @param string $template Name of the template file (without extension)
	 * @param array $vars Array of variables name=>value available to display code (optional)
	 * @return the content
	 */
	function loadTemplate( $template, $vars = array() )
	{
		extract($vars);
		
		ob_start();
		if ( file_exists( TEMPLATEPATH . "/projectmanager/$template.php")) {
			include(TEMPLATEPATH . "/projectmanager/$template.php");
		} elseif ( file_exists(LEAGUEMANAGER_PATH . "/view/$template.php") ) {
			include(PROJECTMANAGER_PATH . "/view/$template.php");
		} else {
			parent::setMessage( sprintf(__('Could not load template %s.php', 'projectmanager'), $template), true );
			parent::printMessage();
		}
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	
	/**
	 * replace old shortcodes with new ones
	 *
	 * @param string $content
	 * @return string
	 */
	function convert( $content )
	{
		if ( stristr( $content, '[projectmanager_search' )) {
			$search = "@\[prjctmngr_search_form\s*=\s*(\w+),(|right|center|left|),(|extend|compact|)\]@i";
	
			if ( preg_match_all($search, $content , $matches) ) {
				if (is_array($matches)) {
					foreach($matches[1] AS $key => $v0) {
						$project_id = $v0;
						$search = $matches[0][$key];
						$replace = "[projectmanager_search_form project_id=".$project_id." align=".$matches[2][$key]." display=".$matches[3][$key]." ]" );
			
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
						$replace = "[dataset_list project_id=".$project_id." output=".$matches[3][$key]." cat_id=".$matches[2][$key]."]";
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
						$replace =  "[dataset_gallery project_id=".$project_id." num_cols=".$matches[2][$key]." cat_id=".$matches[3][$key]." ]";
			
						$content = str_replace($search, $replace, $content);
					}
				}
			}
		}

		$content = str_replace('<p></p>', '', $content);
		return $content;
	}
	

	/**
	 * Function to display search formular
	 *
	 *	[projectmanager_search_form project_id="1" mode="extend|compact" ]
	 *
	 * - project_id is the ID of the project
	 * - mode controls if the search options dropdown is shown ('extend' or missing) or not ('compact')
	 *
	 * @param array $atts
	 * @return string
	 */
	function getSearchForm( $atts )
	{
		extract(shortcode_atts(array(
			'project_id' => 0,
			'align' => 'left',
			'display' => 'extend'
		), $atts ));
		
		$this->project_id = $project_id;
		
		$search_option = parent::getSearchOption();
		$search_string = parent::getSearchString();
		$form_fields = parent::getFormFields()
		
		$align = ( $align != '' ) ? 'align'.$pos : '';

		if ( !isset($_GET['show'])) {
			$out = $this->loadTeamplate( 'searchform', array( 'form_fields' => $form_fields, 'search_string' => $search_string, 'search_option' => $search_option, 'align' => $align, 'display' => $display ) );
		}

		return $out;
	}
		
	
	/**
	 * get dropdown selections
	 *
	 * This function is called via do_action('projectmanager_tablenav') and loads the template tablenav.php
	 *
	 * @param boolean $echo
	 * @return void the dropdown selections
	 */
	function tablenav()
	{
		global $wp_query;
		$options = get_option( 'projectmanager' );
		$options = $options['project_options'][$this->project_id];
		
		$page_obj = $wp_query->get_queried_object();
		$page_ID = $page_obj->ID;
		
		$orderby = array( '' => __('Order By', 'projectmanager'), 'name' => __('Name','projectmanager'), 'id' => __('ID','projectmanager') );
		foreach ( parent::getFormFields() AS $form_field )
			$orderby['formfields_'.$form_field->id] = $form_field->label;
		
		$order = array( '' => __('Order','projectmanager'), 'ASC' => __('Ascending','projectmanager'), 'DESC' => __('Descending','projectmanager') );
		
		$category = ( -1 != $options['category'] ) ? $options['category'] ) : false;
		$curr_cat = parent::getCatID();
		
		$out = $this->loadTeamplate( 'tablenav', array( 'page_ID' => $page_ID, 'category' => $category, 'curr_cat' => $curr_cat, 'orderby' => $orderby, 'order' => $order) );

		echo $out;
	}
	
	
	/**
	 * Function to display the datasets of a given project in a page or post as list.
	 *
	 *	[dataset_list project_id="1" output="table" cat_id="3"]
	 *
	 * - project_id is the ID of the project to display
	 * - output can be either "table", "ul" or "ol" to show them as table, unsorted or sorted list. (default is "table")
	 * - cat_id: specify a category to only display those datasets. all datasets will be displayed if missing
	 *
	 * @param array $atts
	 * @return the content
	 */
	function list( $atts )
	{
		global $wp_query;
		
		extract(shortcode_atts(array(
			'project_id' => 0,
			'output' = > 'table',
			'cat_id' => false
		), $atts ));
		parent::initialize($project_id);
		
		$this->project_id = $project_id;
		if ( $cat_id ) parent::setCatID($cat_id);
	
		if ( isset( $_GET['show'] ) )
			$out = $this->single(array('id' => $_GET['show']));
		else {
			if ( parent::isSearch() )
				$datasets = parent:getSearchResults();
			else
				$datasets = parent::getDatasets( true );
	
			$num_datasets = parent::getNumDatasets(parent::getProjectID(), true);
			
			$out = $this->loadTemplate( 'list', array('datasets' => $datasets, 'num_datasets' => $num_datasets, 'output' => $output) );
		}
		
		return $out;
	}
	
	
	/**
	 * Function to display the datasets of a given project in a page or post as gallery.
	 *
	 *	[dataset_gallery project_id="1" num_cols="3" cat_id="3"]
	 *
	 * - project_id is the ID of the project to display
	 * - num_cols is the number of columns (default: 3)
	 * - cat_id: specify a category to only display those datasets. all datasets will be displayed if missing
	 *
	 * @param array $atts
	 * @return the content
	 */
	function gallery( $atts )
	{
		extract(shortcode_atts(array(
			'project_id' => 0,
			'num_cols' => 3,
			'cat_id' => false
		), $atts ));
		
		$this->project_id = $project_id;
		parent::initialize($project_id);
		
		$options = get_option( 'projectmanager' );
		$options = $options['project_options'][$this->project_id];
		
		if ( $cat_id ) parent::setCatID($cat_id);

		if ( isset( $_GET['show'] ) ) {
			$out = $this->single(array('id' => $_GET['show']));
		} else {
			if ( parent::isSearch() )
				$datasets = parent::getSearchResults();
			else
				$datasets = parent::getDatasets( true );
				
			$out = $this->loadTemplate( 'gallery', array('datasets' => $datasets, 'num_cols' => $num_cols) );
		}
		
		return $out;
	}
	
	
	/**
	 * Function to display the single view of a dataset. Loaded by function list and gallery
	 *
	 *	[dataset id="1" ]
	 *
	 * - id is the ID of the dataset to display
	 *
	 * @param int $dataset_id
	 * @return string
	 */
	function single( $atts )
	{
		extract(shortcode_atts(array(
			'id' => 0,
		), $atts ));
		
		$backurl = get_permalink();
		$backurl = add_query_arg('paged', parent::getDatasetPage($id), $url);
		$backurl = (parent::isCategory()) ? add_query_arg('cat_id', parent::getCatID(), $url) : $url;
		
		$dataset = $this->getDataset( $id );
				
		$out = $this->loadTemplate( 'single', array('dataset' => $dataset, 'backurl' => $backurl) );
		
		return $out;
	}