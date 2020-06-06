<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Product_Categories_Tabs') ) {
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
class Puca_Elementor_Product_Categories_Tabs extends  Puca_Elementor_Carousel_Base{
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
        return 'tbay-product-categories-tabs';
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
        return esc_html__( 'Puca Product Categories Tabs', 'puca' );
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
        return 'eicon-product-tabs';
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
        return [ 'woocommerce-elements', 'product-categories' ];
    }

    protected function _register_controls() {
        $this->register_controls_heading();
        $this->register_remove_heading_element();
        
        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'Product Categories', 'puca' ),
            ]
        );        

        $this->add_control(
            'limit',
            [
                'label' => esc_html__('Number of products', 'puca'),
                'type' => Controls_Manager::NUMBER,
                'description' => esc_html__( 'Number of products to show ( -1 = all )', 'puca' ),
                'default' => 6,
                'min'  => -1,
            ]
        );

        $this->add_control(
            'advanced',
            [
                'label' => esc_html__('Advanced', 'puca'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->register_woocommerce_order();
        $this->add_control(
            'product_type',
            [   
                'label'   => esc_html__('Product Type','puca'),
                'type'     => Controls_Manager::SELECT,
                'options' => $this->get_product_type(),
                'default' => 'newest'
            ]
        );
        $this->register_woocommerce_layout_type();
        $this->register_style_controls();

        $this->register_controls_supermaket();
        $repeater = $this->register_category_repeater();

        $this->add_control( 
            'categories', 
                [
                'label' => esc_html__( 'Categories', 'puca' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->register_view_all();

        $this->end_controls_section();
        $this->add_control_responsive(['layout_type!' => 'list']);
        $this->add_control_carousel(['layout_type' => [ 'carousel', 'carousel-special', 'special' ]]);
    }

    protected function register_category_repeater() {
        $repeater = new \Elementor\Repeater();

        $categories = $this->get_product_categories();
        $repeater->add_control (
            'category', 
            [
                'label'     => esc_html__('Category', 'puca'),
                'type'      => Controls_Manager::SELECT, 
                'default'   => array_keys($categories)[0],
                'options'   => $categories, 
            ]
        );

        $repeater->add_control(
            'category_type',
            [
                'label' => esc_html__( 'Display Type', 'puca' ),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'icon',
                'options' => [
                    'icon' => [
                        'title' => esc_html__('Icon', 'puca'),
                        'icon' => 'fa fa-info',
                    ], 
                    'image' => [
                        'title' => esc_html__('Image', 'puca'),
                        'icon' => 'fa fa-image',
                    ],
                ],
                'default' => 'icon',
            ]
        );

        $repeater->add_control(
            'category_icon',
            [
                'label'     => esc_html__('Select icon', 'puca'),
                'type'      => Controls_Manager::ICONS,
                'condition' => [
                    'category_type' => 'icon'
                ]
            ]
        );  

        $repeater->add_control(
            'category_image',
            [
                'label' => esc_html('Choose Image','puca'),
                'type' => Controls_Manager::MEDIA,
                'condition' => [
                    'category_type' => 'image'
                ]
            ]
        );
        $repeater->add_group_control(
			Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
				'default' => 'full',
                'separator' => 'none',
                'condition' => [
                    'category_type' => 'image'
                ]
			]
		);


        return $repeater;
    }

    protected function register_view_all() {
        $this->add_control(
            'show_view_all',
            [
                'label'     => esc_html__('Display View All', 'puca'),
                'type'      => Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );  
        $this->add_control(
            'view_all_text',
            [
                'label'     => esc_html__('Text Button', 'puca'),
                'type'      => Controls_Manager::TEXT,
                'default' => esc_html__('View All', 'puca'),
                'condition' => [
                    'show_view_all' => 'yes'
                ]
            ]
        ); 
    }
   
    public function render_product_tab($categories,$_id,$settings) {
        extract($settings);
        ?>
        
        <div class="heading-product-category-tabs">
            <?php
                if(!empty($title_cat_tab) || !empty($sub_title_cat_tab) ) {
                    ?>
                    <h3 class="heading-tbay-title">
                        <?php if( !empty($title_cat_tab) ) : ?>
                            <span class="title"><?php echo trim($title_cat_tab); ?></span>
                        <?php endif; ?>	    	
                        <?php if( !empty($sub_title_cat_tab) ) : ?>
                            <span class="subtitle"><?php echo trim($sub_title_cat_tab); ?></span>
                        <?php endif; ?>
                    </h3>
                    <?php
                }
            ?>
            <ul class="tabs-list nav nav-tabs">
                <?php 
                    $__count=0;
                ?>

                <?php foreach ($categories as $key) { ?>
                        <?php 
                            $active = ($__count==0)? 'active':''; 
                            
                            $category   = get_term_by( 'slug', $key['category'], 'product_cat' );
                            $title      = $category->name;

                        ?>
                        <li class="<?php echo esc_attr( $active ); ?>">
                            <a href="#<?php echo esc_attr($key['category'].'-'.$_id); ?>" data-toggle="tab" data-title="<?php echo esc_attr($title);?>">
                                <?php $this->render_item_type($key); ?>
                                <?php echo trim($title);?>
                            </a>
                        </li>
                    <?php $__count++; ?>
                <?php } ?>
            </ul>
            
        </div>
        

       <?php
    }

    public function render_product_tabs_content($categories, $_id) {
        ?>
            <div class="widget-inner">
                <div class="tab-content">
                 <?php 
                 $__count=0;
                 foreach ($categories as $key ) {
                     $tab_active = ($__count == 0) ? ' active' : '';
                         $this->render_content_tab($key['category'], $tab_active, $_id);
                     $__count++;
                 }
                 ?>
                </div>
            </div>
        <?php
 
    }
    private function  render_content_tab($key, $tab_active, $_id) {
        $settings = $this->get_settings_for_display();
        $cat_operator = $product_type = $limit = $orderby = $order = '';
        $rows = 1;
        extract( $settings );
        
        /** Get Query Products */
        $loop = $this->get_query_products($key,  $cat_operator, $product_type, $limit, $orderby, $order);

        $attr_row = $this->get_render_attribute_string('row');

        $active_theme = puca_tbay_get_part_theme();
        ?>
        <div class="tab-pane animated fadeIn <?php echo esc_attr( $tab_active ); ?>" id="<?php echo esc_attr($key).'-'.$_id; ?>">
        
        <?php wc_get_template( 'layout-products/'. $active_theme .'/'. $layout_type .'.php' , array( 'loop' => $loop, 'attr_row' => $attr_row, 'rows' => $rows) ); ?>
        <?php $this->render_item_btn_view_all($key); ?>
        </div>
        <?php
        
    }

    private function  render_item_type($key) {
        $type = $key['category_type'];

        if( $type === 'icon' ) {
            $this->render_item_icon($key['category_icon']);
        } else if( $type === 'image' ) { 
            $image_id = $key['category_image']['id']; 
            if( isset($image_id) && !empty($image_id) ) {
                echo  wp_get_attachment_image($image_id, $key['thumbnail_size']);
            } 
        }

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
                    'style2'                => esc_html__('Style 3', 'puca'),
                ],
            ]
        ); 

    }

    protected function register_controls_supermaket() {

        $active_theme = puca_tbay_get_theme();

        if( $active_theme !== 'supermaket' ) return;

        $this->add_control(
            'tab_title_center',
            [
                'label'     => esc_html__('Tab title align center?', 'puca'),
                'type'      => Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );  

    }

    protected function render_item_icon($type_icon) {
        if( empty( $type_icon['value'] ) ) return;  
        echo '<i class="'. esc_attr($type_icon['value']) .'"></i>';
    }
    
    public function render_item_btn_view_all($key) {
        $settings = $this->get_settings_for_display();
        extract( $settings );

        if( $show_view_all !== 'yes' ) return;

        $category = get_term_by('slug', $key, 'product_cat');
        $url_category =  get_term_link($category);

        if(isset($view_all_text) && !empty($view_all_text)) {?>
            <a href="<?php echo esc_url($url_category)?>" class="btn btn-block btn-view-all"><?php echo trim($view_all_text) ?><i class="icon-arrow-right icons"></i></a>
            <?php
        }
        
    }

    public function on_import( $element ) {
		return Elementor\Icons_Manager::on_import_migration( $element, 'icon', 'category_icon', true );
	}

}
$widgets_manager->register_widget_type(new Puca_Elementor_Product_Categories_Tabs());
