<?php
/**
 * Tabs
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( empty( $args['tabs'] ) ) {
	return;
}

$section_class = empty( $section_class ) ? 'section-tabs' : $section_class . ' section-tabs';

if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$default_active_tab = isset( $default_active_tab ) && ( $default_active_tab >= 1 ) ? ( $default_active_tab - 1 ) : 1;

$tab_uniqid = 'tab-' . uniqid();
?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( ! empty( $animation ) ): ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>

	<ul class="nav nav-inline">

		<?php foreach( $args['tabs'] as $key => $tab ) :

			$tab_id = $tab_uniqid . $key; ?>

			<li class="nav-item <?php if ( $key == $default_active_tab ) echo esc_attr( 'active' ); ?>">
				<a class="nav-link" href="#<?php echo esc_attr( $tab_id ); ?>" data-toggle="tab">
					<?php echo wp_kses_post ( $tab['title'] ); ?>
				</a>
			</li>

		<?php endforeach; ?>

	</ul>

	<div class="tab-content">

		<?php foreach( $args['tabs'] as $key => $tab ) :

			$tab_id = $tab_uniqid . $key; ?>

			<div class="tab-pane <?php if ( $key == $default_active_tab ) echo esc_attr( 'active' ); ?>" id="<?php echo esc_attr( $tab_id ); ?>" role="tabpanel">
				<?php
					$shortcode_tag	= isset( $tab['shortcode_tag'] ) ? $tab['shortcode_tag'] : 'recent_products';
					$default_atts 	= array( 'per_page' => intval( $limit ), 'columns' => intval( $columns ) );
					$atts 			= isset( $tab['shortcode_atts'] ) ? $tab['shortcode_atts'] : array();
					$atts 			= wp_parse_args( $atts, $default_atts );
					$products 		= Pizzaro_Products::$shortcode_tag( $atts );
				?>
				<div class="section-products-4-1">
					<div class="products-4-1">
						<div class="woocommerce columns-<?php echo esc_attr( $columns ); ?>">
							<ul class="products exclude-auto-height">
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
										
										if ( $products_count == 4 ) {
											echo '</ul></div>';
											echo '<div class="woocommerce columns-1"><ul class="products exclude-auto-height">';
											remove_action( 'woocommerce_before_shop_loop_item',			'woocommerce_template_loop_product_link_open',	10  );
											remove_action( 'woocommerce_before_shop_loop_item_title',	'woocommerce_template_loop_product_thumbnail',	10  );
											remove_action( 'woocommerce_shop_loop_item_title',			'woocommerce_template_loop_product_link_close',	0  );
											add_action( 'woocommerce_before_shop_loop_item_title',		'pizzaro_product_4_1_gallery_images',			10  );
										}
										
										wc_get_template_part( 'content', 'product' );

										if ( $products_count == 4 ) {
											remove_action( 'woocommerce_before_shop_loop_item_title',	'pizzaro_product_4_1_gallery_images',			10  );
											add_action( 'woocommerce_before_shop_loop_item',			'woocommerce_template_loop_product_link_open',	10  );
											add_action( 'woocommerce_before_shop_loop_item_title',		'woocommerce_template_loop_product_thumbnail',	10  );
											add_action( 'woocommerce_shop_loop_item_title',				'woocommerce_template_loop_product_link_close',	0  );
										}

										$products_count++;

									endwhile;

									woocommerce_reset_loop();
									wp_reset_postdata();
								}
							?>
							</ul>
						</div>
					</div>
				</div>
			</div>

		<?php endforeach; ?>

	</div>
</div>
