<?php
/**
 * Pizzaro  functions.
 *
 * @package pizzaro
 */

/**
 * Check if Redux Framework is activated
 */
if( ! function_exists( 'is_redux_activated' ) ) {
	function is_redux_activated() {
		return class_exists( 'ReduxFrameworkPlugin' ) ? true : false;
	}
}

/**
 * Check if Visual Composer is activated
 */
if( ! function_exists( 'is_vc_activated' ) ) {
	function is_vc_activated() {
		return class_exists( 'WPBakeryVisualComposerAbstract' ) ? true : false;
	}
}

/**
 * Check if Elementor is activated
 */
if( ! function_exists( 'is_elementor_activated' ) ) {
	function is_elementor_activated() {
		return did_action( 'elementor/loaded' ) ? true : false;
	}
}

/**
 * Query WooCommerce activation
 */
if( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		return class_exists( 'woocommerce' ) ? true : false;
	}
}

/**
 * Query WooCommerce Extension Activation.
 * @var  $extension main extension class name
 * @return boolean
 */
if( ! function_exists( 'is_woocommerce_extension_activated' ) ) {
	function is_woocommerce_extension_activated( $extension ) {
		if( is_woocommerce_activated() ) {
			$is_activated = class_exists( $extension ) ? true : false;
		} else {
			$is_activated = false;
		}

		return $is_activated;
	}
}

/**
 * Checks if YITH WooCommerce Product Addon is activated
 *
 * @return boolean
 */
if( ! function_exists( 'is_yith_wapo_activated' ) ) {
	function is_yith_wapo_activated() {
		return is_woocommerce_extension_activated( 'YITH_WAPO' );
	}
}

/**
 * Checks if YITH WooCommerce Quick View is activated
 *
 * @return boolean
 */
if( ! function_exists( 'is_yith_wcqv_activated' ) ) {
	function is_yith_wcqv_activated() {
		return is_woocommerce_extension_activated( 'YITH_WCQV' );
	}
}

/**
 * Checks if WP Store Locator is activated
 *
 * @return boolean
 */
if( ! function_exists( 'is_wp_store_locator_activated' ) ) {
	function is_wp_store_locator_activated() {
		return class_exists( 'WP_Store_locator' ) ? true : false ;
	}
}

/**
 * Query The Events Calender activation
 */
if( ! function_exists( 'is_events_calendar_activated' ) ) {
	function is_events_calendar_activated() {
		return class_exists( 'Tribe__Events__Main' ) ? true : false ;
	}
}

/**
 * Query The Events Calender Pro activation
 */
if( ! function_exists( 'is_events_calendar_pro_activated' ) ) {
	function is_events_calendar_pro_activated() {
		return class_exists( 'Tribe__Events__Pro__Main' ) ? true : false ;
	}
}

/**
 * Query Jetpack activation
 */
if( ! function_exists( 'is_jetpack_activated' ) ) {
	function is_jetpack_activated() {
		return class_exists( 'Jetpack' ) ? true : false ;
	}
}

if( ! function_exists( 'is_ocdi_activated' ) ) {
	/**
	 * Check if One Click Demo Import is activated
	 */
	function is_ocdi_activated() {
		return class_exists( 'OCDI_Plugin' ) ? true : false;
	}
}

/**
 * Should use CDN or local
 */
if ( ! function_exists( 'pizzaro_use_cdn' ) ) {
	function pizzaro_use_cdn() {
		return apply_filters( 'pizzaro_use_cdn', false );
	}
}

/**
 * Call a shortcode function by tag name.
 *
 * @since  1.4.6
 *
 * @param string $tag     The shortcode whose function to call.
 * @param array  $atts    The attributes to pass to the shortcode function. Optional.
 * @param array  $content The shortcode's content. Default is null (none).
 *
 * @return string|bool False on failure, the result of the shortcode on success.
 */
