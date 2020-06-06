<?php
// Get all elementor page templates
if ( ! function_exists( 'pp_get_page_templates' ) ) {
	function pp_get_page_templates( $type = '' ) {
		$args = [
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
		];

		if ( $type ) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'elementor_library_type',
					'field'    => 'slug',
					'terms'    => $type,
				],
			];
		}

		$page_templates = get_posts( $args );

		$options = array();

		if ( ! empty( $page_templates ) && ! is_wp_error( $page_templates ) ) {
			foreach ( $page_templates as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}
		return $options;
	}
}

// Get all forms of Contact Form 7 plugin
if ( ! function_exists( 'pp_get_contact_form_7_forms' ) ) {
	function pp_get_contact_form_7_forms() {
		if ( function_exists( 'wpcf7' ) ) {
			$options = array();

			$args = array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => -1,
			);

			$contact_forms = get_posts( $args );

			if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

				$i = 0;

				foreach ( $contact_forms as $post ) {
					if ( 0 === $i ) {
						$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
					}
					$options[ $post->ID ] = $post->post_title;
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}
}

// Get all forms of Gravity Forms plugin
if ( ! function_exists( 'pp_get_gravity_forms' ) ) {
	function pp_get_gravity_forms() {
		if ( class_exists( 'GFCommon' ) ) {
			$options = array();

			$contact_forms = RGFormsModel::get_forms( null, 'title' );

			if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

				$i = 0;

				foreach ( $contact_forms as $form ) {
					if ( 0 === $i ) {
						$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
					}
					$options[ $form->id ] = $form->title;
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}
}

// Get all forms of Ninja Forms plugin
if ( ! function_exists( 'pp_get_ninja_forms' ) ) {
	function pp_get_ninja_forms() {
		if ( class_exists( 'Ninja_Forms' ) ) {
			$options = array();

			$contact_forms = Ninja_Forms()->form()->get_forms();

			if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

				$i = 0;

				foreach ( $contact_forms as $form ) {
					if ( 0 === $i ) {
						$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
					}
					$options[ $form->get_id() ] = $form->get_setting( 'title' );
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}
}

// Get all forms of Caldera plugin
if ( ! function_exists( 'pp_get_caldera_forms' ) ) {
	function pp_get_caldera_forms() {
		if ( class_exists( 'Caldera_Forms' ) ) {
			$options = array();

			$contact_forms = Caldera_Forms_Forms::get_forms( true, true );

			if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

				$i = 0;

				foreach ( $contact_forms as $form ) {
					if ( 0 === $i ) {
						$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
					}
					$options[ $form['ID'] ] = $form['name'];
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}
}

// Get all forms of WPForms plugin
if ( ! function_exists( 'pp_get_wpforms_forms' ) ) {
	function pp_get_wpforms_forms() {
		if ( function_exists( 'wpforms' ) ) {
			$options = array();

			$args = array(
				'post_type'      => 'wpforms',
				'posts_per_page' => -1,
			);

			$contact_forms = get_posts( $args );

			if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

				$i = 0;

				foreach ( $contact_forms as $post ) {
					if ( 0 === $i ) {
						$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
					}
					$options[ $post->ID ] = $post->post_title;
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}
}

// Get all forms of WP Fluent Forms plugin
if ( ! function_exists( 'pp_get_fluent_forms' ) ) {
	function pp_get_fluent_forms() {
		$options = array();

		if ( function_exists( 'wpFluentForm' ) ) {
			
			global $wpdb;
            
            $result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}fluentform_forms" );
            if ( $result ) {
                $options[0] = esc_html__('Select a Contact Form', 'powerpack');
                foreach( $result as $form ) {
                    $options[$form->id] = $form->title;
                }
            } else {
                $options[0] = esc_html__('No forms found!', 'powerpack');
            }
		}

		return $options;
	}
}

// Get all forms of WPForms plugin
if ( ! function_exists( 'pp_get_formidable_forms' ) ) {
	function pp_get_formidable_forms() {
		if ( class_exists('FrmForm') ) {
			$options = array();

            $forms = FrmForm::get_published_forms( array(), 999, 'exclude' );
            if ( count( $forms ) ) {
				$i = 0;
                foreach ( $forms as $form ) {
					if ( 0 === $i ) {
						$options[0] = esc_html__( 'Select a Contact form', 'powerpack' );
					}
                	$options[$form->id] = $form->name;
					$i++;
				}
            }
        } else {
			$options = array();
		}
		return $options;
	}
}

// Get all forms of WP Fluent Forms plugin
if ( ! function_exists( 'pp_get_fluent_forms' ) ) {
	function pp_get_fluent_forms() {
		$options = array();

		if ( function_exists( 'wpFluentForm' ) ) {
			
			global $wpdb;
            
            $result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}fluentform_forms" );
            if ( $result ) {
                $options[0] = esc_html__('Select a Contact Form', 'powerpack');
                foreach( $result as $form ) {
                    $options[$form->id] = $form->title;
                }
            } else {
                $options[0] = esc_html__('No forms found!', 'powerpack');
            }
		}

		return $options;
	}
}

// Get taxonomies
/*if ( ! function_exists( 'pp_get_post_taxonomies' ) ) {
	function pp_get_post_taxonomies() {

		$options = array();

		$taxonomies = get_taxonomies( array(
			'show_in_nav_menus' => true
		), 'objects' );

		if ( ! empty( $taxonomies ) && ! is_wp_error( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$options[ $taxonomy->name ] = $taxonomy->label;
			}
		}

		return $options;
	}
}*/

// Get categories
if ( ! function_exists( 'pp_get_post_categories' ) ) {
	function pp_get_post_categories() {

		$options = array();

		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => true,
			)
		);

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return $options;
	}
}

// Get Post Types
if ( ! function_exists( 'pp_get_post_types' ) ) {
	function pp_get_post_types() {

		$post_types = get_post_types(
			array(
				'public'            => true,
				'show_in_nav_menus' => true,
			),
			'objects'
		);

		$options = array();

		foreach ( $post_types as $post_type ) {
			$options[ $post_type->name ] = $post_type->label;
		}

		return $options;
	}
}

// Get Post Taxonomies
if ( ! function_exists( 'pp_get_post_taxonomies' ) ) {
	function pp_get_post_taxonomies( $post_type ) {

		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$data       = array();

		foreach ( $taxonomies as $tax_slug => $tax ) {

			if ( ! $tax->public || ! $tax->show_ui ) {
				continue;
			}

			$data[ $tax_slug ] = $tax;
		}

		return apply_filters( 'pp_post_loop_taxonomies', $data, $taxonomies, $post_type );
	}
}

// Get all Authors
if ( ! function_exists( 'pp_get_auhtors' ) ) {
	function pp_get_auhtors() {

		$options = array();

		$users = get_users();

		foreach ( $users as $user ) {
			$options[ $user->ID ] = $user->display_name;
		}

		return $options;
	}
}

// Get post tags
if ( ! function_exists( 'pp_get_tags' ) ) {
	function pp_get_tags() {

		$options = array();

		$tags = get_tags();

		foreach ( $tags as $tag ) {
			$options[ $tag->term_id ] = $tag->name;
		}

		return $options;
	}
}

// Get all Posts
if ( ! function_exists( 'pp_get_posts' ) ) {
	function pp_get_posts() {

		$post_list = get_posts(
			array(
				'post_type'      => 'post',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => -1,
			)
		);

		$posts = array();

		if ( ! empty( $post_list ) && ! is_wp_error( $post_list ) ) {
			foreach ( $post_list as $post ) {
				$posts[ $post->ID ] = $post->post_title;
			}
		}

		return $posts;
	}
}

// Get all Posts
if ( ! function_exists( 'pp_get_posts_any' ) ) {
	function pp_get_posts_any( $post_type ) {

		$post_list = get_posts(
			array(
				'post_type'      => $post_type,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => -1,
			)
		);

		$posts = array();

		if ( ! empty( $post_list ) && ! is_wp_error( $post_list ) ) {
			foreach ( $post_list as $post ) {
				$posts[ $post->ID ] = $post->post_title;
			}
		}

		return $posts;
	}
}

// Custom Excerpt
if ( ! function_exists( 'pp_custom_excerpt' ) ) {
	function pp_custom_excerpt( $limit = '' ) {
		$excerpt = explode( ' ', get_the_excerpt(), $limit );
		if ( count( $excerpt ) >= $limit ) {
			array_pop( $excerpt );
			$excerpt = implode( ' ', $excerpt ) . '...';
		} else {
			$excerpt = implode( ' ', $excerpt );
		}
		$excerpt = preg_replace( '`[[^]]*]`', '', $excerpt );
		return $excerpt;
	}
}
add_filter( 'get_the_excerpt', 'do_shortcode' );

// Get Counter Years
if ( ! function_exists( 'pp_get_normal_years' ) ) {
	function pp_get_normal_years() {
		$options = array( '0' => __( 'Year', 'powerpack' ) );

		for ( $i = date( 'Y' ); $i < date( 'Y' ) + 6; $i++ ) {
			$options[ $i ] = $i;
		}

		return $options;
	}
}

// Get Counter Month
if ( ! function_exists( 'pp_get_normal_month' ) ) {
	function pp_get_normal_month() {
		$months = array(
			'1'  => __( 'Jan', 'powerpack' ),
			'2'  => __( 'Feb', 'powerpack' ),
			'3'  => __( 'Mar', 'powerpack' ),
			'4'  => __( 'Apr', 'powerpack' ),
			'5'  => __( 'May', 'powerpack' ),
			'6'  => __( 'Jun', 'powerpack' ),
			'7'  => __( 'Jul', 'powerpack' ),
			'8'  => __( 'Aug', 'powerpack' ),
			'9'  => __( 'Sep', 'powerpack' ),
			'10' => __( 'Oct', 'powerpack' ),
			'11' => __( 'Nov', 'powerpack' ),
			'12' => __( 'Dec', 'powerpack' ),
		);

		$options = array( '0' => __( 'Month', 'powerpack' ) );

		for ( $i = 1; $i <= 12; $i++ ) {
			$options[ $i ] = $months[ $i ];
		}

		return $options;
	}
}

// Get Counter Date
function pp_get_normal_date() {
	$options = array( '0' => __( 'Date', 'powerpack' ) );

	for ( $i = 1; $i <= 31; $i++ ) {
		$options[ $i ] = $i;
	}

	return $options;
}

// Get Counter Hours
function pp_get_normal_hour() {
	$options = array( '0' => __( 'Hour', 'powerpack' ) );

	for ( $i = 0; $i < 24; $i++ ) {
		$options[ $i ] = $i;
	}

	return $options;
}

// Get Counter Minutes
function pp_get_normal_minutes() {
	$options = array( '0' => __( 'Minute', 'powerpack' ) );

	for ( $i = 0; $i < 60; $i++ ) {
		$options[ $i ] = $i;
	}

	return $options;
}

// Get Counter Seconds
function pp_get_normal_seconds() {
	$options = array( '0' => __( 'Seconds', 'powerpack' ) );

	for ( $i = 0; $i < 60; $i++ ) {
		$options[ $i ] = $i;
	}

	return $options;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( class_exists( 'WooCommerce' ) || is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

	// Get all Products
	if ( ! function_exists( 'pp_get_products' ) ) {
		function pp_get_products() {

			$post_list = get_posts(
				array(
					'post_type'      => 'product',
					'orderby'        => 'date',
					'order'          => 'DESC',
					'posts_per_page' => -1,
				)
			);

			$posts = array();

			if ( ! empty( $post_list ) && ! is_wp_error( $post_list ) ) {
				foreach ( $post_list as $post ) {
					$posts[ $post->ID ] = $post->post_title;
				}
			}

			return $posts;
		}
	}

	// Woocommerce - Get product categories
	if ( ! function_exists( 'pp_get_product_categories' ) ) {
		function pp_get_product_categories() {

			$options = array();

			$terms = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
				)
			);

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$options[ $term->term_id ] = $term->name;
				}
			}

			return $options;
		}
	}

	// WooCommerce - Get product tags
	if ( ! function_exists( 'pp_product_get_tags' ) ) {
		function pp_product_get_tags() {

			$options = array();

			$tags = get_terms( 'product_tag' );

			if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
				foreach ( $tags as $tag ) {
					$options[ $tag->term_id ] = $tag->name;
				}
			}

			return $options;
		}
	}
}

function pp_get_modules() {
	$modules = array(
        'pp-link-effects'           => __('Link Effects', 'powerpack'),
        'pp-divider'                => __('Divider', 'powerpack'),
        'pp-recipe'                 => __('Recipe', 'powerpack'),
        'pp-info-box'               => __('Info Box', 'powerpack'),
        'pp-info-box-carousel'      => __('Info Box Carousel', 'powerpack'),
        'pp-info-list'              => __('Info List', 'powerpack'),
		'pp-info-table'             => __('Info Table', 'powerpack'),
        'pp-tiled-posts'            => __('Tiled Posts', 'powerpack'),
        'pp-posts'					=> __('Posts', 'powerpack'),
        'pp-pricing-table'          => __('Pricing Table', 'powerpack'),
        'pp-price-menu'             => __('Price Menu', 'powerpack'),
        'pp-business-hours'         => __('Businsess Hours', 'powerpack'),
        'pp-team-member'            => __('Team Member', 'powerpack'),
        'pp-team-member-carousel'   => __('Team Member Carousel', 'powerpack'),
        'pp-counter'                => __('Counter', 'powerpack'),
        'pp-hotspots'               => __('Image Hotspots', 'powerpack'),
        'pp-icon-list'              => __('Icon List', 'powerpack'),
        'pp-dual-heading'           => __('Dual Heading', 'powerpack'),
        'pp-promo-box'              => __('Promo Box', 'powerpack'),
        'pp-logo-carousel'          => __('Logo Carousel', 'powerpack'),
        'pp-logo-grid'              => __('Logo Grid', 'powerpack'),
        'pp-modal-popup'            => __('Modal Popup', 'powerpack'),
        'pp-onepage-nav'            => __('One Page Navigation', 'powerpack'),
        'pp-table'                  => __('Table', 'powerpack'),
        'pp-toggle'                 => __('Toggle', 'powerpack'),
        'pp-image-comparison'       => __('Image Comparison', 'powerpack'),
        'pp-instafeed'              => __('Instagram Feed', 'powerpack'),
        'pp-google-maps'            => __('Google Maps', 'powerpack'),
        'pp-review-box'             => __('Review Box', 'powerpack'),
        'pp-countdown'            	=> __('Countdown', 'powerpack'),
        'pp-buttons'            	=> __('Buttons', 'powerpack'),
        'pp-advanced-tabs'          => __('Advanced Tabs', 'powerpack'),
        'pp-image-gallery'          => __('Image Gallery', 'powerpack'),
        'pp-image-slider'           => __('Image Slider', 'powerpack'),
        'pp-advanced-menu'          => __('Advanced Menu', 'powerpack'),
        'pp-offcanvas-content'      => __('Offcanvas Content', 'powerpack'),
        'pp-timeline'               => __('Timeline', 'powerpack'),
        'pp-showcase'               => __('Showcase', 'powerpack'),
        'pp-card-slider'            => __('Card Slider', 'powerpack'),
        'pp-flipbox'                => __('Flip Box', 'powerpack'),
        'pp-image-accordion'        => __('Image Accordion', 'powerpack'),
        'pp-advanced-accordion'     => __('Advanced Accordion', 'powerpack'),
        'pp-breadcrumbs'            => __('Breadcrumbs', 'powerpack'),
        'pp-content-ticker'         => __('Content Ticker', 'powerpack'),
        'pp-magazine-slider'        => __('Magazine Slider', 'powerpack'),
        'pp-video'                  => __('Video', 'powerpack'),
        'pp-video-gallery'          => __('Video Gallery', 'powerpack'),
        'pp-testimonials'           => __('Testimonials', 'powerpack'),
        'pp-scroll-image'           => __('Scroll Image', 'powerpack'),
        'pp-album'                  => __('Album', 'powerpack'),
        'pp-twitter-buttons'        => __('Twitter Buttons', 'powerpack'),
        'pp-twitter-grid'           => __('Twitter Grid', 'powerpack'),
        'pp-twitter-timeline'       => __('Twitter Timeline', 'powerpack'),
        'pp-twitter-tweet'          => __('Twitter Tweet', 'powerpack'),
        'pp-tabbed-gallery'			=> __('Tabbed Gallery', 'powerpack'),
        'pp-devices'				=> __('Devices', 'powerpack'),
        'pp-fancy-heading'			=> __('Fancy Heading', 'powerpack'),
        'pp-faq'					=> __('FAQ', 'powerpack'),
		'pp-how-to'               	=> __( 'How To', 'powerpack' ),
        'pp-coupons'				=> __('Coupons', 'powerpack'),
        'pp-categories'				=> __('Categories', 'powerpack'),
    );

    // Contact Form 7
    if ( function_exists( 'wpcf7' ) ) {
        $modules['pp-contact-form-7'] = __('Contact Form 7', 'powerpack');
    }
    
    // Gravity Forms
    if ( class_exists( 'GFCommon' ) ) {
        $modules['pp-gravity-forms'] = __('Gravity Forms', 'powerpack');
    }
    
    // Ninja Forms
    if ( class_exists( 'Ninja_Forms' ) ) {
        $modules['pp-ninja-forms'] = __('Ninja Forms', 'powerpack');
    }
    
    // Caldera Forms
    if ( class_exists( 'Caldera_Forms' ) ) {
        $modules['pp-caldera-forms'] = __('Caldera Forms', 'powerpack');
    }
    
    // WPForms
    if ( function_exists( 'wpforms' ) ) {
        $modules['pp-wpforms'] = __('WPForms', 'powerpack');
    }
    
    // Fluent Forms
    if ( function_exists( 'wpFluentForm' ) ) {
        $modules['pp-fluent-forms'] = __('Fluent Forms', 'powerpack');
	}
	
	// Formidable Forms
    if ( class_exists( 'FrmForm' ) ) {
        $modules['pp-formidable-forms'] = __('Formidable Forms', 'powerpack');
    }
    
    // Check whether WooCommerce plugin is installed and activated.
    if ( class_exists( 'WooCommerce' ) || is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        $modules['pp-woo-add-to-cart'] = __('Woo - Add To Cart', 'powerpack');
        $modules['pp-woo-categories'] = __('Woo - Categories', 'powerpack');
        $modules['pp-woo-cart'] = __('Woo - Cart', 'powerpack');
        $modules['pp-woo-offcanvas-cart'] = __('Woo - Offcanvas Cart', 'powerpack');
        $modules['pp-woo-checkout'] = __('Woo - Checkout', 'powerpack');
        $modules['pp-woo-mini-cart'] = __('Woo - Mini Cart', 'powerpack');
        $modules['pp-woo-products'] = __('Woo - Products', 'powerpack');
    }

    ksort($modules);

    return $modules;
}

function pp_get_thumbnail_taxonomies()
{
	
	$taxonomies = array();
	$taxonomy_list = array();
	
	$post_types = \PowerpackElements\Classes\PP_Posts_Helper::get_post_types();

	foreach ( $post_types as $slug => $type ) {
		$taxonomies = \PowerpackElements\Classes\PP_Posts_Helper::get_post_taxonomies( $slug );
		foreach ( (array) $taxonomies as $taxonomy ) {
			$taxonomy_list[$taxonomy->name] = $taxonomy->label;
		}
	}

    return $taxonomy_list;	
}

function pp_get_extensions()
{
    $extensions = array(
		'pp-display-conditions' => __( 'Display Conditions', 'powerpack' ),
		'pp-background-effects' => __( 'Background Effects', 'powerpack' ),
	);

	$extensions = apply_filters( 'pp_elements_extensions', $extensions );

    return $extensions;	
}

function pp_get_enabled_modules()
{
    $enabled_modules = \PowerpackElements\Classes\PP_Admin_Settings::get_option( 'pp_elementor_modules', true );

	if ( is_array( $enabled_modules ) ) {
		return $enabled_modules;
	}

	if ( 'disabled' == $enabled_modules ) {
		return $enabled_modules;
	}

	return pp_get_modules();
}

function pp_get_enabled_extensions()
{
	$enabled_extensions = \PowerpackElements\Classes\PP_Admin_Settings::get_option( 'pp_elementor_extensions', true );
	
	if ( is_array( $enabled_extensions ) ) {
		return $enabled_extensions;
	}

	if ( 'disabled' == $enabled_extensions ) {
		return $enabled_extensions;
	}

	return pp_get_extensions();
}

function pp_get_enabled_taxonomies()
{
	$enabled_taxonomies = \PowerpackElements\Classes\PP_Admin_Settings::get_option( 'pp_elementor_taxonomy_thumbnail_taxonomies', true );
	
	if ( is_array( $enabled_taxonomies ) ) {
		return $enabled_taxonomies;
	}

	if ( 'disabled' == $enabled_taxonomies ) {
		return $enabled_taxonomies;
	}

	return pp_get_thumbnail_taxonomies();
}

// Get templates

function pp_get_saved_templates( $templates = array() ) {

	if ( empty( $templates ) ) {
		return array();
	}

	$options = array();

	foreach ( $templates as $template ) {
		$options[ $template['template_id'] ] = $template['title'];
	}

	return $options;
}

// Query functions.

/**
 * Fetches available post types
 *
 * @since 2.0.0
 */
function pp_get_public_post_types_options( $singular = false, $any = false, $args = [] ) {
	$defaults = [
		'show_in_nav_menus' => true,
	];

	$post_types = [];
	$post_type_args = wp_parse_args( $args, $defaults );

	if ( $any ) $post_types['any'] = __( 'Any', 'powerpack' );

	if ( ! function_exists( 'get_post_types' ) )
		return $post_types;

	$_post_types = get_post_types( $post_type_args, 'objects' );

	foreach ( $_post_types as $post_type => $object ) {
		$post_types[ $post_type ] = $singular ? $object->labels->singular_name : $object->label;
	}

	return $post_types;
}

/**
 * Get Taxonomies Options
 *
 * Fetches available taxonomies
 *
 * @since 2.0.0
 */
function pp_get_taxonomies_options( $post_type = false ) {

	$options = [];

	if ( ! $post_type ) {
		// Get all available taxonomies
		$taxonomies = get_taxonomies( array(
			'show_in_nav_menus' => true
		), 'objects' );
	} else {
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
	}

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! $taxonomy->publicly_queryable ) {
			continue;
		}

		$options[ $taxonomy->name ] = $taxonomy->label;
	}

	if ( empty( $options ) ) {
		$options[0] = __( 'No taxonomies found', 'powerpack' );
		return $options;
	}

	return $options;
}

