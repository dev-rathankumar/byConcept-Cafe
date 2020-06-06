<?php
/**
 * WooCommerce Template Loop Functions.
 *
 * @package pizzaro
 */

if ( ! function_exists( 'pizzaro_get_shop_layout' ) ) {
	function pizzaro_get_shop_layout() {
		$layout = apply_filters( 'pizzaro_shop_layout', 'left-sidebar' );

		return $layout;
	}
}

if ( ! function_exists( 'pizzaro_get_shop_style' ) ) {
	function pizzaro_get_shop_style() {
		$style = apply_filters( 'pizzaro_shop_style', '' );

		return $style;
	}
}

if ( ! function_exists( 'pizzaro_get_shop_view' ) ) {
	function pizzaro_get_shop_view() {
		$view = apply_filters( 'pizzaro_shop_view', 'grid-view' );

		return $view;
	}
}

if ( ! function_exists( 'pizzaro_get_shop_category_layout' ) ) {
	function pizzaro_get_shop_category_layout( $layout ) {
		if( is_product_category() ) {
			$term 		= get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') );
			$term_id 	= isset( $term->term_id ) ? intval( $term->term_id ) : 0;
			if( $term_id ) {
				$cat_layout = get_term_meta( $term_id, 'display_layout', true );
			}
		}

		if( ! empty( $cat_layout ) ) {
			$layout = $cat_layout;
		}

		return $layout;
	}
}

if ( ! function_exists( 'pizzaro_get_shop_category_style' ) ) {
	function pizzaro_get_shop_category_style( $style ) {
		if( is_product_category() && pizzaro_get_shop_layout() == 'full-width' ) {
			$term 		= get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') );
			$term_id 	= isset( $term->term_id ) ? intval( $term->term_id ) : 0;
			if( $term_id ) {
				$cat_style 	= get_term_meta( $term_id, 'display_style', true );
			}
		}

		if( ! empty( $cat_style ) ) {
			$style = $cat_style;
		}

		return $style;
	}
}

if ( ! function_exists( 'pizzaro_get_shop_category_view' ) ) {
	function pizzaro_get_shop_category_view( $view ) {
		if( is_product_category() ) {
			$term 		= get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') );
			$term_id 	= isset( $term->term_id ) ? intval( $term->term_id ) : 0;
			if( $term_id ) {
				$cat_view 	= get_term_meta( $term_id, 'display_view', true );
			}
		}

		if( ! empty( $cat_view ) ) {
			$view = $cat_view;
		}

		return $view;
	}
}

if ( ! function_exists( 'pizzaro_template_loop_hover' ) ) {
	/**
	 * Calls pizzaro loop hover
	 */
	function pizzaro_template_loop_hover() {
		?><div class="hover-area"><?php
		/**
		 * @hooked woocommerce_template_loop_add_to_cart - 20
		 */
		do_action( 'pizzaro_product_item_hover_area' );
		?></div><?php
	}
}

