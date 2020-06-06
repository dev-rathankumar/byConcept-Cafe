<?php
/**
 *
 * The default template for displaying content
 * @since 1.0
 * @version 1.2.0
 *
 */

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
						<span class="entry-date"><i class="icons icon-clock"></i><?php echo puca_time_link(); ?></span>
						<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
						   <span class="comments-link"><i class="icon-bubbles icons"></i> <?php comments_popup_link( esc_html__( '0 comments', 'puca' ), esc_html__( '1 comment', 'puca' ), esc_html__( '% comments', 'puca' ) ); ?></span>
						<?php endif; ?>

						<span class="entry-category">
				            <i class="icon-folder-alt icons"></i><?php esc_html_e('Posted in', 'puca'); the_category(','); ?>
				      	</span>
					</div>
				</div>
				<?php 
					puca_tbay_post_share_box();
				?>

				<div class="post-excerpt entry-content">
					<?php
					if ( has_post_thumbnail() ) {
						?>
						<figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">

							<?php puca_tbay_post_thumbnail(); ?>
						</figure>
						<?php
					}
					?> 

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
				</div>
		<?php endif; ?>
		
		
    <?php if ( ! is_single() ) : ?>
	
		<?php if( !puca_tbay_blog_image_sizes_full() ) : ?>

		<?php
		 if ( has_post_thumbnail() ) {
		  ?>
		  <figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
		   <?php puca_tbay_post_thumbnail(); ?>
		  </figure>
		  <?php
		 }
		 ?>
		
	    <div class="entry-header">
	        <?php
	          if (get_the_title()) {
	          ?>
	            <h4 class="entry-title">
	              <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	            </h4>
	          <?php
	        }
	        ?>
	        <div class="meta-info">
		        <span class="entry-date"><i class="icons icon-clock"></i><?php echo puca_time_link(); ?></span>
				<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
				   <span class="comments-link"><i class="icon-bubbles icons"></i> <?php comments_popup_link( esc_html__( '0 comments', 'puca' ), esc_html__( '1 comment', 'puca' ), esc_html__( '% comments', 'puca' ) ); ?></span>
				<?php endif; ?>
	        </div>
	    </div>
	    <?php
			if ( has_excerpt()) {
				the_excerpt();
			} else {
				?>
					<div class="entry-description">
						<?php echo puca_tbay_substring(get_the_content(), 25, '...' ); ?>
						<div class="more">
							<a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Continue reading', 'puca' ); ?>"><?php esc_html_e( 'Continue reading', 'puca' ); ?> <i class="icon-arrow-right icons"></i></a>
						</div>
					</div>
				<?php
			}
		?>
		<?php else : ?>
			<?php
			 if ( has_post_thumbnail() ) {
			  ?>
			  <figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
			   <?php puca_tbay_post_thumbnail(); ?>
			  </figure>
			  <?php
			 }
			 ?>
			<div class="entry-header">
		     
		        <div class="meta-info">
			        <span class="entry-date"><i class="icons icon-clock"></i><?php echo puca_time_link(); ?></span>
					<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
					   <span class="comments-link"><i class="icon-bubbles icons"></i> <?php comments_popup_link( esc_html__( '0 comments', 'puca' ), esc_html__( '1 comment', 'puca' ), esc_html__( '% comments', 'puca' ) ); ?></span>
					<?php endif; ?>
		        </div>
		    </div>
		    <div class="entry-content <?php echo ( !has_post_thumbnail() ) ? 'no-thumb' : ''; ?>">
		    	<div class="entry-meta">
		            <?php
		                if (get_the_title()) {
		                ?>
		                    <h3 class="entry-title">
		                       <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		                    </h3>
		                <?php
		            	}
		            ?>
		        </div>
				<?php
					if ( has_excerpt()) {
						the_excerpt();
					} else {
						?>
							<div class="entry-description">
								<?php echo puca_tbay_substring(get_the_content(), 25, '...' ); ?>
								<div class="more">
									<a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Continue reading', 'puca' ); ?>"><?php esc_html_e( 'Continue reading', 'puca' ); ?> <i class="icon-arrow-right icons"></i></a>
								</div>
							</div>
						<?php
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