<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SZBD_Shortcode {

	protected static $_instance = null;

	protected $is_active = false;

	public static $shortcode_order;

	public function __construct() {


		add_shortcode( 'szbd', array( $this, 'shortcode' ) );

	}

	public static function instance() {
		if ( is_null( self::$_instance )  ){
			self::$_instance = new self();
		}

		return self::$_instance;
	}


public function shortcode( $atts ) {

		$this->is_active = true;

		$options = shortcode_atts( array(

			'ids'   => null,
			'title' =>	null,
			'color' =>	null,
			'interactive' =>	null,


		), $atts );

$ids = empty($options['ids']) ? null : $options['ids'] ;

$title = empty($options['title']) ? '' : $options['title'] ;

$color = empty($options['color']) ? '' : $options['color'] ;

$interactive = empty($options['interactive']) ? 'false' : $options['interactive'] ;


$ids = explode(',', $ids);



	 $this->szbdzones_js( $ids ,$title, $color, $interactive);

		ob_start();

		include SZBD_PREM_PLUGINDIRPATH . '/includes/shortcode-map-template.php';

		$content = ob_get_clean();

		return ($content);
	}


 public function szbdzones_js( $ids ,$title, $color, $interactive)
    {

		$maps= array();
		foreach($ids as $id){
			$array_latlng = array();

			$post   = get_post( $id );
			$post_type = get_post_type( $post );

			if($post_type !== 'szbdzones'){
	continue ;
}
			$post_id = $post->ID;
    $settings     = get_post_meta( $post_id, 'szbdzones_metakey', true );
    $lat          = isset( $settings['lat'] ) ? $settings['lat'] : '';
    $lng          = isset( $settings['lng'] ) ? $settings['lng'] : '';
   $zoom         = isset( $settings['zoom'] ) ? $settings['zoom'] : '1.3';
    $geo_coordinates_array = is_array( $settings ) && is_array( $settings['geo_coordinates'] ) ? $settings['geo_coordinates'] : array();
    if ( count( $geo_coordinates_array ) > 0 )
      {
      foreach ( $geo_coordinates_array as $geo_coordinates )
        {
        if ( $geo_coordinates[0] != '' && $geo_coordinates[1] != '' )
          $array_latlng[] = array(
             $geo_coordinates[0],
            $geo_coordinates[1]
          );
        }
      }
    else
      {
      $array_latlng = array();
      }
	  $maps[] =   array(
       'lat' => $lat,
      'lng' => $lng,
	   'array_latlng' => $array_latlng,
	    'zoom' => intval( $zoom ),
	   );

	}
    $args = array(
       'maps' => $maps,
	   'color' => $color,
	  'title' => $title,
	   'interactive' => $interactive == 'true' ? 1 : 0,
    );

    wp_localize_script( 'szbd-script-short', 'szbd_map', $args );
    //    }
    }


}
