<?php
/**
 * Pizzaro template functions.
 *
 * @package pizzaro
 */

if ( ! function_exists( 'pizzaro_breadcrumb' ) ) {
	/**
	 * Display pizzaro breadcrumb
	 *
	 * @uses woocommerce_breadcrumb()
	 * @since 1.0.0
	 */
	function pizzaro_breadcrumb( $args = array() ) {

		$args = wp_parse_args( $args, apply_filters( 'pizzaro_breadcrumb_defaults', array(
			'delimiter'   => '<span class="delimiter"><i class="po po-arrow-right-slider"></i></span>',
			'wrap_before' => '<div class="pizzaro-breadcrumb"><nav class="woocommerce-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
			'wrap_after'  => '</nav></div>',
			'before'      => '',
			'after'       => '',
			'home'        => _x( 'Home', 'breadcrumb', 'pizzaro' )
		) ) );

		if ( is_woocommerce_activated() && apply_filters( 'pizzaro_show_breadcrumb' , true ) ) {
			woocommerce_breadcrumb( $args );
		}

	}
}

if ( ! function_exists( 'pizzaro_toggle_breadcrumb' ) ) {
	/**
	 *
	 */
	function pizzaro_toggle_breadcrumb( $show_breadcrumb ) {
		global $post;

		if ( isset( $post->ID ) ){
			$page_meta_values = get_post_meta( $post->ID, '_pizzaro_page_metabox', true );

			if ( isset( $page_meta_values['hide_breadcrumb'] ) && $page_meta_values['hide_breadcrumb'] == '1' ) {
				$show_breadcrumb = false;
			} elseif ( is_home() ) {
				$show_breadcrumb = true;
			} elseif( 'post' == get_post_type() && ( is_category() || is_tag() || is_author() || is_date() || is_year() || is_month() || is_time() ) ) {
				$show_breadcrumb = true;
			} elseif ( is_single() && 'post' == get_post_type() ) {
				$show_breadcrumb = true;
			} elseif ( is_woocommerce_activated() && is_woocommerce() && ( is_shop() || is_product_category() || is_product_tag() || is_tax( 'product_label' ) || is_tax( get_object_taxonomies( 'product' ) ) ) ) {
				$show_breadcrumb = false;
			}
		}

		return $show_breadcrumb;
	}
}

if ( ! function_exists( 'pizzaro_get_sidebar' ) ) {
	/**
	 * Display pizzaro sidebar
	 *
	 * @uses get_sidebar()
	 * @since 1.0.0
	 */
	function pizzaro_get_sidebar( $name = null ) {
		get_sidebar( $name );
	}
}

if ( ! function_exists( 'pizzaro_toggle_sidebar' ) ) {
	/**
	 * [pizzaro_toggle_sidebar description]
	 *
	 * @param  [type] $has_sidebar [description]
	 * @return [type]              [description]
	 */
	function pizzaro_toggle_sidebar( $has_sidebar ) {

		$layout = pizzaro_get_blog_layout();

		if ( 'full-width' === $layout ) {

			$has_sidebar = false;

		} elseif ( 'right-sidebar' === $layout || 'left-sidebar' === $layout ) {

			$has_sidebar = true;

		}

		return $has_sidebar;
	}
}

if ( ! function_exists( 'pizzaro_init_structured_data' ) ) {
	/**
	 * Generate the structured data...
	 * Initialize Pizzaro::$structured_data via Pizzaro::set_structured_data()...
	 * Hooked into:
	 * `pizzaro_loop_post`
	 * `pizzaro_single_post`
	 * `pizzaro_page`
	 * Apply `pizzaro_structured_data` filter hook for structured data customization :)
	 */
	function pizzaro_init_structured_data() {
		if ( is_home() || is_category() || is_date() || is_search() || is_single() && ( is_woocommerce_activated() && ! is_woocommerce() ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'normal' );
			$logo  = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );

			$json['@type']            = 'BlogPosting';

			$json['mainEntityOfPage'] = array(
				'@type'                 => 'webpage',
				'@id'                   => get_the_permalink(),
			);

			$json['image']            = array(
				'@type'                 => 'ImageObject',
				'url'                   => $image[0],
				'width'                 => $image[1],
				'height'                => $image[2],
			);

			$json['publisher']        = array(
				'@type'                 => 'organization',
				'name'                  => get_bloginfo( 'name' ),
				'logo'                  => array(
					'@type'               => 'ImageObject',
					'url'                 => $logo[0],
					'width'               => $logo[1],
					'height'              => $logo[2],
				),
			);

			$json['author']           = array(
				'@type'                 => 'person',
				'name'                  => get_the_author(),
			);

			$json['datePublished']    = get_post_time( 'c' );
			$json['dateModified']     = get_the_modified_date( 'c' );
			$json['name']             = get_the_title();
			$json['headline']         = get_the_title();
			$json['description']      = has_excerpt() ? get_the_excerpt() : '';
		} elseif ( is_page() ) {
			$json['@type']            = 'WebPage';
			$json['url']              = get_the_permalink();
			$json['name']             = get_the_title();
			$json['description']      = has_excerpt() ? get_the_excerpt() : '';
		}

		if ( isset( $json ) ) {
			Pizzaro::set_structured_data( apply_filters( 'pizzaro_structured_data', $json ) );
		}
	}
}

require_once get_template_directory() . '/inc/template-tags/content.php';
require_once get_template_directory() . '/inc/template-tags/aboutpage.php';
require_once get_template_directory() . '/inc/template-tags/contactpage.php';
require_once get_template_directory() . '/inc/template-tags/homepage.php';
require_once get_template_directory() . '/inc/template-tags/homepage-v1.php';
require_once get_template_directory() . '/inc/template-tags/homepage-v2.php';
require_once get_template_directory() . '/inc/template-tags/homepage-v3.php';
require_once get_template_directory() . '/inc/template-tags/homepage-v4.php';
require_once get_template_directory() . '/inc/template-tags/homepage-v5.php';
require_once get_template_directory() . '/inc/template-tags/homepage-v6.php';
require_once get_template_directory() . '/inc/template-tags/homepage-v7.php';
require_once get_template_directory() . '/inc/template-tags/header.php';
require_once get_template_directory() . '/inc/template-tags/footer.php';
require_once get_template_directory() . '/inc/template-tags/footer-v2.php';
require_once get_template_directory() . '/inc/template-tags/footer-v3.php';