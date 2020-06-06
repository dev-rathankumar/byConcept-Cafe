<?php
/**
 * Template tags used in contact pages
 *
 * @package pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function pizzaro_get_default_contact_options() {
	$contact = array(
		'cma'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
		),
		'chr'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'title'				=> esc_html__( 'Contact Us', 'pizzaro' ),
			'description'		=> esc_html__( 'We are a second-generation family business established in 1972', 'pizzaro' ),
		),
		'cfa'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'form'				=> array (
				'title'			=> esc_html__( 'Leave us a Message', 'pizzaro' ),
				'description'	=> esc_html__( 'Aenean massa diam, viverra vitae luctus sed, gravida eget est. Etiam nec ipsum porttitor, consequat libero eu, dignissim eros. Nulla auctor lacinia enim id mollis.', 'pizzaro' ),
				'shortcode'		=> '[contact-form-7 title="Contact form 1"]'
			),
			'address'=> array (
				'title'			=> esc_html__( 'Our Address', 'pizzaro' ),
				'description'	=> sprintf( '%s<br>%s<br>%s', esc_html__( '17 Princess Road London, Greater London NW1 8JR, UK', 'pizzaro' ), esc_html__( 'Support (+800) 856 800 604', 'pizzaro' ), esc_html__( 'E-mail: info@pizzaro.com', 'pizzaro' ) ),
				'addresses'		=> array(
					array(
						'title'			=> esc_html__( 'Opening Hours', 'pizzaro' ),
						'address'		=> sprintf( '<ul><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">%s</span></li></ul>', esc_html__( 'Monday', 'pizzaro' ), esc_html__( 'Tuesday', 'pizzaro' ), esc_html__( 'Wednesday', 'pizzaro' ), esc_html__( 'Thursday', 'pizzaro' ), esc_html__( 'Friday', 'pizzaro' ), esc_html__( 'Saturday', 'pizzaro' ), esc_html__( 'Sunday', 'pizzaro' ), esc_html__( 'Closed', 'pizzaro' ) ),
					),
					array(
						'title'			=> esc_html__( 'Careers.', 'pizzaro' ),
						'address'		=> sprintf( '<p class="inner-right-md">%s<a href="mailto:contact@yourstore.com">%s</a></p>', esc_html__( 'If you are interested in employment opportunities at Pizzaro, please email us: ', 'pizzaro' ), 'contact@yourstore.com' ),
					)
				)
			)
		)
		
	);

	return apply_filters( 'pizzaro_get_default_contact_options', $contact );
}

function pizzaro_get_contact_meta( $merge_default = true ) {
	global $post;

	if ( isset( $post->ID ) ){
	
		$contact_options = json_decode( get_post_meta( $post->ID, '_contact_options', true ), true );
	
		if ( $merge_default ) {
			$default_options = pizzaro_get_default_contact_options();
			$contact = wp_parse_args( $contact_options, $default_options );
		} else {
			$contact = $contact_options;
		}
	
		return apply_filters( 'pizzaro_contact_meta', $contact, $post );
	}
}

if ( ! function_exists( 'pizzaro_contact_map' ) ) {
	/**
	 * Displays Contact Address
	 */
	function pizzaro_contact_map() {
		$contact 	= pizzaro_get_contact_meta();
		$cma_options 		= $contact['cma'];

		$is_enabled = isset( $cma_options['is_enabled'] ) ? $cma_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $cma_options['animation'] ) ? $cma_options['animation'] : '';
		$section_class = 'contact-map';
		if ( ! empty( $animation ) ) {
			$section_class .= ' animate-in-view';
		}
		?>
		<div class="<?php echo esc_attr( $section_class );?>" <?php if ( ! empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>
			<?php echo pizzaro_map_content(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_contact_header' ) ) {
	/**
	 * Display contact header
	 */
	function pizzaro_contact_header() {
		$contact 	= pizzaro_get_contact_meta();
		$chr_options 		= $contact['chr'];

		$is_enabled = isset( $chr_options['is_enabled'] ) ? $chr_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $chr_options['animation'] ) ? $chr_options['animation'] : '';

		$args = array(
			'title'			=> isset( $chr_options['title'] ) ? $chr_options['title'] : esc_html__( 'Contact Us', 'pizzaro' ),
			'description'	=> isset( $chr_options['description'] ) ? $chr_options['description'] : esc_html__( 'We are a second-generation family business established in 1972', 'pizzaro' ),
		);

		if ( ! empty( $args['title'] ) ) {
			?>
			<header class="contact-header">
				<h1 class="entry-title"><?php echo wp_kses_post( $args['title'] ); ?></h1>
				<?php if ( ! empty( $args['description'] ) ) { ?>
					<p class="description"><?php echo wp_kses_post( $args['description'] ); ?></p>
				<?php } ?>
			</header>
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_contact_form_with_address' ) ) {
	/**
	 * Displays Contact Form
	 */
	function pizzaro_contact_form_with_address() {
		$contact 	= pizzaro_get_contact_meta();
		$cfa_options 		= $contact['cfa'];

		$is_enabled = isset( $cfa_options['is_enabled'] ) ? $cfa_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = isset( $cfa_options['animation'] ) ? $cfa_options['animation'] : '';

		$form_args = array(
			'shortcode'		=> isset( $cfa_options['form']['shortcode'] ) ? $cfa_options['form']['shortcode'] : '[contact-form-7 title="Contact form 1"]',
			'title'			=> isset( $cfa_options['form']['title'] ) ? $cfa_options['form']['title'] : esc_html__( 'Leave us a Message', 'pizzaro' ),
			'description'	=> isset( $cfa_options['form']['description'] ) ? $cfa_options['form']['description'] : esc_html__( 'Aenean massa diam, viverra vitae luctus sed, gravida eget est. Etiam nec ipsum porttitor, consequat libero eu, dignissim eros. Nulla auctor lacinia enim id mollis.', 'pizzaro' ),
		);

		$info_args = array(
			'title'			=> isset( $cfa_options['address']['title'] ) ? $cfa_options['address']['title'] : esc_html__( 'Our Address', 'pizzaro' ),
			'description'	=> isset( $cfa_options['address']['description'] ) ? $cfa_options['address']['description'] : sprintf( '%s<br>%s<br>%s', esc_html__( '17 Princess Road London, Greater London NW1 8JR, UK', 'pizzaro' ), esc_html__( 'Support (+800) 856 800 604', 'pizzaro' ), esc_html__( 'E-mail: info@pizzaro.com', 'pizzaro' ) ),
			'addresses'		=> array(
				array(
					'title'			=> isset( $cfa_options['address']['addresses'][0]['title'] ) ? $cfa_options['address']['addresses'][0]['title'] : esc_html__( 'Opening Hours', 'pizzaro' ),
					'address'		=> isset( $cfa_options['address']['addresses'][0]['address'] ) ? $cfa_options['address']['addresses'][0]['address'] : sprintf( '<ul><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">12-6 PM</span></li><li class="clearfix"><span class="day">%s</span><span class="pull-right flip hours">%s</span></li></ul>', esc_html__( 'Monday', 'pizzaro' ), esc_html__( 'Tuesday', 'pizzaro' ), esc_html__( 'Wednesday', 'pizzaro' ), esc_html__( 'Thursday', 'pizzaro' ), esc_html__( 'Friday', 'pizzaro' ), esc_html__( 'Saturday', 'pizzaro' ), esc_html__( 'Sunday', 'pizzaro' ), esc_html__( 'Closed', 'pizzaro' ) ),
				),
				array(
					'title'			=> isset( $cfa_options['address']['addresses'][1]['title'] ) ? $cfa_options['address']['addresses'][1]['title'] : esc_html__( 'Careers.', 'pizzaro' ),
					'address'		=> isset( $cfa_options['address']['addresses'][1]['address'] ) ? $cfa_options['address']['addresses'][1]['address'] : sprintf( '<p class="inner-right-md">%s<a href="mailto:contact@yourstore.com">%s</a></p>', esc_html__( 'If you are interested in employment opportunities at Pizzaro, please email us: ', 'pizzaro' ), 'contact@yourstore.com' ),
				)
			)
		);

		$section_class = empty( $section_class ) ? 'contact-form-with-address' : 'contact-form-with-address ' . $section_class;
		?>
		<div class="<?php echo esc_attr( $section_class ); ?>">
			<div class="row">
				<div class="col-md-9 col-sm-9 col-xs-12">
					<div class="contact-form">
						<?php
							if( ! empty( $form_args['title'] ) ) {
								echo '<h2>' . esc_html( $form_args['title'] ) . '</h2>';
							}
							if( ! empty( $form_args['description'] ) ) {
								echo '<p>' . wp_kses_post( $form_args['description'] ) . '</p>';
							}

							echo do_shortcode( $form_args['shortcode'] );
						?>
					</div>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<div class="store-info">
						<?php
							if( ! empty( $info_args['title'] ) ) {
								echo '<h2>' . esc_html( $info_args['title'] ) . '</h2>';
							}
							if( ! empty( $info_args['description'] ) ) {
								echo '<p>' . wp_kses_post( $info_args['description'] ) . '</p>';
							}

							foreach( $info_args['addresses'] as $address ) {
								if( ! empty( $address['address'] ) ) {
									echo '<div class="address">';

									if( ! empty( $address['title'] ) ) {
										echo '<h3>' . esc_html( $address['title'] ) . '</h3>';
									}

									echo '<div class="address-info">' . $address['address'] . '</div>';

									echo '</div>';
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

if( ! function_exists( 'pizzaro_contactpage_hook_control' ) ) {
	function pizzaro_contactpage_hook_control() {
		if( is_page_template( array( 'template-contactpage.php' ) ) ) {
			remove_all_actions( 'pizzaro_contactpage' );

			$contact = pizzaro_get_contact_meta();
			add_action( 'pizzaro_contactpage', 'pizzaro_init_structured_data',					4 );
			add_action( 'pizzaro_contactpage', 'pizzaro_homepage_content',						5 );
			add_action( 'pizzaro_contactpage', 'pizzaro_contact_map',							isset( $contact['cma']['priority'] ) ? intval( $contact['cma']['priority'] ) : 10 );
			add_action( 'pizzaro_contactpage', 'pizzaro_contact_header',						isset( $contact['chr']['priority'] ) ? intval( $contact['chr']['priority'] ) : 20 );
			add_action( 'pizzaro_contactpage', 'pizzaro_contact_form_with_address',				isset( $contact['cfa']['priority'] ) ? intval( $contact['cfa']['priority'] ) : 30 );
		}
	}
}