function pizzaro_do_shortcode( $tag, array $atts = array(), $content = null ) {
	global $shortcode_tags;

	if ( ! isset( $shortcode_tags[ $tag ] ) ) {
		return false;
	}

	return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}

/**
 * Get the content background color
 * Accounts for the Pizzaro Designer's content background option.
 *
 * @since  1.6.0
 * @return string the background color
 */
function pizzaro_get_content_background_color() {
	// Set the bg color var based on whether the Pizzaro designer has set a content bg color or not.
	$content_bg_color = get_theme_mod( 'sd_content_background_color' );
	$content_frame    = get_theme_mod( 'sd_fixed_width' );

	// Set the bg color based on the default theme option.
	$bg_color = str_replace( '#', '', get_theme_mod( 'background_color' ) );

	// But if the Pizzaro Designer extension is active, and the content frame option is enabled we need that bg color instead.
	if ( $content_bg_color && 'true' == $content_frame && class_exists( 'Pizzaro_Designer' ) ) {
		$bg_color = str_replace( '#', '', $content_bg_color );
	}

	return '#' . $bg_color;
}

/**
 * Apply inline style to the Pizzaro header.
 *
 * @uses  get_header_image()
 * @since  2.0.0
 */
function pizzaro_header_styles() {
	$is_header_image = get_header_image();

	if ( $is_header_image ) {
		$header_bg_image = 'url(' . esc_url( $is_header_image ) . ')';
	} else {
		$header_bg_image = 'none';
	}

	$styles = apply_filters( 'pizzaro_header_styles', array(
		'background-image' => $header_bg_image,
	) );

	foreach ( $styles as $style => $value ) {
		echo esc_attr( $style . ': ' . $value . '; ' );
	}
}

/**
 * Adjust a hex color brightness
 * Allows us to create hover styles for custom link colors
 *
 * @param  strong  $hex   hex color e.g. #111111.
 * @param  integer $steps factor by which to brighten/darken ranging from -255 (darken) to 255 (brighten).
 * @return string        brightened/darkened hex color
 * @since  1.0.0
 */
function pizzaro_adjust_color_brightness( $hex, $steps, $is_percentage = false ) {

	// Steps should be between -255 and 255. Negative = darker, positive = lighter.
	$steps  = max( -255, min( 255, $steps ) );

	// Format the hex color string.
	$hex    = str_replace( '#', '', $hex );

	if ( 3 == strlen( $hex ) ) {
		$hex    = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
	}

	// Get decimal values.
	$r  = hexdec( substr( $hex, 0, 2 ) );
	$g  = hexdec( substr( $hex, 2, 2 ) );
	$b  = hexdec( substr( $hex, 4, 2 ) );

	// Adjust number of steps and keep it inside 0 to 255.
	$r  = max( 0, min( 255, $r + $steps ) );
	$g  = max( 0, min( 255, $g + $steps ) );
	$b  = max( 0, min( 255, $b + $steps ) );

	$r_hex  = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
	$g_hex  = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
	$b_hex  = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

	return '#' . $r_hex . $g_hex . $b_hex;
}

/**
 * Sanitizes choices (selects / radios)
 * Checks that the input matches one of the available choices
 *
 * @param array $input the available choices.
 * @param array $setting the setting object.
 * @since  1.3.0
 */
