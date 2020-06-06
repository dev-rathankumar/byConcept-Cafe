<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Product_CountDown') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

 
class Puca_Elementor_Product_CountDown extends Puca_Elementor_Carousel_Base {

    public function get_name() {
        return 'tbay-product-count-down';
    }

    public function get_title() {
        return esc_html__( 'Puca Product CountDown', 'puca' );
    }

    public function get_categories() {
        return [ 'puca-elements', 'woocommerce-elements'];
    }

    public function get_icon() {
        return 'eicon-countdown';
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
        return [ 'slick', 'jquery-countdowntimer' ];  
    }

    public function get_keywords() {
        return [ 'woocommerce-elements', 'product', 'products', 'countdown'];
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
            'layout_type',
            [
                'label'     => esc_html__('Layout Type', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'options'   => $this->get_layout_products_countdown(),
                'default'   => 'grid'
            ]
        ); 
 
        $products = $this->get_available_products_countdown();
        
        if (!empty($products)) {
            $this->add_control(
                'products',
                [
                    'label'        => esc_html__('Products', 'puca'),
                    'type'         => Controls_Manager::SELECT2,
                    'options'      => $products,
                    'default'      => array_keys($products)[0],
                    'multiple'     => true,
                    'save_default' => true,
                    'label_block'  => true,
                    'description'  => esc_html( 'Only search for products by the countdown', 'puca' ),
                   
                ]
            );
        } else {
            $this->add_control(
                'html_products',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf(__('You do not have any discount products. <br>Go to the <strong><a href="%s" target="_blank">Products screen</a></strong> to create one.', 'puca'), admin_url('edit.php?post_type=product')),
                    'separator'       => 'after',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                   
                ]
            );
        }
        $this->end_controls_section(); 
        
        $this->add_control_responsive();
        $this->add_control_carousel(['layout_type!' =>  array('grid', 'list') ]);
    }

    public function render_content_product_count_down() {
        $settings = $this->get_settings_for_display();
        extract($settings);
        $ids = ${'products'};
        if( !is_array($ids) ){
            $atts['ids'] = $ids;
        } else {
            if( count($ids) === 0 ) {
                echo '<div class="not-product-count-down">'. esc_html__('Please select the show product', 'puca')  .'</div>';
                return;
            }

            $atts['ids'] = implode(',', $ids);
        }

        $type = 'products';

        $shortcode = new WC_Shortcode_Products($atts, $type);
        $args = $shortcode->get_query_args();

        $loop = new WP_Query($args); 

        if( !$loop->have_posts() ) return;

        $active_theme = puca_tbay_get_part_theme();

        if( $layout_type == 'grid' || $layout_type == 'grid-v2'  ) {
            if( $layout_type == 'grid' ) $layout_type = '';
            $layout         = 'grid';
            $product_item   = 'inner-countdown'. $layout_type;
        } else {
            $layout         = 'carousel';
            $product_item   = $layout_type;
            $_class_carousel = str_replace('inner-countdownthumbnail','carousel', $layout_type );
            $this->add_render_attribute('row', 'class', [$_class_carousel, 'products'] );
        }  

        $attr_row = $this->get_render_attribute_string('row');

        wc_get_template( 'layout-products/'.$active_theme.'/'. $layout .'.php' , array( 'loop' => $loop, 'product_item' => $product_item,'attr_row' => $attr_row) );
        
    }
    

}
$widgets_manager->register_widget_type(new Puca_Elementor_Product_CountDown());