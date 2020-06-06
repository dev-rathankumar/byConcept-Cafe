<?php $thumbsize = isset($thumbsize) ? $thumbsize : 'medium';?>
<?php
  $post_category = "";
  $categories = get_the_category();
  $separator = ' | ';
  $output = '';
  if($categories){
    foreach($categories as $category) {
      $output .= '<a href="'.esc_url( get_category_link( $category->term_id ) ).'" title="' . esc_attr( sprintf( esc_html__( 'View all posts in %s', 'puca' ), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
    }
  $post_category = trim($output, $separator);
  }      
?>
<div class="post-grid">
    <article class="post">   
        <figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?> col-sm-6">
          	<a href="<?php the_permalink(); ?>"  class="entry-image tbay-image-loaded">
              	<?php
					$thumbnail_id = get_post_thumbnail_id(get_the_ID());
					echo puca_tbay_get_attachment_image_loaded($thumbnail_id, $thumbsize );
              	?>
          	</a>
        </figure>
        <div class="entry-content col-sm-6">
			
          <div class="entry">
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
						<span class="author"><i class="icon-user icons"></i> <?php echo esc_html__( 'By', 'puca' ); ?> <?php the_author_posts_link(); ?></span>
						
						<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
						   <span class="comments-link"><i class="icon-bubbles icons"></i> <?php comments_popup_link( esc_html__( '0 comment', 'puca' ), esc_html__( '1 comment', 'puca' ), esc_html__( '% comments', 'puca' ) ); ?></span>
						<?php endif; ?>
  					
  				    </div>
				
				    <div>
						<?php
							if (! has_excerpt()) {
								echo "";
							} else {
								?>
									<div class="entry-description"><?php echo get_the_excerpt(); ?></div>
								<?php
							}
						?>
					</div>
					<div class="readmore">
						<a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Read More', 'puca' ); ?>"><i class="icon-arrow-right icons"></i></a>
					</div>
			</div>
        </div>
    </article>
    
</div>