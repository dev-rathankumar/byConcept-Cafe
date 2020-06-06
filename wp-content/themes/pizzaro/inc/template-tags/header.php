<?php

if ( ! function_exists( 'pizzaro_skip_links' ) ) {
	/**
	 * Skip links
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function pizzaro_skip_links() {
		?>
		<a class="skip-link screen-reader-text" href="#site-navigation"><?php esc_attr_e( 'Skip to navigation', 'pizzaro' ); ?></a>
		<a class="skip-link screen-reader-text" href="#content"><?php esc_attr_e( 'Skip to content', 'pizzaro' ); ?></a>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_site_branding' ) ) {
	/**
	 * Site branding wrapper and display
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function pizzaro_site_branding() {
		?>
		<div class="site-branding">
			<?php pizzaro_site_title_or_logo(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_site_title_or_logo' ) ) {
	/**
	 * Display the site title or logo
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function pizzaro_site_title_or_logo() {
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			the_custom_logo();
		} elseif ( function_exists( 'jetpack_has_site_logo' ) && jetpack_has_site_logo() ) {
			jetpack_the_site_logo();
		} elseif ( apply_filters( 'pizzaro_site_logo_svg', true ) ) {
			echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="custom-logo-link" rel="home">';
			pizzaro_get_template( 'global/logo-svg.php' );
			echo '</a>';
		} else {
			echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="custom-logo-link" rel="home">';
			?>
			<h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
			<?php if ( '' != get_bloginfo( 'description' ) ) : ?>
				<p class="site-description"><?php bloginfo( 'description' ); ?></p>
			<?php endif;
			echo '</a>';
		}
	}
}

if ( ! function_exists( 'pizzaro_primary_navigation' ) ) {
	/**
	 * Display Primary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function pizzaro_primary_navigation() {
		if ( apply_filters( 'pizzaro_show_primary_navigation', true ) ) {
			?>
			<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'pizzaro' ); ?>">
				<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span class="close-icon"><i class="po po-close-delete"></i></span><span class="menu-icon"><i class="po po-menu-icon"></i></span><span class=" screen-reader-text"><?php echo esc_attr( apply_filters( 'pizzaro_menu_toggle_text', esc_html__( 'Menu', 'pizzaro' ) ) ); ?></span></button>

				<div class="primary-navigation">
					<?php
					wp_nav_menu( apply_filters( 'pizzaro_main_menu_args', array(
						'theme_location'	=> 'main_menu',
						'container'			=> false,
						'fallback_cb'		=> 'pizzaro_nav_menu_fallback',
					) ) );
					?>
				</div>

				<div class="handheld-navigation">
					<?php
					wp_nav_menu( apply_filters( 'pizzaro_handheld_menu_args', array(
						'theme_location'	=> 'handheld',
						'container'			=> false,
						'fallback_cb'		=> 'pizzaro_nav_menu_fallback',
						'items_wrap'		=> '<span class="phm-close">' . apply_filters( 'pizzaro_handheld_menu_close_button_text', esc_html__( 'Close', 'pizzaro' ) ) . '</span><ul id="%1$s" class="%2$s">%3$s</ul>'
					) ) );
					?>
				</div>

			</nav><!-- #site-navigation -->
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_secondary_navigation' ) ) {
	/**
	 * Display Secondary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function pizzaro_secondary_navigation() {
		if ( apply_filters( 'pizzaro_show_secondary_navigation', true ) && has_nav_menu( 'food_menu' ) ) {
			?>
			<nav class="secondary-navigation" role="navigation" aria-label="<?php esc_html_e( 'Secondary Navigation', 'pizzaro' ); ?>">
				<?php
					wp_nav_menu( apply_filters( 'pizzaro_food_menu_args', array(
						'theme_location'	=> 'food_menu',
						'container'			=> false,
						'fallback_cb'		=> 'pizzaro_nav_menu_fallback',
					) ) );
				?>
			</nav><!-- #secondary-navigation -->
			<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_header_phone' ) ) {
	/**
	 * Displays phone number in the header
	 */
	function pizzaro_header_phone() {
		$header_phone_args = apply_filters( 'pizzaro_header_phone_args', array(
			'text'			=> esc_html__( 'Call and Order in', 'pizzaro' ),
			'phone_numbers'	=> array(
				array(
					'city'		=> esc_html__( 'London', 'pizzaro' ),
					'number'	=> '54 548 779 654'
				),
				array(
					'city'		=> esc_html__( 'Paris', 'pizzaro' ),
					'number'	=> '33 398 621 710'
				),
				array(
					'city'		=> esc_html__( 'New York', 'pizzaro' ),
					'number'	=> '718 54 674 021'
				)
			)
		) );

		if ( apply_filters( 'pizzaro_show_header_phone_numbers', true ) && ! empty( $header_phone_args['phone_numbers'] ) ) : ?>
		<div class="header-phone-numbers">
			<div class="header-phone-numbers-wrap">
				<span class="intro-text"><?php echo esc_html( $header_phone_args['text'] ); ?></span>
				<select class="select-city-phone-numbers" name="city-phone-numbers" id="city-phone-numbers">
					<?php foreach ( $header_phone_args['phone_numbers'] as $key => $phone_number ) { ?>
						<option value="<?php echo esc_attr( $phone_number['number'] ); ?>"><?php echo esc_html( $phone_number['city'] ); ?></option>
					<?php } ?>
				</select>
			</div>
			<span id="city-phone-number-label" class="phone-number"></span>
		</div>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_header_navigation_link' ) ) {
	function pizzaro_header_navigation_link() {
		$link_args = apply_filters( 'pizzaro_header_navigation_link_args', array(
			'my-account' =>	array(
				'title'	=> is_user_logged_in() ? esc_html__( 'My Account', 'pizzaro' ) : esc_html__( 'Login / Register', 'pizzaro' ),
				'link'	=> is_woocommerce_activated() ? get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) : wp_login_url(),
				'icon'	=> '',
				'class'	=> 'my-account',
			)
		) );
		if ( apply_filters( 'pizzaro_show_header_navigation_link', true ) && ! empty( $link_args ) ) : ?>
		<div class="header-nav-links">
			<?php if( is_array( $link_args ) && ! empty( $link_args ) ) : ?>
				<ul>
					<?php foreach ( $link_args as $key => $link_arg ) : ?>
						<li>
							<?php
								$class = isset( $link_arg['class'] ) ? $link_arg['class'] : '';
								$link = isset( $link_arg['link'] ) ? $link_arg['link'] : '#';
								$icon = isset( $link_arg['icon'] ) ? $link_arg['icon'] : '';
								$title = isset( $link_arg['title'] ) ? $link_arg['title'] : '';
							?>
							<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $link ); ?>">
								<i class="<?php echo esc_attr( $icon ); ?>"></i>
								<?php echo esc_html( $title ) ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_header_info_wrapper' ) ) {
	/**
	 * The info wrapper
	 */
	function pizzaro_header_info_wrapper() {
		echo '<div class="header-info-wrapper">';
	}
}

if ( ! function_exists( 'pizzaro_header_info_wrapper_close' ) ) {
	/**
	 * The info wrapper close
	 */
	function pizzaro_header_info_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'pizzaro_header_wrapper' ) ) {
	/**
	 * The header wrapper open
	 */
	function pizzaro_header_wrapper() {
		echo '<div class="header-wrap">';
	}
}

if ( ! function_exists( 'pizzaro_header_wrapper_close' ) ) {
	/**
	 * The header wrapper close
	 */
	function pizzaro_header_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'pizzaro_secondary_navigation_wrapper' ) ) {
	/**
	 * The secondary navigation wrapper
	 */
	function pizzaro_secondary_navigation_wrapper() {
		echo '<div class="pizzaro-secondary-navigation">';
	}
}

if ( ! function_exists( 'pizzaro_secondary_navigation_wrapper_close' ) ) {
	/**
	 * The secondary navigation wrapper close
	 */
	function pizzaro_secondary_navigation_wrapper_close() {
		echo '</div>';
	}
}
