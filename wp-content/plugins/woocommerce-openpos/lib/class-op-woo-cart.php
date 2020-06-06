<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 4/10/19
 * Time: 13:33
 */
if(!class_exists('OP_Woo_Cart'))
{
    class OP_Woo_Cart{
        private $settings_api;
        private $_core;
        private $_session;
        public function __construct()
        {
            $this->_session = new OP_Session();
            $this->settings_api = new Openpos_Settings();
            $this->_core = new Openpos_Core();
            //add_action('woocommerce_before_cart_table',array($this,'woocommerce_before_cart_table'));
        }
        public function woocommerce_before_cart_table(){

            $customer_id = WC()->session->get_customer_id();
            if($customer_id)
            {
                $cart_id = $this->getCartIdBySessionKey($customer_id);
                echo 'Your cart number <strong>#cart-'.$cart_id.'</strong>';
            }

        }
        public function getCartIdBySessionKey($session_key,$return_row = false){
            global $wpdb;

            $row = $wpdb->get_row(
                'SELECT * FROM '.$wpdb->prefix . 'woocommerce_sessions'." WHERE session_key = '".esc_attr($session_key)."'",ARRAY_A
            );
            if($row && !empty($row))
            {
                if(!$return_row)
                {
                    return $row['session_id'];
                }else{
                    return $row;
                }

            }
            return 0;
        }
        public function getCartBySessionId($cart_number){
            global $wpdb;
            $cart_id = str_replace('cart-','',$cart_number);
            $cart_id = (int)$cart_id;
            $cart_data = array();
            if($cart_id)
            {
                $row = $wpdb->get_row(
                    'SELECT * FROM '.$wpdb->prefix . 'woocommerce_sessions'." WHERE session_id = '".intval($cart_id)."'",ARRAY_A
                );
                if($row && !empty($row))
                {
                    $session_value = $row['session_value'];
                    $session_key = $row['session_key'];


                    $session_data = maybe_unserialize($row['session_value']);

                    if(isset($session_data['cart']))
                    {

                        $customer_id = 0;
                        if(isset($session_data['customer']))
                        {
                            $customer_data = maybe_unserialize($session_data['customer']);
                            if(isset($customer_data['id']))
                            {
                                $customer_id = $customer_data['id'];
                            }

                        }

                        $cart = maybe_unserialize($session_data['cart']);
                        $items = array();
                        foreach($cart as $item_key => $item)
                        {
                            $product_id = isset($item['variation_id']) && $item['variation_id'] > 0 ? $item['variation_id'] : $item['product_id'];

                            $tmp = array(
                                'product_id' => $product_id,
                                'product_name' => '',
                                'sub_name' => '',
                                'barcode' => trim($this->_core->getBarcode($product_id)),
                                'qty' => $item['quantity'],
                                'note' => ''
                            );
                            $items[] = $tmp;

                        }
                        if(!empty($items))
                        {
                            $cart_data['items'] = $items;
                            $cart_data['customer'] = array();
                            $cart_data['note'] = array();
                        }

                    }

                }
            }

            return $cart_data;
        }
        public function getShippingMethod($shipping_data,$cart_data){
            global $op_woo;
            do_action('op_get_shipping_method_before',$shipping_data,$cart_data);
            $address = isset($shipping_data['address']) ? $shipping_data['address'] : '';
            $address_2 = isset($shipping_data['address_2']) ? $shipping_data['address_2'] : '';
            $city = isset($shipping_data['city']) ? $shipping_data['city'] : '';
            $state = isset($shipping_data['state']) ? $shipping_data['state'] : '';
            $postcode =  isset($shipping_data['postcode']) ? $shipping_data['postcode'] : '';
            $country =  isset($shipping_data['country']) ? $shipping_data['country'] : '';

            $store_country = $op_woo->getDefaultContry();
            if(!$country)
            {
                $country = $store_country;
            }
            WC()->session->cleanup_sessions();
            $cart = new WC_Cart();

            $items = $cart_data['items'];
            foreach($items as $item)
            {
                $product_id = isset($item['product_id']) ? $item['product_id'] : 0;
                $product_qty = isset($item['qty']) ? $item['qty'] : 0;

                if($product_id && $product_qty)
                {
                    $_product = wc_get_product($product_id);

                    if($_product->get_type() == 'variation')
                    {
                        $parent_id = $_product->get_parent_id();
                        $variation_id = $product_id;
                    }else{
                        $parent_id = $product_id;
                        $variation_id = 0;
                    }
                    try{

                        $cart->add_to_cart($parent_id,$product_qty,$variation_id);

                    }catch (Exception $e)
                    {
                        print_r($e);die;
                    }

                }
            }


            $cart->calculate_totals();

            $customer = $cart->get_customer();
            $customer->set_shipping_state($state);
            $customer->set_shipping_address($address);
            $customer->set_shipping_postcode($postcode);
            $customer->set_shipping_address_2($address_2);
            $customer->set_shipping_country($country);
            $customer->set_shipping_city($city);

            $customer->set_billing_state($state);
            $customer->set_billing_address($address);
            $customer->set_billing_postcode($postcode);
            $customer->set_billing_address_2($address_2);
            $customer->set_billing_country($country);
            $customer->set_billing_city($city);

            $customer->set_calculated_shipping(false);
            $cart->calculate_shipping();
            $packages = WC()->shipping()->get_packages();
            $methods_rates = array();


            foreach($packages as $package)
            {
                $package_rates = $package['rates'];
                foreach($package_rates as $package_rate)
                {
                    $id = $package_rate->get_id();
                    $cost = $package_rate->get_cost();
                    $tax = $package_rate->get_shipping_tax();
                    $label = $package_rate->get_label();
                    $tmp = array(
                        'code' => $id,
                        'label' => $label,
                        'title' => sprintf('%s (%s : %s)',$label,__('Cost','openpos'),strip_tags(wc_price($cost+ $tax))),
                        'cost' => $cost,
                        'tax' => $tax,
                    );
                    $methods_rates[$id] = $tmp;
                }
            }
            do_action('op_get_shipping_method_after',$shipping_data,$cart_data);

            return apply_filters('op_shipping_method_data',array_values($methods_rates));
        }
        public function getShippingCost($shipping_data,$cart_data){
            global $op_woo;
            $address = isset($shipping_data['']) ? $shipping_data['address'] : '';
            $address_2 = isset($shipping_data['']) ? $shipping_data['address_2'] : '';
            $city = isset($shipping_data['']) ? $shipping_data['city'] : '';
            $state = isset($shipping_data['']) ? $shipping_data['state'] : '';
            $postcode =  isset($shipping_data['']) ? $shipping_data['postcode'] : '';
            $country =  isset($shipping_data['country']) ? $shipping_data['country'] : '';

            $store_country = $op_woo->getDefaultContry();
            if(!$country)
            {
                $country = $store_country;
            }
            $session = WC()->session;
            $cart = new WC_Cart();

            $items = $cart_data['items'];
            foreach($items as $item)
            {
                $product_id = isset($item['product_id']) ? $item['product_id'] : 0;
                $product_qty = isset($item['qty']) ? $item['qty'] : 0;
                if($product_id && $product_qty)
                {
                    $cart->add_to_cart($product_id,$product_qty);
                }
            }
            $cart->calculate_totals();
            $shipping_method = $shipping_data['shipping_method'];
            $customer = $cart->get_customer();
            $customer->set_shipping_state($state);
            $customer->set_shipping_address($address);
            $customer->set_shipping_postcode($postcode);
            $customer->set_shipping_address_2($address_2);
            $customer->set_shipping_country($country);
            $customer->set_shipping_city($city);
            $customer->set_calculated_shipping(false);
            $cart->calculate_shipping();
            $packages = WC()->shipping()->get_packages();
            $shipping_cost = array();
            $shipping_tax = array();
            $shipping_methods = array();
            foreach($packages as $package)
            {
                $package_rates = $package['rates'];
                foreach($package_rates as $package_rate)
                {

                    $method_id = $package_rate->method_id;
                    if($method_id == $shipping_method)
                    {
                        $shipping_methods[] = $package_rate;
                    }
                }
            }
            foreach($shipping_methods as $shipping_method)
            {
                $id = $shipping_method->get_id();
                $cost = $shipping_method->get_cost();
                $tax = $shipping_method->get_shipping_tax();
                $shipping_cost[$id] = $cost;
                $shipping_tax[$id] = $tax;
            }

            if(!empty($shipping_cost))
            {
                $cost = min($shipping_cost);
                $cost_rate_id = '';
                foreach($shipping_cost as $rate_id => $value)
                {
                    if($value == $cost)
                    {
                        $cost_rate_id = $rate_id;
                    }
                }

                $tax = isset( $shipping_tax[$cost_rate_id] ) ? $shipping_tax[$cost_rate_id] : 0;
                return array(
                    'cost' => $cost,
                    'tax' => $tax,
                    'rate_id' => $cost_rate_id
                );
            }
            return array();
        }
        public function getCartDiscount($cart_data){
            $result = array();
            $session = WC()->session;
            $cart = new WC_Cart();
            $customer_data = $cart_data['customer'];

            if(!empty($customer_data)) {

                if ($customer_data['id'] && $customer_data['id'] > 0) {
                    wp_set_current_user($customer_data['id']);
                }

            }
            $items = $cart_data['items'];
            foreach($items as $item)
            {
                $product_id = isset($item['product_id']) ? $item['product_id'] : 0;
                $product_qty = isset($item['qty']) ? $item['qty'] : 0;
                if($product_id && $product_qty)
                {
                    $cart->add_to_cart($product_id,$product_qty);
                }
            }

            $post_customer_data = array();
            if(!empty($customer_data))
            {
                $customer = $cart->get_customer();


                if(isset($customer_data['email']) && $customer_data['email'])
                {
                    $customer->set_email($customer_data['email']);
                    $post_customer_data['billing_email'] = $customer_data['email'];
                }
                if(isset($customer_data['firstname']) && $customer_data['firstname'])
                {
                    $customer->set_first_name($customer_data['firstname']);
                    $post_customer_data['billing_first_name'] = $customer_data['firstname'];
                }
                if(isset($customer_data['lastname']) && $customer_data['lastname'])
                {
                    $customer->set_last_name($customer_data['lastname']);
                    $post_customer_data['billing_last_name'] = $customer_data['lastname'];
                }
                if($customer_data['address'])
                {
                    $customer->set_address($customer_data['address']);
                    $post_customer_data['billing_address_1'] = $customer_data['address'];
                }

                if(isset($customer_data['address_2']) && $customer_data['address_2'])
                {
                    $customer->set_address_2($customer_data['address_2']);
                    $post_customer_data['billing_address_2'] = $customer_data['address_2'];
                }

                if(isset($customer_data['state']) && $customer_data['state'])
                {
                    $customer->set_state($customer_data['state']);
                    $post_customer_data['billing_state'] = $customer_data['state'];
                }

                if(isset($customer_data['city']) && $customer_data['city'])
                {
                    $customer->set_city($customer_data['city']);
                    $post_customer_data['billing_city'] = $customer_data['city'];
                }

                if(isset($customer_data['country']) && $customer_data['country'])
                {
                    $customer->set_country($customer_data['country']);
                    $post_customer_data['billing_country'] = $customer_data['country'];
                }

                if(isset($customer_data['postcode']) && $customer_data['postcode'])
                {
                    $customer->set_postcode($customer_data['postcode']);
                    $post_customer_data['billing_postcode'] = $customer_data['postcode'];
                }
                WC()->customer = $customer;

            }
            WC()->session->set('refresh_totals', true);
            $cart->calculate_totals();
            WC()->cart = $cart;

            $post_data = implode('&',$post_customer_data);
            $_POST['billing_email'] = $customer_data['email'];
            $_POST['post_data'] = $post_data;
            $_GET['wc-ajax'] = 'update_order_review';

            
            $cart->calculate_totals();
            
            $discount_amount = 0;
            $coupons  = WC()->cart->get_coupons();
            $discount_type = 'fixed';
            foreach($coupons as $coupon)
            {
                $discount_amount += $coupon->get_amount();
                $tmp_discount_type = $coupon->get_discount_type();
                if($tmp_discount_type == 'percent')
                {
                    $discount_type = 'percent';
                }
            }
            if($discount_amount)
            {
                $result = array(
                    'discount_amount' => $discount_amount,
                    'discount_type' => $discount_type // percent , fixed
                    
                );
            }

            wp_set_current_user(0);
            return $result;
        }
    }
}
