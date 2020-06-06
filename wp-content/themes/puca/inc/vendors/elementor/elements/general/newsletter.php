<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Newsletter') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Scheme_Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Controls_Manager;


class Puca_Elementor_Newsletter extends Puca_Elementor_Widget_Base {
    /**
     * Get widget name.
     *
     * Retrieve icon box widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'tbay-newsletter';
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
        return esc_html__( 'Puca newsletter', 'puca' );
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
        return 'eicon-mail';
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
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
            'heading_subtitle',
            [
                'label' => esc_html__('Sub Title', 'puca'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'heading_description',
            [
                'label' => esc_html__( 'Description', 'puca' ),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $this->register_style_controls();
        $this->register_style_controls_fashion2();
        $this->register_style_controls_furniture();

        $this->end_controls_section();

    }

    protected function register_style_controls() {

        $active_theme = puca_tbay_get_theme();

        if( $active_theme !== 'fashion' ) return;

        $this->add_control(
            'style',
            [
                'label'     => esc_html__('Style', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'style1',
                'options'   => [
                    'style1'            => esc_html__('Style 1', 'puca'), 
                    'style2'            => esc_html__('Style 2', 'puca'), 
                    'style3'            => esc_html__('Style 3', 'puca'), 
                    'style4'            => esc_html__('Style 4', 'puca'),
                    'style5'            => esc_html__('Style 5', 'puca'), 
                ],
            ]
        ); 

    }
    protected function register_style_controls_fashion2() {

        $active_theme = puca_tbay_get_theme();

        if( $active_theme !== 'fashion2' ) return;

        $this->add_control(
            'style',
            [
                'label'     => esc_html__('Style', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'style1',
                'options'   => [
                    'style1'            => esc_html__('Style 1', 'puca'), 
                    'style2'            => esc_html__('Style 2', 'puca'), 
                    'style3'            => esc_html__('Style 3', 'puca'), 
                    'style4'            => esc_html__('Style 4', 'puca'), 
                ],
            ]
        ); 

    }    

    protected function register_style_controls_furniture() {

        $active_theme = puca_tbay_get_theme();

        if( $active_theme !== 'furniture' ) return;

        $this->add_control(
            'style',
            [
                'label'     => esc_html__('Style', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'style-1',
                'options'   => [
                    'style-1'            => esc_html__('Style 1', 'puca'), 
                    'style-2'            => esc_html__('Style 2', 'puca'), 
                ],
            ]
        ); 

    }
}
$widgets_manager->register_widget_type(new Puca_Elementor_Newsletter());