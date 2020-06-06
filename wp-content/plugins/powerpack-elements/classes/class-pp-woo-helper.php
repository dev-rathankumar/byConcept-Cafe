<?php
/**
 * PowerPack Helper.
 *
 * @package PowerPack
 */

namespace PowerpackElements\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class PP_Woo_Helper.
 */
class PP_Woo_Helper {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Short Description.
	 *
	 * @since 1.3.3
	 */
	public function woo_shop_short_desc() {
		if ( has_excerpt() ) {
			echo '<div class="pp-woo-products-description">';
				echo the_excerpt();
			echo '</div>';
		}
	}

	/**
	 * Parent Category.
	 *
	 * @since 1.1.0
	 */
	public function woo_shop_parent_category() {
		if ( apply_filters( 'pp_woo_shop_parent_category', true ) ) : ?>
			<span class="pp-woo-product-category">
				<?php
				global $product;
				$product_categories = function_exists( 'wc_get_product_category_list' ) ? wc_get_product_category_list( get_the_ID(), ',', '', '' ) : $product->get_categories( ',', '', '' );

				$product_categories = strip_tags( $product_categories );
				if ( $product_categories ) {
					list( $parent_cat ) = explode( ',', $product_categories );
					echo esc_html( $parent_cat );
				}
				?>
			</span> 
			<?php
		endif;
	}

	/**
	 * Product Flip Image.
	 *
	 * @since 1.3.3
	 */
	public function woo_shop_product_flip_image() {

		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {

			$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'shop_catalog' );

			echo apply_filters( 'pp_woocommerce_product_flip_image', wp_get_attachment_image( reset( $attachment_ids ), $image_size, false, array( 'class' => 'pp-show-on-hover' ) ) );
		}
	}
}
