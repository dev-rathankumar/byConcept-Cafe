<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Puca_Elementor_Addons {
	public function __construct() {
        $this->include_control_customize_widgets();
        $this->include_render_customize_widgets();

		add_action( 'elementor/elements/categories_registered', array( $this, 'add_category' ) );

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'include_widgets' ) );

		add_action( 'wp', [ $this, 'regeister_scripts_frontend' ] );

        // editor
        add_action('elementor/editor/after_register_scripts', [ $this, 'editor_after_register_scripts' ]);

        // frontend
        // Register widget scripts
        add_action('elementor/frontend/after_register_scripts', [ $this, 'frontend_after_register_scripts' ]);
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'frontend_after_enqueue_scripts' ] );
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_icons'], 99);

    
        add_action( 'widgets_init', array( $this, 'register_wp_widgets' ) );

        add_action( 'after_switch_theme', array( $this, 'add_cpt_support'), 10 );
    }   
   
    public function add_cpt_support() {
        $cpt_support = ['tbay_megamenu', 'tbay_footer', 'post', 'page']; 
        update_option( 'elementor_cpt_support', $cpt_support);

        update_option( 'elementor_disable_color_schemes', 'yes'); 
        update_option( 'elementor_disable_typography_schemes', 'yes');
        update_option( 'elementor_container_width', '1200');
        update_option( 'elementor_viewport_lg', '1200');  
        update_option( 'elementor_space_between_widgets', '0');
        update_option( 'elementor_load_fa4_shim', 'yes');
    }


    public function editor_after_register_scripts() {
        $suffix = (puca_tbay_get_config('minified_js', false)) ? '.min' : PUCA_MIN_JS;
        /*slick jquery*/
        wp_register_script( 'slick', PUCA_SCRIPTS . '/slick' . $suffix . '.js', array( 'jquery' ), '1.0.0', true );

        wp_register_script( 'jquery-instagramfeed', PUCA_SCRIPTS . '/jquery.instagramfeed' . $suffix . '.js', array( 'jquery' ),'1.1.3', true );
        wp_register_script( 'jquery-timeago', PUCA_SCRIPTS . '/jquery.timeago' . $suffix . '.js', array( 'jquery' ),'1.6.7', true );
    
        wp_enqueue_script( 'bootstrap', PUCA_SCRIPTS . '/bootstrap' . $suffix . '.js', array( 'jquery' ), '3.3.7', true );
        wp_register_script( 'puca-script',  PUCA_SCRIPTS . '/functions' . $suffix . '.js', array('bootstrap'),  PUCA_THEME_VERSION,  true );  

        wp_register_script( 'jquery-counter', PUCA_SCRIPTS . '/jquery.counterup' . $suffix . '.js', array( 'jquery' ) ); 
    }    

    public function frontend_after_enqueue_scripts() { 

    }    

    public function enqueue_editor_icons() {
        wp_enqueue_style( 'simple-line-icons', PUCA_STYLES . '/simple-line-icons.css', array(), '2.4.0' );
    }


    /**
     * @internal Used as a callback
     */
    public function frontend_after_register_scripts() {
        $this->editor_after_register_scripts();
    }


	public function register_wp_widgets() {

	}

	function regeister_scripts_frontend() {
		
    }


    public function add_category() {
        Elementor\Plugin::instance()->elements_manager->add_category(
            'puca-elements',
            array(
                'title' => esc_html__('Puca Elements', 'puca'),
                'icon'  => 'fa fa-plug',
            ),
            1);
    }

    /**
     * @param $widgets_manager Elementor\Widgets_Manager
     */
    public function include_widgets($widgets_manager) {
        $this->include_abstract_widgets($widgets_manager);
        $this->include_general_widgets($widgets_manager);
        $this->include_woocommerce_widgets($widgets_manager);

        $this->include_fashion2_widgets($widgets_manager);
        $this->include_furniture_widgets($widgets_manager);
        $this->include_supermaket_widgets($widgets_manager);
        $this->include_supermaket2_widgets($widgets_manager);
	} 


    /**
     * Widgets General Theme
     */
    public function include_general_widgets($widgets_manager) {

        $elements = array(
            'video',   
            'nav-menu',   
            'template',  
            'heading',  
            'features', 
            'brands', 
            'posts-grid',
            'our-team',
            'testimonials',
            'button',
            'list-menu',
            'instagram',
            'social-icons',
        );

        if( class_exists('MC4WP_MailChimp') ) {
            array_push($elements, 'newsletter');
        }


        $elements = apply_filters( 'puca_general_elements_array', $elements );

        foreach ( $elements as $file ) {
            $path   = PUCA_ELEMENTOR .'/elements/general/' . $file . '.php';
            if( file_exists( $path ) ) {
                require_once $path;
            }
        }

    }    

    /**
     * Widgets WooComerce Theme
     */
    public function include_woocommerce_widgets($widgets_manager) {
        if( !puca_is_Woocommerce_activated() ) return;

        $woo_elements = array(
            'products',
            'product-category',
            'product-tabs',
            'woocommerce-tags',
            'product-categories-tabs',
            'list-categories-product',
            'custom-image-list-categories',
            'product-count-down',
        );


        $woo_elements = apply_filters( 'puca_woocommerce_elements_array', $woo_elements );

        foreach ( $woo_elements as $file ) {
            $path   = PUCA_ELEMENTOR .'/elements/woocommerce/' . $file . '.php';
            if( file_exists( $path ) ) {
                require_once $path;
            }
        }

    }   


    /**
     * Widgets General Theme
     */
    public function include_fashion2_widgets($widgets_manager) {
        $active_theme = puca_tbay_get_theme();

        $skin = 'fashion2';

        if( $active_theme !== $skin ) return;

        $widget_1   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/fashion2-banner.php';
        if( file_exists( $widget_1 ) ) {
            require_once $widget_1;
        }   

        $widget_2   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/fashion2-woocommerce-tags.php';
        if( file_exists( $widget_2 ) ) {
            require_once $widget_2;
        }   

    }         

    /**
     * Widgets General Theme
     */
    public function include_furniture_widgets($widgets_manager) {
        $active_theme = puca_tbay_get_theme();

        $skin = 'furniture';

        if( $active_theme !== $skin ) return;

        $widget_1   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/furniture-custom-image-list-categories.php';
        if( file_exists( $widget_1 ) ) {
            require_once $widget_1;
        }          

        $customize   = PUCA_ELEMENTOR .'/elements/customize/skins/'. $skin .'.php'; 
        if( file_exists( $customize ) ) {
            require_once $customize;
        }   
  
    }     


    /**
     * Widgets General Theme
     */
    public function include_supermaket_widgets($widgets_manager) {
        $active_theme = puca_tbay_get_theme();
        $skin = 'supermaket';

        if( $active_theme !== $skin ) return;


        $widget_1   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/supermaket-products.php';
        if( file_exists( $widget_1 ) ) {
            require_once $widget_1;
        }           

        $widget_2   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/supermaket-categories-tabs.php';
        if( file_exists( $widget_2 ) ) {
            require_once $widget_2;
        }   
        
        $widget_3   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/supermaket-features.php';
        if( file_exists( $widget_3 ) ) {
            require_once $widget_3;
        }   

    }     

    /**
     * Widgets General Theme
     */
    public function include_supermaket2_widgets($widgets_manager) {
        $active_theme = puca_tbay_get_theme();

        $skin = 'supermaket2';

        if( $active_theme !== $skin ) return;

        $widget_1   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/supermaket2-categoriestabs.php';
        if( file_exists( $widget_1 ) ) {
            require_once $widget_1;
        }    

        $widget_2   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/supermaket2-categoriestabs-2.php';
        if( file_exists( $widget_2 ) ) {
            require_once $widget_2;
        }    

        $widget_3   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/supermaket2-categoriestabs-3.php';
        if( file_exists( $widget_3 ) ) {
            require_once $widget_3;
        }    

        $widget_4   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/supermaket2-counter.php';
        if( file_exists( $widget_4 ) ) {
            require_once $widget_4;
        } 
        $widget_5   = PUCA_ELEMENTOR .'/elements/skins/'. $skin .'/supermaket2-custom-image-menus.php';
        if( file_exists( $widget_5 ) ) {
            require_once $widget_5;
        }    
    }     


    /**
     * Widgets Abstract Theme
     */
    public function include_abstract_widgets($widgets_manager) {
        $abstracts = array(
            'image',
            'base',
            'responsive',
            'carousel',
        );

        $abstracts = apply_filters( 'puca_abstract_elements_array', $abstracts );

        foreach ( $abstracts as $file ) {
            $path   = PUCA_ELEMENTOR .'/abstract/' . $file . '.php';
            if( file_exists( $path ) ) {
                require_once $path;
            }
        } 
    }

    public function include_control_customize_widgets() {
        $widgets = array(
            'column',
            'section-stretch-row',
        );

        $widgets = apply_filters( 'puca_customize_elements_array', $widgets );
 
        foreach ( $widgets as $file ) {
            $control   = PUCA_ELEMENTOR .'/elements/customize/controls/' . $file . '.php';
            if( file_exists( $control ) ) {
                require_once $control;
            }            
        } 
    }    

    public function include_render_customize_widgets() {
        $widgets = array(
        );

        $widgets = apply_filters( 'puca_customize_elements_array', $widgets );
 
        foreach ( $widgets as $file ) {
            $render    = PUCA_ELEMENTOR .'/elements/customize/render/' . $file . '.php';         
            if( file_exists( $render ) ) {
                require_once $render;
            }
        } 
    }
}

new Puca_Elementor_Addons();

