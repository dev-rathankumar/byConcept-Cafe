<?php 
  $thumbsize = isset($thumbsize) ? $thumbsize : 'medium';
?>  
<div class="entry-content media">

  <?php
  if ( has_post_thumbnail() ) {
      ?>
        <div class="media-left">
          <figure class="entry-thumb">
              <a href="<?php the_permalink(); ?>"  class="entry-image tbay-image-loaded">
                <?php 

                    $thumbnail_id = get_post_thumbnail_id(get_the_ID());
                    echo puca_tbay_get_attachment_image_loaded($thumbnail_id, $thumbsize);

                ?>
              </a>  
          </figure>
        </div>
      <?php
  }
  ?>
  <div class="media-body">
    <?php
        if (get_the_title()) {
        ?>
            <h4 class="entry-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h4>
        <?php
    }
    ?>
    <div class="entry-content-inner clearfix">
        <div class="entry-meta">
            <div class="meta-info">
                <span class="entry-date"><?php echo puca_time_link(); ?></span>
            
                <?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
                <span class="comments-link"><i class="icons icon-bubbles"></i> <?php comments_popup_link( '0', '1', '%' ); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
  </div>
</div>      
