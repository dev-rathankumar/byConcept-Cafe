<?php

if ( ! function_exists( 'pizzaro_footer_static_content' ) ) {
	/**
	 * Display the static content before footer
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function pizzaro_footer_static_content() {
		$static_block_id = apply_filters( 'pizzaro_footer_static_block_id', '' );
		if( ! empty( $static_block_id ) ) :
			$static_block = get_post( $static_block_id );
			echo '<div class="footer-v1-static-content">' . do_shortcode( $static_block->post_content ) . '</div>';
		?>
		<?php
		endif;
	}
}

if ( ! function_exists( 'pizzaro_footer_logo' ) ) {
	/**
	 * Display the logo at footer
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function pizzaro_footer_logo() {
		ob_start();
		pizzaro_site_title_or_logo();
		$footer_logo = apply_filters( 'pizzaro_footer_logo_html', ob_get_clean() );
		echo '<div class="footer-logo">' . $footer_logo . '</div>';
	}
}

if ( ! function_exists( 'pizzaro_social_icons' ) ) {
	/**
	 * Displays footer social icons
	 */
	function pizzaro_social_icons() {
		$social_networks 		= apply_filters( 'pizzaro_set_social_networks', pizzaro_get_social_networks() );
		$social_links_output 	= '';
		$social_link_html		= apply_filters( 'pizzaro_footer_social_link_html', '<a class="%1$s" href="%2$s"></a>' );

		foreach ( $social_networks as $social_network ) {
			if ( isset( $social_network[ 'link' ] ) && !empty( $social_network[ 'link' ] ) ) {
				$social_links_output .= sprintf( '<li>' . $social_link_html . '</li>', $social_network[ 'icon' ], $social_network[ 'link' ] );
			}
		}

		if ( apply_filters( 'pizzaro_show_footer_social_icons', true ) && ! empty( $social_links_output ) ) {

			ob_start();
			?>
			<div class="footer-social-icons">
				<span class="social-icon-text"><?php echo esc_html( apply_filters( 'pizzaro_footer_social_icons_text', esc_html__( 'Follow us', 'pizzaro' ) ) ); ?></span>
				<ul class="social-icons list-unstyled">
					<?php echo wp_kses_post( $social_links_output ); ?>
				</ul>
			</div>
			<?php
			echo apply_filters( 'pizzaro_footer_social_links_html', ob_get_clean() );
		}
	}
}

if ( ! function_exists( 'pizzaro_footer_address' ) ) {
	/**
	 * Displays store address at the footer
	 */
	function pizzaro_footer_address() {
		$address_args = apply_filters( 'pizzaro_footer_site_address_args', array(
			'name'    => esc_html__( 'Pizzaro Restaurant', 'pizzaro' ),
			'address' => esc_html__( '901-947 South Drive, Houston, TX 77057, USA', 'pizzaro' ),
			'tel_no'  => esc_html__( 'Telephone: +1 555 1234', 'pizzaro' ),
			'fax_no'  => esc_html__( 'Fax: +1 555 4444', 'pizzaro' )
		) );
		if ( apply_filters( 'pizzaro_show_footer_site_address', true ) && ! empty( $address_args ) ) : ?>
		<div class="site-address">
			<ul class="address">
				<?php foreach( $address_args as $key => $address_arg ) : ?>
				<li><?php echo esc_html( $address_arg ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_footer_action' ) ) {
	/**
	 * Displays an action button at the footer
	 */
	function pizzaro_footer_action() {
		$action_button_args = apply_filters( 'pizzaro_footer_action_button_args', array(
			'text'  => esc_html__( 'Find us on Map', 'pizzaro' ),
			'icon'  => 'po po-map-marker',
		) );
		if ( apply_filters( 'pizzaro_show_footer_action_button', true ) && ! empty( $action_button_args ) ) : ?>
		<a role="button" class="footer-action-btn" data-toggle="collapse" href="#footer-map-collapse">
			<i class="<?php echo esc_attr( $action_button_args['icon'] );?>"></i>
			<?php echo esc_html( $action_button_args['text'] ); ?>
		</a>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_footer_v1_map' ) ) {
	/**
	 * Displays Google map in footer v1
	 */
	function pizzaro_footer_v1_map() {
		?>
		<div id="footer-map-collapse" class="footer-map collapse"><?php echo pizzaro_map_content(); ?></div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_footer_newsletter' ) ) {
	/**
	 * Displays an newsletter at the footer
	 */
	function pizzaro_footer_newsletter() {
		if ( apply_filters( 'pizzaro_show_footer_newsletter', true ) ) : ?>
		<div class="footer-newsletter">
			<?php pizzaro_newsletter_form(); ?>
		</div>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_credit' ) ) {
	/**
	 * Display the theme credit
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function pizzaro_credit() {
		if ( apply_filters( 'pizzaro_show_credit_info', true ) ) : ?>
		<div class="site-info">
			<p class="copyright"><?php echo wp_kses_post( apply_filters( 'pizzaro_copyright_text', $content = sprintf( esc_html__( 'Copyright &copy; %s %s Theme. All rights reserved.', 'pizzaro' ), date( 'Y' ), get_bloginfo( 'name' ) ) ) ); ?></p>
		</div><!-- .site-info -->
		<?php endif;
	}
}
