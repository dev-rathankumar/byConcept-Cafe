<?php
// No dirrect access
if ( ! defined( 'MYCRED_WOOPLUS_VERSION' ) ) exit;

/**
 * Settings Fields
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_part_woo_settings_fields' ) ) :
	function mycred_part_woo_settings_fields( $settings ) {

		global $mycred_partial_payment;

		$plugin = array(
			array(
				'title'       => __( 'Partial Payments', 'mycredpartwoo' ),
				'desc'        => __( 'Partial payments allows your users to pay for parts of the order using points. The remaining amount is paid using one of your active gateways.', 'mycredpartwoo' ),
				'type'        => 'title',
				'id'          => 'partial_payment_options',
			),
			array(
				'title'       => __( 'Position', 'mycredpartwoo' ),
				'desc'        => __( 'This controls where the partial payment form is shown on the checkout page.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[position]',
				'default'     => $mycred_partial_payment['position'],
				'type'        => 'radio',
				'options'     => array(
					'none'        => __( 'Disabled - I will insert the form myself using my theme template files.', 'mycredpartwoo' ),
					'before'      => __( 'Before Order Total', 'mycredpartwoo' ),
					'after'       => __( 'After Order Total', 'mycredpartwoo' ),
				),
				'autoload'    => false,
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Point Type', 'mycredpartwoo' ),
				'desc'        => __( 'Select the point type a user can pay with.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[point_type]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['point_type'],
				'type'        => 'select',
				'options'     => mycred_get_types( true ),
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Exchange Rate', 'mycredpartwoo' ),
				'desc'        => __( 'How much is 1 point worth in your store currency?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[exchange]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['exchange'],
				'desc_tip'    => false
			),
			array(
				'title'       => __( 'Minimum', 'mycredpartwoo' ),
				'desc'        => __( 'The minimum amount of points a user must pay. Use zero to disable and allow users to checkout without any point payment.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[min]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['min'],
				'desc_tip'    => false
			),
			array(
				'title'       => __( 'Maximum', 'mycredpartwoo' ),
				'desc'        => __( 'The maximum percentage of the cart total a user can pay using points.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[max]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['max'],
				'desc_tip'    => false
			),
			array(
				'title'       => __( 'Multiple Payment', 'mycredpartwoo' ),
				'desc'        => __( 'Can users, if they can afford it, make multiple payments?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[multiple]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['multiple'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				),
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Allow Undo', 'mycredpartwoo' ),
				'desc'        => __( 'Can users undo a partial payment? Undoing a partial payment will refund the amount the user paid.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[undo]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['undo'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				),
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Store Rewards', 'mycredpartwoo' ),
				'desc'        => __( 'If you are rewarding your users with points for store purchases, how do you want to handle partial point payments?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[rewards]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['rewards'],
				'type'        => 'select',
				'options'     => array(
					1             => __( 'I do not use store rewards', 'mycredpartwoo' ),
					2             => __( 'Deduct the partial payment amount from the amount the user gets in reward', 'mycredpartwoo' ),
					3             => __( 'Ignore partial payments and let rewards payout the appropriate amount', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Pay for: Tax', 'mycredpartwoo' ),
				'desc'        => __( 'Can points to be used to pay for taxes? This should be set to "No" if prices includes taxes.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[before_tax]',
				'class'       => 'wc-enhanced-select',
				'default'     => $mycred_partial_payment['before_tax'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Pay for: Shipping', 'mycredpartwoo' ),
				'desc'        => __( 'Can points be used to pay for shipping costs? Only applicable for orders that needs to be shipped. If you only sell virtual products, this should be set to "No".', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[free_shipping]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px; display: block;',
				'default'     => $mycred_partial_payment['free_shipping'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Pay for: Sale Items', 'mycredpartwoo' ),
				'desc'        => __( 'Can points be used to pay for items that are "on sale"?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[sale_items]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['sale_items'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				)
			),
			array( 'type'     => 'separator' ),
			array(
				'title'       => __( 'Selector Type', 'mycredpartwoo' ),
				'desc'        => __( 'This controls how a user indicates the amount they want to pay for the order.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[selecttype]',
				'default'     => $mycred_partial_payment['selecttype'],
				'type'        => 'radio',
				'options'     => array(
					'input'       => __( 'Input Field - The amount needs to be typed in.', 'mycredpartwoo' ),
					'slider'      => __( 'Slider - The amount changes based on the sliders position.', 'mycredpartwoo' ),
				),
				'autoload'    => false,
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Step', 'mycredpartwoo' ),
				'desc'        => __( 'The point amount that each step increment when using the slider. Ignored with input fields.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[step]',
				'type'        => 'text',
				'default'     => $mycred_partial_payment['step'],
				'desc_tip'    => false
			),
			array( 'type'     => 'separator' ),
			array(
				'title'       => __( 'Title', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[title]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['title'],
				'css'         => 'min-width:300px;'
			),
			array(
				'title'       => __( 'Description', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[desc]',
				'css'         => 'width:50%; height: 100px;',
				'placeholder' => __( 'Optional', 'mycredpartwoo' ),
				'type'        => 'textarea',
				'default'     => $mycred_partial_payment['desc'],
				'autoload'    => false
			),
			array(
				'title'       => __( 'Button Label', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[button]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['button'],
				'css'         => 'min-width:300px;'
			),
			array( 'type'     => 'separator' ),
			array(
				'title'       => __( 'Log Template', 'mycredpartwoo' ),
				'desc'        => __( 'The log template used for partial payments.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[log]',
				'type'        => 'text',
				'default'     => $mycred_partial_payment['log'],
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'css'         => 'min-width:300px;'
			),
			array(
				'title'       => __( 'Refund Log Template', 'mycredpartwoo' ),
				'desc'        => __( 'The log template used for partial payment refunds. This is used when you manually remove a coupon in an order or if you allow users to undo their payment before placing the order.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[log_refund]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['log_refund'],
				'css'         => 'min-width:300px;'
			),
			array(
				'title'       => __( 'Refund Message', 'mycredpartwoo' ),
				'desc'        => __( 'Optional message to show to user when a partial payment was refunded.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[refund_message]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['refund_message'],
				'css'         => 'min-width:300px;'
			),
			array(
				'type'        => 'sectionend',
				'id'          => 'partial_payment_options',
			),
			array(
				'title'       => __( 'Checkout Totals', 'mycredpartwoo' ),
				'desc'        => __( 'Select what point related details you would like to show on the checkout page.', 'mycredpartwoo' ),
				'type'        => 'title',
				'id'          => 'partial_payment_checkout_option',
			),
			array(
				'title'       => __( 'Point Cost', 'mycredpartwoo' ),
				'desc'        => __( 'Show the cart cost in points based on our exchange rate.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[checkout_total]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['checkout_total'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'cart'        => __( 'In Cart', 'mycredpartwoo' ),
					'checkout'    => __( 'In Checkout', 'mycredpartwoo' ),
					'both'        => __( 'Both Cart and Checkout', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Total Label', 'mycredpartwoo' ),
				'desc'        => __( 'The label to use for the total cost. Supports General Template tags.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[checkout_total_label]',
				'type'        => 'text',
				'placeholder' => __( 'Required if enabled', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['checkout_total_label'],
				'css'         => 'min-width:300px;'
			),
			array(
				'title'       => __( 'Users Balance', 'mycredpartwoo' ),
				'desc'        => __( 'Show the current users balance.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[checkout_balance]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['checkout_balance'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'cart'        => __( 'In Cart', 'mycredpartwoo' ),
					'checkout'    => __( 'In Checkout', 'mycredpartwoo' ),
					'both'        => __( 'Both Cart and Checkout', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Balance Label', 'mycredpartwoo' ),
				'desc'        => __( 'The label to use for the balance. Supports General Template tags.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[checkout_balance_label]',
				'type'        => 'text',
				'placeholder' => __( 'Required if enabled', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['checkout_balance_label'],
				'css'         => 'min-width:300px;'
			),
			array(
				'type'        => 'sectionend',
				'id'          => 'partial_payment_checkout_option',
			),
		);

		return array_merge( $settings, $plugin );

	}
endif;
///add_filter( 'woocommerce_get_settings_checkout', 'mycred_part_woo_settings_fields' );

/**
 * Account Settings Fields
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_part_woo_account_settings_fields' ) ) :
	function mycred_part_woo_account_settings_fields( $settings ) {

		$prefs  = mycred_part_woo_account_settings();

		$plugin = array(
			array(
				'title'       => __( 'Points History', 'mycredpartwoo' ),
				'desc'        => __( 'Option to include your users points related history in the account page. When enabled or if you change the SLUG you will need to reset your permalinks.', 'mycredpartwoo' ),
				'type'        => 'title',
				'id'          => 'partial_payment_options',
			),
			array(
				'title'       => __( 'Page SLUG', 'mycredpartwoo' ),
				'desc'        => __( 'The page slug / endpoint to use. Leave empty to disable.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[slug]',
				'type'        => 'text',
				'placeholder' => __( 'Leave empty to disable', 'mycredpartwoo' ),
				'default'     => $prefs['slug'],
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Page Title', 'mycredpartwoo' ),
				'desc'        => __( 'The page title.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[title]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $prefs['title'],
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Description', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[desc]',
				'css'         => 'width:50%; height: 100px;',
				'placeholder' => __( 'Optional', 'mycredpartwoo' ),
				'type'        => 'textarea',
				'default'     => $prefs['desc'],
				'autoload'    => false
			),
			array( 'type'     => 'separator' ),
			array(
				'title'       => __( 'Number', 'mycredpartwoo' ),
				'desc'        => __( 'The number of log entries to show per page.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[number]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $prefs['number'],
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'References', 'mycredpartwoo' ),
				'desc'        => __( 'Option to filter log entries to only show certain references. This can be either a single reference or a comma separated list of references. Leave empty to show everything. If you want to show partial payments / store payments or refunds, use "store".', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[show]',
				'type'        => 'text',
				'placeholder' => __( 'Optional', 'mycredpartwoo' ),
				'default'     => $prefs['show'],
				'desc_tip'    => false
			),
			array(
				'title'       => __( 'Navigation', 'mycredpartwoo' ),
				'desc'        => __( 'Show navigation?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[nav]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $prefs['nav'],
				'type'        => 'select',
				'options'     => array(
					0             => __( 'No', 'mycredpartwoo' ),
					1             => __( 'Yes', 'mycredpartwoo' )
				),
				'desc_tip'    =>  true
			),
			array(
				'title'       => __( 'Point Type', 'mycredpartwoo' ),
				'desc'        => __( 'Select the point type to show.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[point_type]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $prefs['point_type'],
				'type'        => 'select',
				'options'     => mycred_get_types( true ),
				'desc_tip'    => true,
			),
			array(
				'type'        => 'sectionend',
				'id'          => 'partial_payment_options',
			)
		);

		return array_merge( $settings, $plugin );

	}
endif;
//add_filter( 'woocommerce_get_settings_account', 'mycred_part_woo_account_settings_fields' );

/**
 * Custom Woo Field Type
 * @since 1.0
 * @version 1.0
 */

