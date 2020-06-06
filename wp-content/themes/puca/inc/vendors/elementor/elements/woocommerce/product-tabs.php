<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Product_Tabs') ) {
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
class Puca_Elementor_Product_Tabs extends  Puca_Elementor_Carousel_Base{
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
        return 'tbay-product-tabs';
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
        return esc_html__( 'Puca Product Tabs', 'puca' );
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
        return 'eicon-tabs';
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
        return [ 'woocommerce-elements', 'product', 'products', 'tabs' ];
    }

    protected function _register_controls() {
        $this->register_controls_heading();
        $this->register_remove_heading_element();

        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'Product Tabs', 'puca' ),
            ]
        );
        $this->add_control(
            'limit',
            [
                'label' => esc_html__('Number of products ( -1 = all )', 'puca'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
                'min'  => -1
            ]
        );
        $this->register_woocommerce_layout_type(); 
        $this->register_style_controls();
        $this->register_woocommerce_categories_operator();
        $this->register_controls_product_tabs();
        $this->add_control(
            'advanced',
            [
                'label' => esc_html__('Advanced', 'puca'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        
        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'puca'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => $this->get_woo_order_by(),
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'puca'),
                'type' => Controls_Manager::SELECT,
                'default' => 'asc',
                'options' => $this->get_woo_order(),
            ]
        );
        $this->register_button();
        $this->end_controls_section();
        $this->add_control_responsive(['layout_type!' => 'list']);
        $this->add_control_carousel(['layout_type' => [ 'carousel', 'carousel-special', 'special' ]]);
    }

    public function register_controls_product_tabs() {
        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'product_tabs_title',
            [
                'label' => esc_html__( 'Title', 'puca' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control(
            'product_tabs',
            [
                'label' => esc_html__('Show Tabs', 'puca'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_product_type(),
                'default' => 'newest',
            ]
        );  
        $this->add_control(
            'list_product_tabs',
            [
                'label' => esc_html('Tab Item','puca'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => $this->register_set_product_tabs_default(),
                'title_field' => '{{{ product_tabs_title }}}',
            ]
        );
    }

    private function register_set_product_tabs_default() {
        $defaults = [
            [
                'product_tabs_title'    => esc_html__( 'Newest', 'puca' ),
                'product_tabs'          => 'newest',
            ],
            [
                'product_tabs_title' => esc_html__( 'On Sale', 'puca' ),
                'product_tabs'       => 'newest',
            ],            
            [
                'product_tabs_title' => esc_html__( 'Best Selling', 'puca' ),
                'product_tabs'       => 'best_selling',
            ],
        ];

        return $defaults;
    }

    public function render_product_tabs($product_tabs, $_id,$title,$active) {
       ?>
            <li class="<?php echo esc_attr( $active ); ?>">
                <a href="#<?php echo esc_attr($product_tabs.'-'.$_id); ?>" data-toggle="tab" data-title="<?php echo esc_attr($title);?>" ><?php echo trim($title)?></a>
            </li>

       <?php

    }
    public function  render_content_tab($product_tabs,$tab_active,$_id) {

        $settings = $this->get_settings_for_display();
        $rows = 1;
        extract( $settings );
        
        $this->add_render_attribute('row', 'class', $this->get_name_template());

        if( $layout_type == 'carousel' || $layout_type == 'carousel-special' ) {
            $this->add_render_attribute('gridwrapper', 'class', 'grid-wrapper');
        } else {
            $this->add_render_attribute('gridwrapper', 'class', ['grid-wrapper', 'products-grid']);
        }

        $product_type = $product_tabs;

        /** Get Query Products */
        $loop = $this->get_query_products($categories,  $cat_operator, $product_type, $limit, $orderby, $order);

        $attr_row = $this->get_render_attribute_string('row'); 

        $active_theme = puca_tbay_get_part_theme();
        ?>
        <div class="tab-pane animated fadeIn <?php echo esc_attr( $tab_active ); ?>" id="<?php echo esc_attr($product_tabs).'-'.$_id; ?>">
            <div <?php echo trim($this->get_render_attribute_string('gridwrapper')); ?>>
                <?php wc_get_template( 'layout-products/'. $active_theme .'/'. $layout_type .'.php' , array( 'loop' => $loop, 'attr_row' => $attr_row, 'rows' => $rows) ); ?>
            </div>
        </div>
        <?php
    }
    protected function register_style_controls() {

        $active_theme = puca_tbay_get_theme();

        if( $active_theme !== 'fashion' ) return;

        $this->add_control(
            'style',
            [
                'label'     => esc_html__('Tab Style', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'style1',
                'options'   => [
                    'style1'                => esc_html__('Style 1', 'puca'),
                    'style-tab2'            => esc_html__('Style 2', 'puca'),
                ],
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

        $_id = puca_tbay_random_key();
        $url_category =  get_permalink(wc_get_page_id('shop'));
        if(isset($text_button) && !empty($text_button)) {?>

            <div id="show-view-all<?php echo esc_attr($_id); ?>" class="show-view-all">
                <a href="<?php echo esc_url($url_category)?>" class="show-all">
                    <?php echo '<span class="text">'.trim($text_button) .'</span>'; ?>
                </a>
            </div>
            <?php
        }
        
    }
}
$widgets_manager->register_widget_type(new Puca_Elementor_Product_Tabs());