if ( ! function_exists( 'pizzaro_set_loop_shop_columns' ) ) {
	/**
	 * Sets Shop Loop Columns
	 */
	function pizzaro_set_loop_shop_columns() {
		
		$columns = apply_filters( 'pizzaro_loop_shop_columns', 3 ); // 3 products per row

		if( pizzaro_get_shop_view() == 'list-view' && ( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_label' ) || is_tax( get_object_taxonomies( 'product' ) ) ) ) {
			$columns = ( $columns % 2 ? 1 : 2 );
		} elseif( pizzaro_get_shop_view() == 'list-no-image-view' && ( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_label' ) || is_tax( get_object_taxonomies( 'product' ) ) ) ) {
			$columns = 1;
		}

		return intval( $columns );
	}
}

if ( ! function_exists( 'pizzaro_set_loop_shop_subcategories_columns' ) ) {
	function pizzaro_set_loop_shop_subcategories_columns() {
		return apply_filters( 'pizzaro_shop_loop_subcategories_columns', 3 );
	}
}

if ( ! function_exists( 'pizzaro_woocommerce_pagination_args' ) ) {
	function pizzaro_woocommerce_pagination_args( $args ) {
		$args['next_text'] = _x( 'Next Page &nbsp;&nbsp;&nbsp;&rarr;', 'Next page', 'pizzaro' );
		$args['prev_text'] = _x( '&larr;&nbsp;&nbsp;&nbsp; Previous Page', 'Previous page', 'pizzaro' );

		return $args;
	}
}

if ( ! function_exists( 'pizzaro_single_product_archive_thumbnail_size' ) ) {
	function pizzaro_single_product_archive_thumbnail_size( $image_size ) {
		
		$columns = pizzaro_set_loop_shop_columns();
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
			global $woocommerce_loop;
			if( isset( $woocommerce_loop['columns'] ) && intval( $woocommerce_loop['columns'] ) ) {
				$columns = $woocommerce_loop['columns'];
			}
		} else {
			$columns = wc_get_loop_prop( 'columns', $columns );
		}
		
		$layout = pizzaro_get_shop_layout();
		$view = pizzaro_get_shop_view();
		$style = pizzaro_get_shop_style();

		if( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_label' ) || is_tax( get_object_taxonomies( 'product' ) ) ) {
			if( $columns == 1 && $view == 'list-view' && $layout == 'full-width' ) {
				$image_size = 'pizzaro-product-list-fw-col-1';
			} elseif( $columns == 2 && $view == 'list-view' && $layout == 'full-width' ) {
				$image_size = 'pizzaro-product-list-fw-col-2';
			} elseif( $style == 'dark' && $view == 'grid-view' && $layout == 'full-width' ) {
				$image_size = 'pizzaro-product-dark-catalog';
			}
		}

		return $image_size;
	}
}

if( ! function_exists( 'pizzaro_shop_archive_header' ) ) {
	function pizzaro_shop_archive_header() {
		$args = array();

		if ( is_search() && is_shop() ) {
			$args['title'] = sprintf( esc_html__( 'Search Results: &ldquo;%s&rdquo;', 'pizzaro' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				$args['title'] .= sprintf( esc_html__( '&nbsp;&ndash; Page %s', 'pizzaro' ), get_query_var( 'paged' ) );
			}

		} elseif( is_shop() ) {
			$post_id 				= wc_get_page_id( 'shop' );
			if( has_post_thumbnail( $post_id ) ) {
				$args['image'] 		= get_post_thumbnail_id( $post_id );
			}
		} elseif ( is_product_category() ) {
			$term 					= get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') );
			$args['image'] 			= get_term_meta( $term->term_id, 'thumbnail_id', true );
			$args['title'] 			= single_term_title( "", false );
			$args['description'] 	= wc_format_content( term_description() );
		} elseif( is_tax( get_object_taxonomies( 'product' ) ) ) {
			$args['title'] 			= single_term_title( "", false );
			$args['description'] 	= wc_format_content( term_description() );
		}

		$args = apply_filters( 'pizzaro_shop_archive_header_args', $args );

		if( ! empty( $args ) && apply_filters( 'pizzaro_shop_archive_header', true ) ) {
			if( ! empty( $args['image'] ) ) {
				$image_attributes = wp_get_attachment_image_src( $args['image'], 'full' );
				if( $image_attributes[1] > 1170 || apply_filters( 'pizzaro_shop_archive_header_is_bg_image_width', true ) ) {
					$image_url = $image_attributes[0];
				}
			}
			?>
			<div class="shop-archive-header<?php if ( ! empty( $image_url ) ) : ?> has-bg-image<?php endif; ?>" <?php if ( ! empty( $image_url ) ) : ?>style="background-image: url( <?php echo esc_url( $image_url ); ?> ); height:<?php echo esc_attr( $image_attributes[2] ); ?>px;"<?php endif; ?>>
				<div class="shop-archive-content">
					<?php if( ! empty( $args['title'] ) ) : ?>
						<h3 class="title"><?php echo wp_kses_post( $args['title'] ); ?></h3>
					<?php endif; ?>
					<?php if( ! empty( $args['description'] ) ) :
						echo wp_kses_post( $args['description'] );
					endif; ?>

					<?php if( apply_filters( 'pizzaro_shop_archive_header_breadcrumb', true ) ) :
						$args = apply_filters( 'pizzaro_shop_archive_header_breadcrumb_defaults', array(
							'delimiter'   => '<span class="delimiter"><i class="po po-arrow-right-slider"></i></span>'
						) );
						woocommerce_breadcrumb( $args );
					endif; ?>
				
				</div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_product_gallery_images' ) ) {
	function pizzaro_product_gallery_images( $image_size = 'shop_catalog', $carousel_args = array() ) {
		global $post, $product, $woocommerce;

		if( empty( $image_size ) ) {
			$image_size = 'shop_catalog';
		}

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
			$attachment_ids = $product->get_gallery_attachment_ids();
		} else {
			$attachment_ids = $product->get_gallery_image_ids();
		}

		if ( has_post_thumbnail() ) {
			$thumbnail_id = get_post_thumbnail_id( $post->ID );
			array_unshift( $attachment_ids, $thumbnail_id );
		}

		$default_carousel_args = array(
			'items'				=> 1,
			'nav'				=> true,
			'slideSpeed'		=> 300,
			'dots'				=> false,
			'rtl'				=> is_rtl() ? true : false,
			'paginationSpeed'	=> 400,
			'navText'			=> is_rtl() ? array( '<i class="fa fa-angle-right"></i>', '<i class="fa fa-angle-left"></i>' ) : array( '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' ),
			'margin'			=> 0,
			'touchDrag'			=> true
		);

		$carousel_args 	= wp_parse_args( $carousel_args, $default_carousel_args );

		if ( $attachment_ids ) {
			global $pizzaro_version;
			$carousel_id = 'product-thumbnails-' . uniqid();
			wp_enqueue_script( 'owl-carousel-js', 	get_template_directory_uri() . '/assets/js/owl.carousel.min.js', array( 'jquery' ), $pizzaro_version, true );
			?>
			<div id="<?php echo esc_attr( $carousel_id ); ?>">
				<div class="thumbnails owl-carousel"><?php
					foreach ( $attachment_ids as $attachment_id ) {
						$props	= wc_get_product_attachment_props( $attachment_id, $post );
						echo '<div>' . wp_get_attachment_image( $attachment_id, $image_size, 0, $props ) . '</div>';
					}
				?></div>
				<script type="text/javascript">
					jQuery(document).ready( function($){
						var $owl = $( '#<?php echo esc_attr( $carousel_id ); ?> .owl-carousel');
						$owl.owlCarousel( <?php echo json_encode( $carousel_args );?> );
						if( $( '#<?php echo esc_attr( $carousel_id ); ?>' ).closest( '.section-tabs' ).length > 0 ) {
							$('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
								$owl.owlCarousel( <?php echo json_encode( $carousel_args );?> );
							}).on('hide.bs.tab', function () {
								$owl.trigger('destroy.owl.carousel');
							});
						}
					} );
				</script>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_template_loop_product_gallery_images' ) ) {
	function pizzaro_template_loop_product_gallery_images() {

		$carousel_args = array(
			'items'				=> 1,
			'nav'				=> true,
			'slideSpeed'		=> 300,
			'dots'				=> false,
			'rtl'				=> is_rtl() ? true : false,
			'paginationSpeed'	=> 400,
			'navText'			=> is_rtl() ? array( '<i class="po po-arrow-right-slider"></i>', '<i class="po po-arrow-left-slider"></i>' ) : array( '<i class="po po-arrow-left-slider"></i>', '<i class="po po-arrow-right-slider"></i>' ),
			'margin'			=> 0,
			'touchDrag'			=> true
		);

		pizzaro_product_gallery_images( 'shop_catalog', $carousel_args );
	}
}

if ( ! function_exists( 'pizzaro_product_4_1_gallery_images' ) ) {
	function pizzaro_product_4_1_gallery_images() {

		$carousel_args = array(
			'items'				=> 1,
			'nav'				=> true,
			'slideSpeed'		=> 300,
			'dots'				=> false,
			'rtl'				=> is_rtl() ? true : false,
			'paginationSpeed'	=> 400,
			'navText'			=> is_rtl() ? array( '<i class="po po-arrow-right"></i>', '<i class="po po-arrow-left"></i>' ) : array( '<i class="po po-arrow-left"></i>', '<i class="po po-arrow-right"></i>' ),
			'margin'			=> 0,
			'touchDrag'			=> true
		);

		pizzaro_product_gallery_images( 'shop_single', $carousel_args );
	}
}

if ( ! function_exists ( 'pizzaro_template_loop_product_image_wrap_open' ) ) {
	/**
	 *
	 */
	function pizzaro_template_loop_product_image_wrap_open() {
		?>
		<div class="product-image-wrapper">
		<?php
	}
}

if ( ! function_exists( 'pizzaro_template_loop_product_image_wrap_close' ) ) {
	/**
	 *
	 */
	function pizzaro_template_loop_product_image_wrap_close() {
		?>
		</div>
		<?php
	}
}

if ( ! function_exists ( 'pizzaro_template_loop_product_wrap_open' ) ) {
	/**
	 *
	 */
	function pizzaro_template_loop_product_wrap_open() {
		?>
		<div class="product-content-wrapper">
		<?php
	}
}

if ( ! function_exists( 'pizzaro_template_loop_product_wrap_close' ) ) {
	/**
	 *
	 */
	function pizzaro_template_loop_product_wrap_close() {
		?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_template_loop_product_outer_wrap_open' ) ) {
	/**
	 * Wraps product with product-outer div
	 */
	function pizzaro_template_loop_product_outer_wrap_open() {
		?><div class="product-outer"><?php
	}
}

if ( ! function_exists( 'pizzaro_template_loop_product_inner_wrap_open' ) ) {
	/**
	 * Wraps product with product-inner div
	 */
	function pizzaro_template_loop_product_inner_wrap_open() {
		?><div class="product-inner"><?php
	}
}

if ( ! function_exists( 'pizzaro_template_loop_product_inner_wrap_close' ) ) {
	/**
	 * Closes product-inner wrapper
	 */
	function pizzaro_template_loop_product_inner_wrap_close() {
		?></div><!-- /.product-inner --><?php
	}
}

if ( ! function_exists( 'pizzaro_template_loop_product_outer_wrap_close' ) ) {
	/**
	 * Closes product-outer wrapper
	 */
	function pizzaro_template_loop_product_outer_wrap_close() {
		?></div><!-- /.product-outer --><?php
	}
}

if ( ! function_exists( 'pizzaro_template_product_food_type_icon' ) ) {
	/**
	 *
	 */
	function pizzaro_template_product_food_type_icon() {
		global $product;

		$product_id         = pizzaro_wc_get_product_id( $product );
		$food_type_tax      = pizzaro_get_food_type_taxonomy();
		$terms              = get_the_terms( $product_id, $food_type_tax );
		$food_type_icons    = '';

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$icon_class = get_term_meta( $term->term_id, 'icon_class', true );
				if( ! empty( $icon_class ) ) {
					$food_type_icons .= '<i class="' . esc_attr( $icon_class ) . '"></i>';
				}
			}
		}

		if ( ! empty( $food_type_icons ) ) : ?>
			<span class="food-type-icon">
				<?php echo wp_kses_post( $food_type_icons ) ?>
			</span>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_wc_products_per_page' ) ) {
	/**
	 * Outputs a dropdown for user to select how many products to show per page
	 */
	function pizzaro_wc_products_per_page() {

		global $wp_query;

		$action 			= '';
		$cat 				= '';
		$cat 				= $wp_query->get_queried_object();
		$method 			= apply_filters( 'pizzaro_wc_ppp_method', 'post' );
		$return_to_first 	= apply_filters( 'pizzaro_wc_ppp_return_to_first', false );
		$total    			= $wp_query->found_posts;
		$per_page 			= $wp_query->get( 'posts_per_page' );
		$_per_page 			= apply_filters( 'pizzaro_products_per_page', 12 );

		// Generate per page options
		$products_per_page_options = array();
		while( $_per_page < $total ) {
			$products_per_page_options[] = $_per_page;
			$_per_page = $_per_page * 2;
		}

		if ( empty( $products_per_page_options ) ) {
			return;
		}

		$products_per_page_options[] = -1;

		// Set action url if option behaviour is true
		// Paste QUERY string after for filter and orderby support
		$query_string = ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . add_query_arg( array( 'ppp' => false ), $_SERVER['QUERY_STRING'] ) : null;

		if ( isset( $cat->term_id ) && isset( $cat->taxonomy ) && $return_to_first ) :
			$action = get_term_link( $cat->term_id, $cat->taxonomy ) . $query_string;
		elseif ( $return_to_first ) :
			$action = get_permalink( wc_get_page_id( 'shop' ) ) . $query_string;
		endif;

		// Only show on product categories
		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		do_action( 'pizzaro_wc_ppp_before_dropdown_form' );

		?><form method="POST" action="<?php echo esc_url( $action ); ?>" class="form-pizzaro-wc-ppp"><?php

			 do_action( 'pizzaro_wc_ppp_before_dropdown' );

			?><select name="ppp" onchange="this.form.submit()" class="pizzaro-wc-wppp-select c-select"><?php

				foreach( $products_per_page_options as $key => $value ) :

					?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $per_page ); ?>><?php
						$ppp_text = apply_filters( 'pizzaro_wc_ppp_text', esc_html__( 'Show %s', 'pizzaro' ), $value );
						esc_html( printf( $ppp_text, $value == -1 ? esc_html__( 'All', 'pizzaro' ) : $value ) ); // Set to 'All' when value is -1
					?></option><?php

				endforeach;

			?></select><?php

			// Keep query string vars intact
			foreach ( $_GET as $key => $val ) :

				if ( 'ppp' === $key || 'submit' === $key ) :
					continue;
				endif;
				if ( is_array( $val ) ) :
					foreach( $val as $inner_val ) :
						?><input type="hidden" name="<?php echo esc_attr( $key ); ?>[]" value="<?php echo esc_attr( $inner_val ); ?>" /><?php
					endforeach;
				else :
					?><input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" /><?php
				endif;
			endforeach;

			do_action( 'pizzaro_wc_ppp_after_dropdown' );

		?></form><?php

		do_action( 'pizzaro_wc_ppp_after_dropdown_form' );
	}
}

if( ! function_exists( 'pizzaro_product_filter_widgets' ) ) {
	/**
	 * Filter Widgets
	 * @since 1.0.0
	 * @return void
	 */
	function pizzaro_product_filter_widgets() {

		// Only show on product categories
		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		if( is_active_sidebar( 'product-filters-1' ) ) {
			?>
			<div class="product-filters-widgets">
				<a class="dropdown-toggle" data-toggle="collapse" href="#products_filter_collapse" aria-expanded="false" aria-controls="products_filter_collapse">
					<?php echo esc_html__( 'More Filters', 'pizzaro' ); ?><i class="fa fa-chevron-down"></i>
				</a>

				<div class="collapse" id="products_filter_collapse">
					<?php dynamic_sidebar( 'product-filters-1' ); ?>
				</div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_product_food_type_filter_widget' ) ) {
	/**
	 *
	 */
	function pizzaro_product_food_type_filter_widget() {
		$food_type_attr = pizzaro_get_food_type_attr();
		if ( ! empty( $food_type_attr ) ) {
			$filter_name = 'filter_' . sanitize_title( $food_type_attr );
			$widget_class_name = 'WC_Widget_Layered_Nav';
			$args = array();
			$instance = array(
				'title'         => '',
				'attribute'     => $food_type_attr,
				'display_type'  => 'list',
				'query_type'    => 'and',
			);

			ob_start();
			the_widget( $widget_class_name, $instance, $args );
			$widget_content = ob_get_clean();

			$chosen_class = empty( $_GET[$filter_name] ) ? 'chosen' : '';
			if( ! empty( $widget_content ) ) {
				echo '<div class="clear-food-type-filter '. esc_attr( $chosen_class ) .'"><a href="' . esc_url( remove_query_arg( $filter_name ) ) . '">' . esc_html__( 'Show All', 'pizzaro' ) . '</a></div>';
				echo $widget_content;
				pizzaro_product_create_your_own_button();
			}
		}
	}
}

if( ! function_exists( 'pizzaro_product_food_type_filter' ) ) {
	/**
	 *
	 */
	function pizzaro_product_food_type_filter() {

		// Only show on product categories
		if ( ! woocommerce_products_will_display() ) {
			return;
		}

		$food_type_tax = pizzaro_get_food_type_taxonomy();
		if ( ! empty( $food_type_tax ) && taxonomy_exists( $food_type_tax ) ) {
			add_filter( 'woocommerce_layered_nav_count', 'pizzaro_food_type_filter_widget_title', 10, 3 );
			ob_start();
			pizzaro_product_food_type_filter_widget();
			$filter_content = ob_get_clean();
			if( ! empty( $filter_content ) ) {
				?>
				<div class="food-type-filter">
					<?php echo $filter_content; ?>
				</div>
				<?php
			}
			remove_filter( 'woocommerce_layered_nav_count', 'pizzaro_food_type_filter_widget_title', 10, 3 );
		}
	}
}

if ( ! function_exists( 'pizzaro_food_type_filter_widget_title' ) ) {
	/**
	 *
	 */
	function pizzaro_food_type_filter_widget_title( $count_html, $count, $term ) {
		$food_type_tax = pizzaro_get_food_type_taxonomy();
		if ( ! empty( $food_type_tax ) && taxonomy_exists( $food_type_tax ) && $food_type_tax == $term->taxonomy ) {
			$icon_class = get_term_meta( $term->term_id, 'icon_class', true );
			if( ! empty( $icon_class ) ) {
				$count_html = '<span class="food-type-icon"><i class="' . esc_attr( $icon_class ) . '"></i></span>' . $count_html;
			}
		}

		return $count_html;
	}
}

if ( ! function_exists( 'pizzaro_product_create_your_own_button' ) ) {
	/**
	 *
	 */
	function pizzaro_product_create_your_own_button() {

		// Only show on product categories
		if ( ! woocommerce_products_will_display() ) {
			return;
		}
		
		$button_args = apply_filters( 'pizzaro_create_your_own_button_args', array(
			'text'  => esc_html__( 'Create your own', 'pizzaro' ),
			'link'  => '#'
		) );

		if( apply_filters( 'pizzaro_enable_create_your_own_button', true ) && ! empty( $button_args['link'] ) && ! empty( $button_args['text'] ) ) {
			echo '<div class="create-your-own"><a href="' . esc_url( $button_args['link'] ) . '">' . esc_html( $button_args['text'] ) . '</a></div>';
		}
	}
}

if ( ! function_exists( 'pizzaro_product_subcategories' ) ) {
	/**
	 * Wrapper woocommerce_product_subcategories
	 *
	 */
	function pizzaro_product_subcategories() {
		
		$columns = pizzaro_set_loop_shop_subcategories_columns();
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
			global $woocommerce_loop;
			$woocommerce_loop[ 'columns' ] = $columns;
		} else {
			$display_type = woocommerce_get_loop_display_mode();
			if( ! in_array( $display_type, array( 'subcategories', 'both' ) ) ) {
				return;
			}
			if( wc_get_loop_prop( 'is_shortcode' ) ) {
				return;
			}
			wc_set_loop_prop( 'columns', $columns );
		}

		$class 		= 'woocommerce columns-' . $columns;
		$parent_id	= is_product_category() ? get_queried_object_id() : 0;
		$before 	= '<div class="' . esc_attr( $class ) . '"><ul class="product-loop-categories">';
		$after 		= '</ul></div>';

		do_action( 'pizzaro_before_product_subcategories' );

		if ( ! function_exists( 'woocommerce_output_product_categories' ) ) {
			woocommerce_product_subcategories( array( 'parent_id' => $parent_id, 'before' => $before, 'after' => $after ) );
		} else {
			woocommerce_output_product_categories( array( 'parent_id' => $parent_id, 'before' => $before, 'after' => $after ) );
		}

		do_action( 'pizzaro_after_product_subcategories' );

		$columns 	= pizzaro_set_loop_shop_columns();
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
			$woocommerce_loop[ 'columns' ] = $columns;
		} else {
			wc_set_loop_prop( 'columns', $columns );
			if ( 'subcategories' === $display_type ) {
				wc_set_loop_prop( 'total', 0 );
			}
		}
	}
}