/**
 * Get Taxonomies Labels
 *
 * Fetches labels for given taxonomy
 *
 * @since 2.1.0
 */
function pp_get_taxonomy_labels( $taxonomy = '' ) {

	if ( ! $taxonomy || '' === $taxonomy )
		return false;

	$labels = false;
	$taxonomy_object = get_taxonomy( $taxonomy );

	if ( $taxonomy_object && is_object( $taxonomy_object ) ) {
		$labels = $taxonomy_object->labels;
	}

	return $labels;
}

/**
 * Get Terms Options
 * 
 * Retrieve the terms options array for a control
 *
 * @since  1.6.0
 * @param  taxonomy  	The taxonomy for the terms
 * @param  key|string 	The key to use when building the options. Can be 'slug' or 'id'
 * @param  all|bool  	The string to use for the first option. Can be false to disable. Default: true
 * @return array
 */
function pp_get_terms_options( $taxonomy, $key = 'slug', $all = true ) {

	if ( false !== $all ) {
		$all = ( true === $all ) ? __( 'All', 'powerpack' ) : $all;
		$options = [ '' => $all ];
	}

	$terms = get_terms( array(
		'taxonomy' => $taxonomy
	));

	if ( empty( $terms ) ) {
		$options[ '' ] = sprintf( __( 'No terms found', 'powerpack' ), $taxonomy );
		return $options;
	}

	foreach ( $terms as $term ) {
		$term_key = ( 'id' === $key ) ? $term->term_id : $term->slug;
		$options[ $term_key ] = $term->name;
	}

	return $options;
}

