<?php
/**
 * Template Tags for Footer v2
 */

if ( ! function_exists( 'pizzaro_footer_about_info' ) ) {
	/**
	 * Displays About Information in footer
	 */
	function pizzaro_footer_about_info() {
		$about_info = apply_filters( 'pizzaro_footer_about_info_args', array(
			'img_src'		=> '//placehold.it/435x330',
			'title'			=> esc_html__( 'About us', 'pizzaro' ),
			'description'	=> esc_html__( 'Proin ac semper mi. Phasellus magna elit, dapibus at egestas a, facilisis nec ligula. In vitae ex ante. Aliquam interdum maximus dui quis sodales. Cras vel mi diam. Phasellus mi ante, iaculis nec tempus ac, tincidunt sit amet eros. Fusce malesuada elit massa, ac eleifend  massa ligula, semper sed faucibus vitae, fermentum sed ex.', 'pizzaro' ),
			'button_text'	=> esc_html__( 'Read More &nbsp;&nbsp;&nbsp;&rarr;', 'pizzaro' ),
			'button_link'	=> '#',
		) );
		if ( apply_filters( 'pizzaro_show_footer_about_info', true ) && ! empty( $about_info ) ) : ?>
		<div class="footer-about-info">
			<div class="container">
				<div class="row">
					<div class="col-md-5 image">
						<img src="<?php echo esc_url( $about_info['img_src'] ); ?>" alt="<?php echo esc_attr( $about_info['title'] ); ?>" />
					</div>
					<div class="col-md-7 content">
						<?php if( ! empty( $about_info['title'] ) ) : ?>
						<h2><?php echo esc_html( $about_info['title'] ); ?></h2>
						<?php endif; ?>
						<?php if( ! empty( $about_info['description'] ) ) : ?>
						<p><?php echo esc_html( $about_info['description'] ); ?></p>
						<?php endif; ?>
						<?php if( ! empty( $about_info['button_link'] ) && ! empty( $about_info['button_text'] ) ) : ?>
						<a href="<?php echo esc_url( $about_info['button_link'] ); ?>"><?php echo esc_html( $about_info['button_text'] ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_footer_v2_map' ) ) {
	/**
	 * Displays Google map in footer v2
	 */
	function pizzaro_footer_v2_map() {
		if ( apply_filters( 'pizzaro_show_footer_v2_map', true ) ) : ?>
		<div class="footer-map">
			<?php echo pizzaro_map_content(); ?>
		</div>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_footer_store_info') ) {
	/**
	 * Displays Footer Info like logo, working days, social icons
	 */
	function pizzaro_footer_store_info() {
		pizzaro_hook_footer_store_info(); ?>
		<div class="footer-store-info">
			<?php
			/**
			 *
			 */
			do_action( 'pizzaro_footer_store_info' ); ?>
		</div><!-- /.footer-store-info --><?php
	}
}

if ( ! function_exists( 'pizzaro_hook_footer_store_info' ) ) {
	/**
	 * Adds hooks to pizzaro_footer_store_info_action
	 */
	function pizzaro_hook_footer_store_info() {
		add_action( 'pizzaro_footer_store_info', 'pizzaro_footer_logo', 10 );
		add_action( 'pizzaro_footer_store_info', 'pizzaro_footer_store_timings', 20 );
		add_action( 'pizzaro_footer_store_info', 'pizzaro_social_icons', 30 );
	}
}

if ( ! function_exists( 'pizzaro_footer_store_timings' ) ) {
	/**
	 * Displays Footer Store Timings
	 */
	function pizzaro_footer_store_timings() {
		$store_timings = apply_filters( 'pizzaro_footer_store_timings', array(
			'timing_1' => array(
				'label'  => esc_html__( 'Monday - Thursday', 'pizzaro' ),
				'timing' => esc_html__( '11:00 - 21:00', 'pizzaro' ),
			),
			'timing_2' => array(
				'label'  => esc_html__( 'Friday - Saturday', 'pizzaro' ),
				'timing' => esc_html__( '11:30 - 22:00', 'pizzaro' ),
			),
			'timing_3' => array(
				'label'  => esc_html__( 'Sundays', 'pizzaro' ),
				'timing' => esc_html__( '12:00 - 20:00', 'pizzaro' ),
			),
		) );

		if ( apply_filters( 'pizzaro_show_footer_store_timings', true ) && ! empty( $store_timings ) ) : ?>
		<ul class="store-timings">
		<?php foreach( $store_timings as $store_timing ) : ?>
			<li>
				<span class="store-timing-label"><?php echo esc_html( $store_timing['label'] ); ?></span>
				<span class="store-timing-value"><?php echo esc_html( $store_timing['timing'] ); ?></span>
			</li>
		<?php endforeach; ?>
		</ul><!-- /.store-timings -->
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_footer_contact_form' ) ) {
	/**
	 * Displays contact form at the footer
	 */
	function pizzaro_footer_contact_form() {
		ob_start();
		?>
		<form action="post">
			<div class="form-group">
				<label for="yourName"><?php echo esc_html__( 'Your Name', 'pizzaro' ); ?></label>
				<input type="text" class="form-control" id="yourName" placeholder="<?php echo esc_attr( esc_html__( 'Your Name', 'pizzaro' ) ); ?>">
			</div>
			<div class="form-group">
				<label for="emailAddress"><?php echo esc_html__( 'Email Address', 'pizzaro' ); ?></label>
				<input type="email" class="form-control" id="emailAddress" placeholder="<?php echo esc_attr( esc_html__( 'Email Address', 'pizzaro' ) ); ?>">
			</div>
			<div class="form-group">
				<label for="yourMessage"><?php echo esc_html__( 'Your Message to us..', 'pizzaro' ); ?></label>
				<textarea rows="10" cols="10" class="form-control" id="yourMessage" placeholder="<?php echo esc_attr( esc_html__( 'Your Message to us..', 'pizzaro' ) ); ?>"></textarea>
			</div>
			<div class="text-center"><button type="submit" class="btn btn-default button"><?php echo esc_html__( 'Send Message', 'pizzaro' ); ?></button></div>
		</form>
		<?php
		$form_content = apply_filters( 'pizzaro_footer_contact_form_content', ob_get_clean() );
		$form_title = apply_filters( 'pizzaro_footer_contact_form_title', esc_html__( 'Write Hello to Pizzaro', 'pizzaro' ) );
		if ( apply_filters( 'pizzaro_show_footer_contact_form', true ) && ! empty( $form_content ) ) {
			?><div class="footer-contact-form">
				<div class="contact-form">
					<h3 class="contact-form-title"><?php echo esc_html( $form_title ); ?></h3>
					<?php echo do_shortcode( $form_content ); ?>
				</div>
			</div><?php
		}
	}
}

if ( ! function_exists( 'pizzaro_footer_contact_info' ) ) {
	/**
	 * Displays contact info at the footer
	 */
	function pizzaro_footer_contact_info() {
		$address_args = apply_filters( 'pizzaro_footer_contact_info_args', array(
			'address' => array(
				'text' => esc_html__( '901-947 South Drive, Houston, TX 77057, USA', 'pizzaro' ),
				'icon' => 'po po-map-marker'
			),
			'tel_no'  => array(
				'text' => esc_html__( '+1 555 125 9455, +42 548 78 983', 'pizzaro' ),
				'icon' => 'fa fa-mobile'
			),
			'email'   => array(
				'text' => esc_html__( 'hello@pizzaro.com', 'pizzaro' ),
				'icon' => 'po po-mail-icon',
			),
		) );
		
		if ( apply_filters( 'pizzaro_show_footer_contact_info', true ) && ! empty( $address_args ) ) : ?>
		<div class="footer-contact-info">
			<ul class="address">
				<?php foreach( $address_args as $key => $address_arg ) : ?>
				<li><i class="<?php echo esc_attr( $address_arg['icon'] ); ?>"></i><span class="address-text"><?php echo esc_html( $address_arg['text'] ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif;
	}
}
