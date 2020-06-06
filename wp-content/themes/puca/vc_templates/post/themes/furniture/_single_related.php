<?php 
  $thumbsize = isset($thumbsize) ? $thumbsize : 'medium';
?>
<div class="entry-content media">

  <?php
  if ( has_post_thumbnail() ) {
      ?>
        <div class="media-left">
          <figure class="entry-thumb">
              <a href="<?php the_permalink(); ?>" class="tbay-image-loaded">
                  <?php 
                      $thumbnail_id = get_post_thumbnail_id(get_the_ID());
                      echo puca_tbay_get_attachment_image_loaded($thumbnail_id, $thumbsize );
                  ?>
              </a>  
          </figure>
        </div>
      <?php
  }
  ?>
  <div class="media-body">
    
    <div class="entry-content-inner clearfix">
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
        <?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
          <span class="comments-link"><i class="icons icon-bubbles"></i> <?php comments_popup_link( '0 comments', '1 comment', '% comments' ); ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>      
