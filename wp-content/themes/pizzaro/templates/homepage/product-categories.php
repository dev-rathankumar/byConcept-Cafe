<?php
/**
 * Product Categories
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_class = empty( $section_class ) ? 'section-product-categories' : 'section-product-categories ' . $section_class;
if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$category_args = pizzaro_get_atts_for_taxonomy_slugs( $category_args );
$categories = get_terms( 'product_cat', $category_args );

?>
<section class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
	<?php if ( ! empty( $pre_title ) ) : ?>
		<h3 class="pre-title"><span><?php echo wp_kses_post( $pre_title ); ?></span></h3>
	<?php endif; ?>

	<?php if ( ! empty( $section_title ) ) : ?>
		<h2 class="section-title"><?php echo wp_kses_post( $section_title ); ?></h2>
	<?php endif; ?>

	<div class="categories">
		<?php
		foreach( $categories as $category ) :
			?>
			<div class="category">
				<a href="<?php echo esc_url( get_term_link( $category ) ); ?>">
					<?php
						$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true  );
						if ( $thumbnail_id ) {
							$image = wp_get_attachment_image_src( $thumbnail_id, 'pizzaro-home-category' );
							$image_url = $image[0];
						} else {
							$image_url = wc_placeholder_img_src();
						}

						if ( $image_url ) {
							$image_url = str_replace( ' ', '%20', $image_url );
							echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $category->name ) . '" />';
						}
					?>
					<div class="caption">
						<h4><?php echo esc_html( $category->name ); ?></h4>
					</div>
				</a>
			</div>
			<?php
		endforeach;
		?>
	</div>
</section>
