<?php
/**
 * PowerPack WooCommerce Skin Grid - Default.
 *
 * @package PowerPack
 */

namespace PowerpackElements\Modules\Woocommerce\Skins;

use Elementor\Controls_Manager;
use Elementor\Skin_Base;
use Elementor\Widget_Base;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Grid_Base
 *
 * @property Products $parent
 */
abstract class Skin_Grid_Base extends Skin_Base {

	/**
	 * Change pagination arguments based on settings.
	 *
	 * @since 1.3.3
	 * @access protected
	 * @param string $located location.
	 * @param string $template_name template name.
	 * @param array  $args arguments.
	 * @param string $template_path path.
	 * @param string $default_path default path.
	 * @return string template location
	 */
	public function woo_pagination_template( $located, $template_name, $args, $template_path, $default_path ) {

		if ( 'loop/pagination.php' === $template_name ) {
			$located = POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/loop/pagination.php';
		}

		return $located;
	}

	/**
	 * Change pagination arguments based on settings.
	 *
	 * @since 1.3.3
	 * @access protected
	 * @param array $args pagination args.
	 * @return array
	 */
	public function woo_pagination_options( $args ) {

		$settings = $this->parent->get_settings();

		$pagination_arrow = false;

		if ( 'numbers_arrow' === $settings['pagination_type'] ) {
			$pagination_arrow = true;
		}

		$args['prev_next'] = $pagination_arrow;

		if ( '' !== $settings['pagination_prev_label'] ) {
			$args['prev_text'] = $settings['pagination_prev_label'];
		}

		if ( '' !== $settings['pagination_next_label'] ) {
			$args['next_text'] = $settings['pagination_next_label'];
		}

		return $args;
	}

	/**
	 * Get Wrapper Classes.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function set_slider_attr() {

		$settings = $this->parent->get_settings();

		if ( 'slider' !== $settings['products_layout_type'] ) {
			return;
		}

		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';

		$slick_options = [
			'slidesToShow'   => ( $settings['slides_to_show'] ) ? absint( $settings['slides_to_show'] ) : 4,
			'slidesToScroll' => ( $settings['slides_to_scroll'] ) ? absint( $settings['slides_to_scroll'] ) : 1,
			'autoplaySpeed'  => ( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'autoplay'       => ( 'yes' === $settings['autoplay'] ),
			'infinite'       => ( 'yes' === $settings['infinite'] ),
			'pauseOnHover'   => ( 'yes' === $settings['pause_on_hover'] ),
			'speed'          => ( $settings['transition_speed'] ) ? absint( $settings['transition_speed'] ) : 500,
			'arrows'         => ( 'yes' === $settings['arrows'] ),
			'dots'           => ( 'yes' === $settings['carousel_pagination'] ),
			'rtl'            => $is_rtl,
			'prevArrow'      => '<div class="pp-slider-arrow slick-prev slick-arrow"><i class="fa fa-angle-left"></i></div>',
			'nextArrow'      => '<div class="pp-slider-arrow slick-next slick-arrow"><i class="fa fa-angle-right"></i></div>',
		];

		if ( $settings['slides_to_show_tablet'] || $settings['slides_to_show_mobile'] ) {

			$slick_options['responsive'] = [];

			if ( $settings['slides_to_show_tablet'] ) {

				$tablet_show   = absint( $settings['slides_to_show_tablet'] );
				$tablet_scroll = ( $settings['slides_to_scroll_tablet'] ) ? absint( $settings['slides_to_scroll_tablet'] ) : $tablet_show;

				$slick_options['responsive'][] = [
					'breakpoint' => 1024,
					'settings'   => [
						'slidesToShow'   => $tablet_show,
						'slidesToScroll' => $tablet_scroll,
					],
				];
			}

			if ( $settings['slides_to_show_mobile'] ) {

				$mobile_show   = absint( $settings['slides_to_show_mobile'] );
				$mobile_scroll = ( $settings['slides_to_scroll_mobile'] ) ? absint( $settings['slides_to_scroll_mobile'] ) : $mobile_show;

				$slick_options['responsive'][] = [
					'breakpoint' => 767,
					'settings'   => [
						'slidesToShow'   => $mobile_show,
						'slidesToScroll' => $mobile_scroll,
					],
				];
			}
		}

		$this->parent->add_render_attribute(
			'wrapper', [
				'data-woo_slider' => wp_json_encode( $slick_options ),
			]
		);
	}

	/**
	 * Render Query.
	 *
	 * @since 1.1.0
	 */
	public function render_query() {

		$this->parent->query_posts();
	}

