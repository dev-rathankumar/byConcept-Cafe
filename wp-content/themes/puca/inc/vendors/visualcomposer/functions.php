<?php

/**
 * Custom parameters for visual composer
 */

if(!class_exists('Vc_Manager')) return;

if ( !function_exists('puca_tbay_custom_vc_params')) {
	function puca_tbay_custom_vc_params(){

		vc_add_param( 'vc_row', array(
		    "type" => "checkbox",
		    "heading" => esc_html__('Parallax', 'puca'),
		    "param_name" => "parallax",
		    "value" => array(
		        'Yes, please' => true
		    )
		));

		vc_add_param( 'vc_row', array(
		    "type" => "dropdown",
		    "heading" => esc_html__('Is Boxed', 'puca'),
		    "param_name" => "isfullwidth",
		    "value" => array(
		    	esc_html__('Full', 'puca') => '0',
					esc_html__('Boxed', 'puca') => '1',
				)

		));

        // add param for image elements

        vc_add_param( 'vc_single_image', array(
             "type" => "textarea",
             "heading" => esc_html__('Image Description', 'puca'),
             "param_name" => "description",
             "value" => "",
             'priority' 
        ));		

        // add param for image elements

        vc_add_param( 'vc_column', array(
            "type"          => "checkbox",
            "heading"       => esc_html__( 'Config in vertical menu?', 'puca' ),
            "description"   => esc_html__( 'Config in vertical menu', 'puca' ),
            "param_name"    => "config_vertical_menu",
            "value"         => array(
                esc_html__('Yes', 'puca') =>'yes' 
            ),
            'priority'  => 1,
        ));		

        vc_add_param( 'vc_column', array(
            "type"          => "checkbox",
            "heading"       => esc_html__( 'Full columns in vertical menu?', 'puca' ),
            "description"   => esc_html__( 'Full columns in vertical menu', 'puca' ),
            "param_name"    => "vertical_full",
            "value"         => array(
                                esc_html__('Yes', 'puca') =>'yes' ),
            'dependency'    => array(
                    'element'   => 'config_vertical_menu',
                    'value'     => 'yes'
            ),
            'priority'  => 2,
        ));

        vc_add_param( 'vc_column', array(
            "type"          => "checkbox",
            "heading"       => esc_html__( 'Hidden columns in vertical menu?', 'puca' ),
            "description"   => esc_html__( 'Show/hidden columns in vertical menu', 'puca' ),
            "param_name"    => "vertical_hidden",
            "value"         => array(
                                esc_html__('Yes', 'puca') =>'yes' ),
            'dependency'    => array(
                    'element'   => 'config_vertical_menu',
                    'value'     => 'yes'
            ),
            'priority'  => 3,
        ));    

	}
}
add_action( 'after_setup_theme', 'puca_tbay_custom_vc_params', 99 );
 

if ( function_exists('tbay_framework_add_param') ) {
	tbay_framework_add_param();
}


function puca_tbay_translate_column_width_to_span( $width ) {
	preg_match( '/(\d+)\/(\d+)/', $width, $matches );

	if ( ! empty( $matches ) ) {
		$part_x = (int) $matches[1];
		$part_y = (int) $matches[2];
		if ( $part_x > 0 && $part_y > 0 ) {
			$value = ceil( $part_x / $part_y * 12 );
			if ( $value > 0 && $value <= 12 ) {
				$width = 'vc_col-md-' . $value;
			}
		}
	}

	return $width;
}


