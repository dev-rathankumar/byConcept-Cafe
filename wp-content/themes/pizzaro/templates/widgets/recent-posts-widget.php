<?php

$r = new WP_Query( apply_filters( 'pizzaro_widget_recent_posts_args', array(
	'posts_per_page'      => $instance['number'],
	'no_found_rows'       => true,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true
) ) );

if ($r->have_posts()) :

	echo wp_kses_post( $args['before_widget'] );

	if ( ! empty( $instance['title'] ) ) {
		echo wp_kses_post( $args['before_title'] . $instance['title'] . $args['after_title'] );
	}

	?>
	<ul>
	<?php while ( $r->have_posts() ) : $r->the_post(); ?>
		<li>
			<?php echo pizzaro_post_thumbnail( 'medium', true, true ); ?>
			<div class="post-content">
				<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
					<span class="comments-link"><?php comments_popup_link( '&nbsp;', '&nbsp;1', '&nbsp;%' ); ?></span>
				<?php endif; ?>
				<?php if ( $instance['show_date'] ) : ?>
					<span class="post-date"><?php echo get_the_date(); ?></span>
				<?php endif; ?>
				<a class ="post-name" href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
			</div>
		</li>
	<?php endwhile; ?>
	</ul>
	<?php

	echo wp_kses_post( $args['after_widget'] );

endif;

wp_reset_postdata();
