<?php
/**
 * WC_CP_Members_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Composite Products
 * @since    6.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Memberships Integration: Discounts inheritance.
 *
 * @version  6.0.0
 */
class WC_CP_Members_Compatibility {

	/**
	 * Runtime cache.
	 *
	 * @var boolean
	 */
	private static $member_is_logged_in;

	/**
	 * Flag used to prevent 'wc_memberships_exclude_product_from_member_discounts' from changing the return value.
	 *
	 * @var boolean
	 */
	private static $calculating_inherited_discounts = false;

	/**
	 * Initialization.
	 */
	public static function init() {

		// See 'WC_Memberships_Member_Discounts'.
		if ( ! ( is_admin() && ! is_ajax() ) ) {

	 		if ( 'filters' === WC_CP_Products::get_composited_cart_item_discount_method() ) {
	 			// Composite membership discounts are inherited by Component Options and applied here.
				add_filter( 'woocommerce_composited_product_discount', array( __CLASS__, 'inherit_member_discount' ), 10, 2 );
	 		}
	 	}

 		// Prevent Memberships from applying member discounts to Component Options - we'll handle everything on our end.
 		add_filter( 'wc_memberships_exclude_product_from_member_discounts', array( __CLASS__, 'exclude_composited_product_from_member_discounts' ), 10, 2 );
	}

	/**
	 * Whether the current user has an active membership.
	 *
	 * @return bool
	 */
	private static function member_is_logged_in() {

		if ( null === self::$member_is_logged_in ) {
			self::$member_is_logged_in = wc_memberships_is_user_member( get_current_user_id() );
		}

		return self::$member_is_logged_in;
	}

	/**
	 * Inherit Memberships discounts as composited item discounts.
	 *
	 * @param  mixed          $discount
	 * @param  WC_CP_Product  $component_option
	 * @return mixed
	 */
	public static function inherit_member_discount( $discount, $component_option ) {

		if ( ! self::member_is_logged_in() ) {
			return $discount;
		}

		// Flag to prevent 'exclude_composited_product_from_member_discounts' from kicking in.
		self::$calculating_inherited_discounts = true;

		$composite          = $component_option->get_composite();
		$composited_product = $component_option->get_product();

		// If the composite is excluded from member discounts, don't apply any discounts.
		if ( wc_memberships()->get_member_discounts_instance()->is_product_excluded_from_member_discounts( $composite ) ) {
			return $discount;
		}

		// If the product itself is excluded from member discounts, don't apply any discounts.
		if ( wc_memberships()->get_member_discounts_instance()->is_product_excluded_from_member_discounts( $composited_product ) ) {
			return $discount;
		}

		$member_id             = get_current_user_id();
		$parent_discount_rules = array();
		$child_discount_rules  = array();
		$discount_rules        = array();

		if ( wc_memberships()->get_member_discounts_instance()->user_has_member_discount( $composite ) ) {
			$parent_discount_rules = wc_memberships()->get_rules_instance()->get_user_product_purchasing_discount_rules( $member_id, $composite->get_id() );
		}

		if ( wc_memberships()->get_member_discounts_instance()->user_has_member_discount( $composited_product ) ) {
			$child_discount_rules = wc_memberships()->get_rules_instance()->get_user_product_purchasing_discount_rules( $member_id, $composited_product->get_id() );
		}

		$discount_rules_merged = array_merge( $parent_discount_rules, $child_discount_rules );

		// Make sure we don't apply the same membership discount twice.
		foreach ( $discount_rules_merged as $discount_rule ) {
			if ( empty( $discount_rules[ $discount_rule->get_id() ] ) ) {
				$discount_rules[ $discount_rule->get_id() ] = $discount_rule;
			}
		}

		/**
		 * 'woocommerce_component_option_member_discount_rules' filter.
		 *
		 * Use this filter to modify the discount rules, for example to use composite-level or product-level discount rules only.
		 *
		 * @param  array          $discount_rules
		 * @param  array          $parent_discount_rules
		 * @param  array          $child_discount_rules
		 * @param  WC_CP_Product  $component_option
		 */
		$discount_rules = apply_filters( 'woocommerce_component_option_member_discount_rules', $discount_rules, $parent_discount_rules, $child_discount_rules, $component_option );

		if ( empty( $discount_rules ) ) {
			return $discount;
		}

		$allow_cumulative = apply_filters( 'wc_memberships_allow_cumulative_member_discounts', true, $member_id, $composite );
		$rule_discounts   = array();

		foreach ( $discount_rules as $rule ) {

			// Only '%' discounts are supported!
			if ( 'percentage' !== $rule->get_discount_type() ) {
				continue;
			}

			if ( $rule_discount = (float) $rule->get_discount_amount() ) {
				$rule_discounts[ $rule->get_id() ] = $rule_discount;
			}
		}

		$discount       = (float) $discount;
		$rules_discount = 0;

		if ( $allow_cumulative ) {

			foreach ( $rule_discounts as $rule_discount ) {
				$rules_discount = $rules_discount + $rule_discount - ( $rule_discount * $rules_discount ) / 100;
			}

		} else {

			$rules_discount = max( $rule_discounts );
		}

		$discount = $discount + $rules_discount - ( $rules_discount * $discount ) / 100;

		self::$calculating_inherited_discounts = false;

		/**
		 * 'woocommerce_component_option_member_discount' filter.
		 *
		 * Use this filter to modify the membership discount applied on composited products.
		 *
		 * @param  float          $discount
		 * @param  array          $discount_rules
		 * @param  WC_CP_Product  $component_option
		 */
		return apply_filters( 'woocommerce_component_option_member_discount', $discount, $discount_rules, $component_option );
	}

	/**
	 * Prevent Memberships from applying member discounts to composited products -- membership discounts are inherited.
	 *
	 * @param  boolean     $exclude
	 * @param  WC_Product  $product
	 * @return boolean
	 */
	public static function exclude_composited_product_from_member_discounts( $exclude, $product ) {

		if ( ! self::$calculating_inherited_discounts && WC_CP_Products::is_component_option_pricing_context( $product ) ) {
			$exclude = true;
		}

		return $exclude;
	}
}

WC_CP_Members_Compatibility::init();
