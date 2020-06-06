<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 7/26/16
 * Time: 23:32
 */
use Carbon\Carbon;
class Openpos_Admin{
    private $settings_api;
    public $core;
    public $_filesystem;
    private $_session;
    public function __construct()
    {
        global $OPENPOS_SETTING;
        global $OPENPOS_CORE;
        global $op_session;
        $this->_session = $op_session;
        $this->settings_api = $OPENPOS_SETTING;
        $this->core = $OPENPOS_CORE;
        if(!class_exists('WP_Filesystem_Direct'))
        {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
            require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
        }
        $this->_filesystem = new WP_Filesystem_Direct(false);

    }

    public function init()
    {
       

        add_action( 'admin_notices',array($this, 'admin_notice') );
        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'init', array($this, '_short_code') );
        add_action('admin_enqueue_scripts', array($this,'admin_global_style'));
        //add_action( 'init', array($this,'create_store_taxonomies'), 0 );

        add_filter( "manage_edit-store_columns", array($this,'store_setting_column_header'), 10);
        add_action( "manage_store_custom_column",array($this,'store_setting_column_content'), 10, 3);
        add_action( 'admin_menu', array($this,'pos_admin_menu'),1 );

        add_action( 'woocommerce_product_options_stock_fields', array($this,'woocommerce_product_options_stock_fields'));
        add_action( 'woocommerce_variation_options_inventory', array($this,'woocommerce_variation_options_inventory'),10,3);

        add_action( 'woocommerce_product_options_pricing', array($this,'woocommerce_product_options_pricing'));
        add_action( 'woocommerce_variation_options_pricing', array($this,'woocommerce_variation_options_pricing'),10,3);
        add_action( 'woocommerce_product_options_advanced', array($this,'woocommerce_product_options_advanced'));

        //ajax

        add_action( 'wp_ajax_op_products', array($this,'products') );
        add_action( 'wp_ajax_op_stock_products', array($this,'stock_products') );
        add_action( 'wp_ajax_op_stock_products_update', array($this,'stock_products_update') );

        add_action( 'wp_ajax_op_transactions', array($this,'transactions') );
        add_action( 'wp_ajax_op_orders', array($this,'orders') );

        // Admin bar menus
        if ( apply_filters( 'woocommerce_show_admin_bar_visit_store', true ) ) {
            add_action( 'admin_bar_menu', array( $this, 'admin_bar_menus' ), 31 );
        }

        add_action( 'wp_ajax_op_cashier', array($this,'getUsers') );
        add_action( 'wp_ajax_save_cashier', array($this,'save_cashier') );

        add_action( 'wp_ajax_print_barcode', array($this,'print_bacode') );
        add_action( 'wp_ajax_print_receipt', array($this,'print_receipt') );
        add_action( 'wp_ajax_admin_openpos_data', array($this,'dashboard_data') );
        add_action( 'wp_ajax_admin_openpos_reset_balance', array($this,'reset_balance') );

        add_action( 'wp_ajax_admin_openpos_update_product_grid', array($this,'update_product_grid') );
        add_action( 'wp_ajax_admin_openpos_update_transaction_grid', array($this,'update_transaction_grid') );
        add_action( 'wp_ajax_admin_openpos_update_inventory_grid', array($this,'update_inventory_grid') );

        add_action( 'wp_ajax_admin_openpos_session_unlink', array($this,'session_unlink') );

        //register
        add_action( 'wp_ajax_openpos_update_register', array($this,'update_register') );
        add_action( 'wp_ajax_openpos_delete_register', array($this,'delete_register') );
        //table
        add_action( 'wp_ajax_openpos_update_table', array($this,'update_table') );
        add_action( 'wp_ajax_openpos_delete_table', array($this,'delete_table') );
        //warehouse

        add_action( 'wp_ajax_openpos_delete_warehouse', array($this,'delete_warehouse') );
        add_action( 'wp_ajax_openpos_update_warehouse', array($this,'update_warehouse') );

        add_action( 'wp_ajax_op_inventory', array($this,'get_inventories') );
        add_action( 'wp_ajax_openpos_stock_overview', array($this,'stock_overview') );
        add_action( 'wp_ajax_op_export_inventory', array($this,'export_inventory') );
        add_action( 'wp_ajax_op_upload_inventory_csv', array($this,'upload_inventory_csv') );
        add_action( 'wp_ajax_openpos_adjust_stock_finder', array($this,'adjust_stock_finder') );
        add_action( 'wp_ajax_op_adjust_stock', array($this,'op_adjust_stock') );
        add_action( 'wp_ajax_op_ajax_category', array($this,'op_ajax_category') );
        add_action( 'wp_ajax_op_ajax_order_status', array($this,'op_ajax_order_statuses') );
        add_action( 'wp_ajax_op_ajax_report', array($this,'report_ajax') );
        add_action( 'wp_ajax_op_upload_product_image', array($this,'upload_product_image') );
//        add_action( 'wp_ajax_openpos_report_data_table', array($this,'report_data_table') );

