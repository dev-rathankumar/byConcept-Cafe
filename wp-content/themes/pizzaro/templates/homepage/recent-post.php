<?php
/**
 * Posts Block
 *
 * @package Pizzaro/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$section_class = empty( $section_class ) ? 'section-recent-post' : 'section-recent-post ' . $section_class;
if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$style_attr = '';
if( isset( $bg_choice ) && $bg_choice == 'color' && ! empty( $bg_color ) ) {
	$style_attr = 'background-color:' . $bg_color  . '; height:' . $height . 'px;';
} elseif ( ! empty( $bg_image[0] ) ) {
	$style_attr = 'background-size: cover; background-position: center center; background-image: url( ' . esc_url( $bg_image[0] ) . ' ); height: ' . esc_attr( $bg_image[2] ) . 'px;';
}

$query_args = array(
	'post_type'				=> 'post',
	'post_status'			=> 'publish',
	'ignore_sticky_posts'	=> 1,
	'orderby'				=> 'date',
	'order'					=> 'desc',
	'posts_per_page'		=> 1,
);

if( ! empty( $post_choice ) ) {
	if( $post_choice == 'specific' && ! empty( $post_id ) ) {
		$query_args['post__in'] = explode(",", $post_id);
	} elseif( $post_choice == 'random' ) {
		$query_args['orderby'] = 'rand';
	}
}

$recent_posts = new WP_Query( $query_args );
if ( $recent_posts->have_posts() ) : ?>
<div class="<?php echo esc_attr( $section_class ); ?>" <?php if ( !empty( $animation ) ) : ?>data-animation="<?php echo esc_attr( $animation );?>"<?php endif; ?> <?php if ( !empty( $style_attr ) ) : ?>style="<?php echo esc_attr( $style_attr );?>"<?php endif; ?>>
	<div class="post-wrap">
		<?php if ( ! empty( $section_title ) ) : ?>
			<h2 class="section-title"><?php echo wp_kses_post( $section_title ); ?></h2>
		<?php endif; ?>

		<?php while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); ?>
			<div class="post-item">

				<div class="post-info">

					<h3 class="post-title"><a href="<?php echo esc_url( get_permalink() );?>"><?php echo get_the_title(); ?></a></h3>

					<?php
						$excerpt = get_the_excerpt();
						echo '<p>' . pizzaro_custom_excerpt( $excerpt, 25 ) . '</p>';
					?>

					<?php if ( isset( $show_read_more ) && $show_read_more ) : ?>
						<a class="btn-more" href="<?php echo esc_url( get_permalink() );?>"><?php echo wp_kses_post( __( 'Read more', 'pizzaro' ) ); ?></a>
					<?php endif; ?>

				</div>

			</div>
		<?php endwhile; ?>
	</div>
</div>
<?php endif;
wp_reset_postdata();
