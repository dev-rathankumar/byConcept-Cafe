<?php
/**
 * Template used to display post content.
 *
 * @package pizzaro
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">
		<?php
		$post_url = get_post_meta ( get_the_ID() , 'postformat_link_url' , true );

		the_content(
			sprintf(
				__( 'Continue reading %s', 'pizzaro' ),
				'<span class="screen-reader-text">' . get_the_title() . '</span>'
			)
		);

		if( ! empty( $post_url ) ) {
			?>
			<p>
				<a href="<?php echo esc_url( $post_url ); ?>" target="_blank">
					<span><?php echo esc_url( $post_url ); ?></span>
				</a>
			</p>
			<?php
		}
		
		wp_link_pages( array(
			'before' => '<div class="page-links">' . __( 'Pages:', 'pizzaro' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->