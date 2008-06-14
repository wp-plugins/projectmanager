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
 * @package	image
 * @author	Kolja Schleich (kolja.schleich@googlemail.com)
 * @copyright	2008 Kolja Schleich
 * @license	http://www.gnu.org/licenses/gpl.html
 */

if ( !class_exists("Image") ) {
class Image
{
	/**
	* supported image types
	*
	* @var array
	*/
	var $supported_image_types = array( "jpg", "jpeg", "png", "gif" );
		
	/**
	 *
	 */
	function __construct()
	{
	}
	function Image()
	{
		$this->__construct();
	}
	
	
	/**
	 * get supported image types
	 *
	 * @param none
	 * @return array
	 */
	function getSupportedImageTypes()
	{
		return $this->supported_image_types;
	}
	
	
	/**
	 * gets image type
	 *
	 * @param string $filename
	 * @return string
	 */
	function getImageType( $filename )
	{
		$file_info = pathinfo($filename);
		return strtolower($file_info['extension']);
	}
		
		
	/**
	 * checks if image type is supported
	 *
	 * @param string $filename
	 * @return boolean
	 */
	function imageTypeIsSupported( $filename )
	{
		if ( in_array($this->getImageType($filename), $this->supported_image_types) )
			return true;
		else
			return false;
	}
		
		
	/**
	 * creates path to image file
	 * 
	 * @param string $path
	 * @param string $filename
	 * @return string
	 */
	function makeFilename( $path, $filename )
	{
		if ( substr($path, -1) == '/' )
			return $path.$filename;
		else
			return $path.'/'.$filename;
	}
		
		
	/**
	 * saves image to server
	 *
	 * @param object $image_res
	 * @param string $path
	 * @param string $filename
	 * @return void
	 */
	function save( $image_res, $path, $filename )
	{
		$image_file_type = $this->getImageType($filename);
		if ( "gif" == $image_file_type )
			imagegif($image_res, $this->makeFilename($path, $filename));
		elseif ( "png" == $image_file_type )
			imagepng($image_res, $this->makeFilename($path, $filename));
		elseif ( "jpg" == $image_file_type || "jpeg" == $image_file_type )
			imagejpeg($image_res, $this->makeFilename($path, $filename, 100));
		
		return;
	}
		
		
	/**
	 * opens image file
	 *
	 * @param string $path
	 * @param string $filename
	 * @return object
	 */
	function open( $path, $filename )
	{
		$image_file_type = $this->getImageType($filename);
		if ( "gif" == $image_file_type )
			return imagecreatefromgif($this->makeFilename($path, $filename));
		elseif ( "png" == $image_file_type )
			return imagecreatefrompng($this->makeFilename($path, $filename));
		elseif ( "jpg" == $image_file_type || "jpeg" == $image_file_type )
			return imagecreatefromjpeg($this->makeFilename($path, $filename));
	}
		
		
	/**
	 * creates new Image
	 *
	 * @param int $width
	 * @param int $height
	 * @return object
	 */
	function create( $width, $height )
	{
		if ( function_exists("imagecreatetruecolor") )
			return imagecreatetruecolor($width, $height);
		else
			return imagecreate($width,$height);
	}
		
		
	/**
	 * resizes image and stores it on the webserver
	 *
	 * @param string $img_src source image file
	 * @param int $max_width maximal width
	 * @param int $max_height maximal height
	 * @param string $dir_src source directory
	 * @param string $dir_dest destination directory
	 * @param boolean $fixed_height
	 * @return boolean
	 */			
	function resize( $img_src, $max_width, $max_height, $dir_src, $dir_dest, $fixed_height )
	{
		if ( file_exists($dir_src.$img_src) ) {
			$image = $this->open( $dir_src, $img_src );
			list( $src_width, $src_height) = getimagesize($dir_src.$img_src);
				
			if ( $src_width > $max_width OR $src_height > $max_height ) {
				if ( $fixed_height ) {
					$new_image_height = $max_width;
					$new_image_width = $src_width * $max_height / $src_height;
				} else {
					if( $src_width >= $src_height ) {
						$new_image_width = $max_width;
						$new_image_height = $src_height * $max_width / $src_width;
					}
					if( $src_width < $src_height ) {
						$new_image_height = $max_width;
						$new_image_width = $src_width * $max_height / $src_height;
					}
				}
				
				$new_image = $this->create( $new_image_width, $new_image_height );
				if ( function_exists("imagecreatetruecolor") ) {
					if ( function_exists("imagecopyresampled") )
						imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_image_width,$new_image_height, $src_width, $src_height);
					else
						imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_image_width,$new_image_height, $src_width, $src_height);
				} else {
					imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_image_width,$new_image_height, $src_width, $src_height);
				}
			} else {
				$new_image = $image;
			}
			$this->save($new_image, $dir_dest, $img_src);
						
			return true;
		}
		
		return false;
	}
	
	
	/**
	* creates thumbnails for each image in given source directory
	*
	* @param string $dir_src source directory of images
	* @param string $dir_thumbs directory where thumbnails are stored
	* @param int $max_width
	* @param int $max_height
	* @param boolean $fixed_height
	* @return boolean
	*/
	function thumbnails( $dir_src, $dir_thumbs, $max_width, $max_height, $fixed_height = false )
	{
		if ( !file_exists( ABSPATH . 'wp-content/' . $dir_thumbs) )
			mkdir( ABSPATH . 'wp-content/' . $dir_thumbs );
	
		// open pics directory
		$dir = opendir( ABSPATH . 'wp-content/' . $dir_src);
	
		// create array in which the data is stored later
		$linkl = array();
		while( $file = readdir($dir) ) {
			if( "." != $file && ".." != $file && $file != $_SERVER['PHP_SELF'] && !is_dir(ABSPATH . 'wp-content/' . $dir_src.$file) )
				array_push($linkl, "$file");
		}
	
		// sort array
		sort ($linkl);
	
		foreach( $linkl as $key => $value ) {
			// check if a tumbnail already exists, if not create it
			if( !file_exists( ABSPATH . 'wp-content/' . $dir_thumbs."/".$value) )
				$this->resize( $value, $max_width, $max_height, ABSPATH . 'wp-content/' . $dir_src, ABSPATH . 'wp-content/' . $dir_thumbs, $fixed_height );
		}
					
		// close image directory
		closedir($dir);
	}
}
}