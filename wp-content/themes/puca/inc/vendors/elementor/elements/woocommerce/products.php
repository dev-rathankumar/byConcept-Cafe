<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Products') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;


class Puca_Elementor_Products extends Puca_Elementor_Carousel_Base {

    public function get_name() {
        return 'tbay-products';
    }

    public function get_title() {
        return esc_html__( 'Puca Products', 'puca' );
    }

    public function get_categories() {
        return [ 'puca-elements', 'woocommerce-elements'];
    }

    public function get_icon() {
        return 'eicon-products';
    }

    /**
     * Retrieve the list of scripts the image carousel widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
     * @since 1.3.0
     * @access public
     *
     * @return array Widget scripts dependencies.
     */
    public function get_script_depends()
    {
        return ['slick'];
    }

    public function get_keywords() {
        return [ 'woocommerce-elements', 'product', 'products' ];
    }

    protected function _register_controls() {
        $this->register_controls_heading();
        $this->register_remove_heading_element();

        $this->start_controls_section(
            'general',
            [
                'label' => esc_html__( 'General', 'puca' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => esc_html__('Number of products', 'puca'),
                'type' => Controls_Manager::NUMBER,
                'description' => esc_html__( 'Number of products to show ( -1 = all )', 'puca' ),
                'default' => 6,
                'min'  => -1
            ]
        );


        $this->add_control(
            'advanced',
            [
                'label' => esc_html__('Advanced', 'puca'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->register_woocommerce_layout_type(); 

       $this->register_woocommerce_order();

       $this->register_woocommerce_categories_operator();

        $this->add_control(
            'product_type',
            [
                'label' => esc_html__('Product Type', 'puca'),
                'type' => Controls_Manager::SELECT,
                'default' => 'newest',
                'options' => $this->get_product_type(),
            ]
        );
        
        $this->register_settings_fashion2();

        $this->register_button();

        $this->end_controls_section();
        $this->add_control_responsive(['layout_type!' => 'list']);
        $this->add_control_carousel(['layout_type' => [ 'carousel', 'carousel-special', 'special' ]]);
    }

    protected function register_settings_fashion2() {
        
        $active_theme = puca_tbay_get_theme();

        if( $active_theme !== 'fashion2' ) return;

        $this->add_control(
            'special_home5',
            [
                'label'     => esc_html__('Show carousel special home 5?', 'puca'),
                'type'      => Controls_Manager::SWITCHER,
                'separator'     => 'before',
                'condition' => [
                    'layout_type' => ['carousel', 'carousel-special']
                ],
                'default' => 'no'
            ]
        );  

        $this->add_control(
            'carousel_blur',
            [
                'label'     => esc_html__('Show carousel Blur?', 'puca'),
                'type'      => Controls_Manager::SWITCHER,
                'separator'     => 'after',
                'condition' => [
                    'layout_type' => ['carousel', 'carousel-special']
                ],
                'default' => 'no'
            ]
        );  
 
    }

    protected function settings_layout_skins() {
        $this->settings_layout_fashion2();
    }

    protected function settings_layout_fashion2() {
        $active_theme = puca_tbay_get_theme();
        $settings = $this->get_settings_for_display();
        extract( $settings );

        if( $active_theme !== 'fashion2' ) return;
        
        if( $special_home5 === 'yes' ) {
            $this->add_render_attribute('wrapper', 'class', 'special-home5');
        }

        if( $carousel_blur === 'yes' ) { 
            $this->add_render_attribute('wrapper', 'class', 'carousel-blur');
            $this->set_render_attribute('row', 'data-loop', 'true'); 
        }
    }

    protected function register_button() {
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
        $this->add_control(
            'selected_icon',
            [
                'label'     => esc_html__('Icon Button', 'puca'),
                'type'      => Controls_Manager::ICONS,
                'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_more' => 'yes'
                ]
            ]
        );  
    }
    public function render_item_button() {
        $settings = $this->get_settings_for_display();
        extract( $settings );

        $url_category =  get_permalink(wc_get_page_id('shop'));
        if(isset($text_button) && !empty($text_button)) {?>

            <div class="more_products">
                <a href="<?php echo esc_url($url_category)?>" class="show-all">
                    <?php 
                        $this ->render_item_icon($selected_icon);
                    ?>
                    <?php echo '<span class="text">'.trim($text_button) .'</span>'; ?>
                </a>
            </div>
            <?php
        }
        
    }
    protected function render_item_icon($selected_icon) {

        if( empty( $selected_icon['value'] ) ) return; 

        Elementor\Icons_Manager::enqueue_shim();
       
        $this->add_render_attribute( 'icon', 'class', $selected_icon['value'] );

        echo '<i '. trim($this->get_render_attribute_string( 'icon' )) .'></i>';
    }

    public function on_import( $element ) {
		return Elementor\Icons_Manager::on_import_migration( $element, 'icon', 'selected_icon', true );
	}
}
$widgets_manager->register_widget_type(new Puca_Elementor_Products());