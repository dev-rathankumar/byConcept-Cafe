<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Custom_Image_List_Categories') ) {
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
class Puca_Elementor_Custom_Image_List_Categories extends  Puca_Elementor_Carousel_Base{
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
        return 'tbay-custom-image-list-categories';
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
        return esc_html__( 'Puca Custom Image List Categories', 'puca' );
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
        return [ 'woocommerce-elements', 'custom-image-list-categories' ];
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
                'default'   => 'grid',
                'options'   => [
                    'grid'      => esc_html__('Grid', 'puca'), 
                    'carousel'  => esc_html__('Carousel', 'puca'), 
                ],
            ]
        );  

        $repeater = $this->register_category_repeater();
        $this->add_control(
            'categoriestabs',
            [
                'label'         => esc_html__( 'List Categories Items', 'puca' ),
                'type'          => Controls_Manager::REPEATER,
                'separator'     => 'after',
                'fields'        => $repeater->get_controls(),
            ]
        );
 
        $this->register_display_count();
        $this->register_button();

        $this->end_controls_section();
        $this->add_control_responsive();
        $this->add_control_carousel(['layout_type' => 'carousel']);
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

        $repeater->add_control (
            'check_custom_link', 
            [
                'label' => esc_html__( 'Show Custom Link', 'puca' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );

        $repeater->add_control (
            'custom_link', 
            [
                'label' => esc_html__('Custom Link','puca'),
                'type' => Controls_Manager::URL,
                'condition' => [
                    'check_custom_link' => 'yes'
                ],
                'placeholder' => esc_html__( 'https://your-link.com', 'puca' ),
            ]
        );  

        return $repeater;

    }

    protected function register_display_count() {
        $this->add_control(
            'display_count',
            [
                'label'     => esc_html__('Show Count Category', 'puca'),
                'type'      => Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );  
    }
    protected function register_button() {

        $active_theme = puca_tbay_get_theme();

        if( $active_theme !== 'furniture' ) return;

        $this->add_control(
            'show_more',
            [
                'label'     => esc_html__('Display Show More', 'puca'),
                'type'      => Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );  
        $this->add_control(
            'text_button',
            [
                'label'     => esc_html__('Text Button', 'puca'),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__('show more', 'puca'),
                'condition' => [
                    'show_more' => 'yes'
                ]
            ]
        );  

    }

    public function render_item_button() {
        $settings = $this->get_settings_for_display();
        extract( $settings );

        if( !isset($show_more)  ) return;

        if( !$show_more ) return;

        $url_category =  get_permalink(wc_get_page_id('shop'));
        if(isset($text_button) && !empty($text_button)) {?>
            <a href="<?php echo esc_url($url_category)?>" class="show-all">
                <?php echo '<span class="text">'.trim($text_button) .'</span>'; ?>
            </a>
            <?php
        }
        
    }

}
$widgets_manager->register_widget_type(new Puca_Elementor_Custom_Image_List_Categories());
