<?php
/**
 * Shared trait for Atum Product Levels' products
 *
 * @package        AtumLevels\Levels
 * @subpackage     Products
 * @author          Be Rebel - https://berebel.io
 * @copyright       ©2020 Stock Management Labs™
 *
 * @since           1.1.3
 */

namespace AtumLevels\Levels\Products;

defined( 'ABSPATH' ) || die;

use AtumLevels\Inc\Helpers;


trait BOMProductTrait {

	/**
	 * Whether the product is purchasable in shop
	 *
	 * @var bool
	 */
	protected $purchasable = FALSE;

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 *
	 * @param int|\WC_Product|object $product Product to init.
	 */
	public function __construct( $product ) {

		// Add custom properties for BOM products to the ATUM data.
		$this->atum_data['bom_sellable'] = NULL;
		
		/* @noinspection PhpUndefinedClassInspection */
		parent::__construct( $product );
		
		$this->purchasable = Helpers::is_purchase_allowed( $this );
		
		if ( $this->purchasable && ! Helpers::is_a_bom_variable( $this ) ) {
			$this->supports[] = 'ajax_add_to_cart';
		}
		
	}

	/**
	 * Returns false if the product cannot be bought
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function is_purchasable() {

		/* @noinspection PhpUndefinedClassInspection */
		return apply_filters( "atum/{$this->product_type}/is_purchasable", ! $this->purchasable ? $this->purchasable : parent::is_purchasable(), $this );
	}

	/**
	 * Returns whether or not the product can be backordered
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function backorders_allowed() {

		/* @noinspection PhpUndefinedClassInspection */
		return apply_filters( "atum/{$this->product_type}/backorders_allowed", ! $this->purchasable ? $this->purchasable : parent::backorders_allowed(), $this->id, $this );
	}

	/**
	 * Checks if a product needs shipping
	 *
	 * @return bool
	 */
	public function needs_shipping() {

		/* @noinspection PhpUndefinedClassInspection */
		return apply_filters( "atum/{$this->product_type}/needs_shipping", ! $this->purchasable ? $this->purchasable : parent::needs_shipping(), $this );
	}

	/**
	 * Returns whether or not the product is visible in the catalog
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function is_visible() {

		/* @noinspection PhpUndefinedClassInspection */
		return apply_filters( "atum/{$this->product_type}/is_visible", ! $this->purchasable ? $this->purchasable : parent::is_visible(), $this->id );
	}

	/**
	 * Get the add to url used mainly in loops
	 *
	 * @sinde 1.1.3
	 *
	 * @return string
	 */
	public function add_to_cart_url() {
		
		$url = $this->is_purchasable() && $this->is_in_stock() && ! Helpers::is_a_bom_variable( $this ) ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

		return apply_filters( "atum/product_levels/{$this->product_type}/add_to_cart_url", $url, $this );
	}

	/**
	 * Get the add to cart button text
	 *
	 * @since 1.1.3
	 *
	 * @return string
	 */
	public function add_to_cart_text() {
		
		if ( Helpers::is_a_bom_variable( $this ) ) {
			$text = parent::add_to_cart_text();
		}
		else {
			$text = ( $this->is_purchasable() && $this->is_in_stock() ) ? __( 'Add to cart', ATUM_LEVELS_TEXT_DOMAIN ) : __( 'Read more', ATUM_LEVELS_TEXT_DOMAIN );
		}
		
		return apply_filters( "atum/product_levels/{$this->product_type}/add_to_cart_text", $text, $this );
	}

	/**
	 * Get internal type
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->product_type;
	}

	/**
	 * Returns the BOM product's sellable prop.
	 *
	 * @since 1.2.12
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string 'yes' or 'no' or null
	 */
	public function get_bom_sellable( $context = 'view' ) {

		$bom_sellable = $this->get_prop( 'bom_sellable', $context );

		if ( ! is_null( $bom_sellable ) ) {
			$bom_sellable = wc_bool_to_string( $bom_sellable );
		}

		return $bom_sellable;

	}

	/**
	 * Set if the BOM product is sellable.
	 *
	 * @since 1.2.12
	 *
	 * @param string|bool|null $bom_sellable Whether or not the BOM product is sellable or has the global option.
	 */
	public function set_bom_sellable( $bom_sellable ) {
		$bom_sellable = ! is_null( $bom_sellable ) && 'global' !== $bom_sellable ? wc_string_to_bool( $bom_sellable ) : NULL;
		$this->set_prop( 'bom_sellable', $bom_sellable );
	}

}
