<?php
/**
 *
 * The default template for displaying content
 * @since 1.0
 * @version 1.2.0
 *
 */

$audiolink =  get_post_meta( get_the_ID(),'tbay_post_audio_link', true );

if( isset($audiolink) && $audiolink ) {

} else {
	$content = apply_filters( 'the_content', get_the_content() );
	$audio = false;
	// Only get audio from the content if a playlist isn't present.
	if ( false === strpos( $content, 'wp-playlist-script' ) ) {
		$audio = get_media_embedded_in_content( $content, array( 'audio' ) );
	}
}

?>
<!-- /post-standard -->
<?php if ( ! is_single() ) : ?>
<div  class="post-list clearfix">
<?php endif; ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( is_single() ) : ?>
	<div class="entry-single">
	<?php echo puca_tbay_post_media( get_the_excerpt() ); ?>
<?php endif; ?>
        <?php
			if ( is_single() ) : ?>
	        	<div class="entry-header audio">
	        		<div class="entry-meta">
			            <?php
			                if (get_the_title()) {
			                ?>
			                    <h1 class="entry-title">
			                       <?php the_title(); ?>
			                    </h1>
			                <?php
			            	}
			            ?>
						<span class="entry-date"><?php echo puca_time_link(); ?></span>
			        </div>
			        <div class="meta-info">
						<span class="author"><i class="icon-user icons"></i> <?php echo esc_html__( 'By', 'puca' ); ?> <?php the_author_posts_link(); ?></span>

						<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
						   <span class="comments-link"><i class="icon-bubbles icons"></i> <?php comments_popup_link( esc_html__( '0 comment', 'puca' ), esc_html__( '1 comment', 'puca' ), esc_html__( '% comments', 'puca' ) ); ?></span>
						<?php endif; ?>

						<span class="entry-category">
				            <i class="icon-folder-alt icons"></i>
				            <?php the_category(','); ?>
				      	</span>
					</div>
				</div>

				<div class="post-excerpt entry-content">
					<?php if( $audiolink ) : ?>
						<div class="audio-wrap audio-responsive"><?php echo wp_oembed_get( $audiolink ); ?></div>
					<?php elseif( has_post_thumbnail() ) : ?>
						<?php puca_tbay_post_thumbnail(); ?>
					<?php endif; ?>
					<?php the_content( esc_html__( 'Read More', 'puca' ) ); ?>
				</div><!-- /entry-content -->
				<?php
				wp_link_pages( array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'puca' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
					'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'puca' ) . ' </span>%',
					'separator'   => '<span class="screen-reader-text">, </span>',
				) );
				?>
				
				<div class="entry-bottom">
				<?php puca_tbay_post_tags(); ?>
				
				<?php 
					puca_tbay_post_share_box();
				?>
				</div>
			
		<?php endif; ?>
    <?php if ( ! is_single() ) : ?>

   	 	<?php if( !puca_tbay_blog_image_sizes_full() ) : ?>

   	 	<?php 
   	 		$class_audio = ($audiolink) ? 'post-preview' : '';
   	 	?>
	   	<div class="content-image entry-thumb <?php echo esc_attr( $class_audio ); ?>">
			<?php if( isset($audiolink) && $audiolink ) : ?>
				<div class="audio-wrap audio-responsive"><?php echo wp_oembed_get( $audiolink ); ?></div>
			<?php 
				elseif ( ! empty( $audio ) ) :
					foreach ( $audio as $audio_html ) {
						echo '<div class="entry-audio">';
							echo trim($audio_html);
						echo '</div><!-- .entry-audio -->';
					}
			?>
			<?php elseif( has_post_thumbnail() ) : ?>
				<?php puca_tbay_post_thumbnail(); ?>
			<?php endif; ?>

		</div>

	    <div class="entry-content audio  <?php echo ( !has_post_thumbnail() ) ? 'no-thumb' : ''; ?>">
	      	<div class="meta-info">
				<span class="entry-date"><?php echo puca_time_link(); ?></span>
				<?php
					if (get_the_title()) {
					?>
						<h4 class="entry-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h4>
					<?php
				}
				?>
				<span class="author"><i class="icon-user icons"></i><?php the_author_posts_link(); ?></span>
				<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
				   <span class="comments-link"><i class="icon-bubbles icons"></i> <?php comments_popup_link( esc_html__( '0 comment', 'puca' ), esc_html__( '1 comment', 'puca' ), esc_html__( '% comments', 'puca' ) ); ?></span>
				<?php endif; ?>
				
			</div>

	        <?php if( empty($audio) ) : ?>
		        <?php echo puca_tbay_post_media( get_the_excerpt() ); ?>
		        
				<?php
					if ( has_excerpt()) {
						echo puca_tbay_substring(get_the_excerpt(), 17, '');
					} else {
						?>
							<div class="entry-description"><?php echo puca_tbay_substring(get_the_content(), 25, '...' ); ?> <a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Continue reading', 'puca' ); ?>"><?php esc_html_e( 'Continue reading', 'puca' ); ?><i class="icon-arrow-right-circle icons"></i></a></div>
						<?php
					}
				?>

			<?php endif; ?>

	    </div> 
	    <?php else : ?>

			<?php if( isset($audiolink) && $audiolink ) : ?>
				<figure class="audio-wrap audio-responsive"><?php echo wp_oembed_get( $audiolink ); ?></figure>

			<?php elseif ( ! empty( $audio ) ) :
					foreach ( $audio as $audio_html ) {
						echo '<div class="entry-audio">';
							echo trim($audio_html);
						echo '</div><!-- .entry-audio -->';
					}
			?>
			<?php elseif( has_post_thumbnail() ) : ?>
				<?php puca_tbay_post_thumbnail(); ?>
			<?php endif; ?>

			<div class="entry-header audio">
				<div class="meta-info">
					<span class="entry-date"><?php echo puca_time_link(); ?></span>
					<?php
						if (get_the_title()) {
						?>
							<h4 class="entry-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h4>
						<?php
					}
					?>
					<span class="author"><i class="icon-user icons"></i><?php the_author_posts_link(); ?></span>
					<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
					   <span class="comments-link"><i class="icon-bubbles icons"></i> <?php comments_popup_link( esc_html__( '0 comment', 'puca' ), esc_html__( '1 comment', 'puca' ), esc_html__( '% comments', 'puca' ) ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		    <div class="entry-content <?php echo ( !has_post_thumbnail() ) ? 'no-thumb' : ''; ?>">
				<?php
					if( empty($audio) ) {
						if ( has_excerpt()) {
							the_excerpt();
						} else {
							?>
								<div class="entry-description"><?php echo puca_tbay_substring(get_the_content(), 25, '...' ); ?> <a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Continue reading', 'puca' ); ?>"><?php esc_html_e( 'Continue reading', 'puca' ); ?><i class="icon-arrow-right-circle icons"></i></a></div>
							<?php
						}
					}
				?>
		    </div>

		<?php endif; ?>
    <?php endif; ?>
    <?php if ( is_single() ) : ?>
</div>
<?php endif; ?>
</article>

<?php if ( ! is_single() ) : ?>
</div>
<?php endif; ?>