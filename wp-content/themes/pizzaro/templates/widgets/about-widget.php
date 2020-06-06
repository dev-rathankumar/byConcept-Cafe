<?php

echo wp_kses_post( $args['before_widget'] );

	echo '<div class="about-image">';

	if ( ! empty( $instance['image'] ) ) {
		?><img class="featured-image" src="<?php echo esc_url( $instance['image'] ); ?>"><?php
	}

	if ( ! empty( $instance['logo'] ) ) {
		?><img class="logo" src="<?php echo esc_url( $instance['logo'] ); ?>"><?php
	}

	echo '</div>';

	echo '<div class="about-info">';

	if ( ! empty( $instance['title'] ) ) {
		echo wp_kses_post( '<h2>' . $instance['title'] . '</h2>' );
	}

	if ( ! empty( $instance['desc'] ) ) {
		echo wp_kses_post( '<p>' . $instance['desc'] . '</p>' );
	}

	$social_networks 		= apply_filters( 'pizzaro_about_widget_social_networks', pizzaro_get_social_networks() );
	$social_links_output 	= '';
	$social_link_html		= apply_filters( 'pizzaro_about_widget_social_link_html', '<a class="%1$s" href="%2$s"></a>' );

	foreach ( $social_networks as $social_network ) {
		if ( isset( $social_network[ 'link' ] ) && !empty( $social_network[ 'link' ] ) ) {
			$social_links_output .= sprintf( '<li>' . $social_link_html . '</li>', $social_network[ 'icon' ], $social_network[ 'link' ] );
		}
	}

	if ( $instance['show_social_links'] && ! empty( $social_links_output ) ) {
		?><ul class="social-icons list-inline">
			<?php echo wp_kses_post( $social_links_output ); ?>
		</ul><?php
	}

	echo '</div>';

echo wp_kses_post( $args['after_widget'] );
