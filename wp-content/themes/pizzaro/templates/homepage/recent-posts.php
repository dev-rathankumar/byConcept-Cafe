<?php
/**
 * Posts Block
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$section_class = empty( $section_class ) ? 'section-recent-posts' : 'section-recent-posts ' . $section_class;
if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$query_args = array(
	'post_type'				=> 'post',
	'post_status'			=> 'publish',
	'ignore_sticky_posts'	=> 1,
	'orderby'				=> 'date',
	'order'					=> 'desc',
	'posts_per_page'		=> 12,
);

if( isset( $limit ) ) {
	$query_args['posts_per_page'] = $limit;
}

if( ! empty( $post_choice ) ) {
	if( $post_choice == 'specific' && ! empty( $post_id ) ) {
		$query_args['post__in'] = explode(",", $post_id);
	} elseif( $post_choice == 'random' ) {
		$query_args['orderby'] = 'rand';
	}
}

$recent_posts = new WP_Query( $query_args );
if ( $recent_posts->have_posts() ) : ?>
<section class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?>>

	<?php if ( ! empty( $pre_title ) ) : ?>
		<h3 class="pre-title"><span><?php echo wp_kses_post( $pre_title ); ?></span></h3>
	<?php endif; ?>

	<?php if ( ! empty( $section_title ) ) : ?>
		<h2 class="section-title"><?php echo wp_kses_post( $section_title ); ?></h2>
	<?php endif; ?>

	<div class="post-items">
		<?php while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); ?>
			<div class="post-item">

				<a class="post-thumbnail" href="<?php echo esc_url( get_permalink() );?>">
					<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'post-thumbnail' );
						} else {
							echo '<img alt="" src="//placehold.it/483x278" class="img-responsive" width="483" height="278" />';
						}
					?>
				</a>

				<div class="post-info">

					<h3 class="post-title"><a href="<?php echo esc_url( get_permalink() );?>"><?php echo get_the_title(); ?></a></h3>
					
					<?php
						$excerpt = get_the_excerpt();
						echo '<p>' . pizzaro_custom_excerpt( $excerpt, 25 ) . '</p>';
					?>

					<?php if ( isset( $show_read_more ) && $show_read_more ) : ?>
						<a class="btn-more" href="<?php echo esc_url( get_permalink() );?>"><?php echo wp_kses_post( __( 'Read more', 'pizzaro' ) ); ?></a>
					<?php endif; ?>

					<?php if ( isset( $show_comment_link ) && $show_comment_link && ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
						<span class="comments-link">
							<?php comments_popup_link( esc_html__( 'Leave a comment', 'pizzaro' ), esc_html__( '1 Comment', 'pizzaro' ), esc_html__( '% Comments', 'pizzaro' ) ); ?>
						</span>
					<?php endif; ?>

				</div>

			</div>
		<?php endwhile; ?>
	</div>

</section>
<?php endif;
wp_reset_postdata();