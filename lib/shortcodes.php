<?php
/**
* Shortcodes class for the WordPress plugin ProjectManager
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright Copyright 2008-2015
*/

class ProjectManagerShortcodes
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
		add_shortcode( 'dataset_form', array(&$this, 'displayDatasetForm') );
		add_shortcode( 'project_search', array(&$this, 'displaySearchForm') );
		add_shortcode( 'projectmanager_num_datasets', array(&$this, 'displayNumDatasets') );
		add_shortcode( 'testimonials', array(&$this, 'displayTestimonials') );
		add_action( 'projectmanager_selections', array(&$this, 'displaySelections') );
		add_action( 'projectmanager_tablenav', array(&$this, 'displaySelections') );
		add_action( 'projectmanager_dataset', array(&$this, 'displayDataset'), 10, 2 );
	}
	
	
	/**
	 * Load template for user display.
	 * 
	 * Checks firrst the current theme directory for a template
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
		} elseif ( file_exists(PROJECTMANAGER_PATH . "/templates/$template.php") ) {
			include(PROJECTMANAGER_PATH . "/templates/$template.php");
		} else {
			$projectmanager->setMessage( sprintf(__('Could not load template %s.php', 'projectmanager'), $template), true );
			$projectmanager->printMessage();
		}
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
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
		
		$projectmanager->init(intval($project_id));
		$project = $projectmanager->getCurrentProject();
		
		$search_option = $projectmanager->getSearchOption();
		$search_string = $projectmanager->getSearchString();
		$form_fields = $projectmanager->getFormFields();
		
		$filename = 'searchform-'.$template;
		if ( !isset($_GET['show'])) {
			$out = $this->loadTemplate( $filename, array( 'project' => $project, 'project_id' => intval($project_id), 'form_fields' => $form_fields, 'search' => $search_string, 'search_option' => $search_option ) );
		} else {
			$out = "";
		}

		return $out;
	}
		
	
	/**
	 * display dataset form
	 *
	 * Include the dataset formular into the frontpage
	 *
	 * @param array $atts
	 * @return void
	 */
	function displayDatasetForm( $atts )
	{
		global $projectmanager;

		extract(shortcode_atts(array(
			'project_id' => 0,
			'submit_message' => 'Dataset added to the database',
			'submit_title' => 'Submit',
			'use_captcha' => 'true',
			'captcha_timeout' => 0,
			'template' => '',
		), $atts ));

		$project_id = intval($project_id);
		$projectmanager->init(intval($project_id));
		$project = $projectmanager->getCurrentProject();
		
		print_r($_SESSION['projectmanager_captcha']);
		
		$message = "";
		if (isset($_POST['insertDataset'])) {
			require_once (PROJECTMANAGER_PATH . '/admin/admin.php');	
			$admin = new ProjectManagerAdminPanel();

			$error = false;
			if (isset($_POST['projectmanager_captcha'])) {
				$code = $_SESSION['projectmanager_captcha']['code'];
				$now = time();
				// if timeout is specified in minutes
				if ($captcha_timeout > 0 && ($now - $_SESSION['projectmanager_captcha']['time'])/60 > $captcha_timeout) {
					$error = true;
					$message = __('Your session has expired', 'projectmanager');
				} elseif ($_POST['projectmanager_captcha'] != $code) {
					$error = true;
					$message = __('Wrong Captcha Code', 'projectmanager');
				}
				
				// delete captcha image
				@unlink($projectmanager->getCaptchaPath($_SESSION['projectmanager_captcha']['filename']));
			}
			
			if (!$error) {
				check_admin_referer( 'projectmanager_insert_dataset' );
				$user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : false;
				$category = isset($_POST['post_category']) ? $_POST['post_category'] : '';
				$admin->addDataset( intval($_POST['project_id']), htmlspecialchars($_POST['d_name']), $category, $_POST['form_field'], $user_id, false );
				
				if (!$admin->isError()) $message = htmlspecialchars($submit_message);
			}
		}
		
		$options = get_option('projectmanager');
		if ( isset($_GET['d_id']) ) {
			$edit = true;
			$form_title = __('Edit Dataset','projectmanager');
			$dataset_id = intval($_GET['d_id']);
			$dataset = $projectmanager->getDataset( $dataset_id );
	
			$cat_ids = $projectmanager->getSelectedCategoryIDs($dataset);
			$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );
	
			$name = htmlspecialchars(stripslashes_deep($dataset->name), ENT_QUOTES);
	
			$img_filename = $dataset->image;
			$meta_data = array();
			foreach ( $dataset_meta AS $meta ) {
				if ( is_string($meta_data[$meta->form_field_id] ) )
					$meta_data[$meta->form_field_id] = stripslashes_deep($meta->value);
				else
					$meta_data[$meta->form_field_id] = stripslashes_deep($meta->value);
			}
		}  else {
			$edit = false;
			$form_title = __('Add Dataset','projectmanager');
			$dataset_id = ''; $cat_ids = array(); $img_filename = ''; $name = ''; $meta_data = array();
			$dataset = false;
		}

		$projectmanager->loadTinyMCE(); 

		$filename = 'dataset-form';
		if ($template != "") $filename = 'dataset-form-'.$template;
		$out = $this->loadTemplate( $filename, array('projectmanager' => $projectmanager, 'dataset_id' => $dataset_id, 'dataset' => $dataset, 'project' => $project, 'name' => $name, 'img_filename' => $img_filename, 'meta_data' => $meta_data, 'edit' => $edit, 'cat_ids' => $cat_ids, 'form_title' => $form_title, 'button_title' => $submit_title, 'use_captcha' => $use_captcha, 'message' => $message) );

		return $out;
	}


	/**
	 * get dropdown selections
	 *
	 * This function is called via do_action('projectmanager_selections') and loads the template selections.php
	 *
	 * @param int $project_id
	 * @return void the dropdown selections
	 */
	function displaySelections( $project_id = false )
	{
		global $projectmanager;
		if ( $project_id ) $project_id = intval($project_id);
		else $project_id = $projectmanager->getProjectID();
		
		$project = $projectmanager->getProject(intval($project_id));
		//else
			//$project = $projectmanager->getCurrentProject();
			
		$orderby = array( '' => __('Order By', 'projectmanager'), 'name' => __('Name','projectmanager'), 'id' => __('ID','projectmanager') );
		foreach ( $projectmanager->getFormFields() AS $form_field ) {
			if ($form_field->private == 0)
				$orderby['formfields_'.$form_field->id] = $form_field->label;
		}

		$order = array( '' => __('Order','projectmanager'), 'asc' => __('Ascending','projectmanager'), 'desc' => __('Descending','projectmanager') );
		
		$category = ( -1 != $project->category ) ? $project->category : false;
		$selected_cat = $projectmanager->getCatID();
		
		$orderby_request = (isset($_GET['orderby_'.$project_id])) ? htmlspecialchars($_GET['orderby_'.$project_id]) : '';
		$order_request = (isset($_GET['order_'.$project_id]) ) ? htmlspecialchars($_GET['order_'.$project_id]) : '';
		
		$out = $this->loadTemplate( 'selections', array( 'project_id' => $project_id, 'category' => $category, 'orderby' => $orderby, 'order' => $order, 'selected' => array('category' => $selected_cat, 'orderby' => $orderby_request, 'order' => $order_request) ) );

		echo $out;
	}
	
	
	/**
	 * Function to display the project in a page or post as list.
	 *
	 *	[project id="x" template="table|gallery"]
	 *
	 * - id is the ID of the project to display
	 * - template is the template file without extension. Default values are "table" or "gallery".
	 *
	 * It follows a list of optional attributes
	 *
	 * - cat_id: specify a category to only display those datasets. all datasets will be displayed if missing
	 * - orderby: 'name', 'id' or 'formfield_X' where x is the formfield ID (default 'name')
	 * - order: 'asc' or 'desc' (default 'asc')
	 * - single: control if link to sigle dataset is displayed. Either 'true' or 'false' (default 'true')
	 * - selections: control wether or not selection panel is dislayed (default 'true')
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
			'order' => false,
			'single' => 'true',
			'selections' => 'true',
			'results' => true,
			'field_id' => false,
			'field_value' => false,
		), $atts ));
		$project_id = intval($id);
		
		$get_cat_id = "cat_id_".$project_id;
		if (isset($_GET[$get_cat_id])) $cat_id = intval($_GET[$get_cat_id]);
		if ( $cat_id ) $projectmanager->setCatID(intval($cat_id));
		
		$projectmanager->init($project_id);
		$project = $projectmanager->getCurrentProject();
		$single = ( $single == 'true' ) ? true : false;
		$random = ( $orderby == 'rand' ) ? true : false;
		
		$show = "show_".$project_id;
		if ( isset($_GET[$show]) && !$projectmanager->isSearch() ) {// && isset($_GET['project_id']) && $_GET['project_id'] == $project_id ) {
			$datasets = $title = $pagination = false;
			$dataset_id = intval($_GET[$show]);
		} else {
			$formfield_id = false;
			$dataset_id = false;
			
			$page_get = "paged_".$project_id;
			if (isset($_GET['paged']) && isset($_GET['project_id']) && $_GET['project_id'] == $project_id)
				$current_page = intval($_GET['paged']);
			elseif (isset($wp->query_vars['paged']) && isset($_GET['project_id']) && $_GET['project_id'] == $project_id)
				$current_page = max(1, intval($wp->query_vars['paged']));
			elseif (isset($_GET[$page_get]))
				$current_page = intval($_GET[$page_get]);
			elseif (isset($wp->query_vars[$page_get]))
				$current_page = max(1, intval($wp->query_vars[$page_get]));
			else
				$current_page = 1;
				
			if ( $projectmanager->isSearch() )
				$datasets = $projectmanager->getSearchResults();
			else
				$datasets = $projectmanager->getDatasets( array( 'project_id' => $project_id, 'current_page' => $current_page, 'limit' => $results, 'orderby' => $orderby, 'order' => $order, 'random' => $random, 'meta_key' => intval($field_id), 'meta_value' => $field_value) );
			
			$title = '';
			if ( $projectmanager->isSearch() ) {
				$num_datasets = $projectmanager->getNumDatasets($projectmanager->getProjectID(), true);
				$title = "<h3 style='clear:both;'>".sprintf(__('Search: %d of %d', 'projectmanager'), count($datasets), $num_datasets)."</h3>";
			} elseif ( $cat_id ) {
				$title = "<h3 style='clear:both;'>".$projectmanager->getCatTitle($cat_id)."</h3>";
			}
			
			$pagination = ( $projectmanager->isSearch() ) ? '' : $projectmanager->getPageLinks($current_page, $page_get);
			
			$project->num_datasets = $projectmanager->getNumDatasets($projectmanager->getProjectID(), true);
			$project->gallery_num_cols = ( $project->gallery_num_cols == 0 ) ? 4 : $project->gallery_num_cols;
			$project->dataset_width = floor(100/$project->gallery_num_cols);
			$project->single = ( $single == 'true' ) ? true : false;
			$project->selections = ( $selections == 'true' ) ? true : false;
			
			$i = 0;
			foreach ( $datasets AS $dataset ) {
				$class = ( !isset($class) || "alternate" == $class ) ? '' : "alternate"; 
				
				$dataset->name = stripslashes($dataset->name);
				
				$url = get_permalink();
				$url = add_query_arg($show, $dataset->id, $url);
				//$url = add_query_arg('project_id', $project_id, $url);
				$url = ($projectmanager->isCategory()) ? add_query_arg('cat_id', $projectmanager->getCatID(), $url) : $url;
				if (!isset($_GET['order_'.$project->id]))
					$url = add_query_arg('order_'.$project->id, $projectmanager->getDatasetOrder(), $url);
				if (!isset($_GET['orderby_'.$project->id]))
					$url = add_query_arg('orderby_'.$project->id, $projectmanager->getDatasetOrderBy(), $url);
				
				foreach ( $matches = preg_grep("/cat_id_\d+/", array_keys($_GET)) AS $key )
					$url = add_query_arg($key, $_GET[$key], $url);
				foreach ( $matches = preg_grep("/paged_\d+/", array_keys($_GET)) AS $key )
					$url = add_query_arg($key, $_GET[$key], $url);
				foreach ( $matches = preg_grep("/orderby_\d+/", array_keys($_GET)) AS $key )
					$url = add_query_arg($key, $_GET[$key], $url);
				foreach ( $matches = preg_grep("/order_\d+/", array_keys($_GET)) AS $key )
					$url = add_query_arg($key, $_GET[$key], $url);
				foreach ( $matches = preg_grep("/show_\d+/", array_keys($_GET)) AS $key )
					$url = add_query_arg($key, $_GET[$key], $url);

				$datasets[$i]->class = $class;
				$datasets[$i]->URL = $url;
				if ($dataset->image == "") {
					$dataset->image = $project->default_image;
					$datasets[$i]->image = $dataset->image;
				}
				$datasets[$i]->thumbURL = $projectmanager->getFileURL('/thumb.'.$dataset->image);
				$datasets[$i]->nameURL = ($projectmanager->hasDetails($single)) ? '<a href="'.$url.'">'.$dataset->name.'</a>' : $dataset->name;
				
				$i++;
			}
		}
		
		$out = $this->loadTemplate( $template, array('project' => $project, 'dataset_id' => $dataset_id, 'datasets' => $datasets, 'title' => $title, 'pagination' => $pagination) );
		
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
	 * @param boolean $callback - checks if function is called via action hook
	 * @return string
	 */
	function displayDataset( $atts, $action = false )
	{
		global $projectmanager;
		
		extract(shortcode_atts(array(
			'id' => 0,
			'template' => '',
			'echo' => 0
		), $atts ));
		
		$id = intval($id);

		$projectmanager->setDatasetOrder();
		
		$dataset = $projectmanager->getDataset($id);
		if ($dataset->image == "") {
			$project = $projectmanager->getProject($dataset->project_id);
			$dataset->image = $project->default_image;
		}
		$dataset->imgURL = $projectmanager->getFileURL($dataset->image);
		$dataset->name = stripslashes($dataset->name);
				
		if ( $action ) {
			$url = get_permalink();
			$url = remove_query_arg('show_'.$dataset->project_id, $url);
			$url = add_query_arg('project_id', $dataset->project_id, $url);
			
			foreach ( $matches = preg_grep("/cat_id_\d+/", array_keys($_GET)) AS $key )
				$url = add_query_arg($key, $_GET[$key], $url);
			foreach ( $matches = preg_grep("/paged_\d+/", array_keys($_GET)) AS $key )
				$url = add_query_arg($key, $_GET[$key], $url);
			foreach ( $matches = preg_grep("/orderby_\d+/", array_keys($_GET)) AS $key )
				$url = add_query_arg($key, $_GET[$key], $url);
			foreach ( $matches = preg_grep("/order_\d+/", array_keys($_GET)) AS $key )
				$url = add_query_arg($key, $_GET[$key], $url);
			foreach ( $matches = preg_grep("/show_\d+/", array_keys($_GET)) AS $key )
				$url = add_query_arg($key, $_GET[$key], $url);
			
			$url = remove_query_arg('show_'.$dataset->project_id, $url);
			$url = remove_query_arg('paged_'.$dataset->project_id, $url);
			$url = add_query_arg('paged_'.$dataset->project_id, $projectmanager->getDatasetPage($id), $url);
			
			$url = ($projectmanager->isCategory()) ? add_query_arg('cat_id', $projectmanager->getCatID(), $url) : $url;
		} else {
			$url = false;
		}


				
		$filename = ( empty($template) ) ? 'dataset' : 'dataset-'.$template;
		$out = $this->loadTemplate( $filename, array('dataset' => $dataset, 'backurl' => $url) );

		if ( $echo )
			echo $out;
		
		return $out;
	}
	
	
	/**
	 * Function to display the number of datasets in given project
	 *
	 *	[projectmanager_num_datasets project_id="1"]
	 *
	 * - project_id is the ID of the project
	 * - text optional text
	 */
	function displayNumDatasets( $atts, $action = false )
	{
		global $projectmanager;
		
		extract(shortcode_atts(array(
			'project_id' => 0,
			'text' => '',
		), $atts ));
		
		$project_id = intval($project_id);
		$num_datasets = $projectmanager->getNumDatasets($project_id, true);
		
		if ($text == "")
			return "<span class='projectmanager_num_datasets'>".$num_datasets."</span>";
		else
			return "<div class='projectmanager_num_datasets'><p><span class='text'>". $text."</span> <span class='num_datasets'>".$num_datasets."</span></p></div>";
	}

	
	/**
	 * Function to display testimonials
	 *
	 *	[testimonials project_id="x" number= comment= country= city= ncol= title= sign_page_id= list_page_id= template=]
	 *
	 * - project_id is the ID of the project to display
	 * - number is the number of random datasets
	 * - comment, country, city are the formfield IDs for those dataset
	 * - ncol is the number of columns
	 * - sign_page_id is the page ID containing the petition signing form. Can be also an anker if formular is on the same page
	 * - list_page_id is the page ID containing a list of all supporters
	 * - template should be either empty or "intro"
	 *
	 * @param array $atts
	 * @return the content
	 */
	function displayTestimonials( $atts )
	{
		global $wp, $wpdb, $projectmanager;
		
		extract(shortcode_atts(array(
			'project_id' => 0,
			'number' => 6,
			'comment' => 0,
			'country' => 0,
			'city' => 0,
			'ncol' => 3,
			'sign_page_id' => 0,
			'list_page_id' => 0,
			'template' => '',
			'selections' => 'true',
		), $atts ));
		$project_id = intval($project_id);
		$number = intval($number);
		$comment_id = intval($comment);
		$country_id = intval($country);
		$ncol = intval($ncol);
		$city_id = intval($city);
		//$sign_page_id = intval($sign_page_id);
		$list_page_id = intval($list_page_id);
		
		$projectmanager->init($project_id);
		$project = $projectmanager->getCurrentProject();
		
		$page_get = "paged_".$project_id;
		if (isset($_GET['paged']) && isset($_GET['project_id']) && $_GET['project_id'] == $project_id)
			$current_page = intval($_GET['paged']);
		elseif (isset($wp->query_vars['paged']) && isset($_GET['project_id']) && $_GET['project_id'] == $project_id)
			$current_page = max(1, intval($wp->query_vars['paged']));
		elseif (isset($_GET[$page_get]))
			$current_page = intval($_GET[$page_get]);
		elseif (isset($wp->query_vars[$page_get]))
			$current_page = max(1, intval($wp->query_vars[$page_get]));
		else
			$current_page = 1;
			
		if (!empty($sign_page_id))
			$sign_petition_href = is_numeric($sign_page_id) ? get_permalink($sign_page_id) : htmlspecialchars($sign_page_id);
		else
			$sign_petition_href = "";
		
		if (!empty($list_page_id))
			$list_page_href = get_permalink($list_page_id);
		else
			$list_page_href = "";
		
		if ($template == "intro")
			$datasets = $projectmanager->getDatasets( array( 'project_id' => $project_id, 'limit' => $number, 'random' => true) );
		else
			$datasets = $projectmanager->getDatasets( array( 'project_id' => $project_id, 'current_page' => $current_page, 'limit' => true, 'orderby' => 'id', 'order' => 'DESC', 'random' => false) );
		
		$project->num_datasets = $projectmanager->getNumDatasets($projectmanager->getProjectID(), true);
		$project->selections = ( $selections == 'true' ) ? true : false;
		
		$pagination = $projectmanager->getPageLinks($current_page, $page_get);
		
		$single = false;
		
		$i = 0;
		foreach ( $datasets AS $dataset ) {
			$class = ( !isset($class) || "alternate" == $class ) ? '' : "alternate"; 
				
			$dataset->name = stripslashes($dataset->name);
			
			$comment = $wpdb->get_results($wpdb->prepare( "SELECT value FROM {$wpdb->projectmanager_datasetmeta} WHERE dataset_id = '%d' AND form_id = '%d'", intval($dataset->id), $comment_id));
			$dataset->comment = $comment[0]->value;
			
			// Trim comment in intro template
			if ($template == "intro") {
				$comment_field = $wpdb->get_results($wpdb->prepare( "SELECT options FROM {$wpdb->projectmanager_projectmeta} WHERE id = '%d'", $comment_id));
				$formfield_options = explode(";", $comment_field[0]->options);
				$match = array_values(preg_grep("/limit:/", $formfield_options));
				if (count($match) == 1) {
					$str_limit = explode(":", $match[0]);
					$str_limit = $str_limit[1];
				} else {
					$str_limit = 150;
				}
				
				if (strlen($dataset->comment) > $str_limit)
					$dataset->comment = substr($dataset->comment, 0, $str_limit)." ...";
			}
			
			$country = $wpdb->get_results($wpdb->prepare( "SELECT value FROM {$wpdb->projectmanager_datasetmeta} WHERE dataset_id = '%d' AND form_id = '%d'", intval($dataset->id), $country_id));
			$dataset->country = __($projectmanager->getCountryName($country[0]->value), 'projectmanager');
			$city = $wpdb->get_results($wpdb->prepare( "SELECT value FROM {$wpdb->projectmanager_datasetmeta} WHERE dataset_id = '%d' AND form_id = '%d'", intval($dataset->id), $city_id));
			$dataset->city = $city[0]->value;
				
			$datasets[$i]->class = $class;
			if ($dataset->image == "") {
				$dataset->image = $project->default_image;
				$datasets[$i]->image = $dataset->image;
			}
			$datasets[$i]->thumbURL = $projectmanager->getFileURL('/thumb.'.$dataset->image);
			$datasets[$i]->nameURL = ($projectmanager->hasDetails($single)) ? '<a href="'.$url.'">'.$dataset->name.'</a>' : $dataset->name;
				
			$i++;
		}
		
		if ($template == "")
			$template = "testimonials";
		else
			$template = "testimonials-".$template;
		
		$out = $this->loadTemplate( $template, array('project' => $project, 'datasets' => $datasets, 'pagination' => $pagination, 'sign_petition_href' => $sign_petition_href, 'list_page_href' => $list_page_href, 'ncol' => $ncol) );
		
		return $out;
	}
}
?>