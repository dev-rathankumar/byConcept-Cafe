<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 10/19/18
 * Time: 17:58
 */
class OP_Grconnect {

    protected static $_domain = 'https://appsmav.com/';

    protected static $_callback_url = 'https://appsmav.com/gr/';

    protected static $_api_version = 'newapi/v2/';

    protected static $_curl_url = 'https://appsmav.com/handle_curl.php';

    protected static $_api_url = 'https://appsmav.com/customer/api_v1.php';

    protected static $_c_sdk_url = '//res.cloudinary.com/appsmav/raw/upload/v1534916585/live/gr/assets/js/gr-widget-sdk.js';


    public function init(){
        $this->grconnect();
    }

    //start gratisfaction integrate
    #  /plugins/gratisfaction-all-in-one-loyalty-contests-referral-program-for-woocommerce/grconnect.php
    public function grconnect(){
        if(is_plugin_active( 'gratisfaction-all-in-one-loyalty-contests-referral-program-for-woocommerce/grconnect.php' ))
        {
            if(!class_exists('GR_Connect') )
            {
                require_once ABSPATH.'wp-content/plugins/gratisfaction-all-in-one-loyalty-contests-referral-program-for-woocommerce/grconnect.php';
            }


            add_filter('op_customer_data',[$this,'grconnect_user_data'],10,1);

            add_action('plugins_loaded', array($this, 'woohook_init'),11);
            add_action('op_add_order_before', array($this, 'op_add_order_before'),10,2);
        }
    }
    public function woohook_init(){
        remove_action('woocommerce_order_status_changed', array('GR_Connect', 'send_status_init'));
        add_action('woocommerce_order_status_changed',[$this,'send_status_init'],10,1);
    }
    public function grconnect_user_data($user_data){

        if($email = $user_data['email'])
        {
            $_callback_url = 'https://appsmav.com/gr/';

            $_api_version = 'newapi/v2/';

            $urlApi = $_callback_url . $_api_version . 'getRedeemSettings';
            $shop_id = get_option('grconnect_shop_id');

            $grAppId = get_option('grconnect_appid');

            $grCampId = get_option('grconnect_secret');

            $params['admin_email'] = get_option('grconnect_admin_email');

            $params['id_site'] = $grAppId;

            $params['id_campaign'] = $grCampId;

            $params['app'] = 'WP';

            $params['id_shop'] = $shop_id;

            $params['payload'] = get_option('grconnect_payload');

            $params['status'] = 'Get';

            $params['plugin_version'] = "2.3.6";

            $params['user_email'] = $email;

            $response = wp_remote_post($urlApi, array('body' => $params, 'timeout' => 10));//timeout reduced from 180 to 10
            if(is_array($response) && !empty($response['body']))
            {
                $ret = json_decode($response['body'], true);

                if(isset($ret['redeem_point_per_dollar']) && isset($ret['gr_user_points']))
                {
                    $user_data['point'] = $ret['gr_user_points'];
                    $user_data['point_rate'] = $ret['redeem_point_per_dollar'];
                    $user_data['point_setting'] = $ret;

                }
            }
        }

        //$user_data['point_rules'] = array();

        return $user_data;
    }




