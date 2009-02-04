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
		add_filter( 'projectmanager_dataset_list', array(&$this, 'list'), 10, 4 );
		add_filter( 'projectmanager_dataset_gallery', array(&$this, 'gallery'), 10, 4 );
		add_filter( 'projectmanager_single_view', array(&$this, 'single'), 10, 3 );
		add_filter( 'projectmanager_tablenav', array(&$this, 'tablenav'), 10 );
		
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
		add_shortcode( 'dataset_list', array(&$this, '') );
		add_shortcode( 'dataset_gallery', array(&$this, 'gallery') );
		add_shortcode( '', array(&$this, '') );
		
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
		
		if ( stristr( $content, '[prjctmngr_tablenav' )) {
			$search = "@\[prjctmngr_tablenav\s*=\s*(\w+)\]@i";
		
			if ( preg_match_all($search, $content , $matches) ) {
				if (is_array($matches)) {
					foreach($matches[1] AS $key => $v0) {
						$project_id = $v0;
						$search = $matches[0][$key];
						$replace = $this->getTablenav( $project_id );
				
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
	 * @param int $project_id
	 * @param string $pos
	 * @param string display compact|extend
	 * @return string
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
		$options = $options['project_optoins'][$this->project_id];
		$class = ($pos != '') ? 'align'.$pos : '';
		
		$out = "</p>";
		if ( !isset($_GET['show']) && -1 != $options['category'] ) {
			$out .= "\n\n<div class='".$class." projectmanager'>\n<form action='".get_permalink($page_ID)."' method='get'><input type='hidden' name='page_id' value='".$page_ID."' />\n";
			$out .= wp_dropdown_categories(array('echo' => 0, 'hide_empty' => 0, 'name' => 'cat_id', 'orderby' => 'name', 'selected' => $this->getCatID(), 'hierarchical' => true, 'child_of' => $options['category'], 'show_option_all' => __('View all categories')));
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
	 * @param int $project_id
	 * @param string $pos
	 * @return string
	 */
	function getCategoryList( $project_id, $pos )
	{
		global $wpdb;
		$this->project_id = $project_id;
		$options = get_option( 'projectmanager' );
		$options = $options['project_options'][$this->project_id];
		
		$out = '</p>';
		if ( !isset($_GET['show'])) {
			$out = "\n<div class='align".$pos."'>\n\t<ul>";
			$out .= wp_list_categories(array('echo' => 0, 'title_li' => __('Categories', 'projectmanager'), 'child_of' => $options['category']));
			$out .= "\n\t</ul>\n</div>";
		}
		$out .= '<p>';
		
		return $out;
	}
	
	
	/**
	 * getTablenav() - get dropdown selections
	 *
	 * Function to show display selection possbilities, namely Category selection and ordering of datasets
	 * The function is called via the filter projectmanager_display_selections and can be modified or overwritten by
	 *
	 * remove_filter('projectmanager_display_selections', array(&$projectmanager, 'getDisplaySelections'));
	 * add_filter('projectmanager_display_selections', 'my_function', 10);
	 *
	 * function my_function( ) {
	 *	// Do some stuff
	 * }
	 * @param none
	 * @return string
	 */
	function tablenav()
	{
		global $wp_query;
		$options = get_option( 'projectmanager' );
		$options = $options['project_options'][$this->project_id];
		
		$page_obj = $wp_query->get_queried_object();
		$page_ID = $page_obj->ID;
		
		$orderby = array( '' => __('Order By', 'projectmanager'), 'name' => __('Name','projectmanager'), 'id' => __('ID','projectmanager') );
		foreach ( $this->getFormFields() AS $form_field )
			$orderby['formfields_'.$form_field->id] = $form_field->label;
		
		$order = array( '' => __('Order','projectmanager'), 'ASC' => __('Ascending','projectmanager'), 'DESC' => __('Descending','projectmanager') );
	
		$out = "<div class='projectmanager_tablenav'><form action='".get_permalink($page_ID)."' method='get'><input type='hidden' name='page_id' value='".$page_ID."' />\n";
		if ( -1 != $options['category'] )
			$out .= wp_dropdown_categories(array('echo' => 0, 'hide_empty' => 0, 'name' => 'cat_id', 'orderby' => 'name', 'selected' => $this->getCatID(), 'hierarchical' => true, 'child_of' => $options['category'], 'show_option_all' => __('View all categories')));
		$out .= "<select size='1' name='orderby'>";
		foreach ( $orderby AS $key => $value ) {
			$selected = ($_GET['orderby'] == $key) ? ' selected="selected"' : '';
			$out .= "<option value='".$key."'".$selected.">".$value."</option>";
		}		
		$out .= "</select>";
		$out .= "<select size='1' name='order'>";
		foreach ( $order AS $key => $value ) {
			$selected = ($_GET['order'] == $key) ? ' selected="selected"' : '';
			$out .= "\n\t\t<option value='".$key."'".$selected.">".$value."</option>";
		}
		$out .= "</select>";
		$out .= "<input type='submit' value='".__( 'Apply' )."' class='button' /></form></div>";

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
	function list( $out, $project_id, $output = 'table', $cat_id = false )
	{
		global $wp_query;
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
			
			$num_total_datasets = $this->getNumDatasets($this->project_id, true);
			if ( $this->isSearch() )
				$out .= "<h3 style='clear:both;'>".sprintf(__('Search: %d of %d', 'projectmanager'),  $this->getNumDatasets($this->project_id), $num_total_datasets)."</h3>";
			elseif ( $this->isCategory() )
				$out .= "<h3 style='clear:both;'>".$this->getCatTitle($this->getCatID())."</h3>";
			
			$out .= apply_filters( 'projectmanager_tablenav', $out );
	
			if ( $datasets ) {
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
	function gallery( $out, $project_id, $num_cols, $cat_id = false )
	{
		$options = get_option( 'projectmanager' );
		$options = $options['project_options'][$this->project_id];
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
		
			$out .= apply_filters( 'projectmanager_tablenav', $out );
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
					if ($options['show_image'] == 1 && '' != $dataset->image)
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
	function single( $out, $project_id, $dataset_id )
	{
		$options = get_option( 'projectmanager' );
		$options = $options['project_options'][$this->project_id];
				
		$url = get_permalink();
		$url = add_query_arg('paged', $this->getDatasetPage($dataset_id), $url);
		$url = ($this->isCategory()) ? add_query_arg('cat_id', $this->getCatID(), $url) : $url;
					
		$out = "</p>";
		$out .= "\n<p><a href='".$url."'>".__('Back to list', 'projectmanager')."</a></p>\n";
		
		if ( $dataset = $this->getDataset( $dataset_id ) ) {
			$out .= "<fieldset class='dataset'><legend>".__( 'Details of', 'projectmanager' )." ".$dataset->name."</legend>\n";
			if ($options['show_image'] == 1 && '' != $dataset->image)
				$out .= "\t<div class='image'><img src='".$this->getImageUrl($dataset->image)."' title='".$dataset->name."' alt='".$dataset->name."' /></div>\n";
				
			$out .= "<dl>".$this->getDatasetMetaData( $dataset, 'dl', true )."\n</dl>\n";
			$out .= "</fieldset>\n";
		}
		
		$out .= "<p>";
		
		return $out;
	}