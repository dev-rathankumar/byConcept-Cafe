<?php
/**
 * Template used to display post content on single pages.
 *
 * @package pizzaro
 */

$post_format = get_post_format();

if( $post_format == 'quote' || $post_format == 'link' || $post_format == 'aside' || $post_format == 'status' ) {
	get_template_part( 'content', $post_format );
} else {
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php
		do_action( 'pizzaro_single_post_top' );

		/**
		 * Functions hooked into pizzaro_single_post add_action
		 *
		 * @hooked pizzaro_post_header          - 10
		 * @hooked pizzaro_post_meta            - 20
		 * @hooked pizzaro_post_content         - 30
		 * @hooked pizzaro_init_structured_data - 40
		 */
		do_action( 'pizzaro_single_post' );

		/**
		 * Functions hooked in to pizzaro_single_post_after action
		 *
		 * @hooked pizzaro_post_nav         - 10
		 * @hooked pizzaro_display_comments - 20
		 */
		do_action( 'pizzaro_single_post_bottom' );
		?>

	</article><!-- #post-## -->
	<?php
}