<?php

/**
 * Woo Point Rewards by Order Total
 * Reward store purchases by paying a percentage of the order total
 * as points to the buyer.
 * @version 1.1
 */

 
if ( ! class_exists( 'mycred_woo_reward_product' ) ) :
class mycred_woo_reward_product {
		
		public function __construct() {
			add_action( 'woocommerce_single_product_summary', array( $this , 'woocommerce_before_add_to_cart_button') );

			add_action( 'woocommerce_order_status_completed',  array( $this , 'mycred_pro_reward_order_percentage' ));

			add_action( 'woocommerce_checkout_before_customer_details',  array( $this , 'woocommerce_review_order_before_order_total' ), 10);

			add_action( 'woocommerce_before_cart_table',  array( $this , 'woocommerce_review_order_before_order_total' ), 10);

			add_filter( 'woocommerce_get_item_data', array( $this ,  'woocommerce_get_item_data'), 10, 2 );	

		}
		
		public function woocommerce_get_item_data( $item_data, $cart_item ) {
			
			$mycred_rewards = get_post_meta( $cart_item['product_id'], 'mycred_reward', true ); 
			//print_r ($mycred_rewards);
			if($mycred_rewards){

				if ( (is_cart() && 'yes'==get_option('reward_cart_product_meta')) || (is_checkout() && 'yes'==get_option('reward_checkout_product_meta')) ) {
					foreach( $mycred_rewards as $mycred_reward_key => $mycred_reward_value ) {	
						$value = '<span class="reward_span">'. $mycred_reward_value .' ' .mycred_get_point_type_name($mycred_reward_key) .'</span>'	;
	
						$item_data[] = array(
							'key'     => '<span style="reward_span">Earn</span>',
							'value'   => __( $value, 'mycredpartwoo' ),
							'display' => '',
						);

					}
				} 

			}

			return $item_data;
		}
 
		public function woocommerce_review_order_before_order_total() {  

			do_action( 'woocommerce_set_cart_cookies',  true );

				
			$total_reward_point = array();
			$message = '';
			
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				// var_dump($cart_item);
				$mycred_rewards = get_post_meta( $cart_item['product_id'], 'mycred_reward', true ); 
				if($mycred_rewards){
					
					foreach( $mycred_rewards as $mycred_reward_key => $mycred_reward_value ){ 
						
						if (isset($total_reward_point[$mycred_reward_key])) {
							
							$total_reward_point[$mycred_reward_key]['total'] = $total_reward_point[$mycred_reward_key]['total'] + $mycred_reward_value * $cart_item['quantity'];
							
						}else{
							
							$total_reward_point[$mycred_reward_key] = array( 'name' => $mycred_reward_key ,'total' => $mycred_reward_value * $cart_item['quantity']);
						}
					}
				}	
			}

			$message .= "Earn ";
			$i = 1;
			$count = count($total_reward_point);
			
			foreach( $total_reward_point as $mycred_reward_key => $mycred_reward_value ){
			
				$mycred_point_type_name = mycred_get_point_type_name( $mycred_reward_key );
				$mycred = mycred( $mycred_reward_key );

				if(1==$count) {
					$message .= $mycred->format_creds( $mycred_reward_value['total'] ) .' '. $mycred_point_type_name;
				}else {
					if($i<$count) {
						$message .= $mycred->format_creds( $mycred_reward_value['total'] ) .' '. $mycred_point_type_name .', ';
					} else {
						$message .= ' and ' . $mycred->format_creds( $mycred_reward_value['total'] ) .' '. $mycred_point_type_name;
					}
				}

				$i++;
				
			}

			wc_clear_notices();

			$reward_points_global = get_option('reward_points_global', true);

			//wp_die(WC()->cart->get_subtotal());

