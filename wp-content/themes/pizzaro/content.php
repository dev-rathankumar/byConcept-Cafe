<?php
/**
 * Template used to display post content.
 *
 * @package pizzaro
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	/**
	 * Functions hooked in to pizzaro_loop_post action.
	 *
	 * @hooked pizzaro_post_header          - 10
	 * @hooked pizzaro_post_meta            - 20
	 * @hooked pizzaro_post_content         - 30
	 * @hooked pizzaro_init_structured_data - 40
	 */
	do_action( 'pizzaro_loop_post' );
	?>

</article><!-- #post-## -->