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

$section_class = empty( $section_class ) ? 'stretch-full-width products-with-gallery-tabs section-tabs' : $section_class . ' stretch-full-width products-with-gallery-tabs section-tabs';

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
				<div class="section-products-with-gallery section-products">
					<?php

						
						$default_atts 	= array( 'per_page' => intval( $limit ), 'columns' => intval( $columns ) );
						$atts 			= isset( $tab['shortcode_atts'] ) ? $tab['shortcode_atts'] : array();
						$atts 			= wp_parse_args( $atts, $default_atts );
						
						remove_action( 'woocommerce_before_shop_loop_item',			'woocommerce_template_loop_product_link_open',	10  );
						remove_action( 'woocommerce_before_shop_loop_item_title',	'woocommerce_template_loop_product_thumbnail',	10  );
						remove_action( 'woocommerce_shop_loop_item_title',			'woocommerce_template_loop_product_link_close',	0  );
						add_action( 'woocommerce_before_shop_loop_item_title',		'pizzaro_template_loop_product_gallery_images',	10  );
						echo pizzaro_do_shortcode( $tab['shortcode_tag'],  $atts );
						remove_action( 'woocommerce_before_shop_loop_item_title',	'pizzaro_template_loop_product_gallery_images',	10  );
						add_action( 'woocommerce_before_shop_loop_item',			'woocommerce_template_loop_product_link_open',	10  );
						add_action( 'woocommerce_before_shop_loop_item_title',		'woocommerce_template_loop_product_thumbnail',	10  );
						add_action( 'woocommerce_shop_loop_item_title',				'woocommerce_template_loop_product_link_close',	0  );
					?>
				</div>
			</div>

		<?php endforeach; ?>

	</div>
</div>
