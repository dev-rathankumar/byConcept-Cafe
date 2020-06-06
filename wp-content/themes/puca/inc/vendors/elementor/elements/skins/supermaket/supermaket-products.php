<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Supermaket_Products') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;


class Puca_Elementor_Supermaket_Products extends Puca_Elementor_Carousel_Base {

    public function get_name() {
        return 'tbay-supermaket-products';
    }

    public function get_title() {
        return esc_html__( 'Puca Supermaket Products', 'puca' );
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

        $this->register_banner();
        
        $this->register_button();

        $this->end_controls_section();
        $this->add_control_responsive(['layout_type!' => 'list']);
        $this->add_control_carousel(['layout_type' => [ 'carousel', 'carousel-special' ]]);
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

    protected function register_banner() {
        $this->add_control(
            'banner',
            [
                'label' => esc_html__('Banner', 'puca'),
                'type' => Controls_Manager::HEADING,
                'separator'     => 'before',
            ]
        );

        $this->add_control(
            'banner_align',
            [
                'label' => esc_html__('Alignment', 'puca'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'puca'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'puca'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => '',
            ]
        );

        $this->add_control(
            'banner_img',
            [
                'label' => esc_html__( 'Choose Image', 'puca' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control (
            'banner_link', 
            [
                'label' => esc_html__('External link','puca'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'separator'     => 'after',
                'placeholder' => esc_html__( 'https://your-link.com', 'puca' ),
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

    public function render_content_banner() {
        $settings = $this->get_settings_for_display();
        extract($settings);

        if( isset($banner_img['id']) && $banner_img['id'] ) { ?>
            <div class="img-banner tbay-image-loaded">
                
                <?php if ( !empty($banner_link['url']) ) : ?>
                    <?php 
                        $this->add_render_attribute('link', 'href', $banner_link['url'] );

                        if( $banner_link['is_external'] === 'on' ) {
                            $this->add_render_attribute('link', 'target', '_blank');
                        }
                        if( $banner_link['nofollow'] === 'on' ) {
                            $this->add_render_attribute('link', 'rel', 'nofollow');
                        }    
                    ?>
                    <a <?php echo trim($this->get_render_attribute_string('link')); ?>>
                <?php endif; ?>

                    <?php echo wp_get_attachment_image($banner_img['id'], 'full'); ?>

                <?php if ( !empty($banner_link['url']) ) : ?>
                </a>
                <?php endif; ?>
            </div>
         
        <?php }
    }

}
$widgets_manager->register_widget_type(new Puca_Elementor_Supermaket_Products());