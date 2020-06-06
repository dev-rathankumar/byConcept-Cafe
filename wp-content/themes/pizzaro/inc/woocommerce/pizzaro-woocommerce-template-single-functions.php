<?php
/**
 * WooCommerce Single Template Functions.
 *
 * @package pizzaro
 */

if ( ! function_exists( 'pizzaro_woocommerce_is_purchasable' ) ) {
	function pizzaro_woocommerce_is_purchasable( $purchasable, $product ){
		if( $product->get_price() == 0 ||  $product->get_price() == '') {
			$purchasable = true;
		}
		return $purchasable;
	}
}

if ( ! function_exists( 'pizzaro_get_single_product_layout' ) ) {
	function pizzaro_get_single_product_layout() {
		$layout = apply_filters( 'pizzaro_single_product_layout', 'full-width' );

		return $layout;
	}
}

if ( ! function_exists( 'pizzaro_get_single_product_style' ) ) {
	function pizzaro_get_single_product_style() {

		$product_style = get_post_meta( get_the_ID(), '_product_style', true );
		$style = ! empty( $product_style ) ? $product_style : apply_filters( 'pizzaro_single_product_layout_style', 'style-1' );

		return $style;
	}
}

if ( ! function_exists( 'pizzaro_toggle_single_product_hooks' ) ) {
	function pizzaro_toggle_single_product_hooks() {

		$style 	= pizzaro_get_single_product_style();

		if ( 'style-3' === $style ) {
			add_action( 'woocommerce_after_single_product_summary', 'pizzaro_wrap_single_add_to_cart', 4 );
			add_action( 'woocommerce_after_single_product_summary', 'pizzaro_template_single_add_to_cart', 5 );
			add_action( 'woocommerce_after_single_product_summary', 'pizzaro_wrap_single_add_to_cart_close', 6 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 10 );
		} elseif ( 'style-2' === $style ) {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
				remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
				add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_thumbnails', 25 );
			} else {
				add_action( 'woocommerce_single_product_summary', 'pizzaro_wc_show_product_thumbnails', 25 );
			}
			add_action( 'woocommerce_after_single_product_summary', 'pizzaro_wrap_single_add_to_cart', 4 );
			add_action( 'woocommerce_after_single_product_summary', 'pizzaro_get_product_delivery_time', 5 );
			add_action( 'woocommerce_after_single_product_summary', 'pizzaro_template_single_add_to_cart', 5 );
			add_action( 'woocommerce_after_single_product_summary', 'pizzaro_wrap_single_add_to_cart_close', 6 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 23 );
			add_action( 'woocommerce_single_product_summary', 'pizzaro_get_product_allergy_alerts', 24 );
		} else {
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 23 );
			add_action( 'woocommerce_single_product_summary', 'pizzaro_template_single_add_to_cart', 30 );
		}
	}
}

if( ! function_exists( 'pizzaro_template_single_add_to_cart' ) ) {
	function pizzaro_template_single_add_to_cart() {
		global $product;

		$product_type = pizzaro_wc_get_product_type( $product );
		if( pizzaro_shop_catalog_mode() == false ) {
			do_action( 'woocommerce_' . $product_type . '_add_to_cart'  );
		} elseif( pizzaro_shop_catalog_mode() == true && $product->is_type( 'external' ) ) {
			do_action( 'woocommerce_' . $product_type . '_add_to_cart'  );
		}
	}
}

