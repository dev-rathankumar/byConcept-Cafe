<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Supermaket2_Categories_Tabs_3') ) {
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
class Puca_Elementor_Supermaket2_Categories_Tabs_3 extends  Puca_Elementor_Carousel_Base {
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
        return 'tbay-supermaket2-categoriestabs-3';
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
        return esc_html__( 'Products Supermaket2 Categories Tabs 3', 'puca' );
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
            'banner',
            [
                'label' => esc_html__( 'Banner', 'puca' ),
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
                'placeholder' => esc_html__( 'https://your-link.com', 'puca' ),
            ]
        );
        $this->add_control(
            'banner_positions',
            [
                'label'     => esc_html__('Positions Banner', 'puca'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'left',
                'options'   => [
                    'left'  => esc_html__('Left','puca'),
                    'right' => esc_html__('Right','puca'),
                ],
            ]
        );
        $this->add_control(
            'banner_title',
            [
                'label' => esc_html__( 'Title banner', 'puca' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'banner_des',
            [
                'label' => esc_html__( 'Descriptions banner', 'puca' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'show_catname_tabs',
            [
                'label'         => esc_html__('Display Name Category?', 'puca'),
                "description"   => esc_html__( 'Show name category in tabs ', 'puca' ),
                'type'          => Controls_Manager::SWITCHER,
                'default'       => 'no',
                'separator'     => 'before',
            ]
        );

        $this->add_control(
            'tabs_view_more',
            [
                'label'         => esc_html__('View More Products', 'puca'),
                'type'          => Controls_Manager::SWITCHER,
                'default'       => 'yes'
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

        return $repeater;

    }
    public function render_content_tab($tab_id) {
        $settings = $this->get_settings_for_display();
        extract($settings);
        ?>
        <div class="tab-content">
        <?php $i = 0; foreach ($categoriestabs as $tab) : ?>

            <?php 
                $cat_id = $this->get_id_cat_product_by_slug($tab['category']);
                $link           = get_term_link( $cat_id, 'product_cat' );

                $tab_class = ($i == 0 ? 'active' : '');
            ?>

            <div id="tab-<?php echo esc_attr($tab_id);?>-<?php echo esc_attr($i); ?>" class="tab-pane animated fadeIn <?php echo esc_attr( $tab_class ); ?>">
                <div class="hidden-xs tab-menu">
                    <?php $this->render_content_menu($tab['nav_menu']); ?>
                </div>                        

                <?php $this->render_layout_products_tab($tab); ?>


                <?php $this->render_btn_view($link); ?>

            </div>

        <?php $i++; endforeach; ?>
        </div>
    <?php
    }
    public function render_content_banner() {
        $settings = $this->get_settings_for_display();
        extract($settings);

        ?>
        <?php 

            $img = wp_get_attachment_image_src($banner['id'],'full'); 
        ?>

        <?php if ( !empty($img) && isset($img[0]) ): ?>

            <?php 

                if( isset($img[0]) ){
                    $style = 'style="background-image:url(\''.esc_url($img[0]).'\')"';
                }

            ?>
            <div class="tab-banner" <?php echo trim($style); ?>>

                <div class="wpb_wrapper">
                    <div class="banner-content">

                        <?php if( isset($banner_title) ) : ?>
                            <p>
                                <?php echo esc_html($banner_title); ?>
                            </p>

                        <?php endif; ?>

                        <?php if( isset($banner_des) ) : ?>
                            <span>
                                <?php echo trim($banner_des); ?>
                            </span>
                        <?php endif; ?>                                

                        <?php if( isset($banner_link['url']) ) : ?>
                            <a href="<?php echo esc_url($banner_link['url']); ?>"><?php esc_html_e('Shop now','puca'); ?></a>
                        <?php endif; ?>

                    </div>
                    <div class="overlay"></div>
                </div>
            </div>

        <?php endif;
    }
    public function render_content_menu($menu_id) {
        $settings = $this->get_settings_for_display();
        extract($settings);

        $available_menus = $this->get_available_menus();

        if (!$available_menus) {
            return;
        }
        
        $_id = puca_tbay_random_key();

        $args = [
            'echo'        => false, 
            'menu'        => $menu_id,
            'container_class' => 'collapse navbar-collapse',
            'menu_id'     => 'menu-' . $this->get_nav_menu_index() . '-' . $_id,
            'walker'      => new Puca_Tbay_Nav_Menu(),
            'fallback_cb' => '__return_empty_string',
            'container'   => '',
        ];  

        $args['menu_class']     = 'elementor-nav-menu menu';


        // General Menu.
        $menu_html = wp_nav_menu($args);

        $this->add_render_attribute('main-menu', 'class', [
            'elementor-nav-menu--main',
            'elementor-nav-menu__container'
        ]);

        ?>
        <div class="tab-menu-wrapper">
            <nav <?php echo trim($this->get_render_attribute_string('main-menu')); ?>><?php echo trim($menu_html); ?></nav>
        </div>
        <?php
    }

    public function render_btn_view($link) {
        $settings = $this->get_settings_for_display();
        extract($settings);

        if( isset($tabs_view_more) && $tabs_view_more == 'yes') { ?>
            <a href="<?php echo esc_url( $link ); ?>" class="btn btn-view-all"><?php echo esc_html__('All products', 'puca'); ?></a>
        <?php }
    }
}
$widgets_manager->register_widget_type(new Puca_Elementor_Supermaket2_Categories_Tabs_3());
