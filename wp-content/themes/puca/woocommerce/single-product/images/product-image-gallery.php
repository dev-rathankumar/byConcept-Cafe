<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

wp_dequeue_script( 'jquery-flexslider' );
wp_dequeue_script( 'flexslider' );
wp_dequeue_script( 'zoom' );

//check Enough number image thumbnail
$attachment_ids 		= $product->get_gallery_image_ids();
$count 					= count( $attachment_ids);

if( isset($count) && $count > 0 ) {
	wp_enqueue_script( 'hc-sticky' );
}

$columns           		= apply_filters( 'woocommerce_product_thumbnails_columns', 2 );
$class_thumbnail 		= '';
if( empty($attachment_ids) || $count < 2 ) {
	$class_thumbnail 	= 'no-gallery-image';
}

$thumbnail_size    		= apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' );
$post_thumbnail_id 		= $product->get_image_id();
$full_size_image   		= wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
$placeholder       		= has_post_thumbnail() ? 'with-images' : 'without-images';
$wrapper_classes   		= apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . $placeholder,
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
	$class_thumbnail,
) );

?>



<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">

	<?php do_action( 'tbay_product_video' ); ?>

	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		$attributes = array(
			'title'                   => get_post_field( 'post_title', $post_thumbnail_id ),
			'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
			'data-src'                => $full_size_image[0],
			'data-large_image'        => $full_size_image[0],
			'data-large_image_width'  => $full_size_image[1],
			'data-large_image_height' => $full_size_image[2],
		);

		if ( is_singular('product') ) {

			if ( has_post_thumbnail() ) {
				$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'woocommerce_gallery_thumbnail' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '">';
				$html .= get_the_post_thumbnail( $post->ID, 'woocommerce_single', $attributes );
				$html .= '</a>';
				$html .='</div>';
			} else {
				$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
				$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'puca' ) );
				$html .= '</div>';
			}

			
			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

			
			do_action( 'woocommerce_product_thumbnails' );

		}
		else {

			if ( has_post_thumbnail() ) {
				$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'woocommerce_gallery_thumbnail' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( get_permalink($post->ID) ) . '">';
				$html .= get_the_post_thumbnail( $post->ID, 'woocommerce_single', $attributes );
				$html .= '</a></div>';
			} else {
				$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
				$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'puca' ) );
				$html .= '</div>';
			}


			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

			do_action( 'woocommerce_product_thumbnails' );

		}
		?>
	</figure>
</div>
