<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package pizzaro
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/**
	 * Functions hooked in to pizzaro_page add_action
	 *
	 * @hooked pizzaro_page_header          - 10
	 * @hooked pizzaro_page_content         - 20
	 * @hooked pizzaro_init_structured_data - 30
	 */
	do_action( 'pizzaro_page' );
	?>
</div><!-- #post-## -->