if ( ! function_exists( 'pizzaro_get_product_delivery_time' ) ) {
	function pizzaro_get_product_delivery_time() {

		$delivery_time = get_post_meta( get_the_ID(), '_delivery_time', true );
		if( ! empty( $delivery_time ) ) {
			?>
			<div class="delivery-time"><?php echo esc_html__( 'Delivery: ', 'pizzaro' ) ?><span><?php echo esc_html( $delivery_time ) ?></span></div>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_get_product_allergy_alerts' ) ) {
	function pizzaro_get_product_allergy_alerts() {

		$allergy_alerts = get_post_meta( get_the_ID(), '_allergy_alerts', true );
		if( ! empty( $allergy_alerts ) ) {
			?>
			<div class="allergy-alerts"><?php echo esc_html__( 'Allergy Alerts ', 'pizzaro' ) ?><span><?php echo esc_html( $allergy_alerts ) ?></span></div>
			<?php
		}
	}
}

if ( ! function_exists( 'woocommerce_template_single_title' ) ) {

	/**
	 * Output the product title.
	 *
	 * @subpackage	Product
	 */
	function woocommerce_template_single_title() {
		ob_start();
		pizzaro_template_product_food_type_icon();
		$food_icon = ob_get_clean();
		the_title( '<h1 itemprop="name" class="product_title entry-title">', $food_icon . '</h1>' );
	}
}

if ( ! function_exists( 'pizzaro_product_description_tab' ) ) {
	function pizzaro_product_description_tab() {
		wc_get_template( 'single-product/tabs/description.php' );
	}
}

if ( ! function_exists( 'pizzaro_product_nutrition_tab' ) ) {
	function pizzaro_product_nutrition_tab() {
		global $product;

		$product_id = pizzaro_wc_get_product_id( $product );
		$nutritions = get_post_meta( $product_id, '_nutritions', true );

		echo wp_kses_post( $nutritions );
	}
}

if ( ! function_exists ( 'pizzaro_wrap_single_product' ) ) {
	/**
	 *
	 */
	function pizzaro_wrap_single_product() {
		?>
		<div class="single-product-wrapper">
		<?php
	}
}

if ( ! function_exists( 'pizzaro_wrap_single_product_close' ) ) {
	/**
	 *
	 */
	function pizzaro_wrap_single_product_close() {
		?>
		</div><!-- /.single-product-wrapper -->
		<?php
	}
}

if ( ! function_exists ( 'pizzaro_wrap_product_images' ) ) {
	/**
	 *
	 */
	function pizzaro_wrap_product_images() {
		?>
		<div class="product-images-wrapper">
		<?php
	}
}

if ( ! function_exists( 'pizzaro_wrap_product_images_close' ) ) {
	/**
	 *
	 */
	function pizzaro_wrap_product_images_close() {
		?>
		</div><!-- /.product-images-wrapper -->
		<?php
	}
}

if ( ! function_exists ( 'pizzaro_wrap_single_add_to_cart' ) ) {
	/**
	 *
	 */
	function pizzaro_wrap_single_add_to_cart() {
		?>
		<div class="product-form-wrapper">
		<?php
	}
}

if ( ! function_exists( 'pizzaro_wrap_single_add_to_cart_close' ) ) {
	/**
	 *
	 */
	function pizzaro_wrap_single_add_to_cart_close() {
		?>
		</div><!-- /.product-images-wrapper -->
		<?php
	}
}

if( ! function_exists( 'pizzaro_wc_show_product_thumbnails' ) ) {
	/**
	 *
	 */
	function pizzaro_wc_show_product_thumbnails() {
		global $post, $product;
		$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
		$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
		$attachment_ids    = $product->get_gallery_image_ids();
		$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
		$thumbnail_post    = get_post( $post_thumbnail_id );
		$image_title       = $thumbnail_post->post_content;
		$placeholder       = has_post_thumbnail() ? 'with-images' : 'without-images';
		$wrapper_classes   = apply_filters( 'pizzaro_wc_single_product_image_gallery_classes', array(
			'pizzaro-wc-product-gallery',
			'pizzaro-wc-product-gallery--' . $placeholder,
			'pizzaro-wc-product-gallery--columns-' . absint( $columns ),
			'images',
		) );
		$carousel_args     = apply_filters( 'pizzaro_wc_product_thumbnails_carousel_args', array(
			'selector'		=> '.pizzaro-wc-product-gallery__wrapper > .pizzaro-wc-product-gallery__image',
			'animation'		=> 'slide',
			'controlNav'	=> false,
			'animationLoop'	=> false,
			'slideshow'		=> false,
			'itemWidth'		=> 90,
			'itemMargin'	=> 20,
			'asNavFor'		=> '.woocommerce-product-gallery'
		) );
		?>
		<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
			<figure class="pizzaro-wc-product-gallery__wrapper">
				<?php
				$attributes = array(
					'title'                   => $image_title,
					'data-large-image'        => $full_size_image[0],
					'data-large-image-width'  => $full_size_image[1],
					'data-large-image-height' => $full_size_image[2],
				);

				if ( has_post_thumbnail() ) {
					$html  = '<figure data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'shop_thumbnail' ) . '" class="pizzaro-wc-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '">';
					$html .= get_the_post_thumbnail( $post->ID, 'shop_thumbnail', $attributes );
					$html .= '</a></figure>';
				} else {
					$html  = '<figure class="pizzaro-wc-product-gallery__image--placeholder">';
					$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'pizzaro' ) );
					$html .= '</figure>';
				}

				echo apply_filters( 'pizzaro_wc_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

				if ( $attachment_ids ) {
					foreach ( $attachment_ids as $attachment_id ) {
						$full_size_image  = wp_get_attachment_image_src( $attachment_id, 'full' );
						$thumbnail        = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
						$thumbnail_post   = get_post( $attachment_id );
						$image_title      = $thumbnail_post->post_content;

						$attributes = array(
							'title'                   => $image_title,
							'data-large-image'        => $full_size_image[0],
							'data-large-image-width'  => $full_size_image[1],
							'data-large-image-height' => $full_size_image[2],
						);

						$html  = '<figure data-thumb="' . esc_url( $thumbnail[0] ) . '" class="pizzaro-wc-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '">';
						$html .= wp_get_attachment_image( $attachment_id, 'shop_thumbnail', false, $attributes );
				 		$html .= '</a></figure>';

						echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
					}
				}
				?>
			</figure>
		</div>
		<script type="text/javascript">
			jQuery(document).ready( function($){
				var $flex = $( '.pizzaro-wc-product-gallery');
				$flex.flexslider( <?php echo json_encode( $carousel_args );?> );
			} );
		</script>
		<?php
	}
}
