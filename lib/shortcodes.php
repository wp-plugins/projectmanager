<?php
/**
* Shortcodes class for the WordPress plugin ProjectManager
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright 	Copyright 2008-2009
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
		
		add_action( 'projectmanager_tablenav', array(&$this, 'displayTablenav') );
		add_action( 'projectmanager_dataset', array(&$this, 'displayDataset') );
		
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
						$replace = "[project id=".$project_id." template=table cat_id=".$matches[2][$key]."]";
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
	function displaySearchForm( $atts )
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
	function displayTablenav()
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
	 * Function to display the project in a page or post as list.
	 *
	 *	[project id="x" template="table|gallery" cat_id="3"]
	 *
	 * - project_id is the ID of the project to display
	 * - template is the template file without extension. Default values are "table" or "gallery".
	 * - cat_id: specify a category to only display those datasets. all datasets will be displayed if missing
	 *
	 * @param array $atts
	 * @return the content
	 */
	function displayProject( $atts )
	{
		global $wp, $projectmanager;
		
		extract(shortcode_atts(array(
			'id' => 0,
			'template' => 'table',
			'cat_id' => false,
			'orderby' => false,
			'order' => false
		), $atts ));
		$projectmanager->initialize($id);
		
		$options = get_option('projectmanager');
		$options = $options['project_options'][$id];
		
		$this->project_id = $id;
		if ( $cat_id ) $projectmanager->setCatID($cat_id);
	
		if ( isset( $_GET['show'] ) ) {
			$datasets = $title = $pagination = $project = false;
		} else {
			$orderby = $formfield_id = false;
			if ( $order ) {
				$tmp = explode("-",$orderby);
				$orderby = $tmp[0];
				$formfield_id = $tmp[1];
			}
			
			if ( $projectmanager->isSearch() )
				$datasets = $projectmanager->getSearchResults();
			else
				$datasets = $projectmanager->getDatasets( true, $orderby, $order, $formfield_id );
			
			$title = '';
			if ( $projectmanager->isSearch() ) {
				$title = "<h3 style='clear:both;'>".sprintf(__('Search: %d of %d', 'projectmanager'),  $projectmanager->getNumDatasets($projectmanager->getProjectID()), $num_datasets)."</h3>";
			} elseif ( $projectmanager->isCategory() ) {
				$title = "<h3 style='clear:both;'>".$projectmanager->getCatTitle($projectmanager->getCatID())."</h3>";
			}
			
			$pagination = ( $projectmanager->isSearch() ) ? '' : $projectmanager->getPageLinks();
			
			$i = 0;
			foreach ( $datasets AS $dataset ) {
				if ( substr($template,0,7) == 'gallery' ) {
					$url = get_permalink();
					$url = add_query_arg('show', $dataset->id, $url);
					$url = ($projectmanager->isCategory()) ? add_query_arg('cat_id', $projectmanager->getCatID(), $url) : $url;
				
					$datasets[$i]->URL = $url;
					$datasets[$i]->thumbURL = $projectmanager->getImageUrl('/thumb.'.$dataset->image);
				
					$project['num_datasets'] = $projectmanager->getNumDatasets($projectmanager->getProjectID(), true);
					$project['num_cols'] = ( $options['gallery_num_cols'] == 0 ) ? 4 : $options['gallery_num_cols'];
					$project['dataset_width'] = floor(100/$options['gallery_num_cols'])."%";
				} else {
					$url = get_permalink();$url = add_query_arg('show', $dataset->id, $url);
					$url = ($projectmanager->isCategory()) ? add_query_arg('cat_id', $projectmanager->getCatID(), $url) : $url;
				
					$datasets[$i]->name = ($projectmanager->hasDetails()) ? '<a href="'.$url.'">'.$dataset->name.'</a>' : $dataset->name;
				
					$project['num_datasets'] = $projectmanager->getNumDatasets($projectmanager->getProjectID(), true);
				}
				$i++;
			}
		}
		
		$out = $this->loadTemplate( $template, array('project' => $project, 'datasets' => $datasets, 'title' => $title, 'pagination' => $pagination) );
		
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
		
		$out = $this->loadTemplate( $filename, array('dataset' => $dataset, 'backurl' => $url) );

		if ( $echo )
			echo $out;
		
		return $out;
	}
}
?>