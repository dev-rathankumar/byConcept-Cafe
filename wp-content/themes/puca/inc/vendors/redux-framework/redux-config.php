<?php
/**
 * ReduxFramework Sample Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
 */

if (!class_exists('puca_Redux_Framework_Config')) {

    class puca_Redux_Framework_Config
    {
        public $args = array();
        public $sections = array();
        public $theme;
        public $ReduxFramework;

        public function __construct()
        {
            if (!class_exists('ReduxFramework')) {
                return; 
            }  

            add_action('init', array($this, 'initSettings'), 10);
        }

        public function initSettings()
        {
            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        public function setSections()
        {
            global $wp_registered_sidebars;
            $sidebars = array();


            if ( !empty($wp_registered_sidebars) ) {
                foreach ($wp_registered_sidebars as $sidebar) {
                    $sidebars[$sidebar['id']] = $sidebar['name'];
                }
            }
            $columns = array( 
                ''  => esc_html__('Default', 'puca'),
                '1' => esc_html__('1 Column', 'puca'),
                '2' => esc_html__('2 Columns', 'puca'),
                '3' => esc_html__('3 Columns', 'puca'),
                '4' => esc_html__('4 Columns', 'puca'),
                '5' => esc_html__('5 Columns', 'puca'),
                '6' => esc_html__('6 Columns', 'puca')
            );            

            $blog_image_size = array( 
                'post-thumbnail'    => esc_html__('Thumbnail', 'puca'),
                'medium'            => esc_html__('Medium', 'puca'),
                'large'             => esc_html__('Large', 'puca'),
                'full'              => esc_html__('Full', 'puca'),
            );            
            
            // General Settings Tab
            $this->sections[] = array(
                'icon' => 'el-icon-cogs',
                'title' => esc_html__('General', 'puca'),
                'fields' => array(
                    array(
                        'id'        => 'active_theme',
                        'type'      => 'image_select', 
                        'compiler'  => true,
                        'class'     => 'image-large active_skins',
                        'title'     => esc_html__('Activated Skin', 'puca'),
                        'subtitle'  => '<em>'.esc_html__('Choose a skin for your website.', 'puca').'</em>',
                        'options'   => puca_tbay_get_themes(),
                        'default'   => 'fashion'
                    ),
					
                    array(
                        'id'        => 'preload',
                        'type'      => 'switch',
                        'title'     => esc_html__('Preload Website', 'puca'),
                        'default'   => false
                    ),
                    array(
                        'id' => 'select_preloader',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Select Preloader', 'puca'),
                        'subtitle' => esc_html__('Choose a Preloader for your website.', 'puca'),
                        'required'  => array('preload','=',true),
                        'options' => array(
                            'loader1' => array(
                                'title' => 'Loader 1',
                                'img'   => PUCA_ASSETS_IMAGES . '/preloader/loader1.png'
                            ),         
                            'loader2' => array(
                                'title' => 'Loader 2',
                                'img'   => PUCA_ASSETS_IMAGES . '/preloader/loader2.png'
                            ),              
                            'loader3' => array(
                                'title' => 'Loader 3',
                                'img'   => PUCA_ASSETS_IMAGES . '/preloader/loader3.png'
                            ),         
                            'loader4' => array(
                                'title' => 'Loader 4',
                                'img'   => PUCA_ASSETS_IMAGES . '/preloader/loader4.png'
                            ),          
                            'loader5' => array(
                                'title' => 'Loader 5',
                                'img'   => PUCA_ASSETS_IMAGES . '/preloader/loader5.png'
                            ),         
                            'loader6' => array(
                                'title' => 'Loader 6',
                                'img'   => PUCA_ASSETS_IMAGES . '/preloader/loader6.png'
                            ),                              
                            'custom_image' => array(
                                'title' => 'Custom image',
                                'img'   => PUCA_ASSETS_IMAGES . '/preloader/custom_image.png'
                            ),                            
                        ),
                        'default' => 'loader1'
                    ),
                    array(
                        'id' => 'media-preloader',
                        'type' => 'media',
                        'required' => array('select_preloader','=', 'custom_image'),
                        'title' => esc_html__('Upload preloader image', 'puca'),
                        'subtitle' => esc_html__('Image File (.gif)', 'puca'),
                        'desc' =>   sprintf( wp_kses( __('You can download some the Gif images <a target="_blank" href="%1$s">here</a>.', 'puca' ),  array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), 'https://loading.io/' ), 
                    ),
                    array(
                        'id'            => 'config_media',
                        'type'          => 'switch',
                        'title'         => esc_html__('Enable Config Image Size', 'puca'),
                        'subtitle'      => esc_html__('Config Image Size in WooCommerce and Media Setting', 'puca'),
                        'default'       => false
                    ),                    
                    array(
                        'id'            => 'enable_lazyloadimage',
                        'type'          => 'switch',
                        'title'         => esc_html__('Enable LazyLoadImage', 'puca'),
                        'default'       => true
                    ),
                )
            );
            // Header
            $this->sections[] = array(
                'icon' => 'el el-website',
                'title' => esc_html__('Header', 'puca'),
            );

            // Header
            $this->sections[] = array(
                'title' => esc_html__('Header Config', 'puca'),
                'subsection' => true,
                'fields' => array(
                    array(
                        'id' => 'header_type',
                        'type' => 'select',
                        'title' => esc_html__('Select Header Layout', 'puca'),
                        'options' => puca_tbay_get_header_layouts(),
                        'desc' => esc_html__('e.g.: v1,v2..', 'puca'),
                        'default' => 'v1'
                    ),
                    array(
                        'id' => 'media-logo',
                        'type' => 'media',
                        'title' => esc_html__('Upload Logo', 'puca'),
                        'subtitle' => esc_html__('Image File (.png or .gif)', 'puca'),
                    ),
                    array(
                        'id'        => 'logo_img_width',
                        'type'      => 'slider',
                        'title'     => esc_html__('Maximum logo width (px)', 'puca'),
                        "default"   => 160,
                        "min"       => 100,
                        "step"      => 1, 
                        "max"       => 600,
                    ),
                    array(
                        'id'             => 'logo_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('px'),
                        'units_extended' => 'false',
                        'title'          => esc_html__('Logo Padding', 'puca'),
                        'desc'           => esc_html__('Add more spacing around logo.', 'puca'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                    ),                    
                    array(
                        'id'        => 'logo_tablets_img_width',
                        'type'      => 'slider',
                        'title'     => esc_html__('Tablets Logo maximum width (px)', 'puca'),
                        "default"   => 160,
                        "min"       => 100,
                        "step"      => 1,
                        "max"       => 600,
                    ),
                    array(
                        'id'             => 'logo_tablets_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('px'),
                        'units_extended' => 'false',
                        'title'          => esc_html__('Tablets Logo Padding', 'puca'),
                        'desc'           => esc_html__('Add more spacing around logo.', 'puca'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                    ),
                    array(
                        'id' => 'keep_header',
                        'type' => 'switch',
                        'title' => esc_html__('Keep Header', 'puca'),
                        'default' => false
                    ),
                    array(
                        'id' => 'enable_categoires',
                        'type' => 'switch',
                        'required' => array('active_theme','equals','supermaket2'),
                        'title' => esc_html__('Categories in header', 'puca'),
                        'subtitle' => esc_html__('Enable/disable Categories in header', 'puca'),
                        'default' => true
                    ),
                )
            );
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Search Form', 'puca'),
                'fields' => array(
                    array(
                        'id'=>'show_searchform',
                        'type' => 'switch',
                        'title' => esc_html__('Show Search Form', 'puca'),
                        'default' => true,
                        'on' => esc_html__('Yes', 'puca'),
                        'off' => esc_html__('No', 'puca'),
                    ),
                    array(
                        'id'=>'search_type',
                        'type' => 'button_set',
                        'title' => esc_html__('Search Content Type', 'puca'),
                        'required' => array('show_searchform','equals',true),
                        'options' => array('all' => esc_html__('All', 'puca'), 'post' => esc_html__('Post', 'puca'), 'product' => esc_html__('Product', 'puca')),
                        'default' => 'product'
                    ),
                    array(
                        'id'=>'search_category',
                        'type' => 'switch',
                        'title' => esc_html__('Show Categories', 'puca'),
                        'required' => array('search_type', 'equals', array('post', 'product')),
                        'default' => false,
                        'on' => esc_html__('Yes', 'puca'),
                        'off' => esc_html__('No', 'puca'),
						'required' => array('active_theme','=',array('supermaket','supermaket2', 'furniture')),
                    ),
                    array(
                        'id' => 'autocomplete_search',
                        'type' => 'switch',
                        'title' => esc_html__('Autocomplete search?', 'puca'),
                        'required' => array('show_searchform','equals',true),
                        'default' => 1
                    ),
                    array(
                        'id' => 'show_search_product_image',
                        'type' => 'switch',
                        'title' => esc_html__('Show Search Result Image', 'puca'),
                        'required' => array('autocomplete_search', '=', '1'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'show_search_product_price',
                        'type' => 'switch',
                        'title' => esc_html__('Show Search Result Price', 'puca'),
                        'required' => array(array('autocomplete_search', '=', '1'), array('search_type', '=', 'product')),
                        'default' => true
                    ),
                    array(
                        'id' => 'search_max_number_results',
                        'title' => esc_html__('Max number of results show', 'puca'),
                        'required' => array('autocomplete_search', '=', '1'),
                        'default' => 5,
                        'min'   => '2',
                        'step'  => '1',
                        'max'   => '10',
                        'type'  => 'slider'
                    ),  
                )
            );
            // Footer
            $this->sections[] = array(
                'icon' => 'el el-website',
                'title' => esc_html__('Footer', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'footer_type',
                        'type' => 'select',
                        'title' => esc_html__('Select Footer Layout', 'puca'),
                        'options' => puca_tbay_get_footer_layouts(),
 						'default' => 'footer-2'
                    ),
                    array(
                        'id' => 'copyright_text',
                        'type' => 'editor',
                        'title' => esc_html__('Copyright Text', 'puca'),
                        'default' => '<p>Copyright  &#64; 2018 Puca Designed by ThemBay. All Rights Reserved.</p>',
                        'required' => array('footer_type','=','')
                    ),
                    array(
                        'id' => 'back_to_top',
                        'type' => 'switch',
                        'title' => esc_html__('"Back to Top" Button', 'puca'),
                        'subtitle' => esc_html__('Enable or disable "Back to top" button.', 'puca'),
                        'default' => true,
                    ),
					array(
                        'id' => 'category_fixed',
                        'type' => 'switch',
                        'title' => esc_html__('Show Menu Category Fixed', 'puca'),
                        'subtitle' => esc_html__('Toggle whether or not to show "Menu Category Fixed" on your pages.', 'puca'),
                        'default' => true,
						'required' => array('active_theme','=','supermaket')
                    ),
                )
            );



            // Mobile
            $this->sections[] = array(
                'icon' => 'el el-photo',
                'title' => esc_html__('Mobile', 'puca'),
            );

            // Mobile Header settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Mobile Header', 'puca'),
                'fields' => array(
                    array (
                        'id'       => 'header_mobile',
                        'type'     => 'image_select',
                        'title'    => esc_html__('Select Mobile Header Layout', 'puca'),
                        'subtitle' => esc_html__('Set your header design for mobile devices', 'puca'),
                        'class'     => 'image-two',
                        'options'  => array(
                            'center' => array( 
                                'title' => 'Logo-Center',
                                'img' => PUCA_ASSETS_IMAGES . '/header_mobile/header-mobile-center.png',
                            ),
                            'left' => array(
                                'title' => 'Menu-Left',
                                'img' => PUCA_ASSETS_IMAGES . '/header_mobile/header-mobile-left.png',
                            ),
                            'right' => array(
                                'title' => 'Menu-Right',
                                'img' => PUCA_ASSETS_IMAGES . '/header_mobile/header-mobile-right.png',
                            ),
                        ),
                        'default' => 'center',
                    ),
                    array(
                        'id' => 'mobile-logo',
                        'type' => 'media',
                        'title' => esc_html__('Upload Mobile Logo', 'puca'),
                        'subtitle' => esc_html__('Image File (.png or .gif)', 'puca'),
                    ),
                    array(
                        'id'        => 'logo_img_width_mobile',
                        'type'      => 'slider',
                        'title'     => esc_html__('Mobile Logo maximum width (px)', 'puca'),
                        "default"   => 120,
                        "min"       => 50,
                        "step"      => 1,
                        "max"       => 600,
                    ),
                    array(
                        'id'             => 'logo_mobile_padding',
                        'type'           => 'spacing',
                        'mode'           => 'padding',
                        'units'          => array('px'),
                        'units_extended' => 'false',
                        'title'          => esc_html__('Mobile Logo Padding', 'puca'),
                        'desc'           => esc_html__('Add more spacing around logo.', 'puca'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                    ),
                    array(
                        'id'        => 'logo_all_page',
                        'type'      => 'switch',
                        'title'     => esc_html__('Logo all page', 'puca'),
                        'desc'      => esc_html__('Shown logo on all pages', 'puca'),
                        'default'   => false
                    ),
                )
            );

             // Mobile Footer settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Mobile Footer', 'puca'),
                'fields' => array(                
                    array(
                        'id' => 'mobile_footer',
                        'type' => 'switch',
                        'title' => esc_html__('Show Desktop Footer', 'puca'),
                        'default' => false
                    ),                    
                    array(
                        'id' => 'mobile_footer_icon',
                        'type' => 'switch',
                        'title' => esc_html__('Show Mobile Footer Icons', 'puca'),
                        'default' => true
                    ),
                    array(
                        'id' => 'mobile_back_to_top',
                        'type' => 'switch',
                        'title' => esc_html__('Show Mobile "Back to Top" Button', 'puca'),
                        'default' => false
                    ),

                )
            );     


            // Menu mobile social settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Menu mobile', 'puca'),
                'fields' => array(
                    array(
                        'id'       => 'menu_mobile_type',
                        'type'     => 'button_set',
                        'title'    => esc_html__( 'Menu Mobile Type', 'puca' ),
                        'options'  => array(
                            'smart_menu' => 'Smart Menu',
                            'treeview'   => 'Treeview Menu'
                        ),
                        'default'  => 'treeview'
                    ),
                     array(
                        'id' => 'menu_mobile_themes',
                        'type' => 'button_set', 
                        'title' => esc_html__('Menu mobile theme', 'puca'),
                        'required' => array('menu_mobile_type','=','smart_menu'),
                        'options' => array( 
                            'theme-light'       => esc_html__('Light', 'puca'),
                            'theme-dark'        => esc_html__('Dark', 'puca'),
                        ),
                        'default' => 'theme-light'
                    ),
                    array(
                        'id' => 'enable_menu_mobile_effects',
                        'type' => 'switch',
                        'title' => esc_html__('Menu mobile effects ', 'puca'),
                        'required' => array('menu_mobile_type','=','smart_menu'),
                        'default' => false
                    ),                    
                    array(
                        'id' => 'menu_mobile_effects_panels',
                        'type' => 'select', 
                        'title' => esc_html__('Panels effect', 'puca'),
                        'required' => array('enable_menu_mobile_effects','=', true),
                        'options' => array( 
                            'fx-panels-none'            => esc_html__('No effect', 'puca'),
                            'fx-panels-slide-0'         => esc_html__('Slide 0', 'puca'),
                            'no-effect'                 => esc_html__('Slide 30', 'puca'),
                            'fx-panels-slide-100'       => esc_html__('Slide 100', 'puca'),
                            'fx-panels-slide-up'        => esc_html__('Slide uo', 'puca'),
                            'fx-panels-zoom'            => esc_html__('Zoom', 'puca'),
                        ),
                        'default' => 'no-effect'
                    ),                    
                    array(
                        'id' => 'menu_mobile_effects_listitems',
                        'type' => 'select', 
                        'title' => esc_html__('List items effect', 'puca'),
                        'required' => array('enable_menu_mobile_effects','=', true),
                        'options' => array( 
                            'no-effect'                          => esc_html__('No effect', 'puca'),
                            'fx-listitems-drop'         => esc_html__('Drop', 'puca'),
                            'fx-listitems-fade'         => esc_html__('Fade', 'puca'),
                            'fx-listitems-slide'        => esc_html__('slide', 'puca'),
                        ),
                        'default' => 'fx-listitems-fade'
                    ),
                    array(
                        'id'       => 'menu_mobile_title',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Menu mobile Title', 'puca' ),
                        'default'  => esc_html__( 'Menu', 'puca' ),
                    ), 
                    array(
                        'id' => 'enable_menu_mobile_search',
                        'type' => 'switch',
                        'title' => esc_html__('Search menu item', 'puca'),
                        'required' => array('menu_mobile_type','=','smart_menu'),
                        'default' => false
                    ),                                     
                    array(
                        'id'       => 'menu_mobile_search_items',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Search item menu placeholder', 'puca' ),
                        'required' => array('enable_menu_mobile_search','=', true),
                        'default'  => esc_html__( 'Search in menu...', 'puca' ),
                    ),                    
                    array(
                        'id'       => 'menu_mobile_no_esults',
                        'type'     => 'text',
                        'title'    => esc_html__( '“No results” text', 'puca' ),
                        'required' => array('enable_menu_mobile_search','=', true),
                        'default'  => esc_html__( 'No results found.', 'puca' ),
                    ),                    
                    array(
                        'id'       => 'menu_mobile_search_splash',
                        'type'     => 'textarea',
                        'title'    => esc_html__( 'Search text splash', 'puca' ),
                        'required' => array('enable_menu_mobile_search','=', true),
                        'default'  => esc_html__( 'What are you looking for? </br> Start typing to search the menu.', 'puca' ),
                    ),
                    array(
                        'id' => 'enable_menu_mobile_counters',
                        'type' => 'switch',
                        'title' => esc_html__('Menu mobile counters', 'puca'),
                        'required' => array('menu_mobile_type','=','smart_menu'),
                        'default' => false
                    ),                     
                    array(
                        'id' => 'enable_menu_social',
                        'type' => 'switch',
                        'title' => esc_html__('Menu mobile social', 'puca'),
                        'required' => array('menu_mobile_type','=','smart_menu'),
                        'default' => false
                    ), 

                    array(
                        'id'          => 'menu_social_slides',
                        'type'        => 'slides',
                        'title'       => esc_html__( 'Menu mobile social slides', 'puca' ),
                        'desc'        => esc_html__( 'This social will store all slides values into a multidimensional array to use into a foreach loop.', 'puca' ),
                        'class' => 'remove-upload-slides',
                        'show' => array(
                            'title' => true,
                            'description' => false,
                            'url' => true,
                        ),
                        'required' => array('enable_menu_social','=', true),
                        'placeholder'   => array(
                            'title'      => esc_html__( 'Enter icon name', 'puca' ),
                            'url'       => esc_html__( 'Link icon', 'puca' ),
                        ),
                    ),
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),

                    array(
                        'id'       => 'menu_mobile_one_select',
                        'type'     => 'select',
                        'data'     => 'menus',
                        'title'    => esc_html__( 'Main menu', 'puca' ),
                        'subtitle' => '<em>'.esc_html__('Tab 1 menu option', 'puca').'</em>',
                        'desc'     => esc_html__( 'Select the menu you want to display.', 'puca' ),
                    ),
                    array(
                        'id'       => 'menu_mobile_tab_one',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Tab 1 title', 'puca' ),
                        'required' => array('enable_menu_second','=', true),
                        'default'  => esc_html__( 'Menu', 'puca' ),
                    ), 
                    array(
                        'id'       => 'menu_mobile_tab_one_icon',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Tab 1 icon', 'puca' ),
                        'required' => array('enable_menu_second','=', true),
                        'desc'     => esc_html__( 'Enter icon name of font: awesome, simplelineicons', 'puca' ),
                        'default'  => 'icon-menu icons',
                    ), 
                    array(
                        'id' => 'enable_menu_second',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Tab 2', 'puca'),
                        'required' => array('menu_mobile_type','=','smart_menu'),
                        'default' => false
                    ),    

                    array(
                        'id'       => 'menu_mobile_tab_scond',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Tab 2 title', 'puca' ),
                        'required' => array('enable_menu_second','=', true),
                        'default'  => esc_html__( 'Categories', 'puca' ),
                    ), 

                    array(
                        'id'       => 'menu_mobile_second_select',
                        'type'     => 'select',
                        'data'     => 'menus',
                        'title'    => esc_html__( 'Tab 2 menu option', 'puca' ),
                        'required' => array('enable_menu_second','=', true),
                        'desc'     => esc_html__( 'Select the menu you want to display.', 'puca' ),
                    ),
                    array(
                        'id'       => 'menu_mobile_tab_second_icon',
                        'type'     => 'text',
                        'title'    => esc_html__( 'Tab 2 icon', 'puca' ),
                        'required' => array('enable_menu_second','=', true),
                        'desc'     => esc_html__( 'Enter icon name of font: awesome, simplelineicons', 'puca' ),
                        'default'  => 'icon-grid icons',
                    ), 
                )
            );
        

            // Mobile Woocommerce settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Mobile WooCommerce', 'puca'),
                'fields' => array(                
                    array(
                        'id' => 'mobile_product_number',
                        'type' => 'image_select',
                        'title' => esc_html__('Product Column in Shop page', 'puca'),
                        'options' => array(
                            'one' => array(
                                'title' => 'One Column',
                                'alt'   => 'One Column',
                                'img'   => PUCA_ASSETS_IMAGES . '/product_number_mobile/one_column.jpg'
                            ),                            
                            'two' => array(
                                'title' => 'Two Columns',
                                'alt'   => 'Two Columns',
                                'img'   => PUCA_ASSETS_IMAGES . '/product_number_mobile/two_columns.jpg'
                            ),
                        ),
                        'default' => 'two'
                    ),
					array(
                        'id' => 'enable_add_cart_mobile',
                        'type' => 'switch',
                        'title' => esc_html__('Show "Add to Cart" Button', 'puca'),
                        'subtitle' => esc_html__('Enable or disable in Home and Shop page', 'puca'),
                        'default' => false
                    ),
					array(
                        'id' => 'enable_quantity_mobile',
                        'type' => 'switch',
                        'title' => esc_html__('Show Quantity', 'puca'),
                        'subtitle' => esc_html__('Enable or disable in single product', 'puca'),
                        'default' => false
                    ),
                    array(
                        'id' => 'redirect_add_to_cart',
                        'type' => 'switch',
                        'title' => esc_html__('Enable/Disable redirect add to cart', 'puca'),
                        'required'  => array('enable_buy_now','=', false),
                        'subtitle' => esc_html__('Redirect add to cart to page cart in single product', 'puca'),
                        'default' => false
                    ),    
                )
            );

            // Blog settings
            $this->sections[] = array(
                'icon' => 'el el-pencil',
                'title' => esc_html__('Blog', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'show_blog_breadcrumb',
                        'type' => 'switch',
                        'title' => esc_html__('Breadcrumb', 'puca'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'blog_breadcrumb_layout',
                        'type' => 'image_select',
                        'class'     => 'image-two',
                        'compiler' => true,
                        'title' => esc_html__('Select Breadcrumb Blog Layout', 'puca'),
                        'required' => array('show_blog_breadcrumb','=',1),
                        'options' => array(                        
                            'image' => array(
                                'title' => 'Background Image',
                                'alt'   => 'Background Image',
                                'img'   => PUCA_ASSETS_IMAGES . '/breadcrumbs/image.jpg'
                            ),
                            'color' => array(
                                'title' => 'Background color',
                                'alt'   => 'Breadcrumb Color',
                                'img'   => PUCA_ASSETS_IMAGES . '/breadcrumbs/color.jpg'
                            ),
                            'text'=> array(
                                'title' => 'Text Only',
                                'alt'   => 'Breadcrumb Text Only',
                                'img'   => PUCA_ASSETS_IMAGES . '/breadcrumbs/text_only.jpg'
                            ),
                        ),
                        'default' => 'color'
                    ),
                    array (
                        'title' => esc_html__('Breadcrumb Background Color', 'puca'),
                        'id' => 'blog_breadcrumb_color',
                        'type' => 'color',
                        'default' => '#fafafa',
                        'transparent' => false,
                        'required' => array('blog_breadcrumb_layout','=',array('default','color')),
                    ),
                    array(
                        'id' => 'blog_breadcrumb_image',
                        'type' => 'media',
                        'title' => esc_html__('Breadcrumb Background Image', 'puca'),
                        'subtitle' => esc_html__('Image File (.png or .jpg)', 'puca'),
                        'default'  => array(
                            'url'=> get_template_directory_uri() . '/images/breadcrumbs-blog.jpg'
                        ),
                        'required' => array('blog_breadcrumb_layout','=','image'),
                    ),
                )
            );
            // Archive Blogs settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Blog Article', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'blog_archive_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Blog Layout', 'puca'),
                        'options' => array(
                            'main-v1' => array(
                                'title' => 'Blog Main v1',
                                'alt' => 'Blog Main v1',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/layout01.png'
                            ),                             
                            'main-v2' => array(
                                'title' => 'Blog Main v2',
                                'alt' => 'Blog Main v2',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/layout02.png'
                            ),                              
                            'main-v3' => array(
                                'title' => 'Blog Main v3',
                                'alt' => 'Blog v3',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/layout03.png'
                            ),                               
                            'main-v4' => array(
                                'title' => 'Blog Main v4',
                                'alt' => 'Blog v4',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/layout04.png'
                            ),                                
                            'left-main-v1' => array(
                                'title' => 'Blog Left Main v1',
                                'alt' => 'Blog Left Main v1',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_left01.png'
                            ),                                 
                            'main-right-v1' => array(
                                'title' => 'Blog Main Right v1',
                                'alt' => 'Blog Main Right v1',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_right01.png'
                            ),                             
                            'left-main-v2' => array(
                                'title' => 'Blog Left Main v2',
                                'alt' => 'Blog Left Main v2',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_left02.png'
                            ),                             
                            'main-right-v2' => array(
                                'title' => 'Blog Main Right v2',
                                'alt' => 'Blog Main Right v2',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_right02.png'
                            ),                                
                            'left-main-v3' => array(
                                'title' => 'Blog Left Main v3',
                                'alt' => 'Blog Left Main v3',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_left03.png'
                            ),                               
                            'main-right-v3' => array(
                                'title' => 'Blog Main Right v3',
                                'alt' => 'Blog Main Right v3',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_right03.png'
                            ),                               
                            'left-main-v4' => array(
                                'title' => 'Blog Left Main v4',
                                'alt' => 'Blog Left Main v4',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_left04.png'
                            ),                              
                            'main-right-v4' => array(
                                'title' => 'Blog Main Right v4',
                                'alt' => 'Blog Main Right v4',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_right04.png'
                            ),                              
                            'left-main-v5' => array(
                                'title' => 'Blog Left Main v5',
                                'alt' => 'Blog Left Main v5',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_left05.png'
                            ),                              
                            'main-right-v5' => array(
                                'title' => 'Blog Main Right v5',
                                'alt' => 'Blog Main Right v5',
                                'img' => PUCA_ASSETS_IMAGES . '/blog_archives/sidebar_right05.png'
                            )                          
                        ),
                        'default' => 'main-right-v1'
                    ),
                    array(
                        'id' => 'blog_archive_left_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Left Sidebar', 'puca'),
                        'options' => $sidebars,
                        'default' => 'blog-left-sidebar'
                    ),
                    array(
                        'id' => 'blog_archive_right_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Right Sidebar', 'puca'),
                       'options' => $sidebars,
                        'default' => 'blog-right-sidebar'
                        
                    ),                
                    array(
                        'id' => 'blog_archive_left_sidebar45',
                        'type' => 'select',
                        'title' => esc_html__('V45 Left Sidebar', 'puca'),
                        'options' => $sidebars,
						'default' => 'blog-left-sidebar-45'
                    ),
                    array(
                        'id' => 'blog_archive_right_sidebar45',
                        'type' => 'select',
                        'title' => esc_html__(' V45 Right Sidebar', 'puca'),
                        'options' => $sidebars,
                        'default' => 'blog-right-sidebar-45'
                        
                    ),
                    array(
                        'id' => 'blog_columns',
                        'type' => 'select',
                        'title' => esc_html__('Columns', 'puca'),
                        'options' => $columns,
                        'default' => ''
                    ),                    
                    array(
                        'id' => 'blog_image_sizes',
                        'type' => 'select',
                        'title' => esc_html__('Image Size', 'puca'),
                        'options' => $blog_image_size,
                        'default' => 'large'
                    ),

                )
            );
            // Single Blogs settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Blog Post', 'puca'),
                'fields' => array(
                    
                    array(
                        'id' => 'blog_single_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Blog Single Layout', 'puca'),
                        'subtitle' => esc_html__('Select the variation you want to apply on your store.', 'puca'),
                        'options' => array(
                            'main' => array(
                                'title' => 'Main Only',
                                'alt' => 'Main Only',
                                'img' => PUCA_ASSETS_IMAGES . '/screen1.png'
                            ),
                            'left-main' => array(
                                'title' => 'Left - Main Sidebar',
                                'alt' => 'Left - Main Sidebar',
                                'img' => PUCA_ASSETS_IMAGES . '/screen2.png'
                            ),
                            'main-right' => array(
                                'title' => 'Main - Right Sidebar',
                                'alt' => 'Main - Right Sidebar',
                                'img' => PUCA_ASSETS_IMAGES . '/screen3.png'
                            ),
                        ),
                        'default' => 'main-right'
                    ),
                    array(
                        'id' => 'blog_single_left_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Single Blog Left Sidebar', 'puca'),
                        'subtitle' => esc_html__('Choose a sidebar for left sidebar.', 'puca'),
                         'options'   => $sidebars,
                        'default'   => 'blog-left-sidebar'
                    ),
                    array(
                        'id' => 'blog_single_right_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Single Blog Right Sidebar', 'puca'),
                        'options'   => $sidebars,
                        'default'   => 'blog-right-sidebar'
                    ),
                    array(
                        'id' => 'show_blog_social_share',
                        'type' => 'switch',
                        'title' => esc_html__('Show Social Share', 'puca'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'show_blog_releated',
                        'type' => 'switch',
                        'title' => esc_html__('Show Releated Posts', 'puca'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'number_blog_releated',
                        'type' => 'text',
                        'title' => esc_html__('Number of related posts to show', 'puca'),
                        'required' => array('show_blog_releated', '=', '1'),
                        'default' => 4,
                        'min' => '1',
                        'step' => '1',
                        'max' => '20',
                        'type' => 'slider'
                    ),
                    array(
                        'id' => 'releated_blog_columns',
                        'type' => 'select',
                        'title' => esc_html__('Releated Blogs Columns', 'puca'),
                        'required' => array('show_blog_releated', '=', '1'),
                        'options' => $columns,
                        'default' => 2
                    ),

                )
            );

            // Woocommerce
            $this->sections[] = array(
                'icon' => 'el el-shopping-cart',
                'title' => esc_html__('Woocommerce', 'puca'),
                'fields' => array(          
                    array(
                        'title'    => esc_html__('Sale Tag Settings', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Predefined Format', 'puca').'</em>',
                        'id'       => 'sale_tags',
                        'type'     => 'radio',
                        'options'  => array(
                            'Sale!' => esc_html__('Sale!' ,'puca'),
                            'Save {percent-diff}%' => esc_html__('Save {percent-diff}% (e.g "Save 50%")' ,'puca'),
                            'Save {symbol}{price-diff}' => esc_html__('Save {symbol}{price-diff} (e.g "Save $50")' ,'puca'),
                            'custom' => esc_html__('Custom Format (e.g -50%, -$50)' ,'puca')
                        ),
                        'default' => 'custom'
                    ),
                    array(
                        'id'        => 'sale_tag_custom',
                        'type'      => 'text',
                        'title'     => esc_html__( 'Custom Format', 'puca' ),
                        'desc'      => esc_html__('{price-diff} inserts the dollar amount off.', 'puca'). '</br>'.
                                       esc_html__('{percent-diff} inserts the percent reduction (rounded).', 'puca'). '</br>'.
                                       esc_html__('{symbol} inserts the Default currency symbol.', 'puca'), 
                        'required'  => array('sale_tags','=', 'custom'),
                        'default'   => '-{percent-diff}%'
                    ), 
                    array(
                        'id' => 'enable_label_featured',
                        'type' => 'switch',
                        'title' => esc_html__('Label featured', 'puca'),
                        'subtitle' => esc_html__('Enable/Disable label featured', 'puca'),
                        'default' => true
                    ),   
                    array(
                        'id'        => 'custom_label_featured',
                        'type'      => 'text',
                        'title'     => esc_html__( 'Custom Label featured', 'puca' ),
                        'required'  => array('enable_label_featured','=', true),
                        'default'   => esc_html__('Hot', 'puca')
                    ), 
                    array(
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),        
                    array(
                        'id' => 'enable_woocommerce_catalog_mode',
                        'type' => 'switch',
                        'title' => esc_html__('Show WooCommerce Catalog Mode', 'puca'),
                        'default' => false
                    ),                     
                    array(
                        'id' => 'ajax_update_quantity',
                        'type' => 'switch',
                        'title' => esc_html__('Enable/Disable Ajax update quantity', 'puca'),
                        'subtitle' => esc_html__('Enable/Disable Ajax update quantity in Cart Page', 'puca'),
                        'default' => true
                    ),                     
					array(
                        'id' => 'enable_variation_selector',
                        'type' => 'switch',
                        'title' => esc_html__('Hide Variation Selector on HomePage and Shop page', 'puca'),
                        'default' => false
                    ),   					
                    array(
                        'id' => 'show_woocommerce_password_strength',
                        'type' => 'switch',
                        'title' => esc_html__('Show Password Strength Meter', 'puca'),
                        'default' => true
                    ),                    
                     array(
                        'id' => 'woo_mini_cart_position',
                        'type' => 'select', 
                        'title' => esc_html__('Mini-Cart Position', 'puca'),
                        'options' => array( 
                            'top'        => esc_html__('Top', 'puca'),
                            'left'       => esc_html__('Left', 'puca'),
                            'right'      => esc_html__('Right', 'puca'),
                            'bottom'     => esc_html__('Bottom', 'puca'),
                            'popup'      => esc_html__('Popup', 'puca'),
                            'no-popup'   => esc_html__('None Popup', 'puca')
                        ),
                        'default' => 'right'
                    ),
                    array(
                        'id' => 'show_product_breadcrumb',
                        'type' => 'switch',
                        'title' => esc_html__('Breadcrumb', 'puca'),
                        'default' => true
                    ),
                    array(
                        'id' => 'product_breadcrumb_layout',
                        'type' => 'image_select',
                        'class'     => 'image-two',
                        'compiler' => true,
                        'title' => esc_html__('Select Breadcrumb WooCommerce Layout', 'puca'),
                        'required' => array('show_product_breadcrumb','=',1),
                        'options' => array(                          
                            'image' => array(
                                'title' => 'Background Image',
                                'alt'   => 'Background Image',
                                'img'   => PUCA_ASSETS_IMAGES . '/breadcrumbs/image.jpg'
                            ),
                            'color' => array(
                                'title' => 'Background color',
                                'alt'   => 'Breadcrumb Color',
                                'img'   => PUCA_ASSETS_IMAGES . '/breadcrumbs/color.jpg'
                            ),
                            'text'=> array(
                                'title' => 'Text Only',
                                'alt'   => 'Breadcrumb Text Only',
                                'img'   => PUCA_ASSETS_IMAGES . '/breadcrumbs/text_only.jpg'
                            ),
                        ),
                        'default' => 'color'
                    ),
                    array (
                        'title' => esc_html__('Breadcrumb Background Color', 'puca'),
                        'subtitle' => '<em>'.esc_html__('The Breadcrumb background color of the site.', 'puca').'</em>',
                        'id' => 'woo_breadcrumb_color',
                        'required' => array('product_breadcrumb_layout','=',array('default','color')),
                        'type' => 'color',
                        'default' => '#f4f9fc',
                        'transparent' => false,
                    ),
                    array(
                        'id' => 'woo_breadcrumb_image',
                        'type' => 'media',
                        'title' => esc_html__('Breadcrumb Background', 'puca'),
                        'subtitle' => esc_html__('Upload a .jpg or .png image that will be your Breadcrumb.', 'puca'),
                        'required' => array('product_breadcrumb_layout','=','image'),
                        'default'  => array( 
                            'url'=> get_template_directory_uri() . '/images/breadcrumbs-woo.jpg'
                        ),
                    ),
                )
            );

            // Archive settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Product archives', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'product_archive_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Product Layout', 'puca'),
                        'options' => array(
                            'shop-left' => array(
                                'title' => 'Shop Left',
                                'alt' => 'Left Sidebar',
                                'img' => PUCA_ASSETS_IMAGES . '/product_archives/shop_left.png'
                            ),                                  
                            'shop-right' => array(
                                'title' => 'Shop Right',
                                'alt' => 'Shop Right',
                                'img' => PUCA_ASSETS_IMAGES . '/product_archives/shop_right.png'
                            ),                      
                            'shop-des-left' => array(
                                'title' => 'Shop Left with Descreption',
                                'alt' => 'Shop Left with Descreption',
                                'img' => PUCA_ASSETS_IMAGES . '/product_archives/shop_left_with_descreption.png'
                            ),                              
                            'shop-des-right' => array(
                                'title' => 'Shop Descreption Width Right Sidebar',
                                'alt' => 'Shop Descreption Width Right Sidebar',
                                'img' => PUCA_ASSETS_IMAGES . '/product_archives/shop_right_with_descreption.png'
                            ),                            
                            'full-width-wide' => array(
                                'title' => 'Full Width Wide',
                                'alt' => 'Full Width Wide',
                                'img' => PUCA_ASSETS_IMAGES . '/product_archives/full_width_wide.png'
                            ),                            
                            'full-width' => array(
                                'title' => 'Full Width',
                                'alt' => 'Full Width',
                                'img' => PUCA_ASSETS_IMAGES . '/product_archives/full_width.png'
                            ),                            
                            'multi-viewed-left' => array(
                                'title' => 'Multi Viewed Left',
                                'alt'   => 'Shop Multi Viewed Left',
                                'img'   => PUCA_ASSETS_IMAGES . '/product_archives/multi_viewed_left.png'
                            ),                            
                            'multi-viewed-right' => array(
                                'title' => 'Multi Viewed Right',
                                'alt'   => 'Shop Multi Viewed Right',
                                'img'   => PUCA_ASSETS_IMAGES . '/product_archives/multi_viewed_right.png'
                            ),                            
                            'filter-bar' => array(
                                'title' => 'Filter Bar',
                                'alt'   => 'Filter Bar',
                                'img'   => PUCA_ASSETS_IMAGES . '/product_archives/filter_bar.png'
                            ),                            
                            'canvas-left-sidebar' => array(
                                'title' => 'Canvas Left Sidebar',
                                'alt'   => 'Shop Canvas Left Sidebar',
                                'img'   => PUCA_ASSETS_IMAGES . '/product_archives/canvas_left_sidebar.png'
                            ),                            
                            'canvas-right-sidebar' => array(
                                'title' => 'Canvas Right Sidebar',
                                'alt'   => 'Shop Canvas Right Sidebar',
                                'img'   => PUCA_ASSETS_IMAGES . '/product_archives/canvas_right_sidebar.png'
                            ),
                        ),
                        'default' => 'multi-viewed-right'
                    ),
                    array(
                        'id' => 'enable_cat_title_des_img',
                        'type' => 'switch',
                        'title' => esc_html__('Title, Description, Image in category', 'puca'),
                        'subtitle' => esc_html__('Enable/Disable title, description, image', 'puca'),
                        'default' => false
                    ),   
                    array(
                        'id' => 'product_archive_left_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Left Sidebar', 'puca'),
                        'options' => $sidebars,
                        'default' => 'product-left-sidebar'
                    ),
                    array(
                        'id' => 'product_archive_right_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Right Sidebar', 'puca'),
                       'options' => $sidebars,
                        'default' => 'product-right-sidebar'
                    ),
                    array(
                        'id' => 'product_display_mode',
                        'type' => 'select',
                        'title' => esc_html__('Display Mode', 'puca'),
                        'options' => array('grid' => esc_html__('Grid', 'puca'), 'list' => esc_html__('List', 'puca')),
                        'default' => 'grid'
                    ),
                    array(
                        'id' => 'number_products_per_page',
                        'type' => 'text',
                        'title' => esc_html__('Number of Products Per Page', 'puca'),
                        'default' => 12,
                        'min' => '1',
                        'step' => '1',
                        'max' => '100',
                        'type' => 'slider'
                    ),
                    array(
                        'id' => 'product_columns',
                        'type' => 'select',
                        'title' => esc_html__('Product Columns', 'puca'),
                        'options' => $columns,
                        'default' => 3
                    ),
                    array(
                        'id' => 'product_pagination_style',
                        'type' => 'select',
                        'title' => esc_html__('Product Pagination Style', 'puca'),
                        'options' => array( 
                            'number' => esc_html__('Pagination Number', 'puca'),
                            'loadmore'  => esc_html__('Load More Button', 'puca'),  
                        ),
                        'default' => 'loadmore' 
                    ),
                    array(
                        'id' => 'product_type_fillter',
                        'type' => 'switch',
                        'title' => esc_html__('Product type fillter', 'puca'),
                        'default' => 0
                    ),                     
                    array(
                        'id' => 'product_per_page_fillter',
                        'type' => 'switch',
                        'title' => esc_html__('Numbered Product on page', 'puca'),
                        'default' => 0
                    ),                       
                    array(
                        'id' => 'product_category_fillter',
                        'type' => 'switch',
                        'title' => esc_html__('Product category fillter', 'puca'),
                        'default' => 0
                    ),                    
                    array(
                        'id' => 'show_swap_image',
                        'type' => 'switch',
                        'title' => esc_html__('Show Second Image (Hover)', 'puca'),
                        'default' => 1
                    ),
                )
            );
            // Product Page
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Single product sample layout', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'product_single_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Select Single Product Layout', 'puca'),
                        'options' => array(
                            'full-width-vertical-left' => array(
                                'title' => 'Full Width Image Vertical Left',
                                'alt' => 'Full Width Image Vertical Left',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/product_vertical_left.png'
                            ),                              
                            'full-width-vertical-right' => array(
                                'title' => 'Full Width Image Vertical Right',
                                'alt' => 'Full Width Image Vertical Right',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/product_vertical_right.png'
                            ),                            
                            'full-width-horizontal-top' => array(
                                'title' => 'Full Width Image Horizontal Top',
                                'alt' => 'Full Width Image Horizontal Top',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/product_horizontal_top.png'
                            ),                             
                            'full-width-horizontal-bottom' => array(
                                'title' => 'Full Width Image Horizontal Bottom',
                                'alt' => 'Full Width Image Horizontal Bottom',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/product_horizontal_bottom.png'
                            ),                            
                            'full-width-stick' => array(
                                'title' => 'Full Width Image Stick',
                                'alt' => 'Full Width Image Stick',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/product_image_stick.png'
                            ),                            
                            'full-width-gallery' => array(
                                'title' => 'Full Width Image Gallery',
                                'alt' => 'Full Width Image gallery',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/product_image_gallery.png'
                            ),                                                        
                            'full-width-slide' => array(
                                'title' => 'Full Width Image Slide',
                                'alt' => 'Full Width Image Slide',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/product_image_slide.png'
                            ),                            
                            'full-width-carousel' => array(
                                'title' => 'Full Width Image Carousel',
                                'alt' => 'Full Width Image Carousel',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/full_width_carousel.png'
                            ),
                            'left-main' => array(
                                'title' => 'Left - Main Sidebar',
                                'alt' => 'Left - Main Sidebar',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/product_left_sidebar.png'
                            ),
                            'main-right' => array(
                                'title' => 'Main - Right Sidebar',
                                'alt' => 'Main - Right Sidebar',
                                'img' => PUCA_ASSETS_IMAGES . '/product_single/product_right_sidebar.png'
                            ),
                        ),
                        'default' => 'full-width-vertical-left'
                    ),
                    array(
                        'id' => 'product_single_left_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Left Sidebar', 'puca'),
                         'options' => $sidebars,
                        'default' => 'product-left-sidebar'
                    ),
                    array(
                        'id' => 'product_single_right_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Right Sidebar', 'puca'),
                        'options' => $sidebars,
                        'default' => 'product-right-sidebar'
                    ),

                )
            );

            // Product Page
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Single product custom layout', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'thumbnail_image',
                        'type' => 'select',
                        'title' => esc_html__('Thumbnail image', 'puca'),
                        'options' => array(
                                'default'           => 'Default',
                                'horizontal-top'    => 'Horizontal Top',
                                'horizontal-bottom' => 'Horizontal Bottom',
                                'vertical-left'     => 'Vertical Left',
                                'vertical-right'    => 'Vertical Right',
                                'stick'             => 'Stick',
                                'gallery'           => 'Gallery',
                                'slide'             => 'Slide',
                                'carousel'          => 'Carousel',
                        ),
                        'default' => 'default'
                    ),                  
                    array(
                        'id' => 'style_single_tabs_style',
                        'type' => 'select',
                        'title' => esc_html__('Single Product Tabs', 'puca'),
                        'options' => array(
                                'default'          => 'Default',
                                'tbhorizontal'     => 'Horizontal',
                                'tbvertical'       => 'Vertical',
                                'accordion'        => 'Accordion ',
                                'fulltext'         => 'Full text'
                        ),
                        'default' => 'default'
                    ),                    
                    array(
                        'id' => 'single_tabs_position',
                        'type' => 'select', 
                        'title' => esc_html__('Single Product Tabs Position', 'puca'),
                        'options' => array(
                                'default'          => 'Default',
                                'bottom'           => 'Bottom',
                                'right'            => 'Right',
                        ),
                        'default' => 'default'
                    ),

                )

            );

            // Product Page
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Other Single Product', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'ajax_single_add_to_cart',
                        'type' => 'switch',
                        'title' => esc_html__('Enable/Disable Ajax add to cart', 'puca'),
                        'subtitle' => esc_html__('Enable/Disable Ajax add to cart in Single Product Page', 'puca'),
                        'default' => true
                    ), 
                    array( 
                        'id'   => 'opt-divide',
                        'class' => 'big-divide',
                        'type' => 'divide'
                    ),
                    array(
                        'id' => 'enable_total_sales',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Total Sales', 'puca'),
                        'default' => true
                    ),                     
                    array(
                        'id' => 'enable_buy_now',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Buy Now', 'puca'),
                        'default' => false
                    ), 
                    array( 
                        'id' => 'redirect_buy_now',
                        'required' => array('enable_buy_now','=',true),
                        'type' => 'button_set',
                        'title' => esc_html__('Redirect to page after Buy Now', 'puca'),
                        'options' => array( 
                                'cart'          => esc_html__('Page Cart', 'puca'),
                                'checkout'      => esc_html__('Page CheckOut', 'puca'),
                        ),
                        'default' => 'cart'
                    ),  
                   array(
                        'id' => 'show_product_nav',
                        'type' => 'switch', 
                        'title' => esc_html__('Show Product navigator', 'puca'),
                        'default' => true
                    ),                   
                    array(
                        'id' => 'show_product_menu_bar',
                        'type' => 'switch',
                        'title' => esc_html__('Show Menu Bar', 'puca'),
                        'default' => false
                    ),                    
                    array(
                        'id' => 'show_product_social_share',
                        'type' => 'switch',
                        'title' => esc_html__('Show Social Share', 'puca'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'show_product_review_tab',
                        'type' => 'switch',
                        'title' => esc_html__('Show Product Review Tab', 'puca'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'show_product_releated',
                        'type' => 'switch',
                        'title' => esc_html__('Show Products Releated', 'puca'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'show_product_upsells',
                        'type' => 'switch',
                        'title' => esc_html__('Show Products upsells', 'puca'),
                        'default' => 1
                    ),                    
                    array(
                        'id' => 'show_product_countdown',
                        'type' => 'switch',
                        'title' => esc_html__('Show Products Countdown', 'puca'),
                        'default' => false
                    ),
                    array(
                        'id' => 'number_product_thumbnail',
                        'title' => esc_html__('Number Images Thumbnail to show', 'puca'),
                        'default' => 4,
                        'min'   => '2',
                        'step'  => '1',
                        'max'   => '5',
                        'type'  => 'slider'
                    ),  
                    array(
                        'id' => 'number_product_releated',
                        'title' => esc_html__('Number of related products to show', 'puca'),
                        'default' => 8,
                        'min' => '1',
                        'step' => '1',
                        'max' => '20',
                        'type' => 'slider'
                    ),                    
                    array(
                        'id' => 'releated_product_columns',
                        'type' => 'select',
                        'title' => esc_html__('Releated Products Columns', 'puca'),
                        'options' => $columns,
                        'default' => 4
                    ),
                    array(
                        'id'       => 'html_before_add_to_cart_btn',
                        'type'     => 'textarea',
                        'title'    => esc_html__( 'HTML before Add To Cart button (Global)', 'puca' ),
                        'desc'     => esc_html__( 'Enter HTML and shortcodes that will show before Add to cart selections.', 'puca' ),
                    ),
                    array(
                        'id'       => 'html_after_add_to_cart_btn',
                        'type'     => 'textarea',
                        'title'    => esc_html__( 'HTML after Add To Cart button (Global)', 'puca' ),
                        'desc'     => esc_html__( 'Enter HTML and shortcodes that will show after Add to cart button.', 'puca' ),
                    ),
                )

            );

            // Portfolio settings
            $this->sections[] = array(
                'icon' => 'el el-briefcase',
                'title' => esc_html__('Portfolio', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'show_portfolio_breadcrumb',
                        'type' => 'switch',
                        'title' => esc_html__('Breadcrumb', 'puca'),
                        'default' => false
                    ),
                    array(
                        'id' => 'portfolio_breadcrumb_layout',
                        'type' => 'image_select',
                        'class'     => 'image-two',
                        'compiler' => true,
                        'title' => esc_html__('Breadcrumb Portfolio Layout', 'puca'),
                        'subtitle' => esc_html__('Select the layout you want to apply on your Breadcrumb portfolio layout.', 'puca'),
                        'required' => array('show_portfolio_breadcrumb','=',1),
                        'options' => array(                          
                            'image' => array(
                                'title' => 'Background Image',
                                'alt'   => 'Background Image',
                                'img'   => PUCA_ASSETS_IMAGES . '/breadcrumbs/image.jpg'
                            ),
                            'color' => array(
                                'title' => 'Background color',
                                'alt'   => 'Breadcrumb Color',
                                'img'   => PUCA_ASSETS_IMAGES . '/breadcrumbs/color.jpg'
                            ),
                            'text'=> array(
                                'title' => 'Text Only',
                                'alt'   => 'Breadcrumb Text Only',
                                'img'   => PUCA_ASSETS_IMAGES . '/breadcrumbs/text_only.jpg'
                            ),
                        ),
                        'default' => 'color'
                    ),
                    array (
                        'title' => esc_html__('Breadcrumb Background Color', 'puca'),
                        'subtitle' => '<em>'.esc_html__('The Breadcrumb background color of the site.', 'puca').'</em>',
                        'id' => 'portfolio_breadcrumb_color',
                        'required' => array('portfolio_breadcrumb_layout','=',array('default','color')),
                        'type' => 'color',
                        'default' => '#fafafa',
                        'transparent' => false,
                    ),
                    array(
                        'id' => 'portfolio_breadcrumb_image',
                        'type' => 'media',
                        'title' => esc_html__('Breadcrumb Background', 'puca'),
                        'subtitle' => esc_html__('Upload a .jpg or .png image that will be your Breadcrumb.', 'puca'),
                        'required' => array('portfolio_breadcrumb_layout','=','image'),
                        'default'  => array(
                            'url'=> get_template_directory_uri() . '/images/breadcrumbs-portfolio.jpg'
                        ),
                    ),
                )
            );

            // Archive Portfolio settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Portfolio Archives', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'portfolio_columns',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Columns', 'puca'),
                        'subtitle' => esc_html__('Select the variation you want to apply on your store.', 'puca'),
                        'options' => array(
                            '2' => array(
                                'title' => '2 Columns',
                                'alt' => '2 Columns',
                                'img' => PUCA_ASSETS_IMAGES . '/portfolio_archives/02_columns.png'
                            ),                                 
                            '3' => array(
                                'title' => '3 Columns',
                                'alt' => '3 Columns',
                                'img' => PUCA_ASSETS_IMAGES . '/portfolio_archives/03_columns.png'
                            ),                                
                            '4' => array(
                                'title' => '4 Columns',
                                'alt' => '4 Columns',
                                'img' => PUCA_ASSETS_IMAGES . '/portfolio_archives/04_columns.png'
                            ),                                 
                            '5' => array(
                                'title' => '5 Columns',
                                'alt' => '5 Columns',
                                'img' => PUCA_ASSETS_IMAGES . '/portfolio_archives/05_columns.png'
                            ),                                                                               
                        ),
                        'default' => '4'
                    ),
                    array(
                        'id'            => 'portfolio_per_page',
                        'type'          => 'slider',
                        'title'         => esc_html__('Projects per page', 'puca'),
                        'desc'          => esc_html__('Amount of projects per page on portfolio page', 'puca'),
                        "default"       => 12,
                        "min"           => 4,
                        "step"          => 1,
                        "max"           => 48,
                        'display_value' => 'text'
                    ),
                    array(
                        'id'        => 'portfolio_full_wide',
                        'type'      => 'switch',
                        'title'     => esc_html__('Portfolio Full Wide', 'puca'),
                        'default'   => false
                    ),                    
                    array(
                        'id'        => 'portfolio_random_size_image',
                        'type'      => 'switch',
                        'title'     => esc_html__('Random size image', 'puca'),
                        'default'   => false
                    ),

                )
            );

            // Single Portfolio settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Portfolio Single', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'portfolio_single_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Single Portfolio Layout', 'puca'),
                        'options' => array(
                            'stick' => array(
                                'title' => 'Stick',
                                'alt' => 'Stick',
                                'img' => PUCA_ASSETS_IMAGES . '/portfolio_single/single_po_stick.png'
                            ),                                 
                            'carousel' => array(
                                'title' => 'Carousel',
                                'alt' => 'Carousel',
                                'img' => PUCA_ASSETS_IMAGES . '/portfolio_single/single_po_carousel.png'
                            ),                                
                            'full' => array(
                                'title' => 'Full',
                                'alt' => 'Full',
                                'img' => PUCA_ASSETS_IMAGES . '/portfolio_single/single_po_full.png'
                            ),                                                                                                            
                        ),
                        'default' => 'carousel'
                    ),
                    array(
                        'id' => 'show_portfolio_social_share',
                        'type' => 'switch',
                        'title' => esc_html__('Show Social Share', 'puca'),
                        'default' => 1
                    ),
                )
            );

            // Other Pages settings
            $this->sections[] = array(
                'icon' => 'el el-list-alt',
                'title' => esc_html__('Other Pages', 'puca'),
            );

            // Page 404 settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Page 404', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'page_404_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Page 404 Layout', 'puca'),
                        'options' => array(
                            'v1' => array(
                                'title' => 'v1',
                                'alt' => 'v1',
                                'img' => PUCA_ASSETS_IMAGES . '/404/404_v1.png'
                            ),                                 
                            'v2' => array(
                                'title' => 'v2',
                                'alt' => 'v2',
                                'img' => PUCA_ASSETS_IMAGES . '/404/404_v2.png'
                            ),                                                                 
                        ),
                        'default' => 'v1'
                    ),
                )
            );

            // Style
            $this->sections[] = array(
                'icon' => 'el el-icon-css',
                'title' => esc_html__('Style', 'puca'),
            ); 
            // Style
            $this->sections[] = array(
                'title' => esc_html__('Main', 'puca'),
                'subsection' => true,
                'fields' => array(
                    array(
                        'id'       => 'boby_bg',
                        'type'     => 'background',
                        'output'   => array( 'body' ),
                        'title'    => esc_html__( 'Body Background', 'puca' ),
                        'subtitle' => esc_html__( 'Body background with image, color, etc.', 'puca' ),
                        'default' => array(
                            'background-color' => ''
                        )
                    ),
                    array (
                        'title' => esc_html__('Main Theme Color', 'puca'),
                        'subtitle' => '<em>'.esc_html__('The main color of the site.', 'puca').'</em>',
                        'id' => 'main_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
					array (
                        'title' => esc_html__('Main Theme Color 2', 'puca'),
                        'subtitle' => '<em>'.esc_html__('The main color 2 of Fashion 2, Furniture and Supermaket.', 'puca').'</em>',
                        'id' => 'main_color2',
                        'type' => 'color',
                        'transparent' => false,
						'required' => array('active_theme','=',array('supermaket','fashion2', 'furniture')),
                        'default' => '',
                    ),
					array (
                        'title' => esc_html__('Border Button Color', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Border Button Color of Supermaket', 'puca').'</em>',
                        'id' => 'button_border_color',
                        'type' => 'color',
                        'transparent' => false,
						'required' => array('active_theme','=','supermaket'),
                        'default' => '',
                    ),
                    array (
                        'title' => esc_html__('Button Main Color Hover', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Button Main Color Hover of the site.', 'puca').'</em>',
                        'id' => 'button_hover_color',
                        'type' => 'color',
                        'required' => array('active_theme','=','supermaket'),
                        'transparent' => false,
                        'default' => '',
                    ),
					array (
                        'title' => esc_html__('Border Button Color Hover', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Border Button Color Hover of Supermaket', 'puca').'</em>',
                        'id' => 'button_border_color_hover',
                        'type' => 'color',
                        'transparent' => false,
						'required' => array('active_theme','=','supermaket'),
                        'default' => '',
                    ),
                )
            );
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Typography', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'show_typography',
                        'type' => 'switch',
                        'title' => esc_html__('Typography', 'puca'),
                        'default' => false
                    ),
                    array(
                        'title'    => esc_html__('Font Source', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Choose the Font Source', 'puca').'</em>',
                        'id'       => 'font_source',
                        'type'     => 'radio',
                        'required' => array('show_typography','=', true),
                        'options'  => array(
                            '1' => 'Standard + Google Webfonts',
                            '2' => 'Google Custom',
                            '3' => 'Custom Fonts'
                        ),
                        'default' => '1'
                    ),
                    array(
                        'id'=>'font_google_code',
                        'type' => 'text',
                        'title' => esc_html__('Google Link', 'puca'), 
                        'subtitle' => '<em>'.esc_html__('Paste the provided Google Code', 'puca').'</em>',
                        'default' => '',
                        'desc' => esc_html__('e.g.: https://fonts.googleapis.com/css?family=Open+Sans', 'puca'),
                        'required' => array('font_source','=','2')
                    ),

                    array (
                        'id' => 'main_custom_font_info',
                        'icon' => true,
                        'type' => 'info',
                        'raw' => '<h3 style="margin: 0;">'. sprintf(
                                                                    '%1$s <a href="%2$s">%3$s</a>',
                                                                    esc_html__( 'Video guide custom font in ', 'puca' ),
                                                                    esc_url( 'https://www.youtube.com/watch?v=ljXAxueAQUc' ),
                                                                    esc_html__( 'here', 'puca' )
                                ) .'</h3>',
                        'required' => array('font_source','=','3')
                    ),

                    array (
                        'id' => 'main_font_info',
                        'icon' => true,
                        'type' => 'info',
                        'raw' => '<h3 style="margin: 0;"> '.esc_html__('Main Font', 'puca').'</h3>',
                        'required' => array('show_typography','=', true),
                    ),                    

                    // Standard + Google Webfonts
                    array (
                        'title' => esc_html__('Font Face', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Pick the Main Font for your site.', 'puca').'</em>',
                        'id' => 'main_font',
                        'type' => 'typography',
                        'line-height' => false,
                        'text-align' => false,
                        'font-style' => false,
                        'font-weight' => false,
                        'all_styles'=> true,
                        'font-size' => false,
                        'color' => false,
                        'default' => array (
                            'font-family' => '',
                            'subsets' => '',
                        ),
                        'required' => array('font_source','=','1')
                    ),
                    
                    // Google Custom                        
                    array (
                        'title' => esc_html__('Google Font Face', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Enter your Google Font Name for the theme\'s Main Typography', 'puca').'</em>',
                        'desc' => esc_html__('e.g.: &#39;Open Sans&#39;, sans-serif', 'puca'),
                        'id' => 'main_google_font_face',
                        'type' => 'text',
                        'default' => '',
                        'required' => array('font_source','=','2')
                    ),                    

                    // main Custom fonts                      
                    array (
                        'title' => esc_html__('Main custom Font Face', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Enter your Custom Font Name for the theme\'s Main Typography', 'puca').'</em>',
                        'desc' => esc_html__('e.g.: &#39;Open Sans&#39;, sans-serif', 'puca'),
                        'id' => 'main_custom_font_face',
                        'type' => 'text',
                        'default' => '',
                        'required' => array('font_source','=','3')
                    ),

                    array (
                        'id' => 'secondary_font_info',
                        'icon' => true,
                        'type' => 'info',
                        'raw' => '<h3 style="margin: 0;"> '. esc_html__(' Secondary Font', 'puca').'</h3>',
                        'required' => array('show_typography','=', true),
                    ),
                    
                    // Standard + Google Webfonts
                    array (
                        'title' => esc_html__('Font Face', 'puca'),
                        'subtitle' => '<em>'. esc_html__('Pick the Secondary Font for your site.', 'puca').'</em>',
                        'id' => 'secondary_font',
                        'type' => 'typography',
                        'line-height' => false,
                        'text-align' => false,
                        'font-style' => false,
                        'font-weight' => false,
                        'all_styles'=> true,
                        'font-size' => false,
                        'color' => false,
                        'default' => array (
                            'font-family' => 'Pontano Sans',
                            'subsets' => '',
                        ),
                        'required' => array('font_source','=','1')
                        
                    ),
                    
                    // Google Custom                        
                    array (
                        'title' => esc_html__('Google Font Face', 'puca'),
                        'subtitle' => '<em>'. esc_html__('Enter your Google Font Name for the theme\'s Secondary Typography', 'puca').'</em>',
                        'desc' => esc_html__('e.g.: &#39;Open Sans&#39;, sans-serif', 'puca'),
                        'id' => 'secondary_google_font_face',
                        'type' => 'text',
                        'default' => '',
                        'required' => array('font_source','=','2')
                    ),                    

                    // Main Custom fonts                        
                    array (
                        'title' => esc_html__('Main Custom Font Face', 'puca'),
                        'subtitle' => '<em>'. esc_html__('Enter your Custom Font Name for the theme\'s Secondary Typography', 'puca').'</em>',
                        'desc' => esc_html__('e.g.: &#39;Open Sans&#39;, sans-serif', 'puca'),
                        'id' => 'secondary_custom_font_face',
                        'type' => 'text',
                        'default' => '',
                        'required' => array('font_source','=','3')
                    ),
                )
            );         
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Top Bar', 'puca'),
                'fields' => array(
                    array(
                        'id'=>'topbar_bg',
                        'type' => 'background',
                        'title' => esc_html__('Background', 'puca'),
                        'default' => array(
                            'background-color' => ''
                        )
                    ),
                    array(
                        'title' => esc_html__('Text Color', 'puca'),
                        'id' => 'topbar_text_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__('Link Color', 'puca'),
                        'id' => 'topbar_link_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),                    
                    array(
                        'title' => esc_html__('Link Color Hover', 'puca'),
                        'id' => 'topbar_link_color_hover', 
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                )
            );
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Header', 'puca'),
                'fields' => array(
                    array(
                        'id'=>'header_bg',
                        'type' => 'background',
                        'title' => esc_html__('Background', 'puca'),
                        'default' => array(
                            'background-color' => ''
                        )
                    ),
                    array(
                        'title' => esc_html__('Text Color', 'puca'),
                        'id' => 'header_text_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__('Link Color', 'puca'),
                        'id' => 'header_link_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__('Link Color Active', 'puca'),
                        'id' => 'header_link_color_active',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                )
            );
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Main Menu', 'puca'),
                'fields' => array(
                    array(
                        'title' => esc_html__('Link Color', 'puca'),
                        'id' => 'main_menu_link_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__('Link Color Active', 'puca'),
                        'id' => 'main_menu_link_color_active',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
					array (
                        'title' => esc_html__('Background Color Menu for Home Layout 01', 'puca'),
                        'id' => 'main_menu_background_color_hover',
                        'type' => 'color',
                        'transparent' => false,
						'required' => array('active_theme','=','supermaket'),
                        'default' => '',
                    ),
                )
            );
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Footer', 'puca'),
                'fields' => array(
                    array(
                        'id'=>'footer_bg',
                        'type' => 'background',
                        'title' => esc_html__('Background', 'puca'),
                        'default' => array(
                            'background-color' => ''
                        )
                    ),
                    array(
                        'title' => esc_html__('Heading Color', 'puca'),
                        'id' => 'footer_heading_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__('Text Color', 'puca'),
                        'id' => 'footer_text_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__('Link Color', 'puca'),
                        'id' => 'footer_link_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__('Link Color Hover', 'puca'),
                        'id' => 'footer_link_color_hover',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                )
            );
            
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Copyright', 'puca'),
                'fields' => array(
                    array(
                        'id'=>'copyright_bg',
                        'type' => 'background',
                        'title' => esc_html__('Background', 'puca'),
                        'default' => array(
                            'background-color' => ''
                        )
                    ),
                    array(
                        'title' => esc_html__('Text Color', 'puca'),
                        'id' => 'copyright_text_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__('Link Color', 'puca'),
                        'id' => 'copyright_link_color',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                    array(
                        'title' => esc_html__('Link Color Hover', 'puca'),
                        'id' => 'copyright_link_color_hover',
                        'type' => 'color',
                        'transparent' => false,
                        'default' => '',
                    ),
                )
            );

            // Social Media
            $this->sections[] = array(
                'icon' => 'el el-share',
                'title' => esc_html__('Social Share', 'puca'),
                'fields' => array(
                    array(
                        'id' => 'enable_code_share',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Code Share', 'puca'),
                        'default' => true
                    ),
                    array(
                        'id'        =>'code_share',
                        'type'      => 'textarea',
                        'required'  => array('enable_code_share','=',true),
                        'title'     => esc_html__('Addthis your code', 'puca'), 
                        'desc'      => esc_html__('You get your code share in https://www.addthis.com', 'puca'),
                        'validate'  => 'html_custom',
                        'default'   => '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-59f2a47d2f1aaba2"></script>'
                    ),
                )
            );

            // Performance
            $this->sections[] = array(
                'icon' => 'el-icon-cog',
                'title' => esc_html__('Performance', 'puca'),
            );   
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Performance', 'puca'),
                'fields' => array(
                    array (
                        'id'       => 'minified_js',
                        'type'     => 'switch',
                        'title'    => esc_html__('Include minified JS', 'puca'),
                        'subtitle' => esc_html__('Minify all ".js" files (speed up website)', 'puca'),
                        'default' => true
                    ),
                )
            );
             

            // Custom Code
            $this->sections[] = array(
                'icon' => 'el-icon-css',
                'title' => esc_html__('Custom CSS/JS', 'puca'),
            );

            // Css Custom Code
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Custom CSS', 'puca'),
                'fields' => array(
                    array (
                        'title' => esc_html__('Global Custom CSS', 'puca'),
                        'id' => 'custom_css',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                    array (
                        'title' => esc_html__('Custom CSS for desktop', 'puca'),
                        'id' => 'css_desktop',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                    array (
                        'title' => esc_html__('Custom CSS for tablet', 'puca'),
                        'id' => 'css_tablet',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                    array (
                        'title' => esc_html__('Custom CSS for mobile landscape', 'puca'),
                        'id' => 'css_wide_mobile',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                    array (
                        'title' => esc_html__('Custom CSS for mobile', 'puca'),
                        'id' => 'css_mobile',
                        'type' => 'ace_editor',
                        'mode' => 'css',
                    ),
                )
            );

            // Js Custom Code
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Custom Js', 'puca'),
                'fields' => array(
                    array (
                        'title' => esc_html__('Header JavaScript Code', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Paste your custom JS code here. The code will be added to the header of your site.', 'puca').'<em>',
                        'id' => 'header_js',
                        'type' => 'ace_editor',
                        'mode' => 'javascript',
                    ),
                    
                    array (
                        'title' => esc_html__('Footer JavaScript Code', 'puca'),
                        'subtitle' => '<em>'.esc_html__('Here is the place to paste your Google Analytics code or any other JS code you might want to add to be loaded in the footer of your website.', 'puca').'<em>',
                        'id' => 'footer_js',
                        'type' => 'ace_editor',
                        'mode' => 'javascript',
                    ),
                )
            );



            $this->sections[] = array(
                'title' => esc_html__('Import / Export', 'puca'),
                'desc' => esc_html__('Import and Export your Redux Framework settings from file, text or URL.', 'puca'),
                'icon' => 'el-icon-refresh',
                'fields' => array(
                    array(
                        'id' => 'opt-import-export',
                        'type' => 'import_export',
                        'title' => 'Import Export',
                        'subtitle' => 'Save and restore your Redux options',
                        'full_width' => false,
                    ),
                ),
            );

            $this->sections[] = array(
                'type' => 'divide',
            );
        }
		
		
		
		
        /**
         * All the possible arguments for Redux.
         * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
         * */
		 
		 /**
     * Custom function for the callback validation referenced above
     * */
		
		 
        public function setArguments()
        {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name' => 'puca_tbay_theme_options',
                // This is where your data is stored in the database and also becomes your global variable name.
                'display_name' => $theme->get('Name'),
                // Name that appears at the top of your panel
                'display_version' => $theme->get('Version'),
                // Version that appears at the top of your panel
                'menu_type' => 'menu',
                //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu' => true,
                // Show the sections below the admin menu item or not
                'menu_title' => esc_html__('Puca Options', 'puca'),
                'page_title' => esc_html__('Puca Options', 'puca'),

                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '',
                // Set it you want google fonts to update weekly. A google_api_key value is required.
                'google_update_weekly' => false,
                // Must be defined to add google fonts to the typography module
                'async_typography' => false,
                // Use a asynchronous font on the front end or font string
                //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar' => true,
                // Show the panel pages on the admin bar
                'admin_bar_icon' => 'dashicons-portfolio',
                // Choose an icon for the admin bar menu
                'admin_bar_priority' => 50,
                // Choose an priority for the admin bar menu
                'global_variable' => 'tbay_options',
                // Set a different name for your global variable other than the opt_name
                'dev_mode' => false,
				'forced_dev_mode_off' => false,
                // Show the time the page took to load, etc
                'update_notice' => true,
                // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                'customizer' => true,
                // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority' => null,
                // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent' => 'themes.php',
                // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions' => 'manage_options',
                // Permissions needed to access the options panel.
                'menu_icon' => '',
                // Specify a custom URL to an icon
                'last_tab' => '',
                // Force your panel to always open to a specific tab (by id)
                'page_icon' => 'icon-themes',
                // Icon displayed in the admin panel next to your menu_title
                'page_slug' => '_options',
                // Page slug used to denote the panel
                'save_defaults' => true,
                // On load save the defaults to DB before user clicks save or not
                'default_show' => false,
                // If true, shows the default value next to each field that is not the default value.
                'default_mark' => '',
                // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,
                // Shows the Import/Export panel when not used as a field.

                // CAREFUL -> These options are for advanced use only
                'transient_time' => 60 * MINUTE_IN_SECONDS,
                'output' => true,
                // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag' => true,
                // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database' => '',
                // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info' => false,
                // REMOVE

                // HINTS
                'hints' => array(
                    'icon' => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color' => 'lightgray',
                    'icon_size' => 'normal',
                    'tip_style' => array(
                        'color' => 'light',
                        'shadow' => true,
                        'rounded' => false,
                        'style' => '',
                    ),
                    'tip_position' => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect' => array(
                        'show' => array(
                            'effect' => 'slide',
                            'duration' => '500',
                            'event' => 'mouseover',
                        ),
                        'hide' => array(
                            'effect' => 'slide',
                            'duration' => '500',
                            'event' => 'click mouseleave',
                        ),
                    ),
                )
            );
            
            $this->args['intro_text'] = '';

            // Add content after the form.
            $this->args['footer_text'] = '';
            return $this->args;
			
			if ( ! function_exists( 'redux_validate_callback_function' ) ) {
				function redux_validate_callback_function( $field, $value, $existing_value ) {
					$error   = false;
					$warning = false;

					//do your validation
					if ( $value == 1 ) {
						$error = true;
						$value = $existing_value;
					} elseif ( $value == 2 ) {
						$warning = true;
						$value   = $existing_value;
					}

					$return['value'] = $value;

					if ( $error == true ) {
						$field['msg']    = 'your custom error message';
						$return['error'] = $field;
					}

					if ( $warning == true ) {
						$field['msg']      = 'your custom warning message';
						$return['warning'] = $field;
					}

					return $return;
				}
			}
			
        }
    }

    global $reduxConfig;
    $reduxConfig = new puca_Redux_Framework_Config();
	
	
	
}