// Add Simple Line font Icon
function puca_tbay_add_simple_line_icon_set_to_iconbox( ) {
	$param = WPBMap::getParam( 'vc_icon', 'type' );
	$param['value'][esc_html__( 'Simple Line', 'puca' )] = 'simpleline';
    $param['weight'] = 90;
	vc_update_shortcode_param( 'vc_icon', $param );
}
add_filter( 'init', 'puca_tbay_add_simple_line_icon_set_to_iconbox', 40 );
// Add font picker setting to icon box module when you select your font family from the dropdown
function puca_tbay_add_font_picker() {
	vc_add_param( 'vc_icon', array(
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'puca' ),
			'param_name' => 'icon_simpleline',
			'settings' => array(
				'emptyIcon' => true,
				'type' => 'simpleline',
				'iconsPerPage' => 400,
			),
            'value' => 'icon-user',
            'weight' => 80,
			'dependency' => array(
				'element' => 'type',
				'value' => 'simpleline',
			),
		)
	);
}
add_filter( 'vc_after_init', 'puca_tbay_add_font_picker', 40 );
// Add array of your fonts so they can be displayed in the font selector
function puca_tbay_icon_array() {
	return array(
				array('icon-user' 							=> 'user'),
        array('icon-people' 						=> 'people'),
        array('icon-user-female' 				=> 'user female'),
        array('icon-user-follow' 				=> 'user follow'),
        array('icon-user-following' 		=> 'user following'),
        array('icon-login' 							=> 'login'),
        array('icon-logout'			 				=> 'logout'),
        array('icon-emotsmile' 					=> 'emotsmile'),
        array('icon-phone' 							=> 'phone'),
        array('icon-call-end' 					=> 'call end'),
        array('icon-call-in' 						=> 'call in'),
        array('icon-call-out'	 					=> 'call out'),
        array('icon-map' 								=> 'call map'),
        array('icon-location-pin' 			=> 'location pin'),
        array('icon-direction' 					=> 'direction'),
        array('icon-directions' 				=> 'directions'),
        array('icon-compass' 						=> 'compass'),
        array('icon-layers' 						=> 'layers'),
        array('icon-list' 							=> 'list'),
        array('icon-options-vertical' 	=> 'options vertical'),
        array('icon-options' 						=> 'options'),
        array('icon-arrow-down' 				=> 'arrow down'),
        array('icon-arrow-left' 				=> 'arrow left'),
        array('icon-arrow-right' 				=> 'arrow right'),
        array('icon-arrow-up' 					=> 'arrow up'),
        array('icon-arrow-up-circle' 		=> 'arrow up circle'),
        array('icon-arrow-left-circle' 	=> 'arrow left circle'),
        array('icon-arrow-right-circle' => 'arrow right circle'),
        array('icon-arrow-down-circle' 	=> 'arrow down circle'),
        array('icon-check' 							=> 'check'),
        array('icon-clock' 							=> 'clock'),
        array('icon-plus' 							=> 'plus'),
        array('icon-minus' 							=> 'minus'),
        array('icon-close' 							=> 'close'),
        array('icon-event' 							=> 'event'),
        array('icon-exclamation' 				=> 'exclamation'),
        array('icon-organization' 			=> 'organization'),
        array('icon-trophy' 						=> 'trophy'),
        array('icon-screen-smartphone' 	=> 'screen smartphone'),
        array('icon-screen-desktop' 		=> 'screen desktop'),
        array('icon-plane' 							=> 'plane'),
        array('icon-notebook' 					=> 'notebook'),
        array('icon-mustache' 					=> 'mustache'),
        array('icon-mouse' 							=> 'mouse'),
        array('icon-magnet' 						=> 'magnet'),
        array('icon-energy' 						=> 'energy'),
        array('icon-disc' 							=> 'disc'),
        array('icon-cursor' 						=> 'cursor'),
        array('icon-cursor-move' 				=> 'cursor move'),
        array('icon-crop' 							=> 'crop'),
        array('icon-chemistry' 					=> 'chemistry'),
        array('icon-speedometer' 				=> 'speedometer'),
        array('icon-shield' 						=> 'shield'),
        array('icon-screen-tablet' 			=> 'screen tablet'),
        array('icon-magic-wand' 				=> 'magic wand'),
        array('icon-hourglass' 					=> 'hourglass'),
        array('icon-graduation' 				=> 'graduation'),
        array('icon-ghost' 							=> 'ghost'),
        array('icon-game-controller' 		=> 'game controller'),
        array('icon-fire' 							=> 'fire'),
        array('icon-eyeglass' 					=> 'eyeglass'),
        array('icon-envelope-open' 			=> 'envelope open'),
        array('icon-envelope-letter' 		=> 'envelope letter'),
        array('icon-bell' 							=> 'bell'),
        array('icon-badge' 							=> 'badge'),
        array('icon-anchor' 						=> 'anchor'),
        array('icon-wallet' 						=> 'wallet'),
        array('icon-vector' 						=> 'vector'),
        array('icon-speech' 						=> 'speech'),
        array('icon-puzzle' 						=> 'puzzle'),
        array('icon-printer' 						=> 'printer'),
        array('icon-present' 						=> 'present'),
        array('icon-playlist' 					=> 'playlist'),
        array('icon-pin' 								=> 'pin'),
        array('icon-picture' 						=> 'picture'),
        array('icon-handbag' 						=> 'handbag'),
        array('icon-globe-alt' 					=> 'globe alt'),
        array('icon-globe' 							=> 'globe'),
        array('icon-folder-alt' 				=> 'folder alt'),
        array('icon-folder' 						=> 'folder'),
        array('icon-film' 							=> 'film'),
        array('icon-feed' 							=> 'feed'),
        array('icon-drop' 							=> 'drop'),
        array('icon-drawer' 						=> 'drawer'),
        array('icon-docs' 							=> 'docs'),
        array('icon-doc' 								=> 'doc'),
        array('icon-diamond' 						=> 'diamond'),
        array('icon-cup' 								=> 'cup'),
        array('icon-calculator' 				=> 'calculator'),
        array('icon-bubbles' 						=> 'bubbles'),
        array('icon-briefcase' 					=> 'briefcase'),
        array('icon-book-open' 					=> 'book open'),
        array('icon-basket-loaded' 			=> 'basket loaded'),
        array('icon-basket' 						=> 'basket'),
        array('icon-bag' 								=> 'bag'),
        array('icon-action-undo' 				=> 'action undo'),
        array('icon-action-redo' 				=> 'action redo'),
        array('icon-wrench' 						=> 'wrench'),
        array('icon-umbrella' 					=> 'umbrella'),
        array('icon-trash' 							=> 'trash'),
        array('icon-tag' 								=> 'tag'),
        array('icon-support' 						=> 'support'),
        array('icon-frame' 							=> 'frame'),
        array('icon-size-fullscreen' 		=> 'size fullscreen'),
        array('icon-size-actual' 				=> 'size actual'),
        array('icon-shuffle'	 					=> 'shuffle'),
        array('icon-share-alt' 					=> 'share alt'),
        array('icon-share' 							=> 'share'),
        array('icon-rocket' 						=> 'rocket'),
        array('icon-question' 					=> 'question'),
        array('icon-pie-chart' 					=> 'pie chart'),
        array('icon-pencil' 						=> 'pencil'),
        array('icon-note' 							=> 'note'),
        array('icon-loop' 							=> 'loop'),
        array('icon-home' 							=> 'home'),
        array('icon-grid' 							=> 'grid'),
        array('icon-graph' 							=> 'graph'),
        array('icon-microphone' 				=> 'microphone'),
        array('icon-music-tone-alt' 		=> 'music tone alt'),
        array('icon-music-tone' 				=> 'music tone'),
        array('icon-earphones-alt' 			=> 'earphones alt'),
        array('icon-earphones' 					=> 'earphones'),
        array('icon-equalizer' 					=> 'equalizer'),
        array('icon-like' 							=> 'like'),
        array('icon-dislike' 						=> 'dislike'),
        array('icon-control-start' 			=> 'control start'),
        array('icon-control-rewind' 		=> 'control rewind'),
        array('icon-control-play' 			=> 'control play'),
        array('icon-control-pause' 			=> 'control pause'),
        array('icon-control-forward' 		=> 'control forward'),
        array('icon-control-end' 				=> 'control end'),
        array('icon-control-end' 				=> 'control end'),
        array('icon-volume-1' 					=> 'volume 1'),
        array('icon-volume-2' 					=> 'volume 2'),
        array('icon-volume-off' 				=> 'volume off'),
        array('icon-calendar' 					=> 'calendar'),
        array('icon-bulb' 							=> 'bulb'),
        array('icon-chart' 							=> 'chart'),
        array('icon-ban' 								=> 'ban'),
        array('icon-bubble' 						=> 'bubble'),
        array('icon-camrecorder' 				=> 'camrecorder'),
        array('icon-camera' 						=> 'camera'),
        array('icon-cloud-download'			=> 'cloud download'),
        array('icon-cloud-upload' 			=> 'cloud upload'),
        array('icon-envelope'						=> 'envelope'),
        array('icon-eye' 								=> 'eye'),
        array('icon-flag' 							=> 'flag'),
        array('icon-heart' 							=> 'heart'),
        array('icon-info' 							=> 'info'),
        array('icon-key' 								=> 'key'),
        array('icon-link'							 	=> 'link'),
        array('icon-lock' 							=> 'lock'),
        array('icon-lock-open' 					=> 'lock open'),
        array('icon-magnifier' 					=> 'magnifier'),
        array('icon-magnifier-add' 			=> 'magnifier add'),
        array('icon-magnifier-remove' 	=> 'magnifier remove'),
        array('icon-paper-clip' 				=> 'paper clip'),
        array('icon-paper-plane' 				=> 'paper plane'),
        array('icon-power' 							=> 'power'),
        array('icon-refresh' 						=> 'refresh'),
        array('icon-reload' 						=> 'reload'),
        array('icon-settings' 					=> 'settings'),
        array('icon-symbol-female' 			=> 'symbol female'),
        array('icon-symbol-male' 				=> 'symbol male'),
        array('icon-target' 						=> 'target'),
        array('icon-credit-card' 				=> 'credit card'),
        array('icon-paypal' 						=> 'paypal'),
        array('icon-social-tumblr' 			=> 'social tumblr'),
        array('icon-social-twitter' 		=> 'social twitter'),
        array('icon-social-facebook' 		=> 'social facebook'),
        array('icon-social-instagram' 	=> 'social instagram'),
        array('icon-social-linkedin' 		=> 'social linkedin'),
        array('icon-social-pinterest' 	=> 'social pinterest'),
        array('icon-social-github' 			=> 'social github'),
        array('icon-social-google' 			=> 'social google'),
        array('icon-social-reddit' 			=> 'social reddit'),
        array('icon-social-skype' 			=> 'social skype'),
        array('icon-social-skype' 			=> 'social skype'),
        array('icon-social-behance' 		=> 'social behance'),
        array('icon-social-foursqare' 	=> 'social foursqare'),
        array('icon-social-soundcloud' 	=> 'social soundcloud'),
        array('icon-social-stumbleupon' => 'social stumbleupon'),
        array('icon-social-youtube' 		=> 'social youtube'),
        array('icon-social-vkontakte' 	=> 'social vkontakte'),
        array('icon-social-steam' 			=> 'social steam'),
	);
}
add_filter( 'vc_iconpicker-type-simpleline', 'puca_tbay_icon_array' );
/**
 * Register Backend and Frontend CSS Styles
 */
