<?php
/**
 * Tbay Product Image
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

//check Enough number image thumbnail
$attachment_ids = $product->get_gallery_image_ids();
$count = 0;
foreach( $attachment_ids as $attachment_id ) 
{
    $count ++;
}

$src_blank = 'data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D&#039;http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg&#039; viewBox%3D&#039;0 0 600 400&#039;%2F%3E';
$size = 'woocommerce_thumbnail';


$columns = puca_tbay_get_config('number_product_thumbnail', 3);

$post_thumbnail_id = get_post_thumbnail_id( $product->get_id() );

$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'woocommerce_thumbnail' );
$placeholder       = has_post_thumbnail() ? 'with-images' : 'without-images';
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . $placeholder,
	'images',
	'tbay-gallery-varible'
) );


?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		$attributes = array(
			'title'  => get_post_field( 'post_title', $post_thumbnail_id ),
		);


		if ( has_post_thumbnail() ) {
			$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $product->get_id(), 'woocommerce_gallery_thumbnail' ) . '" class="woocommerce-product-gallery__image">';


            if( puca_tbay_get_global_config('enable_lazyloadimage',false) ) {
            	$html .= sprintf( '<img src="%s" alt="%s" data-src="%s" class="wp-post-image unveil-image" />', $src_blank , $attributes['title'],  wp_get_attachment_image_url($post_thumbnail_id, $size)  );
            } else {
            	$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', wp_get_attachment_image_url($post_thumbnail_id, $size) , $attributes['title']  );
            	
            }

			$html .= '</div>';
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';

			if( puca_tbay_get_global_config('enable_lazyloadimage',false) ) {
				$html .= sprintf( '<img src="%s" alt="%s" data-src="%s" class="wp-post-image unveil-image" />', $src_blank , esc_html__( 'Awaiting product image', 'puca' ),  wc_placeholder_img_src()  );
            } else {
				$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', wc_placeholder_img_src() , esc_html__( 'Awaiting product image', 'puca' )  );
            }



			$html .= '</div>';
		}


		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $product->get_id() ) );

		?>
	</figure>
</div>
