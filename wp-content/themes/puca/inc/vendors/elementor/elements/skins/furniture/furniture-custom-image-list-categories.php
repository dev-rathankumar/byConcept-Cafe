<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Furniture_Custom_Image_List_Categories') ) {
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
class Puca_Elementor_Furniture_Custom_Image_List_Categories extends  Puca_Elementor_Carousel_Base{
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
        return 'tbay-furniture-custom-image-list-categories';
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
        return esc_html__( 'Furniture Custom Image List Categories', 'puca' );
    }

    public function get_categories() {
        return [ 'puca-elements', 'woocommerce-elements'];
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
        return 'eicon-product-categories';
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    public function get_script_depends()
    {
        return [ 'slick' ];
    }

    public function get_keywords() {
        return [ 'woocommerce-elements', 'custom-image-list-categories', 'furniture' ];
    }

    protected function _register_controls() {
        $this->register_controls_heading();
        $this->register_remove_heading_element();

        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'Custom Image List Categories', 'puca' ),
            ]
        );

        $this->add_control(
            'advanced',
            [
                'label' => esc_html__('Advanced', 'puca'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label'     => esc_html__('Layout Type', 'puca'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'carousel',
                'options'   => [
                    'carousel'     => esc_html__('Carousel', 'puca'), 
                    'carousel-v2'  => esc_html__('Carousel v2', 'puca'), 
                    'carousel-v3'  => esc_html__('Carousel v3', 'puca'), 
                    'grid'         => esc_html__('Grid', 'puca'), 
                    'single'       => esc_html__('Single', 'puca'), 
                ],
            ]
        );  

        $this->add_control(
            'categoriestabs',
            [
                'label' => esc_html__( 'List Categories Items', 'puca' ),
                'type' => Controls_Manager::REPEATER,
                'condition' => array(
                    'layout_type' => 'carousel',
                ),
                'fields' => $this->register_category_repeater()->get_controls(),
            ]
        );

        $this->add_control(
            'categoriestabs2',
            [
                'label' => esc_html__( 'List Categories Items', 'puca' ),
                'type' => Controls_Manager::REPEATER,
                'condition' => array(
                    'layout_type' => 'carousel-v2',
                ),
                'fields' => $this->register_category_repeater2()->get_controls(),
            ]
        );        

        $this->add_control(
            'categoriestabs3',
            [
                'label' => esc_html__( 'List Categories Items', 'puca' ),
                'type' => Controls_Manager::REPEATER,
                'condition' => ['layout_type' => ['carousel-v3', 'grid'] ],
                'fields' => $this->register_category_repeater3()->get_controls(),
            ]
        );

        $this->add_control(
            'shop_now',
            [
                'label' => esc_html__( 'Show Shop Now?', 'puca' ),
                'condition' => ['layout_type' => ['carousel-v2', 'carousel-v3'] ],
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );   

        $this->register_single_category();   

        $this->register_display_view_all_categories();  

        $this->end_controls_section();
        $this->register_section_style_view_all();
        $this->add_control_responsive();
        $this->add_control_carousel(['layout_type' => ['carousel', 'carousel-v2', 'carousel-v3'] ]);
        $this->remove_control('rows'); 
    }

    protected function register_category_repeater() {
        $repeater = new \Elementor\Repeater();

        $categories = $this->get_product_categories();
        $repeater->add_control (
            'category', 
            [
                'label' => esc_html__( 'Choose category', 'puca' ),
                'type' => Controls_Manager::SELECT,
                'default'   => array_keys($categories)[0],
                'options'   => $categories,
            ]
        );

        $repeater->add_control(
            'label',
            [
                'label' => esc_html__( 'Label', 'puca' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'hot',
            ]
        );

        $repeater->add_control (
            'images', 
            [
                'label' => esc_html__( 'Choose Image', 'puca' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        return $repeater;

    }

    protected function register_category_repeater2() {
        $repeater = new \Elementor\Repeater();

        $categories = $this->get_product_categories();
        $repeater->add_control (
            'category', 
            [
                'label' => esc_html__( 'Choose category', 'puca' ),
                'type' => Controls_Manager::SELECT,
                'default'   => array_keys($categories)[0],
                'options'   => $categories,
            ]
        );

        $repeater->add_control (
            'images', 
            [
                'label' => esc_html__( 'Choose Image', 'puca' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        return $repeater;

    }

    protected function register_category_repeater3() {
        $repeater = new \Elementor\Repeater();

        $categories = $this->get_product_categories();
        $repeater->add_control (
            'category', 
            [
                'label' => esc_html__( 'Choose category', 'puca' ),
                'type' => Controls_Manager::SELECT,
                'default'   => array_keys($categories)[0],
                'options'   => $categories,
            ]
        );

        $menus = $this->get_available_menus();

        if (!empty($menus)) {
            $repeater->add_control(
                'nav_menu',
                [
                    'label'        => esc_html__('Menu', 'puca'),
                    'type'         => Controls_Manager::SELECT,
                    'options'      => $menus,
                    'default'      => array_keys($menus)[0],
                    'save_default' => true,
                    'separator'    => 'after',
                    'description'  => sprintf(__('Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'puca'), admin_url('nav-menus.php')),
                ]
            );
        } else {
            $repeater->add_control(
                'nav_menu',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf(__('<strong>There are no menus in your site.</strong><br>Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'puca'), admin_url('nav-menus.php?action=edit&menu=0')),
                    'separator'       => 'after',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                ]
            );
        }


        $repeater->add_control (
            'images', 
            [
                'label' => esc_html__( 'Choose Image', 'puca' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );


        return $repeater;

    }

    protected function register_single_category() {
        $categories = $this->get_product_categories();
        $this->add_control (
            'category', 
            [
                'label' => esc_html__( 'Choose category', 'puca' ),
                'type' => Controls_Manager::SELECT,
                'default'   => array_keys($categories)[0],
                'options'   => $categories,
                'condition' => array(
                    'layout_type' => 'single',
                ),
            ]
        );  

        $this->add_control (
            'images', 
            [
                'label' => esc_html__( 'Choose Image', 'puca' ),
                'type' => Controls_Manager::MEDIA,
                'condition' => array(
                    'layout_type' => 'single',
                ),
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control (
            'display_count', 
            [
                'label' => esc_html__( 'Show Count Category', 'puca' ),
                'type' => Controls_Manager::SWITCHER,
                'condition' => array(
                    'layout_type' => 'single',
                ),
                'default' => 'yes'
            ]
        );

    }

    protected function register_display_view_all_categories() {
        $this->add_control(
            'show_view_all',
            [
                'label' => esc_html__( 'Display View All Categories?', 'puca' ),
                'type' => Controls_Manager::SWITCHER,
                'condition' => array(
                    'layout_type!' => 'single',
                ),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'view_all_text',
            [
                'label' => esc_html__( 'Text Button View All', 'puca' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'show_view_all' => 'yes',
                ],
                'default' => esc_html__( 'view all categories', 'puca'),
            ]
        );
    }

    private function register_section_style_view_all() {

        $this->start_controls_section(
            'section_style_view_all',
            [
                'label' => esc_html__( 'View All Categories', 'puca' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_view_all' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'section_style_view_all_margin',
            [
                'label' => esc_html__( 'Margin', 'puca' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ], 
                'condition' => [
                    'show_view_all' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .tbay-addon-button .show-all' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );    
        $this->end_controls_section();    

    }

    public function the_view_all_categories() {
        $settings = $this->get_settings_for_display();
        extract( $settings );

        if( $show_view_all === 'yes' ) {

            $aUrl = get_permalink( wc_get_page_id( 'shop' ) );

            echo '<div class="tbay-addon-button text-center icon-left btn"><a class="btn-default show-all" href="'. esc_url($aUrl) .'"><i class="icon-grid icons"></i>'. esc_html($view_all_text) .'</a></div>';
        }

    }

}
$widgets_manager->register_widget_type(new Puca_Elementor_Furniture_Custom_Image_List_Categories());
