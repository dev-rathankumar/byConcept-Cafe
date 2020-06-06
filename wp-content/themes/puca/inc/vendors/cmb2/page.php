<?php

if ( !function_exists( 'puca_tbay_page_metaboxes' ) ) {
	function puca_tbay_page_metaboxes(array $metaboxes) {
		global $wp_registered_sidebars;
        $sidebars = array();

        if ( !empty($wp_registered_sidebars) ) {
            foreach ($wp_registered_sidebars as $sidebar) {
                $sidebars[$sidebar['id']] = $sidebar['name'];
            }
        }

        if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ){
            $cart_position_layout = array(
                                    'top'        => esc_html__('Top', 'puca'),
                                    'left'       => esc_html__('Left', 'puca'),
                                    'right'      => esc_html__('Right', 'puca'),
                                    'bottom'     => esc_html__('Bottom', 'puca'),
                                    'popup'      => esc_html__('Popup', 'puca'),
                                    'no-popup'   => esc_html__('None Popup', 'puca')
            );

            $cart_position = array_merge( array('global' => esc_html__( 'Global Setting', 'puca' )), $cart_position_layout );
        }

        $headers = array_merge( array('global' => esc_html__( 'Global Setting', 'puca' )), puca_tbay_get_header_layouts() );
        $footers = array_merge( array('global' => esc_html__( 'Global Setting', 'puca' )), puca_tbay_get_footer_layouts() );


		$prefix = 'tbay_page_';
	    $fields = array(
			array(
				'name' => esc_html__( 'Select Layout', 'puca' ),
				'id'   => $prefix.'layout',
				'type' => 'select',
				'options' => array(
					'main' => esc_html__('Main Content Only', 'puca'),
					'left-main' => esc_html__('Left Sidebar - Main Content', 'puca'),
					'main-right' => esc_html__('Main Content - Right Sidebar', 'puca'),
					'left-main-right' => esc_html__('Left Sidebar - Main Content - Right Sidebar', 'puca')
				)
			),
            array(
                'id' => $prefix.'left_sidebar',
                'type' => 'select',
                'name' => esc_html__('Left Sidebar', 'puca'),
                'options' => $sidebars
            ),
            array(
                'id' => $prefix.'right_sidebar',
                'type' => 'select',
                'name' => esc_html__('Right Sidebar', 'puca'),
                'options' => $sidebars
            ),
            array(
                'id' => $prefix.'show_breadcrumb',
                'type' => 'select',
                'name' => esc_html__('Show Breadcrumb?', 'puca'),
                'options' => array(
                    'no' => esc_html__('No', 'puca'),
                    'yes' => esc_html__('Yes', 'puca')
                ),
                'default' => 'yes',
            ),
            array(
                'name' => esc_html__( 'Select Breadcrumbs Layout', 'puca' ),
                'id'   => $prefix.'breadcrumbs_layout',
                'type' => 'select',
                'options' => array(
                    'image' => esc_html__('Background Image', 'puca'),
                    'color' => esc_html__('Background color', 'puca'),
                    'text' => esc_html__('Just text', 'puca')
                ),
                'default' => 'color',
            ),
            array(
                'id' => $prefix.'breadcrumb_color',
                'type' => 'colorpicker',
                'name' => esc_html__('Breadcrumb Background Color', 'puca')
            ),
            array(
                'id' => $prefix.'breadcrumb_image',
                'type' => 'file',
                'name' => esc_html__('Breadcrumb Background Image', 'puca')
            ),
            array(
                'id' => $prefix.'header_type',
                'type' => 'select',
                'name' => esc_html__('Header Layout Type', 'puca'),
                'description' => esc_html__('Choose a header for your website.', 'puca'),
                'options' => $headers,
                'default' => 'global'
            )
    	);

        if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ){
            $min_cart_array = array(
                array(
                    'id' => $prefix.'mini_cart_position',
                    'type' => 'select',
                    'name' => esc_html__('Mini Cart Position', 'puca'),
                    'description' => esc_html__('Choose a mini cart position for your website.', 'puca'),
                    'options' => $cart_position,
                    'default' => 'global'
                )
            );

            $fields = array_merge($fields, $min_cart_array); 

        }

        $after_array = array(
            array(
                'id' => $prefix.'footer_type',
                'type' => 'select',
                'name' => esc_html__('Footer Layout Type', 'puca'),
                'description' => esc_html__('Choose a footer for your website.', 'puca'),
                'options' => $footers,
                'default' => 'global'
            ),
            array(
                'id' => $prefix.'extra_class',
                'type' => 'text',
                'name' => esc_html__('Extra Class', 'puca'),
                'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'puca')
            )
        );
        $fields = array_merge($fields, $after_array); 
		
	    $metaboxes[$prefix . 'display_setting'] = array(
			'id'                        => $prefix . 'display_setting',
			'title'                     => esc_html__( 'Display Settings', 'puca' ),
			'object_types'              => array( 'page' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => $fields
		);

	    return $metaboxes;
	}
}
add_filter( 'cmb2_meta_boxes', 'puca_tbay_page_metaboxes' ); 

if ( !function_exists( 'puca_tbay_cmb2_style' ) ) {
	function puca_tbay_cmb2_style() {
		wp_enqueue_style( 'puca-cmb2-style', PUCA_THEME_DIR . '/inc/vendors/cmb2/assets/style.css', array(), '1.0' );
	}
}
add_action( 'admin_enqueue_scripts', 'puca_tbay_cmb2_style' );