function pizzaro_sanitize_choices( $input, $setting ) {
	// Ensure input is a slug.
	$input = sanitize_key( $input );

	// Get list of choices from the control associated with the setting.
	$choices = $setting->manager->get_control( $setting->id )->choices;

	// If the input is a valid key, return it; otherwise, return the default.
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * Checkbox sanitization callback.
 *
 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
 * as a boolean value, either TRUE or FALSE.
 *
 * @param bool $checked Whether the checkbox is checked.
 * @return bool Whether the checkbox is checked.
 * @since  1.5.0
 */
function pizzaro_sanitize_checkbox( $checked ) {
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

/**
 * Enables template debug mode
 *
 */
function pizzaro_template_debug_mode() {
	if ( ! defined( 'PIZZARO_TEMPLATE_DEBUG_MODE' ) ) {
		$status_options = get_option( 'woocommerce_status_options', array() );
		if ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) {
			define( 'PIZZARO_TEMPLATE_DEBUG_MODE', true );
		} else {
			define( 'PIZZARO_TEMPLATE_DEBUG_MODE', false );
		}
	}
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function pizzaro_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = pizzaro_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'pizzaro_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'pizzaro_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'pizzaro_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function pizzaro_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = 'templates/';
	}

	if ( ! $default_path ) {
		$default_path = 'templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template || PIZZARO_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters( 'pizzaro_locate_template', $template, $template_name, $template_path );
}

if ( ! function_exists( 'pr' ) ) {
	/**
	 * print_r() convenience function.
     *
     * In terminals this will act similar to using print_r() directly, when not run on cli
     * print_r() will also wrap <pre> tags around the output of given variable.
     *
     * @param mixed $var Variable to print out.
     * @return void
	 */
	function pr( $var ) {
		if ( ! WP_DEBUG ) {
			return;
		}

		$template = (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg') ? '<pre class="pr">%s</pre>' : "\n%s\n\n";
		printf( $template, trim( print_r( $var, true ) ) );
	}
}

if ( ! function_exists( 'pizzaro_get_image' ) ) {
	/**
	 * Get an HTML img element representing an image src array
	 */
	function pizzaro_get_image( $image_src ) {
		$html = '';
		if ( $image_src ) {
			list($src, $width, $height) = $image_src;
			$hwstring = image_hwstring($width, $height);
			$attr = array(
				'src'   => $src,
				'alt'   => '',
			);

			$attr = array_map( 'esc_attr', $attr );
			$html = rtrim("<img $hwstring");
			foreach ( $attr as $name => $value ) {
            	$html .= " $name=" . '"' . $value . '"';
	        }
	        $html .= ' />';
		}
		return $html;
	}
}

if ( ! function_exists( 'pizzaro_clean_kses_post' ) ) {
	/**
	 * Clean variables using wp_kses_post.
	 * @param string|array $var
	 * @return string|array
	 */
	function pizzaro_clean_kses_post( $var ) {
		return is_array( $var ) ? array_map( 'pizzaro_clean_kses_post', $var ) : wp_kses_post( stripslashes( $var ) );
	}
}

if ( ! function_exists( 'pizzaro_newsletter_form' ) ) {
	/**
	 * Pizzaro Newsletter Form
	 *
	 */
	function pizzaro_newsletter_form() {
		ob_start();
		?>
		<form>
			<div class="newsletter-form">
				<input type="text" placeholder="<?php echo esc_attr( __( 'Type here your email address to receive our newsletter', 'pizzaro' ) ); ?>">
				<button class="button" type="button"><?php echo esc_html__( 'Sign Up', 'pizzaro' ); ?></button>
			</div>
		</form>
		<?php
		$newsletter_form = ob_get_clean();
		echo apply_filters( 'pizzaro_newsletter_form', $newsletter_form );
	}
}

if ( ! function_exists( 'pizzaro_map_content' ) ) {
	/**
	 * Displays Google map
	 */
	function pizzaro_map_content() {
		return apply_filters( 'pizzaro_map_content', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d317718.69319292053!2d-0.3817765050863085!3d51.528307984912544!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47d8a00baf21de75%3A0x52963a5addd52a99!2sLondon%2C+UK!5e0!3m2!1sen!2sin!4v1463669021863" height="462" allowfullscreen></iframe>' );
	}
}

if ( ! function_exists( 'pizzaro_get_social_networks' ) ) {
	/**
	 * List of all available social networks
	 *
	 * @return array array of all social networks and its details
	 */
	function pizzaro_get_social_networks() {
		return apply_filters( 'pizzaro_get_social_networks', array(
			'facebook' 		=> array(
				'label'	=> esc_html__( 'Facebook', 'pizzaro' ),
				'icon'	=> 'fa fa-facebook-official',
				'id'	=> 'facebook_link',
				'link'	=> '#',
			),
			'twitter' 		=> array(
				'label'	=> esc_html__( 'Twitter', 'pizzaro' ),
				'icon'	=> 'fa fa-twitter',
				'id'	=> 'twitter_link',
				'link'	=> '#',
			),
			'pinterest' 	=> array(
				'label'	=> esc_html__( 'Pinterest', 'pizzaro' ),
				'icon'	=> 'fa fa-pinterest',
				'id'	=> 'pinterest_link',
			),
			'linkedin' 		=> array(
				'label'	=> esc_html__( 'LinkedIn', 'pizzaro' ),
				'icon'	=> 'fa fa-linkedin',
				'id'	=> 'linkedin_link',
			),
			'googleplus' 	=> array(
				'label'	=> esc_html__( 'Google+', 'pizzaro' ),
				'icon'	=> 'fa fa-google-plus',
				'id'	=> 'googleplus_link',
			),
			'tumblr' 	=> array(
				'label'	=> esc_html__( 'Tumblr', 'pizzaro' ),
				'icon'	=> 'fa fa-tumblr',
				'id'	=> 'tumblr_link'
			),
			'instagram' 	=> array(
				'label'	=> esc_html__( 'Instagram', 'pizzaro' ),
				'icon'	=> 'fa fa-instagram',
				'id'	=> 'instagram_link',
				'link'  => '#',
			),
			'youtube' 		=> array(
				'label'	=> esc_html__( 'Youtube', 'pizzaro' ),
				'icon'	=> 'fa fa-youtube-play',
				'id'	=> 'youtube_link',
				'link'  => '#',
			),
			'vimeo' 		=> array(
				'label'	=> esc_html__( 'Vimeo', 'pizzaro' ),
				'icon'	=> 'fa fa-vimeo-square',
				'id'	=> 'vimeo_link'
			),
			'dribbble' 		=> array(
				'label'	=> esc_html__( 'Dribbble', 'pizzaro' ),
				'icon'	=> 'fa fa-dribbble',
				'id'	=> 'dribbble_link',
			),
			'stumbleupon' 	=> array(
				'label'	=> esc_html__( 'StumbleUpon', 'pizzaro' ),
				'icon'	=> 'fa fa-stumbleupon',
				'id'	=> 'stumble_upon_link'
			),
			'rss'			=> array(
				'label'	=> esc_html__( 'RSS', 'pizzaro' ),
				'icon'	=> 'fa fa-rss',
				'id'	=> 'rss_link',
				'link'	=> get_bloginfo( 'rss2_url' ),
			)
		) );
	}
}

/**
 * Schema type
 *
 * @return void
 */
function pizzaro_html_tag_schema() {
	_deprecated_function( 'pizzaro_html_tag_schema', '2.0.2' );

	$schema = 'http://schema.org/';
	$type   = 'WebPage';

	if ( is_singular( 'post' ) ) {
		$type = 'Article';
	} elseif ( is_author() ) {
		$type = 'ProfilePage';
	} elseif ( is_search() ) {
		$type 	= 'SearchResultsPage';
	}

	echo 'itemscope="itemscope" itemtype="' . esc_attr( $schema ) . esc_attr( $type ) . '"';
}

/**
 * Sanitizes the layout setting
 *
 * Ensures only array keys matching the original settings specified in add_control() are valid
 *
 * @param array $input the layout options.
 * @since 1.0.3
 */
function pizzaro_sanitize_layout( $input ) {
	_deprecated_function( 'pizzaro_sanitize_layout', '2.0', 'pizzaro_sanitize_choices' );

	$valid = array(
		'right' => 'Right',
		'left'  => 'Left',
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return '';
	}
}

/**
 * Pizzaro Sanitize Hex Color
 *
 * @param string $color The color as a hex.
 * @todo remove in 2.1.
 */
function pizzaro_sanitize_hex_color( $color ) {
	_deprecated_function( 'pizzaro_sanitize_hex_color', '2.0', 'sanitize_hex_color' );

	if ( '' === $color ) {
		return '';
	}

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}

	return null;
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 * @todo remove in 2.1.
 */
function pizzaro_categorized_blog() {
	_deprecated_function( 'pizzaro_categorized_blog', '2.0' );

	if ( false === ( $all_the_cool_cats = get_transient( 'pizzaro_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );
		set_transient( 'pizzaro_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so pizzaro_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so pizzaro_categorized_blog should return false.
		return false;
	}
}

if ( ! function_exists( 'pizzaro_get_header_version' ) ) {
	/**
	 * Gets the Header version set in theme options
	 */
	function pizzaro_get_header_version() {

		global $post;

		$template_file = '';

		if ( isset( $post ) ) {
			$template_file = get_post_meta( $post->ID, '_wp_page_template', true );
		}

		switch( $template_file ) {
			case 'template-homepage-v1.php':
				$home_v1 		= pizzaro_get_home_v1_meta();
				$header_style 	= ! empty( $home_v1['header_style'] ) ? $home_v1['header_style'] : 'v1';
				$header_version = apply_filters( 'pizzaro_home_v1_header_version', $header_style );
				break;
			case 'template-homepage-v2.php':
				$home_v2 		= pizzaro_get_home_v2_meta();
				$header_style 	= ! empty( $home_v2['header_style'] ) ? $home_v2['header_style'] : 'v2';
				$header_version = apply_filters( 'pizzaro_home_v2_header_version', $header_style );
				break;
			case 'template-homepage-v3.php':
				$home_v3 		= pizzaro_get_home_v3_meta();
				$header_style 	= ! empty( $home_v3['header_style'] ) ? $home_v3['header_style'] : 'v3';
				$header_version = apply_filters( 'pizzaro_home_v3_header_version', $header_style );
				break;
			case 'template-homepage-v4.php':
				$home_v4 		= pizzaro_get_home_v4_meta();
				$header_style 	= ! empty( $home_v4['header_style'] ) ? $home_v4['header_style'] : 'v3';
				$header_version = apply_filters( 'pizzaro_home_v4_header_version', $header_style );
				break;
			case 'template-homepage-v5.php':
				$home_v5 		= pizzaro_get_home_v5_meta();
				$header_style 	= ! empty( $home_v5['header_style'] ) ? $home_v5['header_style'] : 'v4';
				$header_version = apply_filters( 'pizzaro_home_v5_header_version', $header_style );
				break;
			case 'template-homepage-v6.php':
				$home_v6 		= pizzaro_get_home_v6_meta();
				$header_style 	= ! empty( $home_v6['header_style'] ) ? $home_v6['header_style'] : 'v5';
				$header_version = apply_filters( 'pizzaro_home_v6_header_version', $header_style );
				break;
			case 'template-homepage-v7.php':
				$home_v7 		= pizzaro_get_home_v7_meta();
				$header_style 	= ! empty( $home_v7['header_style'] ) ? $home_v7['header_style'] : 'v4';
				$header_version = apply_filters( 'pizzaro_home_v7_header_version', $header_style );
				break;
			default:
				$header_version = apply_filters( 'pizzaro_header_version', 'v1' );
		}

		return $header_version;
	}
}

if ( ! function_exists( 'pizzaro_get_header_bg_version' ) ) {
	/**
	 * Gets the Header background version set in theme options
	 */
	function pizzaro_get_header_bg_version() {
		global $post;

		$bg_version = apply_filters( 'pizzaro_header_bg_version', '' );

		if( is_page() ) {
			$page_meta_values = array();
			$template_file = '';
			if ( isset( $post ) ) {
				$page_meta_values	= get_post_meta( $post->ID, '_pizzaro_page_metabox', true );
				$template_file		= get_post_meta( $post->ID, '_wp_page_template', true );
			}

			switch ( $template_file ) {
				case 'template-homepage-v1.php':
				case 'template-homepage-v2.php':
				case 'template-homepage-v3.php':
				case 'template-homepage-v6.php':
				case 'template-homepage-v7.php':
					$bg_version = apply_filters( 'pizzaro_homepage_header_bg_version', '' );
					break;

				case 'template-homepage-v4.php':
				case 'template-homepage-v5.php':
					$bg_version = apply_filters( 'pizzaro_homepage_header_bg_version', 'lite-bg' );
					break;

				default:
					if ( isset( $page_meta_values['site_header_background'] ) && ! empty( $page_meta_values['site_header_background'] ) ) {
						$bg_version = $page_meta_values['site_header_background'];
					}
					break;
			}

		}

		return $bg_version;
	}
}

if ( ! function_exists( 'pizzaro_get_footer_version' ) ) {
	/**
	 * Gets the Footer version set in theme options
	 */
	function pizzaro_get_footer_version() {
		global $post;

		$template_file = '';

		if ( isset( $post ) ) {
			$template_file = get_post_meta( $post->ID, '_wp_page_template', true );
		}

		switch( $template_file ) {
			case 'template-homepage-v1.php':
				$home_v1 		= pizzaro_get_home_v1_meta();
				$footer_style 	= ! empty( $home_v1['footer_style'] ) ? $home_v1['footer_style'] : 'v1';
				$footer_version = apply_filters( 'pizzaro_home_v1_footer_version', $footer_style );
				break;
			case 'template-homepage-v2.php':
				$home_v2 		= pizzaro_get_home_v2_meta();
				$footer_style 	= ! empty( $home_v2['footer_style'] ) ? $home_v2['footer_style'] : 'v2';
				$footer_version = apply_filters( 'pizzaro_home_v2_footer_version', $footer_style );
				break;
			case 'template-homepage-v3.php':
				$home_v3 		= pizzaro_get_home_v3_meta();
				$footer_style 	= ! empty( $home_v3['footer_style'] ) ? $home_v3['footer_style'] : 'v3';
				$footer_version = apply_filters( 'pizzaro_home_v3_footer_version', $footer_style );
				break;
			case 'template-homepage-v4.php':
				$home_v4 		= pizzaro_get_home_v4_meta();
				$footer_style 	= ! empty( $home_v4['footer_style'] ) ? $home_v4['footer_style'] : 'v3';
				$footer_version = apply_filters( 'pizzaro_home_v4_footer_version', $footer_style );
				break;
			case 'template-homepage-v5.php':
				$home_v5 		= pizzaro_get_home_v5_meta();
				$footer_style 	= ! empty( $home_v5['footer_style'] ) ? $home_v5['footer_style'] : 'v4';
				$footer_version = apply_filters( 'pizzaro_home_v5_footer_version', $footer_style );
				break;
			case 'template-homepage-v6.php':
				$home_v6 		= pizzaro_get_home_v6_meta();
				$footer_style 	= ! empty( $home_v6['footer_style'] ) ? $home_v6['footer_style'] : 'v5';
				$footer_version = apply_filters( 'pizzaro_home_v6_footer_version', $footer_style );
				break;
			case 'template-homepage-v7.php':
				$home_v7 		= pizzaro_get_home_v7_meta();
				$footer_style 	= ! empty( $home_v7['footer_style'] ) ? $home_v7['footer_style'] : 'v4';
				$footer_version = apply_filters( 'pizzaro_home_v7_footer_version', $footer_style );
				break;
			default:
				$footer_version = apply_filters( 'pizzaro_footer_version', 'v1' );
		}
		
		return $footer_version;
	}
}

require get_template_directory() . '/inc/functions/menu.php';
