<?php
/**
 * Events Block
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$section_class = empty( $section_class ) ? 'section-events' : $section_class . ' section-events';

if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$posts = tribe_get_events( array(
	'eventDisplay'		=> 'list',
	'posts_per_page'	=> 2,
) );

if ( count( $posts ) > 0 ) : ?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( ! empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation ); ?>"<?php endif; ?>>

	<?php if ( ! empty( $pre_title ) ) : ?>
		<h3 class="pre-title"><span><?php echo wp_kses_post( $pre_title ); ?></span></h3>
	<?php endif; ?>

	<?php if ( ! empty( $section_title ) ) : ?>
		<h2 class="section-title"><?php echo wp_kses_post( $section_title ); ?></h2>
	<?php endif; ?>

	<div class="events">
		<?php foreach ( $posts as $post ) : ?>
			<?php setup_postdata( $post ); ?>
			<?php $event_id = get_the_ID(); ?>
			<div class="event">
				<div class="media">
					<div class="media-left">
						<div class="event-date">
							<div class="date">
								<?php echo tribe_get_start_date( $event_id, false, 'd' ); ?>
							</div>
							<div class="month">
								<?php echo tribe_get_start_date( $event_id, false, 'M' ); ?>
							</div>
						</div>
					</div>
					<div class="media-body">
						<h2>
							<a class="tribe-event-url" href="<?php echo esc_url( tribe_get_event_link() ); ?>" title="<?php the_title_attribute() ?>" rel="bookmark"><?php the_title(); ?></a>
						</h2>
						<div class="time-venue">
							<div class="time"><?php echo tribe_get_start_time( $event_id ); ?> - <?php echo tribe_get_end_time( $event_id ); ?></div>
							<?php if( tribe_has_venue() ) : ?>
								<div class="venue">
									<?php
										echo tribe_get_venue();
										if( tribe_get_address() ) {
											echo ' - ' . tribe_get_address();
										}
										if( tribe_get_city() ) {
											echo ', '. tribe_get_city();
										}
									?>
								</div>
							<?php endif; ?>
						</div>
						<p>
							<?php
								$excerpt = get_the_content();
								echo pizzaro_custom_excerpt( $excerpt, 20 );
							?>
						</p>
						<a class="btn-more" href="<?php echo esc_url( tribe_get_event_link() ); ?>"><?php echo wp_kses_post( __( 'Read more', 'pizzaro' ) ); ?></a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

</div>
<?php endif;
wp_reset_postdata();
