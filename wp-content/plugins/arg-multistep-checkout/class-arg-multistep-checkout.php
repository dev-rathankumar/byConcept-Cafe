<?php
namespace argMC;

class WooCommerceCheckout
{
    private static $options         = array(); 
    private static $defaultOptions  = array();		


    /**
     * Plugin activation
     * @return void	 
     */
    public static function activate()
    {
        self::checkRequirements();
    }


    /**
     * Check plugin requirements
     * @return void	 
     */
    private static function checkRequirements()
    {
        delete_option('arg-mc-admin-error');

        //Detect WooCommerce plugin
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            //Load the plugin's translated strings
            load_plugin_textdomain('argMC', false, dirname(ARG_MC_BASENAME) . '/languages');

            $error = '<strong>' . sprintf(__('%s %s requires WooCommerce Plugin to be installed and activated.' , 'argMC'), ARG_MC_PLUGIN_NAME, ARG_MC_VERSION) . '</strong> ' . sprintf(__('Please <a href="%1$s" target="_blank">install WooCommerce Plugin</a>.', 'argMC'), 'https://wordpress.org/plugins/woocommerce/');

            update_option('arg-mc-admin-error', $error);	
        }
    }	


    /**
     * Initialize WordPress hooks
     * @return void	 
     */
    public static function initHooks()
    {
        //After setup theme
        add_action('after_setup_theme', array('argMC\WooCommerceCheckout', 'setup'));

        //Init
        add_action('init', array('argMC\WooCommerceCheckout', 'init'));	
		
        //Admin init
        add_action('admin_init', array('argMC\WooCommerceCheckout', 'adminInit'));

        //Admin notices
        add_action('admin_notices', array('argMC\WooCommerceCheckout', 'adminNotices'));		

        //Admin menu
        add_action('admin_menu', array('argMC\WooCommerceCheckout', 'adminMenu'));

        //Scripts & styles
        add_action('admin_enqueue_scripts', array('argMC\WooCommerceCheckout', 'enqueueScriptAdmin'));		
        add_action('wp_enqueue_scripts', array('argMC\WooCommerceCheckout', 'enqueueScript'));   

        add_action('wp_head', array('argMC\WooCommerceCheckout', 'loadStyle'));

        //WooCommerce
        add_filter('woocommerce_locate_template', array('argMC\WooCommerceCheckout', 'locateTemplate'), 20, 3);

        add_action('woocommerce_checkout_login_form', 'woocommerce_checkout_login_form');			
        add_action('woocommerce_checkout_coupon_form', 'woocommerce_checkout_coupon_form');	

        add_action('woocommerce_order_review', 'woocommerce_order_review');
        add_action('woocommerce_checkout_payment', 'woocommerce_checkout_payment', 20);
		
		//Custom actions
		add_action('arg_checkout_customer_details', array('argMC\WooCommerceCheckout', 'customerDetails'));

        //Ajax login
        add_action('wp_ajax_arg_mc_login', array('argMC\WooCommerceCheckout', 'login'));
        add_action('wp_ajax_nopriv_arg_mc_login', array('argMC\WooCommerceCheckout', 'login'));
		
		//Ajax register
		add_action('wp_ajax_arg_mc_register', array('argMC\WooCommerceCheckout', 'register'));
		add_action('wp_ajax_nopriv_arg_mc_register', array('argMC\WooCommerceCheckout', 'register'));	

        //Plugins page
        add_filter('plugin_row_meta', array('argMC\WooCommerceCheckout', 'pluginRowMeta'), 10, 2);
        add_filter('plugin_action_links_' . ARG_MC_BASENAME, array('argMC\WooCommerceCheckout', 'actionLinks'));

        //Admin page
        $page = filter_input(INPUT_GET, 'page');
        if (!empty($page) && $page == ARG_MC_MENU_SLUG) {
            add_filter('admin_footer_text', array('argMC\WooCommerceCheckout', 'adminFooter'));		
        }
    }


    /**
     * Plugin setup
     * @return void
     */	
    public static function setup()
    {		
		
        //Avada Theme Settings
        remove_action('woocommerce_before_checkout_form', 'avada_woocommerce_checkout_coupon_form');
        remove_action('woocommerce_checkout_before_customer_details', 'avada_woocommerce_checkout_before_customer_details');		
        remove_action('woocommerce_checkout_after_customer_details', 'avada_woocommerce_checkout_after_customer_details');
        remove_action('woocommerce_checkout_billing', 'avada_woocommerce_checkout_billing', 20);
        remove_action('woocommerce_checkout_shipping', 'avada_woocommerce_checkout_shipping', 20);
		remove_action('woocommerce_checkout_after_order_review', 'avada_woocommerce_checkout_after_order_review', 20);
		
		if (class_exists('Avada_Woocommerce')) {
			global $avada_woocommerce;

			if (!empty($avada_woocommerce) && $avada_woocommerce instanceof \Avada_Woocommerce) {
				remove_action('woocommerce_before_checkout_form', array($avada_woocommerce, 'checkout_coupon_form'));
				remove_action('woocommerce_checkout_before_customer_details', array($avada_woocommerce, 'checkout_before_customer_details'));
				remove_action('woocommerce_checkout_after_customer_details', array($avada_woocommerce, 'checkout_after_customer_details'));
				remove_action('woocommerce_checkout_billing', array($avada_woocommerce, 'checkout_billing'), 20);
				remove_action('woocommerce_checkout_shipping', array($avada_woocommerce, 'checkout_shipping'), 20);
				remove_action('woocommerce_checkout_after_order_review', array($avada_woocommerce, 'checkout_after_order_review'), 20);		
			}
		}				
    }


    /**
     * Init
     * @return void	 
     */	
    public static function init()
    {	
        //Load the plugin's translated strings
        load_plugin_textdomain('argMC', false, dirname(ARG_MC_BASENAME) . '/languages');

		//Init variables
        self::initVariables();					
    }	
	
	
    /**
     * Admin init
     * @return void	 
     */
    public static function adminInit()
    {
        //Check plugin requirements
        self::checkRequirements();
    }


    /**
     * Admin notices
     * @return void	 
     */	
    public static function adminNotices()
    {
        if (get_option('arg-mc-admin-error')) {
            $class      = 'notice notice-error';
            $message    = get_option('arg-mc-admin-error');

            printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
        }
    }


    /**
     * Admin menu
     * @return void	 
     */ 	
    public static function adminMenu()
    {
        add_submenu_page(
            'woocommerce',
            ARG_MC_PLUGIN_NAME,
            ARG_MC_PLUGIN_NAME,
            'manage_options',
            ARG_MC_MENU_SLUG,
            array('argMC\WooCommerceCheckout', 'adminOptions')
        );		
    }	


    /**
     * Enqueue scripts and styles for the admin
     * @return void
     */
    public static function enqueueScriptAdmin()
    {
        //Admin page
        $page = filter_input(INPUT_GET, 'page');
        if (empty($page) || $page !== ARG_MC_MENU_SLUG) {
            return;
        }

        //Color picker styles
        wp_enqueue_style('wp-color-picker');

        //Plugin admin styles
        wp_enqueue_style('arg-mc-styles-admin', ARG_MC_DIR_URL . 'css/styles-admin.css', array(), ARG_MC_VERSION);           

        //Color picker script
        wp_enqueue_script('wp-color-picker');

        //Plugin admin script
        wp_register_script('arg-mc-scripts-admin', ARG_MC_DIR_URL . 'js/scripts-admin.js', array('jquery'), ARG_MC_VERSION, true);
		

		wp_localize_script('arg-mc-scripts-admin', 'argmcJsVars', array(
			'ajaxURL' => admin_url('admin-ajax.php')
		));
		
		wp_enqueue_script('arg-mc-scripts-admin');
			
    }


    /**
     * Enqueue scripts and styles for the front end
     * @return void
     */     
    public static function enqueueScript()
    {
        //Check if is checkout page
        if (function_exists('is_checkout') && is_checkout()) {
            //Custom fonts
            wp_enqueue_style('arg-mc-icons', ARG_MC_DIR_URL . 'icons/css/arg-mc-icons.css', array(), ARG_MC_VERSION);

            //jQuery Validation Engine styles
            wp_enqueue_style('arg-mc-jquery-validation-engine-css', ARG_MC_DIR_URL . 'css/validationEngine.jquery.css', array(), 'v2.6.2');

            //Plugin styles
            wp_enqueue_style('arg-mc-styles', ARG_MC_DIR_URL . 'css/styles.css', array(), ARG_MC_VERSION);

            //Tabs layout styles
			$tabsLayout = 'tabs-square';
			
            if (!empty(self::$options['tabs_layout']) && in_array(self::$options['tabs_layout'], array('tabs-square', 'tabs-arrow', 'tabs-arrow-alt', 'tabs-progress-bar', 'tabs-outline'))) {
				$tabsLayout = self::$options['tabs_layout'];
			}
			
			switch ($tabsLayout) {
				case 'tabs-square':
					wp_enqueue_style('arg-mc-styles-tabs', ARG_MC_DIR_URL . 'css/styles-tabs-square.css', array(), ARG_MC_VERSION);
					break;
					
				case 'tabs-arrow':
					wp_enqueue_style('arg-mc-styles-tabs', ARG_MC_DIR_URL . 'css/styles-tabs-arrow.css', array(), ARG_MC_VERSION);
					break;
					
				case 'tabs-arrow-alt':
					wp_enqueue_style('arg-mc-styles-tabs', ARG_MC_DIR_URL . 'css/styles-tabs-arrow-alt.css', array(), ARG_MC_VERSION);
					break;	
					
				case 'tabs-progress-bar':
				    wp_enqueue_style('arg-mc-styles-tabs', ARG_MC_DIR_URL . 'css/styles-tabs-progress-bar.css', array(), ARG_MC_VERSION);
				    break;
				case 'tabs-outline':
				    wp_enqueue_style('arg-mc-styles-tabs', ARG_MC_DIR_URL . 'css/styles-tabs-outline.css', array(), ARG_MC_VERSION);
				break;
			}

            //Woocommerce styles
            if (!empty(self::$options['overwrite_woo_styles'])) {
                wp_enqueue_style('arg-mc-styles-woocommerce', ARG_MC_DIR_URL . 'css/styles-woocommerce.css', array(), ARG_MC_VERSION);
            }

            //jQuery Validation Engine script
            wp_register_script('arg-mc-jquery-validation-engine-en-js', ARG_MC_DIR_URL . 'js/jquery.validationEngine-en.js', array('jquery'), 'v2.6.2', true);

            wp_localize_script('arg-mc-jquery-validation-engine-en-js', 'argmcJsVars', array(
                'errorRequiredText'     => self::$options['error_required_text'],
                'errorRequiredCheckbox' => self::$options['error_required_checkbox'],
                'errorEmail'            => self::$options['error_email'],
				'errorPhone'            => self::$options['error_phone'],
				'errorZip'            	=> self::$options['error_zip']
            ));		

            wp_enqueue_script('arg-mc-jquery-validation-engine-en');
            wp_enqueue_script('arg-mc-jquery-validation-engine-js', ARG_MC_DIR_URL . 'js/jquery.validationEngine.js', array('jquery', 'arg-mc-jquery-validation-engine-en-js'), 'v2.6.2', true);	

            //Plugin script
            wp_register_script('arg-mc-scripts', ARG_MC_DIR_URL . 'js/scripts.js', array('jquery', 'wc-checkout', 'arg-mc-jquery-validation-engine-js', 'select2'), ARG_MC_VERSION, true);

            wp_localize_script('arg-mc-scripts', 'argmcJsVars', array(
                'ajaxURL'       => admin_url('admin-ajax.php'),
                'loginNonce'    => wp_create_nonce('login-nonce'),
				'registerNonce'	=> wp_create_nonce('register-nonce')
            ));
            wp_enqueue_script('arg-mc-scripts');
            
            //Custom checkout step
            if (!empty(self::$options['show_order_review']) || !empty(self::$options['show_customer_details_review'])) {
                wp_enqueue_script('arg-custom-steps', ARG_MC_DIR_URL . 'js/scripts-order-review.js', array('arg-mc-scripts'), ARG_MC_VERSION, true);							
            }            
        }
    }


    /**
     * Load custom styles
     * @return void	 
     */
    public static function loadStyle()
    {
       //Check if is checkout page
        if (function_exists('is_checkout') && is_checkout()) {
            global $argOptions;

            $argOptions = self::$options;
            include_once(ARG_MC_DIR_PATH . 'inc/style.php');
        }	
    }	


    /**
     * Load WooCommerce checkout form template file.s
     * @param mixed $template required
     * @param mixed $templateName optional
     * @param mixed $templatePath optional
     * @return mixed
     */      
    public static function locateTemplate($template, $templateName, $templatePath)
    {	
        if ($templateName == 'checkout/review-order.php' && empty(self::$options['show_product_image'])) {
            return $template;
        }
		
		 if ($templateName == 'checkout/form-register.php' && self::$options['overwrite_form_register'] ) {
			// Look within passed path within the theme - this is priority.
			$templateTheme = locate_template(array(
				trailingslashit($templatePath) . $templateName,
				$templateName
			));
			
			if (!empty($templateTheme)) {
				return $template; 
			}
        }

		if ($templateName == 'checkout/form-checkout.php') {
			if (!empty(self::$options['overwrite_form_checkout'])) {		
		
				// Look within passed path within the theme - this is priority.
				$templateTheme = locate_template(array(
					trailingslashit($templatePath) . $templateName,
					$templateName
				));
				
				if (!empty($templateTheme)) {
					return $templateTheme; 
				}
			}
			

			if (!empty(self::$options['remove_all_hooks'])) {
				$templateName = 'checkout/form-checkout-hooks-removed.php';
			}
		}
   
        if (file_exists(ARG_MC_DIR_PATH . 'woocommerce/' . $templateName)) {
            $template = ARG_MC_DIR_PATH . 'woocommerce/' . $templateName;
			
			return $template; 
        }
	
        return $template;               
    }
	
    /**
     * Output the customer details
     */  	
	public static function customerDetails()
	{
		?>
		<div class="argmc-customer-review">
			<div class="argmc-customer-details">
				<h3><?php _e('Customer Details', 'argMC'); ?></h3>
				<ul class="argmc-customer-list">
					<li>
						<div class="argmc-customer-detail"><?php _e('Email:', 'argMC'); ?></div>
						<div class="argmc-customer-email"></div>
					</li>
					<li>
						<div class="argmc-customer-detail"><?php _e('Phone:', 'argMC'); ?></div>
						<div class="argmc-customer-phone"></div>
					</li>
				</ul>
			</div>
			
			<div class="argmc-customer-addresses">
				<div class = "argmc-billing-details">
					<h3><?php _e('Billing Address', 'argMC'); ?></h3>
					<div class="argmc-billing-address"></div>
				</div>
				
				<div class = "argmc-shipping-details">
					<h3><?php _e('Shipping Address', 'argMC'); ?></h3>
					<div class="argmc-shipping-address"></div>
				</div>
			</div>
		</div>
		<?php
	
	}


    /**
     * Initialize global variables
     * @return void	 
     */   
    private static function initVariables()
    {			
        self::$defaultOptions = array(
            'btn_next_text'             => __('Next', 'argMC'),
            'btn_prev_text'             => __('Previous', 'argMC'),
            'btn_submit_text'           => __('Place Order', 'argMC'),
            'btn_skip_login_text'       => __('Skip Login', 'argMC'),
            'error_required_text'       => __('This field is required', 'argMC'),
            'error_required_checkbox'   => __('You must accept the terms and conditions', 'argMC'),
            'error_email'               => __('Invalid email address', 'argMC'),
			'error_phone'               => __('Invalid phone number', 'argMC'),
			'error_zip'                 => __('Please enter a valid postcode/ZIP', 'argMC'),
			'overwrite_form_checkout'	=> 0,
			'overwrite_form_register'	=> 0,
			'remove_all_hooks'          => 0,
			'scrollTopDesktops'         => 0,
			'scrollTopMobiles'          => 0,
            //Important - Do not change steps order
            'steps'                     => array(
                'login' => array(
                    'text'  => __('Login', 'argMC'), 
                    'class' => 'argmc-login-step argmc-skip-validation',
					'data'  => 'login-step'
                ),
                'coupon' => array(
                    'text'  => __('Coupon', 'argMC'),
                    'class' => 'argmc-coupon-step argmc-skip-validation',
					'data'  => 'coupon-step'
					
                ),
                'billing_shipping' => array(
                    'text'  => __('Billing & Shipping', 'argMC'),
                    'class' => 'argmc-billing-shipping-step',
					'data'  => 'billing-shipping-step'
                ),			
                'billing' => array(
                    'text'  => __('Billing', 'argMC'),
                    'class' => 'argmc-billing-step',
					'data'  => 'billing-step'
                ),
                'shipping' => array(
                    'text'  => __('Shipping', 'argMC'),
                    'class' => 'argmc-shipping-step',
					'data'  => 'shipping-step'
                ),
                'order_payment' => array(
                    'text'  => __('Order & Payment', 'argMC'),
                    'class' => 'argmc-order-payment-step',
					'data'  => 'order-payment-step'
                ),					
                'order' => array(
                    'text'  => __('Order', 'argMC'),
                    'class' => 'argmc-order-step',
					'data'  => 'order-step'
                ),
                'payment' => array(
                    'text'  => __('Payment', 'argMC'),
                    'class' => 'argmc-payment-step',
					'data'  => 'payment-step'
                ),
                'order_review' => array(
                    'text'  => __('Order Review', 'argMC'),
                    'class' => 'argmc-order-review-step argmc-skip-validation',
					'data'  => 'order-review-step'
					
                )
            ),
            'footer_text'                   => __('Far far away, behind the word mountains, far from the countries Vokalia and Consonantia.', 'argMC'),
            'wizard_max_width'              => '900px',
            'secondary_font'                => '',
            'secondary_font_weight'         => '600',
            'show_login'                    => 1,
			'show_register'					=> 1,
			'login_layout'					=> 'register-switched-with-login',
			'login_heading'                 => __('Login', 'argMC'),
			'register_heading'             	=> __('Register', 'argMC'),
			'force_login_message'           => '',
			'login_register_top_message'    => '',
            'show_coupon'                   => 1,
            'show_order'                    => 1,
            'show_additional_information'   => 1,
			'show_customer_details_review'  => 0,
            'show_order_review'             => 0,
			'show_order_review_table'       => 0,
            'show_product_image'            => 0,
			'coupon_position'				=> 'default',
            'merge_billing_shipping'        => 1,
            'merge_order_payment'           => 1,
            'tabs_layout'                   => 'tabs-square',			
            'tabs_template'                 => 'tabs-default',
            'tabs_width'                    => 'tabs-equal-width',
            'wizard_color'                  => '#555',
            'accent_color'                  => '#e23636',
            'border_color'                  => '#d9d9d9',
			'overwrite_wizard_buttons'      => 0, 
			'wizard_text_errors_color'      => '#e23636',
			'wizard_button_text_color'      => '#fff',
			'wizard_button_text_opacity'    => '0.7',  
			'next_button_bkg'               => '#e23636',
			'prev_button_bkg'               => '#b4b4b4',
			'place_order_button_bkg'        => '#96c457',
            
            'tab_text_color'                         => '#bbb',
            'tab_bkg_color'                          => '#eee',
            'tab_border_left_color'                  => '#dcdcdc',
            'tab_border_bottom_color'                => '#c9c9c9',
            'number_text_color'                      => '#999',
			'tab_number_bkg_color'                   => '#fff',
            'tab_number_color_hover'                 => '#e23636',
			'tab_number_bkg_color_hover'             => '#fff',
            'tab_text_color_hover'                   => '#000',
            'tab_bkg_color_hover'                    => '#f8f8f8',
            'tab_border_bottom_color_hover'          => '#555555',
            'show_number_checkmark'                  => 0,		
            'tab_before_arrow_color'                 => '#fff',            
			'tab_adjust_number_position'             => '0px',
			'tab_adjust_checkmark_position'          => '0px',
			'tab_adjust_text_position'               => '0px',
            
			'tab_arrow_alt_hide_number_bkg'				=> 0,
			'tab_arrow_alt_hide_tails'					=> 0,
			'tab_arrow_alt_orientation'					=> 'horizontal',
            'tab_arrow_alt_text_color'                  => '#bbb',
            'tab_arrow_alt_bkg_color'                   => '#eee',
            'tab_arrow_alt_border_bottom_color'         => '#c9c9c9',
            'tab_arrow_alt_number_text_color'           => '#999',
            'tab_arrow_alt_number_bkg_color'            => '#fff',
            'tab_arrow_alt_number_color_hover'          => '#000',
            'tab_arrow_alt_number_bkg_color_hover'      => '#fff',
            'tab_arrow_alt_text_color_hover'            => '#fff',
            'tab_arrow_alt_bkg_color_hover'             => '#555555',
            'tab_arrow_alt_border_bottom_color_hover'   => '#c9c9c9',   
            'tab_arrow_alt_completed_bkg_color'         => '#afafaf',
            'tab_arrow_alt_before_arrow_color'          => '#fff', 
            'tab_arrow_alt_show_number_checkmark'       => 0,
            'tab_arrow_alt_adjust_number_position'      => '1px',
            'tab_arrow_alt_adjust_checkmark_position'   => '1px',
            'tab_arrow_alt_adjust_text_position'        => '1px',
            
			'tab_progress_template'               			=> 'progress-boxed',
            'tab_progress_bar_text_color'                   => '#aaa',
            'tab_progress_bar_number_text_color'            => '#fff',           
            'tab_progress_bar_number_bkg_color'             => '#afafaf',            
            'tab_progress_bar_border_bottom_color'          => '#c9c9c9',
            'tab_progress_bar_bkg_color'                    => '#f9f9f9',            
            'tab_progress_bar_number_color_hover'           => '#fff',
            'tab_progress_bar_number_bkg_color_hover'       => '#000',
            'tab_progress_bar_text_color_hover'             => '#000',
            'tab_progress_bar_border_bottom_color_hover'    => '#000',
            'tab_progress_bar_completed_text_color'    => '#000',
            'tab_progress_bar_completed_number_color'    => '#fff',
            'tab_progress_bar_completed_number_bkg_color'    => '#96c457',
            'tab_progress_bar_show_number_checkmark'        => 0,
            'tab_progress_bar_adjust_number_position'       => '1px',
            'tab_progress_bar_adjust_checkmark_position'    => '1px',
            'tab_progress_bar_adjust_text_position'         => '0px',
            
			'tab_outline_orientation'					=> 'horizontal-text-under',
			'tab_outline_text_color'                   	=> '#999',
            'tab_outline_number_text_color'            	=> '#999',           
            'tab_outline_border_color'         			=> '#ddd',
			'tab_outline_text_color_hover'				=> '#000',
			'tab_outline_bkg_color_hover'				=> '#f7f7f7',
			'tab_outline_number_color_hover'			=> '#fff',
			'tab_outline_number_bkg_color_hover'		=> '#333',
			'tab_outline_current_border_left' 			=> '#333',
            'tab_outline_completed_text_color'	=> '#000',
			'tab_outline_number_color_completed'		=> '#fff',
			'tab_outline_number_bkg_color_completed'	=> '#96c457',
			'tab_outline_show_number_checkmark'        	=> 0,
            'tab_outline_adjust_number_position'       	=> '0px',
            'tab_outline_adjust_checkmark_position'    	=> '1px',
            'tab_outline_adjust_text_position'         	=> '0px',
			     
            'overwrite_woo_styles'              => 0,
            'woo_text_color'                    => '#555',
            'woo_label_color'                   => '#4b4b4b',
            'woo_input_border_color'            => '#ddd',
            'woo_input_bkg_color'               => '#f9f9f9',
            'woo_invalid_required_field_border' => '#e23636',
            'woo_invalid_required_field_bkg'    => '#ffefee',
            'woo_validated_field_border'        => "#ddd",
            'woo_button_bkg_color'              => '',
            'woo_button_bkg_color_login'        => '#444',
            'woo_field_border_radius'           => '0px'
		);
	

        $options        = get_option('arg-mc-options'); 
        $defaultOptions = self::$defaultOptions;

        if (!empty($options)) {
            //Merge default options array with options array
            self::setOptions($options, $defaultOptions);
            
            if ($defaultOptions !== $options) {           
                update_option('arg-mc-options', $defaultOptions);
            }
        } else { 		
            update_option('arg-mc-options', self::$defaultOptions);
        } 

        //Set options
        self::$options = $defaultOptions;        
    }

	
    /**
     * Set options
     */	
    public static function setOptions($options, &$defaultOptions) 
    {
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                self::setOptions($options[$key], $defaultOptions[$key]);
            } else { 
                if (array_key_exists($key, $defaultOptions)) { 
                    $defaultOptions[$key] = $value;
                }
            }
        }
    }    


    /**
     * Admin options
     */ 	
    public static function adminOptions()
    {	
        $data = filter_input_array(INPUT_POST);
        
        //Form submit
        if (!empty($data)) { 
           
            $data = array_map('stripslashes_deep', $data);

            if (!empty($data['reset'])) {
                self::$options = self::$defaultOptions;
            } else {				
                foreach ($data as $fieldName => $fieldValue) {
                    if ($fieldName == 'save' || $fieldName == 'reset') {
                        continue;	
                    }

                    if (!array_key_exists($fieldName, self::$options)) {
                        continue;
                    }
                    
                    if ($fieldName == 'steps') {
                        foreach ($fieldValue as $stepName => $stepValue) {
                            self::$options[$fieldName][$stepName]['text'] = $stepValue['text'];
                        }
                    } else {
                        self::$options[$fieldName] = $fieldValue;
                    }
                }
            }

            self::$options = apply_filters('arg-mc-update-options', self::$options);

            update_option('arg-mc-options', self::$options);				
        }

        //Set options
        $options = self::$options;


        //Admin options
        $selectedTab    = 'general';
        $tab            = filter_input(INPUT_GET, 'tab');
        
        if (!empty($tab) && in_array($tab, array('general', 'steps', 'styles', 'custom-fields'))) {
            $selectedTab = $tab;
        }
        ?>

        <div class="argmc-wrapper">

            <div class="nav-tab-wrapper argmc-tab-wrapper">
                <a href="?page=<?php echo ARG_MC_MENU_SLUG; ?>&tab=general" class="nav-tab<?php echo $selectedTab == 'general' ? ' nav-tab-active' : ''; ?>"><?php _e('General Settings', 'argMC'); ?></a>
                <a href="?page=<?php echo ARG_MC_MENU_SLUG; ?>&tab=steps" class="nav-tab<?php echo $selectedTab == 'steps' ? ' nav-tab-active' : ''; ?>"><?php _e('Wizard Steps', 'argMC'); ?></a>
                <a href="?page=<?php echo ARG_MC_MENU_SLUG; ?>&tab=styles" class="nav-tab<?php echo $selectedTab == 'styles' ? ' nav-tab-active' : ''; ?>"><?php _e('Wizard Styles', 'argMC'); ?></a>
		    </div>

            <form method="post" class="argmc-form">

                <?php
                switch ($selectedTab) {
                    case 'general':
						include_once(ARG_MC_DIR_PATH . 'admin/template-parts/content-general-settings.php');
                    break;

				case 'steps':
					include_once(ARG_MC_DIR_PATH . 'admin/template-parts/content-steps.php');					
                    break;

				case 'styles':
					include_once(ARG_MC_DIR_PATH . 'admin/template-parts/content-styles.php');	
                    break;
                }			
                ?>
                <input type="submit" name="save" class="button button-primary" value="<?php _e('Save Changes', 'argMC'); ?>">
                <input type="submit" name="reset" class="button" value="<?php _e('Reset All', 'argMC'); ?>">
            </form>
        </div>
        <?php	
    }


    /**
     * Login
     * @return Json		 
     */ 		
    public static function login()
    {
		if (is_user_logged_in()) {
			echo json_encode(array(
				'success'   => false,
				'error'     => __('You are already logged in', 'argMC')
			));

			exit;						
		}
		
        check_ajax_referer('login-nonce', 'security');

        $info                   = array();
        $info['user_login'] 	= filter_input(INPUT_POST, 'username');
        $info['user_password'] 	= filter_input(INPUT_POST, 'password');
		$remember 				= filter_input(INPUT_POST, 'rememberme');
		$info['remember'] 		= !empty($remember);
			
		$validationError = new \WP_Error();
		$validationError = apply_filters('woocommerce_process_login_errors', $validationError, $info['user_login'], $info['user_password']);

		if ($validationError->get_error_code() ) {
			echo json_encode(array(
				'success'   => false,
				'error'     => $validationError->get_error_message()
			));

			exit;			
		}			
				
		if (empty($info['user_login'])) {
			echo json_encode(array(
				'success'   => false,
				'error'     => __('Username is required', 'argMC')
			));

			exit;	
		}

		if (empty($info['user_password'])) {
			echo json_encode(array(
				'success'   => false,
				'error'     => __('Password is required', 'argMC')
			));

			exit;		
		}
				
		if (is_email($info['user_login']) && apply_filters('woocommerce_get_username_from_email', true)) {
			$user = get_user_by('email', $info['user_login']);

			if (!empty($user->user_login)) {
				$info['user_login'] = $user->user_login;
			} else {
				echo json_encode(array(
					'success'   => false,
					'error'     => __('A user could not be found with this email address', 'argMC')
				));
	
				exit;			
			}
		} 
				
		$secureCookie = is_ssl() ? true : false;

        $user = wp_signon($info, $secureCookie);

        if (is_wp_error($user)) {
            echo json_encode(array(
                'success'   => false,
                'error'     => __('Incorrect username/password', 'argMC')
            ));

            exit;
        } 

        echo json_encode(array(
            'success' => true
        ));		

        exit;
    }	

	
    /**
     * Register user
     * @return Json		 
     */ 		
    public static function register()
    {
		if (is_user_logged_in()) {
			echo json_encode(array(
				'success'   => false,
				'error'     => __('You are already logged in', 'argMC')
			));

			exit;						
		}
		
        check_ajax_referer('register-nonce', 'security');

        $username	= filter_input(INPUT_POST, 'username');
        $email		= filter_input(INPUT_POST, 'email');
		$email2		= filter_input(INPUT_POST, 'email_2');		
		$password	= filter_input(INPUT_POST, 'password');

		$username = 'no' === get_option('woocommerce_registration_generate_username') ? $username : '';
		$password = 'no' === get_option('woocommerce_registration_generate_password') ? $password : '';
		
		$validationError = new \WP_Error();
		$validationError = apply_filters('woocommerce_process_registration_errors', $validationError, $username, $password, $email);

		if ($validationError->get_error_code()) {
			echo json_encode(array(
				'success'   => false,
				'error'     => $validationError->get_error_message()
			));

			exit;			
		}		
		
		// Anti-spam trap
		if (!empty($email2)) {
            echo json_encode(array(
                'success'   => false,
                'error'     => __('Anti-spam field was filled in.', 'argMC')
            ));

            exit;		
		}
					
		if (function_exists('wc_create_new_customer')) {
			$userId = wc_create_new_customer($email, $username, $password);
		} else {
			$data = array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $email,
				'role'       => 'customer',
			);
		
			$userId = wp_insert_user($data);
		}
		
        if (is_wp_error($userId)) {
            echo json_encode(array(
                'success'   => false,
                'error'     => $userId->get_error_message()
            ));

            exit;
        } 

		// Log user in
		if (apply_filters('woocommerce_registration_auth_new_customer', true, $userId)) {
			wp_set_auth_cookie($userId);
		}
		
        echo json_encode(array(
            'success' => true
        ));		

        exit;
    }	
	
	
    /**
     * Plugins page
     * @return array		  
     */ 	
    public static function pluginRowMeta($links, $file)
    {
        if ($file == ARG_MC_BASENAME) {
            unset($links[2]);

            $customLinks = array(
                'documentation'     => '<a href="' . ARG_MC_DOCUMENTATION_URL . '" target="_blank">' . __('Documentation', 'argMC') . '</a>',
                'visit-plugin-site' => '<a href="' . ARG_MC_PLUGIN_URL . '" target="_blank">' . __('Visit plugin site', 'argMC') . '</a>'
            );

            $links = array_merge($links, $customLinks);
        }

        return $links;
    }


    /**
     * Plugins page
     * @return array	 
     */ 
    public static function actionLinks($links)
    {
        $customLinks = array_merge(array('settings' => '<a href="' . admin_url('admin.php?page='. ARG_MC_MENU_SLUG) . '">' . __('Settings', 'argMC') . '</a>'), $links);

        return $customLinks;
    }


    /**
     * Admin footer
     * @return void	 
     */ 		
    public static function adminFooter()
    {
        ?>
        <p><a href="https://codecanyon.net/item/arg-multistep-checkout-for-woocommerce/reviews/18036216" class="arg-review-link" target="_blank"><?php echo sprintf(__('If you like <strong> %s </strong> please leave us a &#9733;&#9733;&#9733;&#9733;&#9733; rating.', 'argMC'), ARG_MC_PLUGIN_NAME); ?></a> <?php _e('Thank you.', 'argMC'); ?></p>
        <?php
    }
}