/**
 * Get Terms
 *
 * Retrieve a list of terms for specific taxonomies
 *
 * @since  1.6.0
 * @return array
 */
function pp_get_terms( $taxonomies = [] ) {
	$_terms = [];

	if ( empty( $taxonomies ) )
		return false;

	if ( is_array( $taxonomies ) ) {
		foreach( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( get_the_ID(), $taxonomy );

			if ( empty( $terms ) )
				continue;

			foreach( $terms as $term ) { $_terms[] = $term; }
		}
	} else {
		$_terms = get_the_terms( get_the_ID(), $taxonomies );
	}

	if ( empty( $_terms ) || 0 === count( $_terms ) )
		return false;

	return $_terms;

}

/**
 * Fetches available pages
 *
 * @since 2.0.0
 */
function pp_get_pages_options() {

	$options = [];

	$pages = get_pages( array(
		'hierarchical' => false,
	) );

	if ( empty( $pages ) ) {
		$options[ '' ] = __( 'No pages found', 'powerpack' );
		return $options;
	}

	foreach ( $pages as $page ) {
		$options[ $page->ID ] = $page->post_title;
	}

	return $options;
}

/**
 * Fetches available users
 *
 * @since 2.0.0
 */
function pp_get_users_options() {

	$options = [];

	$users = get_users( array(
		'fields' => [ 'ID', 'display_name' ],
	) );

	if ( empty( $users ) ) {
		$options[ '' ] = __( 'No users found', 'powerpack' );
		return $options;
	}

	foreach ( $users as $user ) {
		$options[ $user->ID ] = $user->display_name;
	}

	return $options;
}

