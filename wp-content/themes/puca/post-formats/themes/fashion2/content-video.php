<?php
/**
 *
 * The default template for displaying content
 * @since 1.0
 * @version 1.2.0
 *
 */
$videolink =  get_post_meta( get_the_ID(),'tbay_post_video_link', true );

if( !(isset($videolink) && $videolink) ) {
	$content = apply_filters( 'the_content', get_the_content() );
	$video = false;

	// Only get video from the content if a playlist isn't present.
	if ( false === strpos( $content, 'wp-playlist-script' ) ) {
		$video = get_media_embedded_in_content( $content, array( 'video', 'object', 'embed', 'iframe' ) );
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
	        <span class="entry-date"><?php echo puca_time_link(); ?></span>
        	<div class="entry-header">

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
		        </div>
				<div class="meta-info">
					<span class="author"><?php echo get_avatar(puca_tbay_get_id_author_post(), 'puca_avatar_post_carousel'); ?> <?php the_author_posts_link(); ?></span>

					

					<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
						<span class="comments-link"><i class="icons icon-bubbles"></i> <?php comments_popup_link( esc_html__('0 comment', 'puca'), esc_html__('1 comment', 'puca'), esc_html__( '% comments', 'puca' ) ); ?></span>
					<?php endif; ?>
					<?php puca_custom_post_category(); ?>
				</div>
			</div>
			<?php the_excerpt(); ?>

				<div class="post-excerpt entry-content">
					<?php 
						$class_video = ($videolink) ? 'post-preview' : '';
					?>
					<div class="content-image <?php echo esc_attr( $class_video ); ?>">
						<?php if( $videolink ) : ?>
							<div class="video-thumb video-responsive"><?php echo wp_oembed_get( $videolink ); ?></div>
						<?php elseif( has_post_thumbnail() ) : ?>
							<?php puca_tbay_post_thumbnail(); ?>
						<?php endif; ?>
					</div>
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
	
    	<?php if( puca_tbay_blog_image_sizes_full() ) : ?>
		
	    <div class="entry-content"> 
           <div class="entry">
				<div class="meta-info">
					<span class="entry-date"><?php echo puca_time_link(); ?></span>
				</div>
				
                 <div class="entry-meta">
                    <?php
                        if (get_the_title()) {
                        ?>
                            <h4 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h4>
                        <?php
                    }
                    ?>
                     
                </div>

                <?php puca_custom_post_category(); ?>

			</div>
        </div>
		<?php 
			$class_video = ($videolink) ? 'post-preview' : '';
		?>
    	<div class="content-image entry-thumb <?php echo esc_attr( $class_video ); ?>">
			<?php if( $videolink ) : ?>
				<div class="video-thumb video-responsive"><?php echo wp_oembed_get( $videolink ); ?></div>
			<?php elseif ( ! empty( $video ) ) :
					foreach ( $video as $video_html ) {
						echo '<div class="entry-video">';
							echo trim($video_html);
						echo '</div>';
					}
			?>
			<?php elseif( has_post_thumbnail() ) : ?>
				<?php puca_tbay_post_thumbnail(); ?>
			<?php endif; ?>
		</div>
		<?php
			if ( has_excerpt()) {
				the_excerpt();
			} else {
				?>
					<div class="entry-description"><?php echo puca_tbay_substring(get_the_content(), 25, '...' ); ?></div>
				<?php
			}
		?>
		<div class="readmore"><a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Read More', 'puca' ); ?>"><?php esc_html_e( 'Read More', 'puca' ); ?></a></div>

	    <?php else : ?>

		   <?php 
				$class_video = ($videolink) ? 'post-preview' : '';
			?>
			<div class="content-image <?php echo esc_attr( $class_video ); ?>">
				<?php if( $videolink ) : ?>
					<div class="video-thumb video-responsive"><?php echo wp_oembed_get( $videolink ); ?></div>
				<?php elseif ( ! empty( $video ) ) :
						foreach ( $video as $video_html ) {
							echo '<div class="entry-video">';
								echo trim($video_html);
							echo '</div>';
						}
				?>
				<?php elseif( has_post_thumbnail() ) : ?>
					<?php puca_tbay_post_thumbnail(); ?>
				<?php endif; ?>
			</div>
			<div class="entry-content"> 
           <div class="entry">
				<div class="meta-info">
					<span class="entry-date"><?php echo puca_time_link(); ?></span>
				</div>
				
                 <div class="entry-meta">
                    <?php
                        if (get_the_title()) {
                        ?>
                            <h4 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h4>
                        <?php
                    }
                    ?>
                     
                </div>

                <?php puca_custom_post_category(); ?>

                <div class="readmore"><a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Read More', 'puca' ); ?>"><?php esc_html_e( 'Read More', 'puca' ); ?></a></div>

			</div>
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