add_action( 'vc_base_register_front_css', 'puca_tbay_vc_iconpicker_base_register_css' );
add_action( 'vc_base_register_admin_css', 'puca_tbay_vc_iconpicker_base_register_css' );
function puca_tbay_vc_iconpicker_base_register_css(){
    wp_register_style( 'simple-line-icons', PUCA_STYLES . '/simple-line-icons.css', array(), '2.4.0' );
}
/**
 * Enqueue Backend and Frontend CSS Styles
 */
add_action( 'vc_backend_editor_enqueue_js_css', 'puca_tbay_vc_iconpicker_editor_jscss' );
add_action( 'vc_frontend_editor_enqueue_js_css', 'puca_tbay_vc_iconpicker_editor_jscss' );
function puca_tbay_vc_iconpicker_editor_jscss(){
    wp_enqueue_style( 'simple-line-icons' );
    wp_deregister_style( 'font-awesome' );
    wp_enqueue_style( 'font-awesome', PUCA_STYLES . '/font-awesome.css', array(), '4.7.0' );
}
/**
 * Enqueue CSS in Frontend when it's used
 */
add_action('vc_enqueue_font_icon_element', 'puca_tbay_enqueue_font_icomoon');
function puca_tbay_enqueue_font_icomoon($font){
    switch ( $font ) {
        case 'simpleline': wp_enqueue_style( 'simple-line-icons' );
    }
}