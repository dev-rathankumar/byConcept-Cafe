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
<div class="post-grid vertical">
    <article class="post row">   
        <figure class="entry-thumb col-md-6 <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
			<span class="comments-link"><i class="icon-bubbles icons"></i> <?php comments_popup_link( '0', '1', '%' ); ?></span>
            <a href="<?php the_permalink(); ?>"  class="entry-image tbay-image-loaded">
                <?php
                    $thumbnail_id = get_post_thumbnail_id(get_the_ID());
                    echo puca_tbay_get_attachment_image_loaded($thumbnail_id, $thumbsize );
                ?>
            </a>
            
            
        </figure>
        <div class="entry-content col-md-6">
			<div class="meta-info">
				<span class="author"><?php echo get_avatar(puca_tbay_get_id_author_post(), 'puca_avatar_post_carousel'); ?> <?php the_author_posts_link(); ?></span>
				<span class="entry-date"><?php echo puca_time_link(); ?></span>
				<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
				
				<?php endif; ?>
				
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
           <div class="entry">
				
				<div>
					<?php
						if (! has_excerpt()) {
							echo "";
						} else {
							?>
								<div class="entry-description"><?php echo puca_tbay_substring( get_the_excerpt(), 10, '' ); ?></div>
							<?php
						}
					?>
				</div>
			</div>
        </div>
    </article>
    
</div>
