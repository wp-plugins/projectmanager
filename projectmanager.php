<?php
/*
Plugin Name: ProjectManager
Description: This Plugin can be used to manage several different types of projects with redundant data. This could be athlet portraits, DVD database, architect projects. You can define different form field types and groups to sort your project entries.
Plugin URI: http://wordpress.org/extend/plugins/projectmanager/
Version: 1.7
Author: Kolja Schleich


Copyright 2007-2008  Kolja Schleich  (email : kolja.schleich@googlemail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Loading class for the WordPress plugin ProjectManager
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright 	Copyright 2009
*/

class ProjectManagerLoader
{
	/**
	 * plugin versino
	 *
	 * @var string
	 */
	 var $version = '1.7';
	 
	 
	 /**
	  * database version
	  *
	  * @var string
	  */
	 var $dbversion = '1.7';
	 
	 
	 /**
	  * constructor
	  *
	  * @param none
	  * @return void
	  */
	 function __construct()
	 {
	 	global $projectmanager;
		
		// Load language file
		$this->loadTextdomain();

		$this->defineConstants();
		$this->defineTables();
		$this->loadOptions();
		$this->loadLibraries();

		register_activation_hook(__FILE__, array(&$this, 'activate') );
		
		if (function_exists('register_uninstall_hook'))
			register_uninstall_hook(__FILE__, array(&$this, 'uninstall'));

		$widget = new ProjectManagerWidget();
		add_action( 'widgets_init', array(&$widget, 'register') );
		// Start this plugin once all other plugins are fully loaded
		add_action( 'plugins_loaded', array(&$this, 'initialize') );
		
		$this->project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : false;
		$projectmanager = new ProjectManager($this->project_id);
		
		add_action( 'show_user_profile', array(&$this->adminPanel, 'profileHook') );
		add_action( 'profile_update', array(&$this->adminPanel, 'updateProfile') );
		
		// Export datasets
		if ( isset($_POST['projectmanager_export']) )
			$this->adminPanel->exportDatasets($_POST['project_id']);
	}
	function ProjectManagerLoader()
	{
		$this->__construct();
	}
	
	
	/**
	 * initialize plugin
	 *
	 * @param none
	 * @return void
	 */
	function initialize()
	{
		// Add the script and style files
		add_action('wp_head', array(&$this, 'loadScripts') );
		add_action('wp_print_styles', array(&$this, 'loadStyles') );

		// Add TinyMCE Button
		add_action( 'init', array(&$this, 'addTinyMCEButton') );
		add_filter( 'tiny_mce_version', array(&$this, 'changeTinyMCEVersion') );
		
		// Ajax Actions
		add_action( 'wp_ajax_projectmanager_save_name', 'projectmanager_save_name' );
		add_action( 'wp_ajax_projectmanager_save_categories', 'projectmanager_save_categories' );
		add_action( 'wp_ajax_projectmanager_save_form_field_data', 'projectmanager_save_form_field_data' );
		add_action( 'wp_ajax_projectmanager_show_category_selection', 'projectmanager_show_category_selection' );
		add_action( 'wp_ajax_projectmanager_save_form_field_options', 'projectmanager_save_form_field_options' );
	}
	
	
	/**
	 * define constants
	 *
	 * @param none
	 * @return void
	 */
	function defineConstants()
	{
		if ( !defined( 'WP_CONTENT_URL' ) )
			define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
		if ( !defined( 'WP_PLUGIN_URL' ) )
			define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
		if ( !defined( 'WP_CONTENT_DIR' ) )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ( !defined( 'WP_PLUGIN_DIR' ) )
			define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
			
		define( 'PROJECTMANAGER_VERSION', $this->version );
		define( 'PROJECTMANAGER_DBVERSION', $this->dbversion );
		define( 'PROJECTMANAGER_URL', WP_PLUGIN_URL.'/projectmanager' );
		define( 'PROJECTMANAGER_PATH', WP_PLUGIN_DIR.'/projectmanager' );
	}
	
	
	/**
	 * define database tables
	 *
	 * @param none
	 * @return void
	 */
	function defineTables()
	{
		global $wpdb;
		$wpdb->projectmanager_projects = $wpdb->prefix . 'projectmanager_projects';
		$wpdb->projectmanager_projectmeta = $wpdb->prefix . 'projectmanager_projectmeta';
		$wpdb->projectmanager_dataset = $wpdb->prefix . 'projectmanager_dataset';
		$wpdb->projectmanager_datasetmeta = $wpdb->prefix . 'projectmanager_datasetmeta';
	}
	
	
	/**
	 * load libraries
	 *
	 * @param none
	 * @return void
	 */
	function loadLibraries()
	{
		// Global libraries
		require_once (dirname (__FILE__) . '/lib/core.php');
		require_once (dirname (__FILE__) . '/lib/widget.php');
		require_once (dirname (__FILE__) . '/functions.php');
		
		if ( is_admin() ) {
			require_once (dirname (__FILE__) . '/lib/image.php');
			require_once (dirname (__FILE__) . '/admin/admin.php');	
			$this->adminPanel = new ProjectManagerAdminPanel($this->project_id);
		} else {
			require_once (dirname (__FILE__) . '/lib/shortcodes.php');
			$this->shortcodes = new ProjectManagerShortcodes();
		}
	}
	
	
	/**
	 * load options
	 *
	 * @param none
	 * @return void
	 */
	function loadOptions()
	{
		$this->options = get_option('projectmanager');
	}
	
	
	/**
	 * get options
	 *
	 * @param none
	 * @return void
	 */
	function getOptions()
	{
		return $this->options;
	}
	
	
	/**
	 * load textdomain
	 *
	 * @param none
	 * @return void
	 */
	function loadTextdomain()
	{
		load_plugin_textdomain( 'projectmanager', false, 'projectmanager/languages' );
	}
	
	
	/**
	 * load scripts
	 *
	 * @param none
	 * @return void
	 */
	function loadScripts()
	{
		$options = get_option( 'projectmanager_widget' );
		
		wp_register_script( 'jquery_slideshow', PROJECTMANAGER_URL.'/js/jquery.aslideshow.js', array('jquery'), '0.5.3' );
		wp_print_scripts( 'jquery_slideshow' );
		
		foreach ( $options AS $widget_id => $opts ) {
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
	
	
	/**
	 * load styles
	 *
	 * @param none
	 * @return void
	 */
	function loadStyles()
	{
		$options = get_option('projectmanager');
		
		wp_enqueue_style('projectmanager', PROJECTMANAGER_URL . "/style.css", false, '1.0', 'screen');
		
		echo "\n<style type='text/css'>";
		echo "\n\ttable.projectmanager th { background-color: ".$options['colors']['headers']." }";
		echo "\n\ttable.projectmanager tr { background-color: ".$options['colors']['rows'][1]." }";
		echo "\n\ttable.projectmanager tr.alternate { background-color: ".$options['colors']['rows'][0]." }";
		echo "\n\tfieldset.dataset { border-color: ".$options['colors']['headers']." }";
		echo "\n</style>";
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
		$plugin_array['ProjectManager'] = PROJECTMANAGER_URL.'/tinymce/editor_plugin.js';
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
	 * Activate Plugin
	 *
	 * @param none
	 */
	function activate()
	{
		global $wpdb;
		include_once( ABSPATH.'/wp-admin/includes/upgrade.php' );
		
		$options = array();
		$options['version'] = $this->version;
		$options['dbversion'] = $this->dbversion;
		
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
		$role->add_cap('project_user_profile');
		
		$role = get_role('editor');
		$role->add_cap('manage_projects');
		$role->add_cap('project_user_profile');
		
		/*
		* Add widget options
		*/
		if ( function_exists('register_sidebar_widget') )
			add_option( 'projectmanager_widget', array(), 'ProjectManager Widget Options', 'yes' );
	}
	
	/**
	 * uninstall Plugin
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
	
	
	/**
	 * get admin panel object
	 *
	 * @param none
	 * @return object
	 */
	function getAdminPanel()
	{
		if ( $this->adminPanel )
			return $this->adminPanel;
		
		return false;
	}
}

// Run the Plugin
global $projectmanager_loader;
$projectmanager_loader = new ProjectManagerLoader();
?>