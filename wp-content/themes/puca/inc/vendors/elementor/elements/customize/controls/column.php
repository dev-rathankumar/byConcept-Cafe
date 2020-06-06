<?php

if ( !function_exists('puca_column_section_advanced')) {
    function puca_column_section_advanced( $widget ) {

        $widget->update_responsive_control(
            'padding',
            [  
                'label' => esc_html__( 'Padding', 'puca' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-column-wrap.elementor-element-populated' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

    }

    add_action( 'elementor/element/column/section_advanced/before_section_end', 'puca_column_section_advanced', 10, 2 );
}

