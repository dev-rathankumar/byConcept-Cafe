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
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product, $woocommerce;
wp_dequeue_script('photoswipe-ui-default');
wp_dequeue_script('photoswipe');

wp_enqueue_script('jquery-magnific-popup');
wp_enqueue_style('magnific-popup');
wp_enqueue_script( 'slick' );

$attachment_ids = $product->get_gallery_image_ids();
$count = count( $attachment_ids);

$thumbnail_size    = apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' );
$post_thumbnail_id = $product->get_image_id();
$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );

$class_thumbnail = '';
if( empty($attachment_ids) || $count < 2 ) {
	$class_thumbnail = 'no-gallery-image';
}

?>
<div class="images <?php echo esc_attr($class_thumbnail); ?>">
		<?php do_action( 'tbay_product_video' ); ?>
	<?php
		if ( has_post_thumbnail() ) 
		{

			$attributes = array(
				'title'                   => get_post_field( 'post_title', $post_thumbnail_id ),
				'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2],
			);

			$attachment_ids = $product->get_gallery_image_ids();

			
			$attachment_count = count( $attachment_ids);
			
			$gallery          = $attachment_count > 0 ? '[product-gallery]' : '';
			$image_link       = wp_get_attachment_url( get_post_thumbnail_id() );
			$props            = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
			$image            = get_the_post_thumbnail( $post->ID, 'full', array(
				'title'	 => $props['title'],
				'alt'    => $props['alt'],
			) );
			
			$fullimage        = get_the_post_thumbnail( $post->ID, 'full', $attributes );

			// tbay FOR SLIDER
			$html  = '<section class="slider tbay-slider-for" data-number="5">';
			
			$html .= '<div class="zoom"><img alt="'.  esc_html__( 'Awaiting product image', 'puca' ) .'" src="'. esc_url($image_link) .'" /><a href="'. esc_url($image_link) .'" class="tbay-popup lightbox-gallery"></a></div>';
			
			foreach( $attachment_ids as $attachment_id ) {
			   $imgfull_src = wp_get_attachment_image_src( $attachment_id,'full');
			   $image_src   = wp_get_attachment_image_src( $attachment_id,'woocommerce_single');
			   $html .= '<div class="zoom"><img alt="'.  esc_html__( 'Awaiting product image', 'puca' ) .'" src="'. esc_url($imgfull_src[0]) .'" /><a href="'. esc_url($imgfull_src[0]) .'" class="tbay-popup lightbox-gallery"></a></div>';
			}
			
			$html .= '</section>';
			
			echo apply_filters('woocommerce_single_product_image_html', $html, $post_thumbnail_id);
		} else {
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), esc_html__( 'Placeholder', 'puca' ) ), $post->ID );
		}

		do_action( 'woocommerce_product_thumbnails' );
	?>
</div>