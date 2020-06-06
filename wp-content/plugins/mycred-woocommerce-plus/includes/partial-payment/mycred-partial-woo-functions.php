<?php
// No dirrect access
if ( ! defined( 'MYCRED_WOOPLUS_VERSION' ) ) exit;

/**
 * Get Plugin Settings
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_part_woo_settings' ) ) :
	function mycred_part_woo_settings() {

		global $mycred_partial_payment;

		if ( ! is_array( $mycred_partial_payment ) ) {

			$default = array(
				'position'      => 'after',
				'multiple'      => 'yes',
				'title'         => 'Partial Payment',
				'desc'          => 'Pay parts of your order using your points.',
				'button'        => 'Apply Discount',
				'min'           => 0,
				'max'           => 100,
				'step'          => 100,
				'default'       => 0,
				'point_type'    => MYCRED_DEFAULT_TYPE_KEY,
				'exchange'      => 1,
				'log'           => '',
				'log_refund'    => 'Partial payment',
				'refund_message' => 'Your partial payment of %cred_f% was refunded to your account.',
				'undo'          => 'yes',
				'rewards'       => 1,
				'before_tax'    => 'yes',
				'free_shipping' => 'no',
				'sale_items'    => 'no',
				'selecttype'    => 'input',
				'checkout_total' => 'no',
				'checkout_total_label' => 'Point Cost',
				'checkout_balance' => 'no',
				'checkout_balance_label' => 'Your Balance'
			);
			$saved   = get_option( 'mycred_partial_payments_woo', $default );

			return shortcode_atts( $default, $saved );

		}

		return $mycred_partial_payment;

	}
endif;

/**
 * Get Plugin Settings
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_part_woo_account_settings' ) ) :
	function mycred_part_woo_account_settings() {

		$default = array(
			'title'      => 'Points History',
			'desc'       => '',
			'slug'       => 'points',
			'point_type' => MYCRED_DEFAULT_TYPE_KEY,
			'number'     => 25,
			'nav'        => 0,
			'nav_size'   => 10,
			'show'       => ''
		);
		$saved   = get_option( 'mycred_partial_payments_account_woo', $default );

		return shortcode_atts( $default, $saved );

	}
endif;

/**
 * Partial Payment Possible?
 * Checks if partial payment is possible for a cart
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_partial_payment_possible' ) ) :
	function mycred_partial_payment_possible() {

		if ( ! is_user_logged_in() ) return false;

		global $mycred_partial_payment, $mycred_remove_partial_payment;

		$user_id  = get_current_user_id();

		$possible = apply_filters( 'mycred_woo_partial_payment', true );
		if ( $possible === false ) return false;

		// Fiscal check
		$mycred   = mycred( $mycred_partial_payment['point_type'] );
		if ( $mycred->exclude_user( $user_id ) )
			return false;

		$total    = WC()->cart->total;

		// If points can not be used to pay for taxes
		if ( $mycred_partial_payment['before_tax'] == 'no' ) {

			$taxes = WC()->cart->get_tax_totals();
			if ( ! empty( $taxes ) ) {
				foreach ( $taxes as $code => $tax )
					$total -= $tax->amount;
			}

		}

		// If points can not be used to pay for shipping
		if ( $mycred_partial_payment['free_shipping'] == 'no' )
			$total -= WC()->cart->shipping_total;

		$balance  = $mycred->get_users_balance( $user_id, $mycred_partial_payment['point_type'] );
		$min      = ( ( $mycred_partial_payment['min'] > 0 ) ? $mycred_partial_payment['min'] : $mycred->get_lowest_value() );

		if ( $total > 0 && $balance < $min )
			return false;

		$coupons  = WC()->cart->get_coupons();
		if ( $mycred_partial_payment['multiple'] === 'no' && ! empty( $coupons ) ) {

			$possible = true;
			foreach ( WC()->cart->applied_coupons as $code ) {

				$coupon          = new WC_Coupon( $code );
				$partial_payment = mycred_get_partial_payment( $coupon->get_id() );
				if ( isset( $partial_payment->user_id ) )
					$possible = false;

			}
			return $possible;

		}

		return true;

	}
endif;

/**
 * Get Partial Payment
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_partial_payment' ) ) :
	function mycred_get_partial_payment( $code = NULL ) {

		global $wpdb, $mycred;

		$payment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mycred->log_table} WHERE ref = 'partial_payment' AND ref_id = %d;", $code ) );
		if ( ! isset( $payment->user_id ) )
			$payment = false;

		return $payment;

	}
endif;

/**
 * Get Incomplete Payment
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_users_incomplete_partial_payment' ) ) :
	function mycred_get_users_incomplete_partial_payment( $user_id = NULL ) {

		global $wpdb, $mycred;

		$payment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mycred->log_table} WHERE ref = 'partial_payment' AND ref_id != 0 AND user_id = %d AND data = '' ORDER BY time DESC LIMIT 1;", $user_id ) );
		if ( ! isset( $payment->user_id ) )
			$payment = false;

		return $payment;

	}
endif;

/**
 * Delete Parial Payment
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_delete_partial_payment' ) ) :
	function mycred_delete_partial_payment( $payment_id = NULL ) {

		global $wpdb, $mycred;

		$wpdb->delete(
			$mycred->log_table,
			array( 'id' => $payment_id ),
			array( '%d' )
		);

	}
endif;

/**
 * Delete Parial Payment
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_part_woo_get_total' ) ) :
	function mycred_part_woo_get_total( $order_id = NULL ) {

		global $mycred_partial_payment;

		$cart  = WC()->cart;
		$total = $cart->total;

		// If points can not be used to pay for taxes
		if ( $mycred_partial_payment['before_tax'] == 'no' ) {

			$taxes = $cart->get_tax_totals();
			if ( ! empty( $taxes ) ) {
				foreach ( $taxes as $code => $tax )
					$total -= $tax->amount;
			}

		}

		// If points can not be used to pay for shipping
		if ( $mycred_partial_payment['free_shipping'] == 'no' )
			$total -= $cart->shipping_total;

		return apply_filters( 'mycred_woo_partial_payment_total', $total, $cart );

	}
endif;

/**
 * Partial Payment Title
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_partial_payment_title' ) ) :
	function mycred_partial_payment_title() {

		global $mycred_partial_payment;

		echo do_shortcode( wptexturize( $mycred_partial_payment['title'] ) );

	}
endif;

/**
 * Partial Payment Description
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_partial_payment_desc' ) ) :
	function mycred_partial_payment_desc() {

		global $mycred_partial_payment;

		echo do_shortcode( wptexturize( $mycred_partial_payment['desc'] ) );

	}
endif;

/**
 * Partial Payment Slider
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_partial_payment_selector' ) ) :
	function mycred_partial_payment_selector() {

		global $mycred_partial_payment;

		$user_id  = get_current_user_id();
		$settings = mycred_part_woo_settings();

		$mycred   = mycred( $mycred_partial_payment['point_type'] );
		if ( $mycred->exclude_user( $user_id ) ) return;

		$step     = ( $mycred_partial_payment['step'] != '' && $mycred_partial_payment['step'] > 0 ) ? $mycred->number( $mycred_partial_payment['step'] ) : false;

		$total    = mycred_part_woo_get_total();

		$balance  = $mycred->get_users_balance( $user_id );
		$max      = $mycred->number( $total / $mycred_partial_payment['exchange'] );
		if ( $balance < $max )
			$max = $balance;

		$min      = ( ( $mycred_partial_payment['min'] > 0 ) ? $mycred_partial_payment['min'] : 0 );
		//set max to percentage value in setting 
		$max      = ( ( $mycred_partial_payment['max'] < 100 ) ? ($max/100)*$mycred_partial_payment['max'] : $max );

?>
<style type="text/css">
#mycred-partial-payment-wrapper { margin-bottom: 24px; }
.uses-input #mycred-partial-payment-total, .uses-input #mycred-range-selector { margin-bottom: 12px; }
</style>
<div id="mycred-partial-payment-wrapper" class="uses-<?php echo $settings['selecttype']; ?>">
	<div id="mycred-partial-payment-total">
		<h2><?php echo $mycred->before; ?> <span><?php echo $min; ?></span> <?php echo $mycred->after; ?></h2>
		<p><?php echo wc_price( $min * $mycred_partial_payment['exchange'] ); ?> <?php _e( 'Discount', 'mycredpartwoo' ); ?></p>
	</div>
	<div id="mycred-range-selector">
		<input 
			type="<?php if ( $settings['selecttype'] == 'input' ) echo 'number'; else echo 'range'; ?>" 
			min="<?php echo $mycred->number( $min ); ?>" 
			max="<?php echo $max; ?>" 
			<?php if ( $settings['selecttype'] == 'input' ) echo 'class="input-text"'; else echo 'class="input-range"'; ?>
			<?php if ( $step !== false ) : ?>
			step="<?php echo esc_attr( $step ); ?>" 
			placeholder="<?php printf( __( 'Increments of %s', 'mycredpartwoo' ), $mycred->format_creds( $step ) ); ?>" 
			<?php endif; ?>
			value="<?php echo $mycred->number( $mycred_partial_payment['default'] ); ?>" 
			style="width:100%;" />
	</div>
	<div id="mycred-range-action">
		<button class="button button-primary btn btn-primary" type="button" disabled="disabled" id="mycred-apply-partial-payment"><?php echo esc_attr( $mycred_partial_payment['button'] ); ?></button>
	</div>
</div>
<?php

	}
endif;

if ( ! function_exists( 'mycred_partial_payment_slider' ) ) :
	function mycred_partial_payment_slider() {

		mycred_partial_payment_selector();

	}
endif;

if ( ! function_exists( 'mycred_part_woo_field_type_separator' ) ) :
	function mycred_part_woo_field_type_separator() {

		echo '<tr valign="top"><th scope="row" class="titledesc"></th><td class="formsep"><hr /></td></tr>';

	}
endif;
add_action( 'woocommerce_admin_field_separator', 'mycred_part_woo_field_type_separator' );
