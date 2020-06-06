<?php
/**
 * Filter functions for Shop Section of Theme Options
 */

if( ! function_exists( 'redux_toggle_shop_catalog_mode' ) ) {
	function redux_toggle_shop_catalog_mode() {
		global $pizzaro_options;

		if( isset( $pizzaro_options['catalog_mode'] ) && $pizzaro_options['catalog_mode'] == '1' ) {
			$catalog_mode = true;
		} else {
			$catalog_mode = false;
		}

		return $catalog_mode;
	}
}

function redux_apply_catalog_mode_for_product_loop( $product_link, $product ) {
	global $pizzaro_options;

	$product_id = pizzaro_wc_get_product_id( $product );
	$product_type = pizzaro_wc_get_product_type( $product );
	if( isset( $pizzaro_options['catalog_mode'] ) && $pizzaro_options['catalog_mode'] == '1' ) {
		$product_link = sprintf( '<a href="%s" class="button product_type_%s">%s</a>',
			get_permalink( $product_id ),
			esc_attr( $product_type ),
			apply_filters( 'pizzaro_catalog_mode_button_text', esc_html__( 'View Product', 'pizzaro' ) )
		);
	}

	return $product_link;
}

if( ! function_exists( 'redux_apply_product_food_type_taxonomy' ) ) {
	function redux_apply_product_food_type_taxonomy( $brand_taxonomy ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['food_type_taxonomy'] ) ) {
			$brand_taxonomy = $pizzaro_options['food_type_taxonomy'];
		}

		return $brand_taxonomy;
	}
}

if ( ! function_exists( 'redux_apply_shop_loop_products_columns' ) ) {
	function redux_apply_shop_loop_products_columns( $columns ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['product_columns'] ) ) {
			$columns = $pizzaro_options['product_columns'];
		}

		return $columns;
	}
}

if ( ! function_exists( 'redux_apply_shop_loop_subcategories_columns' ) ) {
	function redux_apply_shop_loop_subcategories_columns( $columns ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['subcategory_columns'] ) ) {
			$columns = $pizzaro_options['subcategory_columns'];
		}

		return $columns;
	}
}

if ( ! function_exists( 'redux_apply_shop_loop_per_page' ) ) {
	function redux_apply_shop_loop_per_page( $per_page ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['products_per_page'] ) ) {
			$per_page = $pizzaro_options['products_per_page'];
		}

		return $per_page;
	}
}

if ( ! function_exists( 'redux_apply_shop_layout' ) ) {
	function redux_apply_shop_layout( $shop_layout ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['shop_layout'] ) ) {
			$shop_layout = $pizzaro_options['shop_layout'];
		}

		return $shop_layout;
	}
}

if ( ! function_exists( 'redux_apply_shop_style' ) ) {
	function redux_apply_shop_style( $shop_style ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['shop_style'] ) ) {
			$shop_style = $pizzaro_options['shop_style'];
		}

		return $shop_style;
	}
}

if ( ! function_exists( 'redux_apply_shop_view' ) ) {
	function redux_apply_shop_view( $shop_view ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['shop_view'] ) ) {
			$shop_view = $pizzaro_options['shop_view'];
		}

		return $shop_view;
	}
}

if ( ! function_exists( 'redux_apply_single_product_layout' ) ) {
	function redux_apply_single_product_layout( $single_product_layout ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['single_product_layout'] ) ) {
			$single_product_layout = $pizzaro_options['single_product_layout'];
		}

		return $single_product_layout;
	}
}

if ( ! function_exists( 'redux_apply_single_product_layout_style' ) ) {
	function redux_apply_single_product_layout_style( $single_product_style ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['single_product_style'] ) ) {
			$single_product_style = $pizzaro_options['single_product_style'];
		}

		return $single_product_style;
	}
}

if ( ! function_exists( 'redux_apply_create_your_own_button_args' ) ) {
	function redux_apply_create_your_own_button_args( $args ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['create_your_own_button_text'] ) ) {
			$args['text'] = $pizzaro_options['create_your_own_button_text'];
		}

		if( isset( $pizzaro_options['create_your_own_button_link'] ) ) {
			$args['link'] = $pizzaro_options['create_your_own_button_link'];
		}

		return $args;
	}
}