    public function send_status_init($order_id){
        global $wpdb;

        $order = new WC_Order($order_id);

        $status = $order->get_status();

        $arrayAdd = array('processing', 'completed');

        $param['order_status'] = $status;

        $param['plugin_version'] = "2.3.6";



        if(in_array($status, $arrayAdd))

        {

            $urlApi = self::$_callback_url . self::$_api_version . 'addEntry';

            $param['status'] = 'Add';

        }

        else

        {

            $urlApi = self::$_callback_url . self::$_api_version . 'removeEntry';

            $param['status'] = 'Cancel';

        }



        // Set up the settings for this plugin

        $refunded = $order->get_total_refunded();

        $subtotal = $order->get_subtotal();

        $param['refunded'] = empty($refunded) ? 0 : $refunded;

        $param['subtotal'] = $subtotal;



        $sur_charges    =   $order->get_total_tax() + $order->get_total_shipping() + $order->get_shipping_tax();

        $param['gtotal'] = number_format((float) $order->get_total() - $sur_charges, wc_get_price_decimals(), '.', ''); //$order->get_total();

        $param['discount'] = $order->get_total_discount();

        $couponsArr = $order->get_used_coupons();



        if(!empty($couponsArr))

            $param['coupon'] = $couponsArr[0];



        $param['total'] = $param['gtotal'] - $param['refunded'];



        if($param['total'] <= 0)

            $param['total'] = $param['gtotal'];



        $curOrder = $order->get_order_currency();

        $curShop = get_option('woocommerce_currency', 'USD');



        if($curOrder != $curShop)

        {

            $prodArr = $order->get_items();

            $total = 0;



            foreach($prodArr as $prod)

            {

                $product = new WC_Product($prod['product_id']);

                $get_items_sql = $wpdb->prepare("select * from {$wpdb->prefix}postmeta WHERE meta_key = %s AND post_id = %d", '_price', $prod['product_id']);

                $line_item = $wpdb->get_row($get_items_sql);

                $price = $line_item->meta_value;



                if(empty($price))

                    $price = $product->price;



                $total += $price * $prod['qty'];

            }



            $curVal = $param['subtotal'] / $total;

            $param['total'] = $param['total'] / $curVal;

        }



        $user_email = '';

        $ordered_user = $order->get_user();



        if(!empty($ordered_user))

            $user_email = $ordered_user->get('user_email');



        if(empty($user_email))

            return;



        if(empty($_REQUEST['order_status']))

            $_REQUEST['order_status'] = '';



        $order_data = $order->get_data();



        $param['email'] = $user_email;

        $param['name'] = empty($order_data['billing']['first_name']) ? '' : $order_data['billing']['first_name'];

        $param['comment'] = 'Order Id ' . str_replace('wc-', '', $_REQUEST['order_status']) . ' - ' . $order_id . ' From ' . get_option('siteurl');

        $param['order'] = 0;

        $param['id_order'] = $order_id;

        $point_discount = get_post_meta($order_id,'_op_point_discount',true);
        if($point_discount && !empty($point_discount))
        {
            if($point_discount['rule_id'] != null && $point_discount['point'] > 0)
            {
                $param['redeem_points'] = $point_discount['point'];
            }
        }

        $post_order_id = get_post_meta($order_id,'_pos_order_id',true);
        if($post_order_id && $post_order_id > 0)
        {
            $status = $order->get_status();
            if($status == 'completed')
            {
                $this->callGrConnectApi($param, $urlApi);
            }
        }else{
            $this->callGrConnectApi($param, $urlApi);
        }

    }


    private function callGrConnectApi($param, $urlApi)

    {

        $shop_id = get_option('grconnect_shop_id', 0);



        if($shop_id == 0)

            return;



        $grAppIdArr = get_option('grconnect_appid');

        $grAppId = !empty($grAppIdArr) ? $grAppIdArr : '';

        $grCampIdArr = get_option('grconnect_secret');

        $grCampId = !empty($grCampIdArr) ? $grCampIdArr : '';

        $paramSalt = array();

        $paramSalt['id_site'] = $params['id_site'] = $grAppId;

        $paramSalt['points'] = $params['points'] = 0;

        $paramSalt['id_campaign'] = $params['id_campaign'] = $grCampId;

        $paramSalt['email'] = $params['email'] = $param['email'];



        $params['app'] = 'WP';

        $params['name'] = $param['name'];

        $params['comment'] = $param['comment'];

        $params["app_lang"] = str_replace('-', '_', get_bloginfo('language'));

        $allparam = implode('#WP#', $paramSalt);

        $params['salt'] = md5($allparam);

        $params['id_shop'] = $shop_id;

        $params['coupon'] = isset($param['coupon']) ? $param['coupon'] : '';

        $params['id_order'] = $param['id_order'];

        $params['amount'] = $param['total'];

        $params['subtotal'] = $param['subtotal'];

        $params['currency'] = get_option('woocommerce_currency', 'USD');

        $params['status'] = $param['status'];

        $params['order_status'] = !empty($param['order_status']) ? $param['order_status'] : '';

        $params['redeem_points'] = !empty($param['redeem_points']) ? $param['redeem_points'] : 0;

        $params['redeem_charges'] = !empty($param['redeem_charges']) ? $param['redeem_charges'] : 0;

        $params['payload'] = get_option('grconnect_payload', 0);



        if($grAppId != '' && $grCampId != '')

        {

            $httpObj = (new HttpRequestHandler)

                ->setPostData($params)

                ->exec($urlApi);

            $res = $httpObj->getResponse();



            if(!empty($res))

                $res = json_decode($res, true);



            if(!empty($res['error']))

                $msg = 'Unexpected error occur. Please check with administrator.';

        }

        else

        {

            echo 'Gr app id or secret is missing';

        }



        return;

    }

    public function op_add_order_before($order,$order_data){
        $point_discount = isset($order_data['point_discount']) ? $order_data['point_discount'] : array();

        if(!empty($point_discount) && $point_discount['rule_id'] != null && $point_discount['point'] > 0)
        {
            $title = __('Redeem '.$point_discount['point'].'  points for a '.$point_discount['point_money'].' discount','openpos');

            $discount_code = $title;

            $discount_amount = $point_discount['point_money'];

            if($discount_code)
            {
                $discount_amount = 0 - $discount_amount;

                $point_item = new WC_Order_Item_Product();
                $point_item->set_name($discount_code);
                $point_item->set_quantity(1);
                $point_item->set_product_id(0);
                $point_item->set_subtotal($discount_amount);
                $point_item->set_total($discount_amount);
                $order->add_item($point_item);

            }
        }
    }

    //end gratisfaction
}