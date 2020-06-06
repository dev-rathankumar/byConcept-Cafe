<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 9/14/18
 * Time: 21:54
 */
class OP_Woo{
    private $settings_api;
    private $_core;
    private $_session;

    public function __construct()
    {
        $this->_session = new OP_Session();
        $this->settings_api = new Openpos_Settings();
        $this->_core = new Openpos_Core();

    }

    public function init(){
        add_filter( 'posts_where', array($this,'title_filter'), 10, 2 );
        add_filter('woocommerce_payment_complete_reduce_order_stock',array($this,'op_payment_complete_reduce_order_stock'),10,2);
        add_action( 'op_add_order_after', array($this,'op_maybe_reduce_stock_levels'),10,1 );
        add_action( 'op_woocommerce_cancelled_order', array($this,'op_maybe_increase_stock_levels'),10,1 );
        add_action( 'parse_query', array( $this, 'order_table_custom_fields' ) );
        add_action( 'woocommerce_order_refunded', array( $this, 'woocommerce_order_refunded' ),10,2 );
        add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'woocommerce_hidden_order_itemmeta' ),10,1 );
        add_filter( 'woocommerce_available_payment_gateways', array( $this, 'woocommerce_available_payment_gateways' ),10,1 );
        add_filter( 'woocommerce_order_get_payment_method_title', array( $this, 'woocommerce_order_get_payment_method_title' ),10,2 );

        add_action( 'woocommerce_product_options_sku', array( $this, 'woocommerce_product_options_sku_after' ),100);
        add_action( 'woocommerce_variation_options_dimensions', array( $this, 'woocommerce_variation_options_dimensions_after' ),100,3);
        add_action('woocommerce_save_product_variation',array($this,'woocommerce_save_product_variation'),10,2);
        add_action('woocommerce_admin_process_product_object',array($this,'woocommerce_admin_process_product_object'),10,1);
        add_action('woocommerce_after_order_itemmeta',array($this,'woocommerce_after_order_itemmeta'),10,3);

        add_action('woocommerce_email_recipient_customer_completed_order',array($this,'woocommerce_email_recipient_customer_completed_order'),10,2);

        add_action('woocommerce_admin_order_data_after_shipping_address',array($this,'woocommerce_admin_order_data_after_shipping_address'),10,1);


        add_filter('manage_edit-shop_order_columns', array($this,'order_columns_head'),10,1);
        add_action('manage_shop_order_posts_custom_column', array($this,'order_columns_content'), 10, 2);

        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action( 'restrict_manage_posts', array( $this, 'filter_orders_by_source' ) );
            add_filter( 'parse_query', array( $this,'filter_request_query') , 10,1);


        }
        add_filter('product_type_options', array($this,'product_type_options'),10,1);


        add_action( 'woocommerce_new_product', array( $this, 'woocommerce_new_product' ), 30,1 );
        add_action( 'woocommerce_update_product', array( $this, 'woocommerce_update_product' ), 30 ,1);

        add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'woocommerce_order_item_display_meta_key' ), 30 ,2);


        add_filter( 'pre_option_woocommerce_registration_generate_password', array( $this, 'pre_option_woocommerce_openpos' ), 30 ,3);
        add_filter( 'pre_option_woocommerce_registration_generate_username', array( $this, 'pre_option_woocommerce_openpos' ), 30 ,3);

        //$pre = apply_filters( "pre_option_{$option}", false, $option, $default );

        add_action( 'woocommerce_loaded', array( $this, 'woocommerce_loaded' ));
    }

    public function pre_option_woocommerce_openpos($value , $option, $default){
        global $op_session_data;
        if($op_session_data && isset($op_session_data['user_id']) && $op_session_data['user_id'])
        {
            if($option == 'woocommerce_registration_generate_password'){
                $value = 'yes';
            }
            if($option == 'woocommerce_registration_generate_username'){
                $value = 'yes';
            }
        }
        
        return $value;
    }

    public function order_columns_head($defaults){

        $result = array();
        foreach($defaults as $key => $value)
        {
            $result[$key] = $value;
            if($key == 'cb')
            {
                $result['op_source']  = __('Source','openpos');

            }
        }
        return $result;
    }
    public function order_columns_content($column_name, $post_ID){
        if ($column_name == 'op_source') {
            $source = get_post_meta($post_ID,'_op_order_source',true);
            if($source == 'openpos')
            {
                echo '<img style="width: 16px;" alt="from openpos" src="'.OPENPOS_URL.'/assets/images/shop.png">';
            }else{
                echo '<img style="width: 16px;" alt="from online website" src="'.OPENPOS_URL.'/assets/images/woocommerce.png">';
            }
        }
    }

    public function woocommerce_hidden_order_itemmeta($meta){
        $meta[] = '_op_local_id';
        $meta[] = '_op_seller_id';
        $meta[] = '_op_reduced_stock';
        return $meta;
    }
    public function woocommerce_order_refunded($order_id,$refund_id)
    {

        global $op_warehouse;

        $warehouse_id = get_post_meta($order_id,'_pos_order_warehouse',true);

        if($warehouse_id > 0)
        {
                $refund = new WC_Order_Refund($refund_id );
                $line_items = $refund->get_items();
                foreach($line_items as $item)
                {
                    $product_id = $item->get_product_id();
                    $variation_id = $item->get_variation_id();

                    $post_product_id = $product_id;
                    if($variation_id  > 0)
                    {
                        $post_product_id = $variation_id;
                    }
                    $refund_qty = $item->get_quantity();
                    $current_qty = $op_warehouse->get_qty($warehouse_id,$post_product_id);
                    $new_qty = $current_qty - $refund_qty;
                    $op_warehouse->set_qty($warehouse_id,$post_product_id,$new_qty);
                }
        }

    }
    public function order_pos_payment($order){
        global $op_warehouse;
        global $op_register;
        $id = $order->get_id();
        $source = get_post_meta($id,'_op_order_source',true);
        if($source == 'openpos')
        {
            $payment_methods = get_post_meta($id,'_op_payment_methods',true);
            $_op_order_addition_information = get_post_meta($id,'_op_order_addition_information',true);

            

            $_op_sale_by_person_id = get_post_meta($id,'_op_sale_by_person_id',true);
            $_pos_order_id = get_post_meta($id,'_pos_order_id',true);
            $warehouse_meta_key = $op_warehouse->get_order_meta_key();
            $register_meta_key = $op_register->get_order_meta_key();
            $warehouse_id = get_post_meta($id,$warehouse_meta_key,true);
            $register_id = get_post_meta($id,$register_meta_key,true);
            $warehouse = $op_warehouse->get($warehouse_id);
            $register = $op_register->get($register_id);
            ?>
            <?php if($_op_sale_by_person_id): $person = get_user_by('id',$_op_sale_by_person_id);  ?>

                <p class="form-field form-field-wide">
                    <label><?php esc_html_e( 'POS Order Number:', 'openpos' ); ?> <b><?php echo esc_html($_pos_order_id)?></b></label>
                </p>
            <?php endif; ?>
            <?php if($_op_sale_by_person_id): $person = get_user_by('id',$_op_sale_by_person_id);  ?>
            <p class="form-field form-field-wide">
                <label><?php esc_html_e( 'Shop Agent:', 'openpos' ); ?> <b><?php echo esc_html($person->get('display_name') )?></b></label>
            </p>
            <?php endif; ?>
            <?php if(!empty($warehouse)): ?>
            <p class="form-field form-field-wide">
                <label><?php esc_html_e( 'Outlet:', 'openpos' ); ?> <b><?php echo esc_html($warehouse['name'] )?></b></label>
            </p>
            <?php endif; ?>
            <?php if(!empty($warehouse)): ?>
            <p class="form-field form-field-wide">
                <label><?php esc_html_e( 'Register:', 'openpos' ); ?> <b><?php echo isset($register['name']) ?  esc_html($register['name'] ) : __('Unknown','openpos'); ?></b></label>
            </p>
            <?php endif; ?>
            <?php if($payment_methods): ?>
            <hr/>
            <p class="form-field form-field-wide">
                <label><?php esc_html_e( 'POS Payment method:', 'openpos' ); ?></label>
                <ul>
                <?php foreach($payment_methods as $method): ?>
                    <li><?php echo esc_html($method['name']); ?>: <?php echo wc_price($method['paid']); ?> <?php echo $method['ref'] ? '('.esc_html($method['ref']).')':''; ?></li>
                <?php endforeach; ?>
                </ul>
            </p>
            <?php endif; ?>

            <?php if(!empty( $_op_order_addition_information)): ?>
            <hr/>
            <p class="form-field form-field-wide">
                <label><?php esc_html_e( 'Additional information:', 'openpos' ); ?></label>
                <ul>
                <?php foreach($_op_order_addition_information as $key => $info): ?>
                    <li><?php echo esc_html($key); ?>: <?php echo is_array($info) ?  implode(',',$info) : $info ; ?></li>
                <?php endforeach; ?>
                </ul>
            </p>
            <?php endif; ?>

           
            <?php
        }

    }
    public function get_cashiers(){
        $args = array(
            'meta_key' => '_op_allow_pos',
            'meta_value' => '1',
            'fields' => array('ID', 'display_name','user_email','user_login','user_status'),
            'number' => -1
        );
        $cashiers =  get_users( $args);
        $result = array();
        foreach($cashiers as $cashier)
        {
            $result[] = $cashier;
        }
        return $result;
    }
    public function op_payment_complete_reduce_order_stock($result,$order_id){
        global $op_warehouse;
        global $op_session_data;

        $warehouse_meta_key = $op_warehouse->get_order_meta_key();
        $warehouse_id = get_post_meta($order_id,$warehouse_meta_key,true);
        if( $op_session_data )
        {
            $result = false;
        }
        return $result;
    }
    public function op_maybe_reduce_stock_levels($order_id)
    {
        global $_op_warehouse_id;
        global $op_warehouse;
        if ( is_a( $order_id, 'WC_Order' ) ) {
            $order    = $order_id;
            $order_id = $order->get_id();
        } else {
            $order = wc_get_order( $order_id );
        }
        $warehouse_id = $_op_warehouse_id;

        if($warehouse_id > 0)
        {
            foreach ( $order->get_items() as $item ) {
                if ( ! $item->is_type( 'line_item' ) ) {
                    continue;
                }

                $product = $item->get_product();

                $item_stock_reduced = $item->get_meta( '_op_reduced_stock', true );
                if ($item_stock_reduced || ! $product) {
                    continue;
                }

                $item_data = $item->get_data();
                $variation_id = isset($item_data['variation_id']) ? $item_data['variation_id'] : 0;

                if ( $product ) {
                    $product_id = $product->get_id();
                    if($variation_id > 0)
                    {
                        $product_id = $variation_id;
                    }

                    $qty       = apply_filters( 'woocommerce_order_item_quantity', $item->get_quantity(), $order, $item );
                    $current_qty = $op_warehouse->get_qty($warehouse_id,$product_id);
                    if(!$current_qty)
                    {
                        $current_qty = 0;
                    }
                    $new_qty = $current_qty - $qty;
                    $op_warehouse->set_qty($warehouse_id,$product_id,$new_qty);
                    

                    $item->add_meta_data( '_op_reduced_stock', $qty, true );
		            $item->save();

                }
            }

        }else{
            $changes = array();

            foreach ( $order->get_items() as $item ) {
                if ( ! $item->is_type( 'line_item' ) ) {
                    continue;
                }
                $product            = $item->get_product();
                $item_stock_reduced = $item->get_meta( '_reduced_stock', true );
                if ($item_stock_reduced || ! $product || ! $product->managing_stock() ) {
                    continue;
                }
               
                if ( $product ) {
                    $product_id = $product->get_id();
                    $this->_core->addProductChange($product_id,0);
                }

                
                $qty       = apply_filters( 'woocommerce_order_item_quantity', $item->get_quantity(), $order, $item );
                $new_stock = wc_update_product_stock( $product, $qty, 'decrease' );

                $item->add_meta_data( '_reduced_stock', $qty, true );
		        $item->save();

                $changes[] = array(
                    'product' => $product,
                    'from'    => $new_stock + $qty,
                    'to'      => $new_stock,
                );
                
                
            }
            if(!empty($changes))
            {
                wc_trigger_stock_change_notifications( $order, $changes );
            }
        }
    }

    public function op_maybe_increase_stock_levels($order_id){
        global $_op_warehouse_id;
        global $op_warehouse;
        if ( is_a( $order_id, 'WC_Order' ) ) {
            $order    = $order_id;
            $order_id = $order->get_id();
        } else {
            $order = wc_get_order( $order_id );
        }
        $warehouse_id = $_op_warehouse_id;
        
        if($warehouse_id > 0)
        {
            foreach ( $order->get_items() as $item ) {
                if ( ! $item->is_type( 'line_item' ) ) {
                    continue;
                }
               

                $product = $item->get_product();
                $item_data = $item->get_data();
                $variation_id = isset($item_data['variation_id']) ? $item_data['variation_id'] : 0;

                if ( $product ) {
                    $product_id = $product->get_id();
                    if($variation_id > 0)
                    {
                        $product_id = $variation_id;
                    }

                    $qty       = apply_filters( 'woocommerce_order_item_quantity', $item->get_quantity(), $order, $item );
                    $current_qty = $op_warehouse->get_qty($warehouse_id,$product_id);
                    if(!$current_qty)
                    {
                        $current_qty = 0;
                    }
                    $new_qty = $current_qty + $qty;
                    $op_warehouse->set_qty($warehouse_id,$product_id,$new_qty);
                }
            }

        }else{
            $changes = array();
            
            foreach ( $order->get_items() as $item ) {
                if ( ! $item->is_type( 'line_item' ) ) {
                    continue;
                }
                $product            = $item->get_product();
                $item_stock_reduced = $item->get_meta( '_reduced_stock', true );
                
                if (!$item_stock_reduced || ! $product || ! $product->managing_stock() ) {
                    continue;
                }
               
                
                $qty       = apply_filters( 'woocommerce_order_item_quantity_increase', $item_stock_reduced, $order, $item );
                $new_stock = wc_update_product_stock( $product, $qty, 'increase' );

                $item->add_meta_data( '_reduced_stock', 0, true );
                $item->save();
                
                if ( $product ) {
                    $product_id = $product->get_id();
                    $this->_core->addProductChange($product_id,0);
                }

                $changes[] = array(
                    'product' => $product,
                    'from'    => $new_stock - $qty,
                    'to'      => $new_stock,
                );
                
                
            }
            if(!empty($changes))
            {
                wc_trigger_stock_change_notifications( $order, $changes );
            }
        }
       
    }

    public function order_table_custom_fields($wp){
        global $pagenow;

        if ( 'edit.php' !== $pagenow  || 'shop_order' !== $wp->query_vars['post_type']  ) { // WPCS: input var ok.
            return;
        }
        if(isset( $_GET['warehouse'] ))
        {
            $query_vars = $wp->query_vars;
            $query_vars['meta_key'] = '_pos_order_warehouse';
            $query_vars['meta_value'] = (int)$_GET['warehouse'];
            $wp->query_vars = $query_vars;
            return;
        }
        if(isset( $_GET['register'] ))
        {
            $query_vars = $wp->query_vars;
            $query_vars['meta_key'] = '_pos_order_cashdrawer';
            $query_vars['meta_value'] = (int)$_GET['register'];
            $wp->query_vars = $query_vars;
            return;
        }

    }
   

    public function get_available_variations($variation) {
        $available_variations = array();
        $tmp_variable = new WC_Product_Variable();
        foreach ( $variation->get_children() as $child_id ) {
            $variation = wc_get_product( $child_id );
            $available_variations[] = $tmp_variable->get_available_variation( $variation );
        }
        $available_variations = array_values( array_filter( $available_variations ) );

        return $available_variations;
    }

    public function get_variations($product_id,$warehouse_id = 0){
        global $op_warehouse;
        $core = $this->_core;
        $variation = new WC_Product_Variable($product_id);
        if($warehouse_id == 0)
        {
            $item_variations = $variation->get_available_variations();
        }else{
            $item_variations = $this->get_available_variations($variation);
        }
        
        
        $variant_products_with_attribute = array();
        $variation_attributes   = $variation->get_variation_attributes();

        $price_list = array();
        $variations = array();
        $qty_list = array();
        foreach($item_variations as $a_p)
        {
            // $variant_product = new WC_Product_Variable($a_p['variation_id']);

            $variant_product = wc_get_product($a_p['variation_id']);
            $a_p_price =  wc_get_price_including_tax($variant_product);

           
                
            if($warehouse_id > 0 && !$op_warehouse->is_instore($warehouse_id,$a_p['variation_id']))
            {
                continue;
            }
            //end update price

                $v_tmp = array(
                    'value_id' => $a_p['variation_id'],
                    'price' => $a_p_price
                );
                $variation_prices[] = $v_tmp;

                $variant_products_with_attribute[] = array(
                    'value_id' => $a_p['variation_id'],
                    'price' => $a_p_price,
                    'attributes' => $a_p['attributes']
                );
        }

        

        foreach($variation_attributes as $key => $variants)
        {
            $variants = $this->sortAttributeOptions($key,$variants);
            $label = $key;
            if(strpos($key,'pa_') === false)
            {
                $key = strtolower(esc_attr(sanitize_title($key)));
            }else{
                $key = urlencode($key);
            }
            
            $options = array();
            foreach($variants as $v)
            {
                $option_label = $v;
                $values = array();
                $values_price = array();
                foreach($variant_products_with_attribute as $vp)
                {
                    $attribute_key_1 = strtolower('attribute_'.$key);
                    
                    if(isset($vp['attributes'][$attribute_key_1]) && ($vp['attributes'][$attribute_key_1] == $v || $vp['attributes'][$attribute_key_1] == ''))
                    {
                        
                        if($vp['value_id'])
                        {

                            $taxonomy = $key;
                            $term = get_term_by('slug', $v, $taxonomy);
                            if($term)
                            {
                                $option_label = $term->name;
                                
                            }
                            $barcode = $core->getBarcode($vp['value_id']);
                            if($barcode)
                            {
                                $product_post = get_post($vp['value_id']);
                                $child_data = $this->get_product_formatted_data($product_post,$warehouse_id,true);
                                $values_price[$barcode] = $child_data['price_included_tax'] ? ($child_data['final_price'] + $child_data['tax_amount']) : $child_data['final_price'];
                                $values[] = $barcode;

                                $price_list[] =  $child_data['final_price'];
                                $qty_list[$barcode] = 1 * $child_data['qty'];
                            }

                        }

                    }
                }
                if(!empty($values))
                {
                    $values = array_unique($values);
                }
                
                $option_label = rawurldecode( $option_label);
                
                $option_tmp = array(
                    'title' => esc_html($option_label),
                    'slug' => $v,
                    'values' => $values,
                    'prices' => $values_price
                );
                $option_tmp = apply_filters('op_product_variation_attribute_option_data',$option_tmp);
                $options[] = $option_tmp;
            }

            $variant = array(
                'title' => wc_attribute_label( $label ),
                'slug' => $key,
                'options' => $options
            );
            $variations[] = $variant;
        }

        /*
        $variations = array(
            0 => array(
                'title' => 'Variation Color',
                'slug' => 'color',
                'options' => array(
                    0 => array(
                        'title' => 'Red',
                        'slug' => 'red',
                        'values' => array(100,101),
                        'prices' => array()
                    ),
                    1 => array(
                        'title' => 'Blue',
                        'slug' => 'blue',
                        'values' => array(102,103),
                        'prices' => array()
                    )
                )
            )
        );
        */
        
        $result = array(
            'variations' => $variations,
            'price_list' => $price_list,
            'qty_list' => $qty_list
        );
       
        return $result;
    }
    public function get_product_formatted_data($_product,$warehouse_id,$ignore_variable = false){
        global $op_warehouse;
        $setting_tax_class = $this->settings_api->get_option('pos_tax_class','openpos_general');
        $lang = $this->settings_api->get_option('pos_language','openpos_pos');
        $product_id = $_product->ID;
        $product = wc_get_product($product_id);
        $options = array();
        $bundles = array();
        $variations = array();
        if(!$product)
        {
            return false;
        }
        $image =  wc_placeholder_img_src() ;

        if ( has_post_thumbnail( $product->get_id() ) ) {
            $attachment_id =  get_post_thumbnail_id( $product->get_id() );
            $size = 'shop_thumbnail';
            $custom_width = $this->settings_api->get_option('pos_image_width','openpos_pos');
            $custom_height = $this->settings_api->get_option('pos_image_height','openpos_pos');
            if($custom_width && $custom_height)
            {
                $size = array($custom_width,$custom_height);
            }
            $image_attr = wp_get_attachment_image_src($attachment_id, $size);

            if(is_array($image_attr))
            {
                $image = $image_attr[0];
            }
        }

        $type = $product->get_type();
        $post_type = get_post_type($product->get_id());
        if($type == 'grouped')
        {
            return false;
        }

        $qty = $product->get_stock_quantity();
        $manage_stock = $product->get_manage_stock();
        $product_id = $product->get_id();

        if($warehouse_id > 0)
        {
            $manage_stock = true;
            $qty = 1 * $op_warehouse->get_qty($warehouse_id,$product_id);
        }

        $group = array();

        $price_display_html = $product->get_price_html();
        $v_price_display_html = '';
        if(!$ignore_variable)
        {
            switch ($type)
            {

                case 'grouped':
                    $group = $product->get_children();
                    break;
                case 'variable':
                    if($post_type == 'product')
                    {
                        $variations_result = $this->get_variations($product->get_id(),$warehouse_id);
                        $variations = $variations_result['variations'];
                        $price_list = $variations_result['price_list'];
                        $qty_list = $variations_result['qty_list'];
                        $qty = array_sum($qty_list);
                        if(!empty($price_list))
                        {
                            $price_list_min = min($price_list);
                            $price_list_max = max($price_list);


                            if($price_list_min != $price_list_max)
                            {
                                $price_list_min = wc_price($price_list_min,array('currency'=> '&nbsp;'));
                                $price_list_max = wc_price($price_list_max,array('currency'=> '&nbsp;'));
                                $v_price_display_html = implode(' - ',array($price_list_min,$price_list_max));
                            }else{
                                $v_price_display_html = wc_price($price_list_min,array('currency'=> '&nbsp;'));
                            }
                        }

                    }
                    break;
                default:
                    if($setting_tax_class != 'op_productax')
                    {
                        $price_display_html = wc_price(wc_get_price_excluding_tax($product));
                    }
                    break;
            }
        }
        if($price_display_html == null)
        {
            $price_display_html = $v_price_display_html;
        }

        
        

        $tax_amount = 0;

        $tmp_tax_rates = array();
        $tax_rate = array(
                'code' => 'openpos', // in percentage
                'rate' => 0, // in percentage
                'shipping' => 'no',
                'compound' => 'no',
                'rate_id' => 0,
                'label' => __('Tax on POS','openpos')
        );
        $final_price = $product->get_price();
        $price_without_tax = $product->get_price();
         
        if(!$final_price)
        {
            $final_price = 0;
        }

        $regular_price_without_tax = $product->get_regular_price();
        $regular_price_with_tax = $product->get_regular_price();
       
        $has_regular_price = false;
        if($regular_price_without_tax && $regular_price_without_tax > 0 && $price_without_tax != $regular_price_without_tax)
        {
            $has_regular_price = true;
        }
        $price_included_tax = false;
        if(wc_tax_enabled() )
        {

            if( $setting_tax_class != 'op_notax')
            {
                if($setting_tax_class == 'op_productax')
                {

                    $base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );

                    if($warehouse_id > 0)
                    {
                        $warehouse_details = $op_warehouse->getStorePickupAddress($warehouse_id);
                        if($warehouse_details['country'] || $warehouse_details['state'] || $warehouse_details['postcode'] || $warehouse_details['city'] )
                        {
                            $base_tax_rates = $this->getLocationTaxRates($product->get_tax_class( 'unfiltered' ),$warehouse_details);
                        }
                    }
                    $tax_rates = $this->getTaxRates( $product->get_tax_class() );

                    
                    
                   

                    if(!empty($base_tax_rates))
                    {
                        $tax_rates = $base_tax_rates;
                    }else{
                        $keys = array_keys($tax_rates);
                        $rate_id = max($keys);
                        $rate = $tax_rates[$rate_id];

                        $tax_rates = array($rate );
                    }
                     
                    if(!empty($tax_rates))
                    {
                        $tax_amount = 0;
                        $regular_tax_amount = 0;
                        foreach($tax_rates as $rate_id => $rate)
                        {
                            $tax_rate = array(
                                    'code' => 'openpos', // in percentage
                                    'rate' => 0, // in percentage
                                    'shipping' => 'no',
                                    'compound' => 'no',
                                    'rate_id' => 0,
                                    'label' => __('Tax on POS','openpos')
                            );
                            
                            $tax_amount += array_sum(@WC_Tax::calc_tax( $final_price, array($rate_id => $rate), wc_prices_include_tax() ));
                            $regular_tax_amount += array_sum(@WC_Tax::calc_tax( $regular_price_without_tax, array($rate_id => $rate), wc_prices_include_tax() ));

                            $tax_rate['code'] = $product->get_tax_class() ? $product->get_tax_class().'_'.$rate_id : 'standard_'.$rate_id;
                            $tax_rate['rate_id'] = $rate_id;
                            if($rate['label'])
                            {
                                $tax_rate['label'] = $rate['label'];
                            }
                            if(isset($rate['shipping']))
                            {
                                $tax_rate['shipping'] = $rate['shipping'];
                            }
                            if(isset($rate['compound']))
                            {
                                $tax_rate['compound'] = $rate['compound'];
                            }
                            if(isset($rate['rate']))
                            {
                                $tax_rate['rate'] = $rate['rate'];
                            }

                            $tmp_tax_rates[] = $tax_rate;
                        }
                        if($has_regular_price)
                        {
                            $regular_price_with_tax += $regular_tax_amount;
                        }
                    }

                    $price_included_tax = wc_prices_include_tax();
                    if($price_included_tax)
                    {
                        $tax_amount = wc_round_tax_total($tax_amount);
                        $price_without_tax = $final_price - $tax_amount;

                        if($has_regular_price)
                        {
                            $regular_price_with_tax = $regular_price_without_tax;
                            $regular_price_without_tax -= $regular_tax_amount;
                        }
                    }
                }else{


                    $tax_rates = $this->getTaxRates( $setting_tax_class );
                    $price_without_tax = wc_get_price_excluding_tax($product);
                    if(!empty($tax_rates))
                    {
                        $keys = array_keys($tax_rates);
                        $rate_id = max($keys);

                        $setting_tax_rate_id = $this->settings_api->get_option('pos_tax_rate_id','openpos_general');
                        if($setting_tax_rate_id)
                        {
                            $rate_id = $setting_tax_rate_id;
                        }
                        $rate = $tax_rates[$rate_id];

                        $tax_amount = array_sum(@WC_Tax::calc_tax( $price_without_tax, array($rate_id => $rate), false));

                        if($has_regular_price)
                        {
                            $regular_tax_amount = array_sum(@WC_Tax::calc_tax( $regular_price_without_tax, array($rate_id => $rate), false));
                            $regular_price_with_tax += $regular_tax_amount;
                        }
                        $tax_rate['code'] = $setting_tax_class ? $setting_tax_class.'_'.$rate_id : 'standard'.'_'.$rate_id;
                        $tax_rate['rate_id'] = $rate_id;
                        if($rate['label'])
                        {
                            $tax_rate['label'] = $rate['label'];
                        }
                        if(isset($rate['shipping']))
                        {
                            $tax_rate['shipping'] = $rate['shipping'];
                        }
                        if(isset($rate['compound']))
                        {
                            $tax_rate['compound'] = $rate['compound'];
                        }
                        if(isset($rate['rate']))
                        {
                            $tax_rate['rate'] = $rate['rate'];
                        }
                        if($setting_tax_class == 'op_productax')
                        {
                            $price_display_html = wc_price($price_without_tax + $tax_amount);
                        }else{
                            $price_display_html = wc_price($price_without_tax );
                        }

                    }
                    // custom tax
                    $tmp_tax_rates[] = $tax_rate;
                }
            }
        }
       


        $display_pos = true;
        if(get_post_type($product->get_id()) == 'product_variation')
        {
            $display_pos = false;
        }

        $categories = $this->get_product_categories($product->get_id());
        if(!$categories)
        {
            $categories = array();
        }


        $show_out_of_stock_setting = $this->settings_api->get_option('pos_display_outofstock','openpos_pos');
        $stock_status = $product->get_stock_status();
        if($display_pos && $show_out_of_stock_setting != 'yes' && $manage_stock)
        {
            if($qty <= 0 )
            {
                $display_pos = false;
            }

        }
        if($price_display_html == 'null' || $price_display_html == null)
        {
            $price_display_html = ' ';
        }
        if($w_pricing = $this->is_weight_base_pricing($product->get_id()))
        {
            $options[] = array(
                'label' => __("Weight",'openpos'),
                'option_id' => 'op_weight',
                'type' => 'price_base_input',
                'require' => true,
                'options' => array(),
                'cost' => $w_pricing
            );
        }
        if(!$price_without_tax)
        {
            $price_without_tax = 0;
        }
        $price = 1 * $price_without_tax;
        $price_incl_tax = 1 * $price_without_tax + 1 * $tax_amount;
        if($has_regular_price)
        {
            $price = 1 * $regular_price_without_tax;
            $price_incl_tax = 1 * $regular_price_with_tax;
        }
        $final_price = 1 * $price_without_tax;

        $tmp = array(
            'name' => $product->get_name(),
            'id' => $product->get_id(),
            'parent_id' => $product->get_id(),
            'sku' => $product->get_sku(),
            'qty' => $qty,
            'manage_stock' => $manage_stock,
            'stock_status' => $stock_status,
            'barcode' => strtolower(trim($this->_core->getBarcode($product->get_id()))),
            'image' => $image,
            'price' => $price,
            'price_incl_tax' => $price_incl_tax,
            'final_price' => $final_price,
            'special_price' => $product->get_sale_price() ? 1 *$product->get_sale_price() : $product->get_sale_price(),
            'regular_price' =>  $product->get_regular_price() ? 1 * $product->get_regular_price() : $product->get_regular_price(),
            'sale_from' => $product->get_date_on_sale_from(),
            'sale_to' => $product->get_date_on_sale_to(),
            'status' => $product->get_status(),
            'categories' => array_unique($categories),//$product->get_category_ids(),
            'tax' => $tmp_tax_rates,
            'tax_amount' => 1 * $tax_amount,
            'price_included_tax' => 1 * $price_included_tax,
            'group_items' => $group,
            'variations' => $variations,
            'options' => $options,
            'bundles' => $bundles,
            'display_special_price' => false,
            'allow_change_price' => false,
            'price_display_html' => $price_display_html,
            'display' => $display_pos
        );
        

        if($lang == 'vi')
        {
            $tmp['search_keyword'] = $this->custom_vnsearch_slug($tmp['name']);
        }

        if($this->settings_api->get_option('pos_change_price','openpos_pos') == 'yes')
        {
            $tmp['allow_change_price'] = true;
        }
        $product_data = apply_filters('op_product_data',$tmp,$_product);
        
        return $product_data;

    }
    public function getTaxRates($tax_class){
        global $wpdb;
        $criteria = array();
        $criteria[] = $wpdb->prepare( 'tax_rate_class = %s', sanitize_title( $tax_class ) );
        $found_rates = $wpdb->get_results( "
			SELECT tax_rates.*
			FROM {$wpdb->prefix}woocommerce_tax_rates as tax_rates
			WHERE 1=1 AND " . implode( ' AND ', $criteria ) . "
			GROUP BY tax_rates.tax_rate_id
			ORDER BY tax_rates.tax_rate_priority
		");

        $matched_tax_rates = array();

        foreach ( $found_rates as $found_rate ) {

            $matched_tax_rates[ $found_rate->tax_rate_id ] = array(
                'rate'     => (float) $found_rate->tax_rate,
                'label'    => $found_rate->tax_rate_name,
                'shipping' => $found_rate->tax_rate_shipping ? 'yes' : 'no',
                'compound' => $found_rate->tax_rate_compound ? 'yes' : 'no',
            );
        }
        return $matched_tax_rates;
    }
    public function getLocationTaxRates($tax_class,$location = array()){
  
        if(isset($location['country']) && $location['country'])
        {
            $calculate_tax_for['country'] =$location['country'];
        }
        if(isset($location['state']) && $location['state'])
        {
            $calculate_tax_for['state'] = $location['state'];
        }
        if(isset($location['city']) && $location['city'])
        {
            $calculate_tax_for['city'] = $location['city'];
        }
        if(isset($location['postcode']) && $location['postcode'])
        {
            $calculate_tax_for['postcode'] = $location['postcode'];
        }

        $calculate_tax_for['tax_class'] = $tax_class;
        $found_rates = WC_Tax::find_rates( $calculate_tax_for );
       
        return  $found_rates;
    }
    public function stripe_charge($amount,$source){
        global $OPENPOS_SETTING;
        $stripe_secret_key = $OPENPOS_SETTING->get_option('stripe_secret_key','openpos_payment');
        if($stripe_secret_key)
        {
            \Stripe\Stripe::setApiKey($stripe_secret_key);
            $currency = get_woocommerce_currency();
            $charge = \Stripe\Charge::create(['amount' => $amount, 'currency' => strtolower($currency), 'source' => $source]);
            return $charge->__toArray(true);
        }else{
            return array();
        }
    }

    public function stripe_refund($charge_id){
        global $OPENPOS_SETTING;
        $stripe_secret_key = $OPENPOS_SETTING->get_option('stripe_secret_key','openpos_payment');
        if($stripe_secret_key)
        {
            \Stripe\Stripe::setApiKey($stripe_secret_key);

            $refund = \Stripe\Refund::create([
                'charge' => $charge_id,
            ]);
            return $refund->__toArray(true);
        }else{
            return array();
        }
    }

    public function get_pos_categories(){
        global $OPENPOS_SETTING;
        $result = array();
        $category_ids = $OPENPOS_SETTING->get_option('pos_categories','openpos_pos');

        if(is_array($category_ids))
        {

            foreach($category_ids as $cat_id)
            {
                $term = get_term_by( 'id', $cat_id, 'product_cat', 'ARRAY_A' );
                if($term && !empty($term))
                {
                    $parent_id =  $term['parent'];
                    if(!in_array($parent_id,$category_ids))
                    {
                        $parent_id = 0;
                    }
                    $tmp  = array(
                        'id' => $cat_id,
                        'name' => $term['name'],
                        'image' => OPENPOS_URL.'/assets/images/category_placehoder.png',
                        'description' => '',
                        'parent_id' => $parent_id,
                        'child' => array()
                    );

                    $thumbnail_id = get_term_meta( $cat_id, 'thumbnail_id', true );
                    $image = wp_get_attachment_url( $thumbnail_id );
                    if ( $image ) {
                         $tmp['image'] = $image;
                    }

                    $result[] = apply_filters('op_category_data',$tmp,$category_ids);
                }
            }
        }
        if(!empty($result))
        {
            $tree = $this->buildTree($result);
        }else{
            $tree = [];
        }


        return apply_filters('op_category_tree_data',$tree,$result);
    }


    function buildTree($items) {
        $childs = array();
        foreach($items as &$item) $childs[$item['parent_id']][] = &$item;
        unset($item);
        foreach($items as &$item) if (isset($childs[$item['id']]))
            $item['child'] = $childs[$item['id']];
        return $childs[0];
    }

    public function get_product_categories($product_id){
        global $OPENPOS_SETTING;
        $product = wc_get_product($product_id);
        $categories = $product->get_category_ids();

        $category_ids = $OPENPOS_SETTING->get_option('pos_categories','openpos_pos');
        
        foreach($categories as $cat_id)
        {
            $tmp = $this->_cat_parent_ids($cat_id);
            $categories = array_merge($categories,$tmp);
        }
        $categories = array_unique($categories);
        if(!is_array($category_ids))
        {
            $cats = array();
        }else{
            $cats = array_intersect($category_ids,$categories);
        }

        if(!empty($cats))
        {
            $rest_cats = array_values($cats);
            return $rest_cats;
        }
        return $cats;
    }
    private function _cat_parent_ids($cat_id){
        $term = get_term_by( 'id', $cat_id, 'product_cat', 'ARRAY_A' );

        $result = array();
        if($term && $term['parent'] > 0 && $term['parent'] != $cat_id)
        {
            $result[] = $term['parent'];
            $tmp = $this->_cat_parent_ids($term['parent']);
            $result = array_merge($result,$tmp);
        }
        return $result;
    }

    public function get_shipping_method_by_code($code){
        $shipping_methods = WC()->shipping()->get_shipping_methods();
        $result = array(
                'code' => 'openpos',
                'title' => __('Custom Shipping','openpos')
        );
        foreach ($shipping_methods as $shipping_method)
        {
            $shipping_code = $shipping_method->id;
            if($code == $shipping_code)
            {
                $title = $shipping_method->method_title;
                if(!$title)
                {
                    $title = $code;
                }
                $result = array(
                    'code' =>$code,
                    'title' => $title
                );
            }

        }
        return $result;
    }

    public function woocommerce_available_payment_gateways($payment_methods){
        $order_id = absint( get_query_var( 'order-pay' ) );
        if($order_id > 0)
        {
            $pos_payment = get_post_meta($order_id,'pos_payment',true);
            if($pos_payment && is_array($pos_payment) && isset($pos_payment['code']))
            {
                $payment_code = $pos_payment['code'];
                if(isset($payment_methods[$payment_code]))
                {
                    $new_payment_method = array();
                    $new_payment_method[$payment_code] = $payment_methods[$payment_code];

                    return apply_filters( 'openpos_woocommerce_available_payment_gateways',$new_payment_method, $payment_methods );

                }

            }

        }
        return $payment_methods;
    }
    public function woocommerce_order_get_payment_method_title($value, $object){
        $payment_code = $object->get_payment_method();
        if($payment_code == 'pos_multi')
        {
            $methods = get_post_meta($object->get_id(), '_op_payment_methods', true);
            $method_values = array();
            if(!is_array($methods))
            {
                $methods = array();
            }
            foreach($methods as $code => $method)
            {
                $paid = isset($method['paid']) ? $method['paid'] : 0;
                if($paid > 0 && isset($method['name']))
                {
                    $return_paid = isset($method['return']) ? $method['return'] : 0;
                    $ref = isset($method['ref']) ? trim($method['ref']) : '';
                    if($return_paid > 0)
                    {
                        $paid = $paid - $return_paid;

                    }
                    if($ref)
                    {
                        $method_values[] = $method['name'].': '.strip_tags(wc_price($paid)).'('.$ref.')';
                    }else{
                        $method_values[] = $method['name'].': '.strip_tags(wc_price($paid));
                    }

                }

            }
            if(!empty($method_values))
            {
                return implode(', ',$method_values);
            }

        }
        return $value;
    }
    public function woocommerce_admin_order_data_after_shipping_address($order){
        $is_pos = get_post_meta($order->get_id(),'_op_order_source',true);

        if($is_pos == 'openpos' )
        {
            $_pos_shipping_phone = get_post_meta($order->get_id(),'_pos_shipping_phone',true);
            if($_pos_shipping_phone)
            {
                echo sprintf('<p><label>%s</label> : <span>%s</span></p>',__('Shipping Phone'),$_pos_shipping_phone);
            }
        }
    }
    // get formatted customer shipping address
    public function getCustomerShippingAddress($cutomer_id){
            $result = array();

            $customer = new WC_Customer($cutomer_id);
            $first_name = $customer->get_shipping_first_name();
            $last_name = $customer->get_shipping_last_name();
            if(!$first_name && !$last_name)
            {
                $first_name = $customer->get_first_name();
                $last_name = $customer->get_last_name();
            }
            $address_1 = $customer->get_shipping_address_1();
            $address_2 = $customer->get_shipping_address_2();
            $address = $address_1;
            if($address_1 && !$address)
            {
                $address = $address_1;

            }
            if($address_2 && !$address)
            {
                $address = $address_2;

            }
            if(!$address){
               // $address = $customer->get_address();
            }
            $phone = $customer->get_billing_phone();
            $address = array(
                'id' => 1,
                'title' => $address,
                'name' => implode(' ',array($first_name,$last_name)),
                'address' => $address,
                'address_2' => $customer->get_shipping_address_2(),
                'state' => $customer->get_shipping_state(),
                'postcode' => $customer->get_shipping_postcode(),
                'city' => $customer->get_shipping_city(),
                'country' => $customer->get_shipping_country(),
                'phone' => $phone,
            );
            $result[] = $address;
            return $result;
    }
    public function woocommerce_product_options_sku_after(){
        global $post;
        global $product_object;
        $barcode_field = $this->settings_api->get_option('barcode_meta_key','openpos_label');
        $allow = false;
        if(!$barcode_field)
        {
            $barcode_field = '_op_barcode';
        }
        if($barcode_field == '_op_barcode' )
        {
            $allow = true;
        }

        if($allow) {
            $value = '';
            $product_id = $product_object->get_id();
            if($product_id)
            {
                $value = get_post_meta($product_id,$barcode_field,true);

            }
            echo '<div class="options_group hide_if_variable hide_if_grouped">';
            woocommerce_wp_text_input(
                array(
                    'id' => '_op_barcode',
                    'value' => $value,
                    'label' => '<abbr title="' . esc_attr__('Stock Keeping Unit', 'woocommerce') . '">' . esc_html__('OP Barcode', 'openpos') . '</abbr>',
                    'desc_tip' => true,
                    'description' => __('Barcode refers to use in POS panel.', 'openpos'),
                )
            );
            echo '</div>';
        }
    }

    public function woocommerce_variation_options_dimensions_after($loop, $variation_data, $variation){

            $barcode_field = $this->settings_api->get_option('barcode_meta_key','openpos_label');
            $allow = false;
            if(!$barcode_field)
            {
                $barcode_field = '_op_barcode';
            }
            if($barcode_field == '_op_barcode' )
            {
                $allow = true;
            }

            if($allow)
            {

                $value = '';
                if($variation && isset($variation->ID))
                {
                   $variation_id = $variation->ID;
                   $value = get_post_meta($variation_id,$barcode_field,true);

                }

                woocommerce_wp_text_input(
                    array(
                        'id'                => "_op_barcode{$loop}",
                        'name'              => "_op_barcode[{$loop}]",
                        'label'       => '<abbr title="' . esc_attr__( 'POS Barcode', 'openpos' ) . '">' . esc_html__( 'OP Barcode', 'openpos' ) . '</abbr>',
                        'desc_tip'    => true,
                        'value' => $value,
                        'description' => __( 'Barcode refers to use in POS panel.', 'openpos' ),
                        'wrapper_class' => 'form-row form-row-full',
                    )
                );
            }

    }
    public function woocommerce_save_product_variation($variation_id, $i){
        global $op_warehouse;
        $barcode = isset( $_POST['_op_barcode'][ $i ] ) ? sanitize_text_field($_POST['_op_barcode'][ $i ]) : '';
        $_op_cost_price = isset( $_POST['_op_cost_price'][ $i ] ) ? sanitize_text_field($_POST['_op_cost_price'][ $i ]) : '';

        update_post_meta($variation_id,'_op_barcode',$barcode);
        update_post_meta($variation_id,'_op_cost_price',$_op_cost_price);
        $op_warehouse_stock = isset( $_POST['_op_stock']) ? $_POST['_op_stock'] : array();
        foreach($op_warehouse_stock as $warehouse_id => $qty_varation)
        {
            if(is_numeric($qty_varation[ $i ]))
            {
                $qty = isset($qty_varation[ $i ]) ? 1* $qty_varation[ $i ] : 0;
                $op_warehouse->set_qty($warehouse_id,$variation_id, 1*$qty );
            }else{
                $op_warehouse->remove_instore($warehouse_id,$variation_id);
            }
            
        }

    }
    public function woocommerce_admin_process_product_object($product){
        global $op_warehouse;
        $barcode = isset( $_POST['_op_barcode'] ) ? wc_clean( wp_unslash( $_POST['_op_barcode'] ) ) : '';
        $_op_cost_price = isset( $_POST['_op_cost_price'] ) ? wc_clean( wp_unslash( $_POST['_op_cost_price'] ) ) : '';
        $_op_weight_base_pricing = isset( $_POST['_op_weight_base_pricing'] ) ? wc_clean( wp_unslash( $_POST['_op_weight_base_pricing'] ) ) : 'no';


        $product_id = $product->get_id();
        $product_type = empty( $_POST['product-type'] ) ? WC_Product_Factory::get_product_type( $product_id ) : sanitize_title( wp_unslash( $_POST['product-type'] ) );
        if($product_type == 'variable')
        {

            $barcode = '';
        }
        update_post_meta($product_id,'_op_barcode',$barcode);
        update_post_meta($product_id,'_op_cost_price',$_op_cost_price);
        update_post_meta($product_id,'_op_weight_base_pricing',$_op_weight_base_pricing);

        if($product_type != 'variable')
        {
            $op_warehouse_stock = isset( $_POST['_op_stock']) ? $_POST['_op_stock'] : array();
            if(is_array($op_warehouse_stock) && !empty($op_warehouse_stock))
            {
                foreach($op_warehouse_stock as $warehouse_id => $qty)
                {
                    if(is_numeric($qty))
                    {
                        $op_warehouse->set_qty($warehouse_id,$product_id, 1*$qty );
                    }else{
                        $op_warehouse->remove_instore($warehouse_id,$product_id);
                    }
                    
                }
            }

        }


    }

    public function filter_orders_by_source(){
        global $typenow;
        if ( 'shop_order' === $typenow ) {
            $current = isset($_GET['_op_order_source']) ? esc_attr($_GET['_op_order_source']) : '';
             ?>
                <select name="_op_order_source" id="dropdown_order_source">
                    <option value="">
                        <?php esc_html_e( 'Filter by Source', 'openpos' ); ?>
                    </option>
                    <option <?php echo ($current == 'online') ? 'selected':''; ?> value="online"><?php esc_html_e( 'Online Order', 'openpos' ); ?></option>
                    <option <?php echo ($current == 'pos') ? 'selected':''; ?> value="pos"><?php esc_html_e( ' POS Orders', 'openpos' ); ?></option>

                </select>
            <?php
        }
    }
    public function add_order_filterable_where($where, $wp_query){
        global $typenow, $wpdb;

        if ( 'shop_order' === $typenow && isset( $_GET['_op_order_source'] ) && ! empty( $_GET['_op_order_source'] ) ) {
            // Main WHERE query part
            $source = isset($_GET['_op_order_source']) ? esc_attr($_GET['_op_order_source']) : '';
            if($source == 'online')
            {
                $where .= " AND $wpdb->postmeta.meta_value <> 'openpos'";
                //$where .= $wpdb->prepare( " AND woi.order_item_type='coupon' AND woi.order_item_name='%s'", wc_clean( $_GET['_coupons_used'] ) );
            }else{
                $where .= " AND $wpdb->postmeta.meta_value = 'openpos'";
            }

        }
        return $where;
    }
    public function filter_request_query($query){
        global $typenow, $wpdb;
        if ( 'shop_order' === $typenow && isset( $_GET['_op_order_source'] ) && ! empty( $_GET['_op_order_source'] ) ) {
            $source = $_GET['_op_order_source'];
            $meta_query = $query->meta_query;

            if($source == 'online')
            {
                $meta_arr = array(
                    'field' => '_op_order_source',
                    'compare' => 'NOT EXISTS'
                );
                $query->query_vars['meta_key'] = $meta_arr['field'];
                $query->query_vars['meta_compare'] = $meta_arr['compare'];
            }else{
                $meta_arr = array(
                    'field' => '_op_order_source',
                    'value' => 'openpos',
                    'compare' => '='
                );
                $query->query_vars['meta_key'] = $meta_arr['field'];
                $query->query_vars['meta_value'] = $meta_arr['value'];
                $query->query_vars['meta_compare'] = $meta_arr['compare'];
            }
        }

        return $query;
    }
    public function woocommerce_after_order_itemmeta( $item_id, $item, $product){

        $seller_id =  $item->get_meta( '_op_seller_id');
        $_op_local_id =  $item->get_meta( '_op_local_id');
        if($_op_local_id)
        {
            $has_seller = false;
            if($seller_id)
            {
                $user = get_user_by('id',$seller_id);
                if($user)
                {
                    echo '<p>'.__('Seller: ','openpos').'<strong>'.$user->display_name.'</strong></p>';
                    $has_seller = true;
                }

            }
            if(!$has_seller)
            {
                echo '<p>'.__('Sold By Shop Agent','openpos').'</p>';
            }
        }

    }
    public function getProductChanged($local_ver,$warehouse_id = 0){
        global $wpdb;
        global $op_warehouse;
        $meta_key = '_openpos_product_version_'.$warehouse_id;
        $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '".$meta_key."' AND meta_value >".($local_ver - 1)." ORDER BY meta_value ASC LIMIT 0,30", ARRAY_A);

        $result = array(
                'current_version' => $local_ver,
                'data' => array()
        );
        $db_version = get_option('_openpos_product_version_'.$warehouse_id,0);

        if(count($rows) == 0)
        {

            $result['current_version'] = $db_version;
        }
        foreach ($rows as $row)
        {
            $product_id = $row['post_id'];
            $product_verion = $row['meta_value'];
            $qty = $op_warehouse->get_qty($warehouse_id,$product_id);
            $result['current_version'] = $product_verion;

            $barcode = $product_id; //$this->_core->getBarcode($product_id);
            $result['data'][$barcode] = $qty;
        }
        return $result;
    }
    public function title_filter( $where, $wp_query )
    {
        global $wpdb;
        if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
            
            if( $post_status = $wp_query->get( 'post_status' ) )
            {
                if($post_status && !is_array($post_status))
                {
                    $post_status = array($post_status);
                }
                $villes = array_map(function($v) {
                    return "'" . esc_sql($v) . "'";
                }, $post_status);
                $villes = implode(',', $villes);
                $where .= ' OR (' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql(  $search_term ) . '%\' AND ' . $wpdb->posts . '.post_status IN ('.$villes.') ) ';
            }else{
                $where .= ' OR ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql(  $search_term ) . '%\'';
            }
            
        }
       
        return $where;
    }
    public function searchProductsByTerm($term,$limit=10){
        $args = array(
            'posts_per_page'   => $limit,
            'search_prod_title' => $term,
            'post_type'        => $this->_core->getPosPostType(),
            'post_status'      => 'publish',
            'suppress_filters' => false,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => '_sku',
                    'value'   =>  trim($term) ,
                    'compare' => 'LIKE'
                )

            ),
        );
        $query = new WP_Query($args);
        $posts = $query->get_posts();

        return $posts;

    }
    public function getDefaultContry(){
        $store_country_state = get_option( 'woocommerce_default_country', '' );
        $store_country = '';
        $store_country_tmp = explode(':',$store_country_state);
        if($store_country_state && count($store_country_tmp) > 1)
        {
            $store_country = $store_country_tmp[0];
        }
        return $store_country;
    }
    public function getDefaultState(){
        $store_country_state = get_option( 'woocommerce_default_country', '' );
        $store_state = '';
        $store_country_tmp = explode(':',$store_country_state);
        if($store_country_state && count($store_country_tmp) == 2)
        {
            $store_state = $store_country_tmp[1];
        }
        return $store_state;
    }
    public function getCustomerAdditionFields(){

        $address_2_field = array(
            'code' => 'address_2',
            'type' => 'text',
            'label' =>  __('Address 2','openpos'),
            'options' => array(),
            'placeholder' => __('Address 2','openpos'),
            'description' => '',
            'onchange_load' => false,
            'allow_shipping' => 'yes',
        );


        $postcode_field = array(
            'code' => 'postcode',
            'type' => 'text',
            'label' =>  __('PostCode / Zip','openpos'),
            'options' => array(),
            'placeholder' => __('PostCode / Zip','openpos'),
            'description' => '',
            'onchange_load' => false,
            'allow_shipping' => 'yes',
        );

        $city_field = array(
            'code' => 'city',
            'type' => 'text',
            'label' =>  __('City','openpos'),
            'options' => array(),
            'placeholder' => __('City','openpos'),
            'description' => '',
            'onchange_load' => false,
            'allow_shipping' => 'yes',
        );

        $state_field = array(
            'code' => 'state',
            'type' => 'text',
            'label' =>  __('State','openpos'),
            'options' => array(),
            'placeholder' => __('State','openpos'),
            'description' => '',
            'onchange_load' => false,
            'allow_shipping' => 'yes',
        );

        $store_country = $this->getDefaultContry();
        $store_state  = $this->getDefaultState();
        $states = array();
        if($store_country)
        {
            $tmp_states     = WC()->countries->get_states( $store_country );
            foreach($tmp_states as $key => $val)
            {
                $_tmp_state = array(
                        'value' => $key,
                        'label' => $val
                );
                $states[] = $_tmp_state;
            }
        }
        if(!empty($states))
        {
            $state_field = array(
                'code' => 'state',
                'type' => 'select',
                'label' =>  __('State','openpos'),
                'options' => $states,
                'placeholder' => __('State','openpos'),
                'description' => '',
                'onchange_load' => false,
                'allow_shipping' => 'yes',
                'default' => $store_state
            );
        }
        $countries_obj   = new WC_Countries();
        $countries   = $countries_obj->__get('countries');
        $country_options = array();
        foreach($countries as $key => $country)
        {
            $country_options[] = ['value' => $key,'label' => $country];
        }
        $select_contry = array(
            'code' => 'country',
            'type' => 'select',
            'label' =>  __('Country','openpos'),
            'options' => $country_options,
            'placeholder' => __('Choose Country','openpos'),
            'description' => '',
            'default' => $store_country,
            'allow_shipping' => 'yes',
            'onchange_load' => true
        );

        $fields = array(
                $address_2_field,
                $city_field,
                $postcode_field,
                $state_field,
                $select_contry
        );

        return apply_filters( 'op_customer_addition_fields',$fields );
    }
    public function woocommerce_email_recipient_customer_completed_order($recipient,$_order){

        $is_pos = get_post_meta($_order->get_id(),'_op_order_source',true);
        $_op_email_receipt = get_post_meta($_order->get_id(),'_op_email_receipt',true);
        $op_send_email_receipt = apply_filters('op_send_email_receipt',$_op_email_receipt,$_order,$recipient);
        
        if($is_pos == 'openpos' && $op_send_email_receipt == 'no')
        {
            $recipient = '';
        }
        return $recipient;
    }

    // format order to work with POS
    public function formatWooOrder($order_id){
        global $op_register;

        $order_key_format = array(
            'tax_amount',
            'sub_total',
            'sub_total_incl_tax',
            'shipping_cost',
            'shipping_tax',
            'refund_total',
            'grand_total',
            'final_items_discount_amount',
            'final_discount_amount',
            'discount_tax_amount',
            'discount_excl_tax',
            'discount_code_amount',
            'discount_code_tax_amount',
            'discount_code_excl_tax',
            'discount_amount',
            'discount_final_amount',
            'custom_tax_rate'
        );
        
        $pos_order = false;
        $order = wc_get_order($order_id);

        if($_pos_order_id = get_post_meta($order_id,'_pos_order_id',true))
        {
            // $order_number = $_pos_order_id.' ('.$order_id.')';
        }
        $grand_total = $order->get_total('ar');


        $billing_address = $order->get_address( 'billing' );

        $customer_data = array(
                'id' => $order->get_customer_id(),
                'group_id' => 0,
                'name' => implode(' ',array($billing_address['first_name'],$billing_address['last_name'])),
                'address' => $billing_address['address_1'],
                'firstname' => $billing_address['first_name'],
                'lastname' => $billing_address['last_name'],
                'email' => $billing_address['email'],
                'phone' => $billing_address['phone'],
        );

        $customer_data = array_merge($customer_data,$billing_address);

        $item_ids = $order->get_items();
        $order_status = $order->get_status();
        $payment_status = $order_status;
        if($order_status == 'processing' || $order_status == 'completed')
        {
            $payment_status = 'paid';
        }

        $items = array();
        $qty_allow_refund = true; // more logic
        $cart_discount_item = array();
        $final_items_discount_amount = 0;
        foreach($item_ids as $item_id)
        {

            $item = $this->formatOrderItem($order,$item_id);
            if($item && $item['item_type'] != 'cart_discount')
            {
                $items[] = $item;
                if(isset($item['final_discount_amount']))
                {
                    $final_items_discount_amount += $item['final_discount_amount'];
                }
            }
            if($item && $item['item_type'] == 'cart_discount')
            {
                $cart_discount_item  = $item;
            }
            
        }
       
        $user_id = $order->get_meta('_op_sale_by_person_id');
        $sale_person_name = '';
        if($user_id)
        {
            $userdata = get_user_by('id', $user_id);
            if($userdata)
            {
                $sale_person_name = $userdata->data->display_name;
            }

        }
        if(!$sale_person_name && !$_pos_order_id)
        {
            $sale_person_name = __('Done via website','openpos');
        }else{
            $pos_order = get_post_meta($order_id,'_op_order',true);
        }
        $sub_total = $order->get_subtotal();
        
        

        $shipping_cost = (float)$order->get_shipping_total();
        $shipping_tax = (float)$order->get_shipping_tax();

        $final_discount_amount = 0;
        $tax_totals = $order->get_tax_totals();
        $tax_amount = 0;
        foreach($tax_totals as $tax)
        {
            $tax_amount += $tax->amount;
        }
        $allow_pickup = $this->allowPickup($order_id);
        $allow_refund = $this->allowRefundOrder($order_id);
        if($grand_total <= 0)
        {
            $allow_refund = false;
        }
        $payments = array();
        if($_pos_order_id)
        {
            $payments = $order->get_meta('_op_payment_methods');
        }else{
            $method_title = $order->get_payment_method_title();
            $method_paid = $order->is_paid() ? $grand_total : 0;

            $payments[] = array(
                'name' => $method_title,
                'paid' => $method_paid,
                'return' => 0,
                'ref' => '',
            );
        }
        if($allow_refund && !$qty_allow_refund)
        {
            $allow_refund = false;
        }
        $order_status = $order->get_status();
        $setting_pos_continue_checkout_order_status = $this->settings_api->get_option('pos_continue_checkout_order_status','openpos_general');
        
        $allow_checkout = false;
        if($setting_pos_continue_checkout_order_status && is_array($setting_pos_continue_checkout_order_status)){
            foreach($setting_pos_continue_checkout_order_status as $setting_status)
            {
                $new_status = 'wc-' === substr( $setting_status, 0, 3 ) ? substr( $setting_status, 3 ) : $setting_status;
                if($new_status == $order_status)
                {
                    $allow_checkout = true;
                }
            }
        }
        if($payment_status != 'paid')
        {
            $allow_refund = false;
            $allow_pickup = false;
        }
        $continue_pay_url = $order->get_checkout_payment_url(false);

        $order_number = $order_id;
        if($tmp = get_post_meta( $order_id, '_op_wc_custom_order_number', true ))
        {
            if((int)$tmp)
            {
                $order_number = (int)$tmp;
            }
        }
        $discount_amount = 0;
        $discount_tax_amount = 0;
        $discount_excl_tax = 0;
        $discount_tax_details = array();
        
        if(!empty($cart_discount_item))
        {
            $discount_amount += $cart_discount_item['total_incl_tax'];
            $discount_tax_amount += $cart_discount_item['total_tax'];
            $discount_excl_tax += $cart_discount_item['total'];
            $final_discount_amount += $cart_discount_item['total_incl_tax'];
            $discount_tax_details = $cart_discount_item['tax_details'];
        }
        $source = '#'.$order->get_order_number();
        $source_type = 'online';
        $tmp_source_type = get_post_meta($order_id,'_op_order_source',true);
        $note = $order->get_customer_note();
        

        if($tmp_source_type == 'openpos')
        {
            $source_type = 'openpos';
            $cashdrawer_meta_key = $op_register->get_order_meta_key();
            $tmp_source = get_post_meta($order_id,$cashdrawer_meta_key,true);
            $cashdrawer = $op_register->get($tmp_source);
            if($cashdrawer && !empty($cashdrawer))
            {
                $source = $cashdrawer['name'];
            }
        }
        $created_at = wc_format_datetime($order->get_date_created());
        $created_at_time = $order->get_date_created()->getOffsetTimestamp() * 1000;
        if($pos_order && is_array($pos_order) && isset($pos_order['created_at']))
        {
            $created_at = $pos_order['created_at'];
            $created_at_time = $pos_order['created_at_time'];
        }
        $status = $order->get_status();
        $status_label = wc_get_order_status_name($status );
        $result = array(
            'id' => $order_id,
            'order_id' => $order_id,
            'system_order_id' => $order_id,
            'pos_order_id' => $_pos_order_id,
            'order_number' => $order_number,
            'order_number_format' => '#'.$order->get_order_number(),
            'title' => '',
            'items' => $items,
            'customer' => $customer_data,
            'sub_total' => $sub_total, //excl tax
            'sub_total_incl_tax' => $sub_total, // incl tax
            'tax_amount' => $tax_amount,
            'discount_amount' => (float)$discount_amount,
            'discount_type' => 'fixed',
            'discount_final_amount' => (float)$discount_amount,
            'final_items_discount_amount' => $final_items_discount_amount,
            'final_discount_amount' => (float)$final_discount_amount,
            'discount_tax_amount' => $discount_tax_amount,
            'discount_excl_tax' => $discount_excl_tax,
            'grand_total' => 1 * $grand_total,
            'discount_code' => '',
            'discount_codes' => array(),
            'discount_code_amount' => 0,
            'discount_code_tax_amount' => 0,
            'discount_code_excl_tax' => 0,
            'refund_total' => 0,
            'payment_method' => $payments, //ref , paid , return
            'shipping_cost' => $shipping_cost,
            'shipping_tax' => $shipping_tax,
            'shipping_rate_id' => '',
            'shipping_information' => array(),
            'sale_person' => 0,
            'sale_person_name' => $sale_person_name,
            'note' => $note,
            'created_at' => $created_at,
            'created_at_time' => $created_at_time,
            'state' => ($payment_status == 'paid') ? 'completed' : 'pending_payment',
            'online_payment' => false,
            'print_invoice' => true,
            'point_discount' => 0,
            'add_discount' => ($final_discount_amount > 0),
            'add_shipping' => false,
            'add_tax' => true,
            'custom_tax_rate' => '',
            'custom_tax_rates' => array(),
            'tax_details' => array(),
            'discount_tax_details' => $discount_tax_details,
            'source_type' => $source_type,
            'source' => $source,
            'available_shipping_methods' => array(),
            'mode' => '',
            'is_takeaway' => false,
            'checkout_url' => $continue_pay_url,
            'payment_status' => $payment_status,
            'status' =>  $status,
            'status_label' => $status_label,
            'allow_refund' => $allow_refund,
            'allow_pickup' => $allow_pickup,
            'allow_checkout' => $allow_checkout
        );

        if($pos_order)
        {
            if(isset($pos_order['gift_receipt']))
            {
                $result['gift_receipt'] = $pos_order['gift_receipt'];
            }
            if(isset($pos_order['add_shipping']))
            {
                $result['add_shipping'] = $pos_order['add_shipping'];
            }
        }
       
        foreach($result as $result_key => $result_value)
        {
            if(in_array($result_key,$order_key_format))
            {
                $new_key = $result_key.'_currency_formatted';
                $result[$new_key] = $this->stripePriceTag($result_value,wc_price($result_value));
            }
        }
        
        return apply_filters('op_get_online_order_data',$result);
    }

    public function formatOrderItem($order,$item_id){


        $order_item_key_format = array(
            'total_tax',
            'total',
            'total_incl_tax',
            'tax_amount',
            'refund_total',
            'price',
            'price_incl_tax',
            'final_price',
            'final_price_incl_tax',
            'final_discount_amount',
            'discount_amount'
        );


        $item = $order->get_item($item_id);

    
        $items_data = $item->get_data();
        $product_data = array();
        $product_id = isset($items_data['product_id']) ? $items_data['product_id'] : 0;
        $variation_id = isset($items_data['variation_id']) ? $items_data['variation_id'] : 0;
        if($variation_id > 0)
        {
            $product_id = $variation_id;
        }
        $_product = get_post($product_id);
        if($_product)
        {
            $product_data =  $this->get_product_formatted_data($_product,0);
        }
        $refund_qty = $order->get_qty_refunded_for_item( $items_data['id'] );
        if($refund_qty < 0)
        {
            $refund_qty = 0 - $refund_qty;
        }

        $refund_total = $order->get_total_refunded_for_item($items_data['id']);

        $items_data['options'] = array();
        //print_r($items_data);
        $subtotal = $items_data['subtotal'];
        $total = $items_data['total'];


        $total_tax = $items_data['total_tax'];
        $subtotal_tax = $items_data['subtotal_tax'];
        $tax_details_data = array();
        if ( wc_tax_enabled() ) {
            $order_taxes      = $order->get_taxes();

            
            foreach($order_taxes as $otax)
            {
                $o_tax_data = $otax->get_data();
                $tmp_tax = array(
                    'code' => $o_tax_data['rate_code'],
                    'compound' => $o_tax_data['compound'],
                    'label' => $o_tax_data['label'],
                    'rate' => $o_tax_data['rate_percent'],
                    'rate_id' => $o_tax_data['rate_id'],
                    'shipping' => false,
                    'total' => 0
                );
                $tax_details_data[] = $tmp_tax;
            }

        }
        
        
        $discount = ($subtotal   - $total) > 0 ? ($subtotal   - $total) : 0;
        $discount_tax = 0;
        if($discount)
        {
            $discount_tax = $items_data['subtotal_tax'] - $items_data['total_tax'];
        }
        

        $item_price = $items_data['quantity'] > 0 ? ($subtotal / $items_data['quantity']) : $subtotal;

        $item_tax_amount = ($total_tax != 0 && $items_data['quantity'] > 0 ) ? ($total_tax / $items_data['quantity']) : 0 ;
        $item_subtax_amount = ($subtotal_tax != 0 && $items_data['quantity'] > 0 ) ? ($subtotal_tax / $items_data['quantity']) : 0 ;

        //start meta
        $sub_name = '';

        $hidden_order_itemmeta = apply_filters(
            'woocommerce_hidden_order_itemmeta', array(
                '_qty',
                '_tax_class',
                '_product_id',
                '_variation_id',
                '_line_subtotal',
                '_line_subtotal_tax',
                '_line_total',
                '_line_tax',
                'method_id',
                'cost',
                '_reduced_stock',
                'op_item_details'
            )
        );
        $is_cart_discount = false;
        if ( $meta_data = $item->get_formatted_meta_data( '' ) ){
            foreach ( $meta_data as $meta_id => $meta ){
                if ( in_array( $meta->key, $hidden_order_itemmeta, true ) ) {
                    continue;
                }
                if($meta->key == '_pos_item_type' && $meta->value == 'cart_discount' )
                {
                    $is_cart_discount = true;
                }
                $tmp_sub = wp_kses_post( $meta->display_key ).': '.wp_kses_post( force_balance_tags( $meta->display_value ) );
                $sub_name .= '<li id="'.esc_attr($meta->key).'">'.$tmp_sub.'</li>';
            }
        }
        
        if($sub_name )
        {
            $sub_name = '<ul class="item-options-label">'.$sub_name.'</ul>';
        }

        $tax_details = array();
        $item_tax_data = $items_data['taxes'];
        foreach($item_tax_data['total'] as $id => $value)
        {
            foreach($tax_details_data as $t_value)
            {
                if($t_value['rate_id'] == $id)
                {
                    $tmp = $t_value;
                    if(!$value)
                    {
                        $value = 0;
                    }
                    $tmp['total'] = 1 * $value;
                    if($is_cart_discount)
                    {
                        $tmp['total'] = -1 * $value;
                    }
                    $tax_details[] = $tmp;
                }
            }
        }
        $item_type = '';
        if($is_cart_discount)
        {
            $item_type = 'cart_discount';
            $item_price *= -1 ; 
            $item_tax_amount *= -1 ; 
            $item_subtax_amount *= -1 ; 
            $discount *= -1 ; 
            $discount_tax *= -1 ; 
            $total_tax *= -1 ; 
            $items_data['total'] *= -1 ; 
            $items_data['total_tax'] *= -1 ; 
        }
        //end meta

        $item_formatted_data = array(
            'id' => $items_data['id'],
            'name' => $items_data['name'],
            'sub_name' => $sub_name,
            'dining' => '',
            'price' =>  $item_price,
            'price_incl_tax' =>  ($item_price + $item_subtax_amount), //
            'product_id' =>  $product_id,
            'final_price' =>  $item_price,
            'final_price_incl_tax' =>  ($item_price + $item_subtax_amount), //
            'options' => array(),
            'bundles' =>  array(),
            'variations' => array(),
            'discount_amount' =>  ($discount + $discount_tax),
            'discount_type' => 'fixed',
            'final_discount_amount' =>  $discount,
            'final_discount_amount_incl_tax' =>  ($discount + $discount_tax),
            'qty' =>  $items_data['quantity'],
            'refund_qty' =>  $refund_qty,
            'exchange_qty' =>  0,
            'tax_amount' =>  $item_subtax_amount,
            'refund_total' =>  $refund_total,
            'total_tax'=> $total_tax,
            'total'=>  $items_data['total'],
            'total_incl_tax'=>  ($items_data['total'] + $items_data['total_tax']), //
            'product'=> $product_data,
            'option_pass' =>  true,
            'option_total' =>  0,
            'bundle_total' =>  0,
            'note' => '',
            'parent_id' => 0,
            'seller_id' => 0,
            'seller_name' => '',
            'item_type'=> $item_type,
            'has_custom_discount'=> false,
            'disable_qty_change'=> true,
            'read_only'=> false,
            'promotion_added'=> false,
            'tax_details'=> $tax_details,
            'is_exchange'=> false,
        );
        //print_r($items_data);

        foreach($item_formatted_data as $result_key => $result_value)
        {
            if(in_array($result_key,$order_item_key_format))
            {
                $new_key = $result_key.'_currency_formatted';
                $item_formatted_data[$new_key] = $this->stripePriceTag($result_value,wc_price($result_value));
                
            }
        }
        
        return apply_filters('op_get_online_order_item_data',$item_formatted_data,$order);

    }


    public function allowRefundOrder($order_id){
        $allow_refund_duration = $this->settings_api->get_option('pos_allow_refund','openpos_general');
        if($allow_refund_duration == 'yes')
        {
            return true;
        }
        if($allow_refund_duration == 'no')
        {
            return false;
        }
        $refund_duration = $this->settings_api->get_option('pos_refund_duration','openpos_general');
        $post = get_post($order_id);
        $order = wc_get_order($order_id);
        $_pos_order_id = get_post_meta($order_id,'_pos_order_id',true);
        if(!$_pos_order_id)
        {
            return false;
        }

        $created = date_create($post->post_date)->getTimestamp();
        $today = time();
        $diff_time = $today - $created;
        $refund_duration = (float)$refund_duration;
        return ($diff_time < (86400 * $refund_duration));
    }
    public function allowPickup($order_id){
        $order = wc_get_order($order_id);
        $status = $order->get_status();
        $allow = false;
        if($status == 'processing')
        {
            $allow =  true;
        }
        return apply_filters('op_allow_order_pickup',$allow,$order_id);
    }
    public function inclTaxMode(){
        $pos_tax_class = $this->settings_api->get_option('pos_tax_class','openpos_general');

        return ( $pos_tax_class == 'op_productax'  && 'yes' === get_option( 'woocommerce_prices_include_tax' ) )  ? 'yes' : 'no';
    }
    public function getCustomItemTax($warehouse_id = null){
        global $op_warehouse;
        $result = array();
        $pos_custom_item = $this->settings_api->get_option('pos_allow_custom_item','openpos_pos');
        $pos_custom_tax_class = $this->settings_api->get_option('pos_custom_item_tax_class','openpos_pos');
        $pos_tax_class = $this->settings_api->get_option('pos_tax_class','openpos_general');
        if($pos_custom_item == 'yes' && $pos_tax_class != 'op_notax')
        {
            if($pos_tax_class != 'op_productax'){

                $pos_custom_tax_class = $this->settings_api->get_option('pos_tax_class','openpos_general');
               
            }

           
            if($pos_custom_tax_class != 'op_notax' )
            {
                $tax_rates = array();
                if($warehouse_id != null)
                {
                    $base_tax_rates = WC_Tax::get_base_tax_rates( $pos_custom_tax_class );
                    $warehouse_details = $op_warehouse->getStorePickupAddress($warehouse_id);
                    if($warehouse_details['country'] || $warehouse_details['state'] || $warehouse_details['postcode'] || $warehouse_details['city'] )
                    {
                        $base_tax_rates = $this->getLocationTaxRates($pos_custom_tax_class,$warehouse_details);
                    }
                    $tax_rates = $base_tax_rates;
                }
                if(!empty($tax_rates))
                {
                    foreach($tax_rates as $rate_id => $rate)
                    {
                            $tax_rate = array();
                            $tax_rate['code'] = $pos_custom_tax_class ? $pos_custom_tax_class.'_'.$rate_id : 'standard'.'_'.$rate_id;
                            $tax_rate['rate_id'] = $rate_id;
                            if($rate['label'])
                            {
                                $tax_rate['label'] = $rate['label'];
                            }
                            if(isset($rate['shipping']))
                            {
                                $tax_rate['shipping'] = $rate['shipping'];
                            }
                            if(isset($rate['compound']))
                            {
                                $tax_rate['compound'] = $rate['compound'];
                            }
                            if(isset($rate['rate']))
                            {
                                $tax_rate['rate'] = $rate['rate'];
                            }
                            $result[] = $tax_rate;
                    }
                }

            }
        }

        return apply_filters( 'op_custom_item_tax',$result );
    }
    public function getAllUserRoles(){
        global $wp_roles;
        $all_roles = $wp_roles->roles;
        $roles =  array_keys($all_roles);
        return apply_filters('op_customer_roles',$roles);
    }

    public function add_meta_boxes(){
        global $post;
        $source = get_post_meta($post->ID,'_op_order_source',true);
        if($source == 'openpos')
        {
            add_meta_box( 'look-openpos-order-setting',__('POS Information','openpos'), array($this,'add_order_boxes'), 'shop_order', 'side', 'default' );
        }

    }
    public function add_order_boxes(){
        global $post;
        $order = wc_get_order($post->ID);
        $pos_order = get_post_meta($post->ID,'_op_order',true);
        ?>

        <div class="openpos-order-meta-setting">
            <?php if($pos_order):  ?>
            <div style="width: 100%; float: left;">
                <a href="<?php echo admin_url('admin-ajax.php?action=print_receipt&id='.(int)$post->ID); ?>" target="_blank" style="background: transparent;padding: 0; float: right;border: none;"><image style="width: 28px;" src="<?php echo OPENPOS_URL.'/assets/images/print.png'; ?>" /></a>
            </div>
            <?php endif; ?>
            <?php
                $this->order_pos_payment($order);
            ?>

        </div>

        <?php
    }

    public function get_countries_and_states() {
        $countries = WC()->countries->get_countries();
        if ( ! $countries ) {
            return array();
        }
        $output = array();
        foreach ( $countries as $key => $value ) {
            $states = WC()->countries->get_states( $key );

            if ( $states ) {
                foreach ( $states as $state_key => $state_value ) {
                    $output[ $key . ':' . $state_key ] = $value . ' - ' . $state_value;
                }
            } else {
                $output[ $key ] = $value;
            }
        }
        return $output;
    }

    public function getListRestaurantArea(){
        $result = array(
                'cook' => array(
                        'label' => __( 'Kitchen Cook', 'woocommerce' ),
                        'description' => __( 'Display on Kitchen View', 'woocommerce' ),
                        'default' => 'yes' //yes or no
                ),
                'drink' => array(
                    'label' => __( 'Bar Drink', 'openpos' ),
                    'description' => __( 'Display on Bar View', 'woocommerce' )
                ),

        );
        return apply_filters('op_list_restaurant_area',$result);
    }

    public function product_type_options($options)
    {
        global $post;
        $openpos_type = $this->settings_api->get_option('openpos_type','openpos_pos');
        if($openpos_type == 'restaurant')
        {
            $type_options = $this->getListRestaurantArea();

            foreach($type_options as $akey => $aop)
            {
                $a_key = '_op_'.$akey;
                $default_value = isset($aop['default']) ? $aop['default'] : 'no';

                if($post && $post->ID)
                {
                    $_op_value = get_post_meta($post->ID,$a_key,true);

                    if($_op_value )
                    {
                        if( $_op_value == 'no')
                        {
                            $default_value = 'no';
                        }
                        if( $_op_value == 'yes')
                        {
                            $default_value = 'yes';
                        }
                    }

                }

                $options[$a_key] = array(
                    'id'            => $a_key,
                    'wrapper_class' => '',
                    'label'         => $aop['label'],
                    'description'   => $aop['description'],
                    'default'       => $default_value,
                );
            }
        }
        return $options;
    }
    public function woocommerce_new_product($product_id){

        $openpos_type = $this->settings_api->get_option('openpos_type','openpos_pos');
        if($openpos_type == 'restaurant' && $product_id)
        {

            $type_options = $this->getListRestaurantArea();
            foreach($type_options as $akey => $aop) {
                $a_key = '_op_' . $akey;
                $cook = 'no';
                if(isset($_REQUEST[$a_key]))
                {
                    $op_cook = esc_attr($_REQUEST[$a_key]);
                    if($op_cook == 'on')
                    {
                        $cook = 'yes';
                    }
                }
                update_post_meta($product_id,$a_key,$cook);
            }

        }
    }

    public function woocommerce_update_product($product_id){

        $openpos_type = $this->settings_api->get_option('openpos_type','openpos_pos');
        if($openpos_type == 'restaurant' && $product_id)
        {

            $type_options = $this->getListRestaurantArea();
            foreach($type_options as $akey => $aop) {
                $a_key = '_op_' . $akey;
                $cook = 'no';
                if(isset($_REQUEST[$a_key]))
                {
                    $op_cook = esc_attr($_REQUEST[$a_key]);
                    if($op_cook == 'on')
                    {
                        $cook = 'yes';
                    }
                }
                update_post_meta($product_id,$a_key,$cook);
            }
        }
    }
    public function check_product_kitchen_op_type($kitchen_type,$product_id){
        $result = false;
        $key = '_op_'.esc_attr($kitchen_type);
        $post = get_post($product_id);
        if($post)
        {
            if($post->post_parent && $post->post_parent > 0)
            {
                $product_id = $post->post_parent;
            }
            $_op_type = get_post_meta($product_id,$key,true);
            if($_op_type == 'yes')
            {
                $result = true;
            }
        }

        return $result;
    }
    public function sortAttributeOptions($attribute_code,$options){
        if(strpos($attribute_code,'pa_') !== false)
        {
            $result = array();

            $terms = get_terms( $attribute_code, array(
                'hide_empty' => false,
            ) );

            foreach($terms as $term)
            {
                if($term && is_object($term) && in_array($term->slug,$options))
                {
                    $result[] = $term->slug;
                }
            }
            return $result;
        }
        return $options;
    }
    public  function custom_vnsearch_slug($str) {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', ' ', $str);
        $str = str_replace('  ',' ',$str);
        return $str;
    }
    public function woocommerce_order_item_display_meta_key($display_key, $meta){
        if($meta->key && $meta->key == 'op_item_details')
        {
            $display_key = __('Item Details','openpos');
        }
        return $display_key;
    }
    public function get_cost_price($product_id){
        $price = false;
        $tmp_price = get_post_meta($product_id,'_op_cost_price',true);
        if($tmp_price !== false && $tmp_price != '')
        {
            $price = $tmp_price;
        }
        return $price;
    }
    public function is_weight_base_pricing($product_id){
        $result = false;
        $post = get_post($product_id);
        $setting_product_id = $product_id;
        if($post && $post->post_parent)
        {
            $setting_product_id = $post->post_parent;
        }
        $tmp_price = get_post_meta($setting_product_id,'_op_weight_base_pricing',true);

        if($tmp_price == 'yes')
        {
            $product = wc_get_product($product_id);
            $weight =  $product->get_weight('pos');

            if($weight && (float)$weight > 0)
            {
                $weight = (float)$weight;
                $price = $product->get_price();
                $result = ($price/$weight);
            }
        }
        return $result;
    }
    public function stripePriceTag($price,$price_html = ''){
        
	    $negative          = $price < 0;
        $args = array(
            'ex_tax_label'       => false,
            'currency'           => '',
            'decimal_separator'  => wc_get_price_decimal_separator(),
            'thousand_separator' => wc_get_price_thousand_separator(),
            'decimals'           => wc_get_price_decimals(),
            'price_format'       => get_woocommerce_price_format(),
        );
        $price             = apply_filters( 'op_raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
	    $price             = apply_filters( 'op_formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

        return $price;
    }
    public function woocommerce_loaded(){
        include_once OPENPOS_DIR . 'lib/data-stores/class-wc-product-data-store-cpt.php';
        // Removes the WooCommerce filter, that is validating the quantity to be an int
        remove_filter('woocommerce_stock_amount', 'intval');
        // Add a filter, that validates the quantity to be a float
        add_filter('woocommerce_stock_amount', 'floatval');
    }
    public function getStoreShippingMethods($warehouse_id,$setting){
        global $op_warehouse;
        $result = array();
        $shipping_methods = isset($setting['shipping_methods']) ? $setting['shipping_methods'] : array();
        
        $warehouse_details = $op_warehouse->getStorePickupAddress($warehouse_id);
        foreach($shipping_methods as $shipping_method)
        {
            
            $method_code = $shipping_method['code'];
            $tax_method_setting_code = 'shipping_tax_class_'.esc_attr($method_code);
            $tax_method_setting = $this->settings_api->get_option($tax_method_setting_code,'openpos_shipment');
            $shipping_taxes = $shipping_method['tax_details'];
       
            if($tax_method_setting != 'op_notax')
            {
                $shipping_taxes = array();
                if($warehouse_details['country'] || $warehouse_details['state'] || $warehouse_details['postcode'] || $warehouse_details['city'] )
                {
                    $base_tax_rates = $this->getLocationTaxRates($tax_method_setting,$warehouse_details);
                    foreach($base_tax_rates as $rate_id => $base_tax_rate)
                    {
                        if(isset($base_tax_rate['shipping']) && $base_tax_rate['shipping'] == 'yes')
                        {
                            $tax_rate_code = $tax_method_setting ? $tax_method_setting.'_'.$rate_id : 'standard'.'_'.$rate_id;
                            $shipping_tax = array(
                                    'code' => $tax_rate_code, // in percentage
                                    'rate' => $base_tax_rate['rate'], // in percentage
                                    'shipping' => 'yes',
                                    'compound' => 'no',
                                    'rate_id' => $rate_id,
                                    'label' => $base_tax_rate['label']
                            );
                            $shipping_taxes[] = $shipping_tax;
                        }
                    }
                    
                }
                //sample
            }
            $shipping_method['tax_details'] = $shipping_taxes;
            $result[] =  $shipping_method;
        }
        return $result;
    }

}
