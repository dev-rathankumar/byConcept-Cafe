<?php
// No dirrect access
if ( ! defined( 'MYCRED_WOOPLUS_VERSION' ) ) exit;

/**
 * Order Cancelled
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_part_woo_order_cancelled' ) ) :
	function mycred_part_woo_order_cancelled( $order_id ) {

		$order   = wc_get_order( $order_id );
		$coupons = $order->get_used_coupons();

		if ( ! empty( $coupons ) ) {
			foreach ( $coupons as $coupon_code ) {

				$coupon          = new WC_Coupon( $coupon_code );
				$partial_payment = mycred_get_partial_payment( $coupon->get_id() );

				if ( isset( $partial_payment->user_id ) ) {

					$mycred  = mycred( $partial_payment->ctype );

					$mycred->update_users_balance( $partial_payment->user_id, abs( $partial_payment->creds ), $partial_payment->ctype );
					mycred_delete_partial_payment( $partial_payment->id );

				}

			}
		}

	}
endif;
add_action( 'woocommerce_order_status_cancelled', 'mycred_part_woo_order_refund' );
add_action( 'woocommerce_order_status_refunded',  'mycred_part_woo_order_refund' );
add_action( 'woocommerce_order_status_failed',    'mycred_part_woo_order_refund' );

/**
 * Order Completed
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_part_woo_order_completed' ) ) :
	function mycred_part_woo_order_completed( $order_id ) {

		$order   = wc_get_order( $order_id );
		$payment = mycred_get_users_incomplete_partial_payment( $order->get_user_id() );

		if ( $payment !== false ) {

			global $wpdb, $mycred;

			$wpdb->update(
				$mycred->log_table,
				array( 'data' => $order_id ),
				array( 'id'   => $payment->id ),
				array( '%s' ),
				array( '%d' )
			);

		}

	}
endif;
add_action( 'woocommerce_order_status_completed', 'mycred_part_woo_order_completed' );
