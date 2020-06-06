<?php

if ( !function_exists('puca_furniture_features_section_general')) {
    function puca_furniture_features_section_general( $widget, $args ) {
 
        $widget->update_control(
            'styles',
            [  
                'options'   => [
                    'default'           => esc_html__('Default', 'puca'), 
                    'style2'            => esc_html__('Style 2', 'puca'), 
                ],
            ]
        );

    }  

    add_action( 'elementor/element/tbay-features/section_general/before_section_end', 'puca_furniture_features_section_general', 10, 2 );
}


if ( !function_exists('puca_furniture_post_grid_section_general')) { 
    function puca_furniture_post_grid_section_general( $widget, $args ) {
 
        $widget->update_control(
            'layout_type',
            [  
                'options'   => [
                    'grid'           => esc_html__('Grid', 'puca'), 
                    'carousel'       => esc_html__('Carousel', 'puca'), 
                    'carousel-v2'    => esc_html__('Carousel v2', 'puca'), 
                ],
            ]
        );

    }  

    add_action( 'elementor/element/tbay-posts-grid/section_general/before_section_end', 'puca_furniture_post_grid_section_general', 10, 2 );
}

