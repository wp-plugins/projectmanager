<?php
/*
Plugin Name: ProjectManager
Description: This Plugin can be used to manage several different types of projects with redundant data. This could be athlet portraits, DVD database, architect projects. You can define different form field types and groups to sort your project entries.
Plugin URI: http://wordpress.org/extend/plugins/projectmanager/
Version: 1.0.2
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
define( 'PROJECTMANAGER_VERSION', '1.0.2' );

include_once( 'projectmanager.php' );
include_once( 'pagination.php' );
include_once( 'image.php' );
include_once( 'functions.php' );

if ( file_exists('my-hacks.php') )
	include_once( 'my-hacks.php' );
	
$projectmanager = new WP_ProjectManager();

// Load textdomain for translation
load_plugin_textdomain( 'projectmanager', $path = PLUGINDIR.'/projectmanager' );
	
// Actions
add_action( 'admin_head', array(&$projectmanager, 'addHeaderCode') );
add_action( 'wp_head', array(&$projectmanager, 'addHeaderCode') );
add_action( 'activate_projectmanager/plugin-hook.php', array(&$projectmanager, 'init') );
add_action( 'admin_menu', array(&$projectmanager, 'addAdminMenu') );
add_action( 'plugins_loaded', array(&$projectmanager, 'initWidget') );
	
// Filters
add_filter( 'the_content', array(&$projectmanager, 'printProject') );

// TinyMCE
if ( strpos($wp_version, '2.5') === 0 && is_admin() && isset( $_GET['page'] ) && substr( $_GET['page'], 0, 14 ) == 'projectmanager' )
	add_filter( 'tiny_mce_before_init', array( &$projectmanager, 'tinyMceInit' ) );
	
add_filter( 'mce_buttons', array( &$projectmanager, 'tinyMceButtons' ) );
add_filter( 'mce_buttons_2', array( &$projectmanager, 'tinyMceButtons2' ) );

// Uninstall Plugin
if ( isset($_GET['projectmanager']) AND 'uninstall' == $_GET['projectmanager'] AND ( isset($_GET['delete_plugin']) AND 1 == $_GET['delete_plugin'] ) )
	$projectmanager->uninstall();
?>
