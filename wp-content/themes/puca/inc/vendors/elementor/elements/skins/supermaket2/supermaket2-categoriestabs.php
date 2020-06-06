<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Supermaket2_Categories_Tabs') ) {
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
class Puca_Elementor_Supermaket2_Categories_Tabs extends  Puca_Elementor_Carousel_Base{
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
        return 'tbay-supermaket2-categoriestabs';
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
        return esc_html__( 'Products Supermaket2 Categories Tabs 1', 'puca' );
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
        return [ 'woocommerce-elements', 'categoriestabs', 'supermaket2' ];
    }
    
    protected function _register_controls() {
        $this->register_controls_heading();
        $this->register_remove_heading_element();
        $this->register_remove_align_heading_element();

        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'Categoriestabs', 'puca' ),
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
            'style',
            [
                'label'     => esc_html__('Style', 'puca'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'style1',
                'options'   => [
                    'style1' => esc_html__('Style 1 of home 1','puca'),
                    'style2' => esc_html__('Style 2 of home 4','puca'),
                ],
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label'     => esc_html__('Layout Type', 'puca'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'carousel',
                'options'   => [
                    'carousel'  => esc_html__('Carousel','puca'),
                    'grid'      => esc_html__('Grid','puca'),
                ],
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
            'show_catname_tabs',
            [
                'label'     => esc_html__('Display Name Category?', 'puca'),
                "description"   => esc_html__( 'Show name category in tabs ', 'puca' ),
                'type'      => Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        ); 
        $this->add_control(
            'categoriestabs',
            [
                'label' => esc_html__( 'Tabs', 'puca' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $this->register_category_repeater()->get_controls(),
            ]
        );

        $this->end_controls_section();
        $this->add_control_responsive();
        $this->add_control_carousel(['layout_type' => 'carousel']);
    }

    protected function register_category_repeater() {
        $repeater = new \Elementor\Repeater();

        $categories = $this->get_product_categories();
        $menus = $this->get_available_menus();
        
        $repeater->add_control(
            'product_type',
            [
                'label' => esc_html__('Show Tabs', 'puca'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_product_type(),
                'default' => 'newest',
            ]
        ); 
        $repeater->add_control(
            'title',
            [
                'label' => esc_html__( 'Custom Name Tab', 'puca' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control( 
            'category',
            [
                'label'     => esc_html__('Category', 'puca'),
                'type'      => Controls_Manager::SELECT, 
                'default'   => array_keys($categories)[0],
                'options'   => $categories,
            ]
        ); 
        
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
                    'description'  => esc_html__('Note does not apply to Mega Menu.', 'puca'),
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

        $repeater->add_control(
            'banner',
            [
                'label' => esc_html__( 'Banner', 'puca' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control (
            'banner_link', 
            [
                'label' => esc_html__('External link','puca'),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'puca' ),
            ]
        );

        $repeater->add_control(
            'gallerys',
            [
                'label' => esc_html__( 'Gallery', 'puca' ),
                'type' => Controls_Manager::GALLERY,
                'separator'    => 'before',
            ]
        );
        return $repeater;

    }
    public function render_content_tab($tab_id) {
        $settings = $this->get_settings_for_display();
        extract($settings);

        ?>
        <div class="widget-inner">

            <div class="tab-content-product">
                <div class="tab-content">
                    <?php $i = 0; foreach ($categoriestabs as $tab) : ?>
                        

                        <?php 
                            extract( $tab );
                            $tab_class = ($i == 0 ? 'active' : '');
                        ?>

                        <div id="tab-<?php echo esc_attr($tab_id);?>-<?php echo esc_attr($i); ?>" class="tab-pane <?php echo esc_attr( $tab_class ); ?>">
                            <?php if ($style == 'style1') { ?>
                                <div class="tab-menu-banner-brand">
                                
                                    <div class="row">

                                        <div class="col-sm-6 col-md-4 hidden-xs tab-menu">
                                           <?php $this->render_content_menu($nav_menu); ?>
                                        </div>
                                        <div class="col-sm-6 col-md-4 hidden-xs tab-banner">
                                            
                                            <?php $this->render_content_banner($banner, $banner_link); ?>
                                        </div>

                                        <div class="col-md-4 hidden-sm hidden-xs tab-gallery">
                                            <?php $this->render_content_gallery($gallerys); ?>
                                        </div>

                                    </div>

                                </div>

                                <?php $this->render_layout_products_tab($tab); ?>

                            <?php } else { ?>
                                <div class="row">


                                    <div class="tab-left hidden-sm hidden-xs col-md-6 tab-banner-menu-gallery">

                                        <div class="hidden-xs tab-banner">
                                            <?php $this->render_content_banner($banner, $banner_link); ?>
                                        </div>

                                        <div class="menu-gallery">

                                            <?php $this->render_content_menu($nav_menu); ?> 


                                            <div class="tab-gallery">

                                                <?php $this->render_content_gallery($gallerys); ?>

                                            </div>

                                        </div>
    

                                    </div>

                                    <div class="tab-right col-sm-12 col-md-6">
                                        <?php $this->render_layout_products_tab($tab); ?>
                                    </div>
                                </div>
                            <?php } ?>
                            
                        </div>

                    <?php $i++; endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_content_banner($banner, $banner_link) {
        $settings = $this->get_settings_for_display();
        extract($settings);
        $cat_id         =   $banner;

        if( isset($cat_id) && $cat_id ) { ?>
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

                    <?php echo wp_get_attachment_image($cat_id['id'], 'full'); ?>

                <?php if ( !empty($banner_link['url']) ) : ?>
                </a>
                <?php endif; ?>
            </div>
         
        <?php }
    }
    public function render_content_gallery($gallery) {
        $settings = $this->get_settings_for_display();
        extract($settings);

        if( isset($gallery) && !empty($gallery) ) : ?>

        <div class="gallery-content tbay-image-loaded">
            <?php
            $ids = wp_list_pluck( $gallery, 'id' );
            
            $this->add_render_attribute( 'shortcode', 'ids', implode( ',', $ids ) );
            $this->add_render_attribute( 'shortcode', 'orderby', 'rand' );

            echo do_shortcode( '[gallery ' . $this->get_render_attribute_string( 'shortcode' ) . ']' );

            ?>
        </div>

        <?php endif;
    }
}
$widgets_manager->register_widget_type(new Puca_Elementor_Supermaket2_Categories_Tabs());
