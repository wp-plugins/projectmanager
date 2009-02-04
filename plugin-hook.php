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

// Actions
add_action( 'admin_menu', array(&$projectmanager, 'addAdminMenu') );

// Ajax Actions
add_action( 'wp_ajax_projectmanager_save_name', 'projectmanager_save_name' );
add_action( 'wp_ajax_projectmanager_save_categories', 'projectmanager_save_categories' );
add_action( 'wp_ajax_projectmanager_save_form_field_data', 'projectmanager_save_form_field_data' );
add_action( 'wp_ajax_projectmanager_show_category_selection', 'projectmanager_show_category_selection' );
add_action( 'wp_ajax_projectmanager_save_form_field_options', 'projectmanager_save_form_field_options' );




?>