/**
 * Get category with highest number of parents
 * from a given list
 *
 * @since 2.0.0
 */
function pp_get_most_parents_category( $categories = [] ) {

	$counted_cats = [];

	if ( ! is_array( $categories ) )
		return $categories;

	foreach ( $categories as $category ) {
		$category_parents = get_category_parents( $category->term_id, false, ',' );
		$category_parents = explode( ',', $category_parents );
		$counted_cats[ $category->term_id ] = count( $category_parents );
	}

	arsort( $counted_cats );
	reset( $counted_cats );

	return key( $counted_cats );
}

/**
 * Recursively sort an array of taxonomy terms hierarchically. Child categories will be
 * placed under a 'children' member of their parent term.
 *
 * @since 2.2.6
 *
 * @param Array   $cats     taxonomy term objects to sort
 * @param Array   $into     result array to put them in
 * @param integer $parentId the current parent ID to put them in
 */
function pp_sort_terms_hierarchicaly( Array &$cats, Array &$into, $parentId = 0 ) {
	foreach ( $cats as $i => $cat ) {
		if ( $cat->parent == $parentId ) {
			$into[ $cat->term_id ] = $cat;
			unset( $cats[ $i ] );
		}
	}

	foreach ( $into as $topCat ) {
		$topCat->children = [];
		pp_sort_terms_hierarchicaly( $cats, $topCat->children, $topCat->term_id );
	}
}

/**
 * Constrain search query for posts by searching only in post titles
 *
 * @since 2.2.0
 */
function pp_posts_where_by_title_name( $where, &$wp_query ) {
	global $wpdb;
	if ( $s = $wp_query->get( 'search_title_name' ) ) {
		$where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $s ) ) . '%\' OR ' . $wpdb->posts . '.post_name LIKE \'%' . esc_sql( $wpdb->esc_like( $s ) ) . '%\')';
	}
	return $where;
}

/**
 * Compare conditions.
 *
 * Checks two values against an operator
 *
 * @since 2.0.0
 *
 * @param mixed  $left_value  First value to compare.
 * @param mixed  $right_value Second value to compare.
 * @param string $operator    Comparison operator.
 *
 * @return bool
 */
function pp_value_compare( $left_value, $right_value, $operator ) {
	switch ( $operator ) {
		case 'is':
			return $left_value == $right_value;
		case 'not':
			return $left_value != $right_value;
		default:
			return $left_value === $right_value;
	}
}

/**
 * Elementor
 * 
 * Retrieves the elementor plugin instance
 *
 * @since  2.1.0
 * @return \Elementor\Plugin|$instace
 */
function pp_get_elementor() {
	return \Elementor\Plugin::$instance;
}