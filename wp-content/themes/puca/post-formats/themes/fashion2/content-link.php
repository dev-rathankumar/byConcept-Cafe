<?php
/**
 *
 * The default template for displaying content
 * @since 1.0
 * @version 1.2.0
 *
 */
$custom_text = get_post_meta( get_the_ID(),'tbay_post_link_text', true );
$custom_link = get_post_meta( get_the_ID(),'tbay_post_link_link', true );
?>
<!-- /post-standard -->
<?php if ( ! is_single() ) : ?>
<div  class="post-list clearfix">
<?php endif; ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( is_single() ) : ?>
	<div class="entry-single">
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
			    <?php if( !empty($custom_text) &&  !empty($custom_link) ) : ?>
					<div class="link-wrap ">
						<a href="<?php echo esc_url($custom_link); ?>" alt="<?php echo esc_attr($custom_text); ?>"><?php echo esc_html($custom_text); ?></a>
					</div>
				<?php elseif( has_post_thumbnail() ) : ?>
					<?php puca_tbay_post_thumbnail(); ?>
				<?php endif; ?>
				
			</div>

			<div class="post-excerpt entry-content"><?php the_content( esc_html__( 'Read More', 'puca' ) ); ?></div><!-- /entry-content -->
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
    	<?php if( !empty($custom_text) &&  !empty($custom_link) ) : ?>
			<div class="link-wrap ">
				<a href="<?php echo esc_url($custom_link); ?>" alt="<?php echo esc_attr($custom_text); ?>"><?php echo esc_html($custom_text); ?></a>
				<span class="post-type"><?php puca_tbay_icon_post_formats(); ?></span>
			</div>
		<?php elseif( has_post_thumbnail() ) : ?>
			<?php puca_tbay_post_thumbnail(); ?>
		<?php endif; ?>
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

			<?php if( !empty($custom_text) &&  !empty($custom_link) ) : ?>
				<div class="link-wrap ">
					<a href="<?php echo esc_url($custom_link); ?>" alt="<?php echo esc_attr($custom_text); ?>"><?php echo esc_html($custom_text); ?></a>
				</div>
			<?php elseif( has_post_thumbnail() ) : ?>
				<?php puca_tbay_post_thumbnail(); ?>
			<?php endif; ?>
			
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