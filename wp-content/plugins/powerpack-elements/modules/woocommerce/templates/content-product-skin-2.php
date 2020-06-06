<?php
/**
 * PowerPack WooCommerce Products - Template.
 *
 * @package PowerPack
 */

use PowerpackElements\Classes\PP_Woo_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>

<?php

$post_id    = $product->get_id();
$class      = array();
$classes    = array();
$classes[]  = 'post-' . $post_id;
$wc_classes = esc_attr( implode( ' ', wc_product_post_class( $classes, $class, $post_id ) ) );

$sale_badge_position = $settings['sale_badge_position'];
$featured_badge_position = $settings['featured_badge_position'];
$top_rating_badge_position = $settings['top_rating_badge_position'];
$best_selling_badge_position = $settings['best_selling_badge_position'];
$quick_view_type = $settings['quick_view_type'];
$image_size = $settings['thumbnail_size'];
if ( !$image_size ) {
	$image_size = 'woocommerce_thumbnail';
}

$out_of_stock        = get_post_meta( $post_id, '_stock_status', true );
$out_of_stock_string = apply_filters( 'pp_woo_out_of_stock_string', __( 'Out of stock', 'powerpack' ) );

?>
<li <?php post_class(); ?>>
	<div class="pp-woo-product-wrapper">
		<?php

		echo '<div class="pp-woo-products-thumbnail-wrap">';

		if ( $sale_badge_position || $featured_badge_position || $top_rating_badge_position || $best_selling_badge_position ) {


			if ( 'left' == ( $sale_badge_position || $featured_badge_position || $top_rating_badge_position || $best_selling_badge_position ) ) {
                echo '<div class="pp-badge-container pp-left-badge-container">';
				include POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/loop/left-badge.php';
                echo '</div>';
			}

			if ( 'right' == ( $sale_badge_position || $featured_badge_position || $top_rating_badge_position || $best_selling_badge_position ) ) {
                echo '<div class="pp-badge-container pp-right-badge-container">';
				include POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/loop/right-badge.php';
                echo '</div>';
			}
		}

		if ( 'yes' === $settings['link_image'] ) {
			woocommerce_template_loop_product_link_open();
		}
		
		echo woocommerce_get_product_thumbnail( $image_size );

		if ( 'swap' === $settings['products_hover_style'] ) {
			PP_Woo_Helper::get_instance()->woo_shop_product_flip_image();
		}

		if ( 'yes' === $settings['link_image'] ) {
			woocommerce_template_loop_product_link_close();
		}

		/* Out of stock */
		if ( 'outofstock' === $out_of_stock ) {
			echo '<span class="pp-out-of-stock">' . esc_html( $out_of_stock_string ) . '</span>';
		}


		/* Product Actions */
		echo '<div class="pp-product-actions">';
		if ( '' !== $quick_view_type ) {

			echo '<div class="pp-action-item-wrap pp-quick-view-btn" data-product_id="' . $post_id . '">';

				echo '<span class="pp-action-item fa fa-eye"></span>';
				echo '<span class="pp-action-tooltip">' . __( 'Quick View', 'powerpack' ) . '</span>';
			echo '</div>';
		}

		if ( 'yes' === $settings['show_add_cart'] ) {

			$cart_class = $product->is_purchasable() && $product->is_in_stock() ? 'pp-add-to-cart-btn' : '';


			echo '<a href=' . $product->add_to_cart_url() . ' class="pp-action-item-wrap pp-cart-section ajax_add_to_cart add_to_cart_button ' . $cart_class . ' product_type_' . $product->get_type() . '" data-quantity="1" data-product_id="' . $post_id . '">';
				echo '<span class="pp-action-item pp-ajax-add-cart-icon fa fa-shopping-cart"></span>';
				echo '<span class="pp-action-tooltip">' . $product->add_to_cart_text() . '</span>';
			echo '</a>';
		}
		echo '</div>';

		echo '</div>';

		$shop_structure = array();

		if ( 'yes' === $settings['show_category'] ) {

			$shop_structure[] = 'category';
		}
		if ( 'yes' === $settings['show_title'] ) {

			$shop_structure[] = 'title';
		}
		if ( 'yes' === $settings['show_ratings'] ) {

			$shop_structure[] = 'ratings';
		}
		if ( 'yes' === $settings['show_price'] ) {

			$shop_structure[] = 'price';
		}
		if ( 'yes' === $settings['show_short_desc'] ) {

			$shop_structure[] = 'short_desc';
		}

		$shop_structure = apply_filters(
			'pp_woo_products_content_structure',
			$shop_structure
		);

		if ( is_array( $shop_structure ) && ! empty( $shop_structure ) ) {

			do_action( 'pp_woo_products_before_summary_wrap', $post_id, $settings );
			echo '<div class="pp-woo-products-summary-wrap">';
			do_action( 'pp_woo_products_summary_wrap_top', $post_id, $settings );

			foreach ( $shop_structure as $value ) {

				switch ( $value ) {
					case 'title':
						/**
						 * Add Product Title on shop page for all products.
						 */
						do_action( 'pp_woo_products_title_before', $post_id, $settings );
						if ( 'yes' === $settings['link_title'] ) {
							echo '<a href="' . esc_url( apply_filters( 'pp_woo_title_link', get_the_permalink() ) ) . '" class="pp-loop-product__link">';
								woocommerce_template_loop_product_title();
							echo '</a>';
						} else {
							woocommerce_template_loop_product_title();
						}
						do_action( 'pp_woo_products_title_after', $post_id, $settings );
						break;
					case 'price':
						/**
						 * Add Product Price on shop page for all products.
						 */
						do_action( 'pp_woo_products_price_before', $post_id, $settings );
						woocommerce_template_loop_price();
						do_action( 'pp_woo_products_price_after', $post_id, $settings );
						break;
					case 'ratings':
						/**
						 * Add rating on shop page for all products.
						 */
						do_action( 'pp_woo_products_rating_before', $post_id, $settings );
						woocommerce_template_loop_rating();
						do_action( 'pp_woo_products_rating_after', $post_id, $settings );
						break;
					case 'short_desc':
						do_action( 'pp_woo_products_short_description_before', $post_id, $settings );
						PP_Woo_Helper::get_instance()->woo_shop_short_desc();
						do_action( 'pp_woo_products_short_description_after', $post_id, $settings );
						break;
					case 'add_cart':
						do_action( 'pp_woo_products_add_to_cart_before', $post_id, $settings );
						woocommerce_template_loop_add_to_cart();
						do_action( 'pp_woo_products_add_to_cart_after', $post_id, $settings );
						break;
					case 'category':
						/**
						 * Add and/or Remove Categories from shop archive page.
						 */
						do_action( 'pp_woo_products_category_before', $post_id, $settings );
						PP_Woo_Helper::get_instance()->woo_shop_parent_category();
						do_action( 'pp_woo_products_category_after', $post_id, $settings );
						break;
					default:
						break;
				}
			}

			do_action( 'pp_woo_products_summary_wrap_bottom', $post_id, $settings );
			echo '</div>';
			do_action( 'pp_woo_products_after_summary_wrap', $post_id, $settings );
		}
		?>
	</div>
</li>