			if ( 'yes'===$reward_points_global ) {
				/*** mufaddal start work from here */
				$type = get_option('mycred_point_type', true);
				$reward_points_global_type = get_option('reward_points_global_type', true);
				$exchange_rate = get_option('reward_points_exchange_rate', true);
				$reward_points_global_message = get_option('reward_points_global_message', true);
				$reward_points_global_type_val = get_option('reward_points_global_type_val', true);
				$reward_points_global_type_val = (float) $reward_points_global_type_val;
				$cost = WC()->cart->get_subtotal();
				//wp_die($type);

				if ('fixed'===$reward_points_global_type) {

					$reward = round($reward_points_global_type_val);

				}

				if ('percentage'===$reward_points_global_type) {

					$reward = $cost * ( $reward_points_global_type_val / 100 );
					$reward = round($reward);

				}

				if ('exchange'===$reward_points_global_type) {
					
					$reward = ( $cost/$exchange_rate );
					$reward = round($reward);

				}
				
				
				$message = str_replace("{points}", $reward, $reward_points_global_message);
				$message = str_replace("{type}", $type, $message);
				$message = str_replace("mycred_default", "Points", $message);
				if ($cost > 0) {
					wc_print_notice(  $message,  $notice_type = 'notice' ); 
				}				

			} else {

				if ( (is_cart() && 'yes'==get_option('reward_cart_product_total')) || (is_checkout() && 'yes'==get_option('reward_checkout_product_total')) ) {
					wc_print_notice(  $message,  $notice_type = 'notice' ); 
				}

			}

		}
		
		public function woocommerce_before_add_to_cart_button(){
			 
			if( get_option( 'reward_single_page_product' ) == 'yes' ) {
				
			$mycred_rewards = get_post_meta( get_the_ID(), 'mycred_reward', true );
				
				$i = 1;

				if(!empty($mycred_rewards)) {
					$count = count($mycred_rewards);
				}

				//echo 'count is '. $count.'<br>';
				if($mycred_rewards){
					echo '<p>'. esc_html__('Earn ', 'mycredpartwoo');
					foreach($mycred_rewards as $mycred_reward_key => $mycred_reward_value)
					{
						$mycred_point_type_name = mycred_get_point_type_name($mycred_reward_key);
						//echo apply_filters( 'single_page_product_reward_text', '<p>' . sprintf(esc_html__('Reward %s %s for purchase this product.', 'mycredpartwoo'), $mycred_reward_value,$mycred_point_type_name) . '</p>' , $mycred_reward_value,$mycred_point_type_name );
						
						if(1==$count) { 
							echo $mycred_reward_value .' '. $mycred_point_type_name;
						} else {
							if($i<$count) {
								echo $mycred_reward_value .' '. $mycred_point_type_name .', ';
							} else {
								echo ' and ' . $mycred_reward_value .' '. $mycred_point_type_name;
							}
						}

						$i++;
					}
					echo esc_html__(' Reward Points', 'mycredpartwoo') . '</p>';
				}
				
			}
		
			
		}
		
		public function mycred_pro_reward_order_percentage( $order_id ) {
			
			$reward_points_global = get_option('reward_points_global', true);

			if ( 'yes'===$reward_points_global ) {
				//wp_die('pls stop');
				$reward_points_global_type = get_option('reward_points_global_type', true);
				$reward_points_global_type_val = get_option('reward_points_global_type_val', true);
				$exchange_rate = get_option('reward_points_exchange_rate', true);
				$reward_points_global_message = get_option('reward_points_global_message', true);
				$type = get_option('mycred_point_type', true);
			}

			if ( ! function_exists( 'mycred' ) ) return;

			// Get Order
			$order   = new WC_Order( $order_id );
			$cost    = $order->get_subtotal();
			$user_id = get_post_meta($order_id, '_customer_user', true);
			$payment_method = get_post_meta( $order_id, '_payment_method', true );

			// Do not payout if order was paid using points
			if ( $payment_method == 'mycred' ) return;
			
			// Load myCRED
			$mycred = mycred();

			// Make sure user only gets points once per order
			if ( $mycred->has_entry( 'reward', $order_id, $user_id ) ) return;

			// percentage based point
			if ( isset($reward_points_global_type) && 'percentage'===$reward_points_global_type ) {
				
				// Reward example 25% in points.
				$points = (float) $reward_points_global_type_val;
				$reward  = $cost * ( $points / 100 );
				$reward = round($reward);

			} 

			// fixed point
			if ( isset($reward_points_global_type) && 'fixed'===$reward_points_global_type ) {
				
				// Reward example 25% in points.
				$points = (float) $reward_points_global_type_val;
				$reward  = round($points);

			}

			// exchange rate based points
			if ( isset($reward_points_global_type) && 'exchange'===$reward_points_global_type ) {
				
				// Reward example 25% in points.
				$points = (float) $exchange_rate;
				$reward  = round($cost/$points);
				//wp_die('rewards in exchange rate '. $reward);

			}

			// Add reward
			$mycred->add_creds('reward', $user_id, $reward, 'Reward for store purchase', $order_id, array( 'ref_type' => 'post' ), $type );

			if ( 'yes'===$reward_points_global ) {				
				add_filter('mycred_exclude_user', array($this, 'stop_points_for_single_product'), 10, 3);				
			}

		}

		public function stop_points_for_single_product( $false, $user_id, $obj) {
			return true;
		}
		
}

$mycred_woo_reward_product = new mycred_woo_reward_product();

endif;
?>