	/**
	 * Render loop required arguments.
	 *
	 * @since 1.1.0
	 */
	public function render_loop_args() {

		$query = $this->parent->get_query();

		global $woocommerce_loop;

		$settings = $this->parent->get_settings();

		if ( 'grid' === $settings['products_layout_type'] ) {
			$woocommerce_loop['columns'] = (int) $settings['products_columns'];

			if ( 0 < $settings['products_per_page'] && '' !== $settings['pagination_type'] ) {
				/* Pagination */
				$paged                            = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$woocommerce_loop['paged']        = $paged;
				$woocommerce_loop['total']        = $query->found_posts;
				$woocommerce_loop['post_count']   = $query->post_count;
				$woocommerce_loop['per_page']     = $settings['products_per_page'];
				$woocommerce_loop['total_pages']  = ceil( $query->found_posts / $settings['products_per_page'] );
				$woocommerce_loop['current_page'] = $paged;
			}

			$this->parent->add_render_attribute(
				'inner', [
					'class' => [
						' columns-' . $woocommerce_loop['columns'],
					],
				]
			);
		} else {
			if ( ( $settings['arrows'] || $settings['carousel_pagination'] ) == 'yes'  ) {

				$this->parent->add_render_attribute(
					'inner', [
						'class' => [
							'pp-slick-dotted',
						],
					]
				);
			}
		}
	}

	/**
	 * Pagination Structure.
	 *
	 * @since 1.1.0
	 */
	public function render_pagination_structure() {

		$settings = $this->parent->get_settings();

		if ( '' !== $settings['pagination_type'] ) {
			add_filter( 'wc_get_template', [ $this, 'woo_pagination_template' ], 10, 5 );
			add_filter( 'pp_woocommerce_pagination_args', [ $this, 'woo_pagination_options' ] );
			woocommerce_pagination();
			remove_filter( 'pp_woocommerce_pagination_args', [ $this, 'woo_pagination_options' ] );
			remove_filter( 'wc_get_template', [ $this, 'woo_pagination_template' ], 10, 5 );
		}
	}

	/**
	 * Render wrapper start.
	 *
	 * @since 1.1.0
	 */
	public function render_wrapper_start() {

		$settings = $this->parent->get_settings();

		$this->set_slider_attr();

		$this->parent->add_render_attribute(
			'wrapper', [
				'class' => [
					'pp-woocommerce',
					'pp-woo-products-' . $settings['products_layout_type'],
					'pp-woo-skin-' . $this->get_id(),
				],
			]
		);

		echo '<div ' . $this->parent->get_render_attribute_string( 'wrapper' ) . '">';
	}

	/**
	 * Render wrapper end.
	 *
	 * @since 1.1.0
	 */
	public function render_wrapper_end() {
		echo '</div>';
	}

	/**
	 * Render inner container start.
	 *
	 * @since 1.1.0
	 */
	public function render_inner_start() {

		$settings = $this->parent->get_settings();

		$this->parent->add_render_attribute(
			'inner', [
				'class' => [
					'pp-woo-products-inner',
					'pp-woo-product__column-' . $settings['products_columns'],
					'pp-woo-product__column-tablet-' . $settings['products_columns_tablet'],
					'pp-woo-product__column-mobile-' . $settings['products_columns_mobile'],
				],
			]
		);

		if ( '' !== $settings['products_hover_style'] ) {
			$this->parent->add_render_attribute(
				'inner', [
					'class' => [
						'pp-woo-product__hover-' . $settings['products_hover_style'],
					],
				]
			);
		}

		echo '<div ' . $this->parent->get_render_attribute_string( 'inner' ) . '>';
	}

	/**
	 * Render inner container end.
	 *
	 * @since 1.1.0
	 */
	public function render_inner_end() {
		echo '</div>';
	}

	/**
	 * Render woo loop start.
	 *
	 * @since 1.1.0
	 */
	public function render_woo_loop_start() {
		woocommerce_product_loop_start();
	}

	/**
	 * Render woo loop.
	 *
	 * @since 1.1.0
	 */
	public function render_woo_loop() {

		$query = $this->parent->get_query();

		while ( $query->have_posts() ) :
			$query->the_post();
			$this->render_woo_loop_template();
		endwhile;
	}

