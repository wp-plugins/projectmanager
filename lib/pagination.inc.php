<?php
/**
 * This file contains a simple class to create a multiple pages navigation
 *
 * PHP Versions 4+
 *
 * LICENSE: This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package	pagination
 * @author	Kolja Schleich (kolja.schleich@googlemail.com)
 * @copyright	2007-2008 Kolja Schleich
 * @license	http://www.gnu.org/licenses/gpl.html
 */

if (!class_exists("Pagination")) {
class Pagination
{
	/**
	* Current page
	*
	* @var int
	*/
	var $page;
	
	
	/**
	* Array of GET variables to unset
	*
	* @var array
	*/
	var $get_unset = array( 'paging' );
	
	
	/**
	* Number of items per page
	*
	* @var int
	*/
	var $per_page;
	
	
	/**
	* Total number of items
	*
	* @var int
	*/
	var $num_items;
	
	
	/**
	* Set number of objects per page, total number of items and initialize $_GET variables to unset
	*
	* @param int $per_page number of objects displayed on single page
	* @param int $num_items number of items
	* @param array $get_unset array of $_GET variables to be unsetted
	* @return void
	*/
	function __construct( $per_page, $num_items, $get_unset=false )
	{
		$this->page = isset( $_GET['paging']) ? (int)$_GET['paging'] : 1;
		$this->setPerPage( $per_page );
		$this->setNumItems( $num_items );
		
		// Create Array for unsetting $_GET Variables
		if ( $get_unset ) {
			if ( is_array( $get_unset ) ) {
				foreach ( $get_unset AS $unset_item )
					array_push( $this->get_unset, $unset_item );
			} else {
				array_push( $get_unset, $this->get_unset );
			}
		}
		return;
	}
	function Pagination( $per_page, $num_items, $get_unset=false )
	{
		$this->__construct( $per_page, $num_items, $get_unset );
	}
	
	
	/**
	* gets current page
	*
	* @param none
	* @return int current page
	*/
	function getPage()
	{
		return $this->page;
	}
	
	
	/**
	* sets number of obejcts per page
	*
	* @param int $per_page number of objects to be displayed on every page
	* @return void
	*/
	function setPerPage( $per_page )
	{
		$this->per_page = $per_page;
		return;
	}
	
	
	/**
	* saves number of items
	*
	* @param int $num_items number of items
	* @return void
	*/
	function setNumItems( $num_items )
	{
		$this->num_items = $num_items;
		return;
	}
	
	
	/**
	* creates url for links in pagination
	*
	* @param none
	* @return string
	*/
	function createURL()
	{
		// create url for links
		$get = $_GET;
		foreach ( $this->get_unset AS $var ) 
			unset( $get[$var] );
			
		$url = '?';
		foreach ( $get as $key => $value )
			$url .= "$key=".urlencode( $value )."&";

		$out = htmlspecialchars( $url );
		return $out;
	}
	
	
	/**
	* returns pagination as paragraph
	*
	* @param none
	* @return string
	*/
	function get()
	{
		$page = $this->page;
	
		$out = "\n\n<p class='pagination'>";
		
		// determine number of pages
		$num_pages = ( 0 == $this->per_page ) ? 1 : ceil( $this->num_items/$this->per_page );
	
		// only create pagination if number of pages is more than 1
		if( $num_pages > 1 ) {
			// create link to previous page if current page is not the first page
			if ( $page > 1 )
				$out .= '<a href='.$this->createURL().'paging='.($page-1).'>&lt;&lt; '.__( 'Previous', 'prjctmngr' ).'</a>';
				
			// links for all pages
			$i = 1;
			$break = false;
			while ( 1 ) {
				if( $i == $page )
					$out .= '<span class="current">'.$i.'</span>';
				else
					$out .= ' <span><a href="'.$this->createURL().'paging='.$i.'">'.$i.'</a></span> ';
					
				if ( $break )
					break;
	
				if ( $num_pages > 10 ) {
					$i += ( $i != $page ? ceil( abs( $page - $i )/4 ) : 1 );
					if ( $i >= $num_pages ) {
						$i = $num_pages;
						$break = true;
					}
				} else {
					$i++;
					if ( $i >= $num_pages ) {
						$i = $num_pages;
						$break = true;
					}
				}
			}
		
			// create link to next page if current page is not last page
			if ( $page < $num_pages )
				$out .= '<a href="'.$this->createURL().'paging='.($page+1).'">'.__( 'Next', 'prjctmngr' ).' &gt;&gt;</a>';
		}
	
		return $out .= "</p>\n\n";
	}
}
}
?>