        add_filter('pre_update_option_openpos_general',array($this,'pre_update_option_openpos_general'),10,3);

    }
    function pre_update_option_openpos_general($value, $old_value, $option){
        $se_number = isset($value['pos_sequential_number']) ? (int)$value['pos_sequential_number'] : 0;
        $current_order_number = get_option('_op_wc_custom_order_number',0);
        if($se_number && $current_order_number)
        {
            if($se_number > $current_order_number)
            {
                update_option('_op_wc_custom_order_number',$se_number);
            }else{
                //$value['pos_sequential_number'] = $current_order_number;
            }
        }
        return $value;
    }


    function admin_init() {

        add_filter('plugin_row_meta',array($this,'plugin_row_meta'),100,3);
        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );
        //initialize settings
        $this->settings_api->admin_init();

        $this->admin_notice_init();
    }
    function plugin_row_meta($plugin_meta, $plugin_file, $plugin_data){
        $plugin = isset($plugin_data['TextDomain']) ? $plugin_data['TextDomain']:'';

        if($plugin == 'openpos')
        {
           $plugin_meta[] = '<a target="_blank" href="'.esc_url('https://support.openswatch.com/kb/index.php').'">'.__('Knowledgebase','openpos').'</a>';
           $plugin_meta[] = '<a target="_blank" href="'.esc_url('https://support.openswatch.com/open.php').'">'.__('Support','openpos').'</a>';
        }
        return $plugin_meta;
    }

    function get_default_value($key)
    {
        $file_name = $key.'.txt';
        $file_path = rtrim(OPENPOS_DIR,'/').'/default/'.$file_name;
        if($this->_filesystem->is_file($file_path))
        {
            return $this->_filesystem->get_contents($file_path);
        }else{
            return '';
        }
    }
    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'openpos_general',
                'title' => __( 'General Settings', 'openpos' )
            ),
            array(
                'id'    => 'openpos_payment',
                'title' => __( 'Payment Settings', 'openpos' )
            ),
            array(
                'id'    => 'openpos_shipment',
                'title' => __( 'Shipping Settings', 'openpos' )
            ),
            array(
                'id'    => 'openpos_label',
                'title' => __( 'Barcode Label Sheet Settings', 'openpos' )
            ),
            array(
                'id'    => 'openpos_receipt',
                'title' => __( 'Print Receipt Settings', 'openpos' )
            ),
            array(
                'id'    => 'openpos_pos',
                'title' => __( 'POS Layout Setting', 'openpos' )
            )
        );
        return $sections;
    }

    function get_settings_fields() {
        global $op_woo;
        $payment_gateways = WC()->payment_gateways->payment_gateways();
        $payment_options = array();

        $openpos_type = $this->settings_api->get_option('openpos_type','openpos_pos');

        foreach ($payment_gateways as $code => $p)
        {
            $payment_options[$code] = $p->title;
        }
        $addition_payments = $this->core->additionPaymentMethods();
        $payment_options = array_merge($payment_options, $addition_payments);

        $shipping_options = array();
        $shipping_methods = WC()->shipping()->get_shipping_methods();
        foreach ($shipping_methods as $shipping_method)
        {
            $code = $shipping_method->id;
            $title = $shipping_method->method_title;
            if(!$title)
            {
                $title = $code;
            }
            $shipping_options[$code] = $title;
        }


        $refund_duration = array();
        $allow_refund_duration = $this->settings_api->get_option('pos_allow_refund','openpos_general');



        $allow_payment_methods = $this->settings_api->get_option('payment_methods','openpos_payment');

        $setting_pos_discount_tax_class = $this->settings_api->get_option('pos_discount_tax_class','openpos_general');

        $addition_general_setting = array();
        $wc_order_status = wc_get_order_statuses();
        if(isset($allow_payment_methods['stripe']))
        {
            $addition_general_setting = array(
                array(
                    'name'    => 'stripe_public_key',
                    'label'   => __( 'Stripe Publishable key', 'openpos' ),
                    'desc'    => '',
                    'type'    => 'text',
                    'default' => ''
                ),
                array(
                    'name'    => 'stripe_secret_key',
                    'label'   => __( 'Stripe Secret key', 'openpos' ),
                    'desc'    => '',
                    'type'    => 'text',
                    'default' => ''
                ),
            );
        }
        if($allow_refund_duration == 'yes_day')
        {
            $refund_duration = array(
                'name'              => 'pos_refund_duration',
                'label'             => __( 'Refund Duration', 'openpos' ),
                'type'              => 'number',
                'default'           => '1',
                'desc'    => __( 'refund duration in day', 'openpos' ),
                'sanitize_callback' => 'sanitize_text_field'
            );
        }
        $tax_included_discount = array();
        $tax_included_discount_rate = array();
        $allow_exchange_partial_refund = array();
        $allow_exchange = $this->settings_api->get_option('allow_exchange','openpos_general');
        $pos_tax_class = $this->settings_api->get_option('pos_tax_class','openpos_general');

        $pos_custom_item = $this->settings_api->get_option('pos_allow_custom_item','openpos_pos');
        $pos_enable_weight_barcode = $this->settings_api->get_option('pos_enable_weight_barcode','openpos_pos');

        $pos_custom_item_tax_class_setting = array();
        $pos_custom_item_tax_rate_setting = array();

        $pos_discount_tax_class_setting = array();
        $pos_discount_tax_rate_setting = array();


        $pos_weight_barcode_format = array();

        $all_tax_classes = wc_get_product_tax_class_options();
        if(!is_array($all_tax_classes))
        {
            $all_tax_classes = array();
        }

        if($pos_enable_weight_barcode == 'yes' )
        {
            $pos_weight_barcode_format = array(
                'name'    => 'pos_weight_barcode_format',
                'label'   => __( 'Scale Barcode Format', 'openpos' ),
                'desc'    => __('Work with barcode scanner device only. I : item code , P : price , W : weight  , Q : quantity . Example: "DDIIIIIDPPPPC" -   "0281981002076" With "02": string to detect barcode generate by scale, "81981" : product barcode , "0207" : price = 2.07$  ', 'openpos' ),
                'type'    => 'text',
                'default' => 'DDIIIIIDPPPPC'
            );
        }
        if($pos_custom_item == 'yes' && $pos_tax_class == 'op_productax')
        {
            

            $pos_custom_item_tax_class_setting = array(
                'name'              => 'pos_custom_item_tax_class',
                'label'             => __( 'Custom Item Tax Class', 'openpos' ),
                'desc'              => __('Custom item tax class. It for POS only','openpos'),
                'default'           => 'op_notax',
                'type'              => 'select',
                'options' => array_merge( array(
                    'op_notax'  =>  __( 'No Tax', 'openpos' )
                ),$all_tax_classes)
            );

            


        }



        if($allow_exchange == 'yes')
        {
            $allow_exchange_partial_refund = array(
                'name'              => 'pos_exchange_partial_refund',
                'label'             => __( 'Refund exchange cash', 'openpos' ),
                'type'              => 'select',
                'default'           => 'no',
                'desc'    => __( 'Allow return cash with remain money amount after exchange', 'openpos' ),
                'options' =>  array(
                    'yes' => __( 'Yes', 'openpos' ),
                    'no'  => __( 'No', 'openpos' ),
                )
            );
        }

        $tax_included_discount = array(
//            'name'              => 'pos_tax_included_discount',
//            'label'             => __( 'Tax Included Discount', 'openpos' ),
//            'type'              => 'select',
//            'default'           => 'no',
//            'desc'    => __( 'Include discount amount when get final tax amount', 'openpos' ),
//            'options' =>  array(
//                'yes' => __( 'Yes', 'openpos' ),
//                'no'  => __( 'No', 'openpos' ),
//            )
        );

        if($pos_tax_class != 'op_productax')
        {


            if($pos_tax_class != 'op_notax')
            {
                $rates = $op_woo->getTaxRates($pos_tax_class);

                $rate_options = array();
                $default_rate_option = 0;
                foreach($rates as $rate_id => $rate)
                {
                    $rate_options[$rate_id] = $rate['label'].' ('.$rate['rate'].'%)';
                }
                if(!empty($rate_options))
                {
                    $default_rate_option = max(array_keys($rate_options));

                    $tax_included_discount_rate = array(
                        'name'              => 'pos_tax_rate_id',
                        'label'             => __( 'Tax Rate', 'openpos' ),
                        'type'              => 'select',
                        'default'           => $default_rate_option,
                        'desc'    => __( 'Choose Tax Rate', 'openpos' ),
                        'options' =>  $rate_options
                    );
                }


            }

        }else{




            $pos_discount_tax_class_setting = array(
                'name'              => 'pos_discount_tax_class',
                'label'             => __( 'Discount Tax Class', 'openpos' ),
                'desc'              => __('Discount Tax Class, both cart discount and coupon. It for POS only','openpos'),
                'default'           => 'op_notax',
                'type'              => 'select',
                'options' => array_merge( array(
                    'op_notax'  =>  __( 'No Tax', 'openpos' )
                ),$all_tax_classes)
            );

            if($setting_pos_discount_tax_class != 'op_notax')
            {
                $rates = $op_woo->getTaxRates($setting_pos_discount_tax_class);

                $rate_options = array();
                foreach($rates as $rate_id => $rate)
                {
                    $rate_options[''.$rate_id] = $rate['label'].' ('.$rate['rate'].'%)';
                }

                if( !empty($rate_options))
                {
                    $rate_options[0] = __( 'Choose tax rate', 'openpos' );
                    $pos_discount_tax_rate_setting = array(
                        'name'              => 'pos_discount_tax_rate',
                        'label'             => __( 'Discount Tax Rate', 'openpos' ),
                        'desc'              => __('Add discount tax rate, this rate for cart discount and coupon discount on POS only','openpos'),
                        'default'           => '0',
                        'type'              => 'select',
                        'options' => $rate_options
                    );
                }
            }

        }

        $dashboard_display_options = array(
            'board' => __('New DashBoard','openpos'),
            'product' => __('Products','openpos'),
            'category' => __('Categories','openpos'),
        );

        if($openpos_type =='restaurant')
        {
            $dashboard_display_options['table'] = __('Tables','openpos');
        }

        $dashboard_display = array(
            'name'              => 'dashboard_display',
            'label'             => __( 'Default Dashboard Display', 'openpos' ),
            'desc'              => __( 'Default display for POS , in case category please set category item on category setting', 'openpos' ),
            'default'           => 'board',
            'type'              => 'select',
            'options' => $dashboard_display_options
        );


        $barcode_key_options  = apply_filters('op_barcode_key_setting',array(
            '_op_barcode' => __('OpenPOS Barcode','openpos'),
            'post_id' => __('Product Id','openpos'),
            '_sku' => __('Product Sku','openpos')
        ));

        $pos_sequential_number_enable = $this->settings_api->get_option('pos_sequential_number_enable','openpos_general');

        $pos_sequential_number = array();
        $pos_sequential_number_prefix = array();
        if($pos_sequential_number_enable == 'yes')
        {

            $current_order_number = get_option('_op_wc_custom_order_number',0);
            if(!$current_order_number)
            {
                $current_order_number = 1;
            }

            $pos_sequential_number = array(
                'name'              => 'pos_sequential_number',
                'label'             => __( 'Sequential: Start order number', 'openpos' ),
                'type'              => 'number',
                'default'           => $current_order_number,
                'desc'    => __( 'Next order number', 'openpos' ).': '.$current_order_number,
                'sanitize_callback' => 'sanitize_text_field'
            );
            $pos_sequential_number_prefix = array(
                'name'              => 'pos_sequential_number_prefix',
                'label'             => __( 'Order number prefix', 'openpos' ),
                'type'              => 'text',
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field'
            );
        }

        $pos_shipping_enable = $this->settings_api->get_option('shipping_methods','openpos_shipment');
        $openpos_shipment =  array(
            array(
                'name'    => 'shipping_methods',
                'label'   => __( 'Shipping Methods', 'openpos' ),
                'desc'    => __( 'Default Shipping methods for POS beside Store pickup', 'openpos' ),
                'type'    => 'multicheck',
                'default' => '',
                'options' => $shipping_options
            ),
            
            array(
                'name'        => 'html',
                'desc'        => __( 'Set Shipping class for active shipping method. Please choose shipping and click save change to see set shipping tax', 'openpos' ),
                'type'        => 'html'
            ),
            
        );
        if(is_array($pos_shipping_enable) && !empty($pos_shipping_enable))
        {
            foreach($pos_shipping_enable as $pos_shipping)
            {
                $shipping_label = isset($shipping_options[$pos_shipping]) ? $shipping_options[$pos_shipping] : '';
                if($shipping_label)
                {
                    $openpos_shipment[] = array(
                        'name'    => 'shipping_tax_class_'.esc_attr($pos_shipping),
                        'label'   => __( 'Tax for', 'openpos' ).' "'.$shipping_label.'"',
                        'desc'    => __( 'Tax class for current shipping method. Tax rate auto generate base on store/outlet address', 'openpos' ),
                        'type'    => 'select',
                        'default' => 'op_notax',
                        'options' => array_merge( array(
                            'op_notax'  =>  __( 'No Tax', 'openpos' )
                        ),$all_tax_classes)
                    );
                }
            }
        }
        
        

        $settings_fields = array(
            'openpos_general' => array(
                array(
                    'name'    => 'pos_pwa_enable',
                    'label'   => __( 'Progressive Web Apps Cached', 'openpos' ),
                    'desc'    => __( 'OpenPOS Progressive Web Apps on Desktop can be ‘installed’ on the user’s device much like native apps. It’s fast. Feel integrated because they launched in the same way as other apps, and run in an app window, without an address bar or tabs. It is reliable because service workers can cache all of the assets they need to run. And it create an engaging experience for users.', 'openpos' ),
                    'type'    => 'select',
                    'default' => 'yes',
                    'options' =>  array(
                        'yes' => __( 'Yes', 'openpos' ),
                        'no'  => __( 'No', 'openpos' ),
                    )
                ),
                array(
                    'name'    => 'pos_sequential_number_enable',
                    'label'   => __( 'Custom Order Number', 'openpos' ),
                    'desc'    => __( 'Custom Sequential Order Numbers for Order create via POS', 'openpos' ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' =>  array(
                        'yes' => __( 'Yes', 'openpos' ),
                        'no'  => __( 'No', 'openpos' ),
                    )
                ),
                $pos_sequential_number,
                $pos_sequential_number_prefix,
                array(
                    'name'    => 'pos_stock_manage',
                    'label'   => __( 'POS Stock Manager', 'openpos' ),
                    'desc'    => __( 'Don\'t allow checkout out of stock product in POS', 'openpos' ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' =>  array(
                        'yes' => __( 'Yes', 'openpos' ),
                        'no'  => __( 'No', 'openpos' ),
                    )
                ),
                
                array(
                    'name'    => 'pos_order_status',
                    'label'   => __( 'POS Order Status', 'openpos' ),
                    'desc'    => __( 'status for those order created by POS', 'openpos' ),
                    'type'    => 'select',
                    'default' => 'wc-completed',
                    'options' =>  $wc_order_status
                ),
                array(
                    'name'              => 'pos_continue_checkout_order_status',
                    'label'             => __( 'Continue Checkout Order Status', 'openpos' ),
                    'desc'              => __( 'Status of online order allow continue checkout on POS. Enter status name to search', 'openpos' ),
                    'default'           => '',
                    'type'              => 'list_tags',
                    'options' =>  array(
                        'yes' => __( 'Yes', 'openpos' ),
                        'no'  => __( 'No', 'openpos' ),
                    )
                ),
                array(
                    'name'    => 'pos_allow_refund',
                    'label'   => __( 'Allow Refund', 'openpos' ),
                    'desc'    => __( 'Refund offline via pos panel', 'openpos' ),
                    'type'    => 'select',
                    'default' => 'yes',
                    'options' =>  array(
                        'yes' => __( 'Always allow', 'openpos' ),
                        'yes_day' => __( 'Allow with durations', 'openpos' ),
                        'no'  => __( 'No Refund', 'openpos' ),
                    )
                ),
                $refund_duration,
                array(
                    'name'    => 'allow_exchange',
                    'label'   => __( 'Allow Exchange', 'openpos' ),
                    'desc'    => __( 'Allow exchange for order made by current session', 'openpos' ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' =>  array(
                        'yes' => __( 'Yes', 'openpos' ),
                        'no'  => __( 'No', 'openpos' ),
                    )
                ),
                $allow_exchange_partial_refund,
                array(
                    'name'    => 'pos_tax_class',
                    'label'   => __( 'Pos Tax Class', 'openpos' ),
                    'desc'    => __( 'Tax Class assign for POS system. Require refresh product list to take effect.', 'openpos' ),
                    'type'    => 'select',
                    'default' => 'op_notax',
                    'options' => array_merge( array(
                        'op_productax' => 'Use Product Tax Class',
                        'op_notax'  => 'No Tax'
                    ),wc_get_product_tax_class_options())
                ),
                array(
                    'name'    => 'pos_cart_discount',
                    'label'   => __( 'Cart Discount Calculation', 'openpos' ),
                    'desc'    => __( 'Cart discount calculation base on', 'openpos' ),
                    'type'    => 'select',
                    'default' => 'after_tax',
                    'options' =>  array(
                        'after_tax' => __( 'After Tax', 'openpos' ),
                        'before_tax'  => __( 'Before Tax', 'openpos' ),
                    )
                ),
                $tax_included_discount_rate,
                $tax_included_discount,
                $pos_discount_tax_class_setting,
                $pos_discount_tax_rate_setting
            ),
            'openpos_payment' => array(
                array(
                    'name'    => 'payment_methods',
                    'label'   => __( 'POS Addition Payment Methods', 'openpos' ),
                    'desc'    => __( 'Payment methods for POS beside cash(default)', 'openpos' ),
                    'type'    => 'multicheck',
                    'default' => 'op_notax',
                    'options' => $payment_options
                ),
            ),
            'openpos_shipment' => $openpos_shipment,
            'openpos_label' => array(
                array(
                    'name'              => 'barcode_meta_key',
                    'label'             => __( 'Barcode Meta Key', 'openpos' ),
                    'desc'    => __( 'Barcode field . Make sure the data is unique on meta key you are selected', 'openpos' ),
                    'type'              => 'select',
                    'default' => '_op_barcode',
                    'options' => $barcode_key_options
                ),
                array(
                    'name'              => 'unit',
                    'label'             => __( 'Unit', 'openpos' ),
                    'type'              => 'select',
                    'default' => 'in',
                    'options' => array(
                        'in' => 'Inch',
                        'mm' => 'Millimeter'
                    )
                ),
                array(
                    'name'              => 'heading-s',
                    'desc'              => __( '<h2>Sheet Setting</h2>', 'openpos' ),
                    'type'              => 'html'
                ),

                array(
                    'name'              => 'sheet_width',
                    'label'             => __( 'Sheet Width', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '8.5',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'sheet_height',
                    'label'             => __( 'Sheet Height', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '11',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'sheet_vertical_space',
                    'label'             => __( 'Vertical Space', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'sheet_horizontal_space',
                    'label'             => __( 'Horizontal Space', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0.125',
                    'sanitize_callback' => 'sanitize_text_field'
                ),

                array(
                    'name'              => 'sheet_margin_top',
                    'label'             => __( 'Margin Top', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0.5',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'sheet_margin_right',
                    'label'             => __( 'Margin Right', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0.188',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'sheet_margin_bottom',
                    'label'             => __( 'Margin Bottom', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0.5',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'sheet_margin_left',
                    'label'             => __( 'Margin Left', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0.188',
                    'sanitize_callback' => 'sanitize_text_field'
                ),


                array(
                    'name'              => 'barcode_label_width',
                    'label'             => __( 'Label Width', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '2.625',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'barcode_label_height',
                    'label'             => __( 'Label Height', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '1',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'barcode_label_padding_top',
                    'label'             => __( 'Padding Top', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0.1',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'barcode_label_padding_right',
                    'label'             => __( 'Padding Right', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0.1',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'barcode_label_padding_bottom',
                    'label'             => __( 'Padding Bottom', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0.1',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'barcode_label_padding_left',
                    'label'             => __( 'Padding Left', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0.1',
                    'sanitize_callback' => 'sanitize_text_field'
                ),

                array(
                    'name'              => 'barcode_label_template',
                    'label'             => __( 'Label Template', 'openpos' ),
                    'desc'              => __( 'use [barcode with="" height=""] to adjust barcode image, [op_product attribute="attribute_name"] with attribute name: <b>name, price ,regular_price, sale_price, width, height,length,weight</b> and accept html,inline style css string', 'openpos' ),
                    'default'           => '[op_product attribute="name"][barcode][op_product attribute="barcode"]',
                    'type'              => 'wysiwyg'
                ),

                array(
                    'name'              => 'heading',
                    'desc'              => __( '<h2>Barcode Setting</h2>', 'openpos' ),
                    'type'              => 'html'
                ),


                array(
                    'name'              => 'barcode_mode',
                    'label'             => __( 'Mode', 'openpos' ),
                    'type'              => 'select',
                    'default' => 'code_128',
                    'options' => array(
                        'code_128' => 'Code 128',
                        'ean_13' => 'EAN-13',
                        'code_39' => 'Code-39',
                        'qrcode' => __( 'QRCode', 'openpos' ),
                    )
                ),
                array(
                    'name'              => 'barcode_width',
                    'label'             => __( 'Width', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '2.625',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'barcode_height',
                    'label'             => __( 'Height', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '1',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
            ),

            'openpos_receipt' => array(
                array(
                    'name'        => 'html',
                    'desc'        => __( 'Those setting for receipt <strong>Default template</strong>.', 'openpos' ),
                    'type'        => 'html'
                ),
                array(
                    'name'              => 'receipt_width',
                    'label'             => __( 'Receipt Width', 'openpos' ),
                    'desc'              => __( 'inch', 'openpos' ),
                    'type'              => 'text',
                    'default'           => '2.28',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'receipt_padding_top',
                    'label'             => __( 'Padding Top', 'openpos' ),
                    'desc'              => __( 'inch', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'receipt_padding_right',
                    'label'             => __( 'Padding Right', 'openpos' ),
                    'desc'              => __( 'inch', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'receipt_padding_bottom',
                    'label'             => __( 'Padding Bottom', 'openpos' ),
                    'desc'              => __( 'inch', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'receipt_padding_left',
                    'label'             => __( 'Padding Left', 'openpos' ),
                    'desc'              => __( 'inch', 'openpos' ),
                    'type'              => 'number',
                    'default'           => '0',
                    'sanitize_callback' => 'sanitize_text_field'
                ),

                array(
                    'name'              => 'receipt_template_header',
                    'label'             => __( 'Receipt Template Header', 'openpos' ),
                    'desc'              => __( 'use [payment_method], [customer_name], [customer_phone],[sale_person], [created_at], [order_number],[order_number_format],[order_note],[order_qrcode width="_number_" height="_number_"],[order_barcode  width="_number_" height="_number_"], [customer_email],[op_warehouse field="_fiel_name"] - (_fiel_name : name, address, city, postal_code,country,phone,email), [op_register field="name"] shortcode to adjust receipt information, accept html string', 'openpos' ),
                    'type'              => 'wysiwyg',
                    'default'           => $this->get_default_value('receipt_template_header')
                ),
                array(
                    'name'              => 'receipt_template_footer',
                    'label'             => __( 'Receipt Template Footer', 'openpos' ),
                    'desc'              => __( 'use [payment_method],[customer_name], [customer_phone], [sale_person], [created_at], [order_number],[order_number_format],[order_qrcode width="_number_" height="_number_"],[order_barcode  width="_number_" height="_number_"],[order_note], [customer_email], [op_warehouse field="_fiel_name"] - (_fiel_name : name, address, city, postal_code,country,phone,email), [op_register field="name"] shortcode to adjust receipt information, accept html string', 'openpos' ),
                    'type'              => 'wysiwyg',
                    'default'           => $this->get_default_value('receipt_template_footer')
                ),
                array(
                    'name'              => 'receipt_css',
                    'label'             => __( 'Receipt Style', 'openpos' ),
                    'desc'              => sprintf('<a  target="_blank" href="'.admin_url('admin-ajax.php?action=print_receipt').'">%s</a>',__( 'click here to preview receipt', 'openpos' )),
                    'type'              => 'textarea_code',
                    'default'           => $this->get_default_value('receipt_css'),
                ),
            ),
            'openpos_pos' => array(
                array(
                    'name'              => 'openpos_logo',
                    'label'             => __( 'POS Logo', 'openpos' ),
                    'desc'              => __( 'Default Logo for POS Panel (ex: 100x50)', 'openpos' ),
                    'default'           => '',
                    'type'              => 'file',
                    'options' => array()
                ),
                array(
                    'name'              => 'openpos_type',
                    'label'             => __( 'POS Type', 'openpos' ),
                    'desc'              => __( 'Default display for POS , their are table management in cafe/restaurant type', 'openpos' ),
                    'default'           => 'grocery',
                    'type'              => 'select',
                    'options' => array(
                        'grocery' => __('Grocery','openpos'),
                        'restaurant' => __('Cafe / Restaurant','openpos'),
                    )
                ),
                $dashboard_display,
                array(
                    'name'              => 'pos_product_grid',
                    'label'             => __( 'Product Grid Size', 'openpos' ),
                    'desc'              => __( 'Grid Size for Products (column x row)  on POS Panel', 'openpos' ),
                    'default'           => array('col' => 4,'row' => 4),
                    'type'              => 'pos_grid',
                    'options' =>  array()
                ),

                array(
                    'name'              => 'pos_category_grid',
                    'label'             => __( 'Category Grid Size', 'openpos' ),
                    'desc'              => __( 'Grid Size for Categories (column x row)   on POS Panel', 'openpos' ),
                    'default'           => array('col' => 2,'row' => 4),
                    'type'              => 'pos_grid',
                    'options' =>  array()
                ),

                array(
                    'name'              => 'pos_language',
                    'label'             => __( 'Default POS Language', 'openpos' ),
                    'desc'              => __( 'Default language on POS. To translate goto pos/assets/i18n/_you_lang.json and update this file', 'openpos' ),
                    'default'           => '_auto',
                    'type'              => 'select',
                    'options' => $this->core->getAllLanguage()
                ),
                array(
                    'name'              => 'pos_allow_custom_item',
                    'label'             => __( 'Allow Add Custom Item', 'openpos' ),
                    'desc'              => __( 'Add custom item , the item do not exist in your system from POS', 'openpos' ),
                    'default'           => 'yes',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),
                $pos_custom_item_tax_class_setting,
                $pos_custom_item_tax_rate_setting,

                array(
                    'name'              => 'pos_allow_custom_note',
                    'label'             => __( 'Allow Add Order Note', 'openpos' ),
                    'desc'              =>  __( 'Add order note from POS','openpos'),
                    'default'           => 'no',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),


                array(
                    'name'              => 'time_frequency',
                    'label'             => __( 'Time Frequency', 'openpos' ),
                    'desc'              => __( 'Time duration POS state checking (in mini seconds)','openpos'),
                    'default'           => '5000',
                    'type'              => 'number'
                ),
                array(
                    'name'              => 'pos_auto_sync',
                    'label'             => __( 'Product Auto Sync', 'openpos' ),
                    'desc'              => __( 'Auto sync product qty by running process in background','openpos'),
                    'default'           => 'yes',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),
                array(
                    'name'              => 'pos_clear_product',
                    'label'             => __( 'Clear Product List ', 'openpos' ),
                    'desc'              => __( 'Auto clear product list on your local data after logout. Recommend set to "No" if you have > 500 products.','openpos'),
                    'default'           => 'yes',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),
                array(
                    'name'              => 'pos_display_outofstock',
                    'label'             => __( 'Display Out of stock', 'openpos' ),
                    'desc'              => __( 'Display out of stock product in POS panel','openpos'),
                    'default'           => 'no',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),
                array(
                    'name'              => 'accept_negative_checkout',
                    'label'             => __( 'Allow Negative Qty', 'openpos' ),
                    'desc'              => __( 'Allow negative qty , grand total  use as refund','openpos'),
                    'default'           => 'no',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),
                array(
                    'name'              => 'pos_change_price',
                    'label'             => __( 'Allow Update Price', 'openpos' ),
                    'desc'              => __( 'Allow change product price on POS panel. Require refresh product list to take effect.','openpos'),
                    'default'           => 'no',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),
                array(
                    'name'              => 'pos_image_width',
                    'label'             => __( 'POS Image Width', 'openpos' ),
                    'desc'              => __( 'Width of image in pos in px','openpos'),
                    'default'           => '208',
                    'type'              => 'number'
                ),
                array(
                    'name'              => 'pos_image_height',
                    'label'             => __( 'POS Image Height', 'openpos' ),
                    'desc'              => __( 'Height of image in pos in px','openpos'),
                    'default'           => '195',
                    'type'              => 'number'
                ),
                array(
                    'name'              => 'pos_custom_css',
                    'label'             => __( 'POS Custom CSS', 'openpos' ),
                    'desc'              => __( 'Custom style for POS with CSS', 'openpos' ),
                    'default'           => '',
                    'type'              => 'textarea_code'
                ),
                array(
                    'name'              => 'pos_categories',
                    'label'             => __( 'POS Category', 'openpos' ),
                    'desc'              => __( 'List of Categories display on POS panel. Enter keyword to search, this field is autocomplete', 'openpos' ),
                    'default'           => '',
                    'type'              => 'category_tags'
                ),
                array(
                    'name'              => 'pos_money',
                    'label'             => __( 'POS Money List', 'openpos' ),
                    'desc'              => __( 'List of money values in your pos. Separate by "|" character. Example: 10|20|30', 'openpos' ),
                    'default'           => '',
                    'type'              => 'text'
                ),
                array(
                    'name'              => 'pos_custom_item_discount_amount',
                    'label'             => __( 'Quick Item Discount Amount', 'openpos' ),
                    'desc'              => __( 'List of quick discount values in your pos. Separate by "|" character. Example: 5|5%|10%', 'openpos' ),
                    'default'           => '',
                    'type'              => 'text'
                ),
                array(
                    'name'              => 'pos_custom_cart_discount_amount',
                    'label'             => __( 'Quick Cart Discount Amount', 'openpos' ),
                    'desc'              => __( 'List of quick discount values in your pos. Separate by "|" character. Example: 5|5%|10%', 'openpos' ),
                    'default'           => '',
                    'type'              => 'text'
                ),
                array(
                    'name'              => 'pos_require_customer_mode',
                    'label'             => __( 'Require customer', 'openpos' ),
                    'desc'              => __( 'Require checkout with customer added only in POS','openpos'),
                    'default'           => 'no',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),
                array(
                    'name'              => 'pos_default_open_cash',
                    'label'             => __( 'Open Cash When Login', 'openpos' ),
                    'desc'              => __( 'Open Cash Adjustment Popup when login to POS','openpos'),
                    'default'           => 'no',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),
                
                array(
                    'name'              => 'pos_search_product_online',
                    'label'             => __( 'Search Mode', 'openpos' ),
                    'desc'              => __( 'The way of search when type keyword on search box on POS','openpos'),
                    'default'           => 'suggestion',
                    'type'              => 'select',
                    'options' => array(
                        'no' => __('Offline - Local browser data search','openpos'),
                        'yes' => __('Online - Seach by your website','openpos'),
                    )
                ),
                array(
                    'name'              => 'search_type',
                    'label'             => __( 'Search Display Type', 'openpos' ),
                    'desc'              => __( 'Layout of result return by search product input ','openpos'),
                    'default'           => 'suggestion',
                    'type'              => 'select',
                    'options' => array(
                        'suggestion' => __('Auto Suggestion','openpos'),
                        'grid' => __('Product Grid Display','openpos'),
                    )
                ),
                array(
                    'name'              => 'search_result_total',
                    'label'             => __( 'Total Search Result', 'openpos' ),
                    'desc'              => __( 'Number of search result', 'openpos' ),
                    'default'           => '10',
                    'type'              => 'number'
                ),
                
                array(
                    'name'              => 'pos_default_checkout_mode',
                    'label'             => __( 'Payment Type', 'openpos' ),
                    'desc'              => __( 'Logic for Payment method type use in POS checkout','openpos'),
                    'default'           => 'single',
                    'type'              => 'select',
                    'options' => array(
                        'multi' => __('Split Multi Payment','openpos'),
                        'single' => __('Single Payment','openpos'),
                    )
                ),
                array(
                    'name'              => 'pos_enable_weight_barcode',
                    'label'             => __( 'Allow Digital Scale', 'openpos' ),
                    'desc'              => __( 'Allow scan barcode with label generate by digital scale','openpos'),
                    'default'           => 'no',
                    'type'              => 'select',
                    'options' => array(
                        'yes' => __('Yes','openpos'),
                        'no' => __('No','openpos'),
                    )
                ),
                $pos_weight_barcode_format,
               
                



            ),

            'openpos_basics' => array(


                array(
                    'name'              => 'number_input',
                    'label'             => __( 'Number Input', 'openpos' ),
                    'desc'              => __( 'Number field with validation callback `floatval`', 'openpos' ),
                    'placeholder'       => __( '1.99', 'openpos' ),
                    'min'               => 0,
                    'max'               => 100,
                    'step'              => '0.01',
                    'type'              => 'number',
                    'default'           => 'Title',
                    'sanitize_callback' => 'floatval'
                ),
                array(
                    'name'        => 'textarea',
                    'label'       => __( 'Textarea Input', 'openpos' ),
                    'desc'        => __( 'Textarea description', 'openpos' ),
                    'placeholder' => __( 'Textarea placeholder', 'openpos' ),
                    'type'        => 'textarea'
                ),
                array(
                    'name'        => 'html',
                    'desc'        => __( 'HTML area description. You can use any <strong>bold</strong> or other HTML elements.', 'openpos' ),
                    'type'        => 'html'
                ),
                array(
                    'name'  => 'checkbox',
                    'label' => __( 'Checkbox', 'openpos' ),
                    'desc'  => __( 'Checkbox Label', 'openpos' ),
                    'type'  => 'checkbox'
                ),
                array(
                    'name'    => 'radio',
                    'label'   => __( 'Radio Button', 'openpos' ),
                    'desc'    => __( 'A radio button', 'openpos' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'selectbox',
                    'label'   => __( 'A Dropdown', 'openpos' ),
                    'desc'    => __( 'Dropdown description', 'openpos' ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'password',
                    'label'   => __( 'Password', 'openpos' ),
                    'desc'    => __( 'Password description', 'openpos' ),
                    'type'    => 'password',
                    'default' => ''
                ),
                array(
                    'name'    => 'file',
                    'label'   => __( 'File', 'openpos' ),
                    'desc'    => __( 'File description', 'openpos' ),
                    'type'    => 'file',
                    'default' => '',
                    'options' => array(
                        'button_label' => 'Choose Image'
                    )
                )
            ),
            'wedevs_advanced' => array(
                array(
                    'name'    => 'color',
                    'label'   => __( 'Color', 'wedevs' ),
                    'desc'    => __( 'Color description', 'openpos' ),
                    'type'    => 'color',
                    'default' => ''
                ),
                array(
                    'name'    => 'password',
                    'label'   => __( 'Password', 'openpos' ),
                    'desc'    => __( 'Password description', 'openpos' ),
                    'type'    => 'password',
                    'default' => ''
                ),
                array(
                    'name'    => 'wysiwyg',
                    'label'   => __( 'Advanced Editor', 'wedevs' ),
                    'desc'    => __( 'WP_Editor description', 'wedevs' ),
                    'type'    => 'wysiwyg',
                    'default' => ''
                ),
                array(
                    'name'    => 'multicheck',
                    'label'   => __( 'Multile checkbox', 'openpos' ),
                    'desc'    => __( 'Multi checkbox description', 'openpos' ),
                    'type'    => 'multicheck',
                    'default' => array('one' => 'one', 'four' => 'four'),
                    'options' => array(
                        'one'   => 'One',
                        'two'   => 'Two',
                        'three' => 'Three',
                        'four'  => 'Four'
                    )
                ),
            )
        );
        $addition_general_setting = apply_filters('op_addition_general_setting',$addition_general_setting);
        $settings_fields['openpos_payment'] = array_merge($settings_fields['openpos_payment'],$addition_general_setting);
        return $settings_fields;
    }

    public function products()
    {
        $rows = array();
        $allow_types = $this->core->getPosProductTypes();
        $current = isset($_REQUEST['current']) ? intval($_REQUEST['current']) : 1;
        $sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : false;
        $searchPhrase  = $_REQUEST['searchPhrase'] ? sanitize_text_field($_REQUEST['searchPhrase']) : false;
        $sortBy = 'date';
        $order = 'DESC';
        if($sort && is_array($sort))
        {
            $key = array_keys($sort);

            $sortBy = end($key);
            if($sortBy == 'id')
            {
                $sortBy = 'ID';
            }
            $order = end($sort);
        }

        $rowCount = $_REQUEST['rowCount'] ? intval($_REQUEST['rowCount']) : get_option( 'posts_per_page' );
        $offet = ($current -1) * $rowCount;
        $ignores = array();
        $args = array(
            'posts_per_page'   => $rowCount,
            'offset'           => $offet,
            'current_page'           => $current,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => $sortBy,
            'order'            => $order,
            'exclude'          => $ignores,
            'post_type'        => $this->core->getPosPostType(),
            'post_status'      => $this->core->getDefaultProductPostStatus(),
            'suppress_filters' => false
        );
        $total = 0;
        if($searchPhrase)
        {
            $args['s'] = $searchPhrase;
            if($_tmp_product = wc_get_product($searchPhrase))
            {
                $fields = array('post_title');
                $tmp_type = $_tmp_product->get_type();
                if(in_array($tmp_type,$allow_types))
                {
                    $total = 1;
                    $_product = $_tmp_product;
                    $type = $_product->get_type();
                    $product_id = $_product->get_id();
                    $post = get_post($product_id);
                    $tmp = array();
                    $thumb = '';
                    if( wc_placeholder_img_src() ) {
                        $thumb = wc_placeholder_img();
                    }
                    $parent_product = false;
                    foreach($fields as $field)
                    {
                        $tmp[$field] = $post->$field;
                    }
                    if($tid = get_post_thumbnail_id($post->ID))
                    {
                        $props = wc_get_product_attachment_props( get_post_thumbnail_id($product_id), $post );
                        $thumb = get_the_post_thumbnail( $post->ID, 'shop_thumbnail', array(
                            'title'  => $props['title'],
                            'alt'    => $props['alt'],
                        ) );
                    }
                    $tmp['action'] = '<a href="'.get_edit_post_link($product_id).'">'.__('edit','openpos').'</a>';
    
                    if($type == 'variation')
                    {
                       $parent_id = $post->post_parent;
                        $parent_product = wc_get_product($parent_id);
                        if($tid = get_post_thumbnail_id($parent_id))
                        {
                            $props = wc_get_product_attachment_props( get_post_thumbnail_id($parent_id), $parent_product );
                            $thumb = get_the_post_thumbnail( $parent_id, 'shop_thumbnail', array(
                                'title'  => $props['title'],
                                'alt'    => $props['alt'],
                            ) );
                        }
                        $tmp['action'] = '<a href="'.get_edit_post_link($parent_id).'">'.__('edit','openpos').'</a>';
    
                    }
                    $tmp['action'] .= '<a href="'.admin_url( 'admin-ajax.php?action=print_barcode&id='.$product_id ).'" target="_blank" class="print-barcode-product-btn">Print Barcode</a>';
                    $tmp['action'] = '<div class="action-row">'.$tmp['action'].'</div>';
                    $tmp['regular_price'] = $_product->get_regular_price();
                    $tmp['sale_price'] = $_product->get_sale_price();
                    $price = $_product->get_price();
                    $tmp['price'] = $price;
                    $barcode = $this->core->getBarcode($product_id);
                    $tmp['barcode'] = '<input type="text" name="barcode['.$product_id.']" class="form-control" disabled value="'.$barcode.'">';
    
    
                    $sub_title = '';
                    if($_product->get_type() == 'variation')
                    {
                        $variation_attributes = $_product->get_attributes();
                        $sub_title = '<p>'.implode(',',$variation_attributes).'</p>';
                    }
                    $tmp['post_title'] .= $sub_title;
    
                    if(!$price)
                    {
                        $price = 0;
                    }
                    $tmp['formatted_price'] = wc_price($price);
                    $qty = $_product->get_stock_quantity();
                    $manage_stock = $_product->get_manage_stock();
                    if($manage_stock)
                    {
                        $tmp['qty'] = '<div class="col-xs-6 pull-left"><input class="form-control"  disabled name="qty['.$product_id.']" type="number" value="'.$qty.'" /></div>';
    
                    }else{
                        $tmp['qty'] = __('Unlimited','openpos');
                    }
                    $tmp['id'] = $product_id;
                    $tmp['product_thumb'] = $thumb;
                    $rows[] = $tmp;
                }
            }

        }

        if(empty($rows))
        {
           
            $posts = $this->core->getProducts($args);
            $posts_array = $posts['posts'];
            $total = $posts['total'];
            $fields = array('post_title');
    
            foreach($posts_array as $post)
            {
                if(is_a($post, 'WP_Post'))
                {
                    $product_id = $post->ID;
                }else{
                    $product_id = $post->get_id();
                    $post = get_post($product_id);
                }
                $_product = wc_get_product($product_id);
                if(!$_product)
                {
                    continue;
                }
                $type = $_product->get_type();
                
                if(in_array($type,$allow_types))
                {
                    $tmp = array();
                    $thumb = '';
                    if( wc_placeholder_img_src() ) {
                        $thumb = wc_placeholder_img();
                    }
                    $parent_product = false;
                    foreach($fields as $field)
                    {
                        $tmp[$field] = $post->$field;
                    }
                    if($tid = get_post_thumbnail_id($post->ID))
                    {
                        $props = wc_get_product_attachment_props( get_post_thumbnail_id($product_id), $post );
                        $thumb = get_the_post_thumbnail( $post->ID, 'shop_thumbnail', array(
                            'title'  => $props['title'],
                            'alt'    => $props['alt'],
                        ) );
                    }
                    $tmp['action'] = '<a href="'.get_edit_post_link($product_id).'">'.__('edit','openpos').'</a>';
    
                    if($type == 'variation')
                    {
                       $parent_id = $post->post_parent;
                        $parent_product = wc_get_product($parent_id);
                        if($tid = get_post_thumbnail_id($parent_id))
                        {
                            $props = wc_get_product_attachment_props( get_post_thumbnail_id($parent_id), $parent_product );
                            $thumb = get_the_post_thumbnail( $parent_id, 'shop_thumbnail', array(
                                'title'  => $props['title'],
                                'alt'    => $props['alt'],
                            ) );
                        }
                        $tmp['action'] = '<a href="'.get_edit_post_link($parent_id).'">'.__('edit','openpos').'</a>';
    
                    }
                    $tmp['action'] .= '<a href="'.admin_url( 'admin-ajax.php?action=print_barcode&id='.$product_id ).'" target="_blank" class="print-barcode-product-btn">Print Barcode</a>';
                    $tmp['action'] = '<div class="action-row">'.$tmp['action'].'</div>';
                    $tmp['regular_price'] = $_product->get_regular_price();
                    $tmp['sale_price'] = $_product->get_sale_price();
                    $price = $_product->get_price();
                    $tmp['price'] = $price;
                    $barcode = $this->core->getBarcode($product_id);
                    $tmp['barcode'] = '<input type="text" name="barcode['.$product_id.']" class="form-control" disabled value="'.$barcode.'">';
    
    
                    $sub_title = '';
                    if($_product->get_type() == 'variation')
                    {
                        $variation_attributes = $_product->get_attributes();
                        $sub_title = '<p>'.implode(',',$variation_attributes).'</p>';
                    }
                    $tmp['post_title'] .= $sub_title;
    
                    if(!$price)
                    {
                        $price = 0;
                    }
    
                    $tmp['formatted_price'] = wc_price($price);
                    $qty = $_product->get_stock_quantity();
                    $manage_stock = $_product->get_manage_stock();
                    if($manage_stock)
                    {
                        $tmp['qty'] = '<div class="col-xs-6 pull-left"><input class="form-control"  disabled name="qty['.$product_id.']" type="number" value="'.$qty.'" /></div>';
    
                    }else{
                        $tmp['qty'] = 'Unlimited';
                    }
                    $tmp['id'] = $product_id;
    
                    $tmp['product_thumb'] = $thumb;
    
                    $rows[] = $tmp;
    
                }
            }
        }
        
        $result = array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $rows,
            'total' => $total

        );
        echo json_encode($result);
        exit;
    }

    public function stock_products_update(){
        global $op_warehouse;
        $params = $_REQUEST;
        if(isset($params['field']) && $params['field'] && isset($params['id']))
        {
                $id = $params['id'];
                $product = wc_get_product($id);
                if($product)
                {
                    if($params['field'] == 'price' && $params['field_value'])
                    {
                        $product->set_price((float)$params['field_value']);
                        $product->set_regular_price((float)$params['field_value']);
                        $product->save();
                    }

                }
        }else{
            $request_data = isset($_REQUEST['qty']) ? $_REQUEST['qty'] : array();
            $request_in_store = isset($_REQUEST['allow_pos']) ? $_REQUEST['allow_pos'] : array();
            foreach($request_data as $product_id => $qty_data)
            {
                foreach($qty_data as $warehouse_id => $qty)
                {
                    if($warehouse_id > 0)
                    {
                        if(isset($request_in_store[$product_id][$warehouse_id]) && $request_in_store[$product_id][$warehouse_id] == 'yes')
                        {
                            $op_warehouse->set_qty($warehouse_id,$product_id,(int)$qty);
                        }else{
                            $op_warehouse->remove_instore($warehouse_id,$product_id);
                        }

                    }else{
                        $_product = wc_get_product($product_id);
                        $_product->set_manage_stock(true);
                        $_product->set_stock_quantity($qty);
                        $_product->save();
                        $this->core->addProductChange($product_id,$warehouse_id);
                        //update_option('_openpos_product_version_0',time());

                        

                    }
                }
            }
        }
    }

    public function stock_products(){
        global $op_warehouse;

        $rows = array();
        $current = isset($_REQUEST['current']) ? intval($_REQUEST['current']) : 1;
        $warehouse_id = isset($_REQUEST['warehouse_id']) ? intval($_REQUEST['warehouse_id']) : -1;
        $sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : false;
        $searchPhrase  = $_REQUEST['searchPhrase'] ? sanitize_text_field($_REQUEST['searchPhrase']) : false;
        $sortBy = 'date';
        $order = 'DESC';
        if($sort && is_array($sort))
        {
            $key = array_keys($sort);

            $sortBy = end($key);
            if($sortBy == 'id')
            {
                $sortBy = 'ID';
            }
            $order = end($sort);
        }


        $rowCount = $_REQUEST['rowCount'] ? intval($_REQUEST['rowCount']) : get_option( 'posts_per_page' );
        $offet = ($current -1) * $rowCount;

        $variable_arg = array(
            'posts_per_page'   => -1,
            'post_type'        => array('product'),
            'product_type' => 'variable'
        );

        $variable_array = get_posts($variable_arg);
        $ignores = array();
        foreach($variable_array as $a)
        {
            $ignores[] = $a->ID;
        }

        $args = array(
            'posts_per_page'   => $rowCount,
            'offset'           => $offet,
            'current_page'           => $current,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => $sortBy,
            'order'            => $order,
            'exclude'          => $ignores,
            'post_type'        => $this->core->getPosPostType(),
            'post_status'      => $this->core->getDefaultProductPostStatus(),
            'suppress_filters' => false
        );
        $search_product_id = 0;
        if($searchPhrase)
        {

            $args['s'] = $searchPhrase;
            $tmp = (int)$searchPhrase;
            if($tmp)
            {
               $tmp_product = wc_get_product($tmp);
               if($tmp_product)
               {
                   $tmp_post = $tmp_product->get_type();
                   if($tmp_post != 'variable')
                   {
                       $search_product_id = $tmp_product->get_id();
                   }

               }
            }
        }
        if($search_product_id)
        {
            $posts_array = array(
                    get_post($search_product_id)
            );
            $total = 1;
        }else{
            $args = apply_filters('op_load_stock_product_args',$args);
            
            $posts = $this->core->getProducts($args);

            
            $posts_array = $posts['posts'];

            $total = $posts['total'];
        }



        $fields = array('post_title');

        $warehouses = $op_warehouse->warehouses();
        
        foreach($posts_array as $post)
        {
            if(is_a($post, 'WP_Post'))
            {
                $product_id = $post->ID;
            }else{
                $product_id = $post->get_id();
                $post = get_post($product_id);
            }
            $_product = wc_get_product($product_id);
            if(!$_product)
            {
                continue;
            }
            $type = $_product->get_type();
            $allow_types = $this->core->getPosProductTypes();
            if(in_array($type,$allow_types))
            {
                $tmp = array();
                $thumb = '';
                if( wc_placeholder_img_src() ) {
                    $thumb = wc_placeholder_img();
                }

                foreach($fields as $field)
                {
                    $tmp[$field] = $post->$field;
                }

                $tid = get_post_thumbnail_id($post->ID);
                if($tid)
                {
                    $props = wc_get_product_attachment_props( get_post_thumbnail_id($product_id), $post );
                    $thumb = get_the_post_thumbnail( $post->ID, 'shop_thumbnail', array(
                        'title'  => $props['title'],
                        'alt'    => $props['alt'],
                    ) );
                }
                $tmp['action'] = '<a href="javascript:void(0)" class="update-row" data-id="'.$product_id.'">'.__('Update','openpos').'</a>';

                if($type == 'variation')
                {
                    $parent_id = $post->post_parent;
                    $parent_product = wc_get_product($parent_id);
                    if(!$tid)
                    {
                        if($tid = get_post_thumbnail_id($parent_id))
                        {
                            $props = wc_get_product_attachment_props( get_post_thumbnail_id($parent_id), $parent_product );
                            $thumb = get_the_post_thumbnail( $parent_id, 'shop_thumbnail', array(
                                'title'  => $props['title'],
                                'alt'    => $props['alt'],
                            ) );
                        }
                    }
                }

                $tmp['action'] = '<div class="action-row">'.$tmp['action'].'</div>';
                $tmp['regular_price'] = $_product->get_regular_price();
                $tmp['sale_price'] = $_product->get_sale_price();
                $price = $_product->get_price();
                $tmp['price'] = $price;
                $barcode = $this->core->getBarcode($product_id);
                $tmp['barcode'] = $barcode;
                $sub_title = '';
                if($_product->get_type() == 'variation')
                {
                    $variation_attributes = $_product->get_attributes();
                    $sub_title = '<p>'.implode(',',$variation_attributes).'</p>';
                }
                $tmp['post_title'] .= $sub_title;

                if(!$price)
                {
                    $price = 0;
                }

                $tmp['formatted_price'] = '<div class="vna-row-price row"><div class="col-md-8 text-right"><input type="text" value="'.$price.'" class="row-price-input form-control" /><div class="row-price-span">'.wc_price($price).'</div> </div><div class="col-md-4 text-left"> <a href="javascript:void(0)" data-id="'.$product_id.'" class="click-edit-price-a"><span class="glyphicon glyphicon-pencil"></span><span class="glyphicon glyphicon-saved"></span></a></div></div>';
                $qty = $_product->get_stock_quantity();




                $tmp['qty_html'] = '<form id="product-row-'.$product_id.'">';
                $tmp['total_qty'] = 0;

                foreach($warehouses as $w)
                {
                    $tmp_qty_html = '';
                    $read_only = '';
                    $checked = 'checked';
                    if(!$op_warehouse->is_instore($w['id'],$product_id))
                    {
                        $checked = '';
                        $read_only = 'readonly';
                    }
                    if($warehouse_id == -1)
                    {

                        $qty = $op_warehouse->get_qty($w['id'],$product_id);

                        $tmp['total_qty']  += $qty;


                        if($w['id'] > 0)
                        {
                            if($qty === false)
                            {
                                $read_only = 'readonly';
                            }
                            $tmp_qty_html .= '<div class="warehouse-product-qty"> <p>'.$w['name'].': </p><p><input '.$read_only.'  class="form-text-input input-control product-qty-warehouse" name="qty['.$product_id.']['.$w['id'].']" type="number" value="'.$qty.'"/><input class="input-control product-allow-warehouse" type="checkbox" name="allow_pos['.$product_id.']['.$w['id'].']" value="yes" '.$checked.' /></p></div>';
                        }else{
                            $tmp_qty_html .= '<div class="warehouse-product-qty"> <p>'.$w['name'].': </p><p><input class="form-text-input input-control product-qty-warehouse" name="qty['.$product_id.']['.$w['id'].']" type="number" value="'.$qty.'"/></p></div>';
                        }

                    }else{
                        if($w['id'] == $warehouse_id)
                        {
                            $qty = $op_warehouse->get_qty($w['id'],$product_id);

                            $tmp['total_qty']  += $qty;

                            if($w['id'] > 0)
                            {
                                if($qty === false)
                                {
                                    $read_only = 'readonly';
                                }
                                $tmp_qty_html .= '<div class="warehouse-product-qty"> <p>'.$w['name'].': </p><p><input '.$read_only.' class="form-text-input input-control product-qty-warehouse" name="qty['.$product_id.']['.$w['id'].']" type="number" value="'.$qty.'"/><input  class="input-control product-allow-warehouse" type="checkbox" name="allow_pos['.$product_id.']['.$w['id'].']" value="yes" '.$checked.' /></p></div>';
                            }else{
                                $tmp_qty_html .= '<div class="warehouse-product-qty"> <p>'.$w['name'].': </p><p><input class="form-text-input input-control product-qty-warehouse" name="qty['.$product_id.']['.$w['id'].']" type="number" value="'.$qty.'"/></p></div>';
                            }


                        }
                    }
                    $tmp['qty_html'] .= apply_filters('op_warehouse_stock_qty_html_input',$tmp_qty_html,$tmp,$w);


                }
                $tmp['qty_html'] .= '</form>';
                $tmp['id'] = $product_id;

                $tmp['product_thumb'] = '<div class="vna-cell-image"><a href="javascript:void(0);" class="upload-a" data-id="'.$product_id.'"><span class="glyphicon glyphicon-camera"></span></a>'.$thumb.'</div>';

                $rows[] =  apply_filters('op_warehouse_stock_row_data',$tmp,$post,$warehouse_id);


            }


        }


        $result = array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $rows,
            'total' => $total

        );
        echo json_encode($result);
        exit;
    }

    public function get_inventories(){
        global $op_warehouse;
        $warehouse_id = isset($_REQUEST['warehouse_id']) ? intval($_REQUEST['warehouse_id']) : 0;
        $rows = array();
        $current = isset($_REQUEST['current']) ? intval($_REQUEST['current']) : 1;
        $sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : false;
        $searchPhrase  = $_REQUEST['searchPhrase'] ? sanitize_text_field($_REQUEST['searchPhrase']) : false;
        $sortBy = 'date';
        $order = 'DESC';
        if($sort && is_array($sort))
        {
            $key = array_keys($sort);

            $sortBy = end($key);
            if($sortBy == 'id')
            {
                $sortBy = 'ID';
            }
            $order = end($sort);
        }


        $rowCount = $_REQUEST['rowCount'] ? intval($_REQUEST['rowCount']) : get_option( 'posts_per_page' );
        $offet = ($current -1) * $rowCount;

        $variable_arg = array(
            'posts_per_page'   => -1,
            'post_type'        => array('product'),
            'product_type' => 'variable'
        );

        $variable_array = get_posts($variable_arg);
        $ignores = array();
        foreach($variable_array as $a)
        {
            $ignores[] = $a->ID;
        }

        $args = array(
            'posts_per_page'   => $rowCount,
            'offset'           => $offet,
            'current_page'           => $current,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => $sortBy,
            'order'            => $order,
            'exclude'          => $ignores,
            'post_type'        => $this->core->getPosPostType(),
            'post_status'      => $this->core->getDefaultProductPostStatus(),
            'suppress_filters' => false
        );
        if($searchPhrase)
        {
            $args['s'] = $searchPhrase;
        }

        $posts = $this->core->getProducts($args);
        $posts_array = $posts['posts'];
        $total = $posts['total'];
        $fields = array('post_title');
       
        foreach($posts_array as $post)
        {
            if(is_a($post, 'WP_Post'))
            {
                $product_id = $post->ID;
            }else{
                $product_id = $post->get_id();
                $post = get_post($product_id);
            }
            $_product = wc_get_product($product_id);
            if(!$_product)
            {
                continue;
            }    
            $type = $_product->get_type();
            $allow_types = $this->core->getPosProductTypes();
            if(in_array($type,$allow_types))
            {
                $tmp = array();
                $thumb = '';
                if( wc_placeholder_img_src() ) {
                    $thumb = wc_placeholder_img();
                }
                $parent_product = false;
                foreach($fields as $field)
                {
                    $tmp[$field] = $post->$field;
                }


                if($tid = get_post_thumbnail_id($post->ID))
                {
                    $props = wc_get_product_attachment_props( get_post_thumbnail_id($product_id), $post );
                    $thumb = get_the_post_thumbnail( $post->ID, 'shop_thumbnail', array(
                        'title'  => $props['title'],
                        'alt'    => $props['alt'],
                    ) );
                }
                $tmp['action'] = '<a href="'.get_edit_post_link($product_id).'">'.__('edit','openpos').'</a>';

                if($type == 'variation')
                {
                    $parent_id = $post->post_parent;
                    $parent_product = wc_get_product($parent_id);
                    if($tid = get_post_thumbnail_id($parent_id))
                    {
                        $props = wc_get_product_attachment_props( get_post_thumbnail_id($parent_id), $parent_product );
                        $thumb = get_the_post_thumbnail( $parent_id, 'shop_thumbnail', array(
                            'title'  => $props['title'],
                            'alt'    => $props['alt'],
                        ) );
                    }
                    $tmp['action'] = '<a href="'.get_edit_post_link($parent_id).'">'.__('edit','openpos').'</a>';

                }

                $tmp['regular_price'] = $_product->get_regular_price();
                $tmp['sale_price'] = $_product->get_sale_price();
                $price = $_product->get_price();
                $tmp['price'] = $price;
                $barcode = $this->core->getBarcode($product_id);
                $tmp['barcode'] = $barcode;

                if(!$price)
                {
                    $price = 0;
                }
                $tmp['formatted_price'] = wc_price($price);

                if($warehouse_id > 0)
                {

                    $qty = $op_warehouse->get_qty($warehouse_id,$product_id);
                    $manage_stock = true;

                }else{
                    $manage_stock = $_product->get_manage_stock();
                    $qty = $_product->get_stock_quantity();
                }
                if($manage_stock)
                {
                    $tmp['qty'] = '<div class="col-xs-6 pull-left"><input class="form-control"  disabled name="qty['.$product_id.']" type="text" value="'.$qty.'" /></div>';

                }else{
                    $tmp['qty'] = '<div class="col-xs-6 pull-left"><input class="form-control"  disabled name="qty['.$product_id.']" type="text" value="" /></div>';
                }
                $tmp['id'] = $product_id;

                $tmp['product_thumb'] = $thumb;


                $rows[] =  apply_filters('op_warehouse_inventory_row',$tmp,$warehouse_id,$post);


            }


        }


        $result = array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $rows,
            'total' => $total

        );
        echo json_encode($result);
        exit;
    }
    public function transactions(){
        global $op_register;
        global $op_warehouse;

        $rows = array();

        $current = isset($_REQUEST['current']) ? intval($_REQUEST['current']) : 1;

        $sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : false;
        $warehouse_id  = isset($_REQUEST['warehouse']) ? $_REQUEST['warehouse'] : 0;
        $register_id  = isset($_REQUEST['register']) ? $_REQUEST['register'] : 0;

        $searchPhrase  = $_REQUEST['searchPhrase'] ? sanitize_text_field($_REQUEST['searchPhrase']) : false;
        $sortBy = 'date';
        $order = 'DESC';
        if($sort && is_array($sort))
        {
            $key = array_keys($sort);

            $sortBy = end($key);
            if($sortBy == 'id')
            {
                $sortBy = 'ID';
            }
            $order = end($sort);
        }


        $rowCount = $_REQUEST['rowCount'] ? intval($_REQUEST['rowCount']) : get_option( 'posts_per_page' );

        $offet = ($current -1) * $rowCount;


        $args = array(
            'posts_per_page'   => $rowCount,
            'offset'           => $offet,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => $sortBy,
            'order'            => $order,
            'post_type'        => array('op_transaction'),
            'post_status'      => 'any',
            'suppress_filters' => false
        );
        $meta_query = array();

        if($register_id)
        {
            $register_meta_key = $op_register->get_transaction_meta_key();
            $meta_query[] = array(
                    'key'     => $register_meta_key,
                    'value'   => $register_id,
                    'compare' => '=',
                );
        }
        if($warehouse_id)
        {
            $warehouse_meta_key = $op_warehouse->get_transaction_meta_key();
            $meta_query[] = array(
                'key'     => $warehouse_meta_key,
                'value'   => $warehouse_id,
                'compare' => '=',
            );
        }
        if(!empty($meta_query))
        {
            $args['meta_query'] = $meta_query;
        }

        if($searchPhrase)
        {
            $args['s'] = $searchPhrase;
        }
        $get_posts = new WP_Query($args);
        $posts = array('total'=>$get_posts->found_posts,'posts' => $get_posts->get_posts());

        $posts_array = $posts['posts'];
        $total = $posts['total'];

        $cashdrawer_key = $op_register->get_transaction_meta_key();
        foreach($posts_array as $post)
        {
            $id = $post->ID;
            $user_id = get_post_meta($id,'_user_id',true);
            $register = 'Unknown';
            $name = 'Unknown';
            if($register_id = get_post_meta($id,$cashdrawer_key,true))
            {
                $register_details = $op_register->get($register_id);
                if($register_details && isset($register_details['name']))
                {
                    $register = $register_details['name'];
                }

            }
            if($user_id)
            {
                $user = get_user_by('ID',$user_id);
                $name = $user->display_name;
            }
            $method_code = get_post_meta($id,'_payment_code',true);
            $method_name = get_post_meta($id,'_payment_name',true);
            if(!$name)
            {
                $method_name = $method_code;
            }
            if(!$method_name)
            {
                $method_name = __('Cash','openpos');
            }
            $created_at_time =  get_post_meta($id,'_created_at',true);
            
            $created_at =  $this->core->render_ago_date_by_time_stamp($post->post_date);
            $created_at_html =  "<p>".$created_at."</p><p class='pos-local-time'>".$created_at_time."</p>";
            $tmp = array(
                'id' => $id,
                'title' => $post->post_title,
                'in_amount' => wc_price(get_post_meta($id,'_in_amount',true)),
                'out_amount'=> wc_price(get_post_meta($id,'_out_amount',true)),
                'payment_name'=> $method_name,
                'created_at'=> $created_at_html,
                'register' => $register,
                'created_by' => $name
            );
            $rows[] = $tmp;


        }


        $result = array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $rows,
            'total' => $total

        );
        echo json_encode($result);
        exit;
    }

    public function orders(){
        global $op_register;
        global $op_warehouse;

        $rows = array();

        $current = isset($_REQUEST['current']) ? intval($_REQUEST['current']) : 1;

        $sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : false;
        $warehouse_id  = isset($_REQUEST['warehouse']) ? $_REQUEST['warehouse'] : 0;
        $register_id  = isset($_REQUEST['register']) ? $_REQUEST['register'] : 0;

        $searchPhrase  = $_REQUEST['searchPhrase'] ? sanitize_text_field($_REQUEST['searchPhrase']) : false;
        $sortBy = 'date';
        $order = 'DESC';
        if($sort && is_array($sort))
        {
            $key = array_keys($sort);

            $sortBy = end($key);
            if($sortBy == 'id')
            {
                $sortBy = 'ID';
            }
            $order = end($sort);
        }


        $rowCount = $_REQUEST['rowCount'] ? intval($_REQUEST['rowCount']) : get_option( 'posts_per_page' );

        $offet = ($current -1) * $rowCount;


        $args = array(
            'posts_per_page'   => $rowCount,
            'offset'           => $offet,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => $sortBy,
            'order'            => $order,
            'post_type'        => array('shop_order'),
            'post_status'      => 'any',
            'suppress_filters' => false
        );
        $meta_query = array();

        if($register_id)
        {
            $register_meta_key = $op_register->get_transaction_meta_key();
            $meta_query[] = array(
                'key'     => $register_meta_key,
                'value'   => $register_id,
                'compare' => '=',
            );
        }
        if($warehouse_id)
        {
            $warehouse_meta_key = $op_warehouse->get_transaction_meta_key();
            $meta_query[] = array(
                'key'     => $warehouse_meta_key,
                'value'   => $warehouse_id,
                'compare' => '=',
            );
        }

        $meta_query[] =  array(
            'key' => '_op_order_source',
            'value' => 'openpos',
            'compare' => '=',
        );

        if(!empty($meta_query))
        {
            $args['meta_query'] = $meta_query;
        }


        if($searchPhrase)
        {
            $args['post__in'] = [$searchPhrase];
        }

        $get_posts = new WP_Query($args);
        $posts = array('total'=>$get_posts->found_posts,'posts' => $get_posts->get_posts());

        $posts_array = $posts['posts'];
        $total = $posts['total'];


        foreach($posts_array as $post)
        {
            $id = $post->ID;
            $order = wc_get_order($id);

            $register_id = get_post_meta($id,'_pos_order_cashdrawer',true);

            $register_name = __('Unknown' , 'openpos');
            $register = $op_register->get($register_id);
            if(!empty($register))
            {
                $register_name = $register['name'];
            }

            $cashier_id = get_post_field( 'post_author', $id);
            $cashier = get_user_by('ID',$cashier_id);
            $cashier_name = 'unknown';

            if($cashier)
            {
                $cashier_name = $cashier->display_name;
            }
            $seller_name = $cashier_name;
            $_op_sale_by_person_id = get_post_meta($id,'_op_sale_by_person_id',true);
            if($_op_sale_by_person_id)
            {
                $seller = get_user_by('ID',$_op_sale_by_person_id);
                if($seller)
                {
                    $seller_name = $seller->display_name;
                }
            }


            $by_html = '<p><b>C:</b> '.$cashier_name.'</p>';
            $by_html .= '<p><b>S:</b> '.$seller_name.'</p>';
            $created_at = $this->core->render_order_date_column($order);
            $_op_order =  get_post_meta($id,'_op_order',true);
            $created_at_local = '';
            if($_op_order && isset($_op_order['created_at']))
            {
                $created_at_local = $_op_order['created_at'];
            }
           
            $created_at_html = '<p>'.$created_at.'</p>';
            if($created_at_local)
            {
                $created_at_html .= '<p class="pos-local-time">'.$created_at_local.'</p>';
            }

            $status_html = '<span class="order_status '.esc_attr($order->get_status()).'">'.$order->get_status().'</span>';
            $view_url = get_edit_post_link($id);
            $order_number_str = '<a class="op-order-number" href="'.esc_url($view_url).'">#'.$order->get_order_number().'</a>';
            $tmp = array(
                'id' => $id,
                'order_number' => $order_number_str,
                'source' => $register_name,
                'created_at' => $created_at_html,
                'total'=> wc_price($order->get_total()),
                'view_url'=> '<a href="'.esc_url($view_url).'" class="order-preview" data-order-id="666" title="Preview">Preview</a>',
                'created_by' => $by_html,
                'status' => $status_html

            );
            $rows[] = $tmp;


        }


        $result = array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $rows,
            'total' => $total

        );
        echo json_encode($result);
        exit;
    }

    public function admin_style() {
        $info = $this->core->getPluginInfo();
        $allow_bootstrap = array('openpos-dasboard','op-products','op-cashiers','op-transactions','op-orders','op-reports','op-sessions','op-registers','op-warehouses','op-stock','op-setting','op-tables','op-receipt-template');
        $all_pos_page = array('op-products','op-cashiers','op-transactions','op-orders','op-reports','op-sessions','op-registers','op-warehouses','op-stock','op-setting','openpos-dasboard','op-tables','op-receipt-template');

        $current_page = isset( $_REQUEST['page'])  ?  esc_attr($_REQUEST['page']): false;


        if(in_array($current_page,$all_pos_page))
        {

            if(in_array($current_page,$allow_bootstrap))
            {
                wp_enqueue_style('openpos.bootstrap', OPENPOS_URL.'/assets/css/bootstrap.min.css','',$info['Version']);
                wp_enqueue_script('openpos.bootstrap', OPENPOS_URL.'/assets/js/bootstrap.min.js','jquery',$info['Version']);

            }
            if($current_page == 'openpos-dasboard' )
            {
                wp_enqueue_script('chart.js', OPENPOS_URL.'/assets/js/Chart.min.js',$info['Version']);
              
            }
            if($current_page == 'op-reports')
            {
                wp_enqueue_script('chart.js', OPENPOS_URL.'/assets/js/Chart.min.js',$info['Version']);
            }


            if($current_page == 'op-reports' || $current_page == 'op-sessions')
            {
                wp_enqueue_style('openpos.admin-jquery.datatable', OPENPOS_URL.'/assets/css/datatable.css','',$info['Version']);
                wp_enqueue_style('openpos.admin-jquery.datatable.bootstrap', OPENPOS_URL.'/assets/css/datatable-bootstrap.css','',$info['Version']);
                wp_enqueue_style( 'jquery-ui-style', WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), $info['Version'] );

                wp_enqueue_script('openpos.admin-jquery.datatable', OPENPOS_URL.'/assets/js/datatable.js',['jquery','jquery-ui-core','jquery-ui-datepicker'],$info['Version']);
                wp_enqueue_script('openpos.admin-jquery.datatable.jquery', OPENPOS_URL.'/assets/js/datatable.jquery.js','jquery',$info['Version']);
                wp_enqueue_script('openpos.admin-jquery.datatable.jquery.csv', OPENPOS_URL.'/assets/js/jquery.tabletoCSV.js','jquery',$info['Version']);

            }else{

                wp_enqueue_style('openpos.admin-jquery.bootgrid', OPENPOS_URL.'/assets/css/jquery.bootgrid.min.css','',$info['Version']);
                wp_enqueue_script('openpos.admin-jquery.bootgrid', OPENPOS_URL.'/assets/js/jquery.bootgrid.min.js','jquery',$info['Version']);
            }


            wp_enqueue_script('openpos.admin.js', OPENPOS_URL.'/assets/js/admin.js','jquery',$info['Version']);
            $vars['ajax_url'] = admin_url('admin-ajax.php');
            wp_localize_script('openpos.admin.js', 'openpos_admin', $vars);
        }
        wp_enqueue_style('openpos.admin', OPENPOS_URL.'/assets/css/admin.css','',$info['Version']);
        
        if($current_page == 'op-setting' )
        {
            wp_dequeue_style( 'select2' );
            wp_deregister_style( 'select2' );
    
            wp_dequeue_script( 'select2');
            wp_deregister_script('select2');
    
            wp_dequeue_script( 'selectWoo');
            wp_deregister_script('selectWoo');
        }

       
        
        

    }

    function add_store_setting_column($content,$column_name,$term_id){

        return $content;
    }

    function store_setting_column_header( $columns ){
        $columns['header_name'] = __( 'Action','openpos' );
        return $columns;
    }

    function store_setting_column_content( $value, $column_name, $tax_id ){

        $href = '';
        return '<a href="'.esc_url($href).'">'.__('Setting','openpos').'</a>';

    }

    function register_post_types()
    {
        register_post_type( 'op_transaction',
                array(
                    'labels'              => array(
                        'name'                  => __( 'Transactions', 'openpos' ),
                        'singular_name'         => __( 'Transaction', 'openpos' )
                    ),
                    'description'         => __( 'This is where you can add new transaction that customers can use in your store.', 'openpos' ),
                    'public'              => false,
                    'show_ui'             => false,
                    'capability_type'     => 'op_transaction',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => false,
                    'exclude_from_search' => true,
                    'show_in_menu'        => false,
                    'hierarchical'        => false,
                    'rewrite'             => false,
                    'query_var'           => false,
                    'supports'            => array( 'title','author' ),
                    'show_in_nav_menus'   => false,
                    'show_in_admin_bar'   => false
                )

        );
       
        register_post_type( 'op_z_report',
                array(
                    'labels'              => array(
                        'name'                  => __( 'Z-Report', 'openpos' ),
                        'singular_name'         => __( 'Z-Report', 'openpos' )
                    ),
                    'description'         => __( 'This is where you can add new transaction that customers can use in your store.', 'openpos' ),
                    'public'              => false,
                    'show_ui'             => false,
                    'capability_type'     => 'op_report',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => false,
                    'exclude_from_search' => true,
                    'show_in_menu'        => false,
                    'hierarchical'        => false,
                    'rewrite'             => false,
                    'query_var'           => false,
                    'supports'            => array( 'title','author','content' ),
                    'show_in_nav_menus'   => false,
                    'show_in_admin_bar'   => false
                )

        );

        register_post_type( '_op_warehouse',
                array(
                    'labels'              => array(
                        'name'                  => __( 'Warehouse', 'openpos' ),
                        'singular_name'         => __( 'Warehouse', 'openpos' )
                    ),
                    'description'         => __( 'This is where you can add new transaction that customers can use in your store.', 'openpos' ),
                    'public'              => false,
                    'show_ui'             => false,
                    'capability_type'     => 'op_report',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => false,
                    'exclude_from_search' => true,
                    'show_in_menu'        => false,
                    'hierarchical'        => false,
                    'rewrite'             => false,
                    'query_var'           => false,
                    'supports'            => array( 'title','author','content' ),
                    'show_in_nav_menus'   => false,
                    'show_in_admin_bar'   => false
                )

        );

        register_post_type( '_op_table',
                array(
                    'labels'              => array(
                        'name'                  => __( 'Table', 'openpos' ),
                        'singular_name'         => __( 'Table', 'openpos' )
                    ),
                    'description'         => __( 'This is where you can add new transaction that customers can use in your store.', 'openpos' ),
                    'public'              => false,
                    'show_ui'             => false,
                    'capability_type'     => 'op_report',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => false,
                    'exclude_from_search' => true,
                    'show_in_menu'        => false,
                    'hierarchical'        => false,
                    'rewrite'             => false,
                    'query_var'           => false,
                    'supports'            => array( 'title','author','content' ),
                    'show_in_nav_menus'   => false,
                    'show_in_admin_bar'   => false
                )

        );

        register_post_type( '_op_register',
                array(
                    'labels'              => array(
                        'name'                  => __( 'Register', 'openpos' ),
                        'singular_name'         => __( 'Register', 'openpos' )
                    ),
                    'description'         => __( 'This is where you can add new transaction that customers can use in your store.', 'openpos' ),
                    'public'              => false,
                    'show_ui'             => false,
                    'capability_type'     => 'op_report',
                    'map_meta_cap'        => true,
                    'publicly_queryable'  => false,
                    'exclude_from_search' => true,
                    'show_in_menu'        => false,
                    'hierarchical'        => false,
                    'rewrite'             => false,
                    'query_var'           => false,
                    'supports'            => array( 'title','author','content' ),
                    'show_in_nav_menus'   => false,
                    'show_in_admin_bar'   => false
                )

        );

    }

    public function get_pos_url(){
        $pos_url = OPENPOS_URL.'/pos/';
        return  apply_filters('op_pos_url',$pos_url);
    }

    public function admin_bar_menus( $wp_admin_bar ) {
        if ( ! is_admin() || ! is_user_logged_in() ) {
            return;
        }
        // Show only when the user is a member of this site, or they're a super admin.
        if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
            return;
        }
        $pos_url = $this->get_pos_url();
        // Add an option to visit the store.
        $wp_admin_bar->add_node( array(
            'parent' => 'site-name',
            'id'     => 'view-pos',
            'target'     => '_blank',
            'title'  => __( 'Visit POS', 'woocommerce' ),
            'href'   => $pos_url,
        ) );
    }

    function pos_admin_menu() {
        $openpos_type = $this->settings_api->get_option('openpos_type','openpos_pos');
        $page = add_menu_page( __( 'Open POS', 'openpos' ), __( 'POS', 'openpos' ),'manage_woocommerce','openpos-dasboard',array($this,'dashboard'),plugins_url('woocommerce-openpos/assets/images/pos.png'),58 );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );

        $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Orders', 'openpos' ),  __( 'Orders', 'openpos' ) , 'manage_woocommerce', 'op-orders', array( $this, 'orders_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );

        $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Transactions', 'openpos' ),  __( 'Transactions', 'openpos' ) , 'manage_woocommerce', 'op-transactions', array( $this, 'transactions_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );

        $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Products', 'openpos' ),  __( 'Products Barcode', 'openpos' ) , 'manage_woocommerce', 'op-products', array( $this, 'products_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );

        $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Staffs', 'openpos' ),  __( 'Store Staff', 'openpos' ) , 'manage_options', 'op-cashiers', array( $this, 'cashier_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );



        $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Registers', 'openpos' ),  __( 'Registers', 'openpos' ) , 'manage_options', 'op-registers', array( $this, 'register_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );

        $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Outlets', 'openpos' ),  __( 'Outlets', 'openpos' ) , 'manage_options', 'op-warehouses', array( $this, 'warehouse_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );

        if($openpos_type == 'restaurant')
        {
            $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Tables', 'openpos' ),  __( 'Tables', 'openpos' ) , 'manage_options', 'op-tables', array( $this, 'table_page' ) );
            add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );
        }

        $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Adjust Stock', 'openpos' ),  __( 'Stock Overview', 'openpos' ) , 'manage_woocommerce', 'op-stock', array( $this, 'stock_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );

        $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Reports', 'openpos' ),  __( 'Reports', 'openpos' ) , 'manage_woocommerce', 'op-reports', array( $this, 'report_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );



        $page = add_submenu_page( 'openpos-dasboard', __( 'Receipt Templates', 'openpos' ),  __( 'Receipt Templates', 'openpos' ) , 'manage_options', 'op-receipt-template', array( $this, 'receipts_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_receipt_enqueue' ) );

        $setting_page = add_submenu_page( 'openpos-dasboard', __( 'POS - Setting', 'openpos' ),  __( 'Setting', 'openpos' ) , 'manage_options', 'op-setting', array( $this, 'setting_page' ) );
        add_action( 'admin_print_styles-'. $setting_page, array( $this, 'admin_enqueue_setting' ) );

        $page = add_submenu_page( 'openpos-dasboard', __( 'POS - Sessions', 'openpos' ),  __( 'Login Sessions', 'openpos' ) , 'manage_options', 'op-sessions', array( $this, 'sessions_page' ) );
        add_action( 'admin_print_styles-'. $page, array( &$this, 'admin_enqueue' ) );

        

    }
    function sessions_page() {
        global $op_register;
        $sessions = $this->_session->getActiveSessions();
        $session_data = array();



        foreach($sessions as $key => $s)
        {
            $login_cashdrawer_id = isset($s['login_cashdrawer_id']) ? $s['login_cashdrawer_id'] : 0;
            $tmp_location = ( isset($s['location']) && $s['location'] ) ?  json_decode(stripslashes($s['location']),true): array();

            $location = __( 'Unknown', 'openpos' );
            if(!empty($tmp_location))
            {
                $coord = implode('%2C',array($tmp_location['latitude'],$tmp_location['longitude']));
                $url = 'https://maps.google.com/maps?q='.$coord.'&t=&z=13&ie=UTF8&iwloc=&output=embed';
                $location = '<a href="javascript:void(0);" data-url="'.$url.'" class="session-location">'.__( 'View', 'openpos' ).'</a>';
            }

            $ip = isset($s['ip']) ? $s['ip'] : '0.0.0.0';
            $_register_location = '';

            $register = $op_register->get($login_cashdrawer_id);
            if(!empty($register))
            {
                $_register_location = $register['name'];
            }
            $created_at = $this->core->convertToShopTime($s['logged_time']);
            $created_at = $this->core->render_ago_date_by_time_stamp($created_at);
            $tmp = array(
                $s['session'],
                $s['username'],
                $created_at,
                $ip,
                $_register_location,
                $location,
                '<a href="javascript:void(0);" class="unlink-session" data-id="'.$s['session'].'">'.__( 'Unlink', 'openpos' ).'</a>',
            );

            $session_data[] = $tmp;
        }

        require(OPENPOS_DIR.'templates/admin/sessions.php');
    }
    function session_unlink(){
        $ids = $_REQUEST['id'];
        foreach($ids as $id)
        {
            if($id)
            {
                $this->_session->clean($id);
            }
        }

        echo json_encode(array());
        exit;
    }

    function receipts_page(){
        global $op_receipt;
        $action = isset($_GET['op-action']) ? $_GET['op-action'] : '';
        $template_file = 'receipt_templates.php';
        switch($action)
        {
            case 'composer':
                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                $default_paper_width = $this->settings_api->get_option('receipt_width','openpos_receipt');
                $default_paper_padding_top = $this->settings_api->get_option('receipt_padding_top','openpos_receipt');
                $default_paper_padding_right = $this->settings_api->get_option('receipt_padding_right','openpos_receipt');
                $default_paper_padding_bottom = $this->settings_api->get_option('receipt_padding_bottom','openpos_receipt');
                $default_paper_padding_left = $this->settings_api->get_option('receipt_padding_left','openpos_receipt');
                $template_details = $op_receipt->get($id);
                
                $paper_width = ($template_details['paper_width'])  ? $template_details['paper_width'] : $default_paper_width;
                $paper_padding_top =  ($template_details['padding_top'] || $template_details['padding_top'] === 0 )  ? $template_details['padding_top'] : $default_paper_padding_top;
                $paper_padding_right = ($template_details['padding_right'] || $template_details['padding_right'] === 0 )  ? $template_details['padding_right'] : $default_paper_padding_right;
                $paper_padding_bottom = ($template_details['padding_bottom'] || $template_details['padding_bottom'] === 0 )  ? $template_details['padding_bottom'] : $default_paper_padding_bottom;
                $paper_padding_left = ($template_details['padding_left'] || $template_details['padding_left'] === 0 )  ? $template_details['padding_left'] : $default_paper_padding_left;
                $template_content = $template_details['content'];
                $template_css = $template_details['custom_css'];
                $default = array(
                    'id' => $id,
                    'paper_width' => $paper_width,
                    'padding_top' => $paper_padding_top,
                    'padding_right' => $paper_padding_right,
                    'padding_bottom' => $paper_padding_bottom,
                    'padding_left' => $paper_padding_left,
                    'content' => $template_content,
                    'custom_css' => $template_css,
                );
                
                $template_file = 'receipt_template_composer.php';
                break;
          

        }
        require(OPENPOS_DIR.'templates/admin/'.$template_file);
    }

    function products_page() {
        require(OPENPOS_DIR.'templates/admin/products.php');
    }
    function register_page() {
        require(OPENPOS_DIR.'templates/admin/registers.php');
    }
    function table_page(){
        require(OPENPOS_DIR.'templates/admin/tables.php');
    }
    function warehouse_page() {

        $action = isset($_GET['op-action']) ? $_GET['op-action'] : '';
        switch ($action)
        {
            case 'adjust_stock':
                require(OPENPOS_DIR.'templates/admin/warehouse/adjust_stock.php');
                break;
            case 'inventory':
                require(OPENPOS_DIR.'templates/admin/warehouse/inventory.php');
                break;
            case 'edit':
            case 'new':
                require(OPENPOS_DIR.'templates/admin/warehouse/new.php');
                break;
            default:
                require(OPENPOS_DIR.'templates/admin/warehouses.php');
                break;
        }

    }
    function stock_page() {
        require(OPENPOS_DIR.'templates/admin/stock.php');
    }

    public function dashboard()
    {
        global $op_woo;
        global $op_register;
        global $op_warehouse;
        global $op_report;
        $duration = 'last_7_days';
        if(isset($_GET['duration']) )
        {
            switch($_GET['duration'])
            {
                case 'today':
                    $duration = 'today';
                    break;
                case 'this_week':
                    $duration = 'this_week';
                    break;
                case 'this_month':
                    $duration = 'this_month';
                    break;
                case 'last_30_days':
                    $duration = 'last_30_days';
                    break;
            }
            
        }
        $ranges = $this->core->getReportRanges($duration);
        $chart_data = array();
        $pos_url = $this->get_pos_url();
        $chart_data[] = array(
            __('Date','openpos'),
            __('Sales','openpos'),
            __('Transactions','openpos')
        );
        $register_sales = array();
        $outlet_sales = array();
        $payment_sales = array();
        $seller_sales = array();
       
        foreach($ranges['ranges'] as $r)
        {
            $sales = $this->core->getPosOrderByDate($r['from'],$r['to']);
            $total_sales = 0;
            $total_commision = 0;
            foreach($sales as $s)
            {
                $order = new WC_Order($s->ID);
                $grand_total = $order->get_total();
                $total_sales += $grand_total;

                $commission = $op_report->getSaleCommision($order);
                $total_commision += $commission ;
                $warehouse_meta_key = $op_warehouse->get_order_meta_key();
                $cashdrawer_meta_key = $op_register->get_order_meta_key();

                $warehouse_id = get_post_meta($order->get_id(),$warehouse_meta_key,true);
                $register_id = get_post_meta($order->get_id(),$cashdrawer_meta_key,true);
                if(isset($register_sales[$register_id]))
                {
                    $register_sales[$register_id] += $grand_total;
                }else{
                    $register_sales[$register_id] = $grand_total;
                }

                if(isset($outlet_sales[$warehouse_id]))
                {
                    $outlet_sales[$warehouse_id] += $grand_total;
                }else{
                    $outlet_sales[$warehouse_id] = $grand_total;
                }

                $tmp_seller_sales = $op_report->getSellerSaleByOrder( $order);
                foreach($tmp_seller_sales as $user_id => $total)
                {
                    if(isset($seller_sales[$user_id]))
                    {
                        $seller_sales[$user_id]['total'] += $total;
                    }else{
                        $_user = get_user_by('ID',$user_id);
                        $name = 'Unknown';
                        if($_user)
                        {
                            $name = $_user->display_name;
                            
                        }
                        $seller_sales[$user_id] = array(
                            'user_id' => $user_id,
                            'name' => $name,
                            'total' => $total
                        );
                    }
                }
            }
            
           
            $total_transaction = 0;
            $transactions = $this->core->getPosTransactionsByDate($r['from'],$r['to']);
            
            foreach($transactions as $s)
            {

                $in = get_post_meta($s->ID,'_in_amount',true);
                $out = get_post_meta($s->ID,'_out_amount',true);
                $_transaction_details = get_post_meta($s->ID,'_transaction_details',true);
                $_total = ($in - $out);
                if(isset($_transaction_details['source_type']) && $_transaction_details['source_type'] == 'order')
                {
                    $payment_code = get_post_meta($s->ID,'_payment_code',true); //add_post_meta($id,'_payment_code',$payment_code);
                    $payment_name = get_post_meta($s->ID,'_payment_name',true); //add_post_meta($id,'_payment_name',$payment_name);
                    if(isset($payment_sales[$payment_code]))
                    {
                        $payment_sales[$payment_code]['total'] += $_total;
                    }else{
                        $payment_sales[$payment_code] = array(
                            'total' => $_total,
                            'code' => $payment_code,
                            'name' => $payment_name
                        );
                    }
                }
                
                $total_transaction += $_total;
            }

            $chart_data[] = array(
                $r['label'],
                $total_sales,
                $total_transaction,
                $total_commision
            );
        }
        
        $pie_data = array();
        if(count($register_sales) > 0)
        {
            foreach($register_sales as $register_id => $register_sale)
            {
                $register = $op_register->get($register_id);
                $label = __('Unknown','openpos');
                if(isset($register['name']))
                {
                    $label = $register['name'];
                }
                $pie_data[] = array(
                    'label' => $register_id.' - '.$label.'( '.$op_woo->stripePriceTag($register_sale).' )',
                    'sale' => $register_sale,
                    'type' => 'register'
                );
            }
        }
        if(count($outlet_sales) > 1)
        {
            $pie_data = array();
            foreach($outlet_sales as $outlet_id => $outlet_sale)
            {
                $outlet = $op_warehouse->get($outlet_id);
                $label = __('Unknown','openpos');
                if(isset($outlet['name']))
                {
                    $label = $outlet['name'];
                }
                
                

                $pie_data[] = array(
                    'label' => $outlet_id.' - '.$label.'( '.$op_woo->stripePriceTag($outlet_sale).' )',
                    'sale' => $outlet_sale,
                    'type' => 'outlet'
                );
            }
        }
        
        $dashboard_data = $this->dashboard_data(true);
        
        require(OPENPOS_DIR.'templates/admin/dashboard.php');
    }
    public function cashier_page()
    {
        require(OPENPOS_DIR.'templates/admin/cashier.php');
    }
    public function transactions_page()
    {
        require(OPENPOS_DIR.'templates/admin/transactions.php');
    }
    public function orders_page(){
        require(OPENPOS_DIR.'templates/admin/orders.php');
    }

    public function setting_page()
    {
        echo '<div class="op-wrap">';
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();
        $this->settings_api->category_widget();
        echo '</div>';
    }

    public function report_ajax(){

        global  $op_warehouse;
        global $op_register;
        global $op_woo;
        global $op_report;

        $report_type = isset($_REQUEST['report_type']) ? $_REQUEST['report_type'] : 'sales';
        $is_export = isset($_REQUEST['export']) ? $_REQUEST['export'] : 0;
        $report_duration =  isset($_REQUEST['report_duration']) ? $_REQUEST['report_duration'] : 'today';
        $report_action =  isset($_REQUEST['report_action']) ? $_REQUEST['report_action'] : '';
        $report_seller_id =  isset($_REQUEST['report_seller']) ? $_REQUEST['report_seller'] : 0;

        $time_offset_mins = 0;

        $report_outlet_id =  isset($_REQUEST['report_outlet']) ? $_REQUEST['report_outlet'] : 0;
        $report_register_id =  isset($_REQUEST['report_register']) ? $_REQUEST['report_register'] : 0;


        $custom_from =  isset($_REQUEST['custom_from']) ? $_REQUEST['custom_from'] : date('Y-m-d');
        $custom_to =  isset($_REQUEST['custom_to']) ? $_REQUEST['custom_to'] : date('Y-m-d');
        $registers = $op_register->registers();
        if($report_action)
        {
            if($report_action == 'load_form')
            {
                $warehouse_id = isset($_REQUEST['report_outlet']) ? $_REQUEST['report_outlet'] : 0;
                $result_registers = array();
                foreach($registers as $register)
                {
                    if($register['status'] == 'publish' && $register['warehouse'] == $warehouse_id)
                    {
                        $result_registers[] = $register;
                    }
                }
                $cashiers = $op_woo->get_cashiers();
                $form_result = array(
                    //'outlets' => array(),
                    'registers' => $result_registers,
                    'sellers' => $cashiers,
                );
                echo json_encode($form_result);
            }else{
                $result = array();
                $ranges = array();
                if($report_duration == 'custom')
                {

                    $gameFrom = Carbon::parse($custom_from.' 00:00:00', 'UTC');
                    $gameTo = Carbon::parse($custom_to.' 00:00:00', 'UTC');
                    $ranges = $this->core->getReportRanges($report_duration,$gameFrom->toFormattedDateString('Y-m-d'),$gameTo->toFormattedDateString('Y-m-d'));
                }else{
                    $ranges = $this->core->getReportRanges($report_duration,false,false);
                }

                if($time_offset_mins)
                {
                    $ranges = $this->core->convertToUtc($ranges,$time_offset_mins);
                }

                $orders_table_data = array();
                $orders_export_data = array();
                
                $chart_data = array();
                $summary_html = '';
                $summary_data = array(
                    'total_order' => 0,
                    'total_sales' => 0,
                    'total_transaction' => 0,
                    'total_transaction_cash' => 0
                );
                $table_label = array(
                    __('Order','openpos'),
                    __('Grand Total','openpos'),
                    __('Commision Total','openpos'),
                    __('Cashier','openpos'),
                    __('Created At','openpos'),
                    __('View','openpos'),
                );
                switch ($report_type)
                {
                    case 'transactions':
                        $table_label = array(
                            __('#','openpos'),
                            __('Ref','openpos'),
                            __('IN','openpos'),
                            __('OUT','openpos'),
                            __('Method','openpos'),
                            __('Created At','openpos'),
                            __('Created By','openpos')
                        );
                        $orders_export_data[] = $table_label;
                        $chart_data[] = array(
                            __('Date','openpos'),
                            __('Cash Transactions','openpos')
                        );

                        foreach($ranges['ranges'] as $r)
                        {


                            $total_transaction = 0;
                            if($report_register_id > 0)
                            {
                                $transactions = $this->core->getPosRegisterTransactionsByDate($report_register_id,$r['from'],$r['to']);
                            }elseif($report_outlet_id > 0)
                            {
                                $transactions = $this->core->getPosWarehouseTransactionsByDate($report_outlet_id,$r['from'],$r['to']);
                            }else{
                                $transactions = $this->core->getPosTransactionsByDate($r['from'],$r['to']);
                            }

                            foreach($transactions as $s)
                            {

                                $in = get_post_meta($s->ID,'_in_amount',true);
                                $out = get_post_meta($s->ID,'_out_amount',true);
                                $total_transaction += ($in - $out);
                            }
                            $chart_data[] = array(
                                $r['label'],
                                $total_transaction
                            );
                        }


                        if($report_register_id > 0)
                        {
                            $transactions = $this->core->getPosRegisterTransactionsByDate($report_register_id,$ranges['start'],$ranges['end']);
                        }elseif($report_outlet_id > 0)
                        {
                            $transactions = $this->core->getPosWarehouseTransactionsByDate($report_outlet_id,$ranges['start'],$ranges['end']);
                        }else{
                            $transactions = $this->core->getPosTransactionsByDate($ranges['start'],$ranges['end']);
                        }
                        $total_in = 0;
                        $total_out = 0;
                        foreach($transactions as $_transaction)
                        {
                            $id = $_transaction->ID;
                            $user_id = get_post_meta($id,'_user_id',true);

                            $name = 'Unknown';
                            if($user_id)
                            {
                                $user = get_user_by('ID',$user_id);
                                $name = $user->display_name;
                            }
                            $in_amount = get_post_meta($id,'_in_amount',true);
                            $out_amount = get_post_meta($id,'_out_amount',true);
                            $method = get_post_meta($id,'_payment_name',true);
                            if(!$method)
                            {
                                $method = __('Cash','openpos');
                            }
                            $total_in += $in_amount;
                            $total_out += $out_amount;
                            $tmp = array(
                                $id,
                                $_transaction->post_title,
                                wc_price($in_amount),
                                wc_price($out_amount),
                                $method,
                                get_post_meta($id,'_created_at',true),
                                $name
                            );
                            $orders_table_data[] = $tmp;
                            $tmp_export = array(
                                $id,
                                $_transaction->post_title,
                                $in_amount,
                                $out_amount,
                                $method,
                                get_post_meta($id,'_created_at',true),
                                $name
                            );
                            $orders_export_data[] = $tmp_export;

                        }
                        $summary_html = '';
                        $title =  __('Total IN','openpos');
                        $value = wc_price($total_in);
                        $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3">';
                        $summary_html .= '<div class="summary-block">';
                        $summary_html .= '<dl>';
                        $summary_html .= '<dt>'.$title.'</dt>';
                        $summary_html .= '<dd>'.$value.'</dd>';
                        $summary_html .= '</dl>';
                        $summary_html .= '</div>';
                        $summary_html .= '</div>';

                        $title =  __('Total OUT','openpos');
                        $value = wc_price($total_out);
                        $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3">';
                        $summary_html .= '<div class="summary-block">';
                        $summary_html .= '<dl>';
                        $summary_html .= '<dt>'.$title.'</dt>';
                        $summary_html .= '<dd>'.$value.'</dd>';
                        $summary_html .= '</dl>';
                        $summary_html .= '</div>';
                        $summary_html .= '</div>';
                        break;
                    case 'sale_by_agent':
                        $table_label = array(
                            __('Order','openpos'),
                            __('Grand Total','openpos'),
                            __('Seller Amount','openpos'),
                            __('Cashier','openpos'),
                            __('Created At','openpos'),
                            __('View','openpos'),
                        );
                        
                        $is_all = false;
                        $orders_export_data = array();
                        if($report_seller_id == '_all')
                        {
                            $is_all = true;
                            $table_label = array(
                                __('User','openpos'),
                                __('Email','openpos'),
                                __('Sold Total','openpos'),
                                __('Sold QTY','openpos'),
                                __('Total Order','openpos'),
                            );
                           
                            $orders_export_data[] = array(
                                __('User','openpos'),
                                __('Email','openpos'),
                                __('Sold Total','openpos'),
                                __('Sold QTY','openpos'),
                                __('Total Order','openpos'),
                            );
                        }else{
                            
                            if($report_seller_id)
                            {
                                $seller_name = 'Unknown';
                                $seller_user = get_user_by('id',$report_seller_id);
                                
                                
                                if($seller_user)
                                {
                                    $seller_user_data = $seller_user->data;
                                    $seller_name = $seller_user_data->user_nicename ? $seller_user_data->user_nicename : $seller_user_data->user_login;
                                }
                                
                                $orders_export_data[] = array(
                                    __('User','openpos'),
                                    $seller_name,
                                   '',
                                   '',
                                   '',
                                );
                            }
                            $orders_export_data[] = array(
                                __('Order','openpos'),
                                __('Grand Total','openpos'),
                                __('Seller Amount','openpos'),
                                __('Cashier','openpos'),
                                __('Created At','openpos')
                            );
                           
                            
                        }

                        if($report_seller_id && !$is_all)
                        {
                            $chart_data[] = array(
                                __('Date','openpos'),
                                __('Sales','openpos')
                            );
                            foreach($ranges['ranges'] as $r)
                            {

                                $sales = $this->core->getPosOrderByDate($r['from'],$r['to']);
                                $total_sales = 0;
                                foreach($sales as $s)
                                {
                                    $order = new WC_Order($s->ID);
                                    $items = $order->get_items();

                                    $_agent_sale_id = $order->get_meta('_op_sale_by_cashier_id');

                                    if($_agent_sale_id && $_agent_sale_id == $report_seller_id)
                                    {

                                        $total_sales += $order->get_total() - $order->get_total_refunded();
                                    }

                                }

                                $chart_data[] = array(
                                    $r['label'],
                                    $total_sales
                                );
                            }
                            $orders = $this->core->getPosOrderByDate($ranges['start'],$ranges['end']);
                            $summary_data['total_order'] = count($orders);
                            $summary_data['total_qty']  = 0;
                            foreach($orders as $_order)
                            {
                                $order = new WC_Order($_order->ID);
                                $items = $order->get_items();
                                $is_sale_by_seller = false;
                                $seller_order_sale = 0;
                                $_agent_sale_id = $order->get_meta('_op_sale_by_cashier_id');

                                if($_agent_sale_id && $_agent_sale_id == $report_seller_id)
                                {
                                    $is_sale_by_seller = true;
                                    $summary_data['total_sales'] += $order->get_total() - $order->get_total_refunded();
                                    $seller_order_sale += $order->get_total();

                                    foreach($items as $item)
                                    {
                                       $summary_data['total_qty'] += $item->get_quantity();
                                    }


                                    $created_at = $order->get_date_created();

                                    $post_author_id = $_agent_sale_id;
                                    $author =   get_userdata($post_author_id);
                                    $author_name = 'Unknown';
                                    if($author)
                                    {
                                        $author_name = $author->display_name;
                                    }
                                    $grand_total = $order->get_total() - $order->get_total_refunded();

                                    $orders_table_data[] = array(
                                        $order->get_order_number(),
                                        strip_tags(wc_price($grand_total)),
                                        strip_tags(wc_price($seller_order_sale)),
                                        $author_name,
                                        wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) ) ,
                                        '<a target="_blank" href="'.esc_url($order->get_edit_order_url()).'">'.__( 'View', 'openpos' ) .'</a>'
                                    );

                                    $tmp_export = array(
                                        $order->get_order_number(),
                                        $grand_total,
                                        $seller_order_sale,
                                        $author_name,
                                        wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) )
                                    );
                                    $orders_export_data[] = $tmp_export;
                                }


                            }
                            $summary_html = '';
                            foreach($summary_data as $k => $v)
                            {
                                if(in_array($k,array('total_sales','total_qty')))
                                {
                                    $title =  __('Total Qty','openpos');
                                    $value = $v;
                                    if($k == 'total_sales')
                                    {
                                        $title =  __('Total Sales','openpos');
                                        $value = wc_price($v);
                                    }

                                    $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3">';
                                    $summary_html .= '<div class="summary-block">';
                                    $summary_html .= '<dl>';
                                    $summary_html .= '<dt>'.$title.'</dt>';
                                    $summary_html .= '<dd>'.$value.'</dd>';
                                    $summary_html .= '</dl>';
                                    $summary_html .= '</div>';
                                    $summary_html .= '</div>';
                                }
                            }
                        }
                        if($is_all)
                        {

                            $all_sellers_id = array();

                            $all_seller_report = $op_report->getSaleByCashierReport($all_sellers_id,$ranges['start'],$ranges['end']);
                            $summary_data = array(
                                'total_sales' => 0,
                                'total_qty' => 0,
                                'total_order' => 0,
                            );
                            foreach($all_seller_report as $user_id => $report_data)
                            {
                                $summary_data['total_sales'] += $report_data['total_sale'];
                                $summary_data['total_qty'] += $report_data['total_qty'];
                                $summary_data['total_order'] += $report_data['total_order'];
                                $user_name = __('Unknown');
                                $user_email = __('Unknown');
                                if($user_id)
                                {
                                    $author_obj = get_user_by('id', $user_id);
                                    if($author_obj)
                                    {
                                        $user_name = $author_obj->user_nicename ? $author_obj->user_nicename : $author_obj->user_login;
                                        $user_email = $author_obj->user_email;
                                    }

                                }
                                $orders_table_data[] = array(
                                    $user_name,
                                    $user_email,
                                    wc_price($report_data['total_sale']),
                                    $report_data['total_qty'],
                                    $report_data['total_order'],
                                );
                                $tmp_export = array(
                                    $user_name,
                                    $user_email,
                                    $report_data['total_sale'],
                                    $report_data['total_qty'],
                                    $report_data['total_order'],
                                );
                                $orders_export_data[] = $tmp_export;
                            }
                            
                            $summary_html = '';

                            foreach($summary_data as $k => $v)
                            {
                                if(in_array($k,array('total_sales','total_qty','total_order')))
                                {
                                    $title =  __('Total Qty','openpos');
                                    $value = $v;
                                    if($k == 'total_sales')
                                    {
                                        $title =  __('Total Sales','openpos');
                                        $value = wc_price($v);
                                    }
                                    if($k == 'total_order')
                                    {
                                        $title =  __('Total Orders','openpos');
                                       
                                    }
                                    $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3" style="margin-bottom: 15px;">';
                                    $summary_html .= '<div class="summary-block">';
                                    $summary_html .= '<dl>';
                                    $summary_html .= '<dt>'.$title.'</dt>';
                                    $summary_html .= '<dd>'.$value.'</dd>';
                                    $summary_html .= '</dl>';
                                    $summary_html .= '</div>';
                                    $summary_html .= '</div>';
                                }
                            }
                        }

                        break;
                    case 'sale_by_seller':
                        $table_label = array(
                            __('Order','openpos'),
                            __('Grand Total','openpos'),
                            __('Seller Amount','openpos'),
                            __('Cashier','openpos'),
                            __('Created At','openpos'),
                            __('View','openpos'),
                        );
                       
                        $is_all = false;
                        $orders_export_data = array();
                        if($report_seller_id == '_all')
                        {
                            $is_all = true;
                            $table_label = array(
                                __('User','openpos'),
                                __('Email','openpos'),
                                __('Sold Total','openpos'),
                                __('Sold QTY','openpos'),
                            );
                            $orders_export_data[] = array(
                                __('User','openpos'),
                                __('Email','openpos'),
                                __('Sold Total','openpos'),
                                __('Sold QTY','openpos'),
                            );
                        }else{
                           
                            if($report_seller_id)
                            {
                                $seller_name = 'Unknown';
                                $seller_user = get_user_by('id',$report_seller_id);
                                
                                
                                if($seller_user)
                                {
                                    $seller_user_data = $seller_user->data;
                                    $seller_name = $seller_user_data->user_nicename ? $seller_user_data->user_nicename : $seller_user_data->user_login;
                                }
                                
                                $orders_export_data[] = array(
                                    __('User','openpos'),
                                    $seller_name,
                                   '',
                                   '',
                                   '',
                                );
                            }
                           
                            $orders_export_data[] = array(
                                __('Order','openpos'),
                                __('Grand Total','openpos'),
                                __('Seller Amount','openpos'),
                                __('Cashier','openpos'),
                                __('Created At','openpos')
                            );
                        }
                        
                        if($report_seller_id && !$is_all)
                        {
                            
                            $chart_data[] = array(
                                __('Date','openpos'),
                                __('Sales','openpos')
                            );
                            foreach($ranges['ranges'] as $r)
                            {

                                $sales = $this->core->getPosOrderByDate($r['from'],$r['to']);
                                $total_sales = 0;
                                foreach($sales as $s)
                                {
                                    $order = new WC_Order($s->ID);
                                    $items = $order->get_items();
                                    $_op_sale_by_person_id = get_post_meta($s->ID,'_op_sale_by_person_id',true);
                                    $_op_sale_by_cashier_id = get_post_meta($s->ID,'_op_sale_by_cashier_id',true);
                                    if(!$_op_sale_by_person_id)
                                    {
                                        $_op_sale_by_person_id = $_op_sale_by_cashier_id;
                                    }

                                    if($report_seller_id == $_op_sale_by_person_id)
                                    {
                                        $partial_sale = 0;
                                        $has_parital = false;
                                        foreach($items as $item)
                                        {
                                            $_item_sale_id = $item->get_meta('_op_seller_id');
                                            if($_item_sale_id > 0 && $_item_sale_id != $_op_sale_by_person_id)
                                            {
                                                $has_parital = true;
                                            }
                                            if($_item_sale_id && $_item_sale_id == $report_seller_id)
                                            {
                                                $item_data = $item->get_data();
                                                //$total_sales += $item_data['subtotal'];
                                                $partial_sale += $item_data['subtotal'];
                                            }
                                        }
                                        if($has_parital)
                                        {
                                            $total_sales += $partial_sale;
                                        }else{
                                            $total_sales += $order->get_total() - $order->get_total_refunded();
                                        }
                                    }else{
                                        foreach($items as $item)
                                        {
                                            $_item_sale_id = $item->get_meta('_op_seller_id');
                                            if($_item_sale_id && $_item_sale_id == $report_seller_id)
                                            {
                                                $item_data = $item->get_data();
                                                $total_sales += $item_data['subtotal'];
                                            }
                                        }
                                    }
                                }

                                $chart_data[] = array(
                                    $r['label'],
                                    $total_sales
                                );
                            }
                            $orders = $this->core->getPosOrderByDate($ranges['start'],$ranges['end']);
                            $summary_data['total_order'] = count($orders);
                            $summary_data['total_qty'] = 0;
                            foreach($orders as $_order)
                            {
                                $order = new WC_Order($_order->ID);
                                $items = $order->get_items();
                                $is_sale_by_seller = false;
                                $seller_order_sale = 0;
                                $_op_sale_by_person_id = get_post_meta($_order->ID,'_op_sale_by_person_id',true);


                                if($report_seller_id == $_op_sale_by_person_id)
                                {

                                    $partial_sale = 0;
                                    $partial_all_qty = 0;
                                    $partial_qty = 0;
                                    $has_parital = false;
                                    foreach($items as $item)
                                    {
                                        $_item_sale_id = $item->get_meta('_op_seller_id');
                                        $item_qty = $item->get_quantity();
                                        $partial_all_qty += $item_qty;


                                        if($_item_sale_id > 0 && $_item_sale_id != $_op_sale_by_person_id)
                                        {
                                            $has_parital = true;
                                        }
                                        if( $_item_sale_id == $report_seller_id )
                                        {
                                            $item_data = $item->get_data();
                                            $partial_sale += $item_data['subtotal'];
                                            $partial_qty += $item_qty;
                                        }
                                    }


                                    if(!$has_parital)
                                    {
                                        $is_sale_by_seller = true;
                                        $seller_order_sale += $order->get_total() - $order->get_total_refunded();
                                        $summary_data['total_sales'] += $order->get_total() - $order->get_total_refunded();
                                        $summary_data['total_qty'] += $partial_all_qty;
                                    }else{
                                        if($partial_sale > 0)
                                        {
                                            $is_sale_by_seller = true;
                                            $summary_data['total_sales'] += $partial_sale;
                                            $summary_data['total_qty'] += $partial_qty;
                                            $seller_order_sale += $partial_sale;
                                        }

                                    }

                                }else{
                                    foreach($items as $item)
                                    {
                                        $_item_sale_id = $item->get_meta('_op_seller_id');
                                        if($_item_sale_id && $_item_sale_id == $report_seller_id)
                                        {
                                            $is_sale_by_seller = true;
                                            $item_data = $item->get_data();
                                            $summary_data['total_sales'] += $item_data['subtotal'];
                                            $seller_order_sale += $item_data['subtotal'];
                                            $summary_data['total_qty'] += $item->get_quantity();
                                        }
                                    }
                                }

                                if($is_sale_by_seller)
                                {
                                    $created_at = $order->get_date_created();
                                    $customer_id = $order->get_customer_id();
                                    $post_author_id = get_post_field( 'post_author', $_order->ID );
                                    $author =   get_userdata($post_author_id);
                                    $author_name = 'Unknown';
                                    if($author)
                                    {
                                        $author_name = $author->display_name;
                                    }
                                    $grand_total = $order->get_total() - $order->get_total_refunded();

                                    $orders_table_data[] = array(
                                        $order->get_order_number(),
                                        strip_tags(wc_price($grand_total)),
                                        strip_tags(wc_price($seller_order_sale)),
                                        $author_name,
                                        wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) ),
                                        '<a target="_blank" href="'.esc_url($order->get_edit_order_url()).'">'.__( 'View', 'openpos' ) .'</a>'
                                    );

                                    $tmp_export = array(
                                        $order->get_order_number(),
                                        $grand_total,
                                        $seller_order_sale,
                                        $author_name,
                                        wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) )
                                    );
                                    $orders_export_data[] = $tmp_export;
                                }

                            }
                            $summary_html = '';
                            foreach($summary_data as $k => $v)
                            {
                                if(in_array($k,array('total_sales','total_qty')))
                                {
                                    $title =  __('Total Qty','openpos');
                                    $value = $v;
                                    if($k == 'total_sales')
                                    {
                                        $title =  __('Total Sales','openpos');
                                        $value = wc_price($v);
                                    }

                                    $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3">';
                                    $summary_html .= '<div class="summary-block">';
                                    $summary_html .= '<dl>';
                                    $summary_html .= '<dt>'.$title.'</dt>';
                                    $summary_html .= '<dd>'.$value.'</dd>';
                                    $summary_html .= '</dl>';
                                    $summary_html .= '</div>';
                                    $summary_html .= '</div>';
                                }
                            }
                        }
                        if($is_all)
                        {
                            $all_sellers_id = array();

                            $all_seller_report = $op_report->getSaleBySellerReport($all_sellers_id,$ranges['start'],$ranges['end']);
                            $summary_data = array(
                                'total_sales' => 0,
                                'total_qty' => 0,
                            );
                            foreach($all_seller_report as $user_id => $report_data)
                            {
                                $summary_data['total_sales'] += $report_data['total_sale'];
                                $summary_data['total_qty'] += $report_data['total_qty'];
                                $user_name = __('Unknown');
                                $user_email = __('Unknown');
                                if($user_id)
                                {
                                    $author_obj = get_user_by('id', $user_id);
                                    if($author_obj)
                                    {
                                        $user_name = $author_obj->user_nicename ? $author_obj->user_nicename : $author_obj->user_login;
                                        $user_email = $author_obj->user_email;
                                    }

                                }
                                $orders_table_data[] = array(
                                    $user_name,
                                    $user_email,
                                    wc_price($report_data['total_sale']),
                                    $report_data['total_qty'],
                                );
                                $tmp_export = array(
                                    $user_name,
                                    $user_email,
                                    $report_data['total_sale'],
                                    $report_data['total_qty'],
                                );
                                $orders_export_data[] = $tmp_export;
                            }
                            
                            $summary_html = '';

                            foreach($summary_data as $k => $v)
                            {
                                if(in_array($k,array('total_sales','total_qty')))
                                {
                                    $title =  __('Total Qty','openpos');
                                    $value = $v;
                                    if($k == 'total_sales')
                                    {
                                        $title =  __('Total Sales','openpos');
                                        $value = wc_price($v);
                                    }

                                    $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3" style="margin-bottom: 15px;">';
                                    $summary_html .= '<div class="summary-block">';
                                    $summary_html .= '<dl>';
                                    $summary_html .= '<dt>'.$title.'</dt>';
                                    $summary_html .= '<dd>'.$value.'</dd>';
                                    $summary_html .= '</dl>';
                                    $summary_html .= '</div>';
                                    $summary_html .= '</div>';
                                }
                            }
                            
                        }

                        break;
                    case 'sale_by_payment':
                        $report_payment = isset($_REQUEST['report_payment']) ? $_REQUEST['report_payment'] : '';
                        $table_label = array(
                            __('Order','openpos'),
                            __('Grand Total','openpos'),
                            __('Method Amount','openpos').'('.$report_payment.')',
                            __('Cashier','openpos'),
                            __('Created At','openpos'),
                            __('View','openpos'),
                        );
                        $orders_export_data[] = array(
                            __('Order','openpos'),
                            __('Grand Total','openpos'),
                            __('Method Amount','openpos').'('.$report_payment.')',
                            __('Cashier','openpos'),
                            __('Created At','openpos')
                        );


                        $chart_data[] = array(
                            __('Date','openpos'),
                            __('Sales','openpos')
                        );
                        if($report_payment)
                        {
                            foreach($ranges['ranges'] as $r)
                            {

                                if($report_register_id > 0)
                                {
                                    $sales = $this->core->getPosRegisterOrderByDate($report_register_id,$r['from'],$r['to']);
                                }elseif($report_outlet_id > 0){
                                    $sales = $this->core->getPosWarehouseOrderByDate($report_outlet_id,$r['from'],$r['to']);
                                }else{
                                    $sales = $this->core->getPosOrderByDate($r['from'],$r['to']);
                                }

                                $total_sales = 0;
                                foreach($sales as $s)
                                {
                                    $payment_methods = get_post_meta($s->ID,'_op_payment_methods',true);
                                    if($payment_methods && is_array($payment_methods))
                                    {
                                        foreach($payment_methods as $payment_method)
                                        {
                                            $payment_code = $payment_method['code'];
                                            if($payment_code == $report_payment)
                                            {
                                                $paid = isset($payment_method['paid']) ? (float)$payment_method['paid'] : 0;
                                                $return_paid = isset($payment_method['return']) ? (float)$payment_method['return'] : 0;
                                                if($payment_code == 'cash' && $return_paid > 0)
                                                {
                                                    $paid = $paid - $return_paid;
                                                }
                                                $total_sales += $paid;
                                            }
                                        }
                                    }

                                }

                                $chart_data[] = array(
                                    $r['label'],
                                    $total_sales
                                );
                            }
                            if($report_register_id > 0)
                            {
                                $orders = $this->core->getPosRegisterOrderByDate($report_register_id,$ranges['start'],$ranges['end']);
                            }elseif($report_outlet_id > 0){
                                $orders = $this->core->getPosWarehouseOrderByDate($report_outlet_id,$ranges['start'],$ranges['end']);
                            }else{
                                $orders = $this->core->getPosOrderByDate($ranges['start'],$ranges['end']);
                            }


                            $summary_data['total_order'] = 0;
                            foreach($orders as $_order)
                            {
                                $order = new WC_Order($_order->ID);

                                $payment_methods = get_post_meta($_order->ID,'_op_payment_methods',true);
                                if($payment_methods && is_array($payment_methods))
                                {
                                    foreach($payment_methods as $payment_method)
                                    {
                                        $payment_code = $payment_method['code'];
                                        if($payment_code == $report_payment)
                                        {
                                            $summary_data['total_order']++;
                                            $paid = isset($payment_method['paid']) ? (float)$payment_method['paid'] : 0;
                                            $return_paid = isset($payment_method['return']) ? (float)$payment_method['return'] : 0;
                                            if($payment_code == 'cash' && $return_paid > 0)
                                            {
                                                $paid = $paid - $return_paid;
                                            }


                                            $created_at = $order->get_date_created();
                                            $post_author_id = get_post_field( 'post_author', $_order->ID );
                                            $author =   get_userdata($post_author_id);
                                            $author_name = 'Unknown';
                                            if($author)
                                            {
                                                $author_name = $author->display_name;
                                            }
                                            $grand_total = $order->get_total() - $order->get_total_refunded();
                                            $summary_data['total_sales'] += $paid;
                                            $orders_table_data[] = array(
                                                $order->get_order_number(),
                                                strip_tags(wc_price($grand_total)),
                                                strip_tags(wc_price($paid)),
                                                $author_name,
                                                wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) ) ,
                                                '<a target="_blank" href="'.esc_url($order->get_edit_order_url()).'">'.__( 'View', 'openpos' ) .'</a>'
                                            );

                                            $tmp_export = array(
                                                $order->get_order_number(),
                                                $grand_total,
                                                $paid,
                                                $author_name,
                                                wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) )
                                            );
                                            $orders_export_data[] = $tmp_export;
                                        }
                                    }
                                }


                            }
                            $summary_html = '';
                            foreach($summary_data as $k => $v)
                            {
                                if(in_array($k,array('total_sales','total_order')))
                                {
                                    $title =  __('Total Orders','openpos');
                                    $value = $v;
                                    if($k == 'total_sales')
                                    {
                                        $title =  __('Total Sales','openpos');
                                        $value = wc_price($v);
                                    }

                                    $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3">';
                                    $summary_html .= '<div class="summary-block">';
                                    $summary_html .= '<dl>';
                                    $summary_html .= '<dt>'.$title.'</dt>';
                                    $summary_html .= '<dd>'.$value.'</dd>';
                                    $summary_html .= '</dl>';
                                    $summary_html .= '</div>';
                                    $summary_html .= '</div>';
                                }
                            }
                        }
                        break;
                    case 'sales':
                        $orders_export_data[] = array(
                            __('#','openpos'),
                            __('Grand Total','openpos'),
                            __('Commision Total','openpos'),
                            __('Cashier','openpos'),
                            __('Created At','openpos')
                        );
                        $chart_data[] = array(
                            __('Date','openpos'),
                            __('Sales','openpos'),
                            __('Commsion','openpos'),
                        );
                        foreach($ranges['ranges'] as $r)
                        {

                            if($report_register_id > 0)
                            {
                                $sales = $this->core->getPosRegisterOrderByDate($report_register_id,$r['from'],$r['to']);
                            }elseif($report_outlet_id > 0){
                                $sales = $this->core->getPosWarehouseOrderByDate($report_outlet_id,$r['from'],$r['to']);
                            }else{
                                $sales = $this->core->getPosOrderByDate($r['from'],$r['to']);
                            }

                            $total_sales = 0;
                            $total_commsion = 0;
                            foreach($sales as $s)
                            {
                                $order = new WC_Order($s->ID);
                                $total_sales += $order->get_total()- $order->get_total_refunded();
                                $total_commsion += $op_report->getSaleCommision($order);
                            }

                            $chart_data[] = array(
                                $r['label'],
                                $total_sales,
                                $total_commsion,
                            );
                        }
                        if($report_register_id > 0)
                        {
                            $orders = $this->core->getPosRegisterOrderByDate($report_register_id,$ranges['start'],$ranges['end']);
                        }elseif($report_outlet_id > 0){
                            $orders = $this->core->getPosWarehouseOrderByDate($report_outlet_id,$ranges['start'],$ranges['end']);
                        }else{
                            $orders = $this->core->getPosOrderByDate($ranges['start'],$ranges['end']);
                        }


                        $summary_data['total_order'] = count($orders);
                        $summary_data['total_commision'] = 0;
                        foreach($orders as $_order)
                        {
                            $order = new WC_Order($_order->ID);
                            $created_at = $order->get_date_created();
                            $customer_id = $order->get_customer_id();
                            $post_author_id = get_post_field( 'post_author', $_order->ID );
                            $author =   get_userdata($post_author_id);
                            $author_name = 'Unknown';
                            if($author)
                            {
                                $author_name = $author->display_name;
                            }
                            $grand_total = $order->get_total() - $order->get_total_refunded();
                            $commision_total = $op_report->getSaleCommision($order);
                            $summary_data['total_sales'] += $grand_total;
                            $summary_data['total_commision'] += $commision_total;
                            $orders_table_data[] = array(
                                $order->get_order_number(),
                                strip_tags(wc_price($grand_total)),
                                strip_tags(wc_price($commision_total)),
                                $author_name,
                                wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) ) ,
                                '<a target="_blank" href="'.esc_url($order->get_edit_order_url()).'">'.__( 'View', 'openpos' ) .'</a>'
                            );

                            $tmp_export = array(
                                $order->get_order_number(),
                                $grand_total,
                                $commision_total,
                                $author_name,
                                wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) )
                            );
                            $orders_export_data[] = $tmp_export;
                        }
                        $summary_html = '';
                       
                        foreach($summary_data as $k => $v)
                        {
                            if(in_array($k,array('total_sales','total_order','total_commision')))
                            {
                                $title =  __('Total Orders','openpos');
                                $value = $v;
                                if($k == 'total_sales')
                                {
                                    $title =  __('Total Sales','openpos');
                                    $value = wc_price($v);
                                }
                                if($k == 'total_commision')
                                {
                                    $title =  __('Total Commsion','openpos');
                                    $value = wc_price($v);
                                }
                                

                                $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3">';
                                $summary_html .= '<div class="summary-block">';
                                $summary_html .= '<dl>';
                                $summary_html .= '<dt>'.$title.'</dt>';
                                $summary_html .= '<dd>'.$value.'</dd>';
                                $summary_html .= '</dl>';
                                $summary_html .= '</div>';
                                $summary_html .= '</div>';
                            }
                        }
                        break;
                    case 'sale_by_product':
                        $orders_table_data = array();
                        $orders_export_data = array();
                        $summary_html = '';
                        $is_all = false;
                        if($report_register_id > 0)
                        {
                            $orders = $this->core->getPosRegisterOrderByDate($report_register_id,$ranges['start'],$ranges['end']);
                        }elseif($report_outlet_id > 0){
                            $orders = $this->core->getPosWarehouseOrderByDate($report_outlet_id,$ranges['start'],$ranges['end']);
                        }else{
                            $orders = $this->core->getPosOrderByDate($ranges['start'],$ranges['end']);
                            $is_all = true;
                        }
                        $table_label = array(
                            __('Product','openpos'),
                            __('QTY','openpos'),
                            __('Sale','openpos'),
                        );
                        $orders_export_data[] = array(
                            __('Product','openpos'),
                            __('QTY','openpos'),
                            __('Sale','openpos'),
                        );

                        $summary_html.= '<div class="container-fluid"><div class="row"><div class="col-md-12">';
                        $summary_html.= '<table class="table table-bordered">';
                        $summary_html.= '<tr>';
                        $summary_html.= '<th>'.__('Product','openpos').'</th>';
                        $summary_html.= '<th>'.__('QTY','openpos').'</th>';
                        $summary_html.= '<th>'.__('Sale','openpos').'</th>';
                        $summary_html.= '</tr>';

                        $item_data = array();
                        $custom_item_data = array();

                        foreach($orders as $_order) {
                            $order = new WC_Order($_order->ID);
                            $items = $order->get_items();
                            foreach($items as $item)
                            {
                                $product = $item->get_product();
                                $order_item_data = $item->get_data();
                                if($product)
                                {
                                    $id = $product->get_id();
                                    $item_qty = $item->get_quantity();
                                    $item_sales =  $order_item_data['subtotal'];
                                    if(isset($item_data[$id]))
                                    {
                                        $item_data[$id]['qty'] += $item_qty;
                                        $item_data[$id]['sale'] += $item_sales;
                                    }else{
                                        $item_data[$id] = array(
                                            'name' => $item->get_name(),
                                            'qty' => $item_qty,
                                            'sale' => $item_sales
                                        );
                                    }
                                }else{

                                    $item_qty = $item->get_quantity();
                                    $item_sales =  $order_item_data['subtotal'];
                                    $custom_item_data[] = array(
                                        'name' => $item->get_name(),
                                        'qty' => $item_qty,
                                        'sale' => $item_sales
                                    );
                                }

                            }
                        }
                        foreach($item_data as $_item)
                        {
                            $summary_html.= '<tr>';
                            $summary_html.= '<td>'.$_item['name'].'</td>';
                            $summary_html.= '<td>'.$_item['qty'].'</td>';
                            $summary_html.= '<td>'.wc_price($_item['sale']).'</td>';
                            $summary_html.= '</tr>';

                            $orders_export_data[] = array(
                                $_item['name'],
                                $_item['qty'],
                                $_item['sale']
                            );
                        }
                        foreach($custom_item_data as $_item)
                        {
                            $summary_html.= '<tr>';
                            $summary_html.= '<td>'.$_item['name'].'</td>';
                            $summary_html.= '<td>'.$_item['qty'].'</td>';
                            $summary_html.= '<td>'.wc_price($_item['sale']).'</td>';
                            $summary_html.= '</tr>';
                            $orders_export_data[] = array(
                                $_item['name'],
                                $_item['qty'],
                                $_item['sale']
                            );
                        }
                        $summary_html.= '</table></div></div></div>';
                        break;
                    

                }


                
                
                $result['table_data'] = array('data' => $orders_table_data,'label' => $table_label);
                $result['chart_data'] = $chart_data;
                
                if(count($chart_data) > 1)
                {
                    $new_chart_label = array();
                    $new_chart_data = array();
                    $new_commsion_data = array();
                    for($i = 1; $i < count($chart_data);$i++)
                    {
                        $new_chart_label[] = $chart_data[$i][0];
                        $new_chart_data[] = $chart_data[$i][1];
                        if(isset($chart_data[$i][2]))
                        {
                            $new_commsion_data[] = $chart_data[$i][2];
                        }
                    }
                    $result['new_chart_data'] = array(
                        'label' => $new_chart_label,
                        'data'=> $new_chart_data,
                    );
                    if(!empty($new_commsion_data))
                    {
                        $result['new_chart_data']['commsion_data'] = $new_commsion_data ;
                    }
                }
                $result['orders_export_data'] = $orders_export_data;
                $result['summary_html'] = $summary_html;
                $result['export_file'] = '';
                
                $result = apply_filters('op_report_result',$result,$ranges,$report_type);
                
                if($is_export)
                {
                    $upload_dir = wp_upload_dir();
                    $url = $upload_dir['baseurl'];
                    $url = rtrim($url,'/');

                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->setActiveSheetIndex(0);

                    $objPHPExcel->getActiveSheet()->fromArray( $result['orders_export_data'], null, 'A1');
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                    $file_name = 'openpos-report-'.time().'.xls';
                    $upload_dir_path = $upload_dir['basedir'];
                    $upload_dir_path = rtrim($upload_dir_path,'/');

                    $objWriter->save($upload_dir_path.'/openpos/'.$file_name);

                    $result['export_file'] = $url.'/openpos/'.$file_name;
                }
                echo json_encode($result);

            }

            exit;
        }
    }
    public function report_page(){
        global  $op_warehouse;
        global $op_register;
        global $op_report;
        $report_type = 'sales';
        $report_duration =  'today';
        $report_action =  '';

        $custom_from =  date('Y-m-d');
        $custom_to =  date('Y-m-d');

        if($report_action)
        {
            $form_result = array();
            echo json_encode($form_result);
            exit;
        }

        if($report_duration == 'custom')
        {

            $gameFrom = Carbon::parse($custom_from.' 00:00:00', 'UTC');
            $gameTo = Carbon::parse($custom_to.' 00:00:00', 'UTC');

            $ranges = $this->core->getReportRanges($report_duration,$gameFrom->toFormattedDateString('Y-m-d'),$gameTo->toFormattedDateString('Y-m-d'));
        }else{
            $ranges = $this->core->getReportRanges($report_duration);
        }

        $orders_table_data = array();
        $chart_data = array();
        $summaries  = array(
            'total_order' => 0,
            'total_sale' => 0,
            'total_commision' => 0,
        );


        $chart_data[] = array(
            __('Date','openpos'),
            __('Sales','openpos')
        );
        foreach($ranges['ranges'] as $r)
        {

            $sales = $this->core->getPosOrderByDate($r['from'],$r['to']);
            $total_sales = 0;
            $total_commision = 0;
            foreach($sales as $s)
            {
                $order = new WC_Order($s->ID);
                $total_refunded  = $order->get_total_refunded();
                $total_sales += $order->get_total() - $total_refunded;
                $total_commision += $op_report->getSaleCommision($order);
                
            }

            $chart_data[] = array(
                $r['label'],
                $total_sales,
                $total_commision,
            );
        }
        $orders = $this->core->getPosOrderByDate($ranges['start'],$ranges['end']);
        $summaries['total_order'] = count($orders);

        foreach($orders as $_order)
        {
            $order = new WC_Order($_order->ID);
            $created_at = $order->get_date_created();
            $customer_id = $order->get_customer_id();
            $post_author_id = get_post_field( 'post_author', $_order->ID );
            $author =   get_userdata($post_author_id);
            $author_name = 'Unknown';
            if($author)
            {
                $author_name = $author->display_name;
            }
            $grand_total = $order->get_total() - $order->get_total_refunded();
            $commision_total = $op_report->getSaleCommision($order);
            $summaries['total_sale'] += $grand_total;
            $summaries['total_commision'] += $commision_total;
            $orders_table_data[] = array(
                $order->get_order_number(),
                wc_price($grand_total),
                wc_price($commision_total),
                $author_name,
                wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) ) ,
                '<a target="_blank" href="'.esc_url($order->get_edit_order_url()).'">'.__( 'View', 'openpos' ) .'</a>'
            );
        }


        require(OPENPOS_DIR.'templates/admin/report/report_form.php');
        require(OPENPOS_DIR.'templates/admin/report/report_'.esc_attr($report_type).'_chart.php');
        require(OPENPOS_DIR.'templates/admin/report/report_'.esc_attr($report_type).'_table.php');
    }



    public function admin_receipt_enqueue(){
        wp_enqueue_script('op.jquery.codemirror',OPENPOS_URL.'/assets/js/codemirror.js',array('jquery'));
        wp_enqueue_style( 'op.codemirror',OPENPOS_URL.'/assets/css/codemirror.css' );
        $this->admin_style();


    }

    public function admin_enqueue_setting() {
        global $OPENPOS_SETTING;
        $OPENPOS_SETTING->admin_enqueue_scripts();
        wp_enqueue_script('op.jquery.codemirror',OPENPOS_URL.'/assets/js/codemirror.js',array('jquery'));
        wp_enqueue_style( 'op.codemirror',OPENPOS_URL.'/assets/css/codemirror.css' );
        $this->admin_style();

    }
    public function admin_enqueue() {
        $this->admin_style();
    }

    public function getUsers(){

        $rows = array();
        $current = isset($_REQUEST['current']) ? intval($_REQUEST['current']) : 1;
        $display = isset($_REQUEST['display']) ? esc_attr($_REQUEST['display']) : 'user';
        $sort  = isset($_REQUEST['sort']) ? sanitize_text_field($_REQUEST['sort']) : false;
        $searchPhrase  = $_REQUEST['searchPhrase'] ? sanitize_text_field($_REQUEST['searchPhrase']) : false;
        $sortBy = 'date';
        $order = 'DESC';
        if($sort)
        {
            if(is_array($sort))
            {
                $sortBy = end(array_keys($sort));
            }
            if($sortBy == 'id')
            {
                $sortBy = 'ID';
            }
            $order = end($sort);
        }


        $rowCount = $_REQUEST['rowCount'] ? intval($_REQUEST['rowCount']) : get_option( 'posts_per_page' );
        $offet = ($current -1) * $rowCount;

        $roles = array();//  array('administrator','shop_manager');
        $final_roles = apply_filters('op_allow_user_roles',$roles);
        
        $args = array(
            'count_total' => true,
            'number'   => $rowCount,
            'offset'           => $offet,
            'orderby'          => $sortBy,
            'order'            => $order,
            'fields' => array('ID', 'display_name','user_email','user_login','user_status')
        );
        if($display == 'staff')
        {
            $args['meta_key'] = '_op_allow_pos';
            $args['meta_value'] = true;
        }
        if(!empty($final_roles))
        {
            $args['role__in'] = $final_roles;
        }
        if($searchPhrase)
        {
            $args['search'] = $searchPhrase;
        }

        $user_query = new WP_User_Query( $args );


        $users = get_users( $args);
        $total = $user_query->total_users;

        foreach($users as $user)
        {
            $tmp = (array)$user;
            
            $allow_pos = get_user_meta($tmp['ID'],'_op_allow_pos',true);
            $user_meta = get_userdata($tmp['ID']);

            $user_role = $user_meta->roles;
            if(!empty($user_role))
            {
                $tmp['user_login'] .= '<p class="op-user-roles">'.implode(',',$user_role).'</p>';
            }

            if(!$allow_pos)
            {
                $allow_pos = 0;
            }else{
                $allow_pos = 1;
            }
            
            $tmp['id'] = (int)$tmp['ID'];
            unset($tmp['ID']);
            if($allow_pos)
            {
                $tmp['allow_pos'] = '<select type="text" name="_op_allow_pos['.$tmp['id'].']" class="form-control _op_allow_pos" disabled><option value="0">No</option><option value="1" selected>Yes</option></select>';
            }else{
                $tmp['allow_pos'] = '<select  type="text" name="_op_allow_pos['.$tmp['id'].']" class="form-control _op_allow_pos" disabled><option value="0" selected>No</option><option value="1">Yes</option></select>';
            }
            $rows[] = $tmp;
        }
        $result = array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $rows,
            'total' => $total
        );
        echo json_encode($result);
        exit;
    }

    public function dashboard_data($return = false){
        global $op_register;
        $result = array('order' => array());

        $args = array(
            "post_type" => "shop_order",
            'posts_per_page' => 10,
            'orderby' => 'publish_date',
            'order' => 'DESC',
            'post_status'      => 'any',
            'meta_query' => array(
                array(
                    'key' => '_op_order_source',
                    'value' => 'openpos',
                    'compare' => '=',
                )
            )
        );
        $query = new WP_Query($args);
        $orders = $query->get_posts();
        foreach($orders as $order)
        {
            $id = $order->ID;
            $_order = wc_get_order($id);
            $customer_name = __('Guest','openpos');
            if( $_order->get_billing_first_name() || $_order->get_billing_last_name())
            {
                $customer_name = $_order->get_billing_first_name().' '.$_order->get_billing_last_name();
            }

            $grand_total = $_order->get_total();
            $cashier_id = get_post_field( 'post_author', $id);
            $cashier = get_user_by('ID',$cashier_id);
            $cashier_name = 'unknown';
            if($cashier)
            {
                $cashier_name = $cashier->display_name;
            }
            $tmp = array(
                'order_id' => "#".$_order->get_order_number(),
                'customer_name' => $customer_name,
                'total' => wc_price($grand_total),
                'cashier' => $cashier_name,
                'created_at' =>  $this->core->render_order_date_column($_order),
                'view' => '<a target="_blank" href="'.get_edit_post_link($id).'">'."#".$_order->get_order_number().'</a>',
                'status' => '<span class="'.esc_attr($_order->get_status()).'">'.wc_get_order_status_name( $_order->get_status() ).'</span>'
            );
            $result['order'][] = $tmp;
        }
        $balance = 0;
        $registers = $op_register->registers();
        foreach($registers as $register)
        {
            if($register['status'] == 'publish')
            {
                $balance +=  $op_register->cash_balance($register['id']);
            }

        }
        $result['cash_balance'] = wc_price($balance);
        if($return)
        {
            return $result;
        }else{
            echo json_encode($result);
            exit;
        }
       
    }

    public function save_cashier(){
        $data = $_REQUEST['_op_allow_pos'];
        foreach($data as $user_id => $value)
        {
            update_user_meta($user_id,'_op_allow_pos',intval($value));
        }
        exit;
    }

    public function update_product_grid(){
        $barcode_field = $this->settings_api->get_option('barcode_meta_key','openpos_label');
        if(isset($_REQUEST['data']))
        {
            parse_str($_REQUEST['data'], $data);
            if(isset($data['barcode']))
            {

                foreach($data['barcode'] as $product_id => $val)
                {
                    if($val)
                    {
                        if($barcode_field != 'post_id')
                        {
                            update_post_meta($product_id,$barcode_field,$val);
                        }

                    }
                }
            }
        }
        exit;
    }

    public function update_transaction_grid(){

        if(isset($_REQUEST['data']))
        {
            $data = $_REQUEST['data'];
            if(is_array($data))
            {
                foreach($data as $post_id)
                {
                    $post_type = get_post_type($post_id);
                    if($post_type == 'op_transaction')
                    {
                        $in = get_post_meta($post_id,'_in_amount',true);
                        $out = get_post_meta($post_id,'_out_amount',true);
                        $total_transaction = ($in - $out);
                        $balance = get_option('_pos_cash_balance',0);
                        $balance += $total_transaction;
                        update_option('_pos_cash_balance',$balance);
                        wp_delete_post($post_id);
                    }

                }
            }
        }
        exit;
    }

    public function update_inventory_grid(){
        global $op_warehouse;
        $data = array();
        if(isset($_REQUEST['data']))
        {
            parse_str($_REQUEST['data'], $data);
            $warehouse_id = $data['warehouse_id'];
            if(isset($data['barcode']))
            {
                foreach($data['qty'] as $product_id => $val)
                {
                    if($warehouse_id > 0)
                    {
                        if(!$val)
                        {
                            $val = 0;
                        }
                        $op_warehouse->set_qty($warehouse_id,$product_id,$val);
                    }else{
                        $product = wc_get_product($product_id);
                        if($val === '')
                        {
                            $product->set_manage_stock(false);

                        }else{
                            $qty = (float)$val;
                            $product->set_manage_stock(true);
                            $product->set_stock_quantity($qty);
                        }
                        $product->save();
                    }

                }
            }
        }
        echo json_encode($data);
        exit;
    }

    public function _short_code()
    {
        $is_pos = false;
        if(isset($_REQUEST['action']) && esc_attr($_REQUEST['action']) == 'openpos')
        {
            $is_pos = true;
        }
        add_shortcode( 'barcode', array($this,'_barcode_img_func'));
        add_shortcode( 'op_product', array($this,'_product_barcode_func'));
        if(!$is_pos)
        {
            add_shortcode( 'order_barcode', array($this,'_order_barcode_func'));
            add_shortcode( 'order_qrcode', array($this,'_order_qrcode_func'));

        }
        add_shortcode( 'op_warehouse', array($this,'_warehouse_func'));

        add_shortcode( 'op_register', array($this,'_register_func'));

        $this->register_post_types();
    }
    public function _order_qrcode_func($atts){
        global $op_current_order;
        $atts = shortcode_atts( array(
            'width' => 1,
            'height' => 1
        ), $atts, 'barcode' );
        $barcode = '100';
        $unit = 'px';
        if($op_current_order)
        {
            if(isset($op_current_order['order_number'])){
                $barcode = $op_current_order['order_number'];
            }
        }
        
        return '<img src="'.esc_url('https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.$barcode.'&choe=UTF-8').'" style="width: '.$atts['width'].$unit.' ;max-width:'.$atts['width'].$unit.';max-height:'.$atts['height'].$unit.';height:'.$atts['height'].$unit.'">';
        
    }
    public function _order_barcode_func($atts)
    {
        global $op_current_order;
        global $barcode_generator;
        global $OPENPOS_SETTING;
        $barcode_mode = $OPENPOS_SETTING->get_option('barcode_mode','openpos_label');

        switch ($barcode_mode)
        {
            case 'code_128':
                $mode = $barcode_generator::TYPE_CODE_128;
                break;
            case 'ean_13':
                $mode = $barcode_generator::TYPE_EAN_13;
                break;
            case 'ean_8':
                $mode = $barcode_generator::TYPE_EAN_8;
                break;
            case 'code_39':
                $mode = $barcode_generator::TYPE_CODE_39;
                break;
            case 'upc_a':
                $mode = $barcode_generator::TYPE_UPC_A;
                break;
            case 'upc_e':
                $mode = $barcode_generator::TYPE_UPC_E;
                break;
            default:
                $mode = $barcode_generator::TYPE_CODE_128;
        }

        

        $atts = shortcode_atts( array(
            'width' => 1.7,
            'height' => 0.5
        ), $atts, 'barcode' );
        $barcode = '100';
        if($op_current_order)
        {
            if(isset($op_current_order['order_number'])){
                $barcode = $op_current_order['order_number'];
            }
        }
        $unit = 'px';
        $img_data = $barcode_generator->getBarcode($barcode, $mode);
        return '<img src="data:image/png;base64, '.base64_encode($img_data).'" style="width: '.$atts['width'].$unit.' ;max-width:'.$atts['width'].$unit.';max-height:'.$atts['height'].$unit.';height:'.$atts['height'].$unit.'">';
    }
    public function _barcode_img_func($atts)
    {
        global $product;
        global $_op_product;
        global $barcode_generator;
        global $unit;
        global $OPENPOS_SETTING;
        global $_barcode_width;
        global $_barcode_height;
        global $_unit;
        $barcode_mode = $OPENPOS_SETTING->get_option('barcode_mode','openpos_label');

        if(!$_op_product && $product)
        {
            $_op_product = $product;
        }
        if($_op_product)
        {
            switch ($barcode_mode)
            {
                case 'code_128':
                    $mode = $barcode_generator::TYPE_CODE_128;
                    break;
                case 'ean_13':
                    $mode = $barcode_generator::TYPE_EAN_13;
                    break;
                case 'ean_8':
                    $mode = $barcode_generator::TYPE_EAN_8;
                    break;
                case 'code_39':
                    $mode = $barcode_generator::TYPE_CODE_39;
                    break;
                case 'upc_a':
                    $mode = $barcode_generator::TYPE_UPC_A;
                    break;
                case 'upc_e':
                    $mode = $barcode_generator::TYPE_UPC_E;
                    break;
                default:
                    $mode = $barcode_generator::TYPE_CODE_128;
            }

            $atts = shortcode_atts( array(
                'width' => 2.7,
                'height' => 0.5
            ), $atts, 'barcode' );

            if($_barcode_width)
            {
                $atts['width'] = $_barcode_width;
            }
            if($_barcode_height)
            {
                $atts['height'] = $_barcode_height;
            }

            $barcode = $this->core->getBarcode($_op_product->get_id());
            $unit = sanitize_text_field($_REQUEST['unit']);
            if($barcode_mode != 'qrcode')
            {
                $img_data = $barcode_generator->getBarcode($barcode, $mode);
                return '<img src="data:image/png;base64, '.base64_encode($img_data).'" style="width: '.$atts['width'].$unit.' ;max-width:'.$atts['width'].$unit.';max-height:'.$atts['height'].$unit.';height:'.$atts['height'].$unit.'">';

            }else{
                $chs = '100x100';
                if($unit == 'in')
                {
                    $barcode_w = round($atts['width'] * 96);
                    $barcode_h = round($atts['height'] * 96);
                    $chs = implode('x',array($barcode_w,$barcode_h));
                }
                if($unit == 'mm')
                {
                    $barcode_w = round($atts['width'] * 3.7795275591);
                    $barcode_h = round($atts['height'] * 3.7795275591);
                    $chs = implode('x',array($barcode_w,$barcode_h));
                }
                $img_url = 'https://chart.googleapis.com/chart?chs='.$chs.'&cht=qr&chl='.urlencode($barcode).'&choe=UTF-8';//$barcode_generator->getBarcode($barcode, $mode);
                return '<img src="'.esc_url($img_url).'" style="width: '.$atts['width'].$unit.' ;max-width:'.$atts['width'].$unit.';max-height:'.$atts['height'].$unit.';height:'.$atts['height'].$unit.'">';

            }
        }
        return '';

    }
    public function _product_barcode_func($atts)
    {
        global $_op_product;
        $atts = shortcode_atts( array(
            'attribute' => 'name',
            'format' => false
        ), $atts, 'op_product' );
        $result = '';
        switch ($atts['attribute'])
        {
            case 'barcode':
                $result = $this->core->getBarcode($_op_product->get_id());
                break;
            case 'price':
                $result = $this->core->getProductPrice($_op_product,$atts['format']);
                break;
            case 'name':
                $result = $_op_product->get_name();
                break;
            default:
                $methods = get_class_methods($_op_product);
                if(in_array('get_'.esc_attr($atts['attribute']),$methods))
                {
                    $result = $_op_product->{'get_'.esc_attr($atts['attribute'])}();
                }
                break;
        }
        if(strpos($atts['attribute'],'price') !== false)
        {
            $result = wc_price($result);
        }
        $result = apply_filters('op_product_info_label',$result,$_op_product,$atts );
        return $result;
    }

    public function _register_func($atts){
        global $op_register;
        $atts = shortcode_atts( array(
            'field' => '',
            'id' => 0
        ), $atts, 'op_register' );
        $result = $atts['field'];
        $register_id = isset($_REQUEST['cashdrawer_id']) ? intval($_REQUEST['cashdrawer_id']) : $atts['id'];
        if($register_id)
        {
            $field = esc_attr($result);
            $result = $register_id;
            $register = $op_register->get($register_id);
            if(isset($register[$field]))
            {
                $result = $register[$field];
            }
        }
        return apply_filters('op_register_field_info',$result,$atts);

    }
    public function _warehouse_func($atts){
        global $op_register;
        global $op_warehouse;
        $atts = shortcode_atts( array(
            'field' => '',
            'id' => 0
        ), $atts, 'op_warehouse' );
        $result = $atts['field'];

        $register_id = isset($_REQUEST['cashdrawer_id']) ? intval($_REQUEST['cashdrawer_id']) : $atts['id'];
        if($register_id)
        {
            $field = esc_attr($result);
            $result = $register_id;
            $register = $op_register->get($register_id);
            $warehouse_id = $register['warehouse'];
            $warehouse = $op_warehouse->get($warehouse_id);
            if(isset($warehouse[$field]))
            {
                $result = $warehouse[$field];
            }
        }
        return apply_filters('op_warehouse_field_info',$result,$atts);
    }


    public function print_bacode(){
        $is_preview = isset($_REQUEST['is_preview']) && $_REQUEST['is_preview'] == 1 ? true : false;
        $is_print = isset($_REQUEST['is_print']) && $_REQUEST['is_print'] == 1 ? true : false;
        if($is_preview)
        {
            global $_op_product;
            require(OPENPOS_DIR.'templates/admin/print_barcode_paper.php');
        }else{
            if(!isset($_POST['product_id']) && !$is_print)
            {
                require(OPENPOS_DIR.'templates/admin/print_barcode.php');
            }else{
                global $_op_product;
                require(OPENPOS_DIR.'templates/admin/print_barcode_paper.php');
            }
        }
        
        
        exit;
    }
    public function reset_balance(){
        global $op_register;
        $registers = $op_register->registers();
        foreach($registers as $register)
        {
            $balance_key = $op_register->get_transaction_balance_key($register['id']);
            update_option($balance_key,0);
        }

    }
    public function print_receipt(){
        global $op_receipt;   
        global $op_register;   
        global $op_current_order;   
        global $op_woo;   
        wp_register_script('openpos.admin.receipt.ejs', OPENPOS_URL.'/assets/js/ejs.js',array('jquery'));
        $sections = $this->settings_api->get_fields();
        $setting = array();
        foreach($sections as $section => $fields)
        {
            foreach($fields as $field)
            {
                if(isset($field['name']))
                {
                    $option = $field['name'];

                    $setting[$option] = $this->settings_api->get_option($option,$section);
                    if($option == 'receipt_template_header' || $option == 'receipt_template_footer')
                    {
                        $setting[$option] = balanceTags($setting[$option],true);
                    }
                }

            }
        }
        $setting = $this->core->formatReceiptSetting($setting);

        $receipt_padding_top = $setting['receipt_padding_top'];
        $unit = 'in';
        $receipt_padding_right = $setting['receipt_padding_right'];
        $receipt_padding_bottom = $setting['receipt_padding_bottom'];
        $receipt_padding_left = $setting['receipt_padding_left'];
        $receipt_width = $setting['receipt_width'];
        $receipt_css = $setting['receipt_css'];
        $receipt_template_header = $setting['receipt_template_header'];
        $receipt_template = $setting['receipt_template'];
        $receipt_template_footer = $setting['receipt_template_footer'];

        $receipt_template_footer = do_shortcode($receipt_template_footer);

        $html_header = '<style type="text/css" media="print,screen">';

        $html_header .= '#invoice-POS { ';
        $html_header .= 'padding:  '.$receipt_padding_top.$unit. ' ' . $receipt_padding_right.$unit .' '.$receipt_padding_bottom.$unit.' '.$receipt_padding_left.$unit.';';
        $html_header .= 'margin: 0 auto;';
        $html_header .= 'width: '.$receipt_width.$unit.' ;';
        $html_header .=  '}';

        $html_header .= $receipt_css;
        $html_header .= '</style>';
        $html_header .= '<body>';


        $html = '<div id="invoice-POS">';
        $html .= '<div id="invoce-header">';
        $html .= $receipt_template_header;


        $html .= '</div>';
        $html .= '<div id="bot">';

        $html .= '<div id="table">';
        $html .= $receipt_template;

        $html .= '</div><!--End Table-->';

        $html .= '<div id="invoce-footer">';
        $html .= $receipt_template_footer;
        $html .= '</div>';

        $html .= '</div><!--End InvoiceBot-->';
        $html .= '</div><!--End Invoice-->';

        $html = trim(preg_replace('/\s+/', ' ', $html));
        $order_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
        $template_id = isset($_REQUEST['template_id']) ? (int)$_REQUEST['template_id'] : 0;
        $order_json = '';
        if($order_id)
        {
            $order_data = get_post_meta($order_id,'_op_order',true);
            
            if($order_data)
            {
                $op_current_order = $order_data;
                $order_json = json_encode($order_data);
            }else{
                $order = wc_get_order($order_id);
                if($order)
                {
                    $tmp_order = $op_woo->formatWooOrder($order_id);
                    $order_json = json_encode($tmp_order);
                }
            }

        }

        $data = array(
            'setting' => $setting,
            'html_header' =>$html_header,
            'html_body' =>  addslashes(html_entity_decode($html)),
            'order_json' =>  $order_json
        );
        $cashdrawer_meta_key = $op_register->get_order_meta_key();
        $register_id = get_post_meta($order_id,$cashdrawer_meta_key,true);
        if($register_id)
        {
            $receipt_template = $op_receipt->get_register_template($register_id);
            if($receipt_template && !empty($receipt_template))
            {
                $data = $op_receipt->get_template_preview_data($receipt_template);
                $data['order_json'] =  $order_json;
            }
        }else{
            if($template_id)
            {
                $receipt_template = $op_receipt->get($template_id);
                if($receipt_template && !empty($receipt_template))
                {
                    $data = $op_receipt->get_template_preview_data($receipt_template);
                    
                }
            }
        }
        require(OPENPOS_DIR.'templates/admin/print_receipt.php');
        exit;
    }

    //register
    public function update_register(){
        global $op_register;
        $params = $_POST;
        $result = array(
            'status' => 0,
            'message' => 'Unknown'
        );
        try{

            if(!$params['name'])
            {
                throw new Exception(__('Please enter register name','openpos'));
            }
            do_action('op_register_save_before',$params,$op_register);
            $id = $op_register->save($params);
            do_action('op_register_save_after',$id,$params,$op_register);
            $result['status'] = 1;
        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }
    public function delete_register(){
        global $op_register;
        $params = $_POST;
        $result = array(
            'status' => 0,
            'message' => 'Unknown'
        );
        try{
            $id = isset($params['id']) ? $params['id'] : 0;
            if(!$id)
            {
                throw new Exception(__('Please choose register to delete','openpos'));
            }
            do_action('op_register_delete_before',$params,$op_register);
            $op_register->delete($id);
            do_action('op_register_delete_after',$params,$op_register);
            $result['status'] = 1;
        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }
    //table
    public function update_table(){
        global $op_table;
        $params = $_POST;
        $result = array(
            'status' => 0,
            'message' => 'Unknown'
        );
        try{

            if(!$params['name'])
            {
                throw new Exception(__('Please enter register name','openpos'));
            }
            do_action('op_table_save_before',$params,$op_table);
            $id = $op_table->save($params);
            do_action('op_table_save_after',$id,$params,$op_table);
            $result['status'] = 1;
        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }
    public function delete_table(){
        global $op_table;
        $params = $_POST;
        $result = array(
            'status' => 0,
            'message' => 'Unknown'
        );
        try{
            $id = isset($params['id']) ? $params['id'] : 0;
            if(!$id)
            {
                throw new Exception(__('Please choose table to delete','openpos'));
            }
            do_action('op_table_delete_before',$params,$op_table);
            $op_table->delete($id);
            do_action('op_table_delete_after',$params,$op_table);
            $result['status'] = 1;
        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }
    //warehouse
    public function update_warehouse(){
        global $op_warehouse;
        $params = $_POST;
        $result = array(
            'status' => 0,
            'message' => 'Unknown',
            'data' => array()
        );
        try{

            if(!$params['name'])
            {
                throw new Exception(__('Please enter outlet name','openpos'));
            }
            do_action('op_warehouse_save_before',$params,$op_warehouse);
            $id = $op_warehouse->save($params);
            do_action('op_warehouse_save_after',$id,$params,$op_warehouse);
            $result['data'] = array(
                'id' => $id
            );
            $result['status'] = 1;
        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }
    public function delete_warehouse(){
        global $op_warehouse;
        $params = $_POST;
        $result = array(
            'status' => 0,
            'message' => 'Unknown'
        );
        try{
            $id = isset($params['id']) ? $params['id'] : 0;
            if(!$id)
            {
                throw new Exception(__('Please choose warehouse to delete','openpos'));
            }
            do_action('op_warehouse_delete_before',$params,$op_warehouse);
            $op_warehouse->delete($id);
            do_action('op_warehouse_delete_after',$params,$op_warehouse);
            $result['status'] = 1;
        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }
    public function stock_overview(){
        global $op_warehouse;
        $params = $_POST;
        $result = array(
            'status' => 0,
            'message' => 'Unknown'
        );
        try{
            $barcode = isset($params['barcode']) ? $params['barcode'] : 0;
            if(!$barcode)
            {

                throw new Exception(__('Please enter barcode to search','openpos'));
            }
            $product_id = $this->core->getProductIdByBarcode($barcode);
            if($product_id)
            {
                $warehouses = $op_warehouse->warehouses();
                $result['html'] = '<div class="table-responsive"><table class="table"><tr><th>'.__('Outlet','openpos').'</th><th>'. __('Qty','openpos').'</th></tr>';
                $total_with_online = 0;
                $total_no_online = 0;

                foreach($warehouses as $w)
                {
                    if($w['status'] == 'draft')
                    {
                        continue;
                    }
                    $qty = $op_warehouse->get_qty($w['id'],$product_id);
                    $total_with_online += $qty;
                    if($w['id'])
                    {
                        $total_no_online += $qty;
                    }
                    $result['html'] .= '<tr ><td>'.$w['name'].'</td><td>'.$qty.'</td></tr>';
                }


                $result['html'] .= '<tr ><th>'. __('Total','openpos').'</th><td>'. $total_with_online .'</td></tr>';
                //$result['html'] .= '<tr ><th>'. __('Total Qty Without Online','openpos').'</th><td>'. $total_no_online .'</td></tr>';

                $result['html'] .= '<tr><td colspan="2"></td></tr></table></div>';
                $result['status'] = 1;
            }else{
                $result['message'] = __('No product found. Please check your barcode !','openpos');
            }

        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }
    public function export_inventory(){
        global $op_warehouse;
        $warehouse_id = (int)$_GET['warehouse_id'];
        $warehouse = $op_warehouse->get($warehouse_id);
        if(empty($warehouse))
        {
            die(__('Your warehouse do not exist.','openpos'));
        }
        $query = $this->core->getProducts(array(
            'numberposts' => -1
        ));
        $products = $query['posts'];
        $filename = 'inventory.csv';
        $fh = @fopen( 'php://output', 'w' );
        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Content-Description: File Transfer' );
        header( 'Content-type: text/csv' );
        header( "Content-Disposition: attachment; filename={$filename}" );
        header( 'Expires: 0' );
        header( 'Pragma: public' );
        fputcsv( $fh, array(__('Barcode','openpos'),__('Qty','openpos'),__('Name','openpos')));
        //start product data
        foreach($products as $p)
        {
           
            if(is_a($p, 'WP_Post'))
            {
                $product_id = $p->ID;
            }else{
                $product_id = $p->get_id();
                
            }
            $product = wc_get_product($product_id);
            $name = $product->get_name();
            $barcode = $this->core->getBarcode($product_id);
            if($warehouse_id > 0)
            {
                $qty = $op_warehouse->get_qty($warehouse_id,$product_id);
            }else{
                
                if(!$product->get_manage_stock())
                {
                    $qty = '';
                }else{
                    $qty = $product->get_stock_quantity();
                }
            }

            fputcsv( $fh, array($barcode,$qty,$name));
        }
        //end product data
        fclose( $fh );
        ob_end_flush();
        exit;
    }
    public function upload_inventory_csv(){
        $result = array('status' => 0,'data' => array(),'message' => '');
        try{
            $data = array();

            if(current_user_can('upload_files') && isset($_FILES['file']))
            {
                $file = $_FILES['file'];
                if($file['type'])
                {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
                    $csv = $spreadsheet->getActiveSheet()->toArray();
                    $labels = $csv[0];
                    array_shift($csv);
                    $barcode_index = 0;
                    $qty_index = 0;
                    foreach($labels as $key => $label)
                    {
                        if(strtoupper($label) == strtoupper('barcode'))
                        {
                            $barcode_index = $key;
                        }
                        if(strtoupper($label) == strtoupper('qty'))
                        {
                            $qty_index = $key;
                        }
                    }

                    foreach($csv as $row)
                    {
                        $barcode = $row[$barcode_index];
                        $qty = $row[$qty_index];
                        $product_id = $this->core->getProductIdByBarcode($barcode);
                        $post = get_post($product_id);
                        if($post)
                        {
                            $_barcode = $this->core->getBarcode($product_id);
                            $name = $post->post_title;
                            $data[] = array(
                                'id' => $product_id,
                                'barcode' => $_barcode,
                                'name' => $name,
                                'qty' => $qty
                            );
                        }


                    }
                }
            }
            $result['data'] = $data;
            $result['status'] = 1;
        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }
    public function adjust_stock_finder(){
        $params = $_POST;
        $result = array(
            'status' => 0,
            'message' => 'Unknown',
            'data' => array()
        );
        try{
            $barcode = isset($params['barcode']) ? $params['barcode'] : 0;
            if(!$barcode)
            {

                throw new Exception(__('Please enter barcode to search','openpos'));
            }
            $product_id = $this->core->getProductIdByBarcode($barcode);
            if($product_id)
            {
                $post = get_post($product_id);
                if($post)
                {
                    $_barcode = $this->core->getBarcode($product_id);
                    $name = $post->post_title;
                    $result['data'][]  = array(
                        'id' => $product_id,
                        'barcode' => $_barcode,
                        'name' => $name,
                        'qty' => 1
                    );
                }

                $result['status'] = 1;
            }else{
                $result['message'] = __('No product found. Please check your barcode !','openpos');
            }

        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }

    public function op_adjust_stock(){
        global $op_warehouse;
        $result = array(
            'status' => 0,
            'message' => 'Unknown',
            'data' => array()
        );
        try{
            $params = $_POST;

            $result['data'] = $params['product'];

            if(!isset($params['warehouse_id']) )
            {
                throw new Exception(__('Outlet do not found'));
            }
            $warehouse_id = (int)$params['warehouse_id'];

            if(!isset($params['product']) || empty($params['product']))
            {
                throw new Exception(__('Product do not found'));
            }
            $products = $params['product'];
            foreach($products as $product_id => $qty)
            {
                if($warehouse_id > 0)
                {
                    $op_warehouse->set_qty($warehouse_id,$product_id,$qty);
                }else{
                    $product = wc_get_product($product_id);
                    if($qty === '')
                    {
                        $product->set_manage_stock(false);
                    }else{
                        $product->set_manage_stock(true);
                        $qty = (float)$qty;
                        $product->set_stock_quantity($qty);
                    }
                    $product->save();
                }

            }
            $result['status'] = 1;

        }catch (Exception $e)
        {
            $result['status'] = 0;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
        exit;
    }
    function op_ajax_category(){
        $query = $_REQUEST['search'];

        $args = array(
            'taxonomy'   => "product_cat",
            'hide_empty' => false,
            'name__like' => $query
        );
        $product_categories = get_terms($args);

        $result = array();
        foreach($product_categories as $cat)
        {

            $id = $cat->term_id;
            $text = $cat->name;
            $result[] = array(
                'value' => $id,
                'text' => $text
            );
        }

        echo json_encode($result);
        exit;
    }
    function op_ajax_order_statuses(){
        $result = array();
        $wc_order_status = wc_get_order_statuses();
        
        foreach($wc_order_status as $key =>$status)
        {
            $result[] = array(
                'value' => $key,
                'text' => $status
            );
        }
        
        echo json_encode($result);
        exit;
    }
    function upload_product_image(){
        $product_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
        if($product_id && current_user_can('upload_files'))
        {
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            $uploadedfile = $_FILES['field_value'];

            $upload_overrides = array( 'test_form' => false );

            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

            if ( $movefile && ! isset( $movefile['error'] ) ) {
                $type = $movefile['type'];
                if(strpos($type,'image') >= 0)
                {
                    $filename = $movefile['file'];
                    $attachment = array(
                        'guid'           => $movefile['url'],
                        'post_mime_type' => $type,
                        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );
                    $attach_id = wp_insert_attachment( $attachment, $filename, $product_id );
                    require_once( ABSPATH . 'wp-admin/includes/image.php' );

                    $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    set_post_thumbnail( $product_id, $attach_id );
                }
            } else {
                /**
                 * Error generated by _wp_handle_upload()
                 * @see _wp_handle_upload() in wp-admin/includes/file.php
                 */
                echo $movefile['error'];
            }
        }

    }
    function admin_global_style(){
        wp_enqueue_style('openpos.admin.global', OPENPOS_URL.'/assets/css/admin_global.css');
    }

    function admin_notice_init(){
        $option_page = isset($_REQUEST['option_page']) ? esc_attr($_REQUEST['option_page']) : '';
        $action = isset($_REQUEST['action']) ? esc_attr($_REQUEST['action']) : '';
        if(strpos($option_page,'openpos_') !== false && $action == 'update')
        {
            update_option('_admin_op_setting_msg',__( 'Your setting has been update succes. Don\'t forget Logout and Login POS again to take effect on POS panel !', 'openpos' ));
        }
    }

    function admin_notice() {

        $msg = get_option('_admin_op_setting_msg',false);

        if($msg)
        {
            ?>
            <div class="notice">
                <p style="color: green;"><?php echo $msg; ?></p>
            </div>
            <?php
            update_option('_admin_op_setting_msg','');
        }
    }
    function woocommerce_product_options_stock_fields(){
        global $post;
        global $op_warehouse;
        $warehouses = $op_warehouse->warehouses();
        if($post && count($warehouses) > 1)
        {
            $product = wc_get_product($post->ID);
            $product_type = $product->get_type();
            if($product_type != 'variable')
            {
                ?>
                <div class="op-product-outlet-stock hide_if_variable">
                    <p class="op-stock-label"><?php echo __('Other Outlet Stock quantity'); ?></p>
                    <table border="1">
                        <?php foreach($warehouses as $warehouse): ?>
                            <?php if($warehouse['id'] > 0): $warehouse_id  = $warehouse['id']; ?>
                            <tr>
                                <th><?php echo sprintf(__( '<strong>%s</strong>', 'openpos' ),$warehouse['name']); ?></th>
                                <td>
                                    <?php
                                        $product_id = $post->ID;
                                        $qty = '';
                                        if($op_warehouse->is_instore($warehouse_id,$product_id))
                                        {
                                            $qty = $op_warehouse->get_qty($warehouse_id,$product_id);
                                            
                                        }
                                        woocommerce_wp_text_input(
                                            array(
                                                'id'                => '_op_stock',
                                                'name'                => '_op_stock['.$warehouse_id.']',
                                                'value'             => $qty,
                                                'label'             => '',
                                                'type'              => 'text'
                                    
                                            )
                                        );
                                    ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php
            }

        }

    }
    function woocommerce_variation_options_inventory($loop, $variation_data, $variation){

        global $op_warehouse;
        $warehouses = $op_warehouse->warehouses();
        if(count($warehouses) > 1)
        {

            ?>
            <div class="op-product-outlet-stock-variation">
                <p class="op-stock-label"><?php echo __('Other Outlet Stock quantity'); ?></p>

                <table border="1">
                    <?php foreach($warehouses as $warehouse): ?>
                        <?php if($warehouse['id'] > 0): $warehouse_id  = $warehouse['id']; ?>
                            <tr>
                                <th><?php echo sprintf(__( '<strong>%s</strong>', 'openpos' ),$warehouse['name']); ?></th>
                                <td>
                                    <?php

                                    $qty = 0;
                                    if($variation && isset($variation->ID))
                                    {
                                        $variation_id = $variation->ID;

                                        $qty = 1 * $op_warehouse->get_qty($warehouse_id,$variation_id);

                                        if(!$op_warehouse->is_instore($warehouse_id,$variation_id))
                                        {
                                                $qty = '';
                                        }
                                    }

                                    

                                    woocommerce_wp_text_input(
                                        array(
                                            'id'                => "_op_stock_{$warehouse_id}_{$loop}",
                                            'name'              => "_op_stock[{$warehouse_id}][{$loop}]",
                                            'label'             => '',
                                            'value' => $qty,
                                            'type'         => 'text',
                                            'wrapper_class' => 'form-row form-row-full op-outlet-variation-stock-row',
                                        )
                                    );
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>



            </div>
            <?php
        }

    }

    function woocommerce_product_options_pricing(){
        global $post;
        $product = wc_get_product($post->ID);
        $product_type = $product->get_type();
        $price = '';
        if($product && $product_type != 'variable')
        {
            $tmp_price = get_post_meta($post->ID,'_op_cost_price',true);
            if($tmp_price !== '')
            {
                $price = $tmp_price;
            }
        }
        woocommerce_wp_text_input(
            array(
                'id'        => '_op_cost_price',
                'value'     => $price,
                'label'     => __( 'OP Cost price', 'openpos' ) . ' (' . get_woocommerce_currency_symbol() . ')',
                'data_type' => 'price',
                'desc_tip'   => true,
                'description'   => __( 'Cost price - Use to get commision report', 'openpos' ),
            )
        );

    }
    function woocommerce_variation_options_pricing($loop, $variation_data, $variation){
        $price = '';
        if($variation && isset($variation->ID))
        {
            $variation_id = $variation->ID;

            $tmp_price = get_post_meta($variation_id,'_op_cost_price',true);
            if($tmp_price !== '')
            {
                $price = $tmp_price;
            }
        }
        woocommerce_wp_text_input(
            array(
                'id'            => "_op_cost_price{$loop}",
                'name'          => "_op_cost_price[{$loop}]",
                'value'         => $price,
                'label'         => __( 'OP Cost price', 'openpos' ) . ' (' . get_woocommerce_currency_symbol() . ')',
                'data_type'     => 'price',
                'wrapper_class' => 'form-row form-row-first',
                'placeholder'   => __( 'Cost price', 'openpos' ),
                'desc_tip'   => true,
                'description'   => __( 'Cost price - Use to get commision report', 'openpos' ),
            )
        );

    }
    function woocommerce_product_options_advanced(){
        global $post;
        $tmp_price = get_post_meta($post->ID,'_op_weight_base_pricing',true);
        if($tmp_price != 'yes')
        {
            $tmp_price = 'no';
        }
        ?>
        <div class="options_group op-weight-base-pricing">
            <?php
            woocommerce_wp_checkbox(
                array(
                    'id'      => '_op_weight_base_pricing',
                    'value'   => $tmp_price,
                    'label'   => __( 'POS Weight-base Pricing', 'openpos' ),
                    'cbvalue' => 'yes',
                )
            );
            ?>
        </div>
        <?php
    }


}