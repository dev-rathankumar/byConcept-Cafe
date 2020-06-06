<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Fashion2_Woocommerce_Tags') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;


class Puca_Elementor_Fashion2_Woocommerce_Tags extends Puca_Elementor_Widget_Base {

    public function get_name() {
        return 'tbay-fashion2-woocommerce-tags'; 
    }

    public function get_title() {
        return esc_html__( 'Fashion 2 Woocommerce Tags', 'puca' );
    }

    public function get_categories() {
        return [ 'puca-elements', 'woocommerce-elements'];
    }

    public function get_icon() {
        return 'eicon-tags';
    }

    public function get_keywords() {
        return [ 'woocommerce-elements', 'woocommerce-tags' ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'general',
            [
                'label' => esc_html__( 'General', 'puca' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'heading_title',
            [
                'label' => esc_html__('Title', 'puca'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'heading_title_tag',
            [
                'label' => esc_html__( 'Title HTML Tag', 'puca' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'columns',
            [
                'label'     => esc_html__('Columns', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => '3',
                'options'   => [
                    '3'            => '3', 
                    '4'            => '4', 
                    '5'            => '5' 
                ],
            ]
        ); 

        $this->add_control(
            'limit',
            [
                'label' => esc_html__('Number tag to show ( -1 = all )', 'puca'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
                'min'  => -1
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__( 'Content', 'puca' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->register_content_styles();
        $this->end_controls_section();
    }

    public function register_content_styles() {
        $this->add_control(
            'heading_stylecontent',
            [
                'label' => esc_html__( 'Content', 'puca' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'content_style_width',
            [
                'label' => esc_html__( 'Max width', 'puca' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1400,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .container .content' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );        


        $this->add_responsive_control(
            'heading_style_margin',
            [
                'label' => esc_html__( 'Margin', 'puca' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ], 
                'selectors' => [
                    '{{WRAPPER}} .container .content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );        


    }

    public function render_item_content() {
        $settings = $this->get_settings_for_display();
        extract($settings);

        if( $limit === 0 ) {
            echo '<p>'. esc_html__('Please select the number of tags again', 'puca') .'</p>';
            return; 
        } 

        $taxonomy = 'product_tag';

        $args = array(
            'taxonomy' => $taxonomy
        );

        if( $limit !== -1 ) {
            $args['number'] = $limit;
        } 

        $tags = get_terms( $args );

        $list = '';
        if( $tags && is_array( $tags ) ) {
            if( !empty( $tags ) ) {
                $list .= '<ul class="list-tags" data-columns="'. $columns .'">';
                foreach( $tags as $tag ) {
                    $term_link = get_term_link( $tag->term_id, $taxonomy );
                    $name =  apply_filters( 'the_title', $tag->name );
                    $list .= '<li><a class="category_links" href="' . esc_url($term_link) . '">' . trim($name) . '</a></li>';
                }
                $list .= '</ul>';
            }
        }
        else $list .= '<p>'. esc_html__('Sorry, but no tags were found','puca') .'</p>';

        echo trim($list);
    }

}
$widgets_manager->register_widget_type(new Puca_Elementor_Fashion2_Woocommerce_Tags());