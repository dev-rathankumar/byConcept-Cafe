<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Fashion2_Banner') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class Puca_Elementor_Fashion2_Banner extends  Puca_Elementor_Widget_Base{
    /**
     * Get widget name.
     *
     * Retrieve tabs widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'tbay-fashion2-banner';
    }

    /**
     * Get widget title.
     *
     * Retrieve tabs widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Fashion 2 Banner', 'puca' );
    }

 
    /**
     * Get widget icon.
     *
     * Retrieve tabs widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-banner';
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'General', 'puca' ),
            ]
        );
        $this->register_title_controls();
        $this->add_control(
            'option_btn1',
            [
                'label' => esc_html__( 'Option Button 1', 'puca' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );        
        $this->add_control(
            'option_btn2',
            [
                'label' => esc_html__( 'Option Button 2', 'puca' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'style',
            [
                'label'     => esc_html__('Style', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'style1',
                'options'   => [
                    'style1'            => esc_html__('Style 1', 'puca'), 
                    'style2'            => esc_html__('Style 2', 'puca'), 
                ],
            ]
        ); 
        $this->end_controls_section();
        $this->add_control_link_one();
        $this->add_control_link_two();
    }

    protected function register_title_controls() {
        $this->add_control(
            'banner_title',
            [
                'label' => esc_html__( 'Title', 'puca' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'banner_sub_title',
            [
                'label' => esc_html__( 'Sub Title', 'puca' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
    }

    
    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function add_control_link_one() {
        $this->start_controls_section(
            'section_options_one',
            [
                'label' => esc_html__( 'Option Button 1', 'puca' ),
                'type'  => Controls_Manager::SECTION,
                'condition' => array(
                    'option_btn1' => 'yes',
                ),
            ]
        );
        $this->add_control(
            'btn_text_1',
            [
                'label' => esc_html__( 'Text Button', 'puca' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Button 01', 'puca' ),
            ]
        );

        $this->add_control(
            'btn_link_1',
            [
                'label' => esc_html__( 'Link to', 'puca' ),
                'type' => Controls_Manager::URL,
                'default' => [
                    'url' => 'https://your-link.com',
                ],
                'placeholder' => 'https://your-link.com',
            ]
        );
        
        $this->end_controls_section();
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function add_control_link_two() {
        $this->start_controls_section(
            'section_options_two',
            [
                'label' => esc_html__( 'Option Button 2', 'puca' ),
                'type'  => Controls_Manager::SECTION,
                'condition' => array(
                    'option_btn2' => 'yes',
                ),
            ]
        );
        $this->add_control(
            'btn_text_2',
            [
                'label' => esc_html__( 'Text Button', 'puca' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Button 02', 'puca' ),
            ]
        );

        $this->add_control(
            'btn_link_2',
            [
                'label' => esc_html__( 'Link to', 'puca' ),
                'type' => Controls_Manager::URL,
                'default' => [
                    'url' => 'https://your-link.com',
                ],
                'placeholder' => 'https://your-link.com',
            ]
        );
        
        $this->end_controls_section();
    }

    protected function render_item_content() {
       $this->render_item_btn1();
       $this->render_item_btn2();
    }

    protected function render_item_btn1() {
        $settings = $this->get_settings_for_display();
        extract( $settings );

        if( $option_btn1 !== 'yes' ) return;

        $this->add_render_attribute('link', 'class', ['btn', 'btn-1']);

        if( $btn_link_1['is_external'] === 'on' ) {
            $this->add_render_attribute('link', 'target', '_blank');
        }

        if( $btn_link_1['nofollow'] === 'on' ) {
            $this->add_render_attribute('link', 'rel', 'nofollow');
        }

        if( !empty($btn_link_1['url']) ) {
            $this->add_render_attribute('link', 'href', $btn_link_1['url']);
        }

        if( !empty($btn_link_1) ) {
            echo '<a '. trim($this->get_render_attribute_string('link')) .'>'. trim($btn_text_1) .'</a>';
        }

    }

    protected function render_item_btn2() {
        $settings = $this->get_settings_for_display();
        extract( $settings );

        if( $option_btn2 !== 'yes' ) return;

        $this->add_render_attribute('link2', 'class', ['btn', 'btn-2']);

        if( $btn_link_2['is_external'] === 'on' ) {
            $this->add_render_attribute('link2', 'target', '_blank');
        }

        if( $btn_link_2['nofollow'] === 'on' ) {
            $this->add_render_attribute('link2', 'rel', 'nofollow');
        }

        if( !empty($btn_link_2['url']) ) {
            $this->add_render_attribute('link2', 'href', $btn_link_2['url']);
        }

        if( !empty($btn_link_2) ) {
            echo '<a '. trim($this->get_render_attribute_string('link2')) .'>'. trim($btn_text_2) .'</a>';
        }
    }

    protected function render_heading_title() {
        $settings = $this->get_settings_for_display();
        extract( $settings );

        if( !empty($banner_title) ) {
            echo '<h3 class="widget-title"><span>'. trim($banner_title) .'</span></h3>';
        }        

        if( !empty($banner_sub_title) ) {
            echo '<h4 class="subtitle-title"><span>'. trim($banner_sub_title) .'</span></h4>';
        }

    }
}
$widgets_manager->register_widget_type(new Puca_Elementor_Fashion2_Banner());
