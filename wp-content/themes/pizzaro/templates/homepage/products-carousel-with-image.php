<?php
/**
 * Products Carousel with Image
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $pizzaro_version;

$section_class = empty( $section_class ) ? 'section-products-carousel-with-image' : $section_class . ' section-products-carousel-with-image';

if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$style_attr = '';
if( isset( $bg_choice ) && $bg_choice == 'color' && ! empty( $bg_color ) ) {
	$style_attr = 'background-color:' . $bg_color  . '; height:' . $height . 'px;';
} elseif ( ! empty( $bg_image[0] ) ) {
	$style_attr = 'background-size: cover; background-position: center center; background-image: url( ' . esc_url( $bg_image[0] ) . ' ); height: ' . esc_attr( $bg_image[2] ) . 'px;';
}

$carousel_id	= 'products-carousel-' . uniqid();

$default_atts 	= array( 'per_page' => intval( $limit ), 'columns' => intval( $columns ) );
$atts 			= isset( $shortcode_atts ) ? $shortcode_atts : array();
$atts 			= wp_parse_args( $atts, $default_atts );
$products 		= Pizzaro_Products::$shortcode_tag( $atts );

$category_args	= pizzaro_get_atts_for_taxonomy_slugs( $category_args );
$categories		= get_terms( 'product_cat',  $category_args );

if ( $products->have_posts() ) :
?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?> <?php if ( !empty( $style_attr ) ) : ?>style="<?php echo esc_attr( $style_attr );?>"<?php endif; ?>>

	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-6">
				<?php if ( ! empty( $image ) ) : ?>
					<?php echo pizzaro_get_image( $image ); ?>
				<?php endif; ?>
			</div>
			<div class="col-md-6 col-sm-6">
				<div id="<?php echo esc_attr( $carousel_id ); ?>" class="woocommerce columns-<?php echo esc_attr( $columns ); ?>">
					<?php if ( ! empty( $section_title ) ) : ?>
						<h2 class="section-title"><?php echo wp_kses_post( $section_title ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $sub_title ) ) : ?>
						<h3 class="sub-title"><?php echo wp_kses_post( $sub_title ); ?></h3>
					<?php endif; ?>

					<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
						<ul class="nav nav-inline product-categories">
							<li class="nav-item"><?php echo sprintf( '<h5>%s<span>%s</span></h5>', esc_html__( 'Show:', 'pizzaro' ), esc_html__( 'All', 'pizzaro' ) ); ?></li>
							<?php foreach( $categories as $category ) : ?>
								<li class="nav-item"><a class="nav-link" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<div class="products owl-carousel">
					<?php
						$products_count = 0;
						if ( $products->have_posts() ) {

							if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
								global $woocommerce_loop;
								$woocommerce_loop['columns'] = $columns;
							} else {
								wc_set_loop_prop( 'columns', $columns );
							}

							while ( $products->have_posts() ) : $products->the_post();
								global $post;
												
								if( ! is_a( $post, 'WP_Query' ) && is_numeric( $post ) ) {
									$post_object = get_post( $post );
									$GLOBALS['post'] =& $post_object; // WPCS: override ok.
									setup_postdata( $post_object );
								}

								echo '<div class="product">';
								woocommerce_template_loop_product_link_open();
								echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' );
								woocommerce_template_loop_product_title();
								woocommerce_template_loop_product_link_close();
								echo '</div>';

							endwhile;

							woocommerce_reset_loop();
							wp_reset_postdata();
						}
					?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		jQuery(document).ready( function($){
			$( '#<?php echo esc_attr( $carousel_id ); ?> .owl-carousel').owlCarousel( <?php echo json_encode( $carousel_args );?> );
		} );
	</script>

</div>

<?php
wp_enqueue_script( 'owl-carousel-js', 	get_template_directory_uri() . '/assets/js/owl.carousel.min.js', array( 'jquery' ), $pizzaro_version, true );
endif;
