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
		add_shortcode( 'dataset', array(&$this, 'displayDataset') );
		add_shortcode( 'project', array(&$this, 'displayProject') );
		add_shortcode( 'project_search', array(&$this, 'displaySearchForm') );
		
		add_action( 'projectmanager_tablenav', array(&$this, 'tablenav') );
		add_action( 'projectmanager_dataset', array(&$this, 'dataset') );
		
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
		global $projectmanager;
		extract($vars);
		
		ob_start();
		if ( file_exists( TEMPLATEPATH . "/projectmanager/$template.php")) {
			include(TEMPLATEPATH . "/projectmanager/$template.php");
		} elseif ( file_exists(PROJECTMANAGER_PATH . "/view/$template.php") ) {
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
		if ( stristr( $content, '[prjctmngr_search' )) {
			$search = "@\[prjctmngr_search_form\s*=\s*(\w+),(|right|center|left|),(|extend|compact|)\]@i";
	
			if ( preg_match_all($search, $content , $matches) ) {
				if (is_array($matches)) {
					foreach($matches[1] AS $key => $v0) {
						$project_id = $v0;
						$search = $matches[0][$key];
						$replace = '[project_search project_id='.$project_id.' template='.$matches[3][$key].']';
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
						$replace = "[project id=".$project_id." template=".$matches[3][$key]." cat_id=".$matches[2][$key]."]";
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
						$replace =  "[project id=".$project_id." template=gallery cat_id=".$matches[3][$key]." ]";
			
						$content = str_replace($search, $replace, $content);
					}
				}
			}
		}

		return $content;
	}
	

	/**
	 * Function to display search formular
	 *
	 * @param array $atts
	 * @return string
	 */
	function getsearchForm( $atts )
	{
		global $projectmanager;
		
		extract(shortcode_atts(array(
			'project_id' => 0,
			'template' => 'extend'
		), $atts ));
		
		$projectmanager->initialize($project_id);

		$search_option = $projectmanager->getSearchOption();
		$search_string = $projectmanager->getSearchString();
		$form_fields = $projectmanager->getFormFields();
		
		$filename = 'searchform-'.$template;
		if ( !isset($_GET['show'])) {
			$out = $this->loadTemplate( $filename, array( 'form_fields' => $form_fields, 'search' => $search_string, 'search_option' => $search_option ) );
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
		global $projectmanager;
		$options = get_option( 'projectmanager' );
		$options = $options['project_options'][$this->project_id];
		
		$orderby = array( '' => __('Order By', 'projectmanager'), 'name' => __('Name','projectmanager'), 'id' => __('ID','projectmanager') );
		foreach ( $projectmanager->getFormFields() AS $form_field )
			$orderby['formfields_'.$form_field->id] = $form_field->label;
		
		$order = array( '' => __('Order','projectmanager'), 'ASC' => __('Ascending','projectmanager'), 'DESC' => __('Descending','projectmanager') );
		
		$category = ( -1 != $options['category'] ) ? $options['category'] : false;
		$selected_cat = $projectmanager->getCatID();
		
		$out = $this->loadTemplate( 'tablenav', array( 'category' => $category, 'selected_cat' => $selected_cat, 'orderby' => $orderby, 'order' => $order) );

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
	function displayProject( $atts )
	{
		global $wp_query, $projectmanager;
		
		extract(shortcode_atts(array(
			'id' => 0,
			'template' => 'table',
			'cat_id' => false
		), $atts ));
		$projectmanager->initialize($project_id);
		
		$this->project_id = $project_id;
		if ( $cat_id ) $projectmanager->setCatID($cat_id);
	
		if ( isset( $_GET['show'] ) )
			$datasets = $title = $num_datasets = $pagination = false;
		else {
			if ( $projectmanager->isSearch() )
				$datasets = $projectmanager->getSearchResults();
			else
				$datasets = $projectmanager->getDatasets( true );
	
			$num_datasets = $projectmanager->getNumDatasets($projectmanager->getProjectID(), true);
			
			$title = '';
			if ( $projectmanager->isSearch() ) {
				$title = "<h3 style='clear:both;'>".sprintf(__('Search: %d of %d', 'projectmanager'),  $projectmanager->getNumDatasets($projectmanager->getProjectID()), $num_datasets)."</h3>";
			} elseif ( $projectmanager->isCategory() ) {
				$title = "<h3 style='clear:both;'>".$projectmanager->getCatTitle($projectmanager->getCatID())."</h3>";
			}
			
			$pagination = ( $projectmanager->isSearch() ) ? '' : $projectmanager->getPageLinks();
			
			$i = 0;
			foreach ( $datasets AS $dataset ) {
				$url = get_permalink();$url = add_query_arg('show', $dataset->id, $url);
				$url = ($projectmanager->isCategory()) ? add_query_arg('cat_id', $projectmanager->getCatID(), $url) : $url;
				
				$datasets[$i]->name = ($projectmanager->hasDetails()) ? '<a href="'.$url.'">'.$dataset->name.'</a>' : $dataset->name;
				
				$i++;
			}
			
			$filename = ( empty($template) ) ? 'list' : 'list-'.$template;
			$out = $this->loadTemplate( $filename, array('datasets' => $datasets, 'title' => $title, 'num_datasets' => $num_datasets, 'pagination' => $pagination) );
		}
		
		return $out;
	}
	
	
	/**
	 * Function to display the datasets of a given project in a page or post as gallery.
	 *
	 *	[dataset_gallery project_id="1" templatenum_cols="3" cat_id="3"]
	 *
	 * - project_id is the ID of the project to display
	 * - num_cols is the number of columns (default: 3)
	 * - cat_id: specify a category to only display those datasets. all datasets will be displayed if missing
	 *
	 * @param array $atts
	 * @return the content
	 */
	function datasetGallery( $atts )
	{
		global $projectmanager;
		extract(shortcode_atts(array(
			'project_id' => 0,
			'template' => '',
			'num_cols' => 4,
			'cat_id' => false
		), $atts ));
		
		$this->project_id = $project_id;
		$projectmanager->initialize($project_id);

		if ( $cat_id ) $projectmanager->setCatID($cat_id);

		if ( !isset($_GET['show']) ) {
			if ( $projectmanager->isSearch() )
				$datasets = $projectmanager->getSearchResults();
			else
				$datasets = $projectmanager->getDatasets( true );
			
			$gallery['num_cols'] = $num_cols;
			$gallery['dataset_width'] = floor(100/$num_cols)."%";
			
			$pagination = ( $projectmanager->isSearch() ) ? '' : $projectmanager->getPageLinks();
			
			$i = 0;
			foreach ( $datasets AS $dataset ) {
				$url = get_permalink();
				$url = add_query_arg('show', $dataset->id, $url);
				$url = ($projectmanager->isCategory()) ? add_query_arg('cat_id', $projectmanager->getCatID(), $url) : $url;
				
				$datasets[$i]->URL = $url;
				$datasets[$i]->thumbURL = $projectmanager->getImageUrl('/thumb.'.$dataset->image);
				
				$i++;
			}
		} else {
			$gallery = $datasets = $pagination = false;
		}
				
		$filename = ( empty($template) ) ? 'gallery' : 'gallery-'.$template;
		$out = $this->loadTemplate( $filename, array('gallery' => $gallery, 'datasets' => $datasets, 'pagination' => $pagination) );
		
		return $out;
	}
	
	
	/**
	 * Function to display the single view of a dataset. Loaded by function list and gallery
	 *
	 *	[dataset id="1" template="" ]
	 *
	 * - id is the ID of the dataset to display
	 * - template is the name of a template (without extension). Will use default template dataset.php if missing or empty
	 *
	 * @param int $dataset_id
	 * @return string
	 */
	function displayDataset( $atts )
	{
		global $projectmanager;
		
		extract(shortcode_atts(array(
			'id' => 0,
			'template' => '',
			'echo' => 0
		), $atts ));
		
		$url = get_permalink();
		$url = remove_query_arg('show', $url);
		$url = add_query_arg('paged', $projectmanager->getDatasetPage($id), $url);
		$url = ($projectmanager->isCategory()) ? add_query_arg('cat_id', $projectmanager->getCatID(), $url) : $url;
		
		$dataset = $this->getDataset( $id );
		$dataset->imgURL = $projectmanager->getImageUrl($dataset->image);
				
		$filename = ( empty($template) ) ? 'dataset' : $template;
		
		$out = $this->loadTemplate( $filename, array($filename => $dataset, 'backurl' => $url) );

		if ( $echo )
			echo $out;
		else
			return $out;
	}
}
?>