	/**
	 * Render woo default template.
	 *
	 * @since 1.1.0
	 */
	public function render_woo_loop_template() {

		$settings = $this->parent->get_settings();

		include POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/content-product-skin-1.php';
	}
	/**
	 * Render woo loop end.
	 *
	 * @since 1.1.0
	 */
	public function render_woo_loop_end() {
		woocommerce_product_loop_end();
	}

	/**
	 * Render reset loop.
	 *
	 * @since 1.1.0
	 */
	public function render_reset_loop() {

		woocommerce_reset_loop();

		wp_reset_postdata();
	}

	/**
	 * Quick View.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function quick_view_modal() {

		$settings = $this->parent->get_settings();

		$quick_view_type = $settings['quick_view_type'];

		if ( '' !== $quick_view_type ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
			wp_enqueue_script( 'flexslider' );

			$widget_id = $this->parent->get_id();

			include POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/quick-view-modal.php';
		}
	}

	/**
	 * Get Best Selling Product for Badge.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function is_best_selling_product( $product_id ) {

		$settings = $this->parent->get_settings();
		$number_of_sales = $settings['number_of_sales'];

		if ( empty( $number_of_sales ) ) {
			return false;
		}

		$total_sales = get_post_meta( $product_id, 'total_sales', true );

		if ( ! $total_sales || empty( $total_sales ) ) {
			return false;
		}

		return $total_sales >= $number_of_sales;
	}

	/**
	 * Get Top Rated Product for Badge.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function is_top_rated_product( $product_id ) {

		$settings = $this->parent->get_settings();
		$rating = $settings['number_of_ratings'];

		if ( empty( $rating ) ) {
			return false;
		}

		$total_rating = get_post_meta( $product_id, '_wc_average_rating', true );

		if ( ! $total_rating || empty( $total_rating ) ) {
			return false;
		}

		return $total_rating >= $rating;
	}

	/**
	 * Get Best Selling Product for Badge 1.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function is_best_selling_product_1( $product_id ) {

		$settings = $this->parent->get_settings();
		$number_of_sales = $settings['number_of_sales_1'];

		if ( empty( $number_of_sales ) ) {
			return false;
		}

		$total_sales = get_post_meta( $product_id, 'total_sales', true );

		if ( ! $total_sales || empty( $total_sales ) ) {
			return false;
		}

		return $total_sales >= $number_of_sales;
	}

	/**
	 * Get Top Rated Product for Badge 1.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function is_top_rated_product_1( $product_id ) {

		$settings = $this->parent->get_settings();
		$rating = $settings['number_of_rating_1'];

		if ( empty( $rating ) ) {
			return false;
		}

		$total_rating = get_post_meta( $product_id, '_wc_average_rating', true );

		if ( ! $total_rating || empty( $total_rating ) ) {
			return false;
		}

		return $total_rating >= $rating;
	}

	/**
	 * Get Best Selling Product for Badge 1.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function is_best_selling_product_2( $product_id ) {

		$settings = $this->parent->get_settings();
		$number_of_sales = $settings['number_of_sales_2'];

		if ( empty( $number_of_sales ) ) {
			return false;
		}

		$total_sales = get_post_meta( $product_id, 'total_sales', true );

		if ( ! $total_sales || empty( $total_sales ) ) {
			return false;
		}

		return $total_sales >= $number_of_sales;
	}

	/**
	 * Get Top Rated Product for Badge 1.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function is_top_rated_product_2( $product_id ) {

		$settings = $this->parent->get_settings();
		$rating = $settings['number_of_rating_2'];

		if ( empty( $rating ) ) {
			return false;
		}

		$total_rating = get_post_meta( $product_id, '_wc_average_rating', true );

		if ( ! $total_rating || empty( $total_rating ) ) {
			return false;
		}

		return $total_rating >= $rating;
	}

	/**
	 * Render Content.
	 *
	 * @since 1.3.3
	 * @access protected
	 */
	public function render() {

		$this->render_query();

		$query = $this->parent->get_query();

		if ( ! $query->have_posts() ) {
			return;
		}

		$this->render_loop_args();
		$this->render_wrapper_start();
			$this->render_inner_start();
				$this->render_woo_loop_start();
					$this->render_woo_loop();
				$this->render_woo_loop_end();
				$this->render_pagination_structure();
				$this->render_reset_loop();
			$this->render_inner_end();
		$this->render_wrapper_end();

		$this->quick_view_modal();
		
	}
}
