<?php
/**
 * Create placeholder
 *
 * @package    tbay-framework
 * @author     Team Thembays <tbaythemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Tbay Themer
 */

class Tbay_Create_Placeholder {
	
	public static function create($size = array()) {
		if (!empty($size)) {
			$img_path = get_template_directory() . '/images/placeholder/' . $size[0] . 'x' . $size[1] . '.png';
			if ( !file_exists($img_path) ) {
				self::create_image( $size[0], $size[1], 'DDDDDD', $img_path );
			}
			return get_template_directory_uri() . '/images/placeholder/' . $size[0] . 'x' . $size[1] . '.png';
		}
		return '';
	}

	public static function create_image($width, $height, $bg_color, $folder )
	{
	    //Define the text to show
	    $text = "$width X $height";

	    //Create the image resource 
	    $image = ImageCreate($width, $height);  

	    //We are making two colors one for BackGround and one for ForGround
		$bg_color = ImageColorAllocate($image, base_convert(substr($bg_color, 0, 2), 16, 10), 
											   base_convert(substr($bg_color, 2, 2), 16, 10), 
											   base_convert(substr($bg_color, 4, 2), 16, 10));
	    //Fill the background color 
	    ImageFill($image, 0, 0, $bg_color); 
	    
	    //Tell the browser what kind of file is come in 
	   // header("Content-Type: image/png"); 
		if( preg_match("#.png#", $folder)){
			 //Output the newly created image in png format 
	    	imagepng($image, $folder );
		}
	   	
	   	if( preg_match("#.jpg#", $folder)){
			 //Output the newly created image in png format 
	    	imagejpeg($image, $folder );
		}
	   
	    //Free up resources
	    ImageDestroy